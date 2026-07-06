<?php

namespace App\Console\Commands;

use App\Models\Payment;
use Illuminate\Console\Command;

class ExpireManualTransferPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:expire-manual';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire manual transfer payments that have exceeded 24 hours without proof upload';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Finding expired manual transfer payments...');

        $expiredPayments = Payment::where('payment_method', Payment::METHOD_TRANSFER_MANUAL)
            ->where('payment_status', Payment::STATUS_PENDING_VERIFICATION)
            ->whereNull('transfer_proof_path')
            ->where('invoice_due_at', '<', now())
            ->get();

        $count = 0;
        foreach ($expiredPayments as $payment) {
            $payment->markAsExpired();
            $count++;
        }

        $this->info("Successfully expired {$count} manual transfer payments.");
    }
}

