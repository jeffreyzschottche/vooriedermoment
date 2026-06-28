<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('song_requests', function (Blueprint $table) {
            // Pull-export naar de lokale Suno-macro.
            $table->string('export_path')->nullable()->after('released_at');
            $table->timestamp('exported_at')->nullable()->after('export_path');
        });
    }

    public function down(): void
    {
        Schema::table('song_requests', function (Blueprint $table) {
            $table->dropColumn(['export_path', 'exported_at']);
        });
    }
};
