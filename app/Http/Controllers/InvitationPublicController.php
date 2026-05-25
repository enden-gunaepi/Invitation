<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Invitation;
use App\Models\Rsvp;
use App\Models\Wish;
use App\Services\GuestPersonalizationService;
use App\Services\InvitationFunnelService;
use App\Services\PhoneNormalizerService;
use App\Services\TemplateRenderService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class InvitationPublicController extends Controller
{
    public function __construct(
        private readonly PhoneNormalizerService $phoneNormalizer,
        private readonly GuestPersonalizationService $guestPersonalization,
        private readonly InvitationFunnelService $funnelService,
        private readonly TemplateRenderService $templateRenderService,
    ) {
    }

    public function show($slug)
    {
        $invitation = $this->preparePublicInvitation($this->getCachedPublicInvitation($slug));
        $personalization = $this->guestPersonalization->forCategory(null);

        return view(
            $this->templateRenderService->resolveView($invitation->template),
            array_merge(
                compact('invitation', 'personalization'),
                $this->templateRenderService->resolveData($invitation->template)
            )
        );
    }

    public function showGuest($slug, $token)
    {
        $invitation = $this->preparePublicInvitation($this->getCachedPublicInvitation($slug));

        $guest = Guest::where('token', $token)
            ->where('invitation_id', $invitation->id)
            ->first();

        $personalization = $this->guestPersonalization->forCategory($guest?->category);

        return view(
            $this->templateRenderService->resolveView($invitation->template),
            array_merge(
                compact('invitation', 'guest', 'personalization'),
                $this->templateRenderService->resolveData($invitation->template)
            )
        );
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

    public function downloadIgStory(string $slug): Response
    {
        $invitation = Invitation::query()
            ->where('slug', $slug)
            ->active()
            ->select(['id', 'slug', 'title', 'bride_name', 'groom_name', 'ig_story_photo', 'event_date'])
            ->firstOrFail();

        abort_if(empty($invitation->ig_story_photo), 404);
        abort_unless(function_exists('imagecreatefromstring') && function_exists('imagejpeg'), 500, 'GD JPEG support is required.');

        $disk = Storage::disk('public');
        abort_unless($disk->exists($invitation->ig_story_photo), 404);

        $binary = $disk->get($invitation->ig_story_photo);
        $source = @imagecreatefromstring($binary);
        abort_unless($source instanceof \GdImage, 422, 'Template story tidak dapat diproses.');

        $width = imagesx($source);
        $height = imagesy($source);
        $canvas = imagecreatetruecolor($width, $height);

        $white = imagecolorallocate($canvas, 255, 255, 255);
        imagefill($canvas, 0, 0, $white);
        imagecopy($canvas, $source, 0, 0, 0, 0, $width, $height);

        $this->renderIgStoryOverlay($canvas, $invitation, $width, $height);

        ob_start();
        imageinterlace($canvas, true);
        imagejpeg($canvas, null, 94);
        $jpgBinary = (string) ob_get_clean();

        imagedestroy($source);
        imagedestroy($canvas);

        $baseName = trim(
            collect([$invitation->bride_name, $invitation->groom_name])
                ->filter()
                ->implode(' & ')
        );
        $baseName = $baseName !== '' ? $baseName : $invitation->title;
        $safeName = str($baseName)->slug('-')->toString() ?: 'instagram-story';
        $fileName = $safeName . '-story-hd.jpg';

        return response($jpgBinary, 200, [
            'Content-Type' => 'image/jpeg',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Content-Length' => (string) strlen($jpgBinary),
            'Cache-Control' => 'public, max-age=300',
        ]);
    }

    private function preparePublicInvitation(Invitation $invitation): Invitation
    {
        if (!empty($invitation->music_url)) {
            $invitation->music_signed_url = app()->environment('local')
                ? Storage::url($invitation->music_url)
                : URL::temporarySignedRoute(
                    'media.music',
                    now()->addHours(12),
                    ['invitation' => $invitation->id]
                );
        }

        return $invitation;
    }

    private function renderIgStoryOverlay(\GdImage $canvas, Invitation $invitation, int $width, int $height): void
    {
        imagealphablending($canvas, true);
        imagesavealpha($canvas, true);

        $name = trim(
            collect([$invitation->bride_name, $invitation->groom_name])
                ->filter()
                ->implode(' & ')
        );
        $name = $name !== '' ? $name : $invitation->title;
        $eventDate = $invitation->event_date
            ? \Carbon\Carbon::parse($invitation->event_date)->format('d  .  m  .  Y')
            : now()->format('d  .  m  .  Y');

        $white = imagecolorallocate($canvas, 255, 255, 255);
        $softWhite = imagecolorallocatealpha($canvas, 255, 255, 255, 22);
        $mutedWhite = imagecolorallocatealpha($canvas, 255, 255, 255, 38);
        $goldGlow = imagecolorallocatealpha($canvas, 198, 167, 106, 96);
        $deepOverlay = imagecolorallocatealpha($canvas, 11, 18, 28, 82);
        $cardFill = imagecolorallocatealpha($canvas, 247, 243, 235, 24);
        $cardBorder = imagecolorallocatealpha($canvas, 255, 255, 255, 42);

        imagefilledrectangle($canvas, 0, (int) ($height * 0.62), $width, $height, $deepOverlay);
        imagefilledellipse($canvas, (int) ($width / 2), (int) ($height * 0.78), (int) ($width * 0.8), (int) ($height * 0.34), $goldGlow);

        $boxX = (int) round($width * 0.055);
        $boxY = (int) round($height * 0.73);
        $boxW = $width - ($boxX * 2);
        $boxH = (int) round($height * 0.19);
        $this->drawRoundedRectangle($canvas, $boxX, $boxY, $boxW, $boxH, 24, $cardFill, $cardBorder);

        $scriptFont = $this->pickFont([
            'C:\\Windows\\Fonts\\segoesc.ttf',
            'C:\\Windows\\Fonts\\Gabriola.ttf',
            'C:\\Windows\\Fonts\\georgiai.ttf',
        ]);
        $bodyFont = $this->pickFont([
            'C:\\Windows\\Fonts\\arial.ttf',
            'C:\\Windows\\Fonts\\georgia.ttf',
        ]);

        $centerX = (int) ($width / 2);
        $nameY = (int) round($height * 0.665);
        $dateY = (int) round($height * 0.706);
        $wishY = (int) round($height * 0.736);
        $footerY = $height - 34;

        $this->drawCenteredText($canvas, $name, $centerX, $nameY, 34, $white, $scriptFont, 5);
        $this->drawCenteredText($canvas, $eventDate, $centerX, $dateY, 14, $softWhite, $bodyFont, 3);
        $this->drawCenteredText($canvas, 'Wish', $centerX, $wishY, 16, $softWhite, $bodyFont, 4);

        $this->drawFooterLink($canvas, $width, $footerY, 'janjisucikita.com', $bodyFont, $mutedWhite, $softWhite);
    }

    private function drawRoundedRectangle(\GdImage $image, int $x, int $y, int $width, int $height, int $radius, int $fillColor, int $borderColor): void
    {
        imagefilledrectangle($image, $x + $radius, $y, $x + $width - $radius, $y + $height, $fillColor);
        imagefilledrectangle($image, $x, $y + $radius, $x + $width, $y + $height - $radius, $fillColor);
        imagefilledellipse($image, $x + $radius, $y + $radius, $radius * 2, $radius * 2, $fillColor);
        imagefilledellipse($image, $x + $width - $radius, $y + $radius, $radius * 2, $radius * 2, $fillColor);
        imagefilledellipse($image, $x + $radius, $y + $height - $radius, $radius * 2, $radius * 2, $fillColor);
        imagefilledellipse($image, $x + $width - $radius, $y + $height - $radius, $radius * 2, $radius * 2, $fillColor);

        imageline($image, $x + $radius, $y, $x + $width - $radius, $y, $borderColor);
        imageline($image, $x + $radius, $y + $height, $x + $width - $radius, $y + $height, $borderColor);
        imageline($image, $x, $y + $radius, $x, $y + $height - $radius, $borderColor);
        imageline($image, $x + $width, $y + $radius, $x + $width, $y + $height - $radius, $borderColor);
        imagearc($image, $x + $radius, $y + $radius, $radius * 2, $radius * 2, 180, 270, $borderColor);
        imagearc($image, $x + $width - $radius, $y + $radius, $radius * 2, $radius * 2, 270, 360, $borderColor);
        imagearc($image, $x + $radius, $y + $height - $radius, $radius * 2, $radius * 2, 90, 180, $borderColor);
        imagearc($image, $x + $width - $radius, $y + $height - $radius, $radius * 2, $radius * 2, 0, 90, $borderColor);
    }

    private function drawCenteredText(\GdImage $image, string $text, int $centerX, int $baselineY, int $size, int $color, ?string $fontPath, int $fallbackFont = 3): void
    {
        if ($fontPath && function_exists('imagettfbbox') && function_exists('imagettftext')) {
            $box = imagettfbbox($size, 0, $fontPath, $text);
            if (is_array($box)) {
                $textWidth = (int) abs($box[2] - $box[0]);
                $x = (int) round($centerX - ($textWidth / 2));
                imagettftext($image, $size, 0, $x, $baselineY, $color, $fontPath, $text);
                return;
            }
        }

        $fontWidth = imagefontwidth($fallbackFont);
        $x = (int) round($centerX - ((strlen($text) * $fontWidth) / 2));
        imagestring($image, $fallbackFont, $x, max(0, $baselineY - imagefontheight($fallbackFont)), $text, $color);
    }

    private function drawFooterLink(\GdImage $image, int $width, int $baselineY, string $label, ?string $fontPath, int $textColor, int $iconColor): void
    {
        $fontSize = 14;
        $iconX = (int) round($width * 0.08);
        $iconY = $baselineY - 11;
        $iconSize = 16;

        imageellipse($image, $iconX, $iconY, $iconSize, $iconSize, $iconColor);
        imageline($image, $iconX - 6, $iconY, $iconX + 6, $iconY, $iconColor);
        imageline($image, $iconX, $iconY - 6, $iconX, $iconY + 6, $iconColor);
        imagearc($image, $iconX, $iconY, 8, 16, 90, 270, $iconColor);
        imagearc($image, $iconX, $iconY, 8, 16, 270, 90, $iconColor);
        imagearc($image, $iconX, $iconY, 16, 8, 0, 180, $iconColor);
        imagearc($image, $iconX, $iconY, 16, 8, 180, 360, $iconColor);

        $textX = $iconX + 16;
        if ($fontPath && function_exists('imagettftext')) {
            imagettftext($image, $fontSize, 0, $textX, $baselineY, $textColor, $fontPath, $label);
            return;
        }

        imagestring($image, 4, $textX, max(0, $baselineY - imagefontheight(4)), $label, $textColor);
    }

    private function pickFont(array $paths): ?string
    {
        foreach ($paths as $path) {
            if (is_file($path)) {
                return $path;
            }
        }

        return null;
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
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor WhatsApp wajib diisi jika tidak menggunakan link tamu personal.',
                    'errors' => ['phone' => ['Nomor WhatsApp wajib diisi.']]
                ], 422);
            }
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

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Terima kasih, RSVP Anda berhasil disimpan.',
                'rsvp' => Rsvp::where('invitation_id', $invitation->id)->latest()->first() // Optional: return the latest RSVP
            ]);
        }

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

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Ucapan Anda berhasil dikirim!'
            ]);
        }

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
                    'template:id,name,html_path,render_mode,builder_config,builder_layout,category,schema_version',
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
