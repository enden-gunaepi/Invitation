<?php

namespace App\Services;

use App\Models\ClientPackageSubscription;
use App\Models\Package;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ClientPackageService
{
    public function getActiveSubscription(int $userId): ?ClientPackageSubscription
    {
        $subscription = ClientPackageSubscription::query()
            ->with('package')
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->latest('started_at')
            ->latest('id')
            ->first();

        return $subscription;
    }

    public function getActivePackage(int $userId): ?Package
    {
        return $this->getActiveSubscription($userId)?->package;
    }

    public function canCreateInvitation(int $userId): array
    {
        $subscription = $this->getActiveSubscription($userId);
        if (!$subscription || !$subscription->package) {
            return [false, 'Anda belum memiliki paket aktif. Pilih paket terlebih dahulu.'];
        }

        $used = DB::table('invitations')
            ->where('user_id', $userId)
            ->count();
        $max = max(1, (int) ($subscription->package->max_invitations ?? 1));

        if ($used >= $max) {
            return [false, "Kuota undangan paket {$subscription->package->name} sudah penuh ({$max})."];
        }

        return [true, null];
    }

    public function activateFromPayment(Payment $payment): void
    {
        $subscription = $payment->clientPackageSubscription;
        if (!$subscription || !$subscription->package) {
            return;
        }

        DB::transaction(function () use ($subscription, $payment): void {
            ClientPackageSubscription::query()
                ->where('user_id', $subscription->user_id)
                ->where('status', 'active')
                ->where('id', '!=', $subscription->id)
                ->update([
                    'status' => 'expired',
                    'expires_at' => now(),
                    'updated_at' => now(),
                ]);

            $start = now();
            $subscription->update([
                'payment_id' => $payment->id,
                'status' => 'active',
                'started_at' => $start,
                'expires_at' => $this->calculateExpiresAt($subscription->package, $start),
            ]);
        });
    }

    public function calculateExpiresAt(Package $package, \Carbon\CarbonInterface $start): ?\Carbon\CarbonInterface
    {
        $duration = (int) ($package->active_duration_value ?? 0);
        $unit = (string) ($package->active_duration_unit ?? '');
        if ($duration <= 0 || !in_array($unit, ['day', 'month'], true)) {
            return null;
        }

        return $unit === 'month'
            ? $start->copy()->addMonths($duration)
            : $start->copy()->addDays($duration);
    }

    public function createPendingSubscription(User $user, Package $package): ClientPackageSubscription
    {
        return ClientPackageSubscription::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'status' => 'pending',
        ]);
    }
}

