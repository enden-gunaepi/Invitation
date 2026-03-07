<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rsvps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitation_id')->constrained()->onDelete('cascade');
            $table->foreignId('guest_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name', 100);
            $table->string('phone', 20)->nullable();
            $table->enum('status', ['attending', 'not_attending', 'maybe'])->default('attending');
            $table->integer('pax')->default(1);
            $table->text('message')->nullable();
            $table->boolean('is_shown')->default(true);
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rsvps');
    }
};
