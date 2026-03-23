<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->decimal('affiliate_commission_rate', 5, 2)->nullable()->after('price');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('signup_ip', 45)->nullable()->after('is_active');
            $table->string('signup_ua_hash', 64)->nullable()->after('signup_ip');
            $table->timestamp('referral_clicked_at')->nullable()->after('signup_ua_hash');
        });

        Schema::table('affiliate_commissions', function (Blueprint $table) {
            $table->boolean('risk_flag')->default(false)->after('status');
            $table->string('risk_reason')->nullable()->after('risk_flag');
        });

        Schema::create('affiliate_clicks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('referral_code', 40);
            $table->foreignId('converted_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->string('ua_hash', 64)->nullable();
            $table->string('fingerprint', 100)->nullable();
            $table->string('landing_url')->nullable();
            $table->timestamp('converted_at')->nullable();
            $table->timestamps();

            $table->index(['referrer_user_id', 'created_at']);
            $table->index(['referral_code', 'created_at']);
        });

        Schema::create('affiliate_fraud_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('referred_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete();
            $table->string('fraud_type', 40);
            $table->string('reason');
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('payment_callback_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('gateway', 20);
            $table->string('idempotency_key', 120)->unique();
            $table->foreignId('payment_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event_id')->nullable();
            $table->string('status')->nullable();
            $table->json('payload')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['gateway', 'created_at']);
        });

        Schema::create('billing_reconciliations', function (Blueprint $table) {
            $table->id();
            $table->date('run_date');
            $table->string('status', 20)->default('ok');
            $table->unsignedInteger('issues_count')->default(0);
            $table->json('summary')->nullable();
            $table->timestamps();

            $table->index(['run_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_reconciliations');
        Schema::dropIfExists('payment_callback_receipts');
        Schema::dropIfExists('affiliate_fraud_logs');
        Schema::dropIfExists('affiliate_clicks');

        Schema::table('affiliate_commissions', function (Blueprint $table) {
            $table->dropColumn(['risk_flag', 'risk_reason']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['signup_ip', 'signup_ua_hash', 'referral_clicked_at']);
        });

        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn(['affiliate_commission_rate']);
        });
    }
};
