<?php

namespace App\Services\Production;

use App\Mail\DiscountLyricsMail;
use App\Models\SongRequest;
use App\Services\Lyrics\LyricsGenerator;
use App\Services\Music\MusicProvider;
use Throwable;
use Illuminate\Support\Facades\Mail;

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
            // De geheime kortingscode is ook een interne lyric-generator. Voor
            // die eigen test-/werkflow willen we altijd de beste AI-versie per
            // e-mail ontvangen, ook wanneer de klantgerichte volledigheidscheck
            // nog details mist. Reguliere betaalde orders blijven strikt.
            $requireCompleteCoverage = (bool) config('ai.lyrics_require_complete_coverage', true);
            if ($songRequest->payment_provider === 'discount_code') {
                config()->set('ai.lyrics_require_complete_coverage', false);
            }

            try {
                $generated = $this->lyrics->generate($songRequest->category, $songRequest->intake ?? []);
            } finally {
                // Queue workers leven lang: laat de uitzonderingsregel nooit
                // per ongeluk doorlekken naar een volgende klantorder.
                config()->set('ai.lyrics_require_complete_coverage', $requireCompleteCoverage);
            }

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

            $songRequest->refresh();
            if (
                $songRequest->payment_provider === 'discount_code'
                && $songRequest->email
                && ! $songRequest->discount_lyrics_sent_at
            ) {
                Mail::to($songRequest->email)->send(new DiscountLyricsMail($songRequest));
                $songRequest->forceFill(['discount_lyrics_sent_at' => now()])->save();
            }

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
