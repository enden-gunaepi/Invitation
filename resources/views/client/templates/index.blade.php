@extends('layouts.client')

@section('title', 'Katalog Template')
@section('page-title', 'Katalog Template')
@section('page-subtitle', 'Lihat demo template sebelum dipakai')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <p class="text-sm" style="color: var(--text-secondary);">{{ $templates->total() }} template aktif</p>
        <a href="{{ $hasActivePackage ? route('client.invitations.create') : route('client.packages.select') }}" class="btn btn-primary text-sm">
            <i class="fas {{ $hasActivePackage ? 'fa-plus' : 'fa-box-open' }}"></i> {{ $hasActivePackage ? 'Buat Undangan' : 'Pilih Paket Dulu' }}
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($templates as $template)
            <article class="card overflow-hidden">
                <div class="h-44 bg-gradient-to-br from-slate-200 to-slate-100 flex items-center justify-center">
                    @if ($template->thumbnail)
                        <img src="{{ asset('storage/' . $template->thumbnail) }}" alt="{{ $template->name }}" class="w-full h-full object-cover">
                    @else
                        <i class="fas fa-palette text-4xl opacity-40" style="color: var(--accent);"></i>
                    @endif
                </div>
                <div class="p-5">
                    <div class="flex items-center justify-between gap-3 mb-3">
                        <h3 class="font-semibold text-sm">{{ $template->name }}</h3>
                        @if ($template->is_premium)
                            <span class="badge badge-warning">Premium</span>
                        @endif
                    </div>
                    <p class="text-xs mb-4" style="color: var(--text-secondary);">
                        Kategori: {{ ucfirst($template->category) }}
                    </p>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('templates.demo', $template->slug) }}" target="_blank" class="btn btn-secondary text-xs flex-1 justify-center">
                            <i class="fas fa-eye"></i> Demo
                        </a>
                        <a href="{{ $hasActivePackage ? route('client.invitations.create', ['template_id' => $template->id]) : route('client.packages.select') }}" class="btn btn-primary text-xs flex-1 justify-center">
                            <i class="fas fa-check"></i> Pakai
                        </a>
                    </div>
                </div>
            </article>
        @empty
            <div class="col-span-full text-center py-12" style="color: var(--text-secondary);">
                Belum ada template aktif.
            </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $templates->links() }}</div>
@endsection
