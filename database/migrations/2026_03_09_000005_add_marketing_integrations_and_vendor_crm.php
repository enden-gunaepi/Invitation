<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invitations', function (Blueprint $table) {
            $table->string('livestream_url')->nullable()->after('google_maps_url');
            $table->string('livestream_label', 100)->nullable()->after('livestream_url');
        });

        Schema::create('reminder_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('channel', 20)->default('whatsapp');
            $table->string('audience', 30)->default('all_guests');
            $table->text('message_template');
            $table->timestamp('scheduled_at');
            $table->timestamp('processed_at')->nullable();
            $table->string('status', 20)->default('scheduled'); // scheduled/sent/failed/cancelled
            $table->unsignedInteger('sent_count')->default(0);
            $table->unsignedInteger('failed_count')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('reminder_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('reminder_campaigns')->cascadeOnDelete();
            $table->foreignId('guest_id')->nullable()->constrained()->nullOnDelete();
            $table->string('phone', 30)->nullable();
            $table->string('status', 20)->default('pending'); // sent/failed/skipped
            $table->string('provider_message_id')->nullable();
            $table->text('response_message')->nullable();
            $table->timestamps();
        });

        Schema::create('vendor_leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('category', 30)->default('wo'); // wo/photographer/makeup/entertainment/other
            $table->string('vendor_name');
            $table->string('contact_name')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('instagram')->nullable();
            $table->string('status', 20)->default('new'); // new/contacted/negotiation/deal/lost
            $table->decimal('offered_price', 12, 2)->nullable();
            $table->date('follow_up_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('last_contact_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_leads');
        Schema::dropIfExists('reminder_logs');
        Schema::dropIfExists('reminder_campaigns');

        Schema::table('invitations', function (Blueprint $table) {
            $table->dropColumn(['livestream_url', 'livestream_label']);
        });
    }
};
