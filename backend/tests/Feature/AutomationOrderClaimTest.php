<?php

namespace Tests\Feature;

use App\Models\SongRequest;
use App\Services\Orders\OrderExporter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutomationOrderClaimTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('orders.api_key', 'automation-test-key');
        config()->set('orders.claim_ttl_minutes', 60);
    }

    public function test_an_order_can_only_be_claimed_by_one_worker_at_a_time(): void
    {
        $songRequest = $this->readyOrder();

        $claim = $this->withHeader('X-Automation-Key', 'automation-test-key')
            ->postJson('/api/v1/automation/orders/claim', [
                'worker_id' => 'studio-mac',
            ])
            ->assertOk()
            ->assertJsonPath('data.order.order_id', $songRequest->id)
            ->assertJsonPath('data.order.suno.vocal_gender', 'female')
            ->assertJsonPath('data.claimed_by', 'studio-mac');

        $claimToken = $claim->json('data.claim_token');

        $this->withHeader('X-Automation-Key', 'automation-test-key')
            ->postJson('/api/v1/automation/orders/claim', [
                'worker_id' => 'second-mac',
            ])
            ->assertOk()
            ->assertJsonPath('data', null);

        $this->withHeaders([
            'X-Automation-Key' => 'automation-test-key',
            'X-Claim-Token' => $claimToken,
        ])->postJson("/api/v1/automation/orders/{$songRequest->id}/fail", [
            'error' => 'Suno was tijdelijk niet bereikbaar.',
        ])->assertOk()
            ->assertJsonPath('data.automation_status', 'ready')
            ->assertJsonPath('data.retryable', true);

        $retry = $this->withHeader('X-Automation-Key', 'automation-test-key')
            ->postJson('/api/v1/automation/orders/claim', [
                'worker_id' => 'second-mac',
            ])
            ->assertOk()
            ->assertJsonPath('data.order.order_id', $songRequest->id);

        $this->withHeaders([
            'X-Automation-Key' => 'automation-test-key',
            'X-Claim-Token' => $retry->json('data.claim_token'),
        ])->postJson("/api/v1/automation/orders/{$songRequest->id}/complete")
            ->assertOk()
            ->assertJsonPath('data.automation_status', 'completed');

        $this->assertDatabaseHas('song_requests', [
            'id' => $songRequest->id,
            'automation_status' => 'completed',
            'automation_attempts' => 2,
        ]);
    }

    public function test_claim_endpoint_requires_the_automation_key(): void
    {
        $this->postJson('/api/v1/automation/orders/claim', [
            'worker_id' => 'studio-mac',
        ])->assertUnauthorized();
    }

    public function test_only_explicit_male_or_female_vocals_are_exported_to_suno(): void
    {
        $maleOrder = $this->readyOrder();
        $maleOrder->forceFill([
            'intake' => array_merge($maleOrder->intake, ['vocals' => 'Warme mannenstem']),
        ])->save();

        $payload = app(OrderExporter::class)->buildPayload($maleOrder->refresh());
        $this->assertSame('male', $payload['suno']['vocal_gender']);

        $maleOrder->forceFill([
            'intake' => array_merge($maleOrder->intake, ['vocals' => 'Laat ons kiezen']),
        ])->save();

        $payload = app(OrderExporter::class)->buildPayload($maleOrder->refresh());
        $this->assertNull($payload['suno']['vocal_gender']);
    }

    public function test_a_specific_order_can_be_claimed_by_its_admin_upload_token(): void
    {
        $olderOrder = $this->readyOrder();
        $olderOrder->forceFill(['paid_at' => now()->subMinute()])->save();
        $selectedOrder = $this->readyOrder();

        $this->withHeader('X-Automation-Key', 'automation-test-key')
            ->postJson('/api/v1/automation/orders/claim', [
                'worker_id' => 'studio-mac',
                'admin_upload_token' => $selectedOrder->admin_upload_token,
            ])
            ->assertOk()
            ->assertJsonPath('data.order.order_id', $selectedOrder->id)
            ->assertJsonPath('data.order.admin_upload_url', route('admin.upload.show', [
                'token' => $selectedOrder->admin_upload_token,
            ]));

        $this->assertDatabaseHas('song_requests', [
            'id' => $selectedOrder->id,
            'automation_status' => 'claimed',
        ]);
        $this->assertDatabaseHas('song_requests', [
            'id' => $olderOrder->id,
            'automation_status' => 'ready',
        ]);
    }

    public function test_an_unknown_admin_upload_token_does_not_claim_another_order(): void
    {
        $order = $this->readyOrder();

        $this->withHeader('X-Automation-Key', 'automation-test-key')
            ->postJson('/api/v1/automation/orders/claim', [
                'worker_id' => 'studio-mac',
                'admin_upload_token' => 'unknown-upload-token',
            ])
            ->assertOk()
            ->assertJsonPath('data', null);

        $this->assertDatabaseHas('song_requests', [
            'id' => $order->id,
            'automation_status' => 'ready',
        ]);
    }

    private function readyOrder(): SongRequest
    {
        return SongRequest::create([
            'category' => 'verjaardag',
            'category_title' => 'Verjaardag',
            'email' => 'klant@example.com',
            'intake' => [
                'recipientName' => 'Anna',
                'musicStyle' => 'Nederlandstalige pop',
                'vocals' => 'Vrouwenstem',
            ],
            'lyrics' => 'Concepttekst',
            'final_lyrics' => 'Definitieve tekst',
            'status' => 'music_prompt_ready',
            'price_cents' => 999,
            'payment_provider' => 'stripe',
            'payment_reference' => 'cs_test_ready',
            'paid_at' => now(),
            'automation_status' => 'ready',
        ]);
    }
}
