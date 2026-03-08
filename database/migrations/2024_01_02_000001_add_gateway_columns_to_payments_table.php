<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('payment_gateway')->default('manual')->after('payment_method');
            $table->string('payment_channel')->nullable()->after('payment_gateway');
            $table->string('gateway_reference')->nullable()->after('transaction_id');
            $table->string('callback_token')->nullable()->after('gateway_reference');
            $table->timestamp('expired_at')->nullable()->after('paid_at');
            $table->string('payment_url')->nullable()->after('expired_at');
            $table->json('gateway_response')->nullable()->after('payment_url');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'payment_gateway', 'payment_channel', 'gateway_reference',
                'callback_token', 'expired_at', 'payment_url', 'gateway_response',
            ]);
        });
    }
};
