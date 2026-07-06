<?php

namespace App\Services;

use App\Models\ManualTransferBankAccount;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ManualTransferService
{
    public function getActiveBankAccounts(): Collection
    {
        return ManualTransferBankAccount::active()->get();
    }

    public function isEnabled(): bool
    {
        return \App\Models\Setting::get('payment_method_transfer_manual', '0') === '1';
    }

    /**
     * Simpan file bukti transfer ke storage dan update payment ke pending_verification.
     */
    public function processProofSubmission(Payment $payment, UploadedFile $file): bool
    {
        try {
            $directory = "transfer-proofs/{$payment->id}";
            $filename  = 'proof.' . $file->getClientOriginalExtension();

            Storage::disk('public')->deleteDirectory($directory);
            Storage::disk('public')->putFileAs($directory, $file, $filename);

            $payment->update([
                'transfer_proof_path' => "{$directory}/{$filename}",
                'payment_status'      => Payment::STATUS_PENDING_VERIFICATION,
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::error('ManualTransferService: gagal menyimpan bukti transfer', [
                'payment_id' => $payment->id,
                'error'      => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Admin konfirmasi (approve) pembayaran transfer manual.
     * Akan memicu post-payment hooks: afiliasi, notifikasi Telegram.
     */
    public function confirmTransfer(Payment $payment, User $admin): bool
    {
        try {
            $payment->markAsVerified($admin->id);

            // Jika payment ini adalah top-up saldo, tambahkan saldo user
            if ($payment->payment_purpose === Payment::PURPOSE_TOPUP) {
                $payment->loadMissing('user');
                $balanceService = app(BalanceService::class);
                $balanceService->topUp($payment->user, (float) $payment->amount, $payment);
            }

            // Post-payment: afiliasi commission (sama dengan flow balance)
            $this->processAffiliateCommission($payment);

            // Notifikasi Telegram
            try {
                (new TelegramNotificationService())->manualTransferConfirmed(
                    $payment->fresh(['user']),
                    $admin
                );
            } catch (\Throwable $e) {
                Log::warning('ManualTransfer: telegram confirm notif failed', ['error' => $e->getMessage()]);
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('ManualTransferService: gagal konfirmasi', [
                'payment_id' => $payment->id,
                'error'      => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Admin tolak (reject) pembayaran transfer manual.
     */
    public function rejectTransfer(Payment $payment, User $admin, string $reason): bool
    {
        try {
            $payment->markAsRejected($admin->id, $reason);

            // Notifikasi Telegram
            try {
                (new TelegramNotificationService())->manualTransferRejected(
                    $payment->fresh(['user']),
                    $admin,
                    $reason
                );
            } catch (\Throwable $e) {
                Log::warning('ManualTransfer: telegram reject notif failed', ['error' => $e->getMessage()]);
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('ManualTransferService: gagal menolak', [
                'payment_id' => $payment->id,
                'error'      => $e->getMessage(),
            ]);
            return false;
        }
    }

    /** Proses komisi afiliasi setelah pembayaran dikonfirmasi */
    private function processAffiliateCommission(Payment $payment): void
    {
        $payment->loadMissing('user');
        $user = $payment->user;

        if (!$user || !$user->referred_by_user_id || (float) $payment->amount <= 0) {
            return;
        }

        $referrer = $user->referredBy;
        if (!$referrer) {
            return;
        }

        $packageRate = (float) ($payment->package?->affiliate_commission_rate ?? 0);
        $rate        = $packageRate > 0 ? $packageRate : (float) ($referrer->affiliate_rate ?? 5);
        $commission  = (int) round(((int) $payment->amount * $rate) / 100);

        if ($commission <= 0) {
            return;
        }

        $riskReason = null;
        if ($referrer->id === $user->id) {
            $riskReason = 'self_referral';
        } elseif (!empty($referrer->signup_ip) && !empty($user->signup_ip) && $referrer->signup_ip === $user->signup_ip) {
            $riskReason = 'same_signup_ip';
        }

        \App\Models\AffiliateCommission::firstOrCreate(
            [
                'referrer_user_id' => $referrer->id,
                'referred_user_id' => $user->id,
                'payment_id'       => $payment->id,
            ],
            [
                'commission_amount' => $commission,
                'status'            => 'pending',
                'risk_flag'         => !is_null($riskReason),
                'risk_reason'       => $riskReason,
            ]
        );

        $payment->update(['affiliate_commission_amount' => $commission]);
    }
}
