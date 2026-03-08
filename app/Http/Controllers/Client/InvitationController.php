<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\InvitationEvent;
use App\Models\LoveStory;
use App\Models\Template;
use App\Models\Package;
use Illuminate\Http\Request;

class InvitationController extends Controller
{
    public function index()
    {
        $invitations = auth()->user()->invitations()
            ->with('template', 'package')
            ->withCount('guests')
            ->latest()
            ->paginate(10);

        return view('client.invitations.index', compact('invitations'));
    }

    public function create()
    {
        $templates = Template::where('is_active', true)->get();
        $packages = Package::where('is_active', true)->get();

        return view('client.invitations.create', compact('templates', 'packages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'template_id' => 'required|exists:templates,id',
            'package_id' => 'required|exists:packages,id',
            'event_type' => 'required|in:wedding,birthday,graduation,corporate,other',
            'title' => 'required|string|max:200',
            'groom_name' => 'nullable|string|max:255',
            'bride_name' => 'nullable|string|max:255',
            'host_name' => 'nullable|string|max:255',
            'event_date' => 'required|date|after:today',
            'event_time' => 'required|date_format:H:i',
            'venue_name' => 'required|string|max:200',
            'venue_address' => 'required|string',
            'google_maps_url' => 'nullable|url',
            'opening_text' => 'nullable|string',
            'closing_text' => 'nullable|string',
        ]);

        // Enforce template access based on package
        $package = Package::findOrFail($validated['package_id']);
        $template = Template::findOrFail($validated['template_id']);

        if ($template->is_premium && !$this->packageCanAccessPremium($package)) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Template \"{$template->name}\" hanya tersedia untuk paket Premium atau Exclusive. Silahkan pilih paket yang sesuai.");
        }

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'draft';

        if ($request->hasFile('cover_photo')) {
            $validated['cover_photo'] = $request->file('cover_photo')->store('invitations/covers', 'public');
        }

        $invitation = Invitation::create($validated);

        return redirect()->route('client.invitations.edit', $invitation)
            ->with('success', 'Undangan berhasil dibuat! Silahkan lengkapi data.');
    }

    public function show(Invitation $invitation)
    {
        $this->authorize($invitation);

        $invitation->load('photos', 'events', 'guests', 'rsvps', 'wishes', 'package');

        $maxGuests = $invitation->package->max_guests ?? 100;
        $maxPhotos = $invitation->package->max_photos ?? 10;
        $currentGuests = $invitation->guests->count();
        $currentPhotos = $invitation->photos->count();

        return view('client.invitations.show', compact('invitation', 'maxGuests', 'maxPhotos', 'currentGuests', 'currentPhotos'));
    }

    public function edit(Invitation $invitation)
    {
        $this->authorize($invitation);

        $invitation->load('photos', 'package', 'events', 'loveStories');
        $templates = Template::where('is_active', true)->get();
        $packages = Package::where('is_active', true)->get();

        return view('client.invitations.edit', compact('invitation', 'templates', 'packages'));
    }

    public function update(Request $request, Invitation $invitation)
    {
        $this->authorize($invitation);

        $validated = $request->validate([
            'template_id' => 'required|exists:templates,id',
            'package_id' => 'required|exists:packages,id',
            'event_type' => 'required|in:wedding,birthday,graduation,corporate,other',
            'title' => 'required|string|max:200',
            'groom_name' => 'nullable|string|max:255',
            'bride_name' => 'nullable|string|max:255',
            'host_name' => 'nullable|string|max:255',
            'event_date' => 'required|date',
            'event_time' => 'required|date_format:H:i',
            'venue_name' => 'required|string|max:200',
            'venue_address' => 'required|string',
            'google_maps_url' => 'nullable|url',
            'opening_text' => 'nullable|string',
            'closing_text' => 'nullable|string',
            'bank_name' => 'nullable|string|max:100',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_account_name' => 'nullable|string|max:255',
            'gift_address' => 'nullable|string',
            'footer_text' => 'nullable|string|max:255',
        ]);

        // Enforce template access on update too
        $package = Package::findOrFail($validated['package_id']);
        $template = Template::findOrFail($validated['template_id']);

        if ($template->is_premium && !$this->packageCanAccessPremium($package)) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Template \"{$template->name}\" hanya tersedia untuk paket Premium atau Exclusive.");
        }

        if ($request->hasFile('cover_photo')) {
            $validated['cover_photo'] = $request->file('cover_photo')->store('invitations/covers', 'public');
        }

        if ($request->hasFile('music_url')) {
            $validated['music_url'] = $request->file('music_url')->store('invitations/music', 'public');
        }

        $invitation->update($validated);

        // Handle Events (Akad / Resepsi)
        if ($request->has('events')) {
            $invitation->events()->delete();
            foreach ($request->input('events', []) as $i => $ev) {
                if (!empty($ev['event_name']) && !empty($ev['venue_name'])) {
                    InvitationEvent::create([
                        'invitation_id' => $invitation->id,
                        'event_name' => $ev['event_name'],
                        'event_date' => $ev['event_date'] ?? $invitation->event_date,
                        'event_time' => $ev['event_time'] ?? '08:00',
                        'event_end_time' => $ev['event_end_time'] ?? null,
                        'venue_name' => $ev['venue_name'],
                        'venue_address' => $ev['venue_address'] ?? '',
                        'venue_maps_url' => $ev['venue_maps_url'] ?? null,
                        'sort_order' => $i,
                    ]);
                }
            }
        }

        // Handle Love Stories
        if ($request->has('love_stories')) {
            $invitation->loveStories()->delete();
            foreach ($request->input('love_stories', []) as $i => $ls) {
                if (!empty($ls['title'])) {
                    LoveStory::create([
                        'invitation_id' => $invitation->id,
                        'year' => $ls['year'] ?? null,
                        'title' => $ls['title'],
                        'description' => $ls['description'] ?? null,
                        'sort_order' => $i,
                    ]);
                }
            }
        }

        return redirect()->route('client.invitations.show', $invitation)
            ->with('success', 'Undangan berhasil diupdate!');
    }

    public function submit(Invitation $invitation)
    {
        $this->authorize($invitation);

        $invitation->update(['status' => 'pending']);

        return redirect()->route('client.invitations.show', $invitation)
            ->with('success', 'Undangan berhasil disubmit untuk review admin!');
    }

    public function destroy(Invitation $invitation)
    {
        $this->authorize($invitation);

        $invitation->delete();

        return redirect()->route('client.invitations.index')
            ->with('success', 'Undangan berhasil dihapus!');
    }

    /**
     * Check if a package can access premium templates.
     */
    private function packageCanAccessPremium(Package $package): bool
    {
        $features = $package->features ?? [];

        // Premium/Exclusive packages have "Semua Template" or "Semua Template Premium" in features
        foreach ($features as $feature) {
            if (str_contains(strtolower($feature), 'semua template')) {
                return true;
            }
        }

        return false;
    }

    private function authorize(Invitation $invitation)
    {
        if ($invitation->user_id !== auth()->id()) {
            abort(403);
        }
    }
}
