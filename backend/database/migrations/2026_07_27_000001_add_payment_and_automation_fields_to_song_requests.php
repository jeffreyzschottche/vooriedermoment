<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('song_requests', function (Blueprint $table) {
            $table->string('payment_provider')->nullable()->after('payment_reference');
            $table->string('payment_intent_reference')->nullable()->after('payment_provider');
            $table->timestamp('paid_at')->nullable()->after('payment_intent_reference');
            $table->timestamp('payment_fulfillment_queued_at')->nullable()->after('paid_at');
            $table->timestamp('order_notification_sent_at')->nullable()->after('payment_fulfillment_queued_at');

            $table->string('automation_status')->nullable()->index()->after('exported_at');
            $table->string('automation_claimed_by')->nullable()->after('automation_status');
            $table->string('automation_claim_token_hash', 64)->nullable()->after('automation_claimed_by');
            $table->timestamp('automation_claimed_at')->nullable()->after('automation_claim_token_hash');
            $table->timestamp('automation_claim_expires_at')->nullable()->index()->after('automation_claimed_at');
            $table->unsignedInteger('automation_attempts')->default(0)->after('automation_claim_expires_at');
            $table->text('automation_last_error')->nullable()->after('automation_attempts');
        });
    }

    public function down(): void
    {
        Schema::table('song_requests', function (Blueprint $table) {
            $table->dropIndex(['automation_status']);
            $table->dropIndex(['automation_claim_expires_at']);
            $table->dropColumn([
                'payment_provider',
                'payment_intent_reference',
                'paid_at',
                'payment_fulfillment_queued_at',
                'order_notification_sent_at',
                'automation_status',
                'automation_claimed_by',
                'automation_claim_token_hash',
                'automation_claimed_at',
                'automation_claim_expires_at',
                'automation_attempts',
                'automation_last_error',
            ]);
        });
    }
};
