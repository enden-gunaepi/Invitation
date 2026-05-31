<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\InvitationBankAccount;
use App\Models\InvitationEvent;
use App\Models\LoveStory;
use App\Models\MusicTrack;
use App\Models\Payment;
use App\Models\Rsvp;
use App\Models\Template;
use App\Models\Package;
use App\Services\ImageCompressionService;
use App\Services\ClientPackageService;
use App\Services\InvitationAccessService;
use App\Services\InvitationFunnelService;
use App\Services\InvitationMediaCleanupService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

class InvitationController extends Controller
{
    public function __construct(
        private readonly ImageCompressionService $imageCompressionService,
        private readonly ClientPackageService $clientPackageService,
        private readonly InvitationFunnelService $funnelService,
        private readonly InvitationAccessService $invitationAccessService,
        private readonly InvitationMediaCleanupService $mediaCleanupService,
    )
    {
    }

    public function index()
    {
        $userId = (int) auth()->id();
        $subscriptions = $this->clientPackageService->getUsableSubscriptions($userId)
            ->load([
                'invitations' => fn ($query) => $query
                    ->with('template', 'package')
                    ->withCount('guests')
                    ->latest(),
            ]);

        $subscriptionCards = $subscriptions->map(function ($subscription) {
            return [
                'subscription' => $subscription,
                'usage' => $this->clientPackageService->getSubscriptionUsage($subscription),
                'invitations' => $subscription->invitations,
            ];
        });

        $legacyInvitations = auth()->user()->invitations()
            ->with('template', 'package')
            ->withCount('guests')
            ->whereNull('client_package_subscription_id')
            ->latest()
            ->get();

        $totalInvitations = (int) auth()->user()->invitations()->count();
        $hasUsableSubscription = $subscriptions->isNotEmpty();

        return view('client.invitations.index', compact(
            'subscriptionCards',
            'legacyInvitations',
            'totalInvitations',
            'hasUsableSubscription'
        ));
    }

    public function create()
    {
        $userId = (int) auth()->id();
        $usableSubscriptions = $this->clientPackageService->getUsableSubscriptions($userId);

        if ($usableSubscriptions->isEmpty()) {
            return redirect()->route('client.packages.select')
                ->with('error', 'Pilih paket aktif terlebih dahulu sebelum membuat undangan.');
        }

        $subscriptionId = request()->integer('subscription_id');
        if (!$subscriptionId && $usableSubscriptions->count() === 1) {
            $subscriptionId = (int) $usableSubscriptions->first()->id;
        }

        if (!$subscriptionId) {
            return redirect()->route('client.invitations.index')
                ->with('error', 'Pilih paket yang akan dipakai untuk membuat undangan.');
        }

        $selectedSubscription = $this->clientPackageService->findAuthorizedUsableSubscription($userId, $subscriptionId);
        if (!$selectedSubscription || !$selectedSubscription->package) {
            return redirect()->route('client.invitations.index')
                ->with('error', 'Paket yang dipilih tidak aktif atau tidak ditemukan.');
        }

        $selectedPackage = $selectedSubscription->package;

        $templatesQuery = Template::where('is_active', true);
        if (!empty($selectedPackage->allowed_template_ids)) {
            $templatesQuery->whereIn('id', $selectedPackage->allowed_template_ids);
        }
        $templates = $templatesQuery->get();

        $musicTracks = MusicTrack::where('is_public', true)->latest()->limit(100)->get();
        $preselectedTemplateId = request()->integer('template_id');
        $subscriptionUsage = $this->clientPackageService->getSubscriptionUsage($selectedSubscription);

        return view('client.invitations.create', compact(
            'templates',
            'musicTracks',
            'preselectedTemplateId',
            'selectedSubscription',
            'selectedPackage',
            'subscriptionUsage'
        ));
    }

