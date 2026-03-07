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
        if ($invitation->user_id !== auth()->id()) {
            abort(403);
        }

        $guests = $invitation->guests()->paginate(20);

        return view('client.guests.index', compact('invitation', 'guests'));
    }

    public function store(Request $request, Invitation $invitation)
    {
        if ($invitation->user_id !== auth()->id()) {
            abort(403);
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
            ->with('success', 'Tamu berhasil ditambahkan!');
    }

    public function destroy(Invitation $invitation, Guest $guest)
    {
        if ($invitation->user_id !== auth()->id()) {
            abort(403);
        }

        $guest->delete();

        return redirect()->route('client.invitations.guests.index', $invitation)
            ->with('success', 'Tamu berhasil dihapus!');
    }
}
