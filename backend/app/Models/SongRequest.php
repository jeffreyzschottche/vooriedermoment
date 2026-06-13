<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    ];

    protected $casts = [
        'intake' => 'array',
        'production_steps' => 'array',
        'production_started_at' => 'datetime',
        'production_finished_at' => 'datetime',
        'price_cents' => 'integer',
    ];
}
