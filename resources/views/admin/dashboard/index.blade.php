@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan statistik dan aktivitas terbaru')

@section('content')
@if(!empty($showInitialSeederButton))
<div class="card p-5 mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h3 class="font-bold text-base mb-1">Inisialisasi Data Awal</h3>
            <p class="text-xs" style="color: var(--text-secondary);">Jalankan seeder untuk memastikan template dan paket default tersedia sebelum proses edit.</p>
        </div>
        <form action="{{ route('admin.seeders.initial') }}" method="POST" onsubmit="return confirm('Jalankan seeder template dan paket sekarang?')">
            @csrf
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-database"></i>
                Jalankan Seeder Awal
            </button>
        </form>
    </div>
</div>
@endif

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <div class="card stat-card">
        <div class="flex items-center justify-between mb-3">
            <div class="stat-icon rounded-2xl bg-[rgba(185,28,28,0.1)] text-[var(--accent)]">
                <i class="fas fa-envelope"></i>
            </div>
            <span class="text-xs font-semibold" style="color: var(--accent);"><i class="fas fa-arrow-up mr-1"></i>+12%</span>
        </div>
        <p class="text-2xl font-bold mb-1">{{ number_format($stats['total_invitations']) }}</p>
        <p class="text-xs" style="color: var(--text-secondary);">Total Undangan</p>
    </div>
    <div class="card stat-card">
        <div class="flex items-center justify-between mb-3">
            <div class="stat-icon rounded-2xl bg-black/[0.05] text-[var(--accent)]">
                <i class="fas fa-check-circle"></i>
            </div>
            <span class="text-xs font-semibold" style="color: var(--accent);">{{ $stats['active_invitations'] }} aktif</span>
        </div>
        <p class="text-2xl font-bold mb-1">{{ number_format($stats['attending_rsvps']) }}</p>
        <p class="text-xs" style="color: var(--text-secondary);">Total RSVP Hadir</p>
    </div>
    <div class="card stat-card">
        <div class="flex items-center justify-between mb-3">
            <div class="stat-icon rounded-2xl bg-black/[0.05] text-[var(--accent)]">
                <i class="fas fa-users"></i>
            </div>
            <span class="text-xs font-semibold" style="color: var(--accent);">{{ $stats['pending_invitations'] }} pending</span>
        </div>
        <p class="text-2xl font-bold mb-1">{{ number_format($stats['total_users']) }}</p>
        <p class="text-xs" style="color: var(--text-secondary);">Total Client</p>
    </div>
    <div class="card stat-card">
        <div class="flex items-center justify-between mb-3">
            <div class="stat-icon rounded-2xl bg-[rgba(185,28,28,0.1)] text-[var(--accent)]">
                <i class="fas fa-palette"></i>
            </div>
        </div>
        <p class="text-2xl font-bold mb-1">{{ $stats['total_templates'] }}</p>
        <p class="text-xs" style="color: var(--text-secondary);">Template Aktif</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 card overflow-hidden">
        <div class="px-6 py-4 border-b border-black/5 flex items-center justify-between">
            <h3 class="font-bold text-base">Undangan Terbaru</h3>
            <a href="{{ route('admin.invitations.index') }}" class="text-xs font-semibold transition" style="color: var(--accent);">
                Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <div class="p-4">
            @forelse($recentInvitations as $inv)
            <div class="flex items-center gap-4 p-3 rounded-2xl hover:bg-black/[0.03] transition mb-1">
                <div class="w-10 h-10 rounded-2xl bg-[rgba(185,28,28,0.1)] flex items-center justify-center text-[var(--accent)]">
                    <i class="fas fa-envelope text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold truncate">{{ $inv->title }}</p>
                    <p class="text-xs" style="color: var(--text-secondary);">{{ $inv->user->name }} · {{ $inv->event_date->format('d M Y') }}</p>
                </div>
                <span class="badge badge-{{ $inv->status }}">{{ ucfirst($inv->status) }}</span>
            </div>
            @empty
            <div class="text-center py-8" style="color: var(--text-secondary);">
                <i class="fas fa-inbox text-3xl mb-3 opacity-50"></i>
                <p class="text-sm">Belum ada undangan</p>
            </div>
            @endforelse
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="px-6 py-4 border-b border-black/5 flex items-center justify-between">
            <h3 class="font-bold text-base">Client Baru</h3>
            <a href="{{ route('admin.users.index') }}" class="text-xs font-semibold transition" style="color: var(--accent);">
                Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <div class="p-4">
            @forelse($recentUsers as $user)
            <div class="flex items-center gap-3 p-3 rounded-2xl hover:bg-black/[0.03] transition mb-1">
                <div class="w-9 h-9 rounded-full bg-[linear-gradient(135deg,#7f1d1d,#dc2626)] flex items-center justify-center text-white text-sm font-bold">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold truncate">{{ $user->name }}</p>
                    <p class="text-xs" style="color: var(--text-secondary);">{{ $user->created_at->diffForHumans() }}</p>
                </div>
                @if($user->is_active)
                <div class="w-2 h-2 rounded-full bg-[var(--accent)]"></div>
                @else
                <div class="w-2 h-2 rounded-full bg-black/20"></div>
                @endif
            </div>
            @empty
            <div class="text-center py-8" style="color: var(--text-secondary);">
                <i class="fas fa-users text-3xl mb-3 opacity-50"></i>
                <p class="text-sm">Belum ada client</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

