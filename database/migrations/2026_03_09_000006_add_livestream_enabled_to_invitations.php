<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invitations', function (Blueprint $table) {
            $table->boolean('livestream_enabled')->default(false)->after('google_maps_url');
        });

        DB::table('invitations')
            ->whereNotNull('livestream_url')
            ->where('livestream_url', '!=', '')
            ->update(['livestream_enabled' => true]);
    }

    public function down(): void
    {
        Schema::table('invitations', function (Blueprint $table) {
            $table->dropColumn('livestream_enabled');
        });
    }
};
