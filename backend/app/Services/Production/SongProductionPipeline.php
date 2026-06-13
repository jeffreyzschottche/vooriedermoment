<?php

namespace App\Services\Production;

use App\Models\SongRequest;
use App\Services\Lyrics\LyricsGenerator;
use App\Services\Music\MusicProvider;
use Throwable;

class SongProductionPipeline
{
    public function __construct(
        private LyricsGenerator $lyrics,
        private MusicProvider $music,
    ) {
    }

    public function run(SongRequest $songRequest): SongRequest
    {
        $songRequest->update([
            'status' => 'producing',
            'production_started_at' => $songRequest->production_started_at ?? now(),
            'production_steps' => [
                'lyrics' => ['status' => 'running', 'started_at' => now()->toISOString()],
                'music' => ['status' => 'waiting'],
            ],
        ]);

        try {
            $generated = $this->lyrics->generate($songRequest->category, $songRequest->intake ?? []);

            $songRequest->update([
                'lyrics' => $generated['lyrics'],
                'lyrics_preview' => $generated['preview'],
                'final_lyrics' => $generated['lyrics'],
                'production_steps' => [
                    'lyrics' => [
                        'status' => 'done',
                        'used_ai' => $generated['used_ai'],
                        'finished_at' => now()->toISOString(),
                    ],
                    'music' => ['status' => 'running', 'started_at' => now()->toISOString()],
                ],
            ]);

            $music = $this->music->generate($songRequest->refresh(), $generated['lyrics'], $songRequest->intake ?? []);

            $songRequest->update([
                'music_prompt' => $music['prompt'],
                'music_reference' => $music['reference'],
                'status' => $music['status'] === 'stubbed' ? 'music_prompt_ready' : 'production_ready',
                'production_finished_at' => now(),
                'production_steps' => [
                    'lyrics' => [
                        'status' => 'done',
                        'used_ai' => $generated['used_ai'],
                    ],
                    'music' => [
                        'status' => $music['status'],
                        'reference' => $music['reference'],
                        'finished_at' => now()->toISOString(),
                    ],
                ],
            ]);
        } catch (Throwable $e) {
            $songRequest->update([
                'status' => 'production_failed',
                'production_steps' => [
                    'lyrics' => ['status' => 'unknown'],
                    'music' => ['status' => 'unknown'],
                    'error' => $e->getMessage(),
                ],
            ]);

            throw $e;
        }

        return $songRequest->refresh();
    }
}

