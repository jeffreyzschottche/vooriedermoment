<?php

namespace Tests\Feature;

use App\Jobs\SendOrderNotification;
use App\Jobs\SendPaymentConfirmation;
use App\Mail\NewOrderMail;
use App\Mail\PaymentConfirmationMail;
use App\Mail\ProductionFailedAdminMail;
use App\Mail\ProductionNeedsAttentionMail;
use App\Jobs\ProcessPaidSongRequest;
use App\Models\SongRequest;
use App\Services\Production\SongProductionPipeline;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
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
        Mail::assertSent(NewOrderMail::class, function (NewOrderMail $mail): bool {
            $html = $mail->render();

            return $mail->hasTo('orders@example.com')
                && str_contains($html, 'Betaald')
                && str_contains($html, 'jan@example.com')
                && str_contains($html, 'Rock / anthem')
                && str_contains($html, 'Bouwbedrijf Jansen')
                && str_contains($html, 'Lyrics');
        });
        Mail::assertSent(NewOrderMail::class, 1);
        Mail::assertSent(PaymentConfirmationMail::class, 1);

        $this->postJson("/api/v1/song-requests/{$id}/checkout")->assertOk();
        Mail::assertSent(NewOrderMail::class, 1);
        Mail::assertSent(PaymentConfirmationMail::class, 1);
    }

    public function test_a_paid_order_that_exhausts_production_retries_notifies_the_customer_and_admin_once(): void
    {
        Mail::fake();
        config()->set('orders.notify_email', 'orders@example.com');

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
        Mail::assertQueued(ProductionNeedsAttentionMail::class, fn ($mail) => $mail->hasTo('klant@example.com'));
        Mail::assertQueued(ProductionFailedAdminMail::class, function (ProductionFailedAdminMail $mail): bool {
            return $mail->hasTo('orders@example.com')
                && str_contains($mail->render(), 'AI-lyrics afgekeurd: details ontbreken.');
        });

        // Een handmatige retry die opnieuw faalt mag klant en admin niet spammen.
        $job->failed(new RuntimeException('Opnieuw mislukt.'));
        Mail::assertQueued(ProductionNeedsAttentionMail::class, 1);
        Mail::assertQueued(ProductionFailedAdminMail::class, 1);
    }

    public function test_the_admin_order_mail_is_sent_by_its_own_job_not_inside_production(): void
    {
        Queue::fake();

        $songRequest = SongRequest::create([
            'category' => 'verjaardag',
            'category_title' => 'Verjaardag',
            'email' => 'klant@example.com',
            'intake' => ['recipientName' => 'Mila'],
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        (new ProcessPaidSongRequest($songRequest->id))->handle(app(SongProductionPipeline::class));

        // Een mailstoring kan de productie dus niet meer laten falen.
        $this->assertSame('music_prompt_ready', $songRequest->fresh()->status);
        Queue::assertPushed(SendOrderNotification::class, fn ($job) => $job->songRequestId === $songRequest->id);
        Queue::assertPushed(SendPaymentConfirmation::class, 1);
    }

    public function test_a_paid_order_receives_one_confirmation_with_the_final_lyrics(): void
    {
        Mail::fake();

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

        $lyrics = $songRequest->fresh()->final_lyrics;
        $this->assertNotEmpty($lyrics);
        $this->assertNotNull($songRequest->fresh()->payment_confirmation_sent_at);
        Mail::assertSent(PaymentConfirmationMail::class, 1, function (PaymentConfirmationMail $mail) use ($lyrics): bool {
            return $mail->hasTo('eigenaar@example.com')
                && str_contains($mail->render(), e(strtok($lyrics, "\n")));
        });

        app(SongProductionPipeline::class)->run($songRequest->fresh());
        Mail::assertSent(PaymentConfirmationMail::class, 1);
    }
}
