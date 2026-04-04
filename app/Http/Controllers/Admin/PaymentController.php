<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\ClientPackageService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        private readonly ClientPackageService $clientPackageService,
    ) {
    }

    public function index(Request $request)
    {
        $query = Payment::with('user', 'invitation', 'package', 'clientPackageSubscription')->latest();

        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        }
        if ($request->filled('gateway')) {
            $query->where('payment_gateway', $request->gateway);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                  ->orWhere('gateway_reference', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        $payments = $query->paginate(20)->withQueryString();

        $stats = [
            'total' => Payment::count(),
            'paid' => Payment::where('payment_status', 'paid')->count(),
            'pending' => Payment::where('payment_status', 'pending')->count(),
            'failed' => Payment::where('payment_status', 'failed')->count(),
            'revenue' => Payment::where('payment_status', 'paid')->sum('amount'),
        ];

        return view('admin.payments.index', compact('payments', 'stats'));
    }

    public function show(Payment $payment)
    {
        $payment->load('user', 'invitation', 'package', 'clientPackageSubscription');
        return view('admin.payments.show', compact('payment'));
    }

    public function markPaid(Payment $payment)
    {
        $payment->markAsPaid('MANUAL-' . now()->timestamp);
        if ($payment->client_package_subscription_id) {
            $this->clientPackageService->activateFromPayment($payment->fresh(['clientPackageSubscription.package']));
        }
        return back()->with('success', 'Pembayaran ditandai sebagai lunas.');
    }
}
