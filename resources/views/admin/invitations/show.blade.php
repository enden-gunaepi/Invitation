@extends('layouts.admin')

@section('title', 'Detail Undangan')
@section('page-title', $invitation->title)
@section('page-subtitle', 'Detail undangan ' . $invitation->user->name)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Main Info --}}
    <div class="lg:col-span-2 space-y-6">
        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-base">Informasi Acara</h3>
                <span class="badge badge-{{ $invitation->status }}">{{ ucfirst($invitation->status) }}</span>
            </div>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div><span class="text-slate-500">Jenis Acara:</span><p class="font-semibold">{{ ucfirst($invitation->event_type) }}</p></div>
                <div><span class="text-slate-500">Tanggal:</span><p class="font-semibold">{{ $invitation->event_date->format('d M Y') }}</p></div>
                <div><span class="text-slate-500">Waktu:</span><p class="font-semibold">{{ $invitation->event_time }}</p></div>
                <div><span class="text-slate-500">Tempat:</span><p class="font-semibold">{{ $invitation->venue_name }}</p></div>
                @if($invitation->groom_name)
                <div><span class="text-slate-500">Mempelai Pria:</span><p class="font-semibold">{{ $invitation->groom_name }}</p></div>
                @endif
                @if($invitation->bride_name)
                <div><span class="text-slate-500">Mempelai Wanita:</span><p class="font-semibold">{{ $invitation->bride_name }}</p></div>
                @endif
            </div>
            <p class="text-sm text-slate-400 mt-4">{{ $invitation->venue_address }}</p>
        </div>

        @if($invitation->status === 'pending')
        <div class="card p-6">
            <h3 class="font-bold text-base mb-4">Aksi Admin</h3>
            <div class="flex gap-3">
                <form method="POST" action="{{ route('admin.invitations.approve', $invitation->id) }}">
                    @csrf @method('PATCH')
                    <textarea name="admin_notes" class="form-input mb-3 w-full" placeholder="Catatan (opsional)" rows="2"></textarea>
                    <button type="submit" class="btn-success text-sm"><i class="fas fa-check mr-2"></i> Approve</button>
                </form>
                <form method="POST" action="{{ route('admin.invitations.reject', $invitation->id) }}">
                    @csrf @method('PATCH')
                    <input type="hidden" name="admin_notes">
                    <button type="submit" class="btn-danger text-sm" onclick="this.form.querySelector('[name=admin_notes]').value = prompt('Alasan penolakan:') || ''; return this.form.querySelector('[name=admin_notes]').value !== '';">
                        <i class="fas fa-times mr-2"></i> Reject
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>

    {{-- Sidebar --}}
    <div class="space-y-6">
        <div class="card p-6">
            <h3 class="font-bold text-base mb-4">Statistik</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-500"><i class="fas fa-eye mr-2"></i>Views</span>
                    <span class="font-bold">{{ number_format($invitation->view_count) }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-500"><i class="fas fa-users mr-2"></i>Tamu</span>
                    <span class="font-bold">{{ $invitation->guests->count() }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-500"><i class="fas fa-check-circle mr-2"></i>RSVP</span>
                    <span class="font-bold">{{ $invitation->rsvps->count() }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-500"><i class="fas fa-heart mr-2"></i>Ucapan</span>
                    <span class="font-bold">{{ $invitation->wishes->count() }}</span>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <h3 class="font-bold text-base mb-4">Info Client</h3>
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white font-bold">
                    {{ substr($invitation->user->name, 0, 1) }}
                </div>
                <div>
                    <p class="font-semibold text-sm">{{ $invitation->user->name }}</p>
                    <p class="text-xs text-slate-500">{{ $invitation->user->email }}</p>
                </div>
            </div>
        </div>

        @if($invitation->isActive())
        <div class="card p-6">
            <h3 class="font-bold text-base mb-3">Link Undangan</h3>
            <div class="p-3 bg-slate-800 rounded-lg text-xs text-indigo-400 break-all">
                {{ $invitation->getPublicUrl() }}
            </div>
        </div>
        @endif
    </div>
</div>

<div class="mt-4">
    <a href="{{ route('admin.invitations.index') }}" class="text-indigo-400 text-sm hover:text-indigo-300">
        <i class="fas fa-arrow-left mr-1"></i> Kembali ke daftar
    </a>
</div>
@endsection
