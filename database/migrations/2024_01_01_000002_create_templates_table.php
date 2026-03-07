<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug')->unique();
            $table->enum('category', ['wedding', 'birthday', 'graduation', 'corporate', 'other'])->default('wedding');
            $table->string('thumbnail')->nullable();
            $table->string('preview_url')->nullable();
            $table->string('html_path')->nullable();
            $table->json('color_schemes')->nullable();
            $table->boolean('is_premium')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('templates');
    }
};
