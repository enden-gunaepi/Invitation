<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitation_bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitation_id')->constrained()->onDelete('cascade');
            $table->string('bank_name', 100);
            $table->string('account_number', 50);
            $table->string('account_name', 255);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitation_bank_accounts');
    }
};

