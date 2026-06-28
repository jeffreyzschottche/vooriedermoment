<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SongRequest extends Model
{
    protected $fillable = [
        'category',
        'category_title',
        'email',
        'intake',
        'lyrics',
        'lyrics_preview',
        'final_lyrics',
        'music_prompt',
        'music_reference',
        'production_steps',
        'production_started_at',
        'production_finished_at',
        'status',
        'price_cents',
        'payment_reference',
        // Sample fields
        'samples',
        'samples_generated_at',
        'chosen_sample_id',
        'selection_token',
        // Final song
        'final_song_url',
        'final_song_duration',
        // Spotify
        'spotify_track_id',
        'spotify_uri',
        'released_at',
        // Pull-export naar Suno-macro
        'export_path',
        'exported_at',
    ];

    protected $casts = [
        'intake' => 'array',
        'production_steps' => 'array',
        'samples' => 'array',
        'production_started_at' => 'datetime',
        'production_finished_at' => 'datetime',
        'samples_generated_at' => 'datetime',
        'released_at' => 'datetime',
        'exported_at' => 'datetime',
        'price_cents' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (SongRequest $request) {
            if (empty($request->selection_token)) {
                $request->selection_token = Str::random(32);
            }
        });
    }

    public function getRecipientNameAttribute(): string
    {
        return $this->intake['recipientName']
            ?? $this->intake['companyName']
            ?? $this->intake['clubName']
            ?? $this->intake['babyName']
            ?? 'Klant';
    }

    public function isPaid(): bool
    {
        return in_array($this->status, ['paid', 'producing', 'music_prompt_ready', 'production_ready', 'samples_ready', 'sample_chosen', 'ready_for_release', 'released']);
    }

    public function hasSamples(): bool
    {
        return !empty($this->samples) && count($this->samples) > 0;
    }
}
