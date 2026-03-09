<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->string('tier', 30)->default('basic')->after('slug');
            $table->string('badge_text', 60)->nullable()->after('description');
            $table->string('support_level', 80)->nullable()->after('badge_text');
            $table->unsignedInteger('sla_hours')->nullable()->after('support_level');
            $table->json('addons')->nullable()->after('features');
            $table->boolean('is_recommended')->default(false)->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn([
                'tier',
                'badge_text',
                'support_level',
                'sla_hours',
                'addons',
                'is_recommended',
            ]);
        });
    }
};

