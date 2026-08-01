<?php

namespace App\Mail;

use App\Models\SongRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public SongRequest $songRequest) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Je bestelling is bevestigd — aanvraag #{$this->songRequest->id}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-confirmation',
            with: [
                'orderId' => $this->songRequest->id,
                'recipientName' => $this->songRequest->recipient_name,
                'categoryTitle' => $this->songRequest->category_title,
                'amount' => number_format($this->songRequest->price_cents / 100, 2, ',', '.'),
                'paymentMethod' => $this->songRequest->payment_provider === 'discount_code'
                    ? 'Kortingscode'
                    : 'Stripe',
                'paidAt' => $this->songRequest->paid_at?->timezone('Europe/Amsterdam')->format('d-m-Y H:i'),
            ],
        );
    }
}