<div class="mt-6 card p-6">
    <h3 class="font-bold text-base mb-4">Aksi Cepat</h3>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <a href="{{ route('admin.invitations.index', ['status' => 'pending']) }}" class="flex flex-col items-center gap-3 p-4 rounded-2xl bg-black/[0.03] hover:bg-[rgba(185,28,28,0.08)] transition group">
            <div class="w-12 h-12 rounded-2xl bg-[rgba(185,28,28,0.1)] flex items-center justify-center text-[var(--accent)] group-hover:scale-110 transition">
                <i class="fas fa-clock text-lg"></i>
            </div>
            <span class="text-xs font-semibold text-center" style="color: var(--text-secondary);">Review Undangan</span>
        </a>
        <a href="{{ route('admin.users.create') }}" class="flex flex-col items-center gap-3 p-4 rounded-2xl bg-black/[0.03] hover:bg-[rgba(185,28,28,0.08)] transition group">
            <div class="w-12 h-12 rounded-2xl bg-black/[0.05] flex items-center justify-center text-[var(--accent)] group-hover:scale-110 transition">
                <i class="fas fa-user-plus text-lg"></i>
            </div>
            <span class="text-xs font-semibold text-center" style="color: var(--text-secondary);">Tambah User</span>
        </a>
        <a href="{{ route('admin.templates.create') }}" class="flex flex-col items-center gap-3 p-4 rounded-2xl bg-black/[0.03] hover:bg-[rgba(185,28,28,0.08)] transition group">
            <div class="w-12 h-12 rounded-2xl bg-black/[0.05] flex items-center justify-center text-[var(--accent)] group-hover:scale-110 transition">
                <i class="fas fa-palette text-lg"></i>
            </div>
            <span class="text-xs font-semibold text-center" style="color: var(--text-secondary);">Tambah Template</span>
        </a>
        <a href="{{ route('admin.packages.create') }}" class="flex flex-col items-center gap-3 p-4 rounded-2xl bg-black/[0.03] hover:bg-[rgba(185,28,28,0.08)] transition group">
            <div class="w-12 h-12 rounded-2xl bg-[rgba(185,28,28,0.1)] flex items-center justify-center text-[var(--accent)] group-hover:scale-110 transition">
                <i class="fas fa-box text-lg"></i>
            </div>
            <span class="text-xs font-semibold text-center" style="color: var(--text-secondary);">Tambah Paket</span>
        </a>
    </div>
</div>
@endsection
