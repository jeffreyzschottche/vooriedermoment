<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('song_requests', function (Blueprint $table) {
            $table->timestamp('payment_confirmation_sent_at')
                ->nullable()
                ->after('payment_fulfillment_queued_at');
        });
    }

    public function down(): void
    {
        Schema::table('song_requests', function (Blueprint $table) {
            $table->dropColumn('payment_confirmation_sent_at');
        });
    }
};
