<?php

namespace App\Http\Middleware;

use App\Models\InvitationView;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class TrackInvitationView
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $slug = (string) $request->route('slug');
        if ($slug !== '' && $response->isSuccessful()) {
            $invitationId = Cache::remember("invitation:slug:{$slug}:id", now()->addMinutes(15), function () use ($slug) {
                return (int) (\App\Models\Invitation::where('slug', $slug)->value('id') ?? 0);
            });

            if ($invitationId > 0) {
                InvitationView::create([
                    'invitation_id' => $invitationId,
                    'guest_token' => $request->route('token'),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'viewed_at' => now(),
                ]);

                DB::table('invitations')->where('id', $invitationId)->increment('view_count');
            }
        }

        return $response;
    }
}
