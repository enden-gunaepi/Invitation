<?php

namespace App\Services;

use App\Models\BalanceTransaction;
use App\Models\ClientPackageSubscription;
use App\Models\Invitation;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class BalanceService
{
    private function lockUser(User $user): User
    {
        return User::query()
            ->whereKey($user->getKey())
            ->lockForUpdate()
            ->firstOrFail();
    }

    /**
     * Top-up saldo dari payment gateway callback
     */
    public function topUp(User $user, float $amount, Payment $payment): BalanceTransaction
    {
        return DB::transaction(function () use ($user, $amount, $payment) {
            $lockedUser = $this->lockUser($user);
            $balanceBefore = (float) $lockedUser->balance;
            $lockedUser->addBalance($amount);

            return BalanceTransaction::create([
                'user_id'        => $lockedUser->id,
                'type'           => BalanceTransaction::TYPE_TOPUP,
                'amount'         => $amount,
                'balance_before' => $balanceBefore,
                'balance_after'  => $balanceBefore + $amount,
                'description'    => "Top-up saldo via " . strtoupper($payment->payment_gateway),
                'reference_type' => 'payment',
                'reference_id'   => $payment->id,
            ]);
        });
    }

    /**
     * Potong saldo untuk pembelian undangan
     */
    public function purchaseInvitation(User $user, float $amount, Invitation $invitation): BalanceTransaction
    {
        return DB::transaction(function () use ($user, $amount, $invitation) {
            $lockedUser = $this->lockUser($user);
            $balanceBefore = (float) $lockedUser->balance;
            $lockedUser->deductBalance($amount);

            return BalanceTransaction::create([
                'user_id'        => $lockedUser->id,
                'type'           => BalanceTransaction::TYPE_PURCHASE,
                'amount'         => -$amount,
                'balance_before' => $balanceBefore,
                'balance_after'  => $balanceBefore - $amount,
                'description'    => "Pembelian undangan: {$invitation->title}",
                'reference_type' => 'invitation',
                'reference_id'   => $invitation->id,
            ]);
        });
    }

    /**
     * Potong saldo untuk pembelian paket subscription
     */
    public function purchaseSubscription(User $user, float $amount, ClientPackageSubscription $sub): BalanceTransaction
    {
        return DB::transaction(function () use ($user, $amount, $sub) {
            $lockedUser = $this->lockUser($user);
            $balanceBefore = (float) $lockedUser->balance;
            $lockedUser->deductBalance($amount);

            return BalanceTransaction::create([
                'user_id'        => $lockedUser->id,
                'type'           => BalanceTransaction::TYPE_PURCHASE,
                'amount'         => -$amount,
                'balance_before' => $balanceBefore,
                'balance_after'  => $balanceBefore - $amount,
                'description'    => "Langganan paket: {$sub->package->name}",
                'reference_type' => 'subscription',
                'reference_id'   => $sub->id,
            ]);
        });
    }

    /**
     * Adjustment saldo oleh admin (bisa tambah atau kurangi)
     */
    public function adminAdjustment(
        User $user,
        float $amount,       // Positif = tambah, Negatif = kurangi
        User $admin,
        string $note = ''
    ): BalanceTransaction {
        $transaction = DB::transaction(function () use ($user, $amount, $admin, $note) {
            $lockedUser = $this->lockUser($user);
            $balanceBefore = (float) $lockedUser->balance;

            if ($amount > 0) {
                $lockedUser->addBalance($amount);
            } else {
                $lockedUser->deductBalance(abs($amount));
            }

            return BalanceTransaction::create([
                'user_id'        => $lockedUser->id,
                'type'           => BalanceTransaction::TYPE_ADJUSTMENT,
                'amount'         => $amount,
                'balance_before' => $balanceBefore,
                'balance_after'  => $balanceBefore + $amount,
                'description'    => $amount > 0 ? 'Penambahan saldo oleh admin' : 'Pengurangan saldo oleh admin',
                'reference_type' => 'manual',
                'reference_id'   => null,
                'performed_by'   => $admin->id,
                'admin_note'     => $note,
            ]);
        });

        $user->refresh();

        try {
            (new \App\Services\TelegramNotificationService())->balanceAdjusted($user, $amount, (float) $user->balance, $admin, $note);
        } catch (\Throwable $e) {
            \Log::warning('Telegram balance adjusted notification failed', ['error' => $e->getMessage()]);
        }

        return $transaction;
    }

    /**
     * Refund saldo
     */
    public function refund(User $user, float $amount, string $reason, ?int $referenceId = null): BalanceTransaction
    {
        return DB::transaction(function () use ($user, $amount, $reason, $referenceId) {
            $lockedUser = $this->lockUser($user);
            $balanceBefore = (float) $lockedUser->balance;
            $lockedUser->addBalance($amount);

            return BalanceTransaction::create([
                'user_id'        => $lockedUser->id,
                'type'           => BalanceTransaction::TYPE_REFUND,
                'amount'         => $amount,
                'balance_before' => $balanceBefore,
                'balance_after'  => $balanceBefore + $amount,
                'description'    => "Refund: {$reason}",
                'reference_type' => 'refund',
                'reference_id'   => $referenceId,
            ]);
        });
    }

    /**
     * Ambil riwayat transaksi saldo user
     */
    public function getTransactionHistory(int $userId, int $perPage = 20)
    {
        return BalanceTransaction::where('user_id', $userId)
            ->latest()
            ->paginate($perPage);
    }
}
