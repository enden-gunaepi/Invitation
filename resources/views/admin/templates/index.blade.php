@extends('layouts.admin')

@section('title', 'Template')
@section('page-title', 'Kelola Template')
@section('page-subtitle', 'Manajemen template undangan')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <p class="text-sm text-slate-400">{{ $templates->total() }} template terdaftar</p>
        <a href="{{ route('admin.templates.create') }}" class="btn-primary text-sm py-1 px-2 rounded-md">
            <i class="fas fa-plus mr-2"></i> Tambah Template
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($templates as $template)
            <div class="card overflow-hidden group">
                <div
                    class="h-40 bg-gradient-to-br from-indigo-500/20 to-purple-500/20 flex items-center justify-center relative">
                    @if ($template->thumbnail)
                        <img src="{{ asset('storage/' . $template->thumbnail) }}" class="w-full h-full object-cover"
                            alt="{{ $template->name }}">
                    @else
                        <i class="fas fa-palette text-4xl text-indigo-400/40"></i>
                    @endif
                    @if ($template->is_premium)
                        <span
                            class="absolute top-3 right-3 bg-amber-500/20 text-amber-400 text-xs font-bold px-3 py-1 rounded-full">
                            <i class="fas fa-crown mr-1"></i> Premium
                        </span>
                    @endif
                </div>
                <div class="p-5">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-bold text-sm">{{ $template->name }}</h4>
                        <span class="badge {{ $template->is_active ? 'badge-active' : 'badge-draft' }}">
                            {{ $template->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mb-4">
                        <i class="fas fa-tag mr-1"></i> {{ ucfirst($template->category) }}
                    </p>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('templates.demo', $template->slug) }}" target="_blank"
                            class="btn-outline text-xs py-2 px-3 text-center">Demo</a>
                        <a href="{{ route('admin.templates.edit', $template) }}"
                            class="btn-outline text-xs py-2 px-4 flex-1 text-center">Edit</a>
                        <form method="POST" action="{{ route('admin.templates.destroy', $template) }}"
                            onsubmit="return confirm('Hapus template?')">
                            @csrf @method('DELETE')
                            <button class="text-red-400 hover:text-red-300 text-sm px-3 py-2"><i
                                    class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center py-12 text-slate-500">
                <i class="fas fa-palette text-4xl mb-4 opacity-40"></i>
                <p>Belum ada template</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $templates->links() }}</div>
@endsection
