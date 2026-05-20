<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\InvitationPhoto;
use App\Services\InvitationAccessService;
use App\Services\ImageCompressionService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PhotoController extends Controller
{
    public function __construct(
        private readonly ImageCompressionService $imageCompressionService,
        private readonly InvitationAccessService $invitationAccessService,
    )
    {
    }

    public function store(Request $request, Invitation $invitation)
    {
        $this->authorize($invitation);

        // Enforce package photo limit
        $invitation->load('package');
        $maxPhotos = $invitation->package->max_photos ?? 10;
        $currentPhotos = $invitation->photos()->count();

        if ($currentPhotos >= $maxPhotos) {
            return redirect()->back()
                ->with('error', "Batas foto untuk paket {$invitation->package->name} adalah {$maxPhotos} foto. Upgrade paket untuk menambah foto.");
        }

        // Validate multiple files if 'photos' array is present, otherwise fallback to single 'photo'
        if ($request->hasFile('photos')) {
            $request->validate([
                'photos' => 'required|array',
                'photos.*' => 'image|mimes:jpg,jpeg,png,webp|max:10240',
                'caption' => 'nullable|string|max:200',
            ]);
            $files = $request->file('photos');
        } else {
            $request->validate([
                'photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:10240',
                'caption' => 'nullable|string|max:200',
            ]);
            $files = [$request->file('photo')];
        }

        $uploadedCount = 0;
        $skippedCount = 0;

        foreach ($files as $file) {
            if ($currentPhotos >= $maxPhotos) {
                $skippedCount++;
                continue;
            }

            try {
                $path = $this->imageCompressionService->compressAndStore(
                    $file,
                    'invitations/photos'
                );

                InvitationPhoto::create([
                    'invitation_id' => $invitation->id,
                    'file_path' => $path,
                    'caption' => $request->caption,
                    'sort_order' => $currentPhotos + 1,
                ]);

                $currentPhotos++;
                $uploadedCount++;
            } catch (\Throwable $e) {
                if (count($files) === 1) {
                    throw ValidationException::withMessages([
                        'photo' => 'Gagal memproses gambar. Coba upload gambar lain.',
                    ]);
                }
            }
        }

        $remaining = $maxPhotos - $currentPhotos;

        if ($uploadedCount > 0) {
            $msg = "{$uploadedCount} foto berhasil diupload!";
            if ($skippedCount > 0) {
                $msg .= " ({$skippedCount} foto dilewati karena batas paket tercapai).";
            }
            $msg .= " (Sisa: {$remaining} foto)";
            return redirect()->back()->with('success', $msg);
        }

        return redirect()->back()->with('error', 'Tidak ada foto yang berhasil diupload.');
    }

    public function destroy(Invitation $invitation, InvitationPhoto $photo)
    {
        $this->authorize($invitation);

        if ($photo->invitation_id !== $invitation->id) {
            abort(403);
        }

        // Delete file from storage
        $filePath = storage_path('app/public/' . $photo->file_path);
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $photo->delete();

        return redirect()->back()
            ->with('success', 'Foto berhasil dihapus!');
    }

    private function authorize(Invitation $invitation)
    {
        if (!$this->invitationAccessService->isOwnerOrEditor($invitation, (int) auth()->id())) {
            abort(403);
        }
    }
}
