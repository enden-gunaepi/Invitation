@extends('layouts.client')

@section('title', 'Katalog Template')
@section('page-title', 'Katalog Template')
@section('page-subtitle', 'Lihat demo template sebelum dipakai')

@section('content')
    <div class="card p-6 mb-6 flex flex-col md:flex-row items-center justify-between gap-6 overflow-hidden relative" style="background: linear-gradient(135deg, rgba(255,255,255,.9), rgba(248,250,252,.8)); border: 1px solid rgba(148, 163, 184, .22);">
        <div class="space-y-2 z-10">
            <h2 class="text-lg font-bold text-primary">Katalog Template Undangan</h2>
            <p class="text-sm max-w-lg" style="color: var(--text-secondary);">Temukan berbagai macam desain eksklusif dan premium untuk hari bahagia Anda. Anda bisa melihat demo secara langsung sebelum memilih template yang cocok.</p>
        </div>
        <div class="shrink-0 z-10">
            <img src="{{ asset('assets/maskot/pilihtemplate.png') }}" alt="Pilih Template Mascot" class="h-28 w-auto drop-shadow-sm transition-transform duration-300 hover:scale-105" style="animation: float 4s ease-in-out infinite;">
        </div>
    </div>

    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }
    </style>

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
                        <div class="flex items-center gap-2">
                            <span class="badge badge-info">{{ $template->render_mode === \App\Models\Template::RENDER_MODE_BUILDER ? 'Builder' : 'Blade' }}</span>
                            @if ($template->is_premium)
                                <span class="badge badge-warning">Premium</span>
                            @endif
                        </div>
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
