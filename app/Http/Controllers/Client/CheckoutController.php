<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Invitation;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\User;
use App\Services\TripayService;
use App\Services\XenditService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CheckoutController extends Controller
{
    public function show(Invitation $invitation)
    {
        if ($invitation->user_id !== auth()->id()) {
            abort(403);
        }

        $invitation->load('package');

        $existingPayment = Payment::where('invitation_id', $invitation->id)
            ->where('payment_status', 'paid')
            ->first();

        if ($existingPayment) {
            return redirect()->route('client.invitations.show', $invitation)
                ->with('success', 'Undangan ini sudah dibayar.');
        }

        $pendingPayment = Payment::where('invitation_id', $invitation->id)
            ->where('payment_status', 'pending')
            ->where(function ($q) {
                $q->whereNull('expired_at')->orWhere('expired_at', '>', now());
            })
            ->first();

        $billing = $this->calculateBilling((int) $invitation->package->price);
        [$gateways, $channelMap] = $this->buildGatewayAndChannels();
        $devMode = $this->isDevModeEnabled();
        $currentReferrer = auth()->user()->referredBy;
        $lockedReferralCode = $currentReferrer?->referral_code;

        return view('client.checkout.show', compact('invitation', 'gateways', 'pendingPayment', 'billing', 'channelMap', 'devMode', 'currentReferrer', 'lockedReferralCode'));
    }

    public function process(Request $request, Invitation $invitation)
    {
        if ($invitation->user_id !== auth()->id()) {
            abort(403);
        }

        [$gateways, $channelMap] = $this->buildGatewayAndChannels();
        $gatewayCodes = collect($gateways)->pluck('code')->values()->all();

        $validated = $request->validate([
            'gateway' => ['required', Rule::in($gatewayCodes)],
            'payment_type' => ['required', Rule::in(['qris', 'ewallet'])],
            'channel' => ['required', 'string', 'max:50'],
            'coupon_code' => ['nullable', 'string', 'max:40'],
            'referral_code' => ['nullable', 'string', 'max:40'],
        ]);

        $gateway = $validated['gateway'];
        $paymentType = $validated['payment_type'];
        $channel = $validated['channel'];

        $availableChannels = collect($channelMap[$gateway][$paymentType] ?? [])->pluck('code')->all();
        if (!in_array($channel, $availableChannels, true)) {
            return back()->with('error', 'Channel pembayaran tidak valid untuk gateway/metode yang dipilih.');
        }

        $invitation->load('package');
        $user = auth()->user();
        $billing = $this->calculateBilling((int) $invitation->package->price);
        $couponCode = strtoupper(trim((string) ($validated['coupon_code'] ?? '')));
        $couponResult = $this->resolveCoupon($couponCode, (int) $billing['subtotal_after_discount'], $user->id);
        if (!$couponResult['ok']) {
            return back()->withInput()->with('error', $couponResult['message']);
        }
        $couponDiscount = (int) $couponResult['discount'];

        $subtotal = max(0, (int) $billing['subtotal_after_discount'] - $couponDiscount);
        $tax = $billing['ppn_enabled'] && $billing['ppn_percent'] > 0
            ? (int) round(($subtotal * (float) $billing['ppn_percent']) / 100)
            : 0;
        $billing['tax'] = $tax;
        $billing['total'] = max(1, $subtotal + $tax);

        $referralCodeInput = strtoupper(trim((string) ($validated['referral_code'] ?? '')));
        $lockedReferrer = $user->referredBy;
        $lockedCode = $lockedReferrer?->referral_code;
        $effectiveReferralCode = $lockedCode ?: $referralCodeInput;
        $referrer = $this->resolveReferrer($effectiveReferralCode, $user->id);

        if ($referralCodeInput !== '' && !$referrer && !$lockedReferrer) {
            return back()->withInput()->with('error', 'Kode referral tidak valid.');
        }

        if ($lockedReferrer && $referralCodeInput !== '' && $lockedCode && $referralCodeInput !== $lockedCode) {
            return back()->withInput()->with('error', 'Akun Anda sudah terhubung ke referral lain dan tidak bisa diganti.');
        }

        if ($referrer && !$user->referred_by_user_id) {
            $user->update(['referred_by_user_id' => $referrer->id]);
        }

        $amount = $billing['total'];
        $callbackToken = Str::random(32);
        $merchantRef = 'INV-' . $invitation->id . '-' . time();
        $invoiceNumber = $this->generateInvoiceNumber($invitation->id);

        $payment = Payment::create([
            'user_id' => $user->id,
            'invitation_id' => $invitation->id,
            'package_id' => $invitation->package_id,
            'amount' => $amount,
            'base_amount' => $billing['base'],
            'discount_amount' => $billing['discount'],
            'tax_amount' => $billing['tax'],
            'total_amount' => $amount,
            'invoice_number' => $invoiceNumber,
            'invoice_due_at' => now()->addHours(24),
            'coupon_code' => $couponCode ?: null,
            'coupon_discount_amount' => $couponDiscount,
            'referral_code' => $effectiveReferralCode ?: null,
            'payment_gateway' => $gateway,
            'payment_method' => $paymentType,
            'payment_channel' => $channel,
            'payment_status' => 'pending',
            'callback_token' => $callbackToken,
            'transaction_id' => $merchantRef,
        ]);

        if ($this->shouldUseMockPayment($gateway)) {
            $result = [
                'success' => true,
                'data' => [
                    'mock' => true,
                    'gateway' => $gateway,
                    'channel' => $channel,
                    'message' => 'Simulasi pembayaran mode development',
                ],
                'payment_url' => route('client.checkout.status', $invitation),
                'reference' => 'MOCK-' . strtoupper($gateway) . '-' . time(),
                'expired_at' => now()->addHours(24),
            ];
        } else {
            if ($gateway === 'xendit') {
                $service = new XenditService();
                $result = $service->createInvoice([
                    'external_id' => $merchantRef,
                    'amount' => $amount,
                    'description' => 'Paket ' . $invitation->package->name . ' - ' . $invitation->title,
                    'email' => $user->email,
                    'name' => $user->name,
                    'success_url' => route('client.checkout.status', $invitation),
                    'failure_url' => route('client.checkout.status', $invitation),
                    'payment_methods' => [$channel],
                ]);
            } else {
                $service = new TripayService();
                $result = $service->createTransaction([
                    'merchant_ref' => $merchantRef,
                    'amount' => $amount,
                    'method' => $channel,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone ?? '',
                    'item_name' => 'Paket ' . $invitation->package->name,
                    'callback_url' => route('callback.tripay'),
                    'return_url' => route('client.checkout.status', $invitation),
                ]);
            }
        }

        if (!$result['success']) {
            $payment->markAsFailed();
            return back()->with('error', 'Gagal membuat pembayaran: ' . ($result['error'] ?? 'Unknown error'));
        }

        $payment->update([
            'payment_url' => $result['payment_url'] ?? null,
            'gateway_reference' => $result['reference'] ?? null,
            'expired_at' => $result['expired_at'] ?? now()->addHours(24),
            'gateway_response' => $result['data'] ?? null,
        ]);

        if (!empty($result['payment_url'])) {
            return redirect()->away($result['payment_url']);
        }

        return redirect()->route('client.checkout.status', $invitation);
    }

    public function status(Invitation $invitation)
    {
        if ($invitation->user_id !== auth()->id()) {
            abort(403);
        }

        $payment = Payment::where('invitation_id', $invitation->id)
            ->latest()
            ->first();

        $devMode = $this->isDevModeEnabled();

        return view('client.checkout.status', compact('invitation', 'payment', 'devMode'));
    }

    public function simulatePaid(Invitation $invitation)
    {
        if ($invitation->user_id !== auth()->id()) {
            abort(403);
        }

        if (!$this->isDevModeEnabled()) {
            return redirect()->route('client.checkout.status', $invitation)
                ->with('error', 'Mode simulasi pembayaran tidak aktif.');
        }

        $payment = Payment::where('invitation_id', $invitation->id)
            ->where('payment_status', 'pending')
            ->latest()
            ->first();

        if (!$payment) {
            return redirect()->route('client.checkout.status', $invitation)
                ->with('error', 'Tidak ada transaksi pending untuk disimulasikan.');
        }

        $payment->markAsPaid('MOCK-PAID-' . now()->timestamp);
        if ($invitation->status !== 'active') {
            $invitation->update(['status' => 'active']);
        }

        return redirect()->route('client.checkout.status', $invitation)
            ->with('success', 'Simulasi pembayaran berhasil. Undangan sudah aktif.');
    }

    private function buildGatewayAndChannels(): array
    {
        $allowQris = Setting::get('payment_allow_qris', '1') === '1';
        $allowEwallet = Setting::get('payment_allow_ewallet', '1') === '1';

        $gateways = [];
        $channelMap = [];

        if (Setting::get('xendit_enabled') === '1') {
            $gateways[] = ['code' => 'xendit', 'name' => 'Xendit', 'icon' => 'fas fa-bolt'];
            $channelMap['xendit'] = [
                'qris' => $allowQris ? [
                    ['code' => 'QRIS', 'name' => 'QRIS'],
                ] : [],
                'ewallet' => $allowEwallet ? [
                    ['code' => 'OVO', 'name' => 'OVO'],
                    ['code' => 'DANA', 'name' => 'DANA'],
                    ['code' => 'SHOPEEPAY', 'name' => 'ShopeePay'],
                    ['code' => 'LINKAJA', 'name' => 'LinkAja'],
                ] : [],
            ];
        }

        if (Setting::get('tripay_enabled') === '1') {
            $gateways[] = ['code' => 'tripay', 'name' => 'Tripay', 'icon' => 'fas fa-credit-card'];
            $tripayChannels = (new TripayService())->getPaymentChannels();
            $channels = collect($tripayChannels['channels'] ?? []);

            $qrisChannels = $allowQris
                ? $channels
                    ->filter(fn ($c) => str_contains(strtoupper((string) ($c['code'] ?? '')), 'QRIS'))
                    ->map(fn ($c) => ['code' => $c['code'], 'name' => $c['name'] ?? $c['code']])
                    ->values()
                    ->all()
                : [];

            $ewalletChannels = $allowEwallet
                ? $channels
                    ->filter(function ($c) {
                        $group = strtoupper((string) ($c['group'] ?? ''));
                        $name = strtoupper((string) ($c['name'] ?? ''));
                        $code = strtoupper((string) ($c['code'] ?? ''));
                        if (str_contains($code, 'QRIS')) {
                            return false;
                        }
                        return str_contains($group, 'EWALLET')
                            || str_contains($group, 'E-WALLET')
                            || str_contains($name, 'WALLET')
                            || in_array($code, ['OVO', 'DANA', 'SHOPEEPAY', 'LINKAJA'], true);
                    })
                    ->map(fn ($c) => ['code' => $c['code'], 'name' => $c['name'] ?? $c['code']])
                    ->values()
                    ->all()
                : [];

            // Fallback agar UI tetap punya opsi saat API channel tripay tidak merespon
            if ($allowQris && empty($qrisChannels)) {
                $qrisChannels = [['code' => 'QRIS', 'name' => 'QRIS']];
            }

            if ($allowEwallet && empty($ewalletChannels)) {
                $ewalletChannels = [
                    ['code' => 'OVO', 'name' => 'OVO'],
                    ['code' => 'DANA', 'name' => 'DANA'],
                    ['code' => 'SHOPEEPAY', 'name' => 'ShopeePay'],
                ];
            }

            $channelMap['tripay'] = [
                'qris' => $qrisChannels,
                'ewallet' => $ewalletChannels,
            ];
        }

        $gateways = collect($gateways)
            ->filter(function ($gw) use ($channelMap) {
                $code = $gw['code'];
                $qris = count($channelMap[$code]['qris'] ?? []);
                $ewallet = count($channelMap[$code]['ewallet'] ?? []);
                return ($qris + $ewallet) > 0;
            })
            ->values()
            ->all();

        return [$gateways, $channelMap];
    }

    private function calculateBilling(int $baseAmount): array
    {
        $discountEnabled = Setting::get('payment_discount_enabled', '0') === '1';
        $discountType = Setting::get('payment_discount_type', 'percent');
        $discountValue = (float) Setting::get('payment_discount_value', 0);

        $discount = 0;
        if ($discountEnabled && $discountValue > 0) {
            if ($discountType === 'fixed') {
                $discount = min($baseAmount, (int) round($discountValue));
            } else {
                $discount = (int) round(($baseAmount * $discountValue) / 100);
            }
        }

        $subAfterDiscount = max(0, $baseAmount - $discount);
        $ppnEnabled = Setting::get('payment_ppn_enabled', '0') === '1';
        $ppnPercent = (float) Setting::get('payment_ppn_percent', 11);
        $tax = $ppnEnabled && $ppnPercent > 0
            ? (int) round(($subAfterDiscount * $ppnPercent) / 100)
            : 0;

        $total = max(1, $subAfterDiscount + $tax);

        return [
            'base' => $baseAmount,
            'discount' => $discount,
            'tax' => $tax,
            'total' => $total,
            'discount_enabled' => $discountEnabled,
            'discount_type' => $discountType,
            'discount_value' => $discountValue,
            'ppn_enabled' => $ppnEnabled,
            'ppn_percent' => $ppnPercent,
            'subtotal_after_discount' => $subAfterDiscount,
        ];
    }

    private function resolveCoupon(string $couponCode, int $baseAmount, int $userId): array
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

        $totalUsage = $coupon->redemptions()->count();
        if ($coupon->usage_limit && $totalUsage >= $coupon->usage_limit) {
            return ['ok' => false, 'discount' => 0, 'message' => 'Kupon sudah mencapai batas penggunaan.'];
        }

        $userUsage = $coupon->redemptions()->where('user_id', $userId)->count();
        if ($coupon->usage_per_user && $userUsage >= $coupon->usage_per_user) {
            return ['ok' => false, 'discount' => 0, 'message' => 'Kupon ini sudah pernah Anda gunakan.'];
        }

        $discount = $coupon->discount_type === 'fixed'
            ? (int) round((float) $coupon->discount_value)
            : (int) round(($baseAmount * (float) $coupon->discount_value) / 100);

        if ($coupon->max_discount_amount) {
            $discount = min($discount, (int) round((float) $coupon->max_discount_amount));
        }
        $discount = max(0, min($discount, $baseAmount));

        return ['ok' => true, 'discount' => $discount, 'message' => null];
    }

    private function resolveReferrer(string $referralCode, int $currentUserId): ?User
    {
        if ($referralCode === '') {
            return null;
        }

        return User::where('referral_code', $referralCode)
            ->where('id', '!=', $currentUserId)
            ->first();
    }

    private function generateInvoiceNumber(int $invitationId): string
    {
        $date = now()->format('Ymd');
        $seq = str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
        return "INV-{$date}-{$invitationId}-{$seq}";
    }

    private function isDevModeEnabled(): bool
    {
        return Setting::get('payment_dev_mode', '0') === '1'
            || app()->environment(['local', 'development']);
    }

    private function shouldUseMockPayment(string $gateway): bool
    {
        if ($this->isDevModeEnabled()) {
            return true;
        }

        if ($gateway === 'xendit') {
            return !(new XenditService())->isConfigured();
        }

        if ($gateway === 'tripay') {
            return !(new TripayService())->isConfigured();
        }

        return false;
    }
}
