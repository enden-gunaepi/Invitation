<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->decimal('base_amount', 12, 2)->nullable()->after('amount');
            $table->decimal('discount_amount', 12, 2)->default(0)->after('base_amount');
            $table->decimal('tax_amount', 12, 2)->default(0)->after('discount_amount');
            $table->decimal('total_amount', 12, 2)->nullable()->after('tax_amount');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['base_amount', 'discount_amount', 'tax_amount', 'total_amount']);
        });
    }
};

