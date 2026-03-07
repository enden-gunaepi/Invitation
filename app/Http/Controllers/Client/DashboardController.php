<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\Rsvp;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $stats = [
            'total_invitations' => $user->invitations()->count(),
            'active_invitations' => $user->invitations()->where('status', 'active')->count(),
            'total_guests' => Invitation::where('user_id', $user->id)
                ->withCount('guests')->get()->sum('guests_count'),
            'total_rsvps' => Rsvp::whereHas('invitation', fn($q) => $q->where('user_id', $user->id))->count(),
            'attending' => Rsvp::whereHas('invitation', fn($q) => $q->where('user_id', $user->id))
                ->where('status', 'attending')->count(),
            'total_views' => $user->invitations()->sum('view_count'),
        ];

        $invitations = $user->invitations()
            ->with('template', 'package')
            ->latest()
            ->take(5)
            ->get();

        return view('client.dashboard.index', compact('stats', 'invitations'));
    }
}
