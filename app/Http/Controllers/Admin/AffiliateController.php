<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AffiliateCommission;
use App\Models\PayoutRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AffiliateController extends Controller
{
    public function index(Request $request)
    {
        $query = AffiliateCommission::with(['referrer', 'referred', 'payment'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('referrer', fn ($r) => $r->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('referred', fn ($r) => $r->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('payment', fn ($p) => $p->where('invoice_number', 'like', "%{$search}%"));
            });
        }

        $commissions = $query->paginate(20)->withQueryString();

        $stats = [
            'total' => AffiliateCommission::count(),
            'pending' => AffiliateCommission::where('status', 'pending')->count(),
            'approved' => AffiliateCommission::where('status', 'approved')->count(),
            'paid' => AffiliateCommission::where('status', 'paid')->count(),
            'amount_total' => AffiliateCommission::sum('commission_amount'),
            'amount_paid' => AffiliateCommission::where('status', 'paid')->sum('commission_amount'),
        ];

        return view('admin.affiliate.index', compact('commissions', 'stats'));
    }

    public function payouts(Request $request)
    {
        $query = PayoutRequest::with(['user', 'commissions'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"))
                    ->orWhere('account_name', 'like', "%{$search}%")
                    ->orWhere('account_number', 'like', "%{$search}%");
            });
        }

        $payouts = $query->paginate(20)->withQueryString();
        $stats = [
            'total' => PayoutRequest::count(),
            'pending' => PayoutRequest::where('status', 'pending')->count(),
            'approved' => PayoutRequest::where('status', 'approved')->count(),
            'paid' => PayoutRequest::where('status', 'paid')->count(),
            'amount_pending' => (float) PayoutRequest::whereIn('status', ['pending', 'approved'])->sum('amount'),
            'amount_paid' => (float) PayoutRequest::where('status', 'paid')->sum('amount'),
        ];

        return view('admin.affiliate.payouts', compact('payouts', 'stats'));
    }

    public function approve(AffiliateCommission $commission)
    {
        if ($commission->status === 'pending') {
            $commission->update([
                'status' => 'approved',
                'approved_at' => now(),
            ]);
        }

        return back()->with('success', 'Komisi affiliate berhasil di-approve.');
    }

    public function markPaid(AffiliateCommission $commission)
    {
        $payload = [
            'status' => 'paid',
            'paid_at' => now(),
        ];

        if ($commission->status === 'pending') {
            $payload['approved_at'] = now();
        }

        $commission->update($payload);

        return back()->with('success', 'Komisi affiliate berhasil ditandai terbayar.');
    }

    public function approvePayout(Request $request, PayoutRequest $payout)
    {
        if ($payout->status !== 'pending') {
            return back()->with('error', 'Payout ini tidak bisa di-approve lagi.');
        }

        $payload = ['status' => 'approved', 'approved_at' => now()];
        if ($request->filled('admin_notes')) {
            $payload['admin_notes'] = $request->string('admin_notes')->value();
        }

        $payout->update($payload);

        return back()->with('success', 'Payout request berhasil di-approve.');
    }

    public function rejectPayout(Request $request, PayoutRequest $payout)
    {
        if (in_array($payout->status, ['rejected', 'paid'], true)) {
            return back()->with('error', 'Payout ini tidak bisa di-reject.');
        }

        DB::transaction(function () use ($request, $payout): void {
            $payout->update([
                'status' => 'rejected',
                'admin_notes' => $request->string('admin_notes')->value() ?: 'Ditolak admin.',
            ]);

            AffiliateCommission::where('payout_request_id', $payout->id)
                ->where('status', 'approved')
                ->update(['payout_request_id' => null]);
        });

        return back()->with('success', 'Payout request berhasil ditolak.');
    }

    public function markPayoutPaid(Request $request, PayoutRequest $payout)
    {
        if ($payout->status === 'paid') {
            return back()->with('error', 'Payout ini sudah ditandai paid.');
        }

        DB::transaction(function () use ($request, $payout): void {
            $payload = [
                'status' => 'paid',
                'paid_at' => now(),
            ];
            if (is_null($payout->approved_at)) {
                $payload['approved_at'] = now();
            }
            if ($request->filled('admin_notes')) {
                $payload['admin_notes'] = $request->string('admin_notes')->value();
            }
            $payout->update($payload);

            AffiliateCommission::where('payout_request_id', $payout->id)->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            AffiliateCommission::where('payout_request_id', $payout->id)
                ->whereNull('approved_at')
                ->update(['approved_at' => now()]);
        });

        return back()->with('success', 'Payout request ditandai selesai dibayar.');
    }
}
