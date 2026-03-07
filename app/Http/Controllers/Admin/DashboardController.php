<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\User;
use App\Models\Package;
use App\Models\Template;
use App\Models\Rsvp;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::where('role', 'client')->count(),
            'total_invitations' => Invitation::count(),
            'active_invitations' => Invitation::where('status', 'active')->count(),
            'pending_invitations' => Invitation::where('status', 'pending')->count(),
            'total_rsvps' => Rsvp::count(),
            'total_templates' => Template::where('is_active', true)->count(),
            'total_packages' => Package::where('is_active', true)->count(),
            'attending_rsvps' => Rsvp::where('status', 'attending')->count(),
        ];

        $recentInvitations = Invitation::with('user', 'template')
            ->latest()
            ->take(5)
            ->get();

        $recentUsers = User::where('role', 'client')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard.index', compact('stats', 'recentInvitations', 'recentUsers'));
    }
}
