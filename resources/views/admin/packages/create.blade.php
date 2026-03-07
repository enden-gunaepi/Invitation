@extends('layouts.admin')
@section('title', isset($package) ? 'Edit Paket' : 'Tambah Paket')
@section('page-title', isset($package) ? 'Edit Paket' : 'Tambah Paket')

@section('content')
<div class="max-w-2xl">
    <div class="card p-6">
        <form method="POST" action="{{ isset($package) ? route('admin.packages.update', $package) : route('admin.packages.store') }}">
            @csrf
            @if(isset($package)) @method('PUT') @endif

            <div class="mb-5">
                <label class="form-label">Nama Paket</label>
                <input type="text" name="name" value="{{ old('name', $package->name ?? '') }}" class="form-input" required>
            </div>
            <div class="mb-5">
                <label class="form-label">Deskripsi</label>
                <textarea name="description" class="form-input" rows="3">{{ old('description', $package->description ?? '') }}</textarea>
            </div>
            <div class="grid grid-cols-3 gap-5 mb-5">
                <div>
                    <label class="form-label">Harga (Rp)</label>
                    <input type="number" name="price" value="{{ old('price', $package->price ?? 0) }}" class="form-input" min="0" step="1000" required>
                </div>
                <div>
                    <label class="form-label">Max Tamu</label>
                    <input type="number" name="max_guests" value="{{ old('max_guests', $package->max_guests ?? 100) }}" class="form-input" min="1" required>
                </div>
                <div>
                    <label class="form-label">Max Foto</label>
                    <input type="number" name="max_photos" value="{{ old('max_photos', $package->max_photos ?? 10) }}" class="form-input" min="1" required>
                </div>
            </div>
            <div class="mb-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $package->is_active ?? true) ? 'checked' : '' }} class="w-4 h-4 rounded bg-slate-800 border-slate-600 text-indigo-500">
                    <span class="text-sm text-slate-400">Aktif</span>
                </label>
            </div>
            <div class="flex items-center gap-3">
                <button type="submit" class="btn-primary text-sm"><i class="fas fa-save mr-2"></i> Simpan</button>
                <a href="{{ route('admin.packages.index') }}" class="btn-outline text-sm">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
