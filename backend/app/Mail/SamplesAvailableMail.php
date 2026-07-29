<?php

namespace App\Mail;

use App\Models\SongRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SamplesAvailableMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public SongRequest $songRequest,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Je samples zijn klaar! - Voor Ieder Moment',
        );
    }

    public function content(): Content
    {
        $expiresAt = $this->songRequest->songSamples()->first()?->expires_at;

        return new Content(
            view: 'emails.samples-available',
            with: [
                'recipientName' => $this->songRequest->recipient_name,
                'categoryTitle' => $this->songRequest->category_title,
                'password' => $this->songRequest->customer_password,
                'samplesUrl' => route('samples.password', ['token' => $this->songRequest->selection_token]),
                'expiresAt' => $expiresAt?->format('d-m-Y'),
            ],
        );
    }
}
