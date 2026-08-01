<?php

namespace App\Services\Payment;

use App\Jobs\ProcessPaidSongRequest;
use App\Jobs\SendPaymentConfirmation;
use App\Models\PaymentWebhookEvent;
use App\Models\SongRequest;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class StripeWebhookProcessor
{
    /**
     * @param  array<string, mixed>  $session
     * @return array{status: string, order_id?: int}
     */
    public function process(string $eventId, string $eventType, array $session): array
    {
        if (! in_array($eventType, [
            'checkout.session.completed',
            'checkout.session.async_payment_succeeded',
        ], true)) {
            return ['status' => 'ignored'];
        }

        if (! in_array($session['payment_status'] ?? null, ['paid', 'no_payment_required'], true)) {
            return ['status' => 'awaiting_payment'];
        }

        return DB::transaction(function () use ($eventId, $eventType, $session): array {
            if (PaymentWebhookEvent::where('provider', 'stripe')
                ->where('external_id', $eventId)
                ->exists()) {
                return ['status' => 'duplicate'];
            }

            $orderId = (int) data_get($session, 'metadata.song_request_id', 0);
            $sessionId = (string) ($session['id'] ?? '');

            if ($orderId < 1 || $sessionId === '') {
                throw new RuntimeException('Stripe Checkout Session mist de ordermetadata.');
            }

            $songRequest = SongRequest::whereKey($orderId)->lockForUpdate()->firstOrFail();

            if ($songRequest->payment_reference !== $sessionId) {
                throw new RuntimeException('Stripe Checkout Session hoort niet bij deze order.');
            }

            $amount = (int) ($session['amount_total'] ?? -1);
            $currency = strtoupper((string) ($session['currency'] ?? ''));

            if ($amount !== $songRequest->price_cents || $currency !== strtoupper((string) config('payment.currency', 'EUR'))) {
                throw new RuntimeException('Het betaalde Stripe-bedrag of de valuta klopt niet.');
            }

            if (! $songRequest->paid_at) {
                $songRequest->forceFill([
                    'status' => 'paid',
                    'payment_provider' => 'stripe',
                    'payment_intent_reference' => $session['payment_intent'] ?? null,
                    'paid_at' => now(),
                ])->save();
            }

            if (! $songRequest->payment_fulfillment_queued_at) {
                ProcessPaidSongRequest::dispatch($songRequest->id)->afterCommit();

                $songRequest->forceFill([
                    'payment_fulfillment_queued_at' => now(),
                ])->save();
            }

            if (! $songRequest->payment_confirmation_sent_at) {
                SendPaymentConfirmation::dispatch($songRequest->id)->afterCommit();
            }

            PaymentWebhookEvent::create([
                'provider' => 'stripe',
                'external_id' => $eventId,
                'type' => $eventType,
                'song_request_id' => $songRequest->id,
                'processed_at' => now(),
            ]);

            return [
                'status' => 'processed',
                'order_id' => $songRequest->id,
            ];
        });
    }
}
