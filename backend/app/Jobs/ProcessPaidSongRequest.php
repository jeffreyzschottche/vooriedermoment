<?php

namespace App\Jobs;

use App\Mail\ProductionNeedsAttentionMail;
use App\Models\SongRequest;
use App\Services\Orders\OrderExporter;
use App\Services\Production\SongProductionPipeline;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ProcessPaidSongRequest implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 900;

    public function __construct(public int $songRequestId) {}

    public function middleware(): array
    {
        return [
            (new WithoutOverlapping('paid-song-request-'.$this->songRequestId))
                ->releaseAfter(15)
                ->expireAfter(960),
        ];
    }

    public function backoff(): array
    {
        return [30, 120, 300];
    }

    public function handle(
        SongProductionPipeline $production,
        OrderExporter $orders,
    ): void {
        $songRequest = SongRequest::find($this->songRequestId);

        if (! $songRequest || ! $songRequest->paid_at) {
            return;
        }

        if (in_array($songRequest->status, ['paid', 'production_failed'], true)) {
            $songRequest = $production->run($songRequest);
        }

        if (! in_array($songRequest->status, ['music_prompt_ready', 'production_ready'], true)) {
            return;
        }

        if (! in_array($songRequest->automation_status, ['claimed', 'completed'], true)) {
            $songRequest->forceFill([
                'automation_status' => 'ready',
                'automation_last_error' => null,
            ])->save();
        }

        $orders->export($songRequest->refresh());
    }

    public function failed(?Throwable $exception): void
    {
        $songRequest = SongRequest::find($this->songRequestId);
        if (! $songRequest) {
            return;
        }

        $error = $exception?->getMessage() ?? 'Onbekende verwerkingsfout.';
        $songRequest->forceFill([
            'status' => 'production_failed',
            'automation_status' => 'failed',
            'automation_last_error' => $error,
            'production_steps' => [
                'lyrics' => ['status' => 'failed'],
                'music' => ['status' => 'waiting'],
                'error' => $error,
            ],
        ])->save();

        // Een betaalde klant mag nooit in stilte op een vastgelopen productie
        // wachten. De technische fout blijft intern; de klant krijgt één
        // duidelijke, actiegerichte update per aanvraag.
        if ($songRequest->email && ! $songRequest->production_failure_notified_at) {
            Mail::to($songRequest->email)->send(new ProductionNeedsAttentionMail($songRequest));

            $songRequest->forceFill([
                'production_failure_notified_at' => now(),
            ])->save();
        }
    }
}
