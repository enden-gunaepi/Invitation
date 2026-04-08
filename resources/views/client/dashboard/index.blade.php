@extends('layouts.client')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Selamat datang, ' . auth()->user()->name)

@section('content')
<style>
    /* Elegant Dashboard Styling */
    .elegant-card {
        background: #ffffff;
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.02);
        border: 1px solid rgba(0, 0, 0, 0.02);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .dark .elegant-card {
        background: #1E293B;
        border-color: rgba(255, 255, 255, 0.05);
    }

    .elegant-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 15px 45px rgba(0, 0, 0, 0.05);
    }

    .elegant-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin-bottom: 16px;
        background: var(--bg);
        color: var(--accent);
    }

    .elegant-card h4 {
        font-size: 28px;
        font-weight: 800;
        color: var(--accent);
        line-height: 1;
        margin-bottom: 6px;
    }

    .elegant-card p.title {
        font-size: 13px;
        font-weight: 600;
        color: var(--text);
        margin-bottom: 2px;
    }

    .elegant-card p.subtitle {
        font-size: 11px;
        color: var(--text-secondary);
    }

    /* Unified clean rows */
    .elegant-row {
        background: transparent;
        border-bottom: 1px solid var(--border);
    }

    .elegant-row:last-child {
        border-bottom: none;
    }
</style>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <div class="elegant-card">
        <div class="elegant-icon"><i class="fas fa-envelope-open-text"></i></div>
        <h4>{{ $stats['total_invitations'] }}</h4>
        <p class="title">Total Undangan</p>
        <p class="subtitle">Semua acara</p>
    </div>

    <div class="elegant-card">
        <div class="elegant-icon"><i class="fas fa-user-check"></i></div>
        <h4>{{ $stats['attending'] }}</h4>
        <p class="title">Tamu Hadir</p>
        <p class="subtitle">Terkonfirmasi (RSVP)</p>
    </div>

    <div class="elegant-card">
        <div class="elegant-icon"><i class="fas fa-reply-all"></i></div>
        <h4>{{ $stats['total_rsvps'] }}</h4>
        <p class="title">Total RSVP</p>
        <p class="subtitle">Semua balasan</p>
    </div>

    <div class="elegant-card">
        <div class="elegant-icon"><i class="fas fa-eye"></i></div>
        <h4>{{ number_format($stats['total_views']) }}</h4>
        <p class="title">Kunjungan</p>
        <p class="subtitle">Total dilihat</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 elegant-card border-none" style="padding: 0;">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
            <h3 class="font-bold text-lg text-[var(--accent)]">Undangan Saya</h3>
            <a href="{{ route('client.invitations.index') }}"
                class="text-xs font-semibold text-gray-500 hover:text-[var(--accent)] transition">
                Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <div class="p-2">
            @forelse($invitations as $inv)
                <a href="{{ route('client.invitations.show', $inv) }}"
                    class="flex items-center gap-4 p-4 hover:bg-gray-50/50 dark:hover:bg-slate-800/50 elegant-row transition block">
                    <div
                        class="w-12 h-12 rounded-full bg-[var(--bg)] flex items-center justify-center text-[var(--accent)]">
                        <i class="fas fa-envelope text-lg"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[15px] font-bold truncate text-gray-900 dark:text-gray-100"
                            style="color: var(--accent);">{{ $inv->title }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1"><i class="far fa-calendar-alt mr-1"></i>
                            {{ $inv->event_date->format('d M Y') }} • {{ $inv->venue_name ?? '-' }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span
                            class="text-[10px] font-semibold px-3 py-1 rounded-full border border-[var(--accent)] text-[var(--accent)]">{{ ucfirst($inv->status) }}</span>
                        <button
                            class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-[var(--accent)] hover:text-white text-gray-400 transition"><i
                                class="fas fa-pen text-xs"></i></button>
                        <button
                            class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-red-500 hover:text-white text-gray-400 transition"><i
                                class="fas fa-trash text-xs"></i></button>
                    </div>
                </a>
            @empty
                <div class="text-center py-12" style="color: gap;">
                    <i class="fas fa-envelope-open text-4xl mb-4 text-gray-300"></i>
                    <p class="text-sm mb-4 text-gray-500">Belum ada undangan. Buat undangan pertama Anda!</p>
                    <a href="{{ $hasActivePackage ? route('client.invitations.create') : route('client.packages.select') }}"
                        class="bg-[var(--accent)] hover:bg-blue-800 text-white px-5 py-2.5 rounded-full text-sm font-semibold transition-all inline-block shadow-md">
                        <i class="fas {{ $hasActivePackage ? 'fa-plus' : 'fa-box-open' }} mr-2"></i>
                        {{ $hasActivePackage ? 'Buat Undangan' : 'Pilih Paket' }}
                    </a>
                </div>
            @endforelse
        </div>
    </div>

    <div class="space-y-6">
        <div class="card p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="font-bold text-base">Progress Setup</h3>
                <span class="text-xs font-semibold"
                    style="color: var(--accent);">{{ $onboarding['progress'] ?? 0 }}%</span>
            </div>
            <div class="mb-4"
                style="background: var(--bg-tertiary); height: 6px; border-radius: 999px; overflow: hidden;">
                <div
                    style="width: {{ $onboarding['progress'] ?? 0 }}%; height: 100%; background: linear-gradient(90deg, #7f1d1d, #dc2626);">
                </div>
            </div>

            <div class="space-y-2 mb-5">
                @foreach(($onboarding['items'] ?? []) as $item)
                    <div class="flex items-center gap-2 text-sm"
                        style="color: {{ $item['done'] ? 'var(--accent)' : 'var(--text-secondary)' }};">
                        <i class="fas {{ $item['done'] ? 'fa-check-circle' : 'fa-circle' }} text-xs"></i>
                        <span>{{ $item['label'] }}</span>
                    </div>
                @endforeach
            </div>

            <a href="{{ $onboarding['next_url'] ?? ($hasActivePackage ? route('client.invitations.create') : route('client.packages.select')) }}"
                class="btn-primary w-full text-center block py-3">
                <i class="fas fa-arrow-right mr-2"></i> {{ $onboarding['next_label'] ?? 'Lanjutkan Setup' }}
            </a>
        </div>

        @if(!empty($upsell))
            <div class="card p-6" style="border-color: rgba(185,28,28,.22);">
                <h3 class="font-bold text-base mb-2" style="color: var(--accent);"><i class="fas fa-rocket mr-2"></i>
                    Rekomendasi Upgrade</h3>
                <p class="text-sm mb-3" style="color: var(--text-secondary);">
                    Untuk menjaga performa undangan, upgrade ke paket <strong>{{ $upsell['next_package_name'] }}</strong>
                    (Rp{{ number_format($upsell['next_package_price'], 0, ',', '.') }}).
                </p>
                <div class="space-y-1 mb-4">
                    @foreach($upsell['reasons'] as $reason)
                        <p class="text-xs" style="color: var(--text-secondary);"><i
                                class="fas fa-angle-right mr-1"></i>{{ $reason }}</p>
                    @endforeach
                </div>
                <form method="POST" action="{{ route('client.invitations.upgrade-suggested', $upsell['invitation_id']) }}">
                    @csrf
                    <button type="submit" class="btn w-full text-center block py-3"
                        style="background: rgba(185,28,28,.08); color: var(--accent); border: 1px solid rgba(185,28,28,.24);">
                        <i class="fas fa-arrow-up mr-2"></i> Upgrade Paket 1 Klik
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection