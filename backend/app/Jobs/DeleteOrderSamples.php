<?php

namespace App\Jobs;

use App\Models\SongRequest;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class DeleteOrderSamples implements ShouldQueue
{
    use Queueable;

    public int $tries = 5;

    public int $timeout = 120;

    public function __construct(public int $songRequestId) {}

    public function backoff(): array
    {
        return [10, 30, 120, 300];
    }

    public function handle(): void
    {
        $songRequest = SongRequest::with('songSamples')->find($this->songRequestId);

        if (! $songRequest || ! $songRequest->chosen_sample_id || $songRequest->samples_deleted_at) {
            return;
        }

        foreach ($songRequest->songSamples->groupBy('storage_disk') as $disk => $samples) {
            $paths = $samples
                ->flatMap(fn ($sample) => [
                    $sample->preview_path,
                    $sample->cover_path,
                    $sample->original_audio_path,
                ])
                ->filter()
                ->unique()
                ->values()
                ->all();

            Storage::disk($disk)->delete($paths);

            foreach ($paths as $path) {
                if (Storage::disk($disk)->exists($path)) {
                    throw new RuntimeException("Tijdelijk samplebestand kon niet worden verwijderd: {$path}");
                }
            }
        }

        DB::transaction(function (): void {
            $lockedOrder = SongRequest::whereKey($this->songRequestId)->lockForUpdate()->first();

            if (! $lockedOrder || $lockedOrder->samples_deleted_at) {
                return;
            }

            $lockedOrder->songSamples()->delete();
            $lockedOrder->forceFill([
                'samples' => null,
                'samples_deleted_at' => now(),
            ])->save();
        });
    }
}
