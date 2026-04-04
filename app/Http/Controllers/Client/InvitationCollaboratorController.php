<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\InvitationCollaborator;
use App\Models\User;
use App\Services\InvitationAccessService;
use Illuminate\Http\Request;

class InvitationCollaboratorController extends Controller
{
    public function __construct(private readonly InvitationAccessService $invitationAccessService)
    {
    }

    public function store(Request $request, Invitation $invitation)
    {
        if (!$this->invitationAccessService->isOwner($invitation, (int) auth()->id())) {
            abort(403);
        }

        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::query()
            ->where('email', $validated['email'])
            ->where('role', 'client')
            ->first();

        if (!$user) {
            return back()->with('error', 'User client dengan email tersebut tidak ditemukan.');
        }

        if ((int) $user->id === (int) $invitation->user_id) {
            return back()->with('error', 'Owner tidak bisa ditambahkan sebagai kolaborator.');
        }

        InvitationCollaborator::updateOrCreate(
            [
                'invitation_id' => $invitation->id,
                'user_id' => $user->id,
            ],
            [
                'invited_by' => auth()->id(),
                'role' => 'editor',
                'status' => 'pending',
                'accepted_at' => null,
            ]
        );

        return back()->with('success', 'Undangan kolaborator berhasil dikirim.');
    }

    public function accept(InvitationCollaborator $collaborator)
    {
        if ((int) $collaborator->user_id !== (int) auth()->id()) {
            abort(403);
        }

        if ($collaborator->status !== 'accepted') {
            $collaborator->update([
                'status' => 'accepted',
                'accepted_at' => now(),
            ]);
        }

        return redirect()->route('client.invitations.show', $collaborator->invitation_id)
            ->with('success', 'Kolaborasi diterima. Anda sekarang dapat mengedit undangan sebagai editor.');
    }

    public function destroy(Invitation $invitation, InvitationCollaborator $collaborator)
    {
        if (!$this->invitationAccessService->isOwner($invitation, (int) auth()->id())) {
            abort(403);
        }

        if ((int) $collaborator->invitation_id !== (int) $invitation->id) {
            abort(404);
        }

        $collaborator->delete();

        return back()->with('success', 'Kolaborator berhasil dihapus.');
    }
}
