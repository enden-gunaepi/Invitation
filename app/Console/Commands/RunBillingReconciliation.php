<?php

namespace App\Console\Commands;

use App\Models\BillingReconciliation;
use App\Models\Payment;
use Illuminate\Console\Command;

class RunBillingReconciliation extends Command
{
    protected $signature = 'billing:reconcile-daily';
    protected $description = 'Run daily billing reconciliation summary for audit and anomaly tracking';

    public function handle(): int
    {
        $today = now()->toDateString();

        $pendingExpired = Payment::query()
            ->where('payment_status', 'pending')
            ->whereNotNull('expired_at')
            ->where('expired_at', '<', now())
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
