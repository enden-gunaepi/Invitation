<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ClientPackageSubscription;
use App\Models\Package;
use App\Models\Payment;
use App\Services\ClientPackageService;
use App\Services\Payments\PaymentOrchestratorService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClientPackageController extends Controller
{
    public function __construct(
        private readonly ClientPackageService $clientPackageService,
        private readonly PaymentOrchestratorService $paymentOrchestrator,
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

        $pendingPayment = Payment::where('client_package_subscription_id', $subscription->id)
            ->where('payment_status', 'pending')
            ->where(function ($q) {
                $q->whereNull('expired_at')->orWhere('expired_at', '>', now());
            })
            ->first();

        $billing = $this->paymentOrchestrator->calculateBilling((int) $subscription->package->price);
        $gateways = $this->paymentOrchestrator->availableGateways();
        $channelMap = $this->paymentOrchestrator->channelMap();
        $devMode = $this->paymentOrchestrator->isDevModeEnabled();
        $currentReferrer = auth()->user()->referredBy;
        $lockedReferralCode = $currentReferrer?->referral_code;

        return view('client.packages.checkout', compact(
            'subscription',
            'gateways',
            'channelMap',
            'pendingPayment',
            'billing',
            'devMode',
            'currentReferrer',
            'lockedReferralCode'
        ));
    }

    public function checkoutProcess(Request $request, ClientPackageSubscription $subscription)
    {
        $this->authorizeSubscription($subscription);
        $subscription->load('package');

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

        $availableChannels = collect($channelMap[$validated['gateway']][$validated['payment_type']] ?? [])
            ->pluck('code')
            ->all();

        if (!in_array($validated['channel'], $availableChannels, true)) {
            return back()->withInput()->with('error', 'Channel pembayaran tidak valid untuk gateway/metode yang dipilih.');
        }

        $result = $this->paymentOrchestrator->createCheckoutPayment(auth()->user(), $subscription, $validated);
        if (!$result['success']) {
            return back()->withInput()->with('error', $result['error']);
        }

        if (!empty($result['redirect_url'])) {
            return redirect()->away($result['redirect_url']);
        }

        return redirect()->route('client.packages.checkout.status', $subscription);
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

    public function checkoutSimulatePaid(ClientPackageSubscription $subscription)
    {
        $this->authorizeSubscription($subscription);

        if (!$this->paymentOrchestrator->isDevModeEnabled()) {
            return redirect()->route('client.packages.checkout.status', $subscription)
                ->with('error', 'Mode simulasi pembayaran tidak aktif.');
        }

        $payment = Payment::where('client_package_subscription_id', $subscription->id)
            ->where('payment_status', 'pending')
            ->latest()
            ->first();

        if (!$payment) {
            return redirect()->route('client.packages.checkout.status', $subscription)
                ->with('error', 'Tidak ada transaksi pending untuk disimulasikan.');
        }

        $payment->markAsPaid('MOCK-SUB-PAID-' . now()->timestamp);
        $this->clientPackageService->activateFromPayment($payment->fresh(['clientPackageSubscription.package']));

        return redirect()->route('client.packages.checkout.status', $subscription)
            ->with('success', 'Pembayaran simulasi berhasil. Paket akun Anda sudah aktif.');
    }

    private function authorizeSubscription(ClientPackageSubscription $subscription): void
    {
        if ($subscription->user_id !== auth()->id()) {
            abort(403);
        }
    }
}
