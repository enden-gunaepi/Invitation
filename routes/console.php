<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Payment;
use App\Models\User;
use App\Services\ClientPackageService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('packages:backfill-subscriptions', function (ClientPackageService $clientPackageService) {
    $clients = User::query()->where('role', 'client')->get();
    $created = 0;

    foreach ($clients as $client) {
        if ($clientPackageService->getUsableSubscriptions((int) $client->id)->isNotEmpty()) {
            continue;
        }

        $latestPaid = Payment::query()
            ->where('user_id', $client->id)
            ->where('payment_status', 'paid')
            ->whereNotNull('package_id')
            ->latest('paid_at')
            ->latest('id')
            ->first();

        if (!$latestPaid || !$latestPaid->package) {
            continue;
        }

        $subscription = $clientPackageService->createPendingSubscription($client, $latestPaid->package);
        $subscription->update([
            'status' => 'active',
            'payment_id' => $latestPaid->id,
            'started_at' => $latestPaid->paid_at ?? now(),
            'expires_at' => $clientPackageService->calculateExpiresAt($latestPaid->package, ($latestPaid->paid_at ?? now())),
        ]);
        $created++;
    }

    $this->info("Backfill selesai. Subscription aktif dibuat: {$created}");
})->purpose('Backfill active client package subscriptions from latest paid payments');

Artisan::command('invitations:backfill-subscription-bindings {user_id?}', function (ClientPackageService $clientPackageService) {
    $userId = $this->argument('user_id');
    $updated = $clientPackageService->backfillInvitationSubscriptionBindings($userId ? (int) $userId : null);

    $this->info("Backfill binding invitation selesai. Invitation terhubung: {$updated}");
})->purpose('Backfill client package subscription bindings for legacy invitations');

$scheduleTimezone = (string) config('system_update.schedule_timezone', 'Asia/Jakarta');

Schedule::command('payments:dunning')
    ->everyThirtyMinutes()->timezone($scheduleTimezone)->withoutOverlapping(25);
Schedule::command('payments:expire-manual')
    ->hourly()->timezone($scheduleTimezone)->withoutOverlapping(55);
Schedule::command('reminders:generate-auto')
    ->hourly()->timezone($scheduleTimezone)->withoutOverlapping(55);
Schedule::command('reminders:process-whatsapp')
    ->everyTenMinutes()->timezone($scheduleTimezone)->withoutOverlapping(30);
Schedule::command('system:heartbeat')
    ->everyFiveMinutes()->timezone($scheduleTimezone)->withoutOverlapping(10);
Schedule::command('system:update')
    ->everyFiveMinutes()->timezone($scheduleTimezone)->withoutOverlapping(10);
Schedule::command('billing:reconcile-daily')
    ->dailyAt('00:30')->timezone($scheduleTimezone)->withoutOverlapping(120);
Schedule::command('invitations:purge-expired')
    ->dailyAt('01:00')->timezone($scheduleTimezone)->withoutOverlapping(120);
Schedule::command('invitations:cleanup-media')
    ->dailyAt('01:30')->timezone($scheduleTimezone)->withoutOverlapping(120);
