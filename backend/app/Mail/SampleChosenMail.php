<?php

namespace App\Mail;

use App\Models\SongRequest;
use App\Models\SongSample;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SampleChosenMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public SongRequest $songRequest,
        public SongSample $chosenSample,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Klant heeft gekozen: #{$this->songRequest->id} — {$this->chosenSample->title}",
        );
    }

    public function content(): Content
    {
        $intake = $this->songRequest->intake ?? [];
        $lyrics = trim((string) ($this->songRequest->final_lyrics ?: $this->songRequest->lyrics ?: ''));

        return new Content(
            view: 'emails.sample-chosen',
            with: [
                'orderId' => $this->songRequest->id,
                'recipientName' => $this->songRequest->recipient_name,
                'categoryTitle' => $this->songRequest->category_title,
                'customerEmail' => $this->songRequest->email,
                'chosenPosition' => $this->chosenSample->position,
                'chosenTitle' => $this->chosenSample->title,
                'sunoUrl' => $this->chosenSample->suno_source_url,
                'musicStyle' => $intake['musicStyle'] ?? '—',
                'vocals' => $intake['vocals'] ?? '—',
                'lyrics' => $lyrics,
            ],
        );
    }
}
