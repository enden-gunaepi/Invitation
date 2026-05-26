<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('balance_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['topup', 'purchase', 'refund', 'adjustment']);
            $table->decimal('amount', 15, 2);            // Jumlah mutasi (positif = masuk, negatif = keluar)
            $table->decimal('balance_before', 15, 2);     // Saldo sebelum transaksi
            $table->decimal('balance_after', 15, 2);      // Saldo setelah transaksi
            $table->string('description')->nullable();     // Deskripsi transaksi
            $table->string('reference_type')->nullable();  // 'payment', 'invitation', 'subscription', 'manual'
            $table->unsignedBigInteger('reference_id')->nullable(); // ID referensi terkait
            $table->foreignId('performed_by')->nullable()->constrained('users'); // Admin yang melakukan (untuk adjustment)
            $table->text('admin_note')->nullable();        // Catatan admin (untuk adjustment)
            $table->timestamps();

            $table->index(['user_id', 'type']);
            $table->index(['user_id', 'created_at']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('balance_transactions');
    }
};
