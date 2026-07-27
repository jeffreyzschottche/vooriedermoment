<?php

namespace App\Services\Orders;

use App\Models\SongRequest;
use App\Models\SongSample;

class SamplePayloadFactory
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function forSelection(SongRequest $songRequest): array
    {
        return $songRequest->songSamples
            ->sortBy('position')
            ->map(fn (SongSample $sample) => [
                'id' => $sample->position,
                'title' => $sample->title,
                'duration' => 15,
                'url' => url("/api/v1/select/{$songRequest->selection_token}/samples/{$sample->id}/preview"),
                'cover_url' => url("/api/v1/select/{$songRequest->selection_token}/samples/{$sample->id}/cover"),
            ])
            ->values()
            ->all();
    }
}
