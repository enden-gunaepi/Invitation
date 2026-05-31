@extends('layouts.client')
@section('title', 'Undangan Saya')
@section('page-title', 'Undangan Saya')
@section('page-subtitle', 'Kelola undangan berdasarkan paket yang sudah dibeli')

@section('content')
<style>
    .package-shell {
        border-radius: 1.25rem;
        border: 1px solid color-mix(in srgb, var(--outline-variant) 72%, transparent);
        background:
            radial-gradient(circle at top right, color-mix(in srgb, var(--accent) 8%, transparent), transparent 34%),
            linear-gradient(180deg, rgba(255,255,255,0.96), rgba(255,255,255,0.92));
        overflow: hidden;
        height: fit-content;
    }

    .package-summary {
        list-style: none;
        cursor: pointer;
        padding: 1.25rem;
    }

    .package-summary::-webkit-details-marker {
        display: none;
    }

    .package-shell[open] .package-chevron {
        transform: rotate(180deg);
    }

    .package-shell[open] {
        border-color: color-mix(in srgb, var(--accent) 26%, transparent);
    }

    .package-chevron {
        transition: transform 0.2s ease;
    }

    .package-metric {
        border-radius: 1rem;
        padding: 0.95rem 1rem;
        background: linear-gradient(180deg, rgba(255,255,255,0.92), rgba(248,250,252,0.96));
        border: 1px solid rgba(226, 232, 240, 0.9);
    }

    .package-cta {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.65rem;
        border-radius: 9999px;
        padding: 0.82rem 1.3rem;
        font-size: 0.86rem;
        font-weight: 700;
        letter-spacing: -0.01em;
        color: var(--on-primary);
        background: var(--accent);
        border: 1px solid var(--accent);
        transition: background 0.2s ease, border-color 0.2s ease, transform 0.2s ease;
    }

    .package-cta:hover {
        transform: translateY(-1px);
        background: var(--accent-hover);
        border-color: var(--accent-hover);
    }

    .package-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
        border-radius: 9999px;
        padding: 0.58rem 0.9rem;
        font-size: 0.74rem;
        font-weight: 700;
        transition: all 0.2s ease;
    }

    .package-action-neutral {
        background: var(--surface-container);
        color: var(--on-surface);
        border: 1px solid var(--outline-variant);
    }

    .package-action-neutral:hover {
        color: var(--accent);
        background: var(--surface-container-high);
        border-color: color-mix(in srgb, var(--accent) 35%, transparent);
    }

    .package-action-success {
        background: #15803d;
        color: #ffffff;
        border: 1px solid #15803d;
    }

    .package-action-warning {
        background: #d97706;
        color: #ffffff;
        border: 1px solid #d97706;
    }

    .package-content {
        background: linear-gradient(180deg, rgba(255,255,255,0.82), rgba(248,250,252,0.94));
    }

    .invitation-item-card {
        display: block;
        border-radius: 1rem;
    }

    .invitation-item-card:hover .invitation-item-title {
        color: rgb(52 211 153);
    }
