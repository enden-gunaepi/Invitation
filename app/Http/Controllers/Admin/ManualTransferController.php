<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ManualTransferBankAccount;
use App\Models\Payment;
use App\Services\ManualTransferService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ManualTransferController extends Controller
{
    public function __construct(
        private readonly ManualTransferService $manualTransferService,
    ) {
    }

    // ── Daftar Pembayaran Transfer Manual ─────────────────────────────────────

    public function index(Request $request)
    {
        $query = Payment::with('user', 'invitation', 'package')
            ->where('payment_method', Payment::METHOD_TRANSFER_MANUAL)
            ->latest();

        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        } else {
            $query->where('payment_status', Payment::STATUS_PENDING_VERIFICATION);
        }

        $payments = $query->paginate(20)->withQueryString();

        $pendingCount = Payment::where('payment_method', Payment::METHOD_TRANSFER_MANUAL)
            ->where('payment_status', Payment::STATUS_PENDING_VERIFICATION)
            ->count();

        return view('admin.manual-transfer.index', compact('payments', 'pendingCount'));
    }

    public function show(Payment $payment)
    {
        abort_unless($payment->isManualTransfer(), 404);
        $payment->load('user', 'invitation', 'package', 'verifiedBy');
        return view('admin.manual-transfer.show', compact('payment'));
    }

    public function confirm(Payment $payment)
    {
        abort_unless($payment->isPendingVerification(), 422, 'Pembayaran tidak dalam status menunggu verifikasi.');

        $ok = $this->manualTransferService->confirmTransfer($payment, auth()->user());

        if ($ok) {
            return redirect()
                ->route('admin.manual-transfer.index')
                ->with('success', "Pembayaran #{$payment->invoice_number} berhasil dikonfirmasi & undangan diaktifkan.");
        }

        return back()->with('error', 'Gagal mengkonfirmasi pembayaran. Silakan coba lagi.');
    }

    public function reject(Request $request, Payment $payment)
    {
        abort_unless($payment->isPendingVerification(), 422, 'Pembayaran tidak dalam status menunggu verifikasi.');

        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        $ok = $this->manualTransferService->rejectTransfer(
            $payment,
            auth()->user(),
            $request->string('rejection_reason')->toString()
        );

        if ($ok) {
            return redirect()
                ->route('admin.manual-transfer.index')
                ->with('success', "Pembayaran #{$payment->invoice_number} berhasil ditolak.");
        }

        return back()->with('error', 'Gagal menolak pembayaran. Silakan coba lagi.');
    }

    // ── Pengaturan Rekening Bank ──────────────────────────────────────────────

    public function bankAccounts()
    {
        $accounts = ManualTransferBankAccount::orderBy('sort_order')->orderBy('id')->get();
        return view('admin.manual-transfer.bank-accounts', compact('accounts'));
    }

    public function storeBankAccount(Request $request)
    {
        $validated = $request->validate([
            'bank_name'           => ['required', 'string', 'max:100'],
            'account_number'      => ['required', 'string', 'max:50'],
            'account_holder_name' => ['required', 'string', 'max:150'],
            'is_active'           => ['nullable', 'boolean'],
            'sort_order'          => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['is_active']  = $request->boolean('is_active', true);
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);

        ManualTransferBankAccount::create($validated);

        return redirect()
            ->route('admin.manual-transfer.bank-accounts')
            ->with('success', 'Rekening bank berhasil ditambahkan.');
    }

    public function updateBankAccount(Request $request, ManualTransferBankAccount $account)
    {
        $validated = $request->validate([
            'bank_name'           => ['required', 'string', 'max:100'],
            'account_number'      => ['required', 'string', 'max:50'],
            'account_holder_name' => ['required', 'string', 'max:150'],
            'is_active'           => ['nullable', 'boolean'],
            'sort_order'          => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['is_active']  = $request->boolean('is_active', false);
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);

        $account->update($validated);

        return redirect()
            ->route('admin.manual-transfer.bank-accounts')
            ->with('success', 'Rekening bank berhasil diperbarui.');
    }

    public function destroyBankAccount(ManualTransferBankAccount $account)
    {
        $account->delete();
        return redirect()
            ->route('admin.manual-transfer.bank-accounts')
            ->with('success', 'Rekening bank berhasil dihapus.');
    }
}
