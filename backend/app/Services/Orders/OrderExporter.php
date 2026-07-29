<?php

namespace App\Services\Orders;

use App\Mail\NewOrderMail;
use App\Models\SongRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

/**
 * Bouwt de macro-payload vanuit de database en verstuurt één notificatiemail.
 * De API is de bron van waarheid; er is geen lokaal JSON-bestand nodig.
 */
class OrderExporter
{
    public function export(SongRequest $songRequest): bool
    {
        if (! config('orders.enabled', true) || $songRequest->order_notification_sent_at) {
            return false;
        }

        try {
            $payload = $this->buildPayload($songRequest);
            $sent = $this->notify($songRequest, $payload);

            if ($sent) {
                $songRequest->forceFill([
                    'order_notification_sent_at' => now(),
                ])->save();
            }

            return $sent;
        } catch (Throwable $e) {
            Log::error('Order export failed', [
                'song_request_id' => $songRequest->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
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
            'created_at' => $songRequest->created_at?->toIso8601String() ?? now()->toIso8601String(),
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
            // Admin upload link
            'admin_upload_url' => route('admin.upload.show', ['token' => $songRequest->admin_upload_token]),
        ];
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

    private function notify(SongRequest $songRequest, array $payload): bool
    {
        $to = config('orders.notify_email');
        if (! $to) {
            Log::warning('ORDERS_NOTIFY_EMAIL ontbreekt; ordermail niet verstuurd.', [
                'song_request_id' => $songRequest->id,
            ]);

            return false;
        }

        Mail::to($to)->send(new NewOrderMail(
            $songRequest,
            $payload,
            $this->filename($songRequest),
        ));

        return true;
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