</style>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <p class="text-sm text-slate-400">{{ $totalInvitations }} undangan total</p>
        <a href="{{ $hasUsableSubscription ? route('client.invitations.index') : route('client.packages.select') }}" class="package-cta">
            <i class="fas {{ $hasUsableSubscription ? 'fa-layer-group' : 'fa-box-open' }}"></i>
            {{ $hasUsableSubscription ? 'Pilih Paket Untuk Buat' : 'Pilih Paket Dulu' }}
        </a>
    </div>

    @if($subscriptionCards->isNotEmpty())
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-start">
            @foreach($subscriptionCards as $card)
                @php
                    $subscription = $card['subscription'];
                    $package = $subscription->package;
                    $usage = $card['usage'];
                    $packageInvitations = $card['invitations'];
                    $displayCode = 'SUB-' . str_pad((string) $subscription->id, 4, '0', STR_PAD_LEFT);
                @endphp
                <details class="package-shell" {{ $loop->first ? 'open' : '' }}>
                    <summary class="package-summary">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <span class="badge {{ $subscription->isActive() ? 'badge-active' : 'badge-draft' }}">
                                    {{ $subscription->isActive() ? 'Aktif' : ucfirst($subscription->status) }}
                                </span>
                                <h3 class="mt-3 text-lg font-bold">{{ $package->name }}</h3>
                                <p class="mt-1 text-xs text-slate-500">{{ $displayCode }}</p>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="text-[11px] text-slate-500 text-right font-medium">
                                    {{ $subscription->expires_at?->format('d M Y') ?? 'Tanpa batas' }}
                                </span>
                                <i class="fas fa-chevron-down package-chevron text-xs text-slate-500 mt-1"></i>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mt-5">
                            <div class="package-metric">
                                <p class="text-[11px] uppercase tracking-[0.18em] text-slate-500">Terpakai</p>
                                <p class="mt-1 text-lg font-bold">{{ $usage['used'] }}</p>
                            </div>
                            <div class="package-metric">
                                <p class="text-[11px] uppercase tracking-[0.18em] text-slate-500">Sisa</p>
                                <p class="mt-1 text-lg font-bold">{{ $usage['remaining'] }}</p>
                            </div>
                            <div class="package-metric">
                                <p class="text-[11px] uppercase tracking-[0.18em] text-slate-500">Kuota</p>
                                <p class="mt-1 text-lg font-bold">{{ $usage['max'] }}</p>
                            </div>
                            <div class="package-metric">
                                <p class="text-[11px] uppercase tracking-[0.18em] text-slate-500">Template</p>
                                <p class="mt-1 text-sm font-bold">{{ empty($package->allowed_template_ids) ? 'Semua' : count($package->allowed_template_ids) . ' template' }}</p>
                            </div>
                        </div>

                        <div class="mt-5">
                            <a href="{{ route('client.invitations.create', ['subscription_id' => $subscription->id]) }}" class="package-cta w-full" onclick="event.stopPropagation();">
                                <i class="fas fa-plus"></i> Buat Dengan Paket Ini
                            </a>
                        </div>
                    </summary>

                    <div class="package-content border-t px-5 pb-5 pt-4" style="border-color: color-mix(in srgb, var(--outline-variant) 70%, transparent);">
                        <div class="mb-4">
                            <p class="text-sm font-semibold">Daftar Undangan</p>
                            <p class="text-xs text-slate-500">Undangan pada paket ini akan muncul di bawah card saat paket dibuka.</p>
                        </div>

                        @if($packageInvitations->isNotEmpty())
                            <div class="space-y-3">
                                @foreach($packageInvitations as $inv)
                                    <div class="card p-4 group hover:border-emerald-500/30 transition">
                                        <a href="{{ route('client.invitations.show', $inv) }}" class="invitation-item-card">
                                            <div class="flex items-center justify-between mb-3">
                                                <span class="badge badge-{{ $inv->status }}">{{ ucfirst($inv->status) }}</span>
                                                <span class="text-xs text-slate-500">{{ $inv->event_date->format('d M Y') }}</span>
                                            </div>
                                            <h4 class="invitation-item-title font-bold text-sm mb-1 transition">{{ $inv->title }}</h4>
                                            <p class="text-xs text-slate-500 mb-3">{{ ucfirst($inv->event_type) }} - {{ $inv->venue_name }}</p>
                                            <div class="flex items-center gap-4 text-xs text-slate-500">
                                                <span><i class="fas fa-eye mr-1"></i>{{ $inv->view_count }}</span>
                                                <span><i class="fas fa-users mr-1"></i>{{ $inv->guests_count ?? 0 }}</span>
                                            </div>
                                        </a>
                                        <div class="mt-4 flex items-center gap-2">
                                            <a href="{{ route('client.invitations.show', $inv) }}" class="package-action package-action-neutral">
                                                <i class="fas fa-gear"></i> Kelola
                                            </a>
                                            <form method="POST" action="{{ route('client.invitations.toggle-status', $inv) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="package-action {{ $inv->status === 'active' && $inv->isActive() ? 'package-action-warning' : 'package-action-success' }}">
                                                    <i class="fas {{ $inv->status === 'active' && $inv->isActive() ? 'fa-toggle-off' : 'fa-toggle-on' }}"></i>
                                                    {{ $inv->status === 'active' && $inv->isActive() ? 'Nonaktifkan' : 'Aktifkan' }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="rounded-2xl border border-dashed px-5 py-10 text-center">
                                <p class="text-sm font-semibold text-slate-500">Belum ada undangan pada paket ini.</p>
                                <p class="text-xs mt-1 text-slate-400">Gunakan tombol di atas untuk membuat undangan pertama pada paket {{ $package->name }}.</p>
                            </div>
                        @endif
                    </div>
                </details>
            @endforeach
        </div>
    @else
        <div class="text-center py-16 flex flex-col items-center">
            <img src="{{ asset('assets/maskot/lihatundangan.png') }}" alt="Mascot Belum Ada Undangan" class="h-32 w-auto mb-4 drop-shadow-md" style="animation: float 4s ease-in-out infinite;">
            <h3 class="text-lg font-bold text-slate-400 mb-2">Belum Ada Undangan</h3>
            <p class="text-sm text-slate-500 mb-6">Mulai dari membeli paket lalu buat undangan digital pertama Anda.</p>
            <a href="{{ route('client.packages.select') }}" class="package-cta">
                <i class="fas fa-box-open"></i> Pilih Paket
            </a>
        </div>
    @endif
</div>

<style>
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-8px); }
    }
</style>
@endsection
