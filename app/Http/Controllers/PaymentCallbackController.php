<?php

namespace App\Http\Controllers;

use App\Models\AffiliateCommission;
use App\Models\Coupon;
use App\Models\CouponRedemption;
use App\Models\Payment;
use App\Services\XenditService;
use App\Services\TripayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentCallbackController extends Controller
{
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

        $payment = Payment::where('transaction_id', $externalId)
            ->where('payment_gateway', 'xendit')
            ->first();

        if (!$payment) {
            Log::warning('Xendit callback: payment not found', ['external_id' => $externalId]);
            return response()->json(['message' => 'Payment not found'], 404);
        }

        $payment->update(['gateway_response' => $request->all()]);

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
            $this->activateInvitation($payment);
        } elseif ($status === 'EXPIRED') {
            $payment->markAsFailed();
        }

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

        $payment = Payment::where('transaction_id', $merchantRef)
            ->where('payment_gateway', 'tripay')
            ->first();

        if (!$payment) {
            Log::warning('Tripay callback: payment not found', ['merchant_ref' => $merchantRef]);
            return response()->json(['message' => 'Payment not found'], 404);
        }

        $payment->update(['gateway_response' => $request->all()]);

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
            $this->activateInvitation($payment);
        } elseif (in_array($status, ['EXPIRED', 'FAILED'])) {
            $payment->markAsFailed();
        }

        return response()->json(['message' => 'OK']);
    }

    /**
     * Auto-activate invitation after successful payment
     */
    private function activateInvitation(Payment $payment): void
    {
        $invitation = $payment->invitation;
        if ($invitation && $invitation->status !== 'active') {
            $invitation->update(['status' => 'active']);
            Log::info('Invitation auto-activated', ['invitation_id' => $invitation->id]);
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

        $rate = (float) ($user->affiliate_rate ?? 5);
        $commission = (int) round(((int) $payment->amount * $rate) / 100);
        if ($commission <= 0) {
            return;
        }

        AffiliateCommission::firstOrCreate(
            [
                'referrer_user_id' => $user->referred_by_user_id,
                'referred_user_id' => $user->id,
                'payment_id' => $payment->id,
            ],
            [
                'commission_amount' => $commission,
                'status' => 'pending',
            ]
        );

        $payment->update(['affiliate_commission_amount' => $commission]);
    }
}
