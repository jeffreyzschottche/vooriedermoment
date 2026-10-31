<?php

namespace Tests\Feature;

use App\Jobs\ProcessPaidSongRequest;
use App\Models\SongRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class StripeWebhookIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['payment.stripe.webhook_secret' => 'whsec_vooriedermoment_test']);
        Queue::fake();
    }

    #[DataProvider('unrelatedCheckouts')]
    public function test_a_checkout_from_another_app_is_ignored_without_changing_orders(string $type, ?array $metadata): void
    {
        $order = $this->order();
        $session = [
            'id' => 'cs_other_app',
            'object' => 'checkout.session',
            'mode' => 'subscription',
            'payment_status' => 'paid',
            'amount_total' => 99,
            'currency' => 'eur',
            'client_reference_id' => (string) $order->id,
            'cancel_url' => 'https://allergieid.nl/account/billing/cancel',
            'metadata' => $metadata,
        ];

        $this->signedWebhook($session, $type)->assertOk()
            ->assertJsonPath('received', true)
            ->assertJsonPath('status', 'ignored');

        $this->assertSame('payment_pending', $order->fresh()->status);
        $this->assertNull($order->fresh()->paid_at);
        $this->assertNull($order->fresh()->payment_fulfillment_queued_at);
        $this->assertDatabaseCount('song_requests', 1);
        $this->assertDatabaseCount('payment_webhook_events', 0);
        Queue::assertNothingPushed();
    }

    public static function unrelatedCheckouts(): array
    {
        return [
            'no metadata' => ['checkout.session.completed', null],
            'empty metadata' => ['checkout.session.completed', []],
            'other app account metadata' => ['checkout.session.async_payment_succeeded', ['user_id' => '1']],
            'Plus1Mee credit metadata' => ['checkout.session.completed', ['credit_purchase_order' => 'other-app-order']],
        ];
    }

    public function test_an_unrelated_checkout_still_requires_a_valid_stripe_signature(): void
    {
        $this->signedWebhook([
            'id' => 'cs_other_app',
            'object' => 'checkout.session',
            'payment_status' => 'paid',
            'metadata' => [],
        ], secret: 'whsec_wrong')->assertStatus(400)->assertJsonPath('error', 'Ongeldige webhook.');

        $this->assertDatabaseCount('song_requests', 0);
        $this->assertDatabaseCount('payment_webhook_events', 0);
        Queue::assertNothingPushed();
    }

    #[DataProvider('paymentEvents')]
    public function test_an_own_payment_is_processed_and_production_is_queued_only_once(string $type): void
    {
        $order = $this->order();
        $session = $this->checkoutSession($order);

        $this->signedWebhook($session, $type)->assertOk()
            ->assertJsonPath('status', 'processed')
            ->assertJsonPath('order_id', $order->id);
        $this->signedWebhook($session, $type)->assertOk()->assertJsonPath('status', 'duplicate');

        $this->assertSame('paid', $order->fresh()->status);
        $this->assertNotNull($order->fresh()->paid_at);
        $this->assertDatabaseCount('payment_webhook_events', 1);
        Queue::assertPushed(ProcessPaidSongRequest::class, 1);
    }

    public static function paymentEvents(): array
    {
        return [
            ['checkout.session.completed'],
            ['checkout.session.async_payment_succeeded'],
        ];
    }

    #[DataProvider('invalidOwnPayments')]
    public function test_an_invalid_own_payment_still_fails_without_changing_the_order(array $overrides): void
    {
        $order = $this->order();
        $session = array_replace_recursive($this->checkoutSession($order), $overrides);

        $this->signedWebhook($session)->assertStatus(500)
            ->assertJsonPath('error', 'Webhookverwerking mislukt.');

        $this->assertSame('payment_pending', $order->fresh()->status);
        $this->assertNull($order->fresh()->paid_at);
        $this->assertDatabaseCount('payment_webhook_events', 0);
        Queue::assertNothingPushed();
    }

    public static function invalidOwnPayments(): array
    {
        return [
            'missing session' => [['id' => '']],
            'different session' => [['id' => 'cs_another_session']],
            'wrong amount' => [['amount_total' => 99]],
            'wrong currency' => [['currency' => 'usd']],
            'missing order' => [['metadata' => ['song_request_id' => '999999']]],
            'invalid order metadata' => [['metadata' => ['song_request_id' => 'invalid']]],
        ];
    }

    private function order(): SongRequest
    {
        return SongRequest::create([
            'category' => 'verjaardag',
            'category_title' => 'Verjaardag',
            'email' => 'klant@example.com',
            'intake' => ['recipientName' => 'Anna'],
            'status' => 'payment_pending',
            'price_cents' => 499,
            'payment_provider' => 'stripe',
            'payment_reference' => 'cs_vooriedermoment',
        ]);
    }

    private function checkoutSession(SongRequest $order): array
    {
        return [
            'id' => $order->payment_reference,
            'object' => 'checkout.session',
            'mode' => 'payment',
            'payment_status' => 'paid',
            'amount_total' => $order->price_cents,
            'currency' => 'eur',
            'payment_intent' => 'pi_vooriedermoment',
            'metadata' => ['song_request_id' => (string) $order->id],
        ];
    }

    private function signedWebhook(array $session, string $type = 'checkout.session.completed', ?string $secret = null): TestResponse
    {
        $body = json_encode([
            'id' => 'evt_vooriedermoment_test',
            'object' => 'event',
            'type' => $type,
            'data' => ['object' => $session],
        ], JSON_THROW_ON_ERROR);
        $timestamp = time();
        $signature = hash_hmac('sha256', $timestamp.'.'.$body, $secret ?? config('payment.stripe.webhook_secret'));

        return $this->call('POST', '/api/v1/payments/stripe/webhook', [], [], [], [
            'HTTP_ACCEPT' => 'application/json',
            'CONTENT_TYPE' => 'application/json',
            'HTTP_STRIPE_SIGNATURE' => "t={$timestamp},v1={$signature}",
        ], $body);
    }
}
