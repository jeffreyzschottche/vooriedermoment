<?php

namespace Tests\Feature;

use App\Mail\NewOrderMail;
use App\Mail\DiscountLyricsMail;
use App\Mail\ProductionNeedsAttentionMail;
use App\Jobs\ProcessPaidSongRequest;
use App\Models\SongRequest;
use App\Services\Production\SongProductionPipeline;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use RuntimeException;

class SongRequestProductionTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_a_request_never_waits_for_the_ai_provider(): void
    {
        config([
            'ai.default' => 'deepseek',
            'ai.providers.deepseek.key' => 'test-key',
        ]);
        Http::fake();

        $response = $this->postJson('/api/v1/song-requests', [
            'category' => 'geslaagd',
            'categoryTitle' => 'Geslaagd',
            'intake' => [
                'recipientName' => 'Lara',
                'studyLevel' => 'VWO',
                'anecdotes' => 'Nachtenlang leren voor wiskunde.',
                'email' => 'lara@example.com',
            ],
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.status', 'draft');

        Http::assertNothingSent();
        $this->assertNotEmpty($response->json('data.lyrics_preview'));
    }

    public function test_checkout_generates_final_lyrics_and_music_prompt(): void
    {
        Mail::fake();
        config()->set('orders.notify_email', 'orders@example.com');

        $create = $this->postJson('/api/v1/song-requests', [
            'category' => 'bouwbedrijven',
            'categoryTitle' => 'Bouwbedrijven',
            'intake' => [
                'companyName' => 'Bouwbedrijf Jansen',
                'contactName' => 'Jan',
                'slogan' => 'Bouwen op vertrouwen',
                'tone' => 'Stoer & energiek',
                'musicStyle' => 'Rock / anthem',
                'anecdotes' => 'Ze bouwen scholen en zingen altijd op vrijdag.',
                'mustMention' => 'De vrijdagmiddagborrel',
                'avoid' => 'Geen flauwe grappen',
                'email' => 'jan@example.com',
            ],
        ]);

        $create->assertCreated();
        $id = $create->json('data.id');

        $checkout = $this->postJson("/api/v1/song-requests/{$id}/checkout");

        $checkout
            ->assertOk()
            ->assertJsonPath('data.status', 'music_prompt_ready')
            ->assertJsonPath('data.music_reference', fn (?string $value) => str_starts_with($value ?? '', 'stub-music-'));

        $this->assertDatabaseHas('song_requests', [
            'id' => $id,
            'status' => 'music_prompt_ready',
            'category' => 'bouwbedrijven',
        ]);

        $this->assertNotEmpty($checkout->json('data.production_steps'));
        $this->assertNotNull(
            SongRequest::findOrFail($id)->order_notification_sent_at
        );
        Mail::assertSent(NewOrderMail::class, 1);

        $this->postJson("/api/v1/song-requests/{$id}/checkout")->assertOk();
        Mail::assertSent(NewOrderMail::class, 1);
    }

    public function test_a_paid_order_that_exhausts_production_retries_notifies_the_customer_once(): void
    {
        Mail::fake();

        $songRequest = SongRequest::create([
            'category' => 'team-clublied',
            'category_title' => 'Team- of clublied',
            'email' => 'klant@example.com',
            'intake' => ['clubName' => 'VV Voorbeeld'],
            'status' => 'producing',
            'paid_at' => now(),
        ]);
        $job = new ProcessPaidSongRequest($songRequest->id);

        $job->failed(new RuntimeException('AI-lyrics afgekeurd: details ontbreken.'));

        $this->assertDatabaseHas('song_requests', [
            'id' => $songRequest->id,
            'status' => 'production_failed',
            'automation_status' => 'failed',
            'automation_last_error' => 'AI-lyrics afgekeurd: details ontbreken.',
        ]);
        $this->assertNotNull($songRequest->fresh()->production_failure_notified_at);
        Mail::assertSent(ProductionNeedsAttentionMail::class, 1);

        // Een handmatige retry die opnieuw faalt mag de klant niet spammen.
        $job->failed(new RuntimeException('Opnieuw mislukt.'));
        Mail::assertSent(ProductionNeedsAttentionMail::class, 1);
    }

    public function test_a_discount_code_order_receives_final_lyrics_once_they_are_generated(): void
    {
        Mail::fake();
        config()->set('ai.lyrics_require_complete_coverage', false);

        $songRequest = SongRequest::create([
            'category' => 'verjaardag',
            'category_title' => 'Verjaardag',
            'email' => 'eigenaar@example.com',
            'intake' => [
                'recipientName' => 'Mila',
                'anecdotes' => 'Mila zingt altijd mee.',
            ],
            'status' => 'paid',
            'paid_at' => now(),
            'payment_provider' => 'discount_code',
        ]);

        app(SongProductionPipeline::class)->run($songRequest);

        $this->assertNotNull($songRequest->fresh()->discount_lyrics_sent_at);
        Mail::assertSent(DiscountLyricsMail::class, 1);

        app(SongProductionPipeline::class)->run($songRequest->fresh());
        Mail::assertSent(DiscountLyricsMail::class, 1);
    }
}
