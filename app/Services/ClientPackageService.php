<?php

namespace App\Services;

use App\Models\ClientPackageSubscription;
use App\Models\Invitation;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Template;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ClientPackageService
{
    public function getActiveSubscriptions(int $userId): Collection
    {
        return ClientPackageSubscription::query()
            ->with('package')
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->latest('started_at')
            ->latest('id')
            ->get();
    }

    public function getUsableSubscriptions(int $userId): Collection
    {
        return $this->getActiveSubscriptions($userId)
            ->filter(fn (ClientPackageSubscription $subscription) => $subscription->package !== null)
            ->values();
    }

    public function getActiveSubscription(int $userId): ?ClientPackageSubscription
    {
        return $this->getActiveSubscriptions($userId)->first();
    }

    public function getDefaultUsableSubscription(int $userId): ?ClientPackageSubscription
    {
        return $this->getUsableSubscriptions($userId)->first();
    }

    public function getActivePackage(int $userId): ?Package
    {
        return $this->getActiveSubscription($userId)?->package;
    }

    public function canCreateInvitation(int $userId): array
    {
        $subscriptions = $this->getUsableSubscriptions($userId);
        if ($subscriptions->isEmpty()) {
            return [false, 'Anda belum memiliki paket aktif. Pilih paket terlebih dahulu.'];
        }

        foreach ($subscriptions as $subscription) {
            $usage = $this->getSubscriptionUsage($subscription);
            if ($usage['remaining'] > 0) {
                return [true, null];
            }
        }

        $packageNames = $subscriptions
            ->map(fn (ClientPackageSubscription $subscription) => $subscription->package?->name)
            ->filter()
            ->unique()
            ->implode(', ');

        $suffix = $packageNames !== '' ? " pada paket {$packageNames}" : '';

        return [false, "Kuota undangan Anda sudah penuh{$suffix}."];
    }

    public function findAuthorizedUsableSubscription(int $userId, int $subscriptionId): ?ClientPackageSubscription
    {
        return $this->getUsableSubscriptions($userId)
            ->first(fn (ClientPackageSubscription $subscription) => (int) $subscription->id === $subscriptionId);
    }

    public function getSubscriptionUsage(ClientPackageSubscription $subscription): array
    {
        $package = $subscription->package;
        $max = max(1, (int) ($package?->max_invitations ?? 1));

        $used = Invitation::query()
            ->where('client_package_subscription_id', $subscription->id)
            ->count();

        return [
            'used' => $used,
            'max' => $max,
            'remaining' => max(0, $max - $used),
        ];
    }

    public function getSubscriptionUsageSummary(int $subscriptionId): array
    {
        $subscription = ClientPackageSubscription::query()
            ->with('package')
            ->findOrFail($subscriptionId);

        return $this->getSubscriptionUsage($subscription);
    }

    public function canUseSubscriptionForTemplate(int $userId, int $subscriptionId, int $templateId): array
    {
        $subscription = $this->findAuthorizedUsableSubscription($userId, $subscriptionId);
        if (!$subscription || !$subscription->package) {
            return [false, 'Paket yang dipilih tidak aktif atau tidak ditemukan.', null];
        }

        $template = Template::query()->find($templateId);
        if (!$template) {
            return [false, 'Template yang dipilih tidak ditemukan.', $subscription];
        }

        $package = $subscription->package;

        if ($template->is_premium && !$this->packageCanAccessPremium($package)) {
            return [false, "Template \"{$template->name}\" hanya tersedia untuk paket premium yang sesuai.", $subscription];
        }

        if (!$package->allowsTemplate((int) $template->id)) {
            return [false, "Template \"{$template->name}\" tidak diizinkan untuk paket {$package->name}.", $subscription];
        }

        return [true, null, $subscription];
    }

    public function canCreateInvitationWithSubscription(int $userId, int $subscriptionId, int $templateId): array
    {
        [$canUse, $message, $subscription] = $this->canUseSubscriptionForTemplate($userId, $subscriptionId, $templateId);
        if (!$canUse || !$subscription) {
            return [false, $message, null];
        }

        $usage = $this->getSubscriptionUsage($subscription);
        if ($usage['remaining'] <= 0) {
            return [false, "Kuota undangan paket {$subscription->package->name} sudah penuh ({$usage['max']}).", $subscription];
        }

        return [true, null, $subscription];
    }

    public function activateFromPayment(Payment $payment): void
    {
        $subscription = $payment->clientPackageSubscription;
        if (!$subscription || !$subscription->package) {
            return;
        }

        DB::transaction(function () use ($subscription, $payment): void {
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

    public function backfillInvitationSubscriptionBindings(?int $userId = null): int
    {
        $query = Invitation::query()
            ->whereNull('client_package_subscription_id')
            ->whereNotNull('package_id');

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $updated = 0;

        $query->with('package')->orderBy('id')->chunkById(100, function ($invitations) use (&$updated) {
            foreach ($invitations as $invitation) {
                $subscription = ClientPackageSubscription::query()
                    ->with('package')
                    ->where('user_id', $invitation->user_id)
                    ->where('package_id', $invitation->package_id)
                    ->where('status', 'active')
                    ->where(function ($q) {
                        $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                    })
                    ->orderBy('started_at')
                    ->orderBy('id')
                    ->first();

                if (!$subscription) {
                    continue;
                }

                $usage = $this->getSubscriptionUsage($subscription);
                if ($usage['remaining'] <= 0) {
                    continue;
                }

                $invitation->update([
                    'client_package_subscription_id' => $subscription->id,
                ]);
                $updated++;
            }
        });

        return $updated;
    }

    private function packageCanAccessPremium(Package $package): bool
    {
        $features = $package->features ?? [];

        foreach ($features as $feature) {
            if (str_contains(strtolower((string) $feature), 'semua template')) {
                return true;
            }
        }

        return false;
    }
}
