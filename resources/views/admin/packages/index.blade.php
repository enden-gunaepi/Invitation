@extends('layouts.admin')
@section('title', 'Kelola Paket')
@section('page-title', 'Kelola Paket')
@section('page-subtitle', 'Manajemen paket harga')

@section('content')
<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-slate-400">{{ $packages->total() }} paket</p>
    <a href="{{ route('admin.packages.create') }}" class="btn-primary text-sm"><i class="fas fa-plus mr-2"></i> Tambah Paket</a>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
    @forelse($packages as $pkg)
    <div class="card p-6 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-indigo-500 to-purple-500"></div>
        <div class="flex items-center justify-between mb-4">
            <h4 class="font-bold text-lg">{{ $pkg->name }}</h4>
            <span class="badge {{ $pkg->is_active ? 'badge-active' : 'badge-draft' }}">
                {{ $pkg->is_active ? 'Aktif' : 'Nonaktif' }}
            </span>
        </div>
        <p class="text-3xl font-bold mb-1">
            Rp{{ number_format($pkg->price, 0, ',', '.') }}
        </p>
        <p class="text-xs text-slate-500 mb-4">per undangan</p>
        <div class="space-y-2 mb-5">
            <div class="text-sm text-slate-400">
                <i class="fas fa-users text-indigo-400 mr-2 w-4"></i> Max {{ $pkg->max_guests }} tamu
            </div>
            <div class="text-sm text-slate-400">
                <i class="fas fa-image text-indigo-400 mr-2 w-4"></i> Max {{ $pkg->max_photos }} foto
            </div>
            @if($pkg->features)
                @foreach($pkg->features as $feature)
                <div class="text-sm text-slate-400">
                    <i class="fas fa-check text-emerald-400 mr-2 w-4"></i> {{ $feature }}
                </div>
                @endforeach
            @endif
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.packages.edit', $pkg) }}" class="btn-outline text-xs py-2 px-4 flex-1 text-center">Edit</a>
            <form method="POST" action="{{ route('admin.packages.destroy', $pkg) }}" onsubmit="return confirm('Hapus paket?')">
                @csrf @method('DELETE')
                <button class="text-red-400 hover:text-red-300 text-sm px-3 py-2"><i class="fas fa-trash"></i></button>
            </form>
        </div>
    </div>
    @empty
    <div class="col-span-3 text-center py-12 text-slate-500">
        <i class="fas fa-box text-4xl mb-4 opacity-40"></i>
        <p>Belum ada paket</p>
    </div>
    @endforelse
</div>

<div class="mt-6">{{ $packages->links() }}</div>
@endsection
