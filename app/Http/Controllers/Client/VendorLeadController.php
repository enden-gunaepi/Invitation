<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\VendorLead;
use App\Services\InvitationAccessService;
use Illuminate\Http\Request;

class VendorLeadController extends Controller
{
    public function __construct(private readonly InvitationAccessService $invitationAccessService)
    {
    }

    public function store(Request $request, Invitation $invitation)
    {
        $this->authorizeInvitation($invitation);

        $validated = $request->validate([
            'category' => 'required|in:wo,photographer,makeup,entertainment,other',
            'vendor_name' => 'required|string|max:150',
            'contact_name' => 'nullable|string|max:150',
            'phone' => 'nullable|string|max:30',
            'instagram' => 'nullable|string|max:120',
            'status' => 'required|in:new,contacted,negotiation,deal,lost',
            'offered_price' => 'nullable|numeric|min:0',
            'follow_up_date' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $validated['invitation_id'] = $invitation->id;
        $validated['user_id'] = auth()->id();
        $validated['last_contact_at'] = now();
        VendorLead::create($validated);

        return back()->with('success', 'Vendor CRM berhasil ditambahkan.');
    }

    public function update(Request $request, Invitation $invitation, VendorLead $vendor)
    {
        $this->authorizeInvitation($invitation);
        if ($vendor->invitation_id !== $invitation->id) {
            abort(404);
        }

        $validated = $request->validate([
            'status' => 'required|in:new,contacted,negotiation,deal,lost',
            'notes' => 'nullable|string|max:1000',
            'follow_up_date' => 'nullable|date',
            'offered_price' => 'nullable|numeric|min:0',
        ]);
        $validated['last_contact_at'] = now();

        $vendor->update($validated);

        return back()->with('success', 'Status vendor berhasil diperbarui.');
    }

    public function destroy(Invitation $invitation, VendorLead $vendor)
    {
        $this->authorizeInvitation($invitation);
        if ($vendor->invitation_id !== $invitation->id) {
            abort(404);
        }

        $vendor->delete();

        return back()->with('success', 'Vendor CRM berhasil dihapus.');
    }

    private function authorizeInvitation(Invitation $invitation): void
    {
        if (!$this->invitationAccessService->isOwnerOrEditor($invitation, (int) auth()->id())) {
            abort(403);
        }
    }
}
