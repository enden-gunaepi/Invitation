<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use App\Models\Invitation;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function index(Invitation $invitation)
    {
        $this->authorize($invitation);

        $guests = $invitation->guests()->paginate(20);
        $invitation->load('package');

        $maxGuests = $invitation->package->max_guests ?? 100;
        $currentGuests = $invitation->guests()->count();

        return view('client.guests.index', compact('invitation', 'guests', 'maxGuests', 'currentGuests'));
    }

    public function store(Request $request, Invitation $invitation)
    {
        $this->authorize($invitation);

        // Enforce package guest limit
        $invitation->load('package');
        $maxGuests = $invitation->package->max_guests ?? 100;
        $currentGuests = $invitation->guests()->count();

        if ($currentGuests >= $maxGuests) {
            return redirect()->route('client.invitations.guests.index', $invitation)
                ->with('error', "Batas tamu untuk paket {$invitation->package->name} adalah {$maxGuests} orang. Upgrade paket untuk menambah tamu.");
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'category' => 'nullable|string',
            'pax' => 'required|integer|min:1|max:10',
        ]);

        $validated['invitation_id'] = $invitation->id;

        Guest::create($validated);

        return redirect()->route('client.invitations.guests.index', $invitation)
            ->with('success', "Tamu berhasil ditambahkan! ({$currentGuests}/{$maxGuests})");
    }

    public function destroy(Invitation $invitation, Guest $guest)
    {
        $this->authorize($invitation);

        $guest->delete();

        return redirect()->route('client.invitations.guests.index', $invitation)
            ->with('success', 'Tamu berhasil dihapus!');
    }

    private function authorize(Invitation $invitation)
    {
        if ($invitation->user_id !== auth()->id()) {
            abort(403);
        }
    }
}
