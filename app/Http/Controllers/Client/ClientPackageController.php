<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ClientPackageSubscription;
use App\Models\Package;
use App\Models\Payment;
use App\Services\ClientPackageService;
use App\Services\Payments\PaymentOrchestratorService;
use App\Services\BalanceService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClientPackageController extends Controller
{
    public function __construct(
        private readonly ClientPackageService $clientPackageService,
        private readonly PaymentOrchestratorService $paymentOrchestrator,
        private readonly BalanceService $balanceService,
    ) {
    }

    public function select()
    {
        $user = auth()->user();
        $packages = Package::query()->where('is_active', true)->orderBy('price')->get();
        $activeSubscription = $this->clientPackageService->getActiveSubscription((int) $user->id);

        return view('client.packages.select', compact('packages', 'activeSubscription'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'package_id' => 'required|exists:packages,id',
        ]);

        $user = auth()->user();
        $package = Package::query()->where('is_active', true)->findOrFail($validated['package_id']);
        $subscription = $this->clientPackageService->createPendingSubscription($user, $package);

        return redirect()
            ->route('client.packages.checkout.show', $subscription)
            ->with('success', 'Paket dipilih. Lanjutkan pembayaran untuk mengaktifkan paket.');
    }

    public function checkoutShow(ClientPackageSubscription $subscription)
    {
        $this->authorizeSubscription($subscription);
        $subscription->load('package');

        $activeSubscription = $this->clientPackageService->getActiveSubscription((int) auth()->id());
        if ($activeSubscription && $activeSubscription->id === $subscription->id) {
            return redirect()->route('client.invitations.index')
                ->with('success', 'Paket ini sudah aktif.');
        }

        $existingPayment = Payment::where('client_package_subscription_id', $subscription->id)
            ->where('payment_status', 'paid')
            ->first();

        if ($existingPayment) {
            return redirect()->route('client.invitations.index')
                ->with('success', 'Paket sudah dibayar.');
        }

        $billing = $this->paymentOrchestrator->calculateBilling((int) $subscription->package->price);
        $user = auth()->user();
        $currentReferrer = $user->referredBy;
        $lockedReferralCode = $currentReferrer?->referral_code;

        return view('client.packages.checkout', compact(
            'subscription',
            'billing',
            'user',
            'currentReferrer',
            'lockedReferralCode'
        ));
    }

    public function checkoutProcess(Request $request, ClientPackageSubscription $subscription)
    {
        $this->authorizeSubscription($subscription);
        $subscription->load('package');

        $validated = $request->validate([
            'coupon_code' => ['nullable', 'string', 'max:40'],
            'referral_code' => ['nullable', 'string', 'max:40'],
        ]);

        $user = auth()->user();

        // Calculate billing with coupon
        $baseAmount = (int) ($subscription->package->price ?? 0);
        $billing = $this->paymentOrchestrator->calculateBilling($baseAmount);

        $couponCode = strtoupper(trim((string) ($validated['coupon_code'] ?? '')));
        $couponResult = $this->paymentOrchestrator->resolveCoupon($couponCode, (int) $billing['subtotal_after_discount'], (int) $user->id);
        if (!$couponResult['ok']) {
            return back()->with('error', $couponResult['message']);
        }

        $couponDiscount = (int) $couponResult['discount'];
        $subtotal = max(0, (int) $billing['subtotal_after_discount'] - $couponDiscount);
        $tax = $billing['ppn_enabled'] && $billing['ppn_percent'] > 0
            ? (int) round(($subtotal * (float) $billing['ppn_percent']) / 100)
            : 0;
        $amount = max(0, $subtotal + $tax);

        // Verify balance is sufficient
        if (!$user->hasSufficientBalance($amount)) {
            return back()->with('error', 'Saldo Anda tidak mencukupi untuk melakukan pembayaran ini. Silakan top up saldo terlebih dahulu.');
        }

        // Process payment in database transaction
        $payment = \DB::transaction(function () use ($user, $amount, $subscription, $billing, $tax, $couponCode, $couponDiscount, $validated) {
            // 1. Deduct balance using BalanceService
            if ($amount > 0) {
                $this->balanceService->purchaseSubscription($user, $amount, $subscription);
            }

            // Resolve referral
            $referralCodeInput = strtoupper(trim((string) ($validated['referral_code'] ?? '')));
            $lockedReferrer = $user->referredBy;
            $lockedCode = $lockedReferrer?->referral_code;
            $effectiveReferralCode = $lockedCode ?: $referralCodeInput;
            $referrer = $this->paymentOrchestrator->resolveReferrer($effectiveReferralCode, (int) $user->id);

            if ($referrer && !$user->referred_by_user_id) {
                $user->update(['referred_by_user_id' => $referrer->id]);
            }

            $timestamp = time();
            $orderId = 'SUB-BAL-' . $subscription->id . '-' . $timestamp;
            $date = now()->format('Ymd');
            $seq = str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
            $invoiceNumber = "SUB-{$date}-{$subscription->id}-{$seq}";

            // 2. Create payment record
            $payment = Payment::create([
                'user_id' => $user->id,
                'invitation_id' => null,
                'client_package_subscription_id' => $subscription->id,
                'package_id' => $subscription->package_id,
                'amount' => $amount,
                'base_amount' => $billing['base'],
                'discount_amount' => $billing['discount'],
                'tax_amount' => $tax,
                'total_amount' => $amount,
                'invoice_number' => $invoiceNumber,
                'invoice_due_at' => now(),
                'coupon_code' => $couponCode ?: null,
                'coupon_discount_amount' => $couponDiscount,
                'referral_code' => $effectiveReferralCode ?: null,
                'payment_gateway' => 'balance',
                'payment_method' => 'balance',
                'payment_channel' => 'balance',
                'payment_purpose' => Payment::PURPOSE_SUBSCRIPTION,
                'payment_status' => Payment::STATUS_PAID,
                'paid_at' => now(),
                'transaction_id' => $orderId,
            ]);

            // 3. Activate Subscription using ClientPackageService
            $this->clientPackageService->activateFromPayment($payment->fresh(['clientPackageSubscription.package']));

            // 4. Finalize Post Paid hooks manually
            if ($payment->coupon_code && (float) $payment->coupon_discount_amount > 0) {
                $coupon = \App\Models\Coupon::where('code', $payment->coupon_code)->first();
                if ($coupon) {
                    \App\Models\CouponRedemption::firstOrCreate([
                        'coupon_id' => $coupon->id,
                        'user_id' => $payment->user_id,
                        'payment_id' => $payment->id,
                    ], [
                        'discount_amount' => $payment->coupon_discount_amount,
                    ]);
                }
            }

            if ($user->referred_by_user_id && $amount > 0) {
                $referrer = $user->referredBy;
                if ($referrer) {
                    $packageRate = (float) ($payment->package?->affiliate_commission_rate ?? 0);
                    $rate = $packageRate > 0 ? $packageRate : (float) ($referrer->affiliate_rate ?? 5);
                    $commission = (int) round(((int) $payment->amount * $rate) / 100);
                    if ($commission > 0) {
                        $riskReason = null;
                        if ($referrer->id === $user->id) {
                            $riskReason = 'self_referral';
                        } elseif (!empty($referrer->signup_ip) && !empty($user->signup_ip) && $referrer->signup_ip === $user->signup_ip) {
                             $riskReason = 'same_signup_ip';
                        } elseif (!empty($referrer->signup_ua_hash) && !empty($user->signup_ua_hash) && $referrer->signup_ua_hash === $user->signup_ua_hash) {
                             $riskReason = 'same_signup_device_hash';
                        }

                        $affCommission = \App\Models\AffiliateCommission::firstOrCreate([
                            'referrer_user_id' => $referrer->id,
                            'referred_user_id' => $user->id,
                            'payment_id' => $payment->id,
                        ], [
                            'commission_amount' => $commission,
                            'status' => 'pending',
                            'risk_flag' => !is_null($riskReason),
                            'risk_reason' => $riskReason,
                        ]);

                        if ($riskReason) {
                            \App\Models\AffiliateFraudLog::create([
                                'referrer_user_id' => $referrer->id,
                                'referred_user_id' => $user->id,
                                'payment_id' => $payment->id,
                                'fraud_type' => 'affiliate_risk_flag',
                                'reason' => $riskReason,
                                'meta' => [
                                    'referrer_signup_ip' => $referrer->signup_ip,
                                    'referred_signup_ip' => $user->signup_ip,
                                    'referrer_ua_hash' => $referrer->signup_ua_hash,
                                    'referred_ua_hash' => $user->signup_ua_hash,
                                    'commission_id' => $affCommission->id,
                                ],
                            ]);
                        }

                        $payment->update(['affiliate_commission_amount' => $commission]);
                    }
                }
            }

            try {
                (new \App\Services\TelegramNotificationService())->paymentPaid($payment->load('user'));
            } catch (\Exception $e) {
                \Log::error('Telegram notification error: ' . $e->getMessage());
            }

            return $payment;
        });

        return redirect()->route('client.packages.checkout.status', $subscription)
            ->with('success', 'Pembayaran paket menggunakan saldo berhasil!');
    }

    public function checkoutStatus(ClientPackageSubscription $subscription)
    {
        $this->authorizeSubscription($subscription);

        $payment = Payment::where('client_package_subscription_id', $subscription->id)
            ->latest()
            ->first();
        $devMode = $this->paymentOrchestrator->isDevModeEnabled();

        return view('client.packages.checkout-status', compact('subscription', 'payment', 'devMode'));
    }

    private function authorizeSubscription(ClientPackageSubscription $subscription): void
    {
        if ($subscription->user_id !== auth()->id()) {
            abort(403);
        }
    }
}

