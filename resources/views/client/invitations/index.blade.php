@extends('layouts.client')
@section('title', 'Undangan Saya')
@section('page-title', 'Undangan Saya')
@section('page-subtitle', 'Kelola semua undangan digital Anda')

@section('content')
<style>
    .invitation-cta {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.65rem;
        border-radius: 9999px;
        padding: 0.82rem 1.3rem;
        font-size: 0.86rem;
        font-weight: 700;
        letter-spacing: -0.01em;
        color: white;
        background: linear-gradient(135deg, color-mix(in srgb, var(--accent) 92%, white 8%), color-mix(in srgb, var(--accent) 68%, #7c3aed 32%));
        box-shadow: 0 14px 30px color-mix(in srgb, var(--accent) 22%, transparent);
        transition: transform 0.22s ease, box-shadow 0.22s ease, filter 0.22s ease;
    }

    .invitation-cta:hover {
        transform: translateY(-1px);
        filter: saturate(1.05);
        box-shadow: 0 18px 34px color-mix(in srgb, var(--accent) 28%, transparent);
    }

    .invitation-cta-secondary {
        background: linear-gradient(135deg, color-mix(in srgb, var(--surface-container) 82%, white 18%), color-mix(in srgb, var(--surface-container-high) 88%, white 12%));
        color: var(--on-surface);
        box-shadow: inset 0 0 0 1px color-mix(in srgb, var(--outline-variant) 78%, transparent), 0 12px 24px rgba(15, 23, 42, 0.06);
    }

    .invitation-cta-secondary:hover {
        box-shadow: inset 0 0 0 1px color-mix(in srgb, var(--accent) 28%, transparent), 0 16px 28px rgba(15, 23, 42, 0.09);
        color: var(--accent);
    }
</style>

<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-slate-400">{{ $invitations->total() }} undangan</p>
    <a href="{{ $hasActivePackage ? route('client.invitations.create') : route('client.packages.select') }}" class="invitation-cta">
        <i class="fas {{ $hasActivePackage ? 'fa-plus' : 'fa-box-open' }} mr-2"></i> {{ $hasActivePackage ? 'Buat Undangan' : 'Pilih Paket Dulu' }}
    </a>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
    @forelse($invitations as $inv)
    <a href="{{ route('client.invitations.show', $inv) }}" class="card p-5 group hover:border-emerald-500/30 transition block">
        <div class="flex items-center justify-between mb-3">
            <span class="badge badge-{{ $inv->status }}">{{ ucfirst($inv->status) }}</span>
            <span class="text-xs text-slate-500">{{ $inv->event_date->format('d M Y') }}</span>
        </div>
        <h4 class="font-bold text-sm mb-1 group-hover:text-emerald-400 transition">{{ $inv->title }}</h4>
        <p class="text-xs text-slate-500 mb-3">{{ ucfirst($inv->event_type) }} - {{ $inv->venue_name }}</p>
        <div class="flex items-center gap-4 text-xs text-slate-500">
            <span><i class="fas fa-eye mr-1"></i>{{ $inv->view_count }}</span>
            <span><i class="fas fa-users mr-1"></i>{{ $inv->guests_count ?? 0 }}</span>
        </div>
    </a>
    @empty
    <div class="col-span-3 text-center py-16 flex flex-col items-center">
        <img src="{{ asset('assets/maskot/lihatundangan.png') }}" alt="Mascot Belum Ada Undangan" class="h-32 w-auto mb-4 drop-shadow-md" style="animation: float 4s ease-in-out infinite;">
        <h3 class="text-lg font-bold text-slate-400 mb-2">Belum Ada Undangan</h3>
        <p class="text-sm text-slate-500 mb-6">Mulai buat undangan digital pertama Anda!</p>
        <a href="{{ $hasActivePackage ? route('client.invitations.create') : route('client.packages.select') }}" class="invitation-cta invitation-cta-secondary">
            <i class="fas {{ $hasActivePackage ? 'fa-plus' : 'fa-box-open' }} mr-2"></i> {{ $hasActivePackage ? 'Buat Undangan Sekarang' : 'Pilih Paket Dulu' }}
        </a>
    </div>
    @endforelse
</div>

<div class="mt-6">{{ $invitations->links() }}</div>

<style>
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-8px); }
    }
</style>
@endsection
