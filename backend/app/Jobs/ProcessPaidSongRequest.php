<?php

namespace App\Jobs;

use App\Models\SongRequest;
use App\Services\Orders\OrderExporter;
use App\Services\Production\SongProductionPipeline;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Throwable;

class ProcessPaidSongRequest implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 600;

    public function __construct(public int $songRequestId) {}

    public function middleware(): array
    {
        return [
            (new WithoutOverlapping('paid-song-request-'.$this->songRequestId))
                ->releaseAfter(15)
                ->expireAfter(660),
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
        SongRequest::whereKey($this->songRequestId)->update([
            'status' => 'production_failed',
            'automation_status' => 'failed',
            'automation_last_error' => $exception?->getMessage() ?? 'Onbekende verwerkingsfout.',
        ]);
    }
}
