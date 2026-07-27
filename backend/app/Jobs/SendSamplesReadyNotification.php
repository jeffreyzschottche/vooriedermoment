<?php

namespace App\Jobs;

use App\Mail\SamplesReadyMail;
use App\Models\SongRequest;
use App\Services\Orders\SamplePayloadFactory;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendSamplesReadyNotification implements ShouldBeUnique, ShouldQueue
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

    public function handle(SamplePayloadFactory $payloads): void
    {
        $songRequest = SongRequest::with('songSamples')->find($this->songRequestId);

        if (! $songRequest || ! $songRequest->email || $songRequest->samples_email_sent_at) {
            return;
        }

        Mail::to($songRequest->email)->send(new SamplesReadyMail(
            $songRequest,
            $payloads->forSelection($songRequest),
        ));

        $songRequest->forceFill([
            'samples_email_sent_at' => now(),
        ])->save();
    }
}
