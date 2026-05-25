<?php

namespace App\Services;

use App\Models\Invitation;
use App\Models\InvitationPhoto;
use App\Models\LoveStory;
use App\Models\MusicTrack;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class InvitationMediaCleanupService
{
    public const IMAGE_DIRECTORIES = [
        'invitations/covers',
        'invitations/couples',
        'invitations/photos',
        'invitations/love-stories',
        'invitations/ig-stories',
    ];

    public const MUSIC_DIRECTORIES = [
        'invitations/music',
    ];

    public function deleteImagePathIfUnused(?string $path, ?int $exceptInvitationId = null): void
    {
        if (empty($path)) {
            return;
        }

        $invitationQuery = Invitation::query();
        if ($exceptInvitationId !== null) {
            $invitationQuery->where('id', '!=', $exceptInvitationId);
        }

        $isUsedByOtherInvitation = $invitationQuery
            ->where(function ($q) use ($path) {
                $q->where('cover_photo', $path)
                    ->orWhere('ig_story_photo', $path)
                    ->orWhere('groom_photo', $path)
                    ->orWhere('bride_photo', $path);
            })
            ->exists();

        $galleryQuery = InvitationPhoto::query()->where('file_path', $path);
        if ($exceptInvitationId !== null) {
            $galleryQuery->where('invitation_id', '!=', $exceptInvitationId);
        }
        $isUsedByOtherGallery = $galleryQuery->exists();

        $storyQuery = LoveStory::query()->where('photo_path', $path);
        if ($exceptInvitationId !== null) {
            $storyQuery->where('invitation_id', '!=', $exceptInvitationId);
        }
        $isUsedByOtherLoveStory = $storyQuery->exists();

        if ($isUsedByOtherInvitation || $isUsedByOtherGallery || $isUsedByOtherLoveStory) {
            return;
        }

        Storage::disk('public')->delete(ltrim($path, '/'));
    }

    public function deleteMusicPathIfUnused(?string $path, ?int $exceptInvitationId = null): void
    {
        if (empty($path)) {
            return;
        }

        $isSharedMusicTrack = MusicTrack::query()
            ->where('file_path', $path)
            ->exists();

        $invitationQuery = Invitation::query()->where('music_url', $path);
        if ($exceptInvitationId !== null) {
            $invitationQuery->where('id', '!=', $exceptInvitationId);
        }
        $isUsedByOtherInvitation = $invitationQuery->exists();

        if ($isSharedMusicTrack || $isUsedByOtherInvitation) {
            return;
        }

        Storage::disk('public')->delete(ltrim($path, '/'));
    }

    public function referencedImagePaths(): Collection
    {
        return collect()
            ->merge(Invitation::query()->pluck('cover_photo'))
            ->merge(Invitation::query()->pluck('ig_story_photo'))
            ->merge(Invitation::query()->pluck('groom_photo'))
            ->merge(Invitation::query()->pluck('bride_photo'))
            ->merge(InvitationPhoto::query()->pluck('file_path'))
            ->merge(LoveStory::query()->pluck('photo_path'))
            ->filter()
            ->map(fn ($path) => ltrim((string) $path, '/'))
            ->unique()
            ->values();
    }

    public function referencedMusicPaths(): Collection
    {
        return collect()
            ->merge(Invitation::query()->pluck('music_url'))
            ->merge(MusicTrack::query()->pluck('file_path'))
            ->filter()
            ->map(fn ($path) => ltrim((string) $path, '/'))
            ->unique()
            ->values();
    }

    public function inspectOrphanMedia(): array
    {
        $disk = Storage::disk('public');
        $referencedImages = $this->referencedImagePaths()->flip();
        $referencedMusic = $this->referencedMusicPaths()->flip();

        $imageFiles = $this->collectOrphanFiles($disk, self::IMAGE_DIRECTORIES, $referencedImages, 'image');
        $musicFiles = $this->collectOrphanFiles($disk, self::MUSIC_DIRECTORIES, $referencedMusic, 'music');
        $files = $imageFiles->concat($musicFiles)->sortBy('path')->values();

        return [
            'files' => $files,
            'totals' => [
                'count' => $files->count(),
                'bytes' => (int) $files->sum('size'),
                'images' => $imageFiles->count(),
                'music' => $musicFiles->count(),
            ],
        ];
    }

    public function cleanupOrphanMedia(): array
    {
        $disk = Storage::disk('public');
        $inspection = $this->inspectOrphanMedia();
        $deleted = 0;
        $deletedBytes = 0;

        foreach ($inspection['files'] as $file) {
            if ($disk->delete($file['path'])) {
                $deleted++;
                $deletedBytes += (int) $file['size'];
            }
        }

        return [
            'deleted' => $deleted,
            'bytes' => $deletedBytes,
        ];
    }

    private function collectOrphanFiles($disk, array $directories, Collection $referencedPaths, string $type): Collection
    {
        $files = collect();

        foreach ($directories as $directory) {
            foreach ($disk->files($directory) as $file) {
                $normalizedPath = ltrim($file, '/');
                if ($referencedPaths->has($normalizedPath)) {
                    continue;
                }

                $files->push([
                    'path' => $normalizedPath,
                    'size' => (int) ($disk->size($normalizedPath) ?: 0),
                    'type' => $type,
                ]);
            }
        }

        return $files;
    }
}
