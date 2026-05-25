<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\Payment;
use App\Services\Payments\PaymentOrchestratorService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly PaymentOrchestratorService $paymentOrchestrator,
    ) {
    }

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

        $billing = $this->paymentOrchestrator->calculateBilling((int) $invitation->package->price);
        $gateways = $this->paymentOrchestrator->availableGateways();
        $channelMap = $this->paymentOrchestrator->channelMap();
        $devMode = $this->paymentOrchestrator->isDevModeEnabled();
        $currentReferrer = auth()->user()->referredBy;
        $lockedReferralCode = $currentReferrer?->referral_code;

        return view('client.checkout.show', compact('invitation', 'gateways', 'pendingPayment', 'billing', 'channelMap', 'devMode', 'currentReferrer', 'lockedReferralCode'));
    }

    public function process(Request $request, Invitation $invitation)
    {
        if ($invitation->user_id !== auth()->id()) {
            abort(403);
        }

        $gateways = $this->paymentOrchestrator->availableGateways();
        $channelMap = $this->paymentOrchestrator->channelMap();
        $gatewayCodes = collect($gateways)->pluck('code')->values()->all();

        $validated = $request->validate([
            'gateway' => ['required', Rule::in($gatewayCodes)],
            'payment_type' => ['required', Rule::in(['qris', 'ewallet'])],
            'channel' => ['required', 'string', 'max:50'],
            'coupon_code' => ['nullable', 'string', 'max:40'],
            'referral_code' => ['nullable', 'string', 'max:40'],
        ]);

        $paymentType = $validated['payment_type'];
        $gateway = $validated['gateway'];
        $channel = $validated['channel'];

        $availableChannels = collect($channelMap[$gateway][$paymentType] ?? [])->pluck('code')->all();
        if (!in_array($channel, $availableChannels, true)) {
            return back()->with('error', 'Channel pembayaran tidak valid untuk gateway/metode yang dipilih.');
        }

        $invitation->load('package');
        $result = $this->paymentOrchestrator->createCheckoutPayment(auth()->user(), $invitation, $validated);

        if (!$result['success']) {
            return back()->withInput()->with('error', $result['error']);
        }

        if (!empty($result['redirect_url'])) {
            return redirect()->away($result['redirect_url']);
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

        $devMode = $this->paymentOrchestrator->isDevModeEnabled();

        return view('client.checkout.status', compact('invitation', 'payment', 'devMode'));
    }

    public function simulatePaid(Invitation $invitation)
    {
        if ($invitation->user_id !== auth()->id()) {
            abort(403);
        }

        if (!$this->paymentOrchestrator->isDevModeEnabled()) {
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

        return redirect()->route('client.checkout.status', $invitation)
            ->with('success', 'Simulasi pembayaran berhasil. Selanjutnya submit undangan untuk direview admin.');
    }

}
