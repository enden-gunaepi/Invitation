<?php

namespace App\Http\Controllers;

use App\Models\AffiliateCommission;
use App\Models\AffiliateFraudLog;
use App\Models\Coupon;
use App\Models\CouponRedemption;
use App\Models\Payment;
use App\Models\PaymentCallbackReceipt;
use App\Services\ClientPackageService;
use App\Services\XenditService;
use App\Services\TripayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentCallbackController extends Controller
{
    public function __construct(
        private readonly ClientPackageService $clientPackageService,
    ) {
    }

    /**
     * Handle Xendit webhook callback
     */
    public function xenditCallback(Request $request)
    {
        Log::info('Xendit callback received', $request->all());

        $service = new XenditService();
        $callbackToken = $request->header('x-callback-token');

        if (!$service->verifyCallback($callbackToken)) {
            Log::warning('Xendit callback: invalid token');
            return response()->json(['message' => 'Invalid callback token'], 403);
        }

        $externalId = $request->input('external_id');
        $status = $request->input('status');
        $idempotencyKey = $this->buildXenditIdempotencyKey($request);

        $receipt = PaymentCallbackReceipt::firstOrCreate(
            ['idempotency_key' => $idempotencyKey],
            [
                'gateway' => 'xendit',
                'event_id' => (string) $request->input('id', ''),
                'status' => (string) $status,
                'payload' => $request->all(),
            ]
        );

        if ($receipt->processed_at) {
            return response()->json(['message' => 'Duplicate callback ignored']);
        }

        $payment = Payment::where('transaction_id', $externalId)
            ->where('payment_gateway', 'xendit')
            ->first();

        if (!$payment) {
            Log::warning('Xendit callback: payment not found', ['external_id' => $externalId]);
            return response()->json(['message' => 'Payment not found'], 404);
        }

        $payment->update(['gateway_response' => $request->all()]);
        $receipt->update([
            'payment_id' => $payment->id,
            'status' => (string) $status,
            'payload' => $request->all(),
        ]);

        if ($status === 'PAID' || $status === 'SETTLED') {
            if ($payment->isPaid()) {
                return response()->json(['message' => 'Already paid']);
            }
            $paidAmount = (int) ($request->input('paid_amount') ?? $request->input('amount') ?? 0);
            if ($paidAmount > 0 && $paidAmount < (int) $payment->amount) {
                Log::warning('Xendit callback: paid amount mismatch', [
                    'payment_id' => $payment->id,
                    'paid_amount' => $paidAmount,
                    'expected' => (int) $payment->amount,
                ]);
                return response()->json(['message' => 'Amount mismatch'], 422);
            }
            $payment->markAsPaid($request->input('id'));
            $this->finalizePostPaid($payment);
            $this->activateSubscriptionIfNeeded($payment);
            $this->markInvitationAsPaidAwaitingReview($payment);
        } elseif ($status === 'EXPIRED') {
            $payment->markAsFailed();
        }

        $receipt->update(['processed_at' => now()]);

        return response()->json(['message' => 'OK']);
    }

    /**
     * Handle Tripay webhook callback
     */
    public function tripayCallback(Request $request)
    {
        Log::info('Tripay callback received', $request->all());

        $service = new TripayService();
        $rawBody = $request->getContent();

        if (!$service->verifyCallback($rawBody)) {
            Log::warning('Tripay callback: invalid signature');
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $merchantRef = $request->input('merchant_ref');
        $status = $request->input('status');
        $idempotencyKey = $this->buildTripayIdempotencyKey($request);

        $receipt = PaymentCallbackReceipt::firstOrCreate(
            ['idempotency_key' => $idempotencyKey],
            [
                'gateway' => 'tripay',
                'event_id' => (string) $request->input('reference', ''),
                'status' => (string) $status,
                'payload' => $request->all(),
            ]
        );

        if ($receipt->processed_at) {
            return response()->json(['message' => 'Duplicate callback ignored']);
        }

        $payment = Payment::where('transaction_id', $merchantRef)
            ->where('payment_gateway', 'tripay')
            ->first();

        if (!$payment) {
            Log::warning('Tripay callback: payment not found', ['merchant_ref' => $merchantRef]);
            return response()->json(['message' => 'Payment not found'], 404);
        }

        $payment->update(['gateway_response' => $request->all()]);
        $receipt->update([
            'payment_id' => $payment->id,
            'status' => (string) $status,
            'payload' => $request->all(),
        ]);

        if ($status === 'PAID') {
            if ($payment->isPaid()) {
                return response()->json(['message' => 'Already paid']);
            }
            $paidAmount = (int) ($request->input('total_amount') ?? $request->input('amount') ?? 0);
            if ($paidAmount > 0 && $paidAmount < (int) $payment->amount) {
                Log::warning('Tripay callback: paid amount mismatch', [
                    'payment_id' => $payment->id,
                    'paid_amount' => $paidAmount,
                    'expected' => (int) $payment->amount,
                ]);
                return response()->json(['message' => 'Amount mismatch'], 422);
            }
            $payment->markAsPaid($request->input('reference'));
            $this->finalizePostPaid($payment);
            $this->activateSubscriptionIfNeeded($payment);
            $this->markInvitationAsPaidAwaitingReview($payment);
        } elseif (in_array($status, ['EXPIRED', 'FAILED'])) {
            $payment->markAsFailed();
        }

        $receipt->update(['processed_at' => now()]);

        return response()->json(['message' => 'OK']);
    }

    /**
     * Mark invitation as paid, but keep activation under admin approval flow.
     */
    private function markInvitationAsPaidAwaitingReview(Payment $payment): void
    {
        $invitation = $payment->invitation;
        if ($invitation) {
            Log::info('Invitation paid, awaiting admin approval', [
                'invitation_id' => $invitation->id,
                'status' => $invitation->status,
            ]);
        }
    }

    private function activateSubscriptionIfNeeded(Payment $payment): void
    {
        if ($payment->client_package_subscription_id) {
            $this->clientPackageService->activateFromPayment($payment->fresh(['clientPackageSubscription.package']));
        }
    }

    private function finalizePostPaid(Payment $payment): void
    {
        if ($payment->coupon_code && (float) $payment->coupon_discount_amount > 0) {
            $coupon = Coupon::where('code', $payment->coupon_code)->first();
            if ($coupon) {
                CouponRedemption::firstOrCreate(
                    [
                        'coupon_id' => $coupon->id,
                        'user_id' => $payment->user_id,
                        'payment_id' => $payment->id,
                    ],
                    [
                        'discount_amount' => $payment->coupon_discount_amount,
                    ]
                );
            }
        }

        $user = $payment->user;
        if (!$user || !$user->referred_by_user_id || (float) $payment->affiliate_commission_amount > 0) {
            return;
        }

        $referrer = $user->referredBy;
        if (!$referrer) {
            return;
        }

        $packageRate = (float) ($payment->package?->affiliate_commission_rate ?? 0);
        $rate = $packageRate > 0 ? $packageRate : (float) ($referrer->affiliate_rate ?? 5);
        $commission = (int) round(((int) $payment->amount * $rate) / 100);
        if ($commission <= 0) {
            return;
        }

        $riskReason = null;
        if ($referrer->id === $user->id) {
            $riskReason = 'self_referral';
        } elseif (!empty($referrer->signup_ip) && !empty($user->signup_ip) && $referrer->signup_ip === $user->signup_ip) {
            $riskReason = 'same_signup_ip';
        } elseif (!empty($referrer->signup_ua_hash) && !empty($user->signup_ua_hash) && $referrer->signup_ua_hash === $user->signup_ua_hash) {
            $riskReason = 'same_signup_device_hash';
        }

        $affiliateCommission = AffiliateCommission::firstOrCreate(
            [
                'referrer_user_id' => $referrer->id,
                'referred_user_id' => $user->id,
                'payment_id' => $payment->id,
            ],
            [
                'commission_amount' => $commission,
                'status' => 'pending',
                'risk_flag' => !is_null($riskReason),
                'risk_reason' => $riskReason,
            ]
        );

        if ($riskReason) {
            AffiliateFraudLog::create([
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
                    'commission_id' => $affiliateCommission->id,
                ],
            ]);
        }

        $payment->update(['affiliate_commission_amount' => $commission]);
    }

    private function buildXenditIdempotencyKey(Request $request): string
    {
        $externalId = (string) $request->input('external_id', '');
        $eventId = (string) $request->input('id', '');
        $status = (string) $request->input('status', '');

        return 'xendit:' . sha1($externalId . '|' . $eventId . '|' . $status);
    }

    private function buildTripayIdempotencyKey(Request $request): string
    {
        $merchantRef = (string) $request->input('merchant_ref', '');
        $reference = (string) $request->input('reference', '');
        $status = (string) $request->input('status', '');

        return 'tripay:' . sha1($merchantRef . '|' . $reference . '|' . $status);
    }
}
