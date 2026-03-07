@extends('layouts.client')
@section('title', 'Buat Undangan')
@section('page-title', 'Buat Undangan Baru')
@section('page-subtitle', 'Isi form di bawah untuk membuat undangan digital')

@section('content')
<div class="max-w-3xl">
    <div class="card p-6">
        <form method="POST" action="{{ route('client.invitations.store') }}" enctype="multipart/form-data">
            @csrf
            {{-- Step 1: Pilih Template & Paket --}}
            <h3 class="font-bold text-base mb-4 text-emerald-400"><i class="fas fa-layer-group mr-2"></i> Template & Paket</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-6">
                <div>
                    <label class="form-label">Template</label>
                    <select name="template_id" class="form-input" required>
                        <option value="">Pilih Template</option>
                        @foreach($templates as $t)
                            <option value="{{ $t->id }}" {{ old('template_id') == $t->id ? 'selected' : '' }}>
                                {{ $t->name }} ({{ ucfirst($t->category) }}) {{ $t->is_premium ? '⭐' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('template_id') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Paket</label>
                    <select name="package_id" class="form-input" required>
                        <option value="">Pilih Paket</option>
                        @foreach($packages as $p)
                            <option value="{{ $p->id }}" {{ old('package_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->name }} — Rp{{ number_format($p->price, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                    @error('package_id') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <hr class="border-[rgba(16,185,129,0.1)] my-6">

            {{-- Step 2: Info Acara --}}
            <h3 class="font-bold text-base mb-4 text-emerald-400"><i class="fas fa-calendar-alt mr-2"></i> Informasi Acara</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                <div>
                    <label class="form-label">Jenis Acara</label>
                    <select name="event_type" class="form-input" required>
                        @foreach(['wedding', 'birthday', 'graduation', 'corporate', 'other'] as $type)
                            <option value="{{ $type }}" {{ old('event_type') === $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Judul Acara</label>
                    <input type="text" name="title" value="{{ old('title') }}" class="form-input" required placeholder="Pernikahan Ahmad & Siti">
                    @error('title') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-5">
                <div>
                    <label class="form-label">Mempelai Pria</label>
                    <input type="text" name="groom_name" value="{{ old('groom_name') }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">Mempelai Wanita</label>
                    <input type="text" name="bride_name" value="{{ old('bride_name') }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">Host (non-wedding)</label>
                    <input type="text" name="host_name" value="{{ old('host_name') }}" class="form-input">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                <div>
                    <label class="form-label">Tanggal Acara</label>
                    <input type="date" name="event_date" value="{{ old('event_date') }}" class="form-input" required>
                    @error('event_date') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Waktu Acara</label>
                    <input type="time" name="event_time" value="{{ old('event_time') }}" class="form-input" required>
                </div>
            </div>

            <hr class="border-[rgba(16,185,129,0.1)] my-6">

            {{-- Step 3: Lokasi --}}
            <h3 class="font-bold text-base mb-4 text-emerald-400"><i class="fas fa-map-marker-alt mr-2"></i> Lokasi</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                <div>
                    <label class="form-label">Nama Tempat</label>
                    <input type="text" name="venue_name" value="{{ old('venue_name') }}" class="form-input" required placeholder="Hotel Grand Ballroom">
                </div>
                <div>
                    <label class="form-label">Link Google Maps</label>
                    <input type="url" name="google_maps_url" value="{{ old('google_maps_url') }}" class="form-input" placeholder="https://maps.google.com/...">
                </div>
            </div>
            <div class="mb-5">
                <label class="form-label">Alamat Lengkap</label>
                <textarea name="venue_address" class="form-input" rows="2" required>{{ old('venue_address') }}</textarea>
            </div>

            <hr class="border-[rgba(16,185,129,0.1)] my-6">

            {{-- Step 4: Teks --}}
            <h3 class="font-bold text-base mb-4 text-emerald-400"><i class="fas fa-align-left mr-2"></i> Teks Undangan</h3>
            <div class="mb-5">
                <label class="form-label">Teks Pembuka</label>
                <textarea name="opening_text" class="form-input" rows="3" placeholder="Bismillahirrahmanirrahim...">{{ old('opening_text') }}</textarea>
            </div>
            <div class="mb-5">
                <label class="form-label">Teks Penutup</label>
                <textarea name="closing_text" class="form-input" rows="3" placeholder="Merupakan suatu kehormatan...">{{ old('closing_text') }}</textarea>
            </div>
            <div class="mb-6">
                <label class="form-label">Cover Photo</label>
                <input type="file" name="cover_photo" class="form-input" accept="image/*">
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="btn-primary text-sm"><i class="fas fa-save mr-2"></i> Simpan Undangan</button>
                <a href="{{ route('client.invitations.index') }}" class="btn-outline text-sm">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
