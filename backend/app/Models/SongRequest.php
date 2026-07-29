<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'payment_provider',
        'payment_intent_reference',
        'paid_at',
        'payment_fulfillment_queued_at',
        'order_notification_sent_at',
        // Sample fields
        'samples',
        'samples_generated_at',
        'samples_email_sent_at',
        'chosen_sample_id',
        'chosen_sample_position',
        'chosen_sample_title',
        'chosen_suno_source_url',
        'samples_deleted_at',
        'selection_token',
        'admin_upload_token',
        'customer_password',
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
        // Automation queue lease
        'automation_status',
        'automation_claimed_by',
        'automation_claim_token_hash',
        'automation_claimed_at',
        'automation_claim_expires_at',
        'automation_attempts',
        'automation_last_error',
    ];

    protected $casts = [
        'intake' => 'array',
        'production_steps' => 'array',
        'samples' => 'array',
        'production_started_at' => 'datetime',
        'production_finished_at' => 'datetime',
        'samples_generated_at' => 'datetime',
        'samples_email_sent_at' => 'datetime',
        'samples_deleted_at' => 'datetime',
        'chosen_sample_position' => 'integer',
        'released_at' => 'datetime',
        'exported_at' => 'datetime',
        'paid_at' => 'datetime',
        'payment_fulfillment_queued_at' => 'datetime',
        'order_notification_sent_at' => 'datetime',
        'automation_claimed_at' => 'datetime',
        'automation_claim_expires_at' => 'datetime',
        'automation_attempts' => 'integer',
        'price_cents' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (SongRequest $request) {
            if (empty($request->selection_token)) {
                $request->selection_token = Str::random(32);
            }
            if (empty($request->admin_upload_token)) {
                $request->admin_upload_token = Str::random(32);
            }
            if (empty($request->customer_password)) {
                $request->customer_password = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            }
        });
    }

    public function samplesExpired(): bool
    {
        $sample = $this->songSamples()->first();
        if (! $sample || ! $sample->expires_at) {
            return false;
        }
        return $sample->expires_at->isPast();
    }

    public function generateCustomerPassword(): string
    {
        $this->customer_password = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $this->save();
        return $this->customer_password;
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
        if ($this->relationLoaded('songSamples') && $this->songSamples->isNotEmpty()) {
            return true;
        }

        return $this->songSamples()->exists()
            || (! empty($this->samples) && count($this->samples) > 0);
    }

    public function songSamples(): HasMany
    {
        return $this->hasMany(SongSample::class)->orderBy('position');
    }
}
