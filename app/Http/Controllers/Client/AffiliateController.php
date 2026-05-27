<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\AffiliateClick;
use App\Models\AffiliateCommission;
use App\Models\PayoutRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AffiliateController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $referralLink = route('referral.visit', ['referralCode' => $user->referral_code]);

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
                ->whereIn('status', ['approved', 'paid'])
                ->whereNull('payout_request_id')
                ->sum('commission_amount'),
            'clicks' => AffiliateClick::where('referrer_user_id', $user->id)->count(),
            'signups' => AffiliateClick::where('referrer_user_id', $user->id)->whereNotNull('converted_user_id')->count(),
            'paid_referrals' => AffiliateCommission::where('referrer_user_id', $user->id)->distinct('referred_user_id')->count('referred_user_id'),
        ];

        return view('client.affiliate.index', compact('commissions', 'payouts', 'stats', 'referralLink'));
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
            ->whereIn('status', ['approved', 'paid'])
            ->whereNull('payout_request_id')
            ->orderBy('id')
            ->get();

        $availableAmount = (float) $availableCommissions->sum('commission_amount');
        if ($availableAmount < 10000) {
            return back()->with('error', 'Komisi yang tersedia belum memenuhi minimum pencairan.');
        }

        $selectedIds = $availableCommissions->pluck('id')->all();
        $allocated = $availableAmount;

        if ($allocated < 10000 || empty($selectedIds)) {
            return back()->with('error', 'Belum ada komisi yang bisa dicairkan ke saldo.');
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

        return back()->with('success', 'Request berhasil dikirim. Semua komisi available sebesar Rp' . number_format($allocated, 0, ',', '.') . ' diajukan untuk dicairkan ke saldo.');
    }
}
