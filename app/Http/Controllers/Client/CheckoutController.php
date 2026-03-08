<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\Payment;
use App\Models\Setting;
use App\Services\XenditService;
use App\Services\TripayService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function show(Invitation $invitation)
    {
        if ($invitation->user_id !== auth()->id()) {
            abort(403);
        }

        $invitation->load('package');

        // Check if already paid
        $existingPayment = Payment::where('invitation_id', $invitation->id)
            ->where('payment_status', 'paid')
            ->first();

        if ($existingPayment) {
            return redirect()->route('client.invitations.show', $invitation)
                ->with('success', 'Undangan ini sudah dibayar.');
        }

        // Get pending payment if any
        $pendingPayment = Payment::where('invitation_id', $invitation->id)
            ->where('payment_status', 'pending')
            ->where(function ($q) {
                $q->whereNull('expired_at')->orWhere('expired_at', '>', now());
            })
            ->first();

        $gateways = [];
        if (Setting::get('xendit_enabled') === '1') {
            $gateways[] = ['code' => 'xendit', 'name' => 'Xendit', 'icon' => 'fas fa-bolt'];
        }
        if (Setting::get('tripay_enabled') === '1') {
            $gateways[] = ['code' => 'tripay', 'name' => 'Tripay', 'icon' => 'fas fa-credit-card'];
        }

        return view('client.checkout.show', compact('invitation', 'gateways', 'pendingPayment'));
    }

    public function process(Request $request, Invitation $invitation)
    {
        if ($invitation->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'gateway' => 'required|in:xendit,tripay',
        ]);

        $invitation->load('package');
        $user = auth()->user();
        $gateway = $request->input('gateway');
        $amount = (int) $invitation->package->price;
        $callbackToken = Str::random(32);
        $merchantRef = 'INV-' . $invitation->id . '-' . time();

        // Create payment record
        $payment = Payment::create([
            'user_id' => $user->id,
            'invitation_id' => $invitation->id,
            'package_id' => $invitation->package_id,
            'amount' => $amount,
            'payment_gateway' => $gateway,
            'payment_status' => 'pending',
            'callback_token' => $callbackToken,
            'transaction_id' => $merchantRef,
        ]);

        if ($gateway === 'xendit') {
            $service = new XenditService();
            $result = $service->createInvoice([
                'external_id' => $merchantRef,
                'amount' => $amount,
                'description' => 'Paket ' . $invitation->package->name . ' — ' . $invitation->title,
                'email' => $user->email,
                'name' => $user->name,
                'success_url' => route('client.checkout.status', $invitation),
                'failure_url' => route('client.checkout.status', $invitation),
            ]);
        } else {
            $service = new TripayService();
            $result = $service->createTransaction([
                'merchant_ref' => $merchantRef,
                'amount' => $amount,
                'method' => $request->input('channel', 'QRIS'),
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? '',
                'item_name' => 'Paket ' . $invitation->package->name,
                'callback_url' => route('callback.tripay'),
                'return_url' => route('client.checkout.status', $invitation),
            ]);
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

        // Redirect to payment page
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

        return view('client.checkout.status', compact('invitation', 'payment'));
    }
}
