<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['invitation_id']);
            $table->unsignedBigInteger('invitation_id')->nullable()->change();
            $table->foreign('invitation_id')->references('id')->on('invitations')->nullOnDelete();

            $table->foreignId('client_package_subscription_id')
                ->nullable()
                ->after('invitation_id')
                ->constrained('client_package_subscriptions')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['client_package_subscription_id']);
            $table->dropColumn('client_package_subscription_id');

            $table->dropForeign(['invitation_id']);
            $table->unsignedBigInteger('invitation_id')->nullable(false)->change();
            $table->foreign('invitation_id')->references('id')->on('invitations')->cascadeOnDelete();
        });
    }
};

