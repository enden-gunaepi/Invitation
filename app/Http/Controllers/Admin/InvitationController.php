<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
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
        $invitation->load('user', 'template', 'package', 'photos', 'events', 'guests', 'rsvps', 'wishes');

        return view('admin.invitations.show', compact('invitation'));
    }

    public function approve(Request $request, $id)
    {
        $invitation = Invitation::findOrFail($id);

        $invitation->update([
            'status' => 'active',
            'published_at' => now(),
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
