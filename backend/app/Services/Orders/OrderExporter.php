<?php

namespace App\Services\Orders;

use App\Mail\NewOrderMail;
use App\Models\SongRequest;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

/**
 * Schrijft per betaalde aanvraag een Suno-klare JSON weg naar een lokale map
 * (config orders.path) en stuurt optioneel een notificatiemail. De JSON is zo
 * opgebouwd dat een lokale macro (Keysmith) er direct titel/stijl/lyrics uit
 * kan plukken om een nummer te genereren op suno.com.
 *
 * Mag de checkout nooit laten falen: alle fouten worden gelogd, niet gegooid.
 */
class OrderExporter
{
    public function export(SongRequest $songRequest): ?string
    {
        if (! config('orders.enabled', true)) {
            return null;
        }

        try {
            $payload = $this->buildPayload($songRequest);
            $path = $this->writeFile($songRequest, $payload);
            $this->notify($songRequest, $payload, $path);

            return $path;
        } catch (Throwable $e) {
            Log::error('Order export failed', [
                'song_request_id' => $songRequest->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * De volledige, macro-vriendelijke JSON-structuur.
     */
    public function buildPayload(SongRequest $songRequest): array
    {
        $intake = $songRequest->intake ?? [];
        $lyrics = trim((string) ($songRequest->final_lyrics ?: $songRequest->lyrics ?: ''));

        return [
            'order_id' => $songRequest->id,
            'filename' => $this->filename($songRequest),
            'created_at' => now()->toIso8601String(),
            'status' => $songRequest->status,
            'category' => $songRequest->category,
            'category_title' => $songRequest->category_title,
            'customer_email' => $songRequest->email,
            'recipient_name' => $songRequest->recipient_name,
            'price_eur' => number_format($songRequest->price_cents / 100, 2, '.', ''),
            // Wat de Suno-macro nodig heeft: titel, stijl-tags en lyrics.
            'suno' => [
                'title' => $this->title($songRequest, $intake),
                'style' => $this->style($intake),
                'lyrics' => $lyrics,
                'make_instrumental' => $this->isInstrumental($intake),
            ],
            // Ruwe intake voor context/handmatige tweaks.
            'intake' => $intake,
        ];
    }

    private function writeFile(SongRequest $songRequest, array $payload): string
    {
        $dir = (string) config('orders.path');
        File::ensureDirectoryExists($dir);

        $path = rtrim($dir, '/').'/'.$this->filename($songRequest);

        File::put($path, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        // Onthoud het pad zodat de ack-endpoint het bestand later kan opruimen.
        $songRequest->forceFill(['export_path' => $path])->saveQuietly();

        return $path;
    }

    /**
     * Bestandsnaam: categorie + naam, bv. moederdag-voor-anna-12.json.
     * Het id achteraan houdt 'm uniek bij dezelfde categorie/naam.
     */
    public function filename(SongRequest $songRequest): string
    {
        $category = Str::slug($songRequest->category_title ?: $songRequest->category) ?: 'aanvraag';
        $name = Str::slug($songRequest->recipient_name) ?: 'klant';

        return "{$category}-voor-{$name}-{$songRequest->id}.json";
    }

    private function notify(SongRequest $songRequest, array $payload, string $path): void
    {
        $to = config('orders.notify_email');
        if (! $to) {
            return;
        }

        Mail::to($to)->send(new NewOrderMail($songRequest, $payload, $path));
    }

    private function title(SongRequest $songRequest, array $intake): string
    {
        $name = $songRequest->recipient_name;

        return $name && $name !== 'Klant'
            ? "{$songRequest->category_title} - {$name}"
            : (string) $songRequest->category_title;
    }

    /**
     * Vertaal de Nederlandse formulierkeuzes naar Suno-stijl-tags.
     */
    private function style(array $intake): string
    {
        $styleMap = [
            'Nederlandstalige pop' => 'dutch pop, catchy, radio-friendly',
            'Feest / meezinger' => 'dutch party schlager, upbeat, sing-along, festive',
            'Akoestisch en klein' => 'acoustic, intimate, warm, stripped-back',
            'Singer-songwriter' => 'singer-songwriter, acoustic, personal, warm',
            'Pop ballad' => 'dutch pop ballad, emotional, melodic',
            'Rock / anthem' => 'anthemic rock, guitar-driven, energetic',
            'Urban pop' => 'urban pop, modern beats, rhythmic',
            'Hiphop / rap coupletten' => 'dutch hip-hop verses, melodic chorus, modern beats',
            'Dance pop' => 'dance pop, electronic, energetic, festival-ready',
            'Disco / funk' => 'disco funk, groovy bass, upbeat',
            'Country pop' => 'country pop, warm guitars, sing-along',
            'Indie pop' => 'indie pop, fresh, catchy, organic',
            'Schlager / apres-ski' => 'dutch schlager, apres-ski, festive, sing-along',
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

        $voiceMap = [
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

        $style = $styleMap[$intake['musicStyle'] ?? ''] ?? 'dutch pop, catchy, radio-friendly';
        $voice = $voiceMap[$intake['vocals'] ?? ''] ?? 'lead vocals';
        $tempo = trim((string) ($intake['tempo'] ?? ''));
        $tempoTag = $tempo !== '' ? ', tempo: '.$tempo : '';

        return "{$style}, {$voice}{$tempoTag}, dutch lyrics, professional production";
    }

    private function isInstrumental(array $intake): bool
    {
        return str_contains(Str::lower((string) ($intake['vocals'] ?? '')), 'instrumentaal');
    }
}
