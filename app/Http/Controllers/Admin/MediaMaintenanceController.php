<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\InvitationMediaCleanupService;

class MediaMaintenanceController extends Controller
{
    public function index(InvitationMediaCleanupService $cleanupService)
    {
        $inspection = $cleanupService->inspectOrphanMedia();

        return view('admin.system.media-maintenance', [
            'orphanFiles' => $inspection['files']->take(100),
            'totals' => $inspection['totals'],
        ]);
    }

    public function cleanup(InvitationMediaCleanupService $cleanupService)
    {
        $inspection = $cleanupService->inspectOrphanMedia();
        $result = $cleanupService->cleanupOrphanMedia();

        return redirect()
            ->route('admin.system.media-maintenance')
            ->with('success', "Cleanup selesai. {$result['deleted']} file orphan dihapus dari {$inspection['totals']['count']} kandidat, total " . number_format($result['bytes'] / 1048576, 2) . ' MB dibebaskan.');
    }
}
