<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->unsignedInteger('active_duration_value')
                ->nullable()
                ->after('max_invitations');
            $table->string('active_duration_unit', 10)
                ->nullable()
                ->after('active_duration_value');
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn(['active_duration_value', 'active_duration_unit']);
        });
    }
};

