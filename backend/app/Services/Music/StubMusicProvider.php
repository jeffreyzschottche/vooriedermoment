<?php

namespace App\Services\Music;

use App\Models\SongRequest;

class StubMusicProvider implements MusicProvider
{
    public function __construct(private MusicPromptBuilder $promptBuilder)
    {
    }

    public function generate(SongRequest $songRequest, string $lyrics, array $intake): array
    {
        $prompt = $this->promptBuilder->build($songRequest, $lyrics, $intake);

        return [
            'reference' => 'stub-music-'.$songRequest->id.'-'.now()->format('YmdHis'),
            'prompt' => $prompt,
            'status' => 'stubbed',
        ];
    }
}

