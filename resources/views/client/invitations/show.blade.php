@extends('layouts.client')
@section('title', $invitation->title)
@section('page-title', $invitation->title)
@section('page-subtitle', ucfirst($invitation->event_type) . ' · ' . $invitation->event_date->format('d M Y'))

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Main Content --}}
    <div class="lg:col-span-2 space-y-6">
        {{-- Event Info Card --}}
        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-base">Informasi Acara</h3>
                <span class="badge badge-{{ $invitation->status }}">{{ ucfirst($invitation->status) }}</span>
            </div>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div><span class="text-slate-500">Jenis Acara</span><p class="font-semibold mt-1">{{ ucfirst($invitation->event_type) }}</p></div>
                <div><span class="text-slate-500">Tanggal</span><p class="font-semibold mt-1">{{ $invitation->event_date->format('d F Y') }}</p></div>
                <div><span class="text-slate-500">Waktu</span><p class="font-semibold mt-1">{{ $invitation->event_time }}</p></div>
                <div><span class="text-slate-500">Tempat</span><p class="font-semibold mt-1">{{ $invitation->venue_name }}</p></div>
                @if($invitation->groom_name)
                <div><span class="text-slate-500">Mempelai Pria</span><p class="font-semibold mt-1">{{ $invitation->groom_name }}</p></div>
                @endif
                @if($invitation->bride_name)
                <div><span class="text-slate-500">Mempelai Wanita</span><p class="font-semibold mt-1">{{ $invitation->bride_name }}</p></div>
                @endif
            </div>
            <div class="mt-4 p-3 bg-slate-800/50 rounded-xl text-sm text-slate-400">
                <i class="fas fa-map-marker-alt text-emerald-400 mr-2"></i> {{ $invitation->venue_address }}
            </div>
        </div>

        {{-- Admin Notes --}}
        @if($invitation->admin_notes)
        <div class="card p-6 border-amber-500/30">
            <h3 class="font-bold text-sm text-amber-400 mb-2"><i class="fas fa-sticky-note mr-2"></i> Catatan Admin</h3>
            <p class="text-sm text-slate-400">{{ $invitation->admin_notes }}</p>
        </div>
        @endif

        {{-- RSVP List --}}
        <div class="card overflow-hidden">
            <div class="px-6 py-4 border-b border-[rgba(16,185,129,0.1)] flex items-center justify-between">
                <h3 class="font-bold text-base">RSVP ({{ $invitation->rsvps->count() }})</h3>
            </div>
            <div class="p-4">
                @forelse($invitation->rsvps as $rsvp)
                <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-[rgba(16,185,129,0.05)] transition mb-1">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold
                        {{ $rsvp->status === 'attending' ? 'bg-emerald-500/20 text-emerald-400' : ($rsvp->status === 'maybe' ? 'bg-amber-500/20 text-amber-400' : 'bg-red-500/20 text-red-400') }}">
                        {{ $rsvp->status === 'attending' ? '✓' : ($rsvp->status === 'maybe' ? '?' : '✗') }}
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold">{{ $rsvp->name }}</p>
                        <p class="text-xs text-slate-500">{{ $rsvp->pax }} orang · {{ $rsvp->created_at->diffForHumans() }}</p>
                    </div>
                    @if($rsvp->message)
                    <p class="text-xs text-slate-500 max-w-xs truncate">{{ $rsvp->message }}</p>
                    @endif
                </div>
                @empty
                <p class="text-center text-sm text-slate-500 py-6">Belum ada RSVP</p>
                @endforelse
            </div>
        </div>

        {{-- Wishes --}}
        <div class="card overflow-hidden">
            <div class="px-6 py-4 border-b border-[rgba(16,185,129,0.1)]">
                <h3 class="font-bold text-base">Ucapan ({{ $invitation->wishes->count() }})</h3>
            </div>
            <div class="p-4">
                @forelse($invitation->wishes as $wish)
                <div class="p-3 rounded-xl hover:bg-[rgba(16,185,129,0.05)] transition mb-2">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-sm font-semibold">{{ $wish->name }}</span>
                        <span class="text-xs text-slate-500">{{ $wish->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-sm text-slate-400">{{ $wish->message }}</p>
                </div>
                @empty
                <p class="text-center text-sm text-slate-500 py-6">Belum ada ucapan</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="space-y-6">
        {{-- Actions --}}
        <div class="card p-6">
            <h3 class="font-bold text-base mb-4">Aksi</h3>
            <div class="space-y-3">
                <a href="{{ route('client.invitations.edit', $invitation) }}" class="btn-primary w-full text-center block text-sm py-3">
                    <i class="fas fa-edit mr-2"></i> Edit Undangan
                </a>
                @if($invitation->status === 'draft')
                <form method="POST" action="{{ route('client.invitations.submit', $invitation) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="w-full text-center text-sm py-3 rounded-xl font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20 hover:bg-amber-500/20 transition">
                        <i class="fas fa-paper-plane mr-2"></i> Submit untuk Review
                    </button>
                </form>
                @endif
                @if($invitation->isActive())
                <a href="{{ $invitation->getPublicUrl() }}" target="_blank" class="block w-full text-center text-sm py-3 rounded-xl font-semibold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 hover:bg-indigo-500/20 transition">
                    <i class="fas fa-external-link-alt mr-2"></i> Lihat Undangan
                </a>
                @endif
                <a href="{{ route('client.invitations.guests.index', $invitation) }}" class="block w-full text-center text-sm py-3 rounded-xl font-semibold bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 hover:bg-cyan-500/20 transition">
                    <i class="fas fa-users mr-2"></i> Kelola Tamu
                </a>
            </div>
        </div>

        {{-- Stats --}}
        <div class="card p-6">
            <h3 class="font-bold text-base mb-4">Statistik</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-500"><i class="fas fa-eye mr-2 w-4"></i>Kunjungan</span>
                    <span class="font-bold">{{ number_format($invitation->view_count) }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-500"><i class="fas fa-users mr-2 w-4"></i>Total Tamu</span>
                    <span class="font-bold">{{ $invitation->guests->count() }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-500"><i class="fas fa-check-circle mr-2 w-4 text-emerald-400"></i>Hadir</span>
                    <span class="font-bold text-emerald-400">{{ $invitation->rsvps->where('status', 'attending')->count() }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-500"><i class="fas fa-question-circle mr-2 w-4 text-amber-400"></i>Maybe</span>
                    <span class="font-bold text-amber-400">{{ $invitation->rsvps->where('status', 'maybe')->count() }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-500"><i class="fas fa-times-circle mr-2 w-4 text-red-400"></i>Tidak</span>
                    <span class="font-bold text-red-400">{{ $invitation->rsvps->where('status', 'not_attending')->count() }}</span>
                </div>
            </div>
        </div>

        {{-- Share Link --}}
        @if($invitation->isActive())
        <div class="card p-6">
            <h3 class="font-bold text-base mb-3">Share Link</h3>
            <div class="p-3 bg-slate-800 rounded-lg text-xs text-emerald-400 break-all mb-3" id="invite-url">
                {{ $invitation->getPublicUrl() }}
            </div>
            <button onclick="navigator.clipboard.writeText(document.getElementById('invite-url').textContent.trim()); this.textContent='Tersalin!'; setTimeout(() => this.textContent='Copy Link', 2000);"
                    class="w-full text-center text-sm py-2 rounded-lg font-semibold bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20 transition">
                Copy Link
            </button>
        </div>
        @endif
    </div>
</div>

<div class="mt-4">
    <a href="{{ route('client.invitations.index') }}" class="text-emerald-400 text-sm hover:text-emerald-300">
        <i class="fas fa-arrow-left mr-1"></i> Kembali ke daftar
    </a>
</div>
@endsection