    public function store(Request $request)
    {
        if ($uploadError = $this->getUploadErrorMessage($request, 'music_url')) {
            return back()->withInput()->withErrors(['music_url' => $uploadError]);
        }
        if ($mimeErrors = $this->validateUploadMimeSniff($request)) {
            return back()->withInput()->withErrors($mimeErrors);
        }

        $validated = $request->validate([
            'client_package_subscription_id' => 'required|integer|exists:client_package_subscriptions,id',
            'template_id' => 'required|exists:templates,id',
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
            'venue_lat' => 'nullable|numeric',
            'venue_lng' => 'nullable|numeric',
            'google_maps_url' => 'nullable|url',
            'livestream_enabled' => 'nullable|boolean',
            'livestream_url' => 'nullable|required_if:livestream_enabled,1|url',
            'livestream_label' => 'nullable|string|max:100',
            'opening_text' => 'nullable|string',
            'closing_text' => 'nullable|string',
            'cover_photo' => 'nullable|image|max:10240',
            'groom_photo' => 'nullable|image|max:10240',
            'bride_photo' => 'nullable|image|max:10240',
            'groom_instagram' => 'nullable|string|max:255',
            'bride_instagram' => 'nullable|string|max:255',
            'groom_facebook' => 'nullable|string|max:255',
            'bride_facebook' => 'nullable|string|max:255',
            'music_url' => 'nullable|file|mimes:mp3,ogg,wav,m4a,aac|max:20480',
            'music_track_id' => 'nullable|exists:music_tracks,id',
            'love_story_photos.*' => 'nullable|image|max:10240',
            'love_stories.*.photo_path' => 'nullable|string|max:255',
            'bank_accounts' => 'nullable|array',
            'bank_accounts.*.bank_name' => 'nullable|string|max:100',
            'bank_accounts.*.account_number' => 'nullable|string|max:50',
            'bank_accounts.*.account_name' => 'nullable|string|max:255',
        ], [
            'music_url.uploaded' => 'Upload musik gagal. Coba file lebih kecil (disarankan <= 20MB) atau cek konfigurasi server upload.',
            'music_url.mimes' => 'Format musik harus mp3, ogg, wav, m4a, atau aac.',
            'music_url.max' => 'Ukuran musik maksimal 20MB.',
            'livestream_url.required_if' => 'Link live streaming wajib diisi jika fitur live streaming diaktifkan.',
            'love_story_photos.*.max' => 'Ukuran foto love story maksimal 10MB.',
        ]);

        [$canCreate, $subscriptionMessage, $selectedSubscription] = $this->clientPackageService->canCreateInvitationWithSubscription(
            (int) auth()->id(),
            (int) $validated['client_package_subscription_id'],
            (int) $validated['template_id']
        );

        if (!$canCreate || !$selectedSubscription || !$selectedSubscription->package) {
            return redirect()->back()
                ->withInput()
                ->with('error', $subscriptionMessage ?? 'Paket aktif tidak ditemukan. Silakan pilih paket terlebih dahulu.');
        }

        $validated['user_id'] = auth()->id();
        $validated['package_id'] = $selectedSubscription->package->id;
        $validated['client_package_subscription_id'] = $selectedSubscription->id;
        $validated['status'] = 'draft';
        $validated['livestream_enabled'] = $request->boolean('livestream_enabled');

        if (!$validated['livestream_enabled']) {
            $validated['livestream_url'] = null;
            $validated['livestream_label'] = null;
        }

        $this->storeCompressedImageIfPresent($request, $validated, 'cover_photo', 'invitations/covers');
        $this->storeCompressedImageIfPresent($request, $validated, 'groom_photo', 'invitations/couples');
        $this->storeCompressedImageIfPresent($request, $validated, 'bride_photo', 'invitations/couples');

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
                        $storyPhotoPath = $this->storeCompressedLoveStoryPhoto($loveStoryPhotos[$i], $i);
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

        (new \App\Services\TelegramNotificationService())->invitationCreated($invitation->load('user', 'package'));

        return redirect()->route('client.invitations.index')
            ->with('success', 'Undangan berhasil dibuat!');
    }

