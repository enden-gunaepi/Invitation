<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->string('billing_type', 20)->default('one_time')->after('price');
            $table->string('billing_cycle', 20)->nullable()->after('billing_type'); // monthly/yearly
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('referral_code', 40)->nullable()->unique()->after('email');
            $table->foreignId('referred_by_user_id')->nullable()->after('referral_code')->constrained('users')->nullOnDelete();
            $table->decimal('affiliate_rate', 5, 2)->default(5)->after('referred_by_user_id');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->string('invoice_number', 60)->nullable()->after('transaction_id');
            $table->timestamp('invoice_due_at')->nullable()->after('invoice_number');
            $table->string('coupon_code', 40)->nullable()->after('invoice_due_at');
            $table->decimal('coupon_discount_amount', 12, 2)->default(0)->after('coupon_code');
            $table->string('referral_code', 40)->nullable()->after('coupon_discount_amount');
            $table->decimal('affiliate_commission_amount', 12, 2)->default(0)->after('referral_code');
            $table->unsignedTinyInteger('retry_count')->default(0)->after('affiliate_commission_amount');
            $table->timestamp('last_retry_at')->nullable()->after('retry_count');
            $table->timestamp('next_retry_at')->nullable()->after('last_retry_at');
        });

        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 40)->unique();
            $table->string('name', 100);
            $table->enum('discount_type', ['percent', 'fixed'])->default('percent');
            $table->decimal('discount_value', 12, 2)->default(0);
            $table->decimal('max_discount_amount', 12, 2)->nullable();
            $table->decimal('min_transaction_amount', 12, 2)->default(0);
            $table->unsignedInteger('usage_limit')->nullable();
            $table->unsignedInteger('usage_per_user')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('coupon_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('affiliate_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('referred_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('payment_id')->constrained()->cascadeOnDelete();
            $table->decimal('commission_amount', 12, 2)->default(0);
            $table->enum('status', ['pending', 'approved', 'paid'])->default('pending');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        Schema::create('dunning_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained()->cascadeOnDelete();
            $table->string('channel', 20); // email/whatsapp
            $table->string('status', 20); // sent/failed/skipped
            $table->text('message')->nullable();
            $table->timestamps();
        });

        Schema::table('guests', function (Blueprint $table) {
            $table->unsignedInteger('table_number')->nullable()->after('notes');
            $table->string('seat_label', 20)->nullable()->after('table_number');
            $table->timestamp('checked_in_at')->nullable()->after('seat_label');
            $table->string('checkin_method', 20)->nullable()->after('checked_in_at');
            $table->foreignId('checked_in_by_user_id')->nullable()->after('checkin_method')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('checked_in_by_user_id');
            $table->dropColumn(['table_number', 'seat_label', 'checked_in_at', 'checkin_method']);
        });

        Schema::dropIfExists('dunning_logs');
        Schema::dropIfExists('affiliate_commissions');
        Schema::dropIfExists('coupon_redemptions');
        Schema::dropIfExists('coupons');

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'invoice_number',
                'invoice_due_at',
                'coupon_code',
                'coupon_discount_amount',
                'referral_code',
                'affiliate_commission_amount',
                'retry_count',
                'last_retry_at',
                'next_retry_at',
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('referred_by_user_id');
            $table->dropColumn(['referral_code', 'affiliate_rate']);
        });

        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn(['billing_type', 'billing_cycle']);
        });
    }
};

