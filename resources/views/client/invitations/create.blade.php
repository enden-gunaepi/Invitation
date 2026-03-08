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
            <h3 class="font-bold text-base mb-4" style="color: var(--accent);"><i class="fas fa-layer-group mr-2"></i> Template & Paket</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-4">
                <div>
                    <label class="form-label">Template</label>
                    <select name="template_id" class="form-input" required>
                        <option value="">Pilih Template</option>
                        @foreach($templates as $t)
                            <option value="{{ $t->id }}" {{ old('template_id') == $t->id ? 'selected' : '' }}>
                                {{ $t->name }} ({{ ucfirst($t->category) }}) {{ $t->is_premium ? '⭐ Premium' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('template_id') <p class="text-xs mt-1" style="color: var(--danger);">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Paket</label>
                    <select name="package_id" class="form-input" required id="packageSelect" onchange="updatePackageInfo()">
                        <option value="">Pilih Paket</option>
                        @foreach($packages as $p)
                            <option value="{{ $p->id }}" data-guests="{{ $p->max_guests }}" data-photos="{{ $p->max_photos }}" data-features='@json($p->features)' {{ old('package_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->name }} — Rp{{ number_format($p->price, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                    @error('package_id') <p class="text-xs mt-1" style="color: var(--danger);">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Package preview --}}
            <div id="packageInfo" class="p-4 rounded-lg mb-6" style="background: var(--bg-tertiary); display: none;">
                <div class="grid grid-cols-2 gap-4 mb-3">
                    <div class="text-xs"><span style="color: var(--text-secondary);">Max Tamu:</span> <strong id="pkgGuests">-</strong></div>
                    <div class="text-xs"><span style="color: var(--text-secondary);">Max Foto:</span> <strong id="pkgPhotos">-</strong></div>
                </div>
                <div id="pkgFeatures" class="flex flex-wrap gap-1"></div>
            </div>

            <div class="p-3 rounded-lg mb-6 text-xs" style="background: rgba(255,149,0,0.08); color: var(--warning); display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-info-circle"></i>
                <span>Template bertanda ⭐ Premium hanya bisa digunakan dengan paket Premium atau Exclusive.</span>
            </div>

            <hr style="border-color: var(--border);" class="my-6">

            {{-- Step 2: Info Acara --}}
            <h3 class="font-bold text-base mb-4" style="color: var(--accent);"><i class="fas fa-calendar-alt mr-2"></i> Informasi Acara</h3>
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
                    @error('title') <p class="text-xs mt-1" style="color: var(--danger);">{{ $message }}</p> @enderror
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
                    @error('event_date') <p class="text-xs mt-1" style="color: var(--danger);">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Waktu Acara</label>
                    <input type="time" name="event_time" value="{{ old('event_time') }}" class="form-input" required>
                </div>
            </div>

            <hr style="border-color: var(--border);" class="my-6">

            {{-- Step 3: Lokasi --}}
            <h3 class="font-bold text-base mb-4" style="color: var(--accent);"><i class="fas fa-map-marker-alt mr-2"></i> Lokasi</h3>
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

            <hr style="border-color: var(--border);" class="my-6">

            {{-- Step 4: Teks --}}
            <h3 class="font-bold text-base mb-4" style="color: var(--accent);"><i class="fas fa-align-left mr-2"></i> Teks Undangan</h3>
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
                <button type="submit" class="btn btn-primary text-sm"><i class="fas fa-save mr-2"></i> Simpan Undangan</button>
                <a href="{{ route('client.invitations.index') }}" class="btn btn-secondary text-sm">Batal</a>
            </div>
        </form>
    </div>
</div>

<script>
function updatePackageInfo() {
    const sel = document.getElementById('packageSelect');
    const opt = sel.options[sel.selectedIndex];
    const info = document.getElementById('packageInfo');
    if (!opt.value) { info.style.display = 'none'; return; }
    info.style.display = 'block';
    document.getElementById('pkgGuests').textContent = opt.dataset.guests + ' orang';
    document.getElementById('pkgPhotos').textContent = opt.dataset.photos + ' foto';
    const features = JSON.parse(opt.dataset.features || '[]');
    const container = document.getElementById('pkgFeatures');
    container.innerHTML = features.map(f => `<span style="background:var(--accent-bg);color:var(--accent);padding:2px 8px;border-radius:4px;font-size:11px;font-weight:600;">${f}</span>`).join('');
}
document.addEventListener('DOMContentLoaded', updatePackageInfo);
</script>
@endsection
