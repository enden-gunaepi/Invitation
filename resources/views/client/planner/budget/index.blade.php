@extends('layouts.client')

@section('title', 'Budget Tracker')
@section('page-title', '💰 Budget Tracker')
@section('page-subtitle', 'Rp' . number_format($summary['total_actual'], 0, ',', '.') . ' / Rp' . number_format($summary['total_budget'], 0, ',', '.'))

@section('content')
<div class="space-y-6">
    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card p-4">
            <p class="text-xs font-semibold mb-1" style="color: var(--text-secondary);">Total Budget</p>
            <p class="text-lg font-black">Rp{{ number_format($summary['total_budget'], 0, ',', '.') }}</p>
        </div>
        <div class="card p-4">
            <p class="text-xs font-semibold mb-1" style="color: var(--text-secondary);">Terpakai</p>
            <p class="text-lg font-black {{ $summary['status'] === 'danger' ? 'text-red-500' : ($summary['status'] === 'warning' ? 'text-amber-500' : 'text-emerald-500') }}">
                Rp{{ number_format($summary['total_actual'], 0, ',', '.') }}
            </p>
        </div>
        <div class="card p-4">
            <p class="text-xs font-semibold mb-1" style="color: var(--text-secondary);">Sisa</p>
            <p class="text-lg font-black {{ $summary['remaining'] <= 0 ? 'text-red-500' : '' }}">
                Rp{{ number_format($summary['remaining'], 0, ',', '.') }}
            </p>
        </div>
        <div class="card p-4">
            <p class="text-xs font-semibold mb-1" style="color: var(--text-secondary);">Persentase</p>
            <p class="text-lg font-black {{ $summary['status'] === 'danger' ? 'text-red-500' : ($summary['status'] === 'warning' ? 'text-amber-500' : 'text-emerald-500') }}">
                {{ $summary['percent'] }}%
            </p>
            <div class="w-full h-2 rounded-full bg-gray-100 mt-2">
                <div class="h-full rounded-full transition-all duration-500 {{ $summary['status'] === 'danger' ? 'bg-red-500' : ($summary['status'] === 'warning' ? 'bg-amber-500' : 'bg-emerald-500') }}"
                    style="width: {{ min(100, $summary['percent']) }}%"></div>
            </div>
        </div>
    </div>

    {{-- Over Budget Alert --}}
    @if($overBudgetCategories->isNotEmpty())
    <div class="card p-4 border-red-200 bg-red-50/50">
        <p class="text-sm font-bold text-red-600 mb-2"><i class="fas fa-exclamation-triangle mr-2"></i>Kategori Over Budget:</p>
        <div class="space-y-1">
            @foreach($overBudgetCategories as $cat)
            <p class="text-xs text-red-500">• <strong>{{ $cat->name }}</strong>: Rp{{ number_format($cat->actual_amount, 0, ',', '.') }} / Rp{{ number_format($cat->estimated_amount, 0, ',', '.') }} ({{ $cat->progress_percent }}%)</p>
            @endforeach
        </div>
        <p class="text-xs mt-2" style="color: var(--text-secondary);">💡 Tips: Review pengeluaran dan cari alternatif yang lebih hemat</p>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Categories & Items --}}
        <div class="lg:col-span-2 space-y-4">
            @forelse($categories as $category)
            <div class="card overflow-hidden" x-data="{ open: false }">
                <div class="px-5 py-4 flex items-center gap-3 cursor-pointer hover:bg-black/[0.02] transition" @click="open = !open">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white shrink-0" style="background: {{ $category->color }};">
                        <i class="fas {{ $category->icon }} text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-bold">{{ $category->name }}</p>
                            <p class="text-xs font-bold {{ $category->isOverBudget() ? 'text-red-500' : '' }}">
                                Rp{{ number_format($category->actual_amount, 0, ',', '.') }} / Rp{{ number_format($category->estimated_amount, 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="w-full h-1.5 rounded-full bg-gray-100 mt-1.5">
                            <div class="h-full rounded-full transition-all duration-500" style="width: {{ min(100, $category->progress_percent) }}%; background: {{ $category->color }};"></div>
                        </div>
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform" :class="open ? 'rotate-180' : ''" style="color: var(--text-tertiary);"></i>
                </div>

                <div x-show="open" x-transition class="border-t px-5 py-4 space-y-3" style="border-color: var(--border);">
                    @foreach($category->items as $item)
                    <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 transition">
                        <div class="flex-1">
                            <p class="text-sm font-semibold">{{ $item->name }}</p>
                            @if($item->vendor_name)<p class="text-xs" style="color: var(--text-secondary);">{{ $item->vendor_name }}</p>@endif
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-bold">Rp{{ number_format($item->actual_amount, 0, ',', '.') }}</p>
                            @if($item->estimated_amount > 0)
                            <p class="text-xs" style="color: var(--text-tertiary);">est. Rp{{ number_format($item->estimated_amount, 0, ',', '.') }}</p>
                            @endif
                        </div>
                        <form method="POST" action="{{ route('client.planner.budget.items.destroy', $item) }}" onsubmit="return confirm('Hapus item ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs p-1 rounded text-gray-300 hover:text-red-400 transition"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                    @endforeach

                    {{-- Add item form --}}
                    <form method="POST" action="{{ route('client.planner.budget.items.store') }}" class="flex flex-wrap gap-2 mt-2 pt-2 border-t" style="border-color: var(--border);">
                        @csrf
                        <input type="hidden" name="wp_budget_category_id" value="{{ $category->id }}">
                        <input type="text" name="name" class="form-input text-xs flex-1 min-w-[120px]" placeholder="Nama item" required>
                        <input type="number" name="actual_amount" class="form-input text-xs w-32" placeholder="Jumlah (Rp)" min="0">
                        <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-bold text-white bg-gradient-to-r from-emerald-500 to-teal-500">
                            <i class="fas fa-plus"></i>
                        </button>
                    </form>

                    <form method="POST" action="{{ route('client.planner.budget.categories.destroy', $category) }}" onsubmit="return confirm('Hapus kategori {{ $category->name }} beserta semua itemnya?')" class="text-right">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs text-red-400 hover:text-red-600 transition"><i class="fas fa-trash mr-1"></i>Hapus kategori</button>
                    </form>
                </div>
            </div>
            @empty
            <div class="card p-8 text-center" style="color: var(--text-secondary);">
                <i class="fas fa-wallet text-4xl mb-3 opacity-30"></i>
                <p class="text-sm">Belum ada kategori budget</p>
            </div>
            @endforelse
        </div>

        {{-- Right: Add Category --}}
        <div>
            <div class="card p-5">
                <h3 class="font-bold text-sm mb-4"><i class="fas fa-plus-circle text-amber-500 mr-2"></i>Tambah Kategori</h3>
                <form method="POST" action="{{ route('client.planner.budget.categories.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold mb-1">Nama Kategori *</label>
                        <input type="text" name="name" class="form-input w-full text-sm" placeholder="Contoh: Transportation" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1">Estimasi Budget (Rp) *</label>
                        <input type="number" name="estimated_amount" class="form-input w-full text-sm" min="0" required>
                    </div>
                    <button type="submit" class="w-full py-2.5 rounded-xl font-bold text-white text-sm
                        bg-gradient-to-r from-amber-500 to-yellow-500 hover:from-amber-600 hover:to-yellow-600 transition shadow-lg shadow-amber-500/20">
                        <i class="fas fa-plus mr-1"></i> Tambah
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
