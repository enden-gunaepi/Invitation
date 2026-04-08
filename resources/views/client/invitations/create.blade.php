@extends('layouts.client')
@section('title', 'Buat Undangan')
@section('page-title', 'Buat Undangan Baru')
@section('page-subtitle', 'Isi form di bawah untuk membuat undangan digital')

@section('content')

@push('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
@endpush

<div class="max-w-3xl">
    <div class="card p-6">
        @if($errors->any())
            <div class="mb-5 rounded-lg border px-4 py-3" style="border-color: var(--danger); background: rgba(239,68,68,.08);">
                <p class="text-sm font-semibold mb-1" style="color: var(--danger);">Ada data yang belum valid:</p>
                <ul class="text-xs space-y-1" style="color: var(--danger);">
                    @foreach($errors->all() as $error)
                        <li>- {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('client.invitations.store') }}" enctype="multipart/form-data">
            @csrf

            <h3 class="font-bold text-base mb-4" style="color: var(--accent);"><i class="fas fa-layer-group mr-2"></i> Template</h3>
            <div class="p-4 rounded-lg mb-4" style="background: var(--bg-tertiary); border:1px solid var(--border);">
                <p class="text-xs mb-1" style="color: var(--text-secondary);">Paket aktif akun</p>
                <p class="text-sm font-semibold">{{ $activePackage->name }} - Rp{{ number_format($activePackage->price, 0, ',', '.') }}</p>
                <p class="text-xs mt-1" style="color: var(--text-secondary);">
                    Kuota undangan: {{ $activePackage->max_invitations ?? 1 }} | Kuota tamu: {{ $activePackage->max_guests ?? 100 }} | Kuota foto: {{ $activePackage->max_photos ?? 10 }}
                </p>
            </div>

            <div class="mb-4">
                <label class="form-label">Template</label>
                <select name="template_id" class="form-input" required id="templateSelect" onchange="updateTemplatePreview()">
                    <option value="">Pilih Template</option>
                    @foreach($templates as $t)
                        <option value="{{ $t->id }}" data-thumbnail="{{ $t->thumbnail ? asset('storage/' . $t->thumbnail) : '' }}" {{ (old('template_id', $preselectedTemplateId ?? null) == $t->id) ? 'selected' : '' }}>
                            {{ $t->name }} ({{ ucfirst($t->category) }}) {{ $t->is_premium ? 'Premium' : '' }}
                        </option>
                    @endforeach
                </select>
                @error('template_id') <p class="text-xs mt-1" style="color: var(--danger);">{{ $message }}</p> @enderror
            </div>

            <div id="templatePreviewWrap" class="p-3 rounded-lg mb-6" style="display:none; background: var(--bg-tertiary);">
                <p class="text-xs mb-2" style="color: var(--text-secondary);">Preview Template</p>
                <img id="templatePreview" src="" alt="Template Preview" style="width:100%; max-height:200px; object-fit:cover; border-radius:10px; border:1px solid var(--border);">
            </div>

            <hr style="border-color: var(--border);" class="my-6">

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
                    <input type="text" name="title" value="{{ old('title') }}" class="form-input" required>
                    @error('title') <p class="text-xs mt-1" style="color: var(--danger);">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-5">
                <div><label class="form-label">Mempelai Pria</label><input type="text" name="groom_name" value="{{ old('groom_name') }}" class="form-input"></div>
                <div><label class="form-label">Mempelai Wanita</label><input type="text" name="bride_name" value="{{ old('bride_name') }}" class="form-input"></div>
                <div><label class="form-label">Host</label><input type="text" name="host_name" value="{{ old('host_name') }}" class="form-input"></div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                <div>
                    <label class="form-label">Tanggal Acara</label>
                    <input type="date" name="event_date" value="{{ old('event_date') }}" class="form-input" required>
                    @error('event_date') <p class="text-xs mt-1" style="color: var(--danger);">{{ $message }}</p> @enderror
                </div>
                <div><label class="form-label">Waktu Acara</label><input type="time" name="event_time" value="{{ old('event_time') }}" class="form-input" required></div>
            </div>

            <hr style="border-color: var(--border);" class="my-6">

            <h3 class="font-bold text-base mb-4" style="color: var(--accent);"><i class="fas fa-map-marker-alt mr-2"></i> Lokasi</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                <div><label class="form-label">Nama Tempat</label><input type="text" name="venue_name" value="{{ old('venue_name') }}" class="form-input" required></div>
                <div><label class="form-label">Link Google Maps</label><input type="url" name="google_maps_url" value="{{ old('google_maps_url') }}" class="form-input"></div>
            </div>
            <div class="mb-5"><label class="form-label">Alamat Lengkap</label><textarea name="venue_address" class="form-input" rows="2" required>{{ old('venue_address') }}</textarea></div>
            
            <div class="mb-5">
                <label class="form-label font-semibold" style="color:var(--accent);">Pin Lokasi Peta (opsional)</label>
                <p class="text-xs mb-2" style="color:var(--text-secondary);">Geser marker merah pada peta ke titik lokasi yang tepat. Koordinat akan diperbarui otomatis.</p>
                <div id="locationPickerMapCreate" style="height: 300px; border-radius: 10px; border: 1px solid var(--border); z-index: 1;"></div>
                <div class="grid grid-cols-2 gap-4 mt-3">
                    <div><label class="form-label text-xs">Latitude</label><input type="text" id="venue_lat" name="venue_lat" value="{{ old('venue_lat') }}" class="form-input bg-gray-100" readonly></div>
                    <div><label class="form-label text-xs">Longitude</label><input type="text" id="venue_lng" name="venue_lng" value="{{ old('venue_lng') }}" class="form-input bg-gray-100" readonly></div>
                </div>
            </div>


            <div class="mb-3">
                <label class="inline-flex items-center gap-2 text-sm font-semibold">
                    <input type="hidden" name="livestream_enabled" value="0">
                    <input type="checkbox" name="livestream_enabled" value="1" id="livestream_enabled_create" {{ old('livestream_enabled') ? 'checked' : '' }} style="accent-color: var(--accent); width:16px; height:16px;">
                    Aktifkan Live Streaming
                </label>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5" id="livestream_fields_create" style="{{ old('livestream_enabled') ? '' : 'display:none;' }}">
                <div><label class="form-label">Link Live Streaming</label><input type="url" name="livestream_url" value="{{ old('livestream_url') }}" class="form-input"></div>
                <div><label class="form-label">Label Live Streaming</label><input type="text" name="livestream_label" value="{{ old('livestream_label', 'Live Streaming') }}" class="form-input"></div>
            </div>

            <hr style="border-color: var(--border);" class="my-6">

            <h3 class="font-bold text-base mb-4" style="color: var(--accent);"><i class="fas fa-align-left mr-2"></i> Teks Undangan</h3>
            <div class="mb-5"><label class="form-label">Teks Pembuka</label><textarea name="opening_text" class="form-input" rows="3">{{ old('opening_text') }}</textarea></div>
            <div class="mb-5"><label class="form-label">Teks Penutup</label><textarea name="closing_text" class="form-input" rows="3">{{ old('closing_text') }}</textarea></div>

            <div class="mb-6">
                <label class="form-label">Cover Photo</label>
                <input type="file" name="cover_photo" class="form-input" accept="image/*">
            </div>
            <div class="mb-6">
                <label class="form-label">Upload Musik (max 20MB)</label>
                <input type="file" name="music_url" class="form-input" accept="audio/*">
            </div>
            <div class="mb-6">
                <label class="form-label">Pilih dari Library Musik</label>
                <select name="music_track_id" class="form-input">
                    <option value="">-- Tidak pilih --</option>
                    @foreach($musicTracks as $track)
                        <option value="{{ $track->id }}" {{ old('music_track_id') == $track->id ? 'selected' : '' }}>
                            {{ $track->title ?: basename($track->file_path) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <hr style="border-color: var(--border);" class="my-6">

            <div class="mb-6" x-data="{ stories: [{ year: '', title: '', description: '', photo_path: '' }] }">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-base" style="color: var(--accent);"><i class="fas fa-heart mr-2"></i> Love Story</h3>
                    <button type="button" @click="stories.push({ year: '', title: '', description: '', photo_path: '' })" class="btn btn-secondary btn-sm">
                        <i class="fas fa-plus mr-1"></i> Tambah Story
                    </button>
                </div>
                <template x-for="(story, idx) in stories" :key="idx">
                    <div class="p-4 mb-3 rounded-lg" style="background: var(--bg-tertiary); border: 1px solid var(--border);">
                        <div class="grid grid-cols-4 gap-3 mb-3">
                            <div><label class="form-label text-xs">Tahun</label><input type="text" :name="'love_stories[' + idx + '][year]'" x-model="story.year" class="form-input"></div>
                            <div class="col-span-3"><label class="form-label text-xs">Judul</label><input type="text" :name="'love_stories[' + idx + '][title]'" x-model="story.title" class="form-input"></div>
                        </div>
                        <div><label class="form-label text-xs">Cerita</label><textarea :name="'love_stories[' + idx + '][description]'" x-model="story.description" class="form-input" rows="2"></textarea></div>
                        <div class="mt-3">
                            <label class="form-label text-xs">Foto Story (opsional)</label>
                            <input type="file" :name="'love_story_photos[' + idx + ']'" class="form-input" accept="image/*">
                        </div>
                    </div>
                </template>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="btn btn-primary text-sm"><i class="fas fa-save mr-2"></i> Simpan Undangan</button>
                <a href="{{ route('client.invitations.index') }}" class="btn btn-secondary text-sm">Batal</a>
            </div>
        </form>
    </div>
</div>

<script>
function updateTemplatePreview() {
    const sel = document.getElementById('templateSelect');
    const opt = sel.options[sel.selectedIndex];
    const wrap = document.getElementById('templatePreviewWrap');
    const img = document.getElementById('templatePreview');
    const thumb = opt?.dataset?.thumbnail || '';
    if (!opt.value || !thumb) { wrap.style.display = 'none'; img.src = ''; return; }
    img.src = thumb;
    wrap.style.display = 'block';
}

document.addEventListener('DOMContentLoaded', () => {
    updateTemplatePreview();
    const toggle = document.getElementById('livestream_enabled_create');
    const fields = document.getElementById('livestream_fields_create');
    if (toggle && fields) {
        const refreshLive = () => { fields.style.display = toggle.checked ? '' : 'none'; };
        toggle.addEventListener('change', refreshLive);
        refreshLive();
    }

    // Init Location Picker Map
    if (typeof L !== 'undefined') {
        const latInput = document.getElementById('venue_lat');
        const lngInput = document.getElementById('venue_lng');
        let initialLat = parseFloat(latInput.value);
        let initialLng = parseFloat(lngInput.value);

        // Default to Jakarta if not set
        if (isNaN(initialLat) || isNaN(initialLng)) {
            initialLat = -6.200000;
            initialLng = 106.816666;
        }

        const map = L.map('locationPickerMapCreate').setView([initialLat, initialLng], 13);
        L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            attribution: '&copy; Google Maps'
        }).addTo(map);

        const marker = L.marker([initialLat, initialLng], {draggable: true}).addTo(map);

        marker.on('dragend', function(e) {
            const position = marker.getLatLng();
            latInput.value = position.lat.toFixed(8);
            lngInput.value = position.lng.toFixed(8);
        });
    }
});
</script>
@push('head')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
@endpush
@endsection
