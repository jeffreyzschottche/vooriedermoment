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
        $style = $intake['musicStyle'] ?? 'Nederlandse pop';
        $tone = $intake['tone'] ?? 'vrolijk en persoonlijk';
        $tempo = trim((string) ($intake['tempo'] ?? ''));
        $vocals = $intake['vocals'] ?? 'mannelijke stem';

        // Map Dutch styles to Suno-compatible tags
        $styleMapping = [
            'Nederlandstalige pop' => 'dutch pop, catchy, radio-friendly',
            'Feest / meezinger' => 'dutch party music, upbeat, celebratory, sing-along',
            'Akoestisch en klein' => 'acoustic, intimate, warm vocals',
            'Singer-songwriter' => 'singer-songwriter, acoustic, personal, warm',
            'Pop ballad' => 'dutch ballad, emotional, piano',
            'Rock / anthem' => 'dutch rock, guitar-driven, energetic, anthemic',
            'Urban pop' => 'urban pop, modern beats, rhythmic',
            'Hiphop / rap coupletten' => 'dutch hip-hop verses, melodic chorus, modern beats',
            'Dance pop' => 'dutch dance pop, electronic, festival',
            'Disco / funk' => 'disco funk, groovy bass, upbeat',
            'Country pop' => 'country pop, warm guitars, sing-along',
            'Indie pop' => 'indie pop, fresh, catchy, organic',
            'Schlager / apres-ski' => 'dutch schlager, apres-ski, feestelijk',
            'Koor / stadion' => 'stadium chant, choir vocals, anthemic',
            'R&B / soul' => 'r&b soul, smooth, warm, emotional',
            'Piano ballad' => 'piano ballad, intimate, emotional',
            'Nederlandse levenslied' => 'dutch levenslied, emotional, sing-along',
            'Volksmuziek modern' => 'modern dutch folk, accordion accents, festive',
            'Reggaeton pop' => 'reggaeton pop, latin rhythm, danceable',
            'Afro pop' => 'afropop, warm groove, rhythmic',
            'Latin pop' => 'latin pop, sunny, danceable',
            'EDM / festival' => 'edm festival, big drops, energetic',
            'House' => 'house, four-on-the-floor, danceable',
            'Techno pop' => 'techno pop, pulsing synths, modern',
            'Drum & bass pop' => 'drum and bass pop, fast breakbeats, melodic',
            'Trap pop' => 'trap pop, modern 808s, melodic hook',
            'Lo-fi pop' => 'lo-fi pop, mellow, warm texture',
            'Gospel / soulkoor' => 'gospel soul choir, uplifting, rich harmonies',
            'Musical / theater' => 'musical theatre, expressive, dramatic',
            'Orkestrale pop' => 'orchestral pop, cinematic, grand',
            'Kinderlied / vrolijk' => 'cheerful kids song, simple catchy melody',
            'Carnaval' => 'dutch carnaval, brass, festive, sing-along',
            'Hardstyle feest' => 'party hardstyle, energetic kick, festival',
        ];

        $voiceMapping = [
            'Mannenstem' => 'male vocals',
            'Vrouwenstem' => 'female vocals',
            'Duet' => 'duet, male and female vocals',
            'Groep / koor' => 'group vocals, choir backing',
            'Warme mannenstem' => 'warm male vocals',
            'Warme vrouwenstem' => 'warm female vocals',
            'Hoge popstem' => 'high pop vocals',
            'Lage rustige stem' => 'low calm vocals',
            'Rauwe rockstem' => 'raspy rock vocals',
            'Soulvolle stem' => 'soulful vocals',
            'Rap coupletten + gezongen refrein' => 'rap verses, sung chorus',
            'Kindvriendelijke stem' => 'friendly clear vocals',
            'Familiekoor' => 'family choir vocals',
            'Stadionkoor' => 'stadium choir vocals',
            'Koor in refrein' => 'choir in chorus',
            'Praat-zang' => 'spoken-sung vocals',
            'Instrumentaal intro met zang' => 'instrumental intro, lead vocals',
        ];

        $tags = $styleMapping[$style] ?? 'dutch pop';
        $voice = $voiceMapping[$vocals] ?? 'male vocals';
        $tempoTag = $tempo !== '' ? ", tempo: {$tempo}" : '';

        return [
            'prompt' => "A Dutch-language song about {$songRequest->category_title}. {$tone} mood.".($tempo ? " Tempo: {$tempo}." : ''),
            'tags' => "{$tags}, {$voice}{$tempoTag}, dutch lyrics, professional production",
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
