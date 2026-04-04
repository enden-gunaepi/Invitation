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
        if ($clientPackageService->getActiveSubscription((int) $client->id)) {
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

Schedule::command('payments:dunning')->everyThirtyMinutes();
Schedule::command('reminders:generate-auto')->hourly();
Schedule::command('reminders:process-whatsapp')->everyTenMinutes();
Schedule::command('system:heartbeat')->everyFiveMinutes();
Schedule::command('billing:reconcile-daily')->dailyAt('00:30');
Schedule::command('invitations:purge-expired')->dailyAt('01:00');
