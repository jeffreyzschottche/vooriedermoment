<?php

namespace App\Services\Audio;

use Illuminate\Http\UploadedFile;
use RuntimeException;
use Symfony\Component\Process\Process;

class AudioPreviewClipper
{
    /**
     * Knip uitsluitend het ingestelde fragment naar een tijdelijk mp3-bestand.
     * De volledige upload blijft in PHP's tijdelijke uploadmap en wordt nooit
     * naar permanente Laravel-storage gekopieerd.
     */
    public function clipToTemporaryFile(UploadedFile $audio): string
    {
        $source = $audio->getRealPath();
        if (! is_string($source) || $source === '' || ! is_file($source)) {
            throw new RuntimeException('Het geüploade audiobestand kan niet worden gelezen.');
        }

        $temporary = tempnam(storage_path('framework/cache'), 'audio-preview-');
        if ($temporary === false) {
            throw new RuntimeException('Er kon geen tijdelijk previewbestand worden aangemaakt.');
        }

        $process = new Process([
            (string) config('media.ffmpeg_binary', 'ffmpeg'),
            '-hide_banner',
            '-loglevel',
            'error',
            '-y',
            '-ss',
            (string) max(0, (int) config('media.preview_start_seconds', 30)),
            '-i',
            $source,
            '-t',
            (string) max(1, (int) config('media.preview_duration_seconds', 15)),
            '-vn',
            '-codec:a',
            'libmp3lame',
            '-b:a',
            (string) config('media.preview_bitrate', '192k'),
            '-f',
            'mp3',
            $temporary,
        ]);
        $process->setTimeout(max(15, (int) config('media.ffmpeg_timeout_seconds', 90)));
        $process->run();

        if (! $process->isSuccessful() || ! is_file($temporary) || filesize($temporary) === 0) {
            @unlink($temporary);
            $error = trim($process->getErrorOutput()) ?: 'FFmpeg leverde geen previewbestand op.';

            throw new RuntimeException('Audio-preview maken is mislukt: '.$error);
        }

        return $temporary;
    }
}
