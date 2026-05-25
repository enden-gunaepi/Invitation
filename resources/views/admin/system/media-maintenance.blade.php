@extends('layouts.admin')
@section('title', 'Media Maintenance')
@section('page-title', 'Media Maintenance')
@section('page-subtitle', 'Audit dan cleanup orphan media undangan')

@section('content')
<div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
    <div class="card stat-card">
        <div class="stat-value">{{ number_format($totals['count']) }}</div>
        <div class="stat-label">Orphan Files</div>
    </div>
    <div class="card stat-card">
        <div class="stat-value">{{ number_format($totals['images']) }}</div>
        <div class="stat-label">Images</div>
    </div>
    <div class="card stat-card">
        <div class="stat-value">{{ number_format($totals['music']) }}</div>
        <div class="stat-label">Music</div>
    </div>
    <div class="card stat-card">
        <div class="stat-value">{{ number_format($totals['bytes'] / 1048576, 2) }} MB</div>
        <div class="stat-label">Potential Space</div>
    </div>
</div>

<div class="card overflow-hidden">
    <div class="px-6 py-4 border-b flex flex-col gap-3 md:flex-row md:items-center md:justify-between" style="border-color: var(--border);">
        <div>
            <h3 class="font-bold text-base">Orphan Media Preview</h3>
            <p class="text-sm mt-1" style="color: var(--text-secondary);">
                Menampilkan maksimal 100 file pertama. Scheduler otomatis tetap berjalan setiap hari pukul 01:30.
            </p>
        </div>
        <form method="POST" action="{{ route('admin.system.media-maintenance.cleanup') }}" onsubmit="return confirm('Jalankan cleanup orphan media sekarang?')">
            @csrf
            <button type="submit" class="btn btn-primary btn-sm" {{ $totals['count'] === 0 ? 'disabled' : '' }}>
                <i class="fas fa-broom mr-2"></i>Jalankan Cleanup
            </button>
        </form>
    </div>

    <div class="p-4">
        @if ($totals['count'] === 0)
            <p class="text-sm py-8 text-center" style="color: var(--text-secondary);">Tidak ada orphan media. Storage dalam kondisi bersih.</p>
        @else
            <div class="space-y-2">
                @foreach ($orphanFiles as $file)
                    <div class="p-3 rounded-lg flex items-center justify-between gap-3" style="background: var(--bg-tertiary);">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold truncate">{{ $file['path'] }}</p>
                            <p class="text-xs mt-1 uppercase tracking-[0.18em]" style="color: var(--text-secondary);">
                                {{ $file['type'] }} · {{ number_format($file['size'] / 1024, 1) }} KB
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($totals['count'] > $orphanFiles->count())
                <p class="text-xs mt-4" style="color: var(--text-secondary);">
                    Masih ada {{ number_format($totals['count'] - $orphanFiles->count()) }} file lain yang tidak ditampilkan di preview ini.
                </p>
            @endif
        @endif
    </div>
</div>
@endsection