    public function show(Invitation $invitation)
    {
        $this->authorizeAnyEditor($invitation);

        $invitation->load('photos', 'events', 'loveStories', 'guests', 'rsvps.guest', 'wishes', 'package', 'clientPackageSubscription.package', 'bankAccounts', 'reminderCampaigns', 'vendorLeads', 'collaborators.user', 'backups');

        $effectivePackage = $this->resolveInvitationPackage($invitation);

        $maxGuests = $effectivePackage->max_guests ?? 100;
        $maxPhotos = $effectivePackage->max_photos ?? 10;
        $maxInvitations = $effectivePackage->max_invitations ?? 1;
        $currentGuests = $invitation->guests->count();
        $currentPhotos = $invitation->photos->count();
        $currentInvitations = $this->resolveInvitationQuotaUsage($invitation);

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
                ->where('price', '>', (float) ($effectivePackage->price ?? 0))
                ->orderBy('price')
                ->first();
        }

        $activePackage = $effectivePackage;

        return view('client.invitations.show', compact(
            'invitation',
            'maxGuests',
            'maxPhotos',
            'maxInvitations',
            'currentGuests',
            'currentPhotos',
            'currentInvitations',
            'nextPackage',
            'upsellReasons',
            'activePackage',
            'effectivePackage'
        ));
    }

    public function edit(Invitation $invitation)
    {
        $this->authorizeAnyEditor($invitation);

        $invitation->load('photos', 'package', 'events', 'loveStories', 'bankAccounts');
        $templates = Template::where('is_active', true)->get();
        $musicTracks = MusicTrack::where('is_public', true)->latest()->limit(100)->get();

        return view('client.invitations.edit', compact('invitation', 'templates', 'musicTracks'));
    }

    public function update(Request $request, Invitation $invitation)
    {
        $this->authorizeAnyEditor($invitation);
        $isOwner = $this->invitationAccessService->isOwner($invitation, (int) auth()->id());
        $previousPackageId = (int) $invitation->package_id;
        $previousMedia = [
            'cover_photo' => $invitation->cover_photo,
            'groom_photo' => $invitation->groom_photo,
            'bride_photo' => $invitation->bride_photo,
            'music_url' => $invitation->music_url,
        ];

        if ($uploadError = $this->getUploadErrorMessage($request, 'music_url')) {
            return back()->withInput()->withErrors(['music_url' => $uploadError]);
        }
        if ($mimeErrors = $this->validateUploadMimeSniff($request)) {
            return back()->withInput()->withErrors($mimeErrors);
        }

        $validated = $request->validate([
            'template_id' => 'required|exists:templates,id',
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
            'venue_lat' => 'nullable|numeric',
            'venue_lng' => 'nullable|numeric',
            'google_maps_url' => 'nullable|url',
            'livestream_enabled' => 'nullable|boolean',
            'livestream_url' => 'nullable|required_if:livestream_enabled,1|url',
            'livestream_label' => 'nullable|string|max:100',
            'opening_text' => 'nullable|string',
            'closing_text' => 'nullable|string',
            'bank_name' => 'nullable|string|max:100',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_account_name' => 'nullable|string|max:255',
            'gift_address' => 'nullable|string',
            'footer_text' => 'nullable|string|max:255',
            'cover_photo' => 'nullable|image|max:10240',
            'groom_photo' => 'nullable|image|max:10240',
            'bride_photo' => 'nullable|image|max:10240',
            'groom_instagram' => 'nullable|string|max:255',
            'bride_instagram' => 'nullable|string|max:255',
            'groom_facebook' => 'nullable|string|max:255',
            'bride_facebook' => 'nullable|string|max:255',
            'music_url' => 'nullable|file|mimes:mp3,ogg,wav,m4a,aac|max:20480',
            'music_track_id' => 'nullable|exists:music_tracks,id',
            'events.*.event_description' => 'nullable|string|max:1000',
            'love_story_photos.*' => 'nullable|image|max:10240',
            'love_stories.*.photo_path' => 'nullable|string|max:255',
            'bank_accounts' => 'nullable|array',
            'bank_accounts.*.bank_name' => 'nullable|string|max:100',
            'bank_accounts.*.account_number' => 'nullable|string|max:50',
            'bank_accounts.*.account_name' => 'nullable|string|max:255',
        ], [
            'music_url.uploaded' => 'Upload musik gagal. Coba file lebih kecil (disarankan <= 20MB) atau cek konfigurasi server upload.',
            'music_url.mimes' => 'Format musik harus mp3, ogg, wav, m4a, atau aac.',
            'music_url.max' => 'Ukuran musik maksimal 20MB.',
            'livestream_url.required_if' => 'Link live streaming wajib diisi jika fitur live streaming diaktifkan.',
            'love_story_photos.*.max' => 'Ukuran foto love story maksimal 10MB.',
        ]);

        $validated['livestream_enabled'] = $request->boolean('livestream_enabled');
        if (!$validated['livestream_enabled']) {
            $validated['livestream_url'] = null;
            $validated['livestream_label'] = null;
        }

        if (!$isOwner) {
            $validated['template_id'] = $invitation->template_id;
        }
        $validated['package_id'] = $invitation->package_id;
        $validated['client_package_subscription_id'] = $invitation->client_package_subscription_id;

        // Enforce template access on update too
        $package = $this->resolveInvitationPackage($invitation);
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

        if ($isOwner) {
            $usedInvitations = $invitation->client_package_subscription_id
                ? Invitation::where('client_package_subscription_id', $invitation->client_package_subscription_id)
                    ->where('id', '!=', $invitation->id)
                    ->count()
                : Invitation::where('user_id', auth()->id())
                    ->where('package_id', $invitation->package_id)
                    ->where('id', '!=', $invitation->id)
                    ->count();
            $maxInvitations = $package?->max_invitations ?? 1;
            if ($usedInvitations >= $maxInvitations) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', "Batas undangan untuk paket {$package?->name} sudah tercapai ({$maxInvitations}).");
            }
        }

        $this->storeCompressedImageIfPresent($request, $validated, 'cover_photo', 'invitations/covers');
        $this->storeCompressedImageIfPresent($request, $validated, 'groom_photo', 'invitations/couples');
        $this->storeCompressedImageIfPresent($request, $validated, 'bride_photo', 'invitations/couples');

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

        foreach (['cover_photo', 'groom_photo', 'bride_photo'] as $field) {
            if (!empty($previousMedia[$field]) && $previousMedia[$field] !== ($invitation->{$field} ?? null)) {
                $this->mediaCleanupService->deleteImagePathIfUnused($previousMedia[$field], $invitation->id);
            }
        }
        if (!empty($previousMedia['music_url']) && $previousMedia['music_url'] !== ($invitation->music_url ?? null)) {
            $this->mediaCleanupService->deleteMusicPathIfUnused($previousMedia['music_url'], $invitation->id);
        }

        $packageChanged = $previousPackageId !== (int) $invitation->package_id;
        if ($packageChanged && $invitation->status === 'active') {
            $invitation->load('package');
            $base = $invitation->published_at ?? now();
            $invitation->update([
                'expires_at' => $invitation->calculateExpiresAtFromPackage($base),
            ]);
        }

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
            $retainedStoryPaths = collect($request->input('love_stories', []))
                ->pluck('photo_path')
                ->filter()
                ->map(fn ($path) => (string) $path)
                ->all();
            foreach ($invitation->loveStories as $existingStory) {
                if (!empty($existingStory->photo_path) && !in_array($existingStory->photo_path, $retainedStoryPaths, true)) {
                    $this->mediaCleanupService->deleteImagePathIfUnused($existingStory->photo_path, $invitation->id);
                }
            }
            $invitation->loveStories()->delete();
            $loveStoryPhotos = $request->file('love_story_photos', []);
            foreach ($request->input('love_stories', []) as $i => $ls) {
                if (!empty($ls['title'])) {
                    $storyPhotoPath = !empty($ls['photo_path']) ? (string) $ls['photo_path'] : null;
                    if (isset($loveStoryPhotos[$i]) && $loveStoryPhotos[$i]->isValid()) {
                        $this->mediaCleanupService->deleteImagePathIfUnused($storyPhotoPath, $invitation->id);
                        $storyPhotoPath = $this->storeCompressedLoveStoryPhoto($loveStoryPhotos[$i], $i);
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
        $this->authorizeOwner($invitation);
        return $this->activateInvitation($invitation, 'Undangan berhasil dipublikasikan.');
    }

    public function toggleStatus(Invitation $invitation)
    {
        $this->authorizeOwner($invitation);

        if ($invitation->status === 'active' && $invitation->isActive()) {
            $invitation->update([
                'status' => 'draft',
                'published_at' => null,
                'expires_at' => null,
            ]);

            return back()->with('success', 'Undangan berhasil dinonaktifkan.');
        }

        return $this->activateInvitation($invitation, 'Undangan berhasil diaktifkan.');
    }

    private function activateInvitation(Invitation $invitation, string $successMessage)
    {
        $subscription = $invitation->client_package_subscription_id
            ? $this->clientPackageService->findAuthorizedUsableSubscription((int) auth()->id(), (int) $invitation->client_package_subscription_id)
            : null;
        $effectivePackage = $subscription?->package ?: $this->resolveInvitationPackage($invitation);

        if (!$effectivePackage) {
            return redirect()->route('client.packages.select')
                ->with('error', 'Anda harus memiliki paket aktif untuk mempublikasikan undangan.');
        }

        $ownedInvitationCount = $invitation->client_package_subscription_id
            ? Invitation::where('client_package_subscription_id', $invitation->client_package_subscription_id)->count()
            : Invitation::where('user_id', auth()->id())->where('package_id', $invitation->package_id)->count();
        $maxInvitations = $effectivePackage->max_invitations ?? 1;
        if ($ownedInvitationCount > $maxInvitations) {
            return redirect()->route('client.packages.select')
                ->with('error', "Kuota undangan pada paket {$effectivePackage->name} tidak mencukupi. Silakan upgrade paket terlebih dahulu.");
        }

        if (!$effectivePackage->allowsTemplate((int) $invitation->template_id)) {
            return redirect()->route('client.invitations.edit', $invitation)
                ->with('error', "Template undangan ini tidak tersedia pada paket {$effectivePackage->name}. Silakan ubah template atau upgrade paket.");
        }

        if ($invitation->status === 'active' && $invitation->isActive()) {
            return redirect()->route('client.invitations.show', $invitation)
                ->with('success', 'Undangan ini sudah dipublikasikan.');
        }

        $publishedAt = now();
        $expiresAt = $this->calculateInvitationExpiryFromPackage($effectivePackage, $publishedAt);

        $invitation->update([
            'package_id' => $effectivePackage->id,
            'status' => 'active',
            'published_at' => $publishedAt,
            'expires_at' => $expiresAt,
            'admin_notes' => null,
        ]);

        return redirect()->route('client.invitations.show', $invitation)
            ->with('success', $successMessage);
    }

    public function upgradeSuggested(Invitation $invitation)
    {
        $this->authorizeOwner($invitation);

        $invitation->load('package', 'clientPackageSubscription.package', 'template');
        $currentPackage = $this->resolveInvitationPackage($invitation);
        if (!$currentPackage) {
            return back()->with('error', 'Paket akun tidak ditemukan.');
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

        $usedInvitations = Invitation::where('user_id', auth()->id())->count();
        $maxInvitations = $nextPackage->max_invitations ?? 1;
        if ($usedInvitations >= $maxInvitations) {
            return back()->with('error', "Kuota undangan pada paket {$nextPackage->name} sudah penuh.");
        }

        $subscription = $this->clientPackageService->createPendingSubscription(auth()->user(), $nextPackage);

        return redirect()->route('client.packages.checkout.show', $subscription)
            ->with('success', "Upgrade 1 klik berhasil. Lanjutkan pembayaran paket {$nextPackage->name}.");
    }

    public function destroy(Invitation $invitation)
    {
        $this->authorizeOwner($invitation);

        $invitation->delete();

        return redirect()->route('client.invitations.index')
            ->with('success', 'Undangan berhasil dihapus!');
    }

    public function analytics(Invitation $invitation)
    {
        $this->authorizeAnyEditor($invitation);

        $statusCounts = Rsvp::where('invitation_id', $invitation->id)
            ->selectRaw('status, COUNT(*) as total, COALESCE(SUM(pax),0) as pax_total')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $categoryCounts = Rsvp::where('rsvps.invitation_id', $invitation->id)
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
            'funnel' => $this->funnelService->summarize((int) $invitation->id),
            'generated_at' => now()->toDateTimeString(),
        ]);
    }

    public function uploadIgStory(Request $request, Invitation $invitation)
    {
        $this->authorizeAnyEditor($invitation);

        $request->validate([
            'ig_story_photo' => 'required|image|max:10240',
        ], [
            'ig_story_photo.required' => 'Pilih file gambar IG Story terlebih dahulu.',
            'ig_story_photo.image' => 'File harus berupa gambar (jpg, png, webp, dll).',
            'ig_story_photo.max' => 'Ukuran gambar maksimal 10MB.',
        ]);

        $previousIgStoryPhoto = $invitation->ig_story_photo;

        try {
            $path = $this->imageCompressionService->compressAndStore(
                $request->file('ig_story_photo'),
                'invitations/ig-stories'
            );
        } catch (\Throwable $e) {
            report($e);
            $detail = app()->environment('local') ? (' Detail: ' . $e->getMessage()) : '';
            return back()->withErrors(['ig_story_photo' => 'Gagal memproses gambar IG Story.' . $detail]);
        }

        $invitation->update(['ig_story_photo' => $path]);
        if (!empty($previousIgStoryPhoto) && $previousIgStoryPhoto !== $path) {
            $this->mediaCleanupService->deleteImagePathIfUnused($previousIgStoryPhoto, $invitation->id);
        }

        return back()->with('success', 'Template IG Story berhasil diupload!');
    }

    public function destroyIgStory(Invitation $invitation)
    {
        $this->authorizeAnyEditor($invitation);

        if ($invitation->ig_story_photo) {
            $previousIgStoryPhoto = $invitation->ig_story_photo;
            $invitation->update(['ig_story_photo' => null]);
            $this->mediaCleanupService->deleteImagePathIfUnused($previousIgStoryPhoto, $invitation->id);
        }

        return back()->with('success', 'Template IG Story berhasil dihapus!');
    }

    public function updateLoveStories(Request $request, Invitation $invitation)
    {
        $this->authorizeAnyEditor($invitation);

        if ($mimeErrors = $this->validateUploadMimeSniff($request)) {
            return back()->withErrors($mimeErrors);
        }

        $validated = $request->validate([
            'love_story_photos.*' => 'nullable|image|max:10240',
            'love_stories' => 'nullable|array',
            'love_stories.*.year' => 'nullable|string|max:50',
            'love_stories.*.title' => 'nullable|string|max:255',
            'love_stories.*.description' => 'nullable|string|max:1000',
            'love_stories.*.photo_path' => 'nullable|string|max:255',
        ], [
            'love_story_photos.*.max' => 'Ukuran foto love story maksimal 10MB.',
        ]);

        $invitation->load('loveStories');
        $inputStories = $validated['love_stories'] ?? [];
        $retainedStoryPaths = collect($inputStories)
            ->pluck('photo_path')
            ->filter()
            ->map(fn ($path) => (string) $path)
            ->all();

        foreach ($invitation->loveStories as $existingStory) {
            if (!empty($existingStory->photo_path) && !in_array($existingStory->photo_path, $retainedStoryPaths, true)) {
                $this->mediaCleanupService->deleteImagePathIfUnused($existingStory->photo_path, $invitation->id);
            }
        }

        $invitation->loveStories()->delete();
        $loveStoryPhotos = $request->file('love_story_photos', []);

        foreach ($inputStories as $i => $story) {
            if (empty($story['title'])) {
                continue;
            }

            $storyPhotoPath = !empty($story['photo_path']) ? (string) $story['photo_path'] : null;
            if (isset($loveStoryPhotos[$i]) && $loveStoryPhotos[$i]->isValid()) {
                $this->mediaCleanupService->deleteImagePathIfUnused($storyPhotoPath, $invitation->id);
                $storyPhotoPath = $this->storeCompressedLoveStoryPhoto($loveStoryPhotos[$i], $i);
            }

            LoveStory::create([
                'invitation_id' => $invitation->id,
                'year' => $story['year'] ?? null,
                'title' => $story['title'],
                'description' => $story['description'] ?? null,
                'photo_path' => $storyPhotoPath,
                'sort_order' => $i,
            ]);
        }

        return back()->with('success', 'Love story berhasil diperbarui!');
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

    private function resolveInvitationPackage(Invitation $invitation): ?Package
    {
        if ($invitation->relationLoaded('clientPackageSubscription') && $invitation->clientPackageSubscription?->package) {
            return $invitation->clientPackageSubscription->package;
        }

        if ($invitation->client_package_subscription_id) {
            $subscription = $invitation->clientPackageSubscription()->with('package')->first();
            if ($subscription?->package) {
                return $subscription->package;
            }
        }

        return $invitation->package;
    }

    private function resolveInvitationQuotaUsage(Invitation $invitation): int
    {
        if ($invitation->client_package_subscription_id) {
            return Invitation::where('client_package_subscription_id', $invitation->client_package_subscription_id)->count();
        }

        return Invitation::where('user_id', $invitation->user_id)
            ->where('package_id', $invitation->package_id)
            ->count();
    }

    private function calculateInvitationExpiryFromPackage(?Package $package, \Carbon\CarbonInterface $publishedAt): ?\Carbon\CarbonInterface
    {
        if (!$package || empty($package->active_duration_value) || empty($package->active_duration_unit)) {
            return null;
        }

        $duration = (int) $package->active_duration_value;
        if ($duration <= 0) {
            return null;
        }

        return $package->active_duration_unit === 'month'
            ? $publishedAt->copy()->addMonthsNoOverflow($duration)
            : $publishedAt->copy()->addDays($duration);
    }

    private function authorizeAnyEditor(Invitation $invitation): void
    {
        if (!$this->invitationAccessService->isOwnerOrEditor($invitation, (int) auth()->id())) {
            abort(403);
        }
    }

    private function authorizeOwner(Invitation $invitation): void
    {
        if (!$this->invitationAccessService->isOwner($invitation, (int) auth()->id())) {
            abort(403);
        }
    }

    private function storeCompressedImageIfPresent(Request $request, array &$validated, string $field, string $directory): void
    {
        if (!$request->hasFile($field)) {
            return;
        }

        try {
            $validated[$field] = $this->imageCompressionService->compressAndStore(
                $request->file($field),
                $directory
            );
        } catch (\Throwable $e) {
            report($e);
            $detail = app()->environment('local') ? (' Detail: ' . $e->getMessage()) : '';
            throw ValidationException::withMessages([
                $field => 'Gagal memproses gambar. Coba upload gambar lain.' . $detail,
            ]);
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

    private function validateUploadMimeSniff(Request $request): array
    {
        $errors = [];

        $imageMimes = ['image/jpeg', 'image/jpg', 'image/pjpeg', 'image/png', 'image/webp', 'image/gif'];
        $audioMimes = ['audio/mpeg', 'audio/mp3', 'audio/ogg', 'audio/wav', 'audio/x-wav', 'audio/mp4', 'audio/x-m4a', 'audio/aac', 'audio/aacp'];
        $audioExts = ['mp3', 'ogg', 'wav', 'm4a', 'aac'];
        $imageExts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

        $imageFields = ['cover_photo', 'groom_photo', 'bride_photo'];
        foreach ($imageFields as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                if (
                    $file instanceof UploadedFile
                    && !$this->isAllowedMimeByContent($file, $imageMimes)
                    && !$this->isAllowedByExtensionFallback($file, $imageExts)
                ) {
                    $errors[$field] = 'File gambar terdeteksi tidak valid (MIME mismatch).';
                }
            }
        }

        if ($request->hasFile('love_story_photos')) {
            foreach ((array) $request->file('love_story_photos') as $idx => $file) {
                if (
                    $file instanceof UploadedFile
                    && !$this->isAllowedMimeByContent($file, $imageMimes)
                    && !$this->isAllowedByExtensionFallback($file, $imageExts)
                ) {
                    $errors["love_story_photos.{$idx}"] = 'Foto love story tidak valid (MIME mismatch).';
                }
            }
        }

        if ($request->hasFile('music_url')) {
            $file = $request->file('music_url');
            if (
                $file instanceof UploadedFile
                && !$this->isAllowedMimeByContent($file, $audioMimes)
                && !$this->isAllowedByExtensionFallback($file, $audioExts)
            ) {
                $errors['music_url'] = 'File musik terdeteksi tidak valid atau ekstensi tidak sesuai isi file.';
            }
        }

        return $errors;
    }

    private function isAllowedMimeByContent(UploadedFile $file, array $allowedMimes): bool
    {
        $detectedMime = $this->detectMimeType($file);
        if ($detectedMime === null) {
            return false;
        }

        $detectedMime = strtolower($detectedMime);
        $allowed = array_map('strtolower', $allowedMimes);
        return in_array($detectedMime, $allowed, true);
    }

    private function isAllowedByExtensionFallback(UploadedFile $file, array $allowedExtensions): bool
    {
        $detectedMime = strtolower($this->detectMimeType($file) ?? '');
        $fallbackMimes = ['application/octet-stream', 'video/mp4'];
        if (!in_array($detectedMime, $fallbackMimes, true)) {
            return false;
        }

        $ext = strtolower((string) $file->getClientOriginalExtension());
        return in_array($ext, array_map('strtolower', $allowedExtensions), true);
    }

    private function detectMimeType(UploadedFile $file): ?string
    {
        $path = $file->getPathname();
        if (!is_file($path)) {
            return null;
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $detected = (string) $finfo->file($path);
        return $detected !== '' ? $detected : null;
    }

    private function storeCompressedLoveStoryPhoto(UploadedFile $file, int $index): string
    {
        try {
            return $this->imageCompressionService->compressAndStore(
                $file,
                'invitations/love-stories'
            );
        } catch (\Throwable $e) {
            report($e);
            $detail = app()->environment('local') ? (' Detail: ' . $e->getMessage()) : '';
            throw ValidationException::withMessages([
                "love_story_photos.{$index}" => 'Gagal memproses foto love story. Coba upload gambar lain.' . $detail,
            ]);
        }
    }

}
