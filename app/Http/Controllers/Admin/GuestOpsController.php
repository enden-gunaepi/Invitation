<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use Illuminate\Http\Request;
use App\Services\InvitationFunnelService;

class GuestOpsController extends Controller
{
    public function checkin(Invitation $invitation)
    {
        $checkedIn = $invitation->guests()->whereNotNull('checked_in_at')->count();
        $total = $invitation->guests()->count();
        $recentCheckins = $invitation->guests()
            ->whereNotNull('checked_in_at')
            ->orderByDesc('checked_in_at')
            ->limit(20)
            ->get();

        return view('admin.invitations.checkin', compact('invitation', 'checkedIn', 'total', 'recentCheckins'));
    }

    public function checkinScan(Request $request, Invitation $invitation)
    {
        $validated = $request->validate([
            'token' => 'required|string|min:10',
        ]);

        $token = trim($validated['token']);
        if (str_contains($token, '/inv/')) {
            $parts = explode('/', trim($token, '/'));
            $token = end($parts);
        }

        $guest = $invitation->guests()->where('token', $token)->first();
        if (!$guest) {
            return back()->with('error', 'Guest token tidak ditemukan untuk undangan ini.');
        }

        if ($guest->checked_in_at) {
            return back()->with('success', "Tamu {$guest->name} sudah check-in pada {$guest->checked_in_at->format('H:i')}.");
        }

        $guest->update([
            'checked_in_at' => now(),
            'checkin_method' => 'admin_qr',
            'checked_in_by_user_id' => auth()->id(),
        ]);

        app(InvitationFunnelService::class)->track((int) $invitation->id, 'checked_in', [
            'guest_id' => $guest->id,
            'guest_token' => $guest->token,
            'phone' => $guest->phone,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'source' => 'admin_checkin_scan',
        ]);

        return back()->with('success', "Check-in berhasil: {$guest->name}");
    }
}
