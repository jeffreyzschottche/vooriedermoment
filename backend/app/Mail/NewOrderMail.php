<?php

namespace App\Mail;

use App\Models\SongRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

/**
 * Interne notificatie naar de eigenaar: er is een betaalde aanvraag binnen.
 * Bevat een korte samenvatting + de Suno-klare JSON als bijlage.
 */
class NewOrderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public SongRequest $songRequest,
        public array $payload,
        public string $jsonFilename,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Nieuwe bestelling #{$this->songRequest->id} — {$this->songRequest->category_title} voor {$this->songRequest->recipient_name}",
        );
    }

    public function content(): Content
    {
        $request = $this->songRequest;
        $intake = $request->intake ?? [];
        $text = static fn (mixed $value): string => is_array($value)
            ? implode(', ', array_filter(array_map(static fn ($item) => is_scalar($item) ? trim((string) $item) : '', $value)))
            : (is_scalar($value) ? trim((string) $value) : '');

        $music = array_filter([
            'Genre / muziekstijl' => $text($intake['musicStyle'] ?? ''),
            'Stem / zangstijl' => $text($intake['vocals'] ?? ''),
            'Sfeer' => $text($intake['tone'] ?? ''),
            'Tempo' => $text($intake['tempo'] ?? ''),
            'Suno-stijl' => $this->payload['suno']['style'] ?? '',
        ], static fn (string $value) => $value !== '');

        $contact = array_filter([
            'E-mail klant' => (string) $request->email,
            'Contactpersoon' => $text($intake['contactName'] ?? ''),
            'Voor' => (string) $request->recipient_name,
            'Van' => $text($intake['fromName'] ?? ''),
        ], static fn (string $value) => $value !== '');

        // Alle overige formulierantwoorden, zodat deze ene mail compleet is.
        $shown = ['email', 'musicStyle', 'vocals', 'tone', 'tempo', 'contactName', 'recipientName', 'fromName'];
        $answers = [];
        foreach ($intake as $field => $value) {
            $value = $text($value);
            if ($value !== '' && ! in_array($field, $shown, true) && ! str_starts_with((string) $field, '_')) {
                $answers[Str::headline((string) $field)] = $value;
            }
        }

        return new Content(
            view: 'emails.new-order',
            with: [
                'order' => $this->payload,
                'payment' => [
                    'Bedrag' => '€'.number_format($request->price_cents / 100, 2, ',', '.'),
                    'Betaalmethode' => $request->payment_provider === 'discount_code' ? 'Kortingscode' : 'Stripe',
                    'Betaald op' => $request->paid_at?->timezone('Europe/Amsterdam')->format('d-m-Y H:i') ?? '—',
                    'Referentie' => $request->payment_intent_reference ?: ($request->payment_reference ?: '—'),
                ],
                'contact' => $contact,
                'music' => $music,
                'answers' => $answers,
            ],
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromData(
                fn () => json_encode($this->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                $this->jsonFilename,
            )->withMime('application/json'),
        ];
    }
}
