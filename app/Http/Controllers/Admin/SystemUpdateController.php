<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\SystemUpdateService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Throwable;

class SystemUpdateController extends Controller
{
    public function __construct(private readonly SystemUpdateService $systemUpdate)
    {
    }

    public function index()
    {
        try {
            $repository = $this->systemUpdate->status();
            $error = null;
        } catch (Throwable $exception) {
            report($exception);
            $repository = null;
            $error = $exception->getMessage();
        }

        $heartbeat = Setting::get('system_scheduler_heartbeat');
        $heartbeatAt = $heartbeat ? Carbon::parse($heartbeat) : null;

        return view('admin.system.update', [
            'repository' => $repository,
            'repositoryError' => $error,
            'autoUpdate' => Setting::get('system_auto_update', '0') === '1',
            'lastCheckedAt' => Setting::get('system_update_last_checked_at'),
            'lastUpdatedAt' => Setting::get('system_update_last_updated_at'),
            'lastUpdateMessage' => Setting::get('system_update_last_message'),
            'schedulerHeartbeatAt' => $heartbeatAt,
            'schedulerHealthy' => $heartbeatAt?->gte(now()->subMinutes(15)) ?? false,
        ]);
    }

    public function check(): RedirectResponse
    {
        try {
            $status = $this->systemUpdate->status(true);
            Setting::set('system_update_last_checked_at', now()->toDateTimeString(), 'system');
            $message = $status['behind'] > 0
                ? "Tersedia {$status['behind']} commit baru dari GitHub."
                : 'Pengecekan selesai. Repository sudah terbaru.';

            return back()->with('success', $message);
        } catch (Throwable $exception) {
            report($exception);
            return back()->with('error', 'Gagal mengecek GitHub: '.$exception->getMessage());
        }
    }

    public function pull(): RedirectResponse
    {
        try {
            $result = $this->systemUpdate->update();
            $message = $result['updated']
                ? "Update berhasil: {$result['before']['current_commit']} → {$result['after']['current_commit']}."
                : 'Repository sudah menggunakan versi terbaru.';

            Setting::set('system_update_last_checked_at', now()->toDateTimeString(), 'system');
            if ($result['updated']) {
                Setting::set('system_update_last_updated_at', now()->toDateTimeString(), 'system');
            }
            Setting::set('system_update_last_message', $message, 'system');

            return back()->with('success', $message);
        } catch (Throwable $exception) {
            report($exception);
            Setting::set('system_update_last_message', $exception->getMessage(), 'system');
            return back()->with('error', 'Update dibatalkan: '.$exception->getMessage());
        }
    }

    public function settings(Request $request): RedirectResponse
    {
        $validated = $request->validate(['auto_update' => ['nullable', 'boolean']]);
        $enabled = (bool) ($validated['auto_update'] ?? false);
        Setting::set('system_auto_update', $enabled ? '1' : '0', 'system');

        return back()->with('success', $enabled
            ? 'Update otomatis diaktifkan. GitHub akan diperiksa setiap 5 menit.'
            : 'Update otomatis dinonaktifkan.');
    }
}
