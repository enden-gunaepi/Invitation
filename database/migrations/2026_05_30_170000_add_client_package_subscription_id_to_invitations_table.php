<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invitations', function (Blueprint $table) {
            $table->foreignId('client_package_subscription_id')
                ->nullable()
                ->after('package_id')
                ->constrained('client_package_subscriptions')
                ->nullOnDelete();

            $table->index(['user_id', 'client_package_subscription_id'], 'invitations_user_subscription_idx');
        });
    }

    public function down(): void
    {
        Schema::table('invitations', function (Blueprint $table) {
            $table->dropIndex('invitations_user_subscription_idx');
            $table->dropConstrainedForeignId('client_package_subscription_id');
        });
    }
};
