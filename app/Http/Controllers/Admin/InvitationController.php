<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\Payment;
use Illuminate\Http\Request;

class InvitationController extends Controller
{
    public function index(Request $request)
    {
        $query = Invitation::with('user', 'template', 'package');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%{$request->search}%"));
            });
        }

        $invitations = $query->latest()->paginate(10);

        return view('admin.invitations.index', compact('invitations'));
    }

    public function show(Invitation $invitation)
    {
        $invitation->load('user', 'template', 'package', 'photos', 'events', 'guests', 'rsvps', 'wishes', 'payments');

        return view('admin.invitations.show', compact('invitation'));
    }

    public function approve(Request $request, $id)
    {
        $invitation = Invitation::findOrFail($id);
        $invitation->load('package');

        // Check payment: either direct invitation payment OR user's subscription payment for the same package
        $hasPaid = $invitation->payments()->where('payment_status', 'paid')->exists();
        if (!$hasPaid) {
            $hasPaid = Payment::where('user_id', $invitation->user_id)
                ->where('package_id', $invitation->package_id)
                ->where('payment_status', 'paid')
                ->exists();
        }

        if (!$hasPaid) {
            return redirect()->route('admin.invitations.show', $invitation)
                ->with('error', 'Undangan belum bisa di-approve karena pembayaran belum lunas.');
        }

        $activatedAt = now();

        $invitation->update([
            'status' => 'active',
            'published_at' => $activatedAt,
            'expires_at' => $invitation->calculateExpiresAtFromPackage($activatedAt),
            'admin_notes' => $request->input('admin_notes'),
        ]);

        return redirect()->route('admin.invitations.index')
            ->with('success', 'Undangan berhasil diapprove!');
    }

    public function reject(Request $request, $id)
    {
        $invitation = Invitation::findOrFail($id);

        $request->validate(['admin_notes' => 'required|string']);

        $invitation->update([
            'status' => 'rejected',
            'admin_notes' => $request->admin_notes,
        ]);

        return redirect()->route('admin.invitations.index')
            ->with('success', 'Undangan ditolak.');
    }

    public function destroy(Invitation $invitation)
    {
        $invitation->delete();

        return redirect()->route('admin.invitations.index')
            ->with('success', 'Undangan berhasil dihapus!');
    }
}
