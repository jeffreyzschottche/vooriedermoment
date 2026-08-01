<?php

namespace App\Jobs;

use App\Mail\PaymentConfirmationMail;
use App\Models\SongRequest;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendPaymentConfirmation implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public int $tries = 5;

    public int $timeout = 60;

    public int $uniqueFor = 3600;

    public function __construct(public int $songRequestId) {}

    public function uniqueId(): string
    {
        return (string) $this->songRequestId;
    }

    public function backoff(): array
    {
        return [30, 120, 300, 600];
    }

    public function handle(): void
    {
        $songRequest = SongRequest::find($this->songRequestId);

        if (! $songRequest || ! $songRequest->email || ! $songRequest->paid_at || $songRequest->payment_confirmation_sent_at) {
            return;
        }

        Mail::to($songRequest->email)->send(new PaymentConfirmationMail($songRequest));

        $songRequest->forceFill([
            'payment_confirmation_sent_at' => now(),
        ])->save();
    }
}
