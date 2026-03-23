@extends('layouts.client')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Selamat datang, ' . auth()->user()->name)

@section('content')
{{-- Stats Grid --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-8">
    <div class="card stat-card">
        <div class="flex items-center justify-between mb-3">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500/20 to-cyan-500/20 flex items-center justify-center text-emerald-400 text-lg">
                <i class="fas fa-envelope"></i>
            </div>
        </div>
        <p class="text-2xl font-bold mb-1">{{ $stats['total_invitations'] }}</p>
        <p class="text-xs text-slate-500">Total Undangan</p>
    </div>
    <div class="card stat-card">
        <div class="flex items-center justify-between mb-3">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500/20 to-purple-500/20 flex items-center justify-center text-indigo-400 text-lg">
                <i class="fas fa-check-circle"></i>
            </div>
            <span class="text-xs text-emerald-400 font-semibold">{{ $stats['attending'] }} hadir</span>
        </div>
        <p class="text-2xl font-bold mb-1">{{ $stats['total_rsvps'] }}</p>
        <p class="text-xs text-slate-500">Total RSVP</p>
    </div>
    <div class="card stat-card">
        <div class="flex items-center justify-between mb-3">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500/20 to-orange-500/20 flex items-center justify-center text-amber-400 text-lg">
                <i class="fas fa-eye"></i>
            </div>
        </div>
        <p class="text-2xl font-bold mb-1">{{ number_format($stats['total_views']) }}</p>
        <p class="text-xs text-slate-500">Total Kunjungan</p>
    </div>
</div>

{{-- Recent Invitations + CTA --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 card overflow-hidden">
        <div class="px-6 py-4 border-b border-[rgba(16,185,129,0.1)] flex items-center justify-between">
            <h3 class="font-bold text-base">Undangan Saya</h3>
            <a href="{{ route('client.invitations.index') }}" class="text-emerald-400 text-xs font-semibold hover:text-emerald-300 transition">
                Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <div class="p-4">
            @forelse($invitations as $inv)
            <a href="{{ route('client.invitations.show', $inv) }}" class="flex items-center gap-4 p-3 rounded-xl hover:bg-[rgba(16,185,129,0.05)] transition mb-1 block">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500/20 to-cyan-500/20 flex items-center justify-center text-emerald-400">
                    <i class="fas fa-envelope text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold truncate">{{ $inv->title }}</p>
                    <p class="text-xs text-slate-500">{{ $inv->event_date->format('d M Y') }} - {{ $inv->venue_name }}</p>
                </div>
                <span class="badge badge-{{ $inv->status }}">{{ ucfirst($inv->status) }}</span>
            </a>
            @empty
            <div class="text-center py-12 text-slate-500">
                <i class="fas fa-envelope-open text-4xl mb-4 opacity-40"></i>
                <p class="text-sm mb-4">Belum ada undangan. Buat undangan pertama Anda!</p>
                <a href="{{ route('client.invitations.create') }}" class="btn-primary inline-block text-sm">
                    <i class="fas fa-plus mr-2"></i> Buat Undangan
                </a>
            </div>
            @endforelse
        </div>
    </div>

    <div class="space-y-6">
        {{-- Onboarding --}}
        <div class="card p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="font-bold text-base">Progress Setup</h3>
                <span class="text-xs font-semibold" style="color: var(--accent);">{{ $onboarding['progress'] ?? 0 }}%</span>
            </div>
            <div class="mb-4" style="background: var(--bg-tertiary); height: 6px; border-radius: 999px; overflow: hidden;">
                <div style="width: {{ $onboarding['progress'] ?? 0 }}%; height: 100%; background: linear-gradient(90deg, #10b981, #22c55e);"></div>
            </div>

            <div class="space-y-2 mb-5">
                @foreach(($onboarding['items'] ?? []) as $item)
                <div class="flex items-center gap-2 text-sm" style="color: {{ $item['done'] ? '#34d399' : 'var(--text-secondary)' }};">
                    <i class="fas {{ $item['done'] ? 'fa-check-circle' : 'fa-circle' }} text-xs"></i>
                    <span>{{ $item['label'] }}</span>
                </div>
                @endforeach
            </div>

            <a href="{{ $onboarding['next_url'] ?? route('client.invitations.create') }}" class="btn-primary w-full text-center block py-3">
                <i class="fas fa-arrow-right mr-2"></i> {{ $onboarding['next_label'] ?? 'Lanjutkan Setup' }}
            </a>
        </div>

        {{-- Upsell --}}
        @if(!empty($upsell))
        <div class="card p-6" style="border-color: rgba(245,158,11,.35);">
            <h3 class="font-bold text-base mb-2" style="color: #f59e0b;"><i class="fas fa-rocket mr-2"></i> Rekomendasi Upgrade</h3>
            <p class="text-sm mb-3" style="color: var(--text-secondary);">
                Untuk menjaga performa undangan, upgrade ke paket <strong>{{ $upsell['next_package_name'] }}</strong>
                (Rp{{ number_format($upsell['next_package_price'], 0, ',', '.') }}).
            </p>
            <div class="space-y-1 mb-4">
                @foreach($upsell['reasons'] as $reason)
                    <p class="text-xs" style="color: var(--text-secondary);"><i class="fas fa-angle-right mr-1"></i>{{ $reason }}</p>
                @endforeach
            </div>
            <form method="POST" action="{{ route('client.invitations.upgrade-suggested', $upsell['invitation_id']) }}">
                @csrf
                <button type="submit" class="btn w-full text-center block py-3" style="background: rgba(245,158,11,.12); color: #f59e0b; border: 1px solid rgba(245,158,11,.35);">
                    <i class="fas fa-arrow-up mr-2"></i> Upgrade Paket 1 Klik
                </button>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection
