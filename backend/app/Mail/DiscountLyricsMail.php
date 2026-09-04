<?php

namespace App\Mail;

use App\Models\SongRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DiscountLyricsMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public SongRequest $songRequest) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: "Je songtekst — aanvraag #{$this->songRequest->id}");
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.discount-lyrics',
            with: [
                'orderId' => $this->songRequest->id,
                'recipientName' => $this->songRequest->recipient_name,
                'lyrics' => $this->songRequest->final_lyrics ?: $this->songRequest->lyrics,
            ],
        );
    }
}
