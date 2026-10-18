<?php

namespace App\Services\Music;

use App\Models\SongRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\SamplesReadyMail;

/**
 * Suno API Music Provider
 *
 * Integrates with the Suno AI music generation API.
 * Uses the unofficial API wrapper: https://github.com/gcui-art/suno-api
 *
 * Flow:
 * 1. After payment, generate 4 music samples (15s each) using custom mode
 * 2. Email the 4 samples to the customer
 * 3. Customer replies with their choice
 * 4. We extend the chosen sample to full length
 * 5. Upload to Spotify via DistroKid/TuneCore API
 */
class SunoMusicProvider implements MusicProvider
{
    private string $baseUrl;
    private string $apiKey;
    private int $sampleDuration = 15; // 15 seconds per sample
    private int $sampleCount = 4;     // 4 samples per request

    public function __construct(private MusicPromptBuilder $promptBuilder)
    {
        // The Suno API wrapper runs as a separate service
        // Default: http://localhost:3000 (the suno-api docker container)
        $this->baseUrl = config('services.suno.base_url', 'http://localhost:3000');
        $this->apiKey = config('services.suno.api_key', '');
    }

    /**
     * Generate 4 music samples based on the lyrics and intake.
     */
    public function generate(SongRequest $songRequest, string $lyrics, array $intake): array
    {
        $prompt = $this->promptBuilder->build($songRequest, $lyrics, $intake);

        // Build the Suno-specific prompt
        $sunoPrompt = $this->buildSunoPrompt($songRequest, $lyrics, $intake);

        try {
            // Generate 4 samples using custom mode
            $samples = $this->generateSamples($sunoPrompt, $lyrics);

            // Store sample URLs in the song request
            $songRequest->update([
                'samples' => $samples,
                'samples_generated_at' => now(),
            ]);

            // Send email with samples to customer
            $this->sendSamplesEmail($songRequest, $samples);

            return [
                'reference' => 'suno-' . $songRequest->id . '-' . now()->format('YmdHis'),
                'prompt' => $prompt,
                'status' => 'samples_sent',
                'samples' => $samples,
            ];
        } catch (\Exception $e) {
            Log::error('Suno API error', [
                'song_request_id' => $songRequest->id,
                'error' => $e->getMessage(),
            ]);

            // Fallback to stub if Suno fails
            return [
                'reference' => 'suno-failed-' . $songRequest->id,
                'prompt' => $prompt,
                'status' => 'generation_failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Build a Suno-optimized prompt for music generation.
     */
    private function buildSunoPrompt(SongRequest $songRequest, string $lyrics, array $intake): array
    {
        $tone = $intake['tone'] ?? 'vrolijk en persoonlijk';
        $tempo = trim((string) ($intake['tempo'] ?? ''));
        $vocals = $intake['vocals'] ?? 'mannelijke stem';

        return [
            'prompt' => "A Dutch-language song about {$songRequest->category_title}. {$tone} mood.".($tempo ? " Tempo: {$tempo}." : ''),
            'tags' => SunoStyleTags::style($intake),
            'title' => $this->generateTitle($songRequest, $intake),
            'make_instrumental' => str_contains(strtolower($vocals), 'instrumentaal'),
        ];
    }

    /**
     * Generate a title for the song.
     */
    private function generateTitle(SongRequest $songRequest, array $intake): string
    {
        $name = $intake['recipientName'] ?? $intake['companyName'] ?? $intake['clubName'] ?? '';
        $category = $songRequest->category_title;

        if ($name) {
            return "{$category} - {$name}";
        }

        return "Voor Ieder Moment - {$category}";
    }

    /**
     * Call the Suno API to generate samples.
     *
     * @return array Array of sample objects with url, duration, id
     */
    private function generateSamples(array $sunoPrompt, string $lyrics): array
    {
        $samples = [];

        // Generate 4 variations
        for ($i = 0; $i < $this->sampleCount; $i++) {
            $response = Http::timeout(120)
                ->withHeaders([
                    'Authorization' => "Bearer {$this->apiKey}",
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->baseUrl}/api/custom_generate", [
                    'prompt' => $lyrics,
                    'tags' => $sunoPrompt['tags'],
                    'title' => $sunoPrompt['title'] . " (Versie " . ($i + 1) . ")",
                    'make_instrumental' => $sunoPrompt['make_instrumental'],
                    'wait_audio' => true,
                ]);

            if (!$response->successful()) {
                throw new \Exception("Suno API error: " . $response->body());
            }

            $data = $response->json();

            // Suno returns an array of generated songs
            foreach ($data as $song) {
                $samples[] = [
                    'id' => $song['id'],
                    'url' => $song['audio_url'],
                    'image_url' => $song['image_url'] ?? null,
                    'title' => $song['title'],
                    'duration' => $song['duration'] ?? $this->sampleDuration,
                    'created_at' => $song['created_at'] ?? now()->toISOString(),
                ];
            }
        }

        // Return first 4 samples (in case more were generated)
        return array_slice($samples, 0, $this->sampleCount);
    }

    /**
     * Send an email with the 4 samples to the customer.
     */
    private function sendSamplesEmail(SongRequest $songRequest, array $samples): void
    {
        $email = $songRequest->email;

        if (!$email) {
            Log::warning('No email found for song request', ['id' => $songRequest->id]);
            return;
        }

        Mail::to($email)->send(new SamplesReadyMail($songRequest, $samples));
    }

    /**
     * Extend a chosen sample to full length.
     * Called when customer selects their preferred sample.
     */
    public function extendSample(SongRequest $songRequest, string $sampleId): array
    {
        $response = Http::timeout(180)
            ->withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type' => 'application/json',
            ])
            ->post("{$this->baseUrl}/api/extend_audio", [
                'audio_id' => $sampleId,
                'prompt' => '', // Continue from existing
                'continue_at' => $this->sampleDuration,
                'wait_audio' => true,
            ]);

        if (!$response->successful()) {
            throw new \Exception("Suno extend error: " . $response->body());
        }

        $data = $response->json();
        $fullSong = $data[0] ?? null;

        if (!$fullSong) {
            throw new \Exception("No extended song returned");
        }

        // Update song request with final song
        $songRequest->update([
            'final_song_id' => $fullSong['id'],
            'final_song_url' => $fullSong['audio_url'],
            'final_song_duration' => $fullSong['duration'],
            'chosen_sample_id' => $sampleId,
            'status' => 'ready_for_release',
        ]);

        return [
            'id' => $fullSong['id'],
            'url' => $fullSong['audio_url'],
            'duration' => $fullSong['duration'],
        ];
    }

    /**
     * Get the status of a generation job.
     */
    public function getStatus(string $songId): array
    {
        $response = Http::timeout(30)
            ->withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
            ])
            ->get("{$this->baseUrl}/api/get", [
                'ids' => $songId,
            ]);

        if (!$response->successful()) {
            throw new \Exception("Suno status error: " . $response->body());
        }

        return $response->json()[0] ?? [];
    }

    /**
     * Check remaining credits.
     */
    public function getCredits(): array
    {
        $response = Http::timeout(30)
            ->withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
            ])
            ->get("{$this->baseUrl}/api/get_limit");

        if (!$response->successful()) {
            throw new \Exception("Suno credits error: " . $response->body());
        }

        return $response->json();
    }
}
