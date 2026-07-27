<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('song_samples', function (Blueprint $table) {
            $table->string('original_audio_path')->nullable()->change();
        });

        Schema::table('song_requests', function (Blueprint $table) {
            $table->unsignedTinyInteger('chosen_sample_position')->nullable()->after('chosen_sample_id');
            $table->string('chosen_sample_title')->nullable()->after('chosen_sample_position');
            $table->text('chosen_suno_source_url')->nullable()->after('chosen_sample_title');
            $table->timestamp('samples_deleted_at')->nullable()->after('chosen_suno_source_url');
        });
    }

    public function down(): void
    {
        Schema::table('song_requests', function (Blueprint $table) {
            $table->dropColumn([
                'chosen_sample_position',
                'chosen_sample_title',
                'chosen_suno_source_url',
                'samples_deleted_at',
            ]);
        });
    }
};
