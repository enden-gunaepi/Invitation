<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->unsignedInteger('max_invitations')->default(1)->after('max_photos');
            $table->json('allowed_template_ids')->nullable()->after('features');
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn(['max_invitations', 'allowed_template_ids']);
        });
    }
};

