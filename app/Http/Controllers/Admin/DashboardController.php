<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\User;
use App\Models\Package;
use App\Models\Template;
use App\Models\Rsvp;
use App\Models\Setting;
use Database\Seeders\PackageSeeder;
use Database\Seeders\TemplateSeeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

class DashboardController extends Controller
{
    public function index()
    {
        $seedersAlreadyInitialized = !empty(Setting::get('initial_seeders_completed_at'));

        $stats = Cache::remember('admin:dashboard:stats:v1', now()->addSeconds(45), function () {
            return [
                'total_users' => User::where('role', 'client')->count(),
                'total_invitations' => Invitation::count(),
                'active_invitations' => Invitation::where('status', 'active')->count(),
                'pending_invitations' => Invitation::where('status', 'pending')->count(),
                'total_rsvps' => Rsvp::count(),
                'total_templates' => Template::where('is_active', true)->count(),
                'total_packages' => Package::where('is_active', true)->count(),
                'attending_rsvps' => Rsvp::where('status', 'attending')->count(),
            ];
        });

        $recentInvitations = Cache::remember('admin:dashboard:recent_invitations:v1', now()->addSeconds(30), function () {
            return Invitation::query()
                ->with(['user:id,name', 'template:id,name'])
                ->select(['id', 'user_id', 'template_id', 'title', 'status', 'event_date', 'created_at'])
                ->latest()
                ->take(5)
                ->get();
        });

        $recentUsers = Cache::remember('admin:dashboard:recent_users:v1', now()->addSeconds(30), function () {
            return User::query()
                ->where('role', 'client')
                ->select(['id', 'name', 'is_active', 'created_at'])
                ->latest()
            ->take(5)
            ->get();
        });

        $showInitialSeederButton = !$seedersAlreadyInitialized;

        return view('admin.dashboard.index', compact('stats', 'recentInvitations', 'recentUsers', 'showInitialSeederButton'));
    }

    public function runInitialSeeders()
    {
        if (!empty(Setting::get('initial_seeders_completed_at'))) {
            return redirect()
                ->route('admin.dashboard')
                ->with('error', 'Seeder awal sudah pernah dijalankan.');
        }

        try {
            Artisan::call('db:seed', [
                '--class' => TemplateSeeder::class,
                '--force' => true,
            ]);

            Artisan::call('db:seed', [
                '--class' => PackageSeeder::class,
                '--force' => true,
            ]);

            Cache::forget('admin:dashboard:stats:v1');
            Setting::set('initial_seeders_completed_at', now()->toDateTimeString(), 'system');

            return redirect()
                ->route('admin.dashboard')
                ->with('success', 'Seeder template dan paket berhasil dijalankan.');
        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->route('admin.dashboard')
                ->with('error', 'Seeder gagal dijalankan. Silakan cek log aplikasi.');
        }
    }
}
