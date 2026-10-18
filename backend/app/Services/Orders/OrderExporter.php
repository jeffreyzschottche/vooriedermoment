<?php

namespace App\Services\Orders;

use App\Mail\NewOrderMail;
use App\Models\SongRequest;
use App\Services\Music\SunoStyleTags;
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
                'style' => SunoStyleTags::style($intake),
                'lyrics' => $lyrics,
                'vocal_gender' => $this->vocalGender($intake),
                'make_instrumental' => $this->isInstrumental($intake),
            ],
            // Ruwe intake voor context/handmatige tweaks.
            'intake' => $intake,
            // Bouw dit rechtstreeks op vanaf APP_URL. De automation-payload mag
            // niet stukgaan door een verouderde route-cache op productie.
            'admin_upload_url' => url('/admin/upload/'.rawurlencode((string) $songRequest->admin_upload_token)),
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

    private function isInstrumental(array $intake): bool
    {
        return str_contains(Str::lower((string) ($intake['vocals'] ?? '')), 'instrumentaal');
    }

    private function vocalGender(array $intake): ?string
    {
        return SunoStyleTags::vocalGender($intake['vocals'] ?? null);
    }
}
