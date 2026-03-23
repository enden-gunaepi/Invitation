<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\Guest;
use App\Models\Rsvp;
use App\Models\Wish;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;

class InvitationPublicController extends Controller
{
    public function show($slug)
    {
        $invitation = $this->preparePublicInvitation($this->getCachedPublicInvitation($slug));

        return view($this->resolveTemplate($invitation), compact('invitation'));
    }

    public function showGuest($slug, $token)
    {
        $invitation = $this->preparePublicInvitation($this->getCachedPublicInvitation($slug));

        $guest = Guest::where('token', $token)
            ->where('invitation_id', $invitation->id)
            ->first();

        return view($this->resolveTemplate($invitation), compact('invitation', 'guest'));
    }

    private function preparePublicInvitation(Invitation $invitation): Invitation
    {
        if (!empty($invitation->music_url)) {
            $invitation->music_signed_url = URL::temporarySignedRoute(
                'media.music',
                now()->addHours(12),
                ['invitation' => $invitation->id]
            );
        }

        return $invitation;
    }

    /**
     * Resolve which Blade template to use based on the invitation's template record.
     */
    private function resolveTemplate(Invitation $invitation): string
    {
        // Try template's html_path from the database
        if ($invitation->template && $invitation->template->html_path) {
            $viewPath = $invitation->template->html_path;

            if (view()->exists($viewPath)) {
                return $viewPath;
            }
        }

        // Fallback to default template
        return 'invitations.templates.wedding-elegant.index';
    }

    public function rsvp(Request $request, $slug)
    {
        $invitation = Invitation::query()
            ->where('slug', $slug)
            ->where('status', 'active')
            ->select(['id', 'slug'])
            ->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:attending,not_attending,maybe',
            'pax' => 'required|integer|min:1|max:10',
            'message' => 'nullable|string|max:500',
            'guest_id' => 'nullable|exists:guests,id',
        ]);

        $validated['invitation_id'] = $invitation->id;
        $validated['ip_address'] = $request->ip();

        Rsvp::create($validated);
        $this->forgetPublicInvitationCache($slug);

        return redirect()->back()->with('success', 'Terima kasih atas konfirmasi kehadiran Anda!');
    }

    public function wish(Request $request, $slug)
    {
        $invitation = Invitation::query()
            ->where('slug', $slug)
            ->where('status', 'active')
            ->select(['id', 'slug'])
            ->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'message' => 'required|string|max:1000',
        ]);

        $validated['invitation_id'] = $invitation->id;
        $validated['ip_address'] = $request->ip();

        Wish::create($validated);
        $this->forgetPublicInvitationCache($slug);

        return redirect()->back()->with('success', 'Ucapan Anda berhasil dikirim!');
    }

    private function getCachedPublicInvitation(string $slug): Invitation
    {
        $cacheKey = $this->publicInvitationCacheKey($slug);

        return Cache::remember($cacheKey, now()->addSeconds(30), function () use ($slug) {
            return Invitation::query()
                ->where('slug', $slug)
                ->where('status', 'active')
                ->with([
                    'template:id,name,html_path',
                    'photos:id,invitation_id,file_path,caption,sort_order',
                    'events:id,invitation_id,event_name,event_description,event_date,event_time,event_end_time,venue_name,venue_address,venue_maps_url,sort_order',
                    'loveStories:id,invitation_id,year,title,description,photo_path,sort_order',
                    'bankAccounts:id,invitation_id,bank_name,account_number,account_name,sort_order',
                    'wishes' => function ($q) {
                        $q->select(['id', 'invitation_id', 'name', 'message', 'created_at'])
                            ->where('is_approved', true)
                            ->latest()
                            ->take(50);
                    },
                    'rsvps' => function ($q) {
                        $q->select(['id', 'invitation_id', 'name', 'status', 'message', 'pax', 'created_at'])
                            ->where('is_shown', true)
                            ->latest()
                            ->take(50);
                    },
                ])
                ->firstOrFail();
        });
    }

    private function forgetPublicInvitationCache(string $slug): void
    {
        Cache::forget($this->publicInvitationCacheKey($slug));
    }

    private function publicInvitationCacheKey(string $slug): string
    {
        return "public:invitation:{$slug}:v1";
    }
}
