@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan statistik dan aktivitas terbaru')

@section('content')
{{-- Stats Grid --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <div class="card stat-card">
        <div class="flex items-center justify-between mb-3">
            <div class="stat-icon bg-gradient-to-br from-indigo-500/20 to-purple-500/20 text-indigo-400">
                <i class="fas fa-envelope"></i>
            </div>
            <span class="text-xs text-emerald-400 font-semibold"><i class="fas fa-arrow-up mr-1"></i>+12%</span>
        </div>
        <p class="text-2xl font-bold mb-1">{{ number_format($stats['total_invitations']) }}</p>
        <p class="text-xs text-slate-500">Total Undangan</p>
    </div>
    <div class="card stat-card">
        <div class="flex items-center justify-between mb-3">
            <div class="stat-icon bg-gradient-to-br from-emerald-500/20 to-cyan-500/20 text-emerald-400">
                <i class="fas fa-check-circle"></i>
            </div>
            <span class="text-xs text-emerald-400 font-semibold">{{ $stats['active_invitations'] }} aktif</span>
        </div>
        <p class="text-2xl font-bold mb-1">{{ number_format($stats['attending_rsvps']) }}</p>
        <p class="text-xs text-slate-500">Total RSVP Hadir</p>
    </div>
    <div class="card stat-card">
        <div class="flex items-center justify-between mb-3">
            <div class="stat-icon bg-gradient-to-br from-amber-500/20 to-orange-500/20 text-amber-400">
                <i class="fas fa-users"></i>
            </div>
            <span class="text-xs text-amber-400 font-semibold">{{ $stats['pending_invitations'] }} pending</span>
        </div>
        <p class="text-2xl font-bold mb-1">{{ number_format($stats['total_users']) }}</p>
        <p class="text-xs text-slate-500">Total Client</p>
    </div>
    <div class="card stat-card">
        <div class="flex items-center justify-between mb-3">
            <div class="stat-icon bg-gradient-to-br from-rose-500/20 to-pink-500/20 text-rose-400">
                <i class="fas fa-palette"></i>
            </div>
        </div>
        <p class="text-2xl font-bold mb-1">{{ $stats['total_templates'] }}</p>
        <p class="text-xs text-slate-500">Template Aktif</p>
    </div>
</div>

{{-- Two Column Layout --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Recent Invitations --}}
    <div class="lg:col-span-2 card overflow-hidden">
        <div class="px-6 py-4 border-b border-[rgba(99,102,241,0.1)] flex items-center justify-between">
            <h3 class="font-bold text-base">Undangan Terbaru</h3>
            <a href="{{ route('admin.invitations.index') }}" class="text-indigo-400 text-xs font-semibold hover:text-indigo-300 transition">
                Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <div class="p-4">
            @forelse($recentInvitations as $inv)
            <div class="flex items-center gap-4 p-3 rounded-xl hover:bg-[rgba(99,102,241,0.05)] transition mb-1">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500/20 to-purple-500/20 flex items-center justify-center text-indigo-400">
                    <i class="fas fa-envelope text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold truncate">{{ $inv->title }}</p>
                    <p class="text-xs text-slate-500">{{ $inv->user->name }} · {{ $inv->event_date->format('d M Y') }}</p>
                </div>
                <span class="badge badge-{{ $inv->status }}">{{ ucfirst($inv->status) }}</span>
            </div>
            @empty
            <div class="text-center py-8 text-slate-500">
                <i class="fas fa-inbox text-3xl mb-3 opacity-50"></i>
                <p class="text-sm">Belum ada undangan</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Recent Users --}}
    <div class="card overflow-hidden">
        <div class="px-6 py-4 border-b border-[rgba(99,102,241,0.1)] flex items-center justify-between">
            <h3 class="font-bold text-base">Client Baru</h3>
            <a href="{{ route('admin.users.index') }}" class="text-indigo-400 text-xs font-semibold hover:text-indigo-300 transition">
                Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <div class="p-4">
            @forelse($recentUsers as $user)
            <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-[rgba(99,102,241,0.05)] transition mb-1">
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-emerald-500 to-cyan-500 flex items-center justify-center text-white text-sm font-bold">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold truncate">{{ $user->name }}</p>
                    <p class="text-xs text-slate-500">{{ $user->created_at->diffForHumans() }}</p>
                </div>
                @if($user->is_active)
                <div class="w-2 h-2 rounded-full bg-emerald-400"></div>
                @else
                <div class="w-2 h-2 rounded-full bg-slate-500"></div>
                @endif
            </div>
            @empty
            <div class="text-center py-8 text-slate-500">
                <i class="fas fa-users text-3xl mb-3 opacity-50"></i>
                <p class="text-sm">Belum ada client</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

{{-- Quick Actions --}}
<div class="mt-6 card p-6">
    <h3 class="font-bold text-base mb-4">Aksi Cepat</h3>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <a href="{{ route('admin.invitations.index', ['status' => 'pending']) }}" class="flex flex-col items-center gap-3 p-4 rounded-xl bg-[rgba(245,158,11,0.08)] hover:bg-[rgba(245,158,11,0.15)] transition group">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500/20 to-orange-500/20 flex items-center justify-center text-amber-400 group-hover:scale-110 transition">
                <i class="fas fa-clock text-lg"></i>
            </div>
            <span class="text-xs text-slate-400 font-semibold text-center">Review Undangan</span>
        </a>
        <a href="{{ route('admin.users.create') }}" class="flex flex-col items-center gap-3 p-4 rounded-xl bg-[rgba(99,102,241,0.08)] hover:bg-[rgba(99,102,241,0.15)] transition group">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500/20 to-purple-500/20 flex items-center justify-center text-indigo-400 group-hover:scale-110 transition">
                <i class="fas fa-user-plus text-lg"></i>
            </div>
            <span class="text-xs text-slate-400 font-semibold text-center">Tambah User</span>
        </a>
        <a href="{{ route('admin.templates.create') }}" class="flex flex-col items-center gap-3 p-4 rounded-xl bg-[rgba(16,185,129,0.08)] hover:bg-[rgba(16,185,129,0.15)] transition group">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500/20 to-cyan-500/20 flex items-center justify-center text-emerald-400 group-hover:scale-110 transition">
                <i class="fas fa-palette text-lg"></i>
            </div>
            <span class="text-xs text-slate-400 font-semibold text-center">Tambah Template</span>
        </a>
        <a href="{{ route('admin.packages.create') }}" class="flex flex-col items-center gap-3 p-4 rounded-xl bg-[rgba(239,68,68,0.08)] hover:bg-[rgba(239,68,68,0.15)] transition group">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-rose-500/20 to-pink-500/20 flex items-center justify-center text-rose-400 group-hover:scale-110 transition">
                <i class="fas fa-box text-lg"></i>
            </div>
            <span class="text-xs text-slate-400 font-semibold text-center">Tambah Paket</span>
        </a>
    </div>
</div>
@endsection
