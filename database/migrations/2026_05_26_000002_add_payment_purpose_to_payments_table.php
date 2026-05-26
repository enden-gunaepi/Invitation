<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->enum('payment_purpose', ['topup', 'invitation', 'subscription'])
                  ->default('topup')
                  ->after('payment_status');

            $table->dropForeign(['package_id']);
            $table->unsignedBigInteger('package_id')->nullable()->change();
            $table->foreign('package_id')->references('id')->on('packages')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('payment_purpose');

            $table->dropForeign(['package_id']);
            $table->unsignedBigInteger('package_id')->nullable(false)->change();
            $table->foreign('package_id')->references('id')->on('packages')->cascadeOnDelete();
        });
    }
};
