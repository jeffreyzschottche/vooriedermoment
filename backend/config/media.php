<?php

return [
    'ffmpeg_binary' => env('FFMPEG_BINARY', 'ffmpeg'),
    'ffmpeg_timeout_seconds' => (int) env('FFMPEG_TIMEOUT_SECONDS', 90),
    'preview_start_seconds' => (int) env('AUDIO_PREVIEW_START_SECONDS', 30),
    'preview_duration_seconds' => (int) env('AUDIO_PREVIEW_DURATION_SECONDS', 15),
    'preview_bitrate' => env('AUDIO_PREVIEW_BITRATE', '192k'),
];
