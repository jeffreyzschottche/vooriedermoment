<?php

namespace Tests\Feature;

use App\Mail\SamplesReadyMail;
use App\Models\SongRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AutomationSampleUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        Mail::fake();
        config()->set('orders.api_key', 'automation-test-key');
        config()->set('orders.storage_disk', 'local');
    }

    public function test_claimed_worker_can_upload_exactly_four_samples(): void
    {
        $songRequest = SongRequest::create([
            'category' => 'verjaardag',
            'category_title' => 'Verjaardag',
            'email' => 'klant@example.com',
            'intake' => ['recipientName' => 'Anna'],
            'lyrics' => 'Tekst',
            'final_lyrics' => 'Definitieve tekst',
            'status' => 'music_prompt_ready',
            'price_cents' => 999,
            'payment_provider' => 'stripe',
            'payment_reference' => 'cs_test_samples',
            'paid_at' => now(),
            'automation_status' => 'ready',
        ]);

        $claim = $this->withHeader('X-Automation-Key', 'automation-test-key')
            ->postJson('/api/v1/automation/orders/claim', [
                'worker_id' => 'studio-mac',
            ])
            ->assertOk();

        $samples = [];
        foreach (range(1, 4) as $position) {
            $samples[] = [
                'position' => $position,
                'title' => "Versie {$position}",
                'suno_source_url' => "https://suno.com/song/{$position}",
                'preview' => UploadedFile::fake()->create("preview-{$position}.mp3", 100, 'audio/mpeg'),
                'cover' => UploadedFile::fake()->image("cover-{$position}.jpg"),
            ];
        }

        $this->withHeaders([
            'X-Automation-Key' => 'automation-test-key',
            'X-Claim-Token' => $claim->json('data.claim_token'),
        ])->post("/api/v1/automation/orders/{$songRequest->id}/samples", [
            'samples' => $samples,
        ])->assertCreated()
            ->assertJsonPath('data.status', 'samples_ready')
            ->assertJsonPath('data.automation_status', 'completed')
            ->assertJsonCount(4, 'data.samples');

        $this->assertDatabaseCount('song_samples', 4);
        $this->assertDatabaseHas('song_requests', [
            'id' => $songRequest->id,
            'status' => 'samples_ready',
            'automation_status' => 'completed',
        ]);

        Storage::disk('local')->assertExists("orders/{$songRequest->id}/samples/1/preview.mp3");
        Storage::disk('local')->assertExists("orders/{$songRequest->id}/samples/1/cover.jpg");
        Mail::assertSent(SamplesReadyMail::class, 1);
        $this->assertNotNull($songRequest->refresh()->samples_email_sent_at);

        $this->postJson("/api/v1/select/{$songRequest->selection_token}", [
            'sample_id' => 3,
        ])->assertOk()
            ->assertJsonPath('chosen_sample_id', 3);

        $songRequest->refresh();

        $this->assertSame('3', $songRequest->chosen_sample_id);
        $this->assertSame(3, $songRequest->chosen_sample_position);
        $this->assertSame('Versie 3', $songRequest->chosen_sample_title);
        $this->assertSame('https://suno.com/song/3', $songRequest->chosen_suno_source_url);
        $this->assertNotNull($songRequest->samples_deleted_at);
        $this->assertDatabaseCount('song_samples', 0);

        foreach (range(1, 4) as $position) {
            Storage::disk('local')->assertMissing("orders/{$songRequest->id}/samples/{$position}/preview.mp3");
            Storage::disk('local')->assertMissing("orders/{$songRequest->id}/samples/{$position}/cover.jpg");
        }

        $this->getJson("/api/v1/select/{$songRequest->selection_token}")
            ->assertOk()
            ->assertJsonPath('already_chosen', true)
            ->assertJsonPath('chosen_sample_id', '3')
            ->assertJsonPath('chosen_sample_title', 'Versie 3');
    }
}
