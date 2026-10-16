<?php

namespace App\Jobs;

use App\Models\SongRequest;
use App\Services\Orders\OrderExporter;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Stuurt de admin één "nieuwe betaalde aanvraag"-mail (lyrics + Suno-JSON).
 * Bewust een eigen job: een mailstoring mag de productiejob niet laten falen,
 * want dan zou de klant onterecht een "we controleren je nummer"-mail krijgen.
 */
class SendOrderNotification implements ShouldBeUnique, ShouldQueue
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

    public function handle(OrderExporter $orders): void
    {
        $songRequest = SongRequest::find($this->songRequestId);

        if ($songRequest) {
            $orders->export($songRequest);
        }
    }
}
