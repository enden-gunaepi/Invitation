<?php

namespace App\Console\Commands;

use App\Models\BillingReconciliation;
use App\Models\Payment;
use App\Services\Payments\PaymentStatusSyncService;
use Illuminate\Console\Command;

class RunBillingReconciliation extends Command
{
    protected $signature = 'billing:reconcile-daily';
    protected $description = 'Run daily billing reconciliation summary for audit and anomaly tracking';

    public function handle(PaymentStatusSyncService $paymentStatusSyncService): int
    {
        $today = now()->toDateString();

        Payment::query()
            ->where('payment_status', 'pending')
            ->whereNotNull('expired_at')
            ->where('expired_at', '<', now())
            ->get()
            ->each
            ->markAsExpired();

        $syncResult = $paymentStatusSyncService->syncPendingPayments(
            Payment::query()
                ->where('payment_status', 'pending')
                ->where('payment_gateway', 'xendit')
                ->whereNotNull('gateway_reference')
                ->where(function ($q) {
                    $q->whereNull('expired_at')
                        ->orWhere('expired_at', '>', now()->subMinutes(5));
                })
                ->get()
        );

        $pendingExpired = Payment::query()
            ->where('payment_status', 'expired')
            ->count();

        $paidWithoutPaidAt = Payment::query()
            ->where('payment_status', 'paid')
            ->whereNull('paid_at')
            ->count();

        $paidWithZeroAmount = Payment::query()
            ->where('payment_status', 'paid')
            ->where(function ($q) {
                $q->whereNull('amount')->orWhere('amount', '<=', 0);
            })
            ->count();

        $issues = $pendingExpired + $paidWithoutPaidAt + $paidWithZeroAmount;
        $status = $issues > 0 ? 'warning' : 'ok';

        $summary = [
            'pending_expired_count' => $pendingExpired,
            'paid_without_paid_at_count' => $paidWithoutPaidAt,
            'paid_with_zero_amount_count' => $paidWithZeroAmount,
            'payments_paid_today_count' => Payment::query()
                ->where('payment_status', 'paid')
                ->whereDate('paid_at', $today)
                ->count(),
            'payments_created_today_count' => Payment::query()
                ->whereDate('created_at', $today)
                ->count(),
            'synced_paid_count' => $syncResult['synced_paid'],
            'marked_expired_count' => $syncResult['marked_expired'],
            'sync_errors_count' => $syncResult['errors'],
        ];

        BillingReconciliation::updateOrCreate(
            ['run_date' => $today],
            [
                'status' => $status,
                'issues_count' => $issues,
                'summary' => $summary,
            ]
        );

        $this->info("Billing reconciliation {$today}: status={$status}, issues={$issues}");

        return self::SUCCESS;
    }
}
