<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('song_requests', function (Blueprint $table) {
            $table->string('admin_upload_token', 64)->nullable()->unique()->after('selection_token');
            $table->string('customer_password', 10)->nullable()->after('admin_upload_token');
        });
    }

    public function down(): void
    {
        Schema::table('song_requests', function (Blueprint $table) {
            $table->dropColumn(['admin_upload_token', 'customer_password']);
        });
    }
};
