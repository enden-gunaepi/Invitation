@extends('layouts.admin')
@section('title', 'Reliability Monitor')
@section('page-title', 'Reliability Monitor')
@section('page-subtitle', 'Monitoring queue, scheduler, dan audit admin')

@section('content')
<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
    <div class="card stat-card">
        <div class="stat-value">{{ number_format($metrics['pending_jobs']) }}</div>
        <div class="stat-label">Pending Jobs</div>
    </div>
    <div class="card stat-card">
        <div class="stat-value" style="color: {{ $metrics['failed_jobs'] > 0 ? 'var(--danger)' : 'var(--success)' }};">
            {{ number_format($metrics['failed_jobs']) }}
        </div>
        <div class="stat-label">Failed Jobs</div>
    </div>
    <div class="card stat-card">
        <div class="stat-value" style="color: {{ $metrics['overdue_reminders'] > 0 ? 'var(--warning)' : 'var(--success)' }};">
            {{ number_format($metrics['overdue_reminders']) }}
        </div>
        <div class="stat-label">Overdue Reminders</div>
    </div>
    <div class="card stat-card">
        <div class="stat-value" style="color: {{ $metrics['failed_dunning_24h'] > 0 ? 'var(--warning)' : 'var(--success)' }};">
            {{ number_format($metrics['failed_dunning_24h']) }}
        </div>
        <div class="stat-label">Dunning Failed (24h)</div>
    </div>
    <div class="card stat-card">
        <div class="stat-value" style="color: {{ $metrics['heartbeat_ok'] ? 'var(--success)' : 'var(--danger)' }};">
            {{ $metrics['heartbeat_ok'] ? 'OK' : 'STALE' }}
        </div>
        <div class="stat-label">
            Scheduler Heartbeat
            @if($metrics['heartbeat_at'])
                <div class="text-[11px]" style="color: var(--text-secondary);">{{ $metrics['heartbeat_at']->format('d/m H:i:s') }}</div>
            @endif
        </div>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
    <div class="card overflow-hidden">
        <div class="px-6 py-4 border-b" style="border-color: var(--border);">
            <h3 class="font-bold text-base">Failed Jobs Terbaru</h3>
        </div>
        <div class="p-4">
            @forelse($recentFailedJobs as $job)
                <div class="p-3 rounded-lg mb-2" style="background: var(--bg-tertiary);">
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-sm font-semibold">#{{ $job->id }} - {{ $job->queue }}</p>
                        <span class="text-xs" style="color: var(--text-secondary);">{{ \Carbon\Carbon::parse($job->failed_at)->format('d/m H:i') }}</span>
                    </div>
                    <p class="text-xs mt-1" style="color: var(--text-secondary);">
                        {{ \Illuminate\Support\Str::limit(preg_replace('/\s+/', ' ', (string) $job->exception), 160) }}
                    </p>
                </div>
            @empty
                <p class="text-sm py-6 text-center" style="color: var(--text-secondary);">Belum ada failed job.</p>
            @endforelse
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="px-6 py-4 border-b" style="border-color: var(--border);">
            <h3 class="font-bold text-base">Admin Audit Log Terbaru</h3>
        </div>
        <div class="p-4">
            @forelse($recentAdminAudits as $audit)
                <div class="p-3 rounded-lg mb-2" style="background: var(--bg-tertiary);">
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-sm font-semibold">{{ $audit->user->name ?? 'System' }} - {{ $audit->method }}</p>
                        <span class="text-xs" style="color: var(--text-secondary);">{{ $audit->created_at->format('d/m H:i') }}</span>
                    </div>
                    <p class="text-xs mt-1" style="color: var(--text-secondary);">
                        {{ $audit->route_name ?? $audit->path }} | Status {{ $audit->status_code ?? '-' }}
                    </p>
                </div>
            @empty
                <p class="text-sm py-6 text-center" style="color: var(--text-secondary);">Belum ada audit log admin.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
