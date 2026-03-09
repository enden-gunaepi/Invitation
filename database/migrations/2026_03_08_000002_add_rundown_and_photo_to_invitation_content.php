<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invitation_events', function (Blueprint $table) {
            $table->text('event_description')->nullable()->after('event_name');
        });

        Schema::table('love_stories', function (Blueprint $table) {
            $table->string('photo_path')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('invitation_events', function (Blueprint $table) {
            $table->dropColumn('event_description');
        });

        Schema::table('love_stories', function (Blueprint $table) {
            $table->dropColumn('photo_path');
        });
    }
};

