<?php

namespace App\Mail;

use App\Models\SongRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Admin-alarm: een betaalde order is na alle pogingen vastgelopen.
 */
class ProductionFailedAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public SongRequest $songRequest,
        public string $error,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Actie nodig: productie mislukt — aanvraag #{$this->songRequest->id} ({$this->songRequest->category_title})",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.production-failed-admin',
            with: [
                'orderId' => $this->songRequest->id,
                'categoryTitle' => $this->songRequest->category_title,
                'recipientName' => $this->songRequest->recipient_name,
                'customerEmail' => $this->songRequest->email,
                'paidAt' => $this->songRequest->paid_at?->timezone('Europe/Amsterdam')->format('d-m-Y H:i'),
                'error' => $this->error,
            ],
        );
    }
}
