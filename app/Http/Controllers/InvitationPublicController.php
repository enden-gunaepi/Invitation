<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\Guest;
use App\Models\Rsvp;
use App\Models\Wish;
use Illuminate\Http\Request;

class InvitationPublicController extends Controller
{
    public function show($slug)
    {
        $invitation = Invitation::where('slug', $slug)
            ->where('status', 'active')
            ->with(['template', 'photos', 'events', 'loveStories', 'wishes' => function ($q) {
                $q->where('is_approved', true)->latest()->take(50);
            }, 'rsvps' => function ($q) {
                $q->where('is_shown', true)->latest()->take(50);
            }])
            ->firstOrFail();

        return view($this->resolveTemplate($invitation), compact('invitation'));
    }

    public function showGuest($slug, $token)
    {
        $invitation = Invitation::where('slug', $slug)
            ->where('status', 'active')
            ->with(['template', 'photos', 'events', 'loveStories', 'wishes' => function ($q) {
                $q->where('is_approved', true)->latest()->take(50);
            }, 'rsvps' => function ($q) {
                $q->where('is_shown', true)->latest()->take(50);
            }])
            ->firstOrFail();

        $guest = Guest::where('token', $token)
            ->where('invitation_id', $invitation->id)
            ->first();

        return view($this->resolveTemplate($invitation), compact('invitation', 'guest'));
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
        $invitation = Invitation::where('slug', $slug)
            ->where('status', 'active')
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

        return redirect()->back()->with('success', 'Terima kasih atas konfirmasi kehadiran Anda!');
    }

    public function wish(Request $request, $slug)
    {
        $invitation = Invitation::where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'message' => 'required|string|max:1000',
        ]);

        $validated['invitation_id'] = $invitation->id;
        $validated['ip_address'] = $request->ip();

        Wish::create($validated);

        return redirect()->back()->with('success', 'Ucapan Anda berhasil dikirim!');
    }
}
