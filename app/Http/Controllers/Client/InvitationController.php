<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\InvitationBankAccount;
use App\Models\InvitationEvent;
use App\Models\LoveStory;
use App\Models\MusicTrack;
use App\Models\Rsvp;
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
        $musicTracks = MusicTrack::where('is_public', true)->latest()->limit(100)->get();

        return view('client.invitations.create', compact('templates', 'packages', 'musicTracks'));
    }

    public function store(Request $request)
    {
        if ($uploadError = $this->getUploadErrorMessage($request, 'music_url')) {
            return back()->withInput()->withErrors(['music_url' => $uploadError]);
        }

        $validated = $request->validate([
            'template_id' => 'required|exists:templates,id',
            'package_id' => 'required|exists:packages,id',
            'event_type' => 'required|in:wedding,birthday,graduation,corporate,other',
            'title' => 'required|string|max:200',
            'groom_name' => 'nullable|string|max:255',
            'groom_parent_name' => 'nullable|string|max:255',
            'bride_name' => 'nullable|string|max:255',
            'bride_parent_name' => 'nullable|string|max:255',
            'host_name' => 'nullable|string|max:255',
            'event_date' => 'required|date|after:today',
            'event_time' => 'required|date_format:H:i',
            'venue_name' => 'required|string|max:200',
            'venue_address' => 'required|string',
            'google_maps_url' => 'nullable|url',
            'livestream_url' => 'nullable|url',
            'livestream_label' => 'nullable|string|max:100',
            'opening_text' => 'nullable|string',
            'closing_text' => 'nullable|string',
            'cover_photo' => 'nullable|image|max:5120',
            'groom_photo' => 'nullable|image|max:5120',
            'bride_photo' => 'nullable|image|max:5120',
            'groom_instagram' => 'nullable|string|max:255',
            'bride_instagram' => 'nullable|string|max:255',
            'groom_facebook' => 'nullable|string|max:255',
            'bride_facebook' => 'nullable|string|max:255',
            'music_url' => 'nullable|file|mimes:mp3,ogg,wav,m4a,aac|max:20480',
            'music_track_id' => 'nullable|exists:music_tracks,id',
            'love_story_photos.*' => 'nullable|image|max:5120',
            'bank_accounts' => 'nullable|array',
            'bank_accounts.*.bank_name' => 'nullable|string|max:100',
            'bank_accounts.*.account_number' => 'nullable|string|max:50',
            'bank_accounts.*.account_name' => 'nullable|string|max:255',
        ], [
            'music_url.uploaded' => 'Upload musik gagal. Coba file lebih kecil (disarankan <= 20MB) atau cek konfigurasi server upload.',
            'music_url.mimes' => 'Format musik harus mp3, ogg, wav, m4a, atau aac.',
            'music_url.max' => 'Ukuran musik maksimal 20MB.',
        ]);

        // Enforce template access based on package
        $package = Package::findOrFail($validated['package_id']);
        $template = Template::findOrFail($validated['template_id']);

        if ($template->is_premium && !$this->packageCanAccessPremium($package)) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Template \"{$template->name}\" hanya tersedia untuk paket Premium atau Exclusive. Silahkan pilih paket yang sesuai.");
        }

        if (!$package->allowsTemplate((int) $validated['template_id'])) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Template \"{$template->name}\" tidak diizinkan untuk paket {$package->name}.");
        }

        $usedInvitations = Invitation::where('user_id', auth()->id())
            ->where('package_id', $package->id)
            ->count();
        $maxInvitations = $package->max_invitations ?? 1;
        if ($usedInvitations >= $maxInvitations) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Paket {$package->name} hanya boleh membuat {$maxInvitations} undangan.");
        }

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'draft';

        if ($request->hasFile('cover_photo')) {
            $validated['cover_photo'] = $request->file('cover_photo')->store('invitations/covers', 'public');
        }
        if ($request->hasFile('groom_photo')) {
            $validated['groom_photo'] = $request->file('groom_photo')->store('invitations/couples', 'public');
        }
        if ($request->hasFile('bride_photo')) {
            $validated['bride_photo'] = $request->file('bride_photo')->store('invitations/couples', 'public');
        }

        if ($request->hasFile('music_url')) {
            $musicFile = $request->file('music_url');
            $validated['music_url'] = $musicFile->store('invitations/music', 'public');

            MusicTrack::create([
                'user_id' => auth()->id(),
                'title' => pathinfo($musicFile->getClientOriginalName(), PATHINFO_FILENAME),
                'file_path' => $validated['music_url'],
                'mime_type' => $musicFile->getMimeType(),
                'file_size' => $musicFile->getSize(),
                'is_public' => true,
                'usage_count' => 1,
            ]);
        } elseif (!empty($validated['music_track_id'])) {
            $track = MusicTrack::find($validated['music_track_id']);
            if ($track) {
                $validated['music_url'] = $track->file_path;
                $track->increment('usage_count');
            }
        }

        unset($validated['music_track_id']);

        $invitation = Invitation::create($validated);

        if ($request->has('bank_accounts')) {
            foreach ($request->input('bank_accounts', []) as $i => $account) {
                if (!empty($account['bank_name']) && !empty($account['account_number']) && !empty($account['account_name'])) {
                    InvitationBankAccount::create([
                        'invitation_id' => $invitation->id,
                        'bank_name' => $account['bank_name'],
                        'account_number' => $account['account_number'],
                        'account_name' => $account['account_name'],
                        'sort_order' => $i,
                    ]);
                }
            }
        }

        // Handle Love Stories on create
        if ($request->has('love_stories')) {
            $loveStoryPhotos = $request->file('love_story_photos', []);
            foreach ($request->input('love_stories', []) as $i => $ls) {
                if (!empty($ls['title'])) {
                    $storyPhotoPath = null;
                    if (isset($loveStoryPhotos[$i]) && $loveStoryPhotos[$i]->isValid()) {
                        $storyPhotoPath = $loveStoryPhotos[$i]->store('invitations/love-stories', 'public');
                    }
                    LoveStory::create([
                        'invitation_id' => $invitation->id,
                        'year' => $ls['year'] ?? null,
                        'title' => $ls['title'],
                        'description' => $ls['description'] ?? null,
                        'photo_path' => $storyPhotoPath,
                        'sort_order' => $i,
                    ]);
                }
            }
        }

        return redirect()->route('client.invitations.edit', $invitation)
            ->with('success', 'Undangan berhasil dibuat! Silahkan lengkapi data.');
    }

    public function show(Invitation $invitation)
    {
        $this->authorize($invitation);

        $invitation->load('photos', 'events', 'guests', 'rsvps', 'wishes', 'package', 'bankAccounts', 'reminderCampaigns', 'vendorLeads');

        $maxGuests = $invitation->package->max_guests ?? 100;
        $maxPhotos = $invitation->package->max_photos ?? 10;
        $maxInvitations = $invitation->package->max_invitations ?? 1;
        $currentGuests = $invitation->guests->count();
        $currentPhotos = $invitation->photos->count();
        $currentInvitations = Invitation::where('user_id', auth()->id())
            ->where('package_id', $invitation->package_id)
            ->count();

        $guestPercent = $maxGuests > 0 ? (int) round(($currentGuests / $maxGuests) * 100) : 0;
        $photoPercent = $maxPhotos > 0 ? (int) round(($currentPhotos / $maxPhotos) * 100) : 0;
        $invitationPercent = $maxInvitations > 0 ? (int) round(($currentInvitations / $maxInvitations) * 100) : 0;

        $upsellReasons = [];
        if ($guestPercent >= 80) {
            $upsellReasons[] = "Kuota tamu hampir penuh ({$guestPercent}%).";
        }
        if ($photoPercent >= 80) {
            $upsellReasons[] = "Kuota foto hampir penuh ({$photoPercent}%).";
        }
        if ($invitationPercent >= 80) {
            $upsellReasons[] = "Kuota jumlah undangan hampir penuh ({$invitationPercent}%).";
        }

        $nextPackage = null;
        if (!empty($upsellReasons)) {
            $nextPackage = Package::where('is_active', true)
                ->where('price', '>', (float) ($invitation->package->price ?? 0))
                ->orderBy('price')
                ->first();
        }

        return view('client.invitations.show', compact(
            'invitation',
            'maxGuests',
            'maxPhotos',
            'maxInvitations',
            'currentGuests',
            'currentPhotos',
            'currentInvitations',
            'nextPackage',
            'upsellReasons'
        ));
    }

    public function edit(Invitation $invitation)
    {
        $this->authorize($invitation);

        $invitation->load('photos', 'package', 'events', 'loveStories', 'bankAccounts');
        $templates = Template::where('is_active', true)->get();
        $packages = Package::where('is_active', true)->get();
        $musicTracks = MusicTrack::where('is_public', true)->latest()->limit(100)->get();

        return view('client.invitations.edit', compact('invitation', 'templates', 'packages', 'musicTracks'));
    }

    public function update(Request $request, Invitation $invitation)
    {
        $this->authorize($invitation);

        if ($uploadError = $this->getUploadErrorMessage($request, 'music_url')) {
            return back()->withInput()->withErrors(['music_url' => $uploadError]);
        }

        $validated = $request->validate([
            'template_id' => 'required|exists:templates,id',
            'package_id' => 'required|exists:packages,id',
            'event_type' => 'required|in:wedding,birthday,graduation,corporate,other',
            'title' => 'required|string|max:200',
            'groom_name' => 'nullable|string|max:255',
            'groom_parent_name' => 'nullable|string|max:255',
            'bride_name' => 'nullable|string|max:255',
            'bride_parent_name' => 'nullable|string|max:255',
            'host_name' => 'nullable|string|max:255',
            'event_date' => 'required|date',
            'event_time' => 'required|date_format:H:i',
            'venue_name' => 'required|string|max:200',
            'venue_address' => 'required|string',
            'google_maps_url' => 'nullable|url',
            'livestream_url' => 'nullable|url',
            'livestream_label' => 'nullable|string|max:100',
            'opening_text' => 'nullable|string',
            'closing_text' => 'nullable|string',
            'bank_name' => 'nullable|string|max:100',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_account_name' => 'nullable|string|max:255',
            'gift_address' => 'nullable|string',
            'footer_text' => 'nullable|string|max:255',
            'cover_photo' => 'nullable|image|max:5120',
            'groom_photo' => 'nullable|image|max:5120',
            'bride_photo' => 'nullable|image|max:5120',
            'groom_instagram' => 'nullable|string|max:255',
            'bride_instagram' => 'nullable|string|max:255',
            'groom_facebook' => 'nullable|string|max:255',
            'bride_facebook' => 'nullable|string|max:255',
            'music_url' => 'nullable|file|mimes:mp3,ogg,wav,m4a,aac|max:20480',
            'music_track_id' => 'nullable|exists:music_tracks,id',
            'events.*.event_description' => 'nullable|string|max:1000',
            'love_story_photos.*' => 'nullable|image|max:5120',
            'bank_accounts' => 'nullable|array',
            'bank_accounts.*.bank_name' => 'nullable|string|max:100',
            'bank_accounts.*.account_number' => 'nullable|string|max:50',
            'bank_accounts.*.account_name' => 'nullable|string|max:255',
        ], [
            'music_url.uploaded' => 'Upload musik gagal. Coba file lebih kecil (disarankan <= 20MB) atau cek konfigurasi server upload.',
            'music_url.mimes' => 'Format musik harus mp3, ogg, wav, m4a, atau aac.',
            'music_url.max' => 'Ukuran musik maksimal 20MB.',
        ]);

        // Enforce template access on update too
        $package = Package::findOrFail($validated['package_id']);
        $template = Template::findOrFail($validated['template_id']);

        if ($template->is_premium && !$this->packageCanAccessPremium($package)) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Template \"{$template->name}\" hanya tersedia untuk paket Premium atau Exclusive.");
        }

        if (!$package->allowsTemplate((int) $validated['template_id'])) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Template \"{$template->name}\" tidak diizinkan untuk paket {$package->name}.");
        }

        $usedInvitations = Invitation::where('user_id', auth()->id())
            ->where('package_id', $package->id)
            ->where('id', '!=', $invitation->id)
            ->count();
        $maxInvitations = $package->max_invitations ?? 1;
        if ($usedInvitations >= $maxInvitations) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Batas undangan untuk paket {$package->name} sudah tercapai ({$maxInvitations}).");
        }

        if ($request->hasFile('cover_photo')) {
            $validated['cover_photo'] = $request->file('cover_photo')->store('invitations/covers', 'public');
        }
        if ($request->hasFile('groom_photo')) {
            $validated['groom_photo'] = $request->file('groom_photo')->store('invitations/couples', 'public');
        }
        if ($request->hasFile('bride_photo')) {
            $validated['bride_photo'] = $request->file('bride_photo')->store('invitations/couples', 'public');
        }

        if ($request->hasFile('music_url')) {
            $musicFile = $request->file('music_url');
            $validated['music_url'] = $musicFile->store('invitations/music', 'public');

            MusicTrack::create([
                'user_id' => auth()->id(),
                'title' => pathinfo($musicFile->getClientOriginalName(), PATHINFO_FILENAME),
                'file_path' => $validated['music_url'],
                'mime_type' => $musicFile->getMimeType(),
                'file_size' => $musicFile->getSize(),
                'is_public' => true,
                'usage_count' => 1,
            ]);
        } elseif (!empty($validated['music_track_id'])) {
            $track = MusicTrack::find($validated['music_track_id']);
            if ($track) {
                $validated['music_url'] = $track->file_path;
                $track->increment('usage_count');
            }
        }

        unset($validated['music_track_id']);

        $invitation->update($validated);

        if ($request->has('bank_accounts')) {
            $invitation->bankAccounts()->delete();
            foreach ($request->input('bank_accounts', []) as $i => $account) {
                if (!empty($account['bank_name']) && !empty($account['account_number']) && !empty($account['account_name'])) {
                    InvitationBankAccount::create([
                        'invitation_id' => $invitation->id,
                        'bank_name' => $account['bank_name'],
                        'account_number' => $account['account_number'],
                        'account_name' => $account['account_name'],
                        'sort_order' => $i,
                    ]);
                }
            }
        }

        // Handle Events (Akad / Resepsi)
        if ($request->has('events')) {
            $invitation->events()->delete();
            foreach ($request->input('events', []) as $i => $ev) {
                if (!empty($ev['event_name']) && !empty($ev['venue_name'])) {
                    InvitationEvent::create([
                        'invitation_id' => $invitation->id,
                        'event_name' => $ev['event_name'],
                        'event_description' => $ev['event_description'] ?? null,
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
            $loveStoryPhotos = $request->file('love_story_photos', []);
            foreach ($request->input('love_stories', []) as $i => $ls) {
                if (!empty($ls['title'])) {
                    $storyPhotoPath = null;
                    if (isset($loveStoryPhotos[$i]) && $loveStoryPhotos[$i]->isValid()) {
                        $storyPhotoPath = $loveStoryPhotos[$i]->store('invitations/love-stories', 'public');
                    }
                    LoveStory::create([
                        'invitation_id' => $invitation->id,
                        'year' => $ls['year'] ?? null,
                        'title' => $ls['title'],
                        'description' => $ls['description'] ?? null,
                        'photo_path' => $storyPhotoPath,
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

    public function upgradeSuggested(Invitation $invitation)
    {
        $this->authorize($invitation);

        $invitation->load('package', 'template');
        $currentPackage = $invitation->package;
        if (!$currentPackage) {
            return back()->with('error', 'Paket undangan tidak ditemukan.');
        }

        $nextPackage = Package::where('is_active', true)
            ->where('price', '>', (float) $currentPackage->price)
            ->orderBy('price')
            ->first();

        if (!$nextPackage) {
            return back()->with('error', 'Belum ada paket upgrade yang tersedia.');
        }

        if (!$nextPackage->allowsTemplate((int) $invitation->template_id)) {
            return back()->with('error', "Template saat ini tidak tersedia di paket {$nextPackage->name}. Ubah template dulu di menu edit.");
        }

        if ($invitation->template && $invitation->template->is_premium && !$this->packageCanAccessPremium($nextPackage)) {
            return back()->with('error', "Template premium saat ini belum didukung paket {$nextPackage->name}.");
        }

        $usedInvitations = Invitation::where('user_id', auth()->id())
            ->where('package_id', $nextPackage->id)
            ->where('id', '!=', $invitation->id)
            ->count();
        $maxInvitations = $nextPackage->max_invitations ?? 1;
        if ($usedInvitations >= $maxInvitations) {
            return back()->with('error', "Kuota undangan pada paket {$nextPackage->name} sudah penuh.");
        }

        $invitation->update(['package_id' => $nextPackage->id]);

        return redirect()->route('client.checkout.show', $invitation)
            ->with('success', "Upgrade 1 klik berhasil. Paket berpindah ke {$nextPackage->name}, lanjutkan pembayaran.");
    }

    public function destroy(Invitation $invitation)
    {
        $this->authorize($invitation);

        $invitation->delete();

        return redirect()->route('client.invitations.index')
            ->with('success', 'Undangan berhasil dihapus!');
    }

    public function analytics(Invitation $invitation)
    {
        $this->authorize($invitation);

        $statusCounts = Rsvp::where('invitation_id', $invitation->id)
            ->selectRaw('status, COUNT(*) as total, COALESCE(SUM(pax),0) as pax_total')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $categoryCounts = Rsvp::where('invitation_id', $invitation->id)
            ->leftJoin('guests', 'rsvps.guest_id', '=', 'guests.id')
            ->selectRaw("COALESCE(guests.category, 'Umum') as category, COUNT(*) as total")
            ->groupBy('category')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        return response()->json([
            'attending' => (int) ($statusCounts['attending']->total ?? 0),
            'attending_pax' => (int) ($statusCounts['attending']->pax_total ?? 0),
            'maybe' => (int) ($statusCounts['maybe']->total ?? 0),
            'not_attending' => (int) ($statusCounts['not_attending']->total ?? 0),
            'categories' => $categoryCounts,
            'checked_in' => (int) $invitation->guests()->whereNotNull('checked_in_at')->count(),
            'total_guests' => (int) $invitation->guests()->count(),
            'generated_at' => now()->toDateTimeString(),
        ]);
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

    private function getUploadErrorMessage(Request $request, string $field): ?string
    {
        if (!isset($_FILES[$field])) {
            return null;
        }

        $errorCode = (int) ($_FILES[$field]['error'] ?? UPLOAD_ERR_OK);
        if ($errorCode === UPLOAD_ERR_OK || $errorCode === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($errorCode === UPLOAD_ERR_INI_SIZE || $errorCode === UPLOAD_ERR_FORM_SIZE) {
            $serverLimit = ini_get('upload_max_filesize') ?: 'unknown';
            $postLimit = ini_get('post_max_size') ?: 'unknown';
            return "Upload gagal karena batas server. upload_max_filesize={$serverLimit}, post_max_size={$postLimit}. Naikkan limit PHP lalu restart server.";
        }

        return 'Upload musik gagal karena error server upload. Coba ulangi atau ganti file.';
    }
}
