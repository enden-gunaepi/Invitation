<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
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

        $invitation->load('photos', 'events', 'guests', 'rsvps', 'wishes');

        return view('client.invitations.show', compact('invitation'));
    }

    public function edit(Invitation $invitation)
    {
        $this->authorize($invitation);

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
        ]);

        if ($request->hasFile('cover_photo')) {
            $validated['cover_photo'] = $request->file('cover_photo')->store('invitations/covers', 'public');
        }

        $invitation->update($validated);

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

    private function authorize(Invitation $invitation)
    {
        if ($invitation->user_id !== auth()->id()) {
            abort(403);
        }
    }
}
