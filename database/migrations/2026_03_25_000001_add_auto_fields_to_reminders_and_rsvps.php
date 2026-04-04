<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reminder_campaigns', function (Blueprint $table) {
            $table->string('source', 20)->default('manual')->after('notes');
            $table->string('scheduled_key', 64)->nullable()->after('source');
            $table->index(['invitation_id', 'source']);
            $table->unique(['invitation_id', 'channel', 'scheduled_key'], 'reminder_campaigns_slot_unique');
        });

        Schema::table('rsvps', function (Blueprint $table) {
            $table->string('normalized_phone', 20)->nullable()->after('phone');
            $table->index(['invitation_id', 'normalized_phone']);
            $table->unique(['invitation_id', 'guest_id'], 'rsvps_invitation_guest_unique');
            $table->unique(['invitation_id', 'normalized_phone'], 'rsvps_invitation_phone_unique');
        });
    }

    public function down(): void
    {
        Schema::table('rsvps', function (Blueprint $table) {
            $table->dropUnique('rsvps_invitation_guest_unique');
            $table->dropUnique('rsvps_invitation_phone_unique');
            $table->dropIndex(['invitation_id', 'normalized_phone']);
            $table->dropColumn('normalized_phone');
        });

        Schema::table('reminder_campaigns', function (Blueprint $table) {
            $table->dropUnique('reminder_campaigns_slot_unique');
            $table->dropIndex(['invitation_id', 'source']);
            $table->dropColumn(['source', 'scheduled_key']);
        });
    }
};
