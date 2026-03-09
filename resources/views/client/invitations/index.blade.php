@extends('layouts.client')
@section('title', 'Undangan Saya')
@section('page-title', 'Undangan Saya')
@section('page-subtitle', 'Kelola semua undangan digital Anda')

@section('content')
<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-slate-400">{{ $invitations->total() }} undangan</p>
    <a href="{{ route('client.invitations.create') }}" class="btn-primary text-sm">
        <i class="fas fa-plus mr-2"></i> Buat Undangan
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
    <div class="col-span-3 text-center py-16">
        <i class="fas fa-envelope-open text-5xl text-slate-600 mb-4"></i>
        <h3 class="text-lg font-bold text-slate-400 mb-2">Belum Ada Undangan</h3>
        <p class="text-sm text-slate-500 mb-6">Mulai buat undangan digital pertama Anda!</p>
        <a href="{{ route('client.invitations.create') }}" class="btn-primary text-sm">
            <i class="fas fa-plus mr-2"></i> Buat Undangan Sekarang
        </a>
    </div>
    @endforelse
</div>

<div class="mt-6">{{ $invitations->links() }}</div>
@endsection
