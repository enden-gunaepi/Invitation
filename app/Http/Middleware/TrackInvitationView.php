<?php

namespace App\Http\Middleware;

use App\Models\InvitationView;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackInvitationView
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->route('slug')) {
            $invitation = \App\Models\Invitation::where('slug', $request->route('slug'))->first();
            if ($invitation) {
                InvitationView::create([
                    'invitation_id' => $invitation->id,
                    'guest_token' => $request->route('token'),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'viewed_at' => now(),
                ]);

                $invitation->increment('view_count');
            }
        }

        return $response;
    }
}
