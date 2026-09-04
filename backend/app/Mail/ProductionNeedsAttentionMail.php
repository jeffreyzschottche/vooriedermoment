<?php

namespace App\Mail;

use App\Models\SongRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProductionNeedsAttentionMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public SongRequest $songRequest) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "We controleren je persoonlijke nummer — aanvraag #{$this->songRequest->id}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.production-needs-attention',
            with: [
                'orderId' => $this->songRequest->id,
                'recipientName' => $this->songRequest->recipient_name,
            ],
        );
    }
}
