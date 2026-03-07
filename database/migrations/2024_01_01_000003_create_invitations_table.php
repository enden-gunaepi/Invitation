<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('template_id')->constrained()->onDelete('cascade');
            $table->foreignId('package_id')->constrained()->onDelete('cascade');
            $table->string('slug')->unique();
            $table->enum('event_type', ['wedding', 'birthday', 'graduation', 'corporate', 'other'])->default('wedding');
            $table->string('title', 200);
            $table->string('groom_name')->nullable();
            $table->string('bride_name')->nullable();
            $table->string('host_name')->nullable();
            $table->date('event_date');
            $table->time('event_time');
            $table->time('event_end_time')->nullable();
            $table->string('venue_name', 200);
            $table->text('venue_address');
            $table->decimal('venue_lat', 10, 8)->nullable();
            $table->decimal('venue_lng', 11, 8)->nullable();
            $table->string('google_maps_url')->nullable();
            $table->string('cover_photo')->nullable();
            $table->text('opening_text')->nullable();
            $table->text('closing_text')->nullable();
            $table->string('music_url')->nullable();
            $table->enum('status', ['draft', 'pending', 'active', 'expired', 'rejected'])->default('draft');
            $table->boolean('is_password_protected')->default(false);
            $table->string('invitation_password')->nullable();
            $table->date('rsvp_deadline')->nullable();
            $table->json('custom_colors')->nullable();
            $table->json('custom_fonts')->nullable();
            $table->integer('view_count')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitations');
    }
};
