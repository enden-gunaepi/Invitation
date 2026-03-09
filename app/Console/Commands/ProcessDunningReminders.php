<?php

namespace App\Console\Commands;

use App\Models\DunningLog;
use App\Models\Payment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class ProcessDunningReminders extends Command
{
    protected $signature = 'payments:dunning';
    protected $description = 'Send reminder for failed/pending payments and schedule retries';

    public function handle(): int
    {
        $now = now();

        $targets = Payment::with('user', 'invitation')
            ->whereIn('payment_status', ['pending', 'failed'])
            ->where(function ($q) use ($now) {
                $q->whereNull('next_retry_at')
                    ->orWhere('next_retry_at', '<=', $now);
            })
            ->where('retry_count', '<', 3)
            ->limit(100)
            ->get();

        foreach ($targets as $payment) {
            $user = $payment->user;
            if (!$user || !$user->email) {
                DunningLog::create([
                    'payment_id' => $payment->id,
                    'channel' => 'email',
                    'status' => 'skipped',
                    'message' => 'User email not available.',
                ]);
                continue;
            }

            try {
                $subject = "Reminder Pembayaran {$payment->invoice_number}";
                $message = "Halo {$user->name}, tagihan {$payment->invoice_number} untuk undangan {$payment->invitation?->title} masih {$payment->payment_status}. Total: Rp" . number_format((float) $payment->amount, 0, ',', '.') . ". Lanjutkan pembayaran: {$payment->payment_url}";
                Mail::raw($message, function ($mail) use ($user, $subject) {
                    $mail->to($user->email)->subject($subject);
                });

                DunningLog::create([
                    'payment_id' => $payment->id,
                    'channel' => 'email',
                    'status' => 'sent',
                    'message' => 'Reminder email sent.',
                ]);
            } catch (\Throwable $e) {
                DunningLog::create([
                    'payment_id' => $payment->id,
                    'channel' => 'email',
                    'status' => 'failed',
                    'message' => $e->getMessage(),
                ]);
            }

            DunningLog::create([
                'payment_id' => $payment->id,
                'channel' => 'whatsapp',
                'status' => 'skipped',
                'message' => 'WA automation not configured yet.',
            ]);

            $payment->update([
                'retry_count' => (int) $payment->retry_count + 1,
                'last_retry_at' => $now,
                'next_retry_at' => $now->copy()->addHours(12),
            ]);
        }

        $this->info("Dunning processed: {$targets->count()} payment(s).");

        return self::SUCCESS;
    }
}

