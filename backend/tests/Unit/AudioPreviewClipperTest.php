<?php

namespace Tests\Unit;

use App\Services\Audio\AudioPreviewClipper;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AudioPreviewClipperTest extends TestCase
{
    public function test_it_asks_ffmpeg_for_only_seconds_30_through_45(): void
    {
        $binary = tempnam(sys_get_temp_dir(), 'fake-ffmpeg-');
        $arguments = tempnam(sys_get_temp_dir(), 'ffmpeg-arguments-');

        file_put_contents($binary, implode("\n", [
            '#!/bin/sh',
            'printf "%s\n" "$@" > '.escapeshellarg($arguments),
            'for last_arg in "$@"; do :; done',
            'printf "fifteen-second-preview" > "$last_arg"',
        ]));
        chmod($binary, 0755);

        config([
            'media.ffmpeg_binary' => $binary,
            'media.preview_start_seconds' => 30,
            'media.preview_duration_seconds' => 15,
        ]);

        $source = UploadedFile::fake()->create('full-song.mp3', 100, 'audio/mpeg');
        $preview = app(AudioPreviewClipper::class)->clipToTemporaryFile($source);
        $args = file_get_contents($arguments);

        $this->assertFileExists($preview);
        $this->assertSame('fifteen-second-preview', file_get_contents($preview));
        $this->assertMatchesRegularExpression('/-ss\\R30\\R/', $args);
        $this->assertMatchesRegularExpression('/-t\\R15\\R/', $args);

        @unlink($preview);
        @unlink($binary);
        @unlink($arguments);
    }
}
