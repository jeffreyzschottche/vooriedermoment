<?php

namespace App\Services\Payment;

use App\Models\SongRequest;
use RuntimeException;
use Stripe\StripeClient;

class StripePaymentProvider implements PaymentProvider
{
    public function __construct(private StripeClient $stripe) {}

    public function createCheckout(SongRequest $request): array
    {
        $frontendUrl = rtrim((string) config('app.frontend_url'), '/');

        if ($frontendUrl === '') {
            throw new RuntimeException('FRONTEND_URL is niet ingesteld.');
        }

        if ($request->payment_provider === 'stripe' && str_starts_with((string) $request->payment_reference, 'cs_')) {
            $existing = $this->stripe->checkout->sessions->retrieve($request->payment_reference);

            if ($existing->status === 'open' && $existing->url) {
                return [
                    'status' => 'payment_pending',
                    'reference' => $existing->id,
                    'checkout_url' => $existing->url,
                ];
            }
        }

        $session = $this->stripe->checkout->sessions->create([
            'mode' => 'payment',
            'success_url' => $frontendUrl.'/bedankt?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $frontendUrl.'/checkout?payment=cancelled',
            'client_reference_id' => (string) $request->id,
            'customer_email' => $request->email,
            'locale' => 'nl',
            'line_items' => [[
                'quantity' => 1,
                'price_data' => [
                    'currency' => strtolower((string) config('payment.currency', 'EUR')),
                    'unit_amount' => $request->price_cents,
                    'product_data' => [
                        'name' => 'Persoonlijk nummer — '.$request->category_title,
                        'description' => 'Vier samples, één gekozen volledig nummer en distributie.',
                    ],
                ],
            ]],
            'metadata' => [
                'song_request_id' => (string) $request->id,
            ],
            'payment_intent_data' => [
                'metadata' => [
                    'song_request_id' => (string) $request->id,
                ],
            ],
        ]);

        if (! $session->url) {
            throw new RuntimeException('Stripe gaf geen checkout-URL terug.');
        }

        return [
            'status' => 'payment_pending',
            'reference' => $session->id,
            'checkout_url' => $session->url,
        ];
    }
}
