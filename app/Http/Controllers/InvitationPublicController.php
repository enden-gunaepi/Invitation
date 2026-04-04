<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Invitation;
use App\Models\Rsvp;
use App\Models\Wish;
use App\Services\GuestPersonalizationService;
use App\Services\InvitationFunnelService;
use App\Services\PhoneNormalizerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;

class InvitationPublicController extends Controller
{
    public function __construct(
        private readonly PhoneNormalizerService $phoneNormalizer,
        private readonly GuestPersonalizationService $guestPersonalization,
        private readonly InvitationFunnelService $funnelService,
    ) {
    }

    public function show($slug)
    {
        $invitation = $this->preparePublicInvitation($this->getCachedPublicInvitation($slug));
        $personalization = $this->guestPersonalization->forCategory(null);

        return view($this->resolveTemplate($invitation), compact('invitation', 'personalization'));
    }

    public function showGuest($slug, $token)
    {
        $invitation = $this->preparePublicInvitation($this->getCachedPublicInvitation($slug));

        $guest = Guest::where('token', $token)
            ->where('invitation_id', $invitation->id)
            ->first();

        $personalization = $this->guestPersonalization->forCategory($guest?->category);

        return view($this->resolveTemplate($invitation), compact('invitation', 'guest', 'personalization'));
    }

    public function mapClick(Request $request, string $slug)
    {
        $invitation = Invitation::query()
            ->where('slug', $slug)
            ->active()
            ->firstOrFail();

        $token = trim((string) $request->query('token', ''));
        $guest = null;
        if ($token !== '') {
            $guest = Guest::query()
                ->where('invitation_id', $invitation->id)
                ->where('token', $token)
                ->first();
        }

        $this->funnelService->track((int) $invitation->id, 'map_clicked', [
            'guest_id' => $guest?->id,
            'guest_token' => $guest?->token,
            'phone' => $guest?->phone,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'source' => 'public_map',
        ]);

        return redirect()->away($invitation->maps_deep_link);
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

    private function resolveTemplate(Invitation $invitation): string
    {
        if ($invitation->template && $invitation->template->html_path) {
            $viewPath = $invitation->template->html_path;

            if (view()->exists($viewPath)) {
                return $viewPath;
            }
        }

        return 'invitations.templates.wedding-elegant.index';
    }

    public function rsvp(Request $request, $slug)
    {
        $invitation = Invitation::query()
            ->where('slug', $slug)
            ->active()
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

        $guest = null;
        if (!empty($validated['guest_id'])) {
            $guest = Guest::query()
                ->where('id', $validated['guest_id'])
                ->where('invitation_id', $invitation->id)
                ->first();
        }

        $normalizedPhone = $this->phoneNormalizer->normalizeIndonesia($validated['phone'] ?? null);
        if (!$guest && empty($normalizedPhone)) {
            return back()->withErrors([
                'phone' => 'Nomor HP wajib diisi jika tidak menggunakan link tamu personal.',
            ])->withInput();
        }

        $payload = [
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? null,
            'normalized_phone' => $normalizedPhone,
            'status' => $validated['status'],
            'pax' => $validated['pax'],
            'message' => $validated['message'] ?? null,
            'ip_address' => $request->ip(),
            'guest_id' => $guest?->id,
        ];

        if ($guest) {
            Rsvp::updateOrCreate(
                [
                    'invitation_id' => $invitation->id,
                    'guest_id' => $guest->id,
                ],
                $payload
            );
        } else {
            Rsvp::updateOrCreate(
                [
                    'invitation_id' => $invitation->id,
                    'normalized_phone' => $normalizedPhone,
                ],
                $payload
            );
        }

        $this->funnelService->track((int) $invitation->id, 'rsvp_submitted', [
            'guest_id' => $guest?->id,
            'guest_token' => $guest?->token,
            'phone' => $normalizedPhone,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'source' => 'public_rsvp',
            'meta' => [
                'status' => $validated['status'],
                'pax' => (int) $validated['pax'],
            ],
        ]);

        $this->forgetPublicInvitationCache($slug);

        return $this->redirectBackWithAnchor($request, 'Terima kasih, RSVP Anda berhasil disimpan dan bisa diperbarui kapan saja.');
    }

    public function wish(Request $request, $slug)
    {
        $invitation = Invitation::query()
            ->where('slug', $slug)
            ->active()
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

        return $this->redirectBackWithAnchor($request, 'Ucapan Anda berhasil dikirim!');
    }

    private function getCachedPublicInvitation(string $slug): Invitation
    {
        $cacheKey = $this->publicInvitationCacheKey($slug);

        return Cache::remember($cacheKey, now()->addSeconds(30), function () use ($slug) {
            return Invitation::query()
                ->where('slug', $slug)
                ->active()
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

    private function redirectBackWithAnchor(Request $request, string $message)
    {
        $anchor = trim((string) $request->input('redirect_anchor', ''));
        $anchor = preg_replace('/[^a-zA-Z0-9\-_]/', '', $anchor ?? '');

        $previousUrl = url()->previous();
        $target = $previousUrl;
        if (!empty($anchor)) {
            $target = rtrim($previousUrl, '#') . '#' . $anchor;
        }

        return redirect($target)->with('success', $message);
    }
}
