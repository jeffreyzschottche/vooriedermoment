<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('song_requests', function (Blueprint $table) {
            $table->longText('final_lyrics')->nullable()->after('lyrics_preview');
            $table->longText('music_prompt')->nullable()->after('final_lyrics');
            $table->string('music_reference')->nullable()->after('music_prompt');
            $table->json('production_steps')->nullable()->after('music_reference');
            $table->timestamp('production_started_at')->nullable()->after('production_steps');
            $table->timestamp('production_finished_at')->nullable()->after('production_started_at');
        });
    }

    public function down(): void
    {
        Schema::table('song_requests', function (Blueprint $table) {
            $table->dropColumn([
                'final_lyrics',
                'music_prompt',
                'music_reference',
                'production_steps',
                'production_started_at',
                'production_finished_at',
            ]);
        });
    }
};

