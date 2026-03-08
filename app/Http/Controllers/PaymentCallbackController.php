<?php

namespace App\Http\Controllers;

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
            $payment->markAsPaid($request->input('id'));
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
            $payment->markAsPaid($request->input('reference'));
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
}
