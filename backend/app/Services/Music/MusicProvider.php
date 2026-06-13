<?php

namespace App\Services\Music;

use App\Models\SongRequest;

interface MusicProvider
{
    /**
     * Start of simuleer muziekgeneratie op basis van definitieve lyrics.
     *
     * @return array{reference: string, prompt: string, status: string}
     */
    public function generate(SongRequest $songRequest, string $lyrics, array $intake): array;
}

