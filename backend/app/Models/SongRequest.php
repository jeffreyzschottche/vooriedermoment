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
        'status',
        'price_cents',
        'payment_reference',
    ];

    protected $casts = [
        'intake' => 'array',
        'price_cents' => 'integer',
    ];
}
