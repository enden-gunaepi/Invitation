<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\AffiliateCommission;
use App\Models\PayoutRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AffiliateController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $commissions = AffiliateCommission::with(['referred', 'payment'])
            ->where('referrer_user_id', $user->id)
            ->latest()
            ->paginate(20);

        $payouts = PayoutRequest::where('user_id', $user->id)
            ->latest()
            ->paginate(10, ['*'], 'payout_page');

        $stats = [
            'pending' => (float) AffiliateCommission::where('referrer_user_id', $user->id)->where('status', 'pending')->sum('commission_amount'),
            'approved' => (float) AffiliateCommission::where('referrer_user_id', $user->id)->where('status', 'approved')->sum('commission_amount'),
            'paid' => (float) AffiliateCommission::where('referrer_user_id', $user->id)->where('status', 'paid')->sum('commission_amount'),
            'available' => (float) AffiliateCommission::where('referrer_user_id', $user->id)
                ->where('status', 'approved')
                ->whereNull('payout_request_id')
                ->sum('commission_amount'),
        ];

        return view('client.affiliate.index', compact('commissions', 'payouts', 'stats'));
    }

    public function requestPayout(Request $request)
    {
        $user = auth()->user();
        $validated = $request->validate([
            'amount' => 'required|numeric|min:10000',
            'method' => 'required|string|max:50',
            'account_name' => 'required|string|max:120',
            'account_number' => 'required|string|max:80',
            'notes' => 'nullable|string|max:500',
        ]);

        $availableCommissions = AffiliateCommission::where('referrer_user_id', $user->id)
            ->where('status', 'approved')
            ->whereNull('payout_request_id')
            ->orderBy('id')
            ->get();

        $requestedAmount = (float) $validated['amount'];
        $availableAmount = (float) $availableCommissions->sum('commission_amount');

        if ($requestedAmount > $availableAmount) {
            return back()->with('error', 'Jumlah payout melebihi saldo komisi yang tersedia.');
        }

        $selectedIds = [];
        $allocated = 0.0;
        foreach ($availableCommissions as $c) {
            $value = (float) $c->commission_amount;
            if (($allocated + $value) <= $requestedAmount) {
                $selectedIds[] = $c->id;
                $allocated += $value;
            }

            if (abs($allocated - $requestedAmount) < 0.01) {
                break;
            }
        }

        if ($allocated < 10000 || empty($selectedIds)) {
            return back()->with('error', 'Nominal tidak bisa diproses. Coba nominal yang lebih kecil atau gunakan saldo tersedia.');
        }

        DB::transaction(function () use ($user, $validated, $allocated, $selectedIds): void {
            $payout = PayoutRequest::create([
                'user_id' => $user->id,
                'amount' => $allocated,
                'status' => 'pending',
                'method' => $validated['method'],
                'account_name' => $validated['account_name'],
                'account_number' => $validated['account_number'],
                'notes' => $validated['notes'] ?? null,
                'requested_at' => now(),
            ]);

            AffiliateCommission::whereIn('id', $selectedIds)->update([
                'payout_request_id' => $payout->id,
            ]);
        });

        return back()->with('success', 'Request payout berhasil dikirim ke admin.');
    }
}
