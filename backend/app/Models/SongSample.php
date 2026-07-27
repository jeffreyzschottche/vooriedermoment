<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SongSample extends Model
{
    protected $fillable = [
        'song_request_id',
        'position',
        'title',
        'storage_disk',
        'preview_path',
        'cover_path',
        'original_audio_path',
        'suno_source_url',
        'is_chosen',
        'expires_at',
    ];

    protected $casts = [
        'position' => 'integer',
        'is_chosen' => 'boolean',
        'expires_at' => 'datetime',
    ];

    public function songRequest(): BelongsTo
    {
        return $this->belongsTo(SongRequest::class);
    }
}
