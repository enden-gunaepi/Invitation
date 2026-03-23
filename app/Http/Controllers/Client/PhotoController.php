<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\InvitationPhoto;
use App\Services\ImageCompressionService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PhotoController extends Controller
{
    public function __construct(private readonly ImageCompressionService $imageCompressionService)
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

        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:10240',
            'caption' => 'nullable|string|max:200',
        ]);

        try {
            $path = $this->imageCompressionService->compressAndStore(
                $request->file('photo'),
                'invitations/photos'
            );
        } catch (\Throwable $e) {
            throw ValidationException::withMessages([
                'photo' => 'Gagal memproses gambar. Coba upload gambar lain.',
            ]);
        }

        InvitationPhoto::create([
            'invitation_id' => $invitation->id,
            'file_path' => $path,
            'caption' => $request->caption,
            'sort_order' => $currentPhotos + 1,
        ]);

        $remaining = $maxPhotos - $currentPhotos - 1;

        return redirect()->back()
            ->with('success', "Foto berhasil diupload! (Sisa: {$remaining} foto)");
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
        if ($invitation->user_id !== auth()->id()) {
            abort(403);
        }
    }
}
