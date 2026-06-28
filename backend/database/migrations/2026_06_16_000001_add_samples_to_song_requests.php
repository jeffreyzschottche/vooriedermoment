<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('song_requests', function (Blueprint $table) {
            // Sample generation tracking
            $table->json('samples')->nullable()->after('music_reference');
            $table->timestamp('samples_generated_at')->nullable()->after('samples');
            $table->string('chosen_sample_id')->nullable()->after('samples_generated_at');
            $table->string('selection_token', 64)->nullable()->unique()->after('chosen_sample_id');

            // Final song
            $table->string('final_song_url')->nullable()->after('selection_token');
            $table->integer('final_song_duration')->nullable()->after('final_song_url');

            // Spotify distribution tracking
            $table->string('spotify_track_id')->nullable()->after('final_song_duration');
            $table->string('spotify_uri')->nullable()->after('spotify_track_id');
            $table->timestamp('released_at')->nullable()->after('spotify_uri');
        });
    }

    public function down(): void
    {
        Schema::table('song_requests', function (Blueprint $table) {
            $table->dropColumn([
                'samples',
                'samples_generated_at',
                'chosen_sample_id',
                'selection_token',
                'final_song_url',
                'final_song_duration',
                'spotify_track_id',
                'spotify_uri',
                'released_at',
            ]);
        });
    }
};
