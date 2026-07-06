<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Setting;
use App\Services\Payments\PaymentOrchestratorService;
use App\Services\BalanceService;
use App\Services\ManualTransferService;
use App\Services\TelegramNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BalanceController extends Controller
{
    private const MIN_TOPUP_AMOUNT = 50000;

    public function __construct(
        private readonly PaymentOrchestratorService $paymentOrchestrator,
        private readonly BalanceService $balanceService,
        private readonly ManualTransferService $manualTransferService
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
        
        $manualTransferActive = Setting::get('payment_method_transfer_manual', '0') === '1';

        return view('client.balance.topup', compact('user', 'gateways', 'channelMap', 'devMode', 'minTopup', 'prefillAmount', 'manualTransferActive'));
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

    // ── Transfer Manual ───────────────────────────────────────────────────────

    public function processManualTransfer(Request $request)
    {
        $user = auth()->user();
        $minTopup = max((int) Setting::get('payment_min_topup', self::MIN_TOPUP_AMOUNT), self::MIN_TOPUP_AMOUNT);

        $validated = $request->validate([
            'amount' => ['required', 'integer', 'min:' . $minTopup],
        ]);

        $amount = (int) $validated['amount'];

        // Cek apakah ada pending manual transfer untuk topup
        $existing = Payment::where('user_id', $user->id)
            ->where('payment_purpose', Payment::PURPOSE_TOPUP)
            ->whereIn('payment_status', [
                Payment::STATUS_PENDING,
                Payment::STATUS_PENDING_VERIFICATION,
            ])
            ->first();

        if ($existing) {
            return redirect()->route('client.balance.topup.manual-transfer.instructions', ['payment_id' => $existing->id])
                ->with('info', 'Anda memiliki tagihan top-up yang belum diselesaikan.');
        }

        $date          = now()->format('Ymd');
        $seq           = str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
        $invoiceNumber = "TOPUP-MANUAL-{$date}-{$seq}";
        $orderId       = 'TU-MT-' . $user->id . '-' . time();

        $payment = Payment::create([
            'user_id'              => $user->id,
            'amount'               => $amount,
            'base_amount'          => $amount,
            'discount_amount'      => 0,
            'tax_amount'           => 0,
            'total_amount'         => $amount,
            'invoice_number'       => $invoiceNumber,
            'invoice_due_at'       => now()->addHours(24),
            'payment_gateway'      => 'manual',
            'payment_method'       => Payment::METHOD_TRANSFER_MANUAL,
            'payment_channel'      => 'bank_transfer',
            'payment_purpose'      => Payment::PURPOSE_TOPUP,
            'payment_status'       => Payment::STATUS_PENDING_VERIFICATION,
            'transaction_id'       => $orderId,
            'callback_token'       => Str::random(32),
        ]);

        return redirect()->route('client.balance.topup.manual-transfer.instructions', ['payment_id' => $payment->id]);
    }

    public function manualTransferInstructions(Request $request)
    {
        $user = auth()->user();
        $paymentId = $request->query('payment_id');

        if (!$paymentId) {
            return redirect()->route('client.balance.topup')->with('error', 'ID Pembayaran tidak ditemukan.');
        }

        $payment = Payment::where('id', $paymentId)
            ->where('user_id', $user->id)
            ->where('payment_purpose', Payment::PURPOSE_TOPUP)
            ->where('payment_method', Payment::METHOD_TRANSFER_MANUAL)
            ->whereIn('payment_status', [Payment::STATUS_PENDING_VERIFICATION])
            ->firstOrFail();

        $bankAccounts = $this->manualTransferService->getActiveBankAccounts();

        return view('client.balance.manual-transfer-instructions', compact('payment', 'bankAccounts'));
    }

    public function submitTransferProof(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'payment_id' => ['required', 'exists:payments,id'],
            'transfer_proof' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $payment = Payment::where('id', $validated['payment_id'])
            ->where('user_id', $user->id)
            ->where('payment_purpose', Payment::PURPOSE_TOPUP)
            ->where('payment_method', Payment::METHOD_TRANSFER_MANUAL)
            ->where('payment_status', Payment::STATUS_PENDING_VERIFICATION)
            ->firstOrFail();

        $ok = $this->manualTransferService->processProofSubmission($payment, $request->file('transfer_proof'));

        if (!$ok) {
            return back()->with('error', 'Gagal mengunggah bukti transfer. Silakan coba lagi.');
        }

        try {
            (new TelegramNotificationService())->manualTransferProofSubmitted($payment->fresh(['user']));
        } catch (\Throwable $e) {
            \Log::warning('submitTransferProof (Topup): telegram notif failed', ['error' => $e->getMessage()]);
        }

        return redirect()->route('client.balance.topup.status', ['payment_id' => $payment->id])
            ->with('success', 'Bukti transfer berhasil dikirim! Kami akan segera melakukan konfirmasi.');
    }
}

