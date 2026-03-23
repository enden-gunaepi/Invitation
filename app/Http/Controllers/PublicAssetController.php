<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class PublicAssetController extends Controller
{
    public function music(Invitation $invitation): Response
    {
        abort_unless($invitation->status === 'active', 404);
        abort_unless(!empty($invitation->music_url), 404);
        abort_unless(Storage::disk('public')->exists($invitation->music_url), 404);

        return Storage::disk('public')->response($invitation->music_url, null, [
            'Cache-Control' => 'public, max-age=300',
        ]);
    }
}
