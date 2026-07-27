<?php

namespace Tests\Feature;

use App\Jobs\ProcessPaidSongRequest;
use App\Models\SongRequest;
use App\Services\Payment\PaymentProvider;
use App\Services\Payment\StripeWebhookProcessor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class StripePaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_creates_a_pending_session_without_marking_the_order_paid(): void
    {
        $this->app->instance(PaymentProvider::class, new class implements PaymentProvider
        {
            public function createCheckout(SongRequest $request): array
            {
                return [
                    'status' => 'payment_pending',
                    'reference' => 'cs_test_checkout_123',
                    'checkout_url' => 'https://checkout.stripe.com/test-session',
                ];
            }
        });

        config()->set('payment.default', 'stripe');

        $songRequest = $this->songRequest();

        $this->postJson("/api/v1/song-requests/{$songRequest->id}/checkout")
            ->assertOk()
            ->assertJsonPath('data.status', 'payment_pending')
            ->assertJsonPath('data.checkout_url', 'https://checkout.stripe.com/test-session');

        $this->assertDatabaseHas('song_requests', [
            'id' => $songRequest->id,
            'status' => 'payment_pending',
            'payment_provider' => 'stripe',
            'payment_reference' => 'cs_test_checkout_123',
            'paid_at' => null,
        ]);
    }

    public function test_paid_webhook_is_idempotent_and_queues_fulfillment_once(): void
    {
        Queue::fake();

        $songRequest = $this->songRequest([
            'status' => 'payment_pending',
            'payment_provider' => 'stripe',
            'payment_reference' => 'cs_test_paid_123',
        ]);

        $session = [
            'id' => 'cs_test_paid_123',
            'payment_status' => 'paid',
            'amount_total' => 999,
            'currency' => 'eur',
            'payment_intent' => 'pi_test_123',
            'metadata' => [
                'song_request_id' => (string) $songRequest->id,
            ],
        ];

        $processor = app(StripeWebhookProcessor::class);

        $this->assertSame('processed', $processor->process(
            'evt_test_paid_123',
            'checkout.session.completed',
            $session,
        )['status']);

        $this->assertSame('duplicate', $processor->process(
            'evt_test_paid_123',
            'checkout.session.completed',
            $session,
        )['status']);

        Queue::assertPushed(ProcessPaidSongRequest::class, 1);

        $this->assertDatabaseHas('song_requests', [
            'id' => $songRequest->id,
            'status' => 'paid',
            'payment_intent_reference' => 'pi_test_123',
        ]);

        $this->assertDatabaseCount('payment_webhook_events', 1);
        $this->assertNotNull($songRequest->refresh()->paid_at);
        $this->assertNotNull($songRequest->payment_fulfillment_queued_at);
    }

    public function test_webhook_rejects_an_incorrect_amount(): void
    {
        Queue::fake();

        $songRequest = $this->songRequest([
            'status' => 'payment_pending',
            'payment_provider' => 'stripe',
            'payment_reference' => 'cs_test_wrong_amount',
        ]);

        $this->expectExceptionMessage('betaalde Stripe-bedrag');

        app(StripeWebhookProcessor::class)->process(
            'evt_test_wrong_amount',
            'checkout.session.completed',
            [
                'id' => 'cs_test_wrong_amount',
                'payment_status' => 'paid',
                'amount_total' => 100,
                'currency' => 'eur',
                'metadata' => [
                    'song_request_id' => (string) $songRequest->id,
                ],
            ],
        );
    }

    public function test_webhook_endpoint_verifies_the_stripe_signature(): void
    {
        Queue::fake();
        config()->set('payment.stripe.webhook_secret', 'whsec_test_secret');

        $songRequest = $this->songRequest([
            'status' => 'payment_pending',
            'payment_provider' => 'stripe',
            'payment_reference' => 'cs_test_signed',
        ]);

        $payload = json_encode([
            'id' => 'evt_test_signed',
            'object' => 'event',
            'api_version' => '2026-06-30',
            'created' => time(),
            'data' => [
                'object' => [
                    'id' => 'cs_test_signed',
                    'object' => 'checkout.session',
                    'payment_status' => 'paid',
                    'amount_total' => 999,
                    'currency' => 'eur',
                    'payment_intent' => 'pi_test_signed',
                    'metadata' => [
                        'song_request_id' => (string) $songRequest->id,
                    ],
                ],
            ],
            'livemode' => false,
            'pending_webhooks' => 1,
            'request' => null,
            'type' => 'checkout.session.completed',
        ], JSON_THROW_ON_ERROR);

        $timestamp = time();
        $signature = hash_hmac(
            'sha256',
            $timestamp.'.'.$payload,
            'whsec_test_secret',
        );

        $this->call(
            'POST',
            '/api/v1/payments/stripe/webhook',
            server: [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_STRIPE_SIGNATURE' => "t={$timestamp},v1={$signature}",
            ],
            content: $payload,
        )->assertOk()
            ->assertJsonPath('status', 'processed')
            ->assertJsonPath('order_id', $songRequest->id);

        Queue::assertPushed(ProcessPaidSongRequest::class, 1);
    }

    private function songRequest(array $overrides = []): SongRequest
    {
        return SongRequest::create(array_merge([
            'category' => 'verjaardag',
            'category_title' => 'Verjaardag',
            'email' => 'klant@example.com',
            'intake' => ['recipientName' => 'Anna'],
            'lyrics' => 'Een testlied',
            'status' => 'draft',
            'price_cents' => 999,
        ], $overrides));
    }
}
