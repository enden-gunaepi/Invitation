<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\InvitationBackup;
use App\Models\InvitationBankAccount;
use App\Models\InvitationEvent;
use App\Models\InvitationPhoto;
use App\Models\LoveStory;
use App\Services\InvitationAccessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InvitationBackupController extends Controller
{
    public function __construct(private readonly InvitationAccessService $invitationAccessService)
    {
    }

    public function store(Request $request, Invitation $invitation)
    {
        $this->authorizeEditor($invitation);

        $validated = $request->validate([
            'label' => 'nullable|string|max:150',
            'notes' => 'nullable|string|max:500',
        ]);

        $invitation->load(['events', 'photos', 'loveStories', 'bankAccounts', 'guests', 'rsvps', 'wishes', 'reminderCampaigns']);

        InvitationBackup::create([
            'invitation_id' => $invitation->id,
            'created_by_user_id' => auth()->id(),
            'label' => $validated['label'] ?? ('Backup ' . now()->format('d M Y H:i')),
            'notes' => $validated['notes'] ?? null,
            'snapshot' => [
                'invitation' => $invitation->toArray(),
                'events' => $invitation->events->toArray(),
                'photos' => $invitation->photos->toArray(),
                'love_stories' => $invitation->loveStories->toArray(),
                'bank_accounts' => $invitation->bankAccounts->toArray(),
                'guests' => $invitation->guests->toArray(),
                'rsvps' => $invitation->rsvps->toArray(),
                'wishes' => $invitation->wishes->toArray(),
                'reminder_campaigns' => $invitation->reminderCampaigns->toArray(),
            ],
        ]);

        return back()->with('success', 'Backup undangan berhasil dibuat.');
    }

    public function restore(Invitation $invitation, InvitationBackup $backup)
    {
        $this->authorizeEditor($invitation);

        if ((int) $backup->invitation_id !== (int) $invitation->id) {
            abort(404);
        }

        $snapshot = (array) $backup->snapshot;
        $base = (array) ($snapshot['invitation'] ?? []);

        $payload = $base;
        unset(
            $payload['id'],
            $payload['user_id'],
            $payload['slug'],
            $payload['status'],
            $payload['published_at'],
            $payload['expires_at'],
            $payload['created_at'],
            $payload['updated_at']
        );

        $payload['user_id'] = auth()->id();
        $payload['slug'] = Str::slug((string) ($base['title'] ?? 'invitation')) . '-' . Str::random(6);
        $payload['status'] = 'draft';
        $payload['published_at'] = null;
        $payload['expires_at'] = null;

        $newInvitation = Invitation::create($payload);

        foreach ((array) ($snapshot['events'] ?? []) as $index => $event) {
            InvitationEvent::create([
                'invitation_id' => $newInvitation->id,
                'event_name' => $event['event_name'] ?? 'Acara',
                'event_description' => $event['event_description'] ?? null,
                'event_date' => $event['event_date'] ?? $newInvitation->event_date,
                'event_time' => $event['event_time'] ?? '08:00',
                'event_end_time' => $event['event_end_time'] ?? null,
                'venue_name' => $event['venue_name'] ?? $newInvitation->venue_name,
                'venue_address' => $event['venue_address'] ?? $newInvitation->venue_address,
                'venue_maps_url' => $event['venue_maps_url'] ?? null,
                'sort_order' => $index,
            ]);
        }

        foreach ((array) ($snapshot['bank_accounts'] ?? []) as $index => $acc) {
            InvitationBankAccount::create([
                'invitation_id' => $newInvitation->id,
                'bank_name' => $acc['bank_name'] ?? '',
                'account_number' => $acc['account_number'] ?? '',
                'account_name' => $acc['account_name'] ?? '',
                'sort_order' => $index,
            ]);
        }

        foreach ((array) ($snapshot['photos'] ?? []) as $index => $photo) {
            $newPath = $this->copyMediaPath((string) ($photo['file_path'] ?? ''), 'invitations/photos');
            if (!$newPath) {
                continue;
            }

            InvitationPhoto::create([
                'invitation_id' => $newInvitation->id,
                'file_path' => $newPath,
                'caption' => $photo['caption'] ?? null,
                'sort_order' => $index,
            ]);
        }

        foreach ((array) ($snapshot['love_stories'] ?? []) as $index => $story) {
            $newPath = $this->copyMediaPath((string) ($story['photo_path'] ?? ''), 'invitations/love-stories');
            LoveStory::create([
                'invitation_id' => $newInvitation->id,
                'year' => $story['year'] ?? null,
                'title' => $story['title'] ?? 'Love Story',
                'description' => $story['description'] ?? null,
                'photo_path' => $newPath,
                'sort_order' => $index,
            ]);
        }

        return redirect()->route('client.invitations.edit', $newInvitation)
            ->with('success', 'Restore berhasil. Draft undangan baru telah dibuat dari backup.');
    }

    private function copyMediaPath(string $path, string $targetDir): ?string
    {
        $path = ltrim($path, '/');
        if ($path === '' || !Storage::disk('public')->exists($path)) {
            return null;
        }

        $filename = pathinfo($path, PATHINFO_FILENAME);
        $ext = pathinfo($path, PATHINFO_EXTENSION) ?: 'webp';
        $newPath = $targetDir . '/' . $filename . '-' . Str::lower(Str::random(8)) . '.' . $ext;

        Storage::disk('public')->copy($path, $newPath);

        return $newPath;
    }

    private function authorizeEditor(Invitation $invitation): void
    {
        if (!$this->invitationAccessService->isOwnerOrEditor($invitation, (int) auth()->id())) {
            abort(403);
        }
    }
}
