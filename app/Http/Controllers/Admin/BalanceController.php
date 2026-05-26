<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\BalanceTransaction;
use App\Services\BalanceService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BalanceController extends Controller
{
    public function __construct(
        private readonly BalanceService $balanceService
    ) {
    }

    /**
     * GET /admin/balance
     * Daftar semua user client + saldo + stats
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $sortBy = $request->query('sort_by', 'balance');
        $sortOrder = $request->query('sort_order', 'desc');

        $users = User::query()
            ->where('role', 'client')
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy($sortBy, $sortOrder)
            ->paginate(15);

        // Stats Cards
        $stats = [
            'total_users_balance' => User::where('role', 'client')->sum('balance'),
            'topup_this_month' => BalanceTransaction::where('type', BalanceTransaction::TYPE_TOPUP)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount'),
            'purchase_this_month' => abs(BalanceTransaction::where('type', BalanceTransaction::TYPE_PURCHASE)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount')),
            'adjustment_this_month' => BalanceTransaction::where('type', BalanceTransaction::TYPE_ADJUSTMENT)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount'),
        ];

        return view('admin.balance.index', compact('users', 'stats', 'search', 'sortBy', 'sortOrder'));
    }

    /**
     * GET /admin/balance/{user}
     * Detail saldo + riwayat mutasi user tertentu
     */
    public function show(User $user)
    {
        if ($user->role !== 'client') {
            abort(404);
        }

        $transactions = $this->balanceService->getTransactionHistory($user->id, 15);

        return view('admin.balance.show', compact('user', 'transactions'));
    }

    /**
     * POST /admin/balance/{user}/adjust
     * Penyesuaian saldo manual (tambah / kurangi) oleh admin
     */
    public function adjust(Request $request, User $user)
    {
        if ($user->role !== 'client') {
            abort(404);
        }

        $validated = $request->validate([
            'action_type' => ['required', Rule::in(['add', 'subtract'])],
            'amount' => ['required', 'numeric', 'min:1'],
            'admin_note' => ['required', 'string', 'max:255', 'min:5'],
        ]);

        $amount = (float) $validated['amount'];
        
        // If subtract, make the amount negative
        if ($validated['action_type'] === 'subtract') {
            if ($user->balance < $amount) {
                return back()->with('error', 'Gagal memotong saldo: Saldo user tidak mencukupi.');
            }
            $amount = -$amount;
        }

        try {
            $this->balanceService->adminAdjustment($user, $amount, auth()->user(), $validated['admin_note']);
            
            // Log action in Laravel standard log
            \Log::info("Admin adjustment on user {$user->id} by admin " . auth()->id() . ": amount: {$amount}, note: {$validated['admin_note']}");

            return back()->with('success', 'Saldo berhasil disesuaikan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyesuaikan saldo: ' . $e->getMessage());
        }
    }

    /**
     * GET /admin/balance/transactions
     * Log mutasi saldo global (semua user)
     */
    public function transactions(Request $request)
    {
        $type = $request->query('type');
        $search = $request->query('search');

        $transactions = BalanceTransaction::query()
            ->with(['user', 'performedBy'])
            ->when($type, function ($query, $type) {
                return $query->where('type', $type);
            })
            ->when($search, function ($query, $search) {
                return $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(20);

        return view('admin.balance.transactions', compact('transactions', 'type', 'search'));
    }
}
