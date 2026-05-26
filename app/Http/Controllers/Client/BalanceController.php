<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Setting;
use App\Services\Payments\PaymentOrchestratorService;
use App\Services\BalanceService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BalanceController extends Controller
{
    private const MIN_TOPUP_AMOUNT = 50000;

    public function __construct(
        private readonly PaymentOrchestratorService $paymentOrchestrator,
        private readonly BalanceService $balanceService
    ) {
    }

    public function index()
    {
        $user = auth()->user();
        $transactions = $this->balanceService->getTransactionHistory($user->id, 15);

        return view('client.balance.index', compact('user', 'transactions'));
    }

    public function topupForm()
    {
        $user = auth()->user();
        $gateways = $this->paymentOrchestrator->availableGateways();
        $channelMap = $this->paymentOrchestrator->channelMap();
        $devMode = $this->paymentOrchestrator->isDevModeEnabled();
        $minTopup = max((int) Setting::get('payment_min_topup', self::MIN_TOPUP_AMOUNT), self::MIN_TOPUP_AMOUNT);
        $prefillAmount = max((int) request()->query('amount', 50000), $minTopup);

        return view('client.balance.topup', compact('user', 'gateways', 'channelMap', 'devMode', 'minTopup', 'prefillAmount'));
    }

    public function topupProcess(Request $request)
    {
        $user = auth()->user();
        $gateways = $this->paymentOrchestrator->availableGateways();
        $channelMap = $this->paymentOrchestrator->channelMap();
        $gatewayCodes = collect($gateways)->pluck('code')->values()->all();
        $minTopup = max((int) Setting::get('payment_min_topup', self::MIN_TOPUP_AMOUNT), self::MIN_TOPUP_AMOUNT);

        $validated = $request->validate([
            'amount' => ['required', 'integer', 'min:' . $minTopup],
            'gateway' => ['required', Rule::in($gatewayCodes)],
            'payment_type' => ['required', Rule::in(['qris', 'ewallet'])],
            'channel' => ['required', 'string', 'max:50'],
        ]);

        $paymentType = $validated['payment_type'];
        $gateway = $validated['gateway'];
        $channel = $validated['channel'];

        $availableChannels = collect($channelMap[$gateway][$paymentType] ?? [])->pluck('code')->all();
        if (!in_array($channel, $availableChannels, true)) {
            return back()->with('error', 'Channel pembayaran tidak valid untuk gateway/metode yang dipilih.');
        }

        $result = $this->paymentOrchestrator->createTopUpPayment($user, (int) $validated['amount'], $validated);

        if (!$result['success']) {
            return back()->withInput()->with('error', $result['error']);
        }

        if (!empty($result['redirect_url']) && !($result['data']['mock'] ?? false)) {
            return redirect()->away($result['redirect_url']);
        }

        return redirect()->route('client.balance.topup.status', ['payment_id' => $result['payment']->id]);
    }

    public function topupStatus(Request $request)
    {
        $user = auth()->user();
        $paymentId = $request->query('payment_id');
        
        $payment = Payment::where('user_id', $user->id)
            ->where('payment_purpose', Payment::PURPOSE_TOPUP)
            ->when($paymentId, function ($query, $paymentId) {
                return $query->where('id', $paymentId);
            })
            ->latest()
            ->first();

        if (!$payment) {
            return redirect()->route('client.balance.index')->with('error', 'Transaksi top-up tidak ditemukan.');
        }

        $devMode = $this->paymentOrchestrator->isDevModeEnabled();

        return view('client.balance.topup-status', compact('payment', 'devMode'));
    }

    public function simulatePaid(Request $request, Payment $payment)
    {
        if ($payment->user_id !== auth()->id()) {
            abort(403);
        }

        if (!$this->paymentOrchestrator->isDevModeEnabled()) {
            return redirect()->route('client.balance.topup.status', ['payment_id' => $payment->id])
                ->with('error', 'Mode simulasi pembayaran tidak aktif.');
        }

        if (!$payment->isPending()) {
            return redirect()->route('client.balance.topup.status', ['payment_id' => $payment->id])
                ->with('error', 'Tidak ada transaksi pending untuk disimulasikan.');
        }

        $payment->markAsPaid('MOCK-TOPUP-PAID-' . now()->timestamp);
        $this->balanceService->topUp($payment->user, (float) $payment->amount, $payment);

        return redirect()->route('client.balance.topup.status', ['payment_id' => $payment->id])
            ->with('success', 'Simulasi top-up berhasil. Saldo Anda telah bertambah.');
    }
}
