<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Wedding Planner Profile
        Schema::create('wp_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invitation_id')->nullable()->constrained()->nullOnDelete();
            $table->string('partner_1_name')->nullable();
            $table->string('partner_2_name')->nullable();
            $table->date('wedding_date')->nullable();
            $table->string('city', 100)->nullable();
            $table->unsignedInteger('target_guests')->default(100);
            $table->string('concept', 30)->default('simple'); // simple, mewah, intimate, outdoor
            $table->decimal('total_budget', 15, 2)->default(0);
            $table->boolean('onboarding_completed')->default(false);
            $table->timestamps();

            $table->index('user_id');
        });

        // Smart Checklist
        Schema::create('wp_checklist_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wp_profile_id')->constrained('wp_profiles')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category', 50)->default('general'); // venue, catering, dekor, busana, foto, undangan, lainnya
            $table->date('deadline')->nullable();
            $table->string('status', 20)->default('todo'); // todo, in_progress, done
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_auto_generated')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['wp_profile_id', 'status']);
            $table->index(['wp_profile_id', 'deadline']);
        });

        // Budget Categories
        Schema::create('wp_budget_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wp_profile_id')->constrained('wp_profiles')->cascadeOnDelete();
            $table->string('name', 100);
            $table->decimal('estimated_amount', 15, 2)->default(0);
            $table->decimal('actual_amount', 15, 2)->default(0);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->string('icon', 30)->default('fa-tag');
            $table->string('color', 20)->default('#6366f1');
            $table->timestamps();

            $table->index('wp_profile_id');
        });

        // Budget Items
        Schema::create('wp_budget_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wp_budget_category_id')->constrained('wp_budget_categories')->cascadeOnDelete();
            $table->string('name');
            $table->string('vendor_name')->nullable();
            $table->decimal('estimated_amount', 15, 2)->default(0);
            $table->decimal('actual_amount', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->date('paid_at')->nullable();
            $table->timestamps();

            $table->index('wp_budget_category_id');
        });

        // Vendor Management
        Schema::create('wp_vendors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wp_profile_id')->constrained('wp_profiles')->cascadeOnDelete();
            $table->string('category', 50); // venue, catering, dekor, foto, video, mc, entertainment, makeup, busana, souvenir, lainnya
            $table->string('name');
            $table->string('contact_person')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('instagram', 100)->nullable();
            $table->string('website')->nullable();
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('dp_amount', 15, 2)->default(0);
            $table->date('dp_paid_at')->nullable();
            $table->decimal('remaining_amount', 15, 2)->default(0);
            $table->date('remaining_paid_at')->nullable();
            $table->string('status', 20)->default('prospek'); // prospek, deal, dp_paid, lunas, cancelled
            $table->text('notes')->nullable();
            $table->string('contract_file')->nullable();
            $table->date('payment_deadline')->nullable();
            $table->timestamps();

            $table->index(['wp_profile_id', 'category']);
            $table->index(['wp_profile_id', 'status']);
        });

        // Timeline Events
        Schema::create('wp_timeline_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wp_profile_id')->constrained('wp_profiles')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('target_date')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->string('category', 50)->default('general');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('wp_profile_id');
        });

        // AI Advisor Logs
        Schema::create('wp_advisor_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wp_profile_id')->constrained('wp_profiles')->cascadeOnDelete();
            $table->text('question');
            $table->text('answer');
            $table->string('category', 50)->default('general');
            $table->timestamps();

            $table->index('wp_profile_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wp_advisor_logs');
        Schema::dropIfExists('wp_timeline_events');
        Schema::dropIfExists('wp_vendors');
        Schema::dropIfExists('wp_budget_items');
        Schema::dropIfExists('wp_budget_categories');
        Schema::dropIfExists('wp_checklist_items');
        Schema::dropIfExists('wp_profiles');
    }
};
