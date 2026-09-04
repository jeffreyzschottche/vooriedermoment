<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('song_requests', function (Blueprint $table) {
            $table->timestamp('production_failure_notified_at')->nullable()->after('automation_last_error');
        });
    }

    public function down(): void
    {
        Schema::table('song_requests', function (Blueprint $table) {
            $table->dropColumn('production_failure_notified_at');
        });
    }
};
