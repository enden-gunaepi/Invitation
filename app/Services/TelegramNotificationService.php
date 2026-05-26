<?php

namespace App\Services;

use App\Models\Invitation;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class TelegramNotificationService
{
    protected TelegramService $telegram;

    public function __construct()
    {
        $this->telegram = new TelegramService();
    }

    /** Apakah notifikasi Telegram aktif dan terkonfigurasi? */
    protected function enabled(): bool
    {
        return \App\Models\Setting::get('telegram_enabled', '0') === '1'
            && $this->telegram->isConfigured();
    }

    protected function send(string $text): void
    {
        if (!$this->enabled()) return;

        try {
            $this->telegram->broadcast($text);
        } catch (\Throwable $e) {
            Log::warning('TelegramNotification failed', ['error' => $e->getMessage()]);
        }
    }

    // ── Template Notifikasi ───────────────────────────────────────────────────

    public function userRegistered(User $user): void
    {
        $role    = $user->role === 'admin' ? '🛡️ Admin' : '👤 Client';
        $ref     = $user->referred_by_user_id ? '✅ Ya' : '—';
        $joined  = $user->created_at->format('d M Y, H:i');

        $this->send(
            "🎉 <b>User Baru Terdaftar!</b>\n"
            . "──────────────────\n"
            . "👤 Nama   : {$this->e($user->name)}\n"
            . "📧 Email  : <code>{$this->e($user->email)}</code>\n"
            . "🏷️ Role   : {$role}\n"
            . "🤝 Referral: {$ref}\n"
            . "🕐 Waktu  : {$joined}"
        );
    }

    public function invitationCreated(Invitation $invitation): void
    {
        $owner   = $invitation->user;
        $title   = $invitation->title ?: '(tanpa judul)';
        $type    = $invitation->event_type ?? '-';
        $date    = $invitation->event_date ? $invitation->event_date->format('d M Y') : '-';
        $slug    = $invitation->slug;
        $package = $invitation->package?->name ?? '-';
        $time    = $invitation->created_at->format('d M Y, H:i');

        $this->send(
            "📨 <b>Undangan Baru Dibuat!</b>\n"
            . "──────────────────\n"
            . "📝 Judul   : {$this->e($title)}\n"
            . "🎊 Tipe    : {$this->e($type)}\n"
            . "📅 Tgl Acara: {$date}\n"
            . "📦 Paket   : {$this->e($package)}\n"
            . "👤 Oleh    : {$this->e($owner?->name ?? '-')}\n"
            . "🔗 Slug    : <code>{$slug}</code>\n"
            . "🕐 Waktu   : {$time}"
        );
    }

    public function paymentPaid(Payment $payment): void
    {
        $user    = $payment->user;
        $gateway = strtoupper($payment->payment_gateway ?? '-');
        $channel = strtoupper($payment->payment_channel ?? '-');
        $amount  = number_format($payment->amount, 0, ',', '.');
        $trx     = $payment->transaction_id ?? '-';
        $time    = now()->format('d M Y, H:i');

        $context = $payment->client_package_subscription_id
            ? '📦 Berlangganan paket'
            : '📨 Pembelian undangan';

        $this->send(
            "✅ <b>Pembayaran Berhasil!</b>\n"
            . "──────────────────\n"
            . "👤 User    : {$this->e($user?->name ?? '-')}\n"
            . "📧 Email   : <code>{$this->e($user?->email ?? '-')}</code>\n"
            . "💰 Nominal : Rp{$amount}\n"
            . "🏦 Gateway : {$gateway} · {$channel}\n"
            . "🧾 Trx ID  : <code>{$trx}</code>\n"
            . "📌 Konteks : {$context}\n"
            . "🕐 Waktu   : {$time}"
        );
    }

    public function paymentExpired(Payment $payment): void
    {
        $user   = $payment->user;
        $amount = number_format($payment->amount, 0, ',', '.');
        $trx    = $payment->transaction_id ?? '-';
        $time   = now()->format('d M Y, H:i');

        $this->send(
            "⏰ <b>Pembayaran Kadaluarsa</b>\n"
            . "──────────────────\n"
            . "👤 User  : {$this->e($user?->name ?? '-')}\n"
            . "💰 Nominal: Rp{$amount}\n"
            . "🧾 Trx ID: <code>{$trx}</code>\n"
            . "🕐 Waktu : {$time}"
        );
    }

    public function paymentFailed(Payment $payment): void
    {
        $user   = $payment->user;
        $amount = number_format($payment->amount, 0, ',', '.');
        $trx    = $payment->transaction_id ?? '-';

        $this->send(
            "❌ <b>Pembayaran Gagal</b>\n"
            . "──────────────────\n"
            . "👤 User  : {$this->e($user?->name ?? '-')}\n"
            . "💰 Nominal: Rp{$amount}\n"
            . "🧾 Trx ID: <code>{$trx}</code>"
        );
    }

    public function adminUserTopup(User $user, int $amount, int $balanceAfter): void
    {
        $this->send(
            "💰 <b>Topup Saldo Admin</b>\n"
            . "──────────────────\n"
            . "👤 User    : {$this->e($user->name)}\n"
            . "📧 Email   : <code>{$this->e($user->email)}</code>\n"
            . "➕ Ditambah: Rp" . number_format($amount, 0, ',', '.') . "\n"
            . "💳 Saldo   : Rp" . number_format($balanceAfter, 0, ',', '.') . "\n"
            . "🕐 Waktu   : " . now()->format('d M Y, H:i')
        );
    }

    public function balanceAdjusted(User $user, float $amount, float $balanceAfter, User $admin, string $note): void
    {
        $action = $amount > 0 ? '➕ Penambahan' : '➖ Pengurangan';
        $amountStr = number_format(abs($amount), 0, ',', '.');
        $balanceStr = number_format($balanceAfter, 0, ',', '.');
        
        $this->send(
            "⚖️ <b>Penyesuaian Saldo Manual (Admin)</b>\n"
            . "──────────────────\n"
            . "👤 User    : {$this->e($user->name)}\n"
            . "📧 Email   : <code>{$this->e($user->email)}</code>\n"
            . "🔧 Tindakan: {$action}\n"
            . "💰 Nominal : Rp{$amountStr}\n"
            . "💳 Saldo Akhir: Rp{$balanceStr}\n"
            . "🛡️ Admin   : {$this->e($admin->name)}\n"
            . "📝 Catatan : <i>{$this->e($note)}</i>\n"
            . "🕐 Waktu   : " . now()->format('d M Y, H:i')
        );
    }

    public function affiliateCommissionCreated(\App\Models\AffiliateCommission $commission): void
    {
        $referrer = $commission->referrer ?? null;
        $referred = $commission->referred  ?? null;
        $amount   = number_format($commission->commission_amount, 0, ',', '.');
        $flag     = $commission->risk_flag ? ' ⚠️ Risk flag: ' . $commission->risk_reason : '';

        $this->send(
            "🤝 <b>Komisi Affiliate</b>\n"
            . "──────────────────\n"
            . "🏅 Referrer : {$this->e($referrer?->name ?? '-')}\n"
            . "👤 Referred : {$this->e($referred?->name ?? '-')}\n"
            . "💰 Komisi   : Rp{$amount}{$flag}"
        );
    }

    // ── Helper ────────────────────────────────────────────────────────────────

    protected function e(string $text): string
    {
        return htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}
