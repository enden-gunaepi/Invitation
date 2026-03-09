<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payout_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 12, 2)->default(0);
            $table->enum('status', ['pending', 'approved', 'rejected', 'paid'])->default('pending');
            $table->string('method', 50)->default('bank_transfer');
            $table->string('account_name', 120);
            $table->string('account_number', 80);
            $table->text('notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        Schema::table('affiliate_commissions', function (Blueprint $table) {
            $table->foreignId('payout_request_id')
                ->nullable()
                ->after('payment_id')
                ->constrained('payout_requests')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('affiliate_commissions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('payout_request_id');
        });

        Schema::dropIfExists('payout_requests');
    }
};

