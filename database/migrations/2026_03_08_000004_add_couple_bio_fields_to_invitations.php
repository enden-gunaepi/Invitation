<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invitations', function (Blueprint $table) {
            $table->string('groom_parent_name')->nullable()->after('groom_name');
            $table->string('bride_parent_name')->nullable()->after('bride_name');
            $table->string('groom_photo')->nullable()->after('cover_photo');
            $table->string('bride_photo')->nullable()->after('groom_photo');
            $table->string('groom_instagram')->nullable()->after('bride_photo');
            $table->string('bride_instagram')->nullable()->after('groom_instagram');
            $table->string('groom_facebook')->nullable()->after('bride_instagram');
            $table->string('bride_facebook')->nullable()->after('groom_facebook');
        });
    }

    public function down(): void
    {
        Schema::table('invitations', function (Blueprint $table) {
            $table->dropColumn([
                'groom_parent_name',
                'bride_parent_name',
                'groom_photo',
                'bride_photo',
                'groom_instagram',
                'bride_instagram',
                'groom_facebook',
                'bride_facebook',
            ]);
        });
    }
};

