<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('song_requests', function (Blueprint $table) {
            $table->id();
            $table->string('category')->index();
            $table->string('category_title')->nullable();
            $table->string('email')->nullable();
            $table->json('intake');
            $table->longText('lyrics')->nullable();
            $table->text('lyrics_preview')->nullable();
            $table->string('status')->default('draft'); // draft | paid
            $table->unsignedInteger('price_cents')->default(0);
            $table->string('payment_reference')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('song_requests');
    }
};
