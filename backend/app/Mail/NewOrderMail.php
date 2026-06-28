<?php

namespace App\Mail;

use App\Models\SongRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

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
        public string $jsonPath,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Nieuwe betaalde aanvraag #{$this->songRequest->id} — {$this->songRequest->category_title}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-order',
            with: [
                'order' => $this->payload,
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
                basename($this->jsonPath),
            )->withMime('application/json'),
        ];
    }
}
