<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\ReminderCampaign;
use App\Services\InvitationAccessService;
use Illuminate\Http\Request;

class ReminderCampaignController extends Controller
{
    public function __construct(private readonly InvitationAccessService $invitationAccessService)
    {
    }

    public function store(Request $request, Invitation $invitation)
    {
        $this->authorizeInvitation($invitation);

        $validated = $request->validate([
            'message_template' => 'required|string|max:2000',
            'audience' => 'required|in:all_guests,no_rsvp,not_checked_in',
            'scheduled_at' => 'required|date|after_or_equal:now',
            'notes' => 'nullable|string|max:300',
        ]);

        ReminderCampaign::create([
            'invitation_id' => $invitation->id,
            'created_by_user_id' => auth()->id(),
            'channel' => 'whatsapp',
            'audience' => $validated['audience'],
            'message_template' => $validated['message_template'],
            'scheduled_at' => $validated['scheduled_at'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'scheduled',
            'source' => 'manual',
        ]);

        return back()->with('success', 'Campaign reminder WhatsApp berhasil dijadwalkan.');
    }

    public function cancel(Invitation $invitation, ReminderCampaign $campaign)
    {
        $this->authorizeInvitation($invitation);
        if ($campaign->invitation_id !== $invitation->id) {
            abort(404);
        }

        if ($campaign->status !== 'scheduled') {
            return back()->with('error', 'Campaign tidak bisa dibatalkan karena sudah diproses.');
        }

        $campaign->update(['status' => 'cancelled']);

        return back()->with('success', 'Campaign reminder berhasil dibatalkan.');
    }

    private function authorizeInvitation(Invitation $invitation): void
    {
        if (!$this->invitationAccessService->isOwnerOrEditor($invitation, (int) auth()->id())) {
            abort(403);
        }
    }
}
