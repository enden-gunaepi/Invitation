<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAuditLog;
use App\Models\DunningLog;
use App\Models\ReminderCampaign;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReliabilityController extends Controller
{
    public function index()
    {
        $pendingJobs = (int) DB::table('jobs')->count();
        $failedJobs = (int) DB::table('failed_jobs')->count();
        $overdueReminders = (int) ReminderCampaign::where('status', 'scheduled')
            ->where('scheduled_at', '<', now()->subMinutes(10))
            ->count();
        $failedDunning24h = (int) DunningLog::where('status', 'failed')
            ->where('created_at', '>=', now()->subDay())
            ->count();

        $heartbeatSetting = Setting::where('key', 'system_scheduler_heartbeat')->first();
        $heartbeatAt = $heartbeatSetting && !empty($heartbeatSetting->value)
            ? Carbon::parse($heartbeatSetting->value)
            : null;

        $isHeartbeatHealthy = $heartbeatAt ? $heartbeatAt->gte(now()->subMinutes(15)) : false;

        $recentFailedJobs = DB::table('failed_jobs')
            ->select(['id', 'queue', 'failed_at', 'exception'])
            ->orderByDesc('failed_at')
            ->limit(8)
            ->get();

        $recentAdminAudits = AdminAuditLog::with('user')
            ->latest()
            ->limit(12)
            ->get();

        $metrics = [
            'pending_jobs' => $pendingJobs,
            'failed_jobs' => $failedJobs,
            'overdue_reminders' => $overdueReminders,
            'failed_dunning_24h' => $failedDunning24h,
            'heartbeat_at' => $heartbeatAt,
            'heartbeat_ok' => $isHeartbeatHealthy,
        ];

        return view('admin.system.reliability', compact('metrics', 'recentFailedJobs', 'recentAdminAudits'));
    }
}
