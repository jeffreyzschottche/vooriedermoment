<?php

namespace App\Mail;

use App\Models\SongRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SamplesReadyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public SongRequest $songRequest,
        public array $samples
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Je 4 samples zijn klaar! - {$this->songRequest->category_title}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.samples-ready',
            with: [
                'songRequest' => $this->songRequest,
                'samples' => $this->samples,
                'recipientName' => $this->songRequest->intake['recipientName']
                    ?? $this->songRequest->intake['companyName']
                    ?? $this->songRequest->intake['clubName']
                    ?? 'daar',
            ],
        );
    }
}
