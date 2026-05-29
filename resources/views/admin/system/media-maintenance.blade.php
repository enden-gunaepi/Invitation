@extends('layouts.admin')
@section('title', 'Media Maintenance')
@section('page-title', 'Media Maintenance')
@section('page-subtitle', 'Audit dan cleanup orphan media undangan')

@section('content')
<div x-data="{
    confirmOpen: false,
    confirmForm: null,
    openConfirm(form) {
        this.confirmForm = form;
        this.confirmOpen = true;
    },
    submitConfirmedForm() {
        if (!this.confirmForm) return;
        this.confirmOpen = false;
        this.confirmForm.submit();
    }
}">
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
        <form method="POST" action="{{ route('admin.system.media-maintenance.cleanup') }}" @submit.prevent="openConfirm($el)">
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

<div x-show="confirmOpen" x-cloak class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-950/45 backdrop-blur-sm" @click="confirmOpen = false"></div>

    <div x-show="confirmOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-3 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-3 scale-95"
        class="relative w-full max-w-md overflow-hidden rounded-[28px] border shadow-[0_24px_70px_rgba(15,23,42,0.18)]"
        style="background: var(--surface-lowest); border-color: var(--outline-variant);"
        @click.stop>
        <div class="p-6">
            <div class="flex items-start gap-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl"
                    style="background: rgba(245,158,11,0.12); color: #d97706;">
                    <i class="fas fa-broom"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="text-[11px] font-semibold uppercase tracking-[0.22em] text-slate-400">Konfirmasi</div>
                    <h3 class="mt-2 text-lg font-bold text-slate-900 dark:text-slate-100">Jalankan cleanup sekarang?</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-500">
                        File orphan yang terdeteksi akan dibersihkan dari storage dan aksi ini sebaiknya dilakukan saat admin sudah selesai review preview file.
                    </p>
                </div>
            </div>

            <div class="mt-5 rounded-2xl border p-4"
                style="background: var(--surface-container-low); border-color: var(--outline-variant);">
                <div class="flex items-center justify-between gap-4 text-sm">
                    <span class="text-slate-500">Total file</span>
                    <span class="font-semibold text-slate-800 dark:text-slate-100">{{ number_format($totals['count']) }}</span>
                </div>
                <div class="mt-3 flex items-center justify-between gap-4 text-sm">
                    <span class="text-slate-500">Images</span>
                    <span class="font-semibold text-slate-800 dark:text-slate-100">{{ number_format($totals['images']) }}</span>
                </div>
                <div class="mt-3 flex items-center justify-between gap-4 text-sm">
                    <span class="text-slate-500">Music</span>
                    <span class="font-semibold text-slate-800 dark:text-slate-100">{{ number_format($totals['music']) }}</span>
                </div>
                <div class="mt-3 flex items-center justify-between gap-4 text-sm">
                    <span class="text-slate-500">Potensi ruang kosong</span>
                    <span class="font-semibold text-slate-800 dark:text-slate-100">{{ number_format($totals['bytes'] / 1048576, 2) }} MB</span>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-3">
                <button type="button" class="btn btn-secondary" @click="confirmOpen = false">Kembali</button>
                <button type="button" class="btn btn-primary" @click="submitConfirmedForm()">
                    <i class="fas fa-check mr-2"></i> Ya, cleanup
                </button>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
