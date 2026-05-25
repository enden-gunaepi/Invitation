<?php

namespace App\Services\Payments;

use App\Models\ClientPackageSubscription;
use App\Models\Coupon;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PaymentOrchestratorService
{
    public function __construct(
        private readonly PaymentGatewayRegistry $gatewayRegistry,
    ) {
    }

    public function availableGateways(): array
    {
        $gateways = [];

        foreach ($this->gatewayRegistry->productionGatewayCodes() as $code) {
            if ($code !== 'xendit') {
                continue;
            }

            $gateway = $this->gatewayRegistry->forCode($code);
            $supportedChannels = $gateway->supportedChannels();
            $hasChannels = count($supportedChannels['qris'] ?? []) + count($supportedChannels['ewallet'] ?? []);

            if ($hasChannels === 0) {
                continue;
            }

            $gateways[] = [
                'code' => $gateway->code(),
                'name' => 'Xendit',
                'icon' => 'fas fa-bolt',
            ];
        }

        return $gateways;
    }

    public function channelMap(): array
    {
        $map = [];

        foreach ($this->availableGateways() as $gatewayInfo) {
            $map[$gatewayInfo['code']] = $this->gatewayRegistry->forCode($gatewayInfo['code'])->supportedChannels();
        }

        return $map;
    }

    public function calculateBilling(int $baseAmount): array
    {
        $discountEnabled = Setting::get('payment_discount_enabled', '0') === '1';
        $discountType = Setting::get('payment_discount_type', 'percent');
        $discountValue = (float) Setting::get('payment_discount_value', 0);

        $discount = 0;
        if ($discountEnabled && $discountValue > 0) {
            $discount = $discountType === 'fixed'
                ? min($baseAmount, (int) round($discountValue))
                : (int) round(($baseAmount * $discountValue) / 100);
        }

        $subAfterDiscount = max(0, $baseAmount - $discount);
        $ppnEnabled = Setting::get('payment_ppn_enabled', '0') === '1';
        $ppnPercent = (float) Setting::get('payment_ppn_percent', 11);
        $tax = $ppnEnabled && $ppnPercent > 0
            ? (int) round(($subAfterDiscount * $ppnPercent) / 100)
            : 0;

        return [
            'base' => $baseAmount,
            'discount' => $discount,
            'tax' => $tax,
            'total' => max(1, $subAfterDiscount + $tax),
            'discount_enabled' => $discountEnabled,
            'discount_type' => $discountType,
            'discount_value' => $discountValue,
            'ppn_enabled' => $ppnEnabled,
            'ppn_percent' => $ppnPercent,
            'subtotal_after_discount' => $subAfterDiscount,
        ];
    }

    public function resolveCoupon(string $couponCode, int $baseAmount, int $userId): array
    {
        if ($couponCode === '') {
            return ['ok' => true, 'discount' => 0, 'message' => null];
        }

        $coupon = Coupon::where('code', $couponCode)->where('is_active', true)->first();
        if (!$coupon) {
            return ['ok' => false, 'discount' => 0, 'message' => 'Kupon tidak ditemukan atau tidak aktif.'];
        }

        $now = now();
        if (($coupon->starts_at && $coupon->starts_at->isFuture()) || ($coupon->ends_at && $coupon->ends_at->isPast())) {
            return ['ok' => false, 'discount' => 0, 'message' => 'Kupon di luar periode berlaku.'];
        }

        if ($baseAmount < (int) $coupon->min_transaction_amount) {
            return ['ok' => false, 'discount' => 0, 'message' => 'Minimum transaksi kupon belum terpenuhi.'];
        }

        if ($coupon->usage_limit && $coupon->redemptions()->count() >= $coupon->usage_limit) {
            return ['ok' => false, 'discount' => 0, 'message' => 'Kupon sudah mencapai batas penggunaan.'];
        }

        if ($coupon->usage_per_user && $coupon->redemptions()->where('user_id', $userId)->count() >= $coupon->usage_per_user) {
            return ['ok' => false, 'discount' => 0, 'message' => 'Kupon ini sudah pernah Anda gunakan.'];
        }

        $discount = $coupon->discount_type === 'fixed'
            ? (int) round((float) $coupon->discount_value)
            : (int) round(($baseAmount * (float) $coupon->discount_value) / 100);

        if ($coupon->max_discount_amount) {
            $discount = min($discount, (int) round((float) $coupon->max_discount_amount));
        }

        return ['ok' => true, 'discount' => max(0, min($discount, $baseAmount)), 'message' => null];
    }

    public function resolveReferrer(string $referralCode, int $currentUserId): ?User
    {
        if ($referralCode === '') {
            return null;
        }

        return User::where('referral_code', $referralCode)
            ->where('id', '!=', $currentUserId)
            ->first();
    }

    public function isDevModeEnabled(): bool
    {
        return Setting::get('payment_dev_mode', '0') === '1' || app()->environment(['local', 'development']);
    }

    public function productionModeEnabled(): bool
    {
        return !$this->isDevModeEnabled();
    }

    public function createCheckoutPayment(User $user, Model $payable, array $validated): array
    {
        $baseAmount = (int) ($payable->package->price ?? 0);
        $billing = $this->calculateBilling($baseAmount);

        $couponCode = strtoupper(trim((string) ($validated['coupon_code'] ?? '')));
        $couponResult = $this->resolveCoupon($couponCode, (int) $billing['subtotal_after_discount'], (int) $user->id);
        if (!$couponResult['ok']) {
            return ['success' => false, 'error' => $couponResult['message']];
        }

        $couponDiscount = (int) $couponResult['discount'];
        $subtotal = max(0, (int) $billing['subtotal_after_discount'] - $couponDiscount);
        $tax = $billing['ppn_enabled'] && $billing['ppn_percent'] > 0
            ? (int) round(($subtotal * (float) $billing['ppn_percent']) / 100)
            : 0;
        $amount = max(1, $subtotal + $tax);

        $referralCodeInput = strtoupper(trim((string) ($validated['referral_code'] ?? '')));
        $lockedReferrer = $user->referredBy;
        $lockedCode = $lockedReferrer?->referral_code;
        $effectiveReferralCode = $lockedCode ?: $referralCodeInput;
        $referrer = $this->resolveReferrer($effectiveReferralCode, (int) $user->id);

        if ($referralCodeInput !== '' && !$referrer && !$lockedReferrer) {
            return ['success' => false, 'error' => 'Kode referral tidak valid.'];
        }

        if ($lockedReferrer && $referralCodeInput !== '' && $lockedCode && $referralCodeInput !== $lockedCode) {
            return ['success' => false, 'error' => 'Akun Anda sudah terhubung ke referral lain dan tidak bisa diganti.'];
        }

        if ($referrer && !$user->referred_by_user_id) {
            $user->update(['referred_by_user_id' => $referrer->id]);
        }

        $gatewayCode = (string) $validated['gateway'];
        $channel = (string) $validated['channel'];
        $paymentType = (string) $validated['payment_type'];
        $gateway = $this->gatewayRegistry->forCode($gatewayCode);
        $expirySeconds = max(1800, (int) Setting::get('payment_expiry_seconds', 86400));

        $orderId = $this->buildOrderId($payable);
        $invoiceNumber = $this->generateInvoiceNumber($payable);
        $payment = Payment::create([
            'user_id' => $user->id,
            'invitation_id' => method_exists($payable, 'getTable') && $payable->getTable() === 'invitations' ? $payable->id : null,
            'client_package_subscription_id' => $payable instanceof ClientPackageSubscription ? $payable->id : null,
            'package_id' => $payable->package_id,
            'amount' => $amount,
            'base_amount' => $billing['base'],
            'discount_amount' => $billing['discount'],
            'tax_amount' => $tax,
            'total_amount' => $amount,
            'invoice_number' => $invoiceNumber,
            'invoice_due_at' => now()->addSeconds($expirySeconds),
            'coupon_code' => $couponCode ?: null,
            'coupon_discount_amount' => $couponDiscount,
            'referral_code' => $effectiveReferralCode ?: null,
            'payment_gateway' => $gatewayCode,
            'payment_method' => $paymentType,
            'payment_channel' => $channel,
            'payment_status' => 'pending',
            'callback_token' => Str::random(32),
            'transaction_id' => $orderId,
        ]);

        if ($this->shouldUseMockPayment($gatewayCode)) {
            $result = [
                'success' => true,
                'data' => [
                    'mock' => true,
                    'gateway' => $gatewayCode,
                    'channel' => $channel,
                    'message' => 'Simulasi pembayaran mode development',
                ],
                'payment_url' => $this->statusRouteFor($payable),
                'gateway_reference' => 'MOCK-' . strtoupper($gatewayCode) . '-' . time(),
                'expired_at' => now()->addSeconds($expirySeconds),
            ];
        } else {
            $result = $gateway->createPaymentIntent([
                'order_id' => $orderId,
                'amount' => $amount,
                'channel' => $channel,
                'description' => $this->descriptionFor($payable),
                'email' => $user->email,
                'name' => $user->name,
                'phone' => $user->phone ?? '',
                'success_url' => $this->statusRouteFor($payable),
                'failure_url' => $this->statusRouteFor($payable),
                'callback_url' => route("callback.{$gatewayCode}"),
                'return_url' => $this->statusRouteFor($payable),
                'item_name' => $this->itemNameFor($payable),
                'expiry_seconds' => $expirySeconds,
            ]);
        }

        if (!$result['success']) {
            $payment->markAsFailed();
            return ['success' => false, 'error' => 'Gagal membuat pembayaran: ' . ($result['error'] ?? 'Unknown error')];
        }

        $payment->update([
            'payment_url' => $result['payment_url'] ?? null,
            'gateway_reference' => $result['gateway_reference'] ?? null,
            'expired_at' => $result['expired_at'] ?? now()->addSeconds($expirySeconds),
            'gateway_response' => $result['data'] ?? null,
        ]);

        return [
            'success' => true,
            'payment' => $payment->fresh(),
            'redirect_url' => $result['payment_url'] ?? null,
            'billing' => array_merge($billing, ['tax' => $tax, 'total' => $amount]),
        ];
    }

    public function shouldUseMockPayment(string $gatewayCode): bool
    {
        if ($this->isDevModeEnabled()) {
            return true;
        }

        return !$this->gatewayRegistry->forCode($gatewayCode)->isConfigured();
    }

    private function buildOrderId(Model $payable): string
    {
        return $payable instanceof ClientPackageSubscription
            ? 'SUB-' . $payable->id . '-' . time()
            : 'INV-' . $payable->id . '-' . time();
    }

    private function generateInvoiceNumber(Model $payable): string
    {
        $date = now()->format('Ymd');
        $seq = str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);

        return $payable instanceof ClientPackageSubscription
            ? "SUB-{$date}-{$payable->id}-{$seq}"
            : "INV-{$date}-{$payable->id}-{$seq}";
    }

    private function statusRouteFor(Model $payable): string
    {
        return $payable instanceof ClientPackageSubscription
            ? route('client.packages.checkout.status', $payable)
            : route('client.checkout.status', $payable);
    }

    private function descriptionFor(Model $payable): string
    {
        return $payable instanceof ClientPackageSubscription
            ? 'Langganan Paket ' . $payable->package->name
            : 'Paket ' . $payable->package->name . ' - ' . $payable->title;
    }

    private function itemNameFor(Model $payable): string
    {
        return $payable instanceof ClientPackageSubscription
            ? 'Langganan Paket ' . $payable->package->name
            : 'Paket ' . $payable->package->name;
    }
}
