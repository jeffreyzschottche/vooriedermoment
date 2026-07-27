<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\SongSample;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SampleAssetController extends Controller
{
    public function preview(string $token, SongSample $songSample): StreamedResponse
    {
        $this->authorizeToken($token, $songSample);

        return Storage::disk($songSample->storage_disk)->response(
            $songSample->preview_path,
            null,
            ['Cache-Control' => 'private, max-age=3600'],
        );
    }

    public function cover(string $token, SongSample $songSample): StreamedResponse
    {
        $this->authorizeToken($token, $songSample);

        return Storage::disk($songSample->storage_disk)->response(
            $songSample->cover_path,
            null,
            ['Cache-Control' => 'private, max-age=3600'],
        );
    }

    private function authorizeToken(string $token, SongSample $songSample): void
    {
        abort_unless(
            hash_equals((string) $songSample->songRequest->selection_token, $token),
            404,
        );
    }
}
