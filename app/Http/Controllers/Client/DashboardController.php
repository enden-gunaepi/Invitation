<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use App\Models\Invitation;
use App\Models\Package;
use App\Models\Rsvp;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $cachePrefix = "client:dashboard:user:{$user->id}";

        $stats = Cache::remember("{$cachePrefix}:stats:v1", now()->addSeconds(45), function () use ($user) {
            return [
                'total_invitations' => Invitation::where('user_id', $user->id)->count(),
                'active_invitations' => Invitation::where('user_id', $user->id)->where('status', 'active')->count(),
                'total_guests' => Guest::whereHas('invitation', fn ($q) => $q->where('user_id', $user->id))->count(),
                'total_rsvps' => Rsvp::whereHas('invitation', fn($q) => $q->where('user_id', $user->id))->count(),
                'attending' => Rsvp::whereHas('invitation', fn($q) => $q->where('user_id', $user->id))
                    ->where('status', 'attending')->count(),
                'total_views' => Invitation::where('user_id', $user->id)->sum('view_count'),
            ];
        });

        $invitations = Cache::remember("{$cachePrefix}:invitations:v1", now()->addSeconds(30), function () use ($user) {
            return Invitation::query()
                ->where('user_id', $user->id)
                ->with(['template:id,name', 'package:id,name'])
                ->withCount(['guests', 'photos'])
                ->select(['id', 'user_id', 'template_id', 'package_id', 'title', 'status', 'event_date', 'view_count', 'created_at'])
                ->latest()
                ->take(5)
                ->get();
        });

        $latestInvitation = Cache::remember("{$cachePrefix}:latest_invitation:v1", now()->addSeconds(30), function () use ($user) {
            return Invitation::query()
                ->where('user_id', $user->id)
                ->with('package:id,name,price,max_guests,max_photos,max_invitations')
                ->withCount(['guests', 'photos'])
                ->select(['id', 'user_id', 'package_id', 'title', 'venue_name', 'status', 'created_at'])
                ->latest()
                ->first();
        });

        $onboarding = $this->buildOnboardingData($latestInvitation);
        $upsell = $this->buildUpsellData($latestInvitation, $user->id);

        return view('client.dashboard.index', compact('stats', 'invitations', 'onboarding', 'upsell'));
    }

    private function buildOnboardingData(?Invitation $invitation): array
    {
        if (!$invitation) {
            return [
                'progress' => 0,
                'items' => [
                    ['label' => 'Buat undangan pertama', 'done' => false],
                    ['label' => 'Lengkapi data acara', 'done' => false],
                    ['label' => 'Tambah daftar tamu', 'done' => false],
                    ['label' => 'Aktifkan undangan', 'done' => false],
                ],
                'next_label' => 'Mulai buat undangan',
                'next_url' => route('client.invitations.create'),
            ];
        }

        $items = [
            ['label' => 'Undangan dibuat', 'done' => true],
            ['label' => 'Data acara lengkap', 'done' => !empty($invitation->title) && !empty($invitation->venue_name)],
            ['label' => 'Tambah foto / galeri', 'done' => ($invitation->photos_count ?? 0) > 0],
            ['label' => 'Tambah daftar tamu', 'done' => ($invitation->guests_count ?? 0) > 0],
            ['label' => 'Undangan aktif', 'done' => $invitation->status === 'active'],
        ];

        $done = collect($items)->where('done', true)->count();
        $progress = (int) round(($done / max(1, count($items))) * 100);

        $nextAction = collect($items)->firstWhere('done', false);
        $nextUrl = $invitation->status === 'active'
            ? route('client.invitations.show', $invitation)
            : route('client.invitations.edit', $invitation);

        return [
            'progress' => $progress,
            'items' => $items,
            'next_label' => $nextAction ? ('Lanjut: ' . $nextAction['label']) : 'Lihat performa undangan',
            'next_url' => $nextUrl,
        ];
    }

    private function buildUpsellData(?Invitation $invitation, int $userId): ?array
    {
        if (!$invitation || !$invitation->package) {
            return null;
        }

        $package = $invitation->package;
        $guestMax = max(1, (int) ($package->max_guests ?? 1));
        $photoMax = max(1, (int) ($package->max_photos ?? 1));
        $invMax = max(1, (int) ($package->max_invitations ?? 1));
        $invUsed = Invitation::where('user_id', $userId)->where('package_id', $package->id)->count();

        $guestP = (int) round((($invitation->guests_count ?? 0) / $guestMax) * 100);
        $photoP = (int) round((($invitation->photos_count ?? 0) / $photoMax) * 100);
        $invP = (int) round(($invUsed / $invMax) * 100);

        $reasons = [];
        if ($guestP >= 80) {
            $reasons[] = "Kuota tamu hampir penuh ({$guestP}%).";
        }
        if ($photoP >= 80) {
            $reasons[] = "Kuota foto hampir penuh ({$photoP}%).";
        }
        if ($invP >= 80) {
            $reasons[] = "Kuota jumlah undangan hampir penuh ({$invP}%).";
        }

        if (empty($reasons)) {
            return null;
        }

        $nextPackage = Package::where('is_active', true)
            ->where('price', '>', (float) $package->price)
            ->orderBy('price')
            ->first();

        if (!$nextPackage) {
            return null;
        }

        return [
            'reasons' => $reasons,
            'next_package_name' => $nextPackage->name,
            'next_package_price' => $nextPackage->price,
            'invitation_id' => $invitation->id,
        ];
    }
}
