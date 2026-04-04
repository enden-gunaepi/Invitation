@extends('layouts.client')

@section('title', 'Smart Checklist')
@section('page-title', '📋 Smart Checklist')
@section('page-subtitle', $stats['done'] . '/' . $stats['total'] . ' selesai')

@section('content')
<div class="space-y-6">
    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
        @foreach([
            ['label' => 'Total', 'value' => $stats['total'], 'color' => 'text-gray-700', 'bg' => 'bg-gray-100'],
            ['label' => 'Belum', 'value' => $stats['todo'], 'color' => 'text-slate-600', 'bg' => 'bg-slate-100'],
            ['label' => 'Proses', 'value' => $stats['in_progress'], 'color' => 'text-blue-600', 'bg' => 'bg-blue-50'],
            ['label' => 'Selesai', 'value' => $stats['done'], 'color' => 'text-emerald-600', 'bg' => 'bg-emerald-50'],
            ['label' => 'Terlambat', 'value' => $stats['overdue'], 'color' => 'text-red-600', 'bg' => 'bg-red-50'],
        ] as $stat)
        <div class="card p-3 text-center">
            <p class="text-xl font-black {{ $stat['color'] }}">{{ $stat['value'] }}</p>
            <p class="text-xs font-medium" style="color: var(--text-secondary);">{{ $stat['label'] }}</p>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left: Checklist --}}
        <div class="lg:col-span-2">
            {{-- Filters --}}
            <div class="flex flex-wrap items-center gap-2 mb-4">
                <a href="{{ route('client.planner.checklist.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition
                    {{ !request('status') ? 'bg-rose-600 text-white' : 'bg-gray-100 hover:bg-gray-200' }}">Semua</a>
                @foreach(['todo' => 'Belum', 'in_progress' => 'Proses', 'done' => 'Selesai'] as $key => $label)
                <a href="{{ route('client.planner.checklist.index', ['status' => $key, 'category' => request('category')]) }}"
                    class="px-3 py-1.5 rounded-lg text-xs font-semibold transition
                    {{ request('status') === $key ? 'bg-rose-600 text-white' : 'bg-gray-100 hover:bg-gray-200' }}">{{ $label }}</a>
                @endforeach
                @if($categories->isNotEmpty())
                <select onchange="window.location.href=this.value" class="form-input py-1.5 px-2 text-xs rounded-lg ml-auto" style="width: auto;">
                    <option value="{{ route('client.planner.checklist.index', ['status' => request('status')]) }}">Semua Kategori</option>
                    @foreach($categories as $cat)
                    <option value="{{ route('client.planner.checklist.index', ['status' => request('status'), 'category' => $cat]) }}"
                        {{ request('category') === $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                    @endforeach
                </select>
                @endif
            </div>

            {{-- Items --}}
            <div class="space-y-2" id="checklist-items">
                @forelse($items as $item)
                <div class="card p-4 flex items-start gap-3 transition-all duration-200 hover:shadow-md
                    {{ $item->isOverdue() ? 'border-red-200 bg-red-50/50' : '' }}
                    {{ $item->isUrgent() ? 'border-amber-200 bg-amber-50/30' : '' }}
                    {{ $item->status === 'done' ? 'opacity-60' : '' }}" data-id="{{ $item->id }}">
                    <form method="POST" action="{{ route('client.planner.checklist.update', $item) }}" class="shrink-0 mt-0.5">
                        @csrf @method('PATCH')
                        @if($item->status === 'done')
                            <input type="hidden" name="status" value="todo">
                            <button type="submit" class="w-5 h-5 rounded-md bg-emerald-500 flex items-center justify-center">
                                <i class="fas fa-check text-white text-xs"></i>
                            </button>
                        @elseif($item->status === 'in_progress')
                            <input type="hidden" name="status" value="done">
                            <button type="submit" class="w-5 h-5 rounded-md border-2 border-blue-400 bg-blue-50 flex items-center justify-center hover:bg-emerald-100 transition">
                                <i class="fas fa-minus text-blue-400 text-xs"></i>
                            </button>
                        @else
                            <input type="hidden" name="status" value="in_progress">
                            <button type="submit" class="w-5 h-5 rounded-md border-2 border-gray-300 hover:border-blue-400 hover:bg-blue-50 transition"></button>
                        @endif
                    </form>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold {{ $item->status === 'done' ? 'line-through' : '' }}">{{ $item->title }}</p>
                        @if($item->description)
                        <p class="text-xs mt-0.5" style="color: var(--text-secondary);">{{ $item->description }}</p>
                        @endif
                        <div class="flex items-center gap-2 mt-1.5">
                            <span class="px-2 py-0.5 rounded text-xs font-medium bg-gray-100">{{ ucfirst($item->category) }}</span>
                            @if($item->deadline)
                                @if($item->isOverdue())
                                    <span class="text-xs font-semibold text-red-500"><i class="fas fa-exclamation-circle mr-1"></i>Terlambat {{ abs(now()->diffInDays($item->deadline)) }}h</span>
                                @elseif($item->isUrgent())
                                    <span class="text-xs font-semibold text-amber-500"><i class="fas fa-clock mr-1"></i>{{ $item->deadline->diffForHumans() }}</span>
                                @else
                                    <span class="text-xs" style="color: var(--text-tertiary);"><i class="fas fa-calendar mr-1"></i>{{ $item->deadline->format('d M Y') }}</span>
                                @endif
                            @endif
                            @if($item->status === 'done' && $item->completed_at)
                                <span class="text-xs text-emerald-500"><i class="fas fa-check mr-1"></i>{{ $item->completed_at->diffForHumans() }}</span>
                            @endif
                        </div>
                    </div>
                    <form method="POST" action="{{ route('client.planner.checklist.destroy', $item) }}" onsubmit="return confirm('Hapus item ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs p-1 rounded hover:bg-red-50 text-gray-300 hover:text-red-400 transition">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
                @empty
                <div class="card p-8 text-center" style="color: var(--text-secondary);">
                    <i class="fas fa-clipboard-check text-4xl mb-3 opacity-30"></i>
                    <p class="text-sm">Tidak ada item untuk filter ini</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Right: Add New Item --}}
        <div>
            <div class="card p-5">
                <h3 class="font-bold text-sm mb-4"><i class="fas fa-plus-circle text-emerald-500 mr-2"></i>Tambah Checklist</h3>
                <form method="POST" action="{{ route('client.planner.checklist.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold mb-1">Judul *</label>
                        <input type="text" name="title" class="form-input w-full text-sm" placeholder="Contoh: Booking photographer" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1">Deskripsi</label>
                        <textarea name="description" class="form-input w-full text-sm" rows="2" placeholder="Optional"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1">Kategori</label>
                        <select name="category" class="form-input w-full text-sm">
                            @foreach(['general', 'venue', 'catering', 'dekor', 'foto', 'busana', 'entertainment', 'undangan', 'souvenir', 'lainnya'] as $cat)
                            <option value="{{ $cat }}">{{ ucfirst($cat) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1">Deadline</label>
                        <input type="date" name="deadline" class="form-input w-full text-sm">
                    </div>
                    <button type="submit" class="w-full py-2.5 rounded-xl font-bold text-white text-sm
                        bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 transition shadow-lg shadow-emerald-500/20">
                        <i class="fas fa-plus mr-1"></i> Tambah
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
