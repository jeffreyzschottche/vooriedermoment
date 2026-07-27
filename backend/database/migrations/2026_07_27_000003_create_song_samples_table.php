<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('song_samples', function (Blueprint $table) {
            $table->id();
            $table->foreignId('song_request_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('position');
            $table->string('title');
            $table->string('storage_disk')->default('local');
            $table->string('preview_path');
            $table->string('cover_path');
            $table->string('original_audio_path');
            $table->text('suno_source_url');
            $table->boolean('is_chosen')->default(false);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->unique(['song_request_id', 'position']);
        });

        Schema::table('song_requests', function (Blueprint $table) {
            $table->timestamp('samples_email_sent_at')->nullable()->after('samples_generated_at');
        });
    }

    public function down(): void
    {
        Schema::table('song_requests', function (Blueprint $table) {
            $table->dropColumn('samples_email_sent_at');
        });

        Schema::dropIfExists('song_samples');
    }
};
