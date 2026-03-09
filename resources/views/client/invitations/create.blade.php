@extends('layouts.client')
@section('title', 'Buat Undangan')
@section('page-title', 'Buat Undangan Baru')
@section('page-subtitle', 'Isi form di bawah untuk membuat undangan digital')

@section('content')
<div class="max-w-3xl">
    <div class="card p-6">
        <form method="POST" action="{{ route('client.invitations.store') }}" enctype="multipart/form-data">
            @csrf

            <h3 class="font-bold text-base mb-4" style="color: var(--accent);"><i class="fas fa-layer-group mr-2"></i> Template & Paket</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-4">
                <div>
                    <label class="form-label">Template</label>
                    <select name="template_id" class="form-input" required id="templateSelect" onchange="updateTemplatePreview()">
                        <option value="">Pilih Template</option>
                        @foreach($templates as $t)
                            <option value="{{ $t->id }}" data-thumbnail="{{ $t->thumbnail ? asset('storage/' . $t->thumbnail) : '' }}" {{ old('template_id') == $t->id ? 'selected' : '' }}>
                                {{ $t->name }} ({{ ucfirst($t->category) }}) {{ $t->is_premium ? 'Premium' : '' }}
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
                            <option value="{{ $p->id }}"
                                data-tier="{{ $p->tier ?? 'starter' }}"
                                data-badge="{{ $p->badge_text ?? '' }}"
                                data-support="{{ $p->support_level ?? '' }}"
                                data-sla="{{ $p->sla_hours ?? '' }}"
                                data-guests="{{ $p->max_guests }}"
                                data-photos="{{ $p->max_photos }}"
                                data-invitations="{{ $p->max_invitations ?? 1 }}"
                                data-templates='@json($p->allowed_template_ids ?? [])'
                                data-features='@json($p->features)'
                                data-addons='@json($p->addons ?? [])'
                                {{ old('package_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->name }} - Rp{{ number_format($p->price, 0, ',', '.') }}
                                ({{ ($p->billing_type ?? 'one_time') === 'subscription' ? 'Subscription ' . strtoupper($p->billing_cycle ?? 'monthly') : 'One-time' }})
                            </option>
                        @endforeach
                    </select>
                    @error('package_id') <p class="text-xs mt-1" style="color: var(--danger);">{{ $message }}</p> @enderror
                </div>
            </div>

            <div id="templatePreviewWrap" class="p-3 rounded-lg mb-4" style="display:none; background: var(--bg-tertiary);">
                <p class="text-xs mb-2" style="color: var(--text-secondary);">Preview Template</p>
                <img id="templatePreview" src="" alt="Template Preview" style="width:100%; max-height:200px; object-fit:cover; border-radius:10px; border:1px solid var(--border);">
            </div>

            <div id="packageInfo" class="p-4 rounded-lg mb-6" style="background: var(--bg-tertiary); display: none;">
                <div class="mb-2 flex items-center justify-between">
                    <strong id="pkgTier" style="color: var(--accent);">-</strong>
                    <span id="pkgBadge" class="text-[11px] px-2 py-1 rounded-full" style="display:none;background:var(--accent-bg);color:var(--accent);"></span>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-3">
                    <div class="text-xs"><span style="color: var(--text-secondary);">Max Tamu:</span> <strong id="pkgGuests">-</strong></div>
                    <div class="text-xs"><span style="color: var(--text-secondary);">Max Foto:</span> <strong id="pkgPhotos">-</strong></div>
                    <div class="text-xs"><span style="color: var(--text-secondary);">Max Undangan:</span> <strong id="pkgInvitations">-</strong></div>
                    <div class="text-xs"><span style="color: var(--text-secondary);">Support:</span> <strong id="pkgSupport">-</strong></div>
                </div>
                <div id="pkgFeatures" class="flex flex-wrap gap-1 mb-2"></div>
                <div id="pkgAddons" class="flex flex-wrap gap-1"></div>
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
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                <div><label class="form-label">Link Live Streaming (opsional)</label><input type="url" name="livestream_url" value="{{ old('livestream_url') }}" class="form-input" placeholder="https://youtube.com/live/..."></div>
                <div><label class="form-label">Label Live Streaming</label><input type="text" name="livestream_label" value="{{ old('livestream_label', 'Live Streaming') }}" class="form-input" placeholder="Live Streaming Akad"></div>
            </div>

            <hr style="border-color: var(--border);" class="my-6">

            <h3 class="font-bold text-base mb-4" style="color: var(--accent);"><i class="fas fa-align-left mr-2"></i> Teks Undangan</h3>
            <div class="mb-5"><label class="form-label">Teks Pembuka</label><textarea name="opening_text" class="form-input" rows="3">{{ old('opening_text') }}</textarea></div>
            <div class="mb-5"><label class="form-label">Teks Penutup</label><textarea name="closing_text" class="form-input" rows="3">{{ old('closing_text') }}</textarea></div>
            <div class="mb-6"><label class="form-label">Cover Photo</label><input type="file" name="cover_photo" class="form-input" accept="image/*"></div>
            <div class="mb-6">
                <label class="form-label">Upload Musik (max 20MB)</label>
                <input type="file" name="music_url" class="form-input" accept="audio/*">
                @error('music_url') <p class="text-xs mt-1" style="color: var(--danger);">{{ $message }}</p> @enderror
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
                @error('music_track_id') <p class="text-xs mt-1" style="color: var(--danger);">{{ $message }}</p> @enderror
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
function updatePackageInfo() {
    const sel = document.getElementById('packageSelect');
    const opt = sel.options[sel.selectedIndex];
    const info = document.getElementById('packageInfo');
    if (!opt.value) { info.style.display = 'none'; return; }
    info.style.display = 'block';
    document.getElementById('pkgTier').textContent = 'Tier ' + (opt.dataset.tier || 'starter').toUpperCase();
    document.getElementById('pkgGuests').textContent = opt.dataset.guests + ' orang';
    document.getElementById('pkgPhotos').textContent = opt.dataset.photos + ' foto';
    document.getElementById('pkgInvitations').textContent = (opt.dataset.invitations || 1) + ' undangan';
    document.getElementById('pkgSupport').textContent = opt.dataset.support
        ? (opt.dataset.support + (opt.dataset.sla ? ' (SLA ' + opt.dataset.sla + ' jam)' : ''))
        : '-';
    const badge = document.getElementById('pkgBadge');
    badge.textContent = opt.dataset.badge || '';
    badge.style.display = opt.dataset.badge ? 'inline-block' : 'none';
    const features = JSON.parse(opt.dataset.features || '[]');
    const addons = JSON.parse(opt.dataset.addons || '[]');
    const container = document.getElementById('pkgFeatures');
    container.innerHTML = features.map(f => `<span style="background:var(--accent-bg);color:var(--accent);padding:2px 8px;border-radius:4px;font-size:11px;font-weight:600;">${f}</span>`).join('');
    const addContainer = document.getElementById('pkgAddons');
    addContainer.innerHTML = addons.map(a => `<span style="background:rgba(250,204,21,.14);color:#b45309;padding:2px 8px;border-radius:4px;font-size:11px;font-weight:600;">⭐ ${a}</span>`).join('');
    filterTemplatesByPackage(opt.dataset.templates || '[]');
}

function filterTemplatesByPackage(templateIdsJson) {
    const templateSelect = document.getElementById('templateSelect');
    const allowed = JSON.parse(templateIdsJson || '[]').map((id) => String(id));
    const useAll = allowed.length === 0;

    [...templateSelect.options].forEach((opt, idx) => {
        if (idx === 0) return;
        const isAllowed = useAll || allowed.includes(opt.value);
        opt.hidden = !isAllowed;
        opt.disabled = !isAllowed;
        if (!isAllowed && opt.selected) {
            templateSelect.selectedIndex = 0;
        }
    });
}

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
    updatePackageInfo();
    updateTemplatePreview();
});
</script>
@endsection

