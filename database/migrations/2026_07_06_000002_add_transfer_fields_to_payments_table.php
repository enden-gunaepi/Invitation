<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('transfer_proof_path')->nullable()->after('gateway_response');
            $table->timestamp('transfer_verified_at')->nullable()->after('transfer_proof_path');
            $table->unsignedBigInteger('transfer_verified_by')->nullable()->after('transfer_verified_at');
            $table->text('transfer_rejection_reason')->nullable()->after('transfer_verified_by');

            $table->foreign('transfer_verified_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['transfer_verified_by']);
            $table->dropColumn([
                'transfer_proof_path',
                'transfer_verified_at',
                'transfer_verified_by',
                'transfer_rejection_reason',
            ]);
        });
    }
};
