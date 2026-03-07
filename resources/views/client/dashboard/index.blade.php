@extends('layouts.client')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Selamat datang, ' . auth()->user()->name)

@section('content')
{{-- Stats Grid --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-8">
    <div class="stat-card">
        <div class="flex items-center justify-between mb-3">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500/20 to-cyan-500/20 flex items-center justify-center text-emerald-400 text-lg">
                <i class="fas fa-envelope"></i>
            </div>
        </div>
        <p class="text-2xl font-bold mb-1">{{ $stats['total_invitations'] }}</p>
        <p class="text-xs text-slate-500">Total Undangan</p>
    </div>
    <div class="stat-card">
        <div class="flex items-center justify-between mb-3">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500/20 to-purple-500/20 flex items-center justify-center text-indigo-400 text-lg">
                <i class="fas fa-check-circle"></i>
            </div>
            <span class="text-xs text-emerald-400 font-semibold">{{ $stats['attending'] }} hadir</span>
        </div>
        <p class="text-2xl font-bold mb-1">{{ $stats['total_rsvps'] }}</p>
        <p class="text-xs text-slate-500">Total RSVP</p>
    </div>
    <div class="stat-card">
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
                    <p class="text-xs text-slate-500">{{ $inv->event_date->format('d M Y') }} · {{ $inv->venue_name }}</p>
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

    {{-- Quick Start --}}
    <div class="card p-6">
        <h3 class="font-bold text-base mb-4">Mulai Buat Undangan</h3>
        <p class="text-sm text-slate-400 mb-6">Buat undangan digital yang indah dalam hitungan menit.</p>
        <a href="{{ route('client.invitations.create') }}" class="btn-primary w-full text-center block py-3">
            <i class="fas fa-plus mr-2"></i> Buat Undangan Baru
        </a>
        <div class="mt-6 space-y-3">
            <div class="flex items-center gap-3 text-sm text-slate-400">
                <div class="w-6 h-6 rounded-full bg-emerald-500/20 flex items-center justify-center">
                    <i class="fas fa-check text-emerald-400 text-xs"></i>
                </div>
                Pilih template premium
            </div>
            <div class="flex items-center gap-3 text-sm text-slate-400">
                <div class="w-6 h-6 rounded-full bg-emerald-500/20 flex items-center justify-center">
                    <i class="fas fa-check text-emerald-400 text-xs"></i>
                </div>
                Kustomisasi sesuai keinginan
            </div>
            <div class="flex items-center gap-3 text-sm text-slate-400">
                <div class="w-6 h-6 rounded-full bg-emerald-500/20 flex items-center justify-center">
                    <i class="fas fa-check text-emerald-400 text-xs"></i>
                </div>
                Share via WhatsApp / link
            </div>
        </div>
    </div>
</div>
@endsection
