<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE payments MODIFY COLUMN payment_status ENUM('draft','pending','pending_verification','paid','expired','failed','cancelled','refunded') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE payments MODIFY COLUMN payment_status ENUM('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending'");
    }
};
