@extends('layouts.client')
@section('title', 'Edit Undangan')
@section('page-title', 'Edit Undangan')
@section('page-subtitle', $invitation->title)

@push('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
@endpush

@section('content')

    @php
        $maxPhotos = $invitation->package->max_photos ?? 10;
        $currentPhotos = $invitation->photos->count();
        $photoPercent = $maxPhotos > 0 ? min(100, round(($currentPhotos / $maxPhotos) * 100)) : 0;
    @endphp

    <style>
        .edit-layout {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .edit-card {
            border: 1px solid var(--border);
            background: var(--bg-secondary);
            border-radius: 14px;
            box-shadow: var(--card-shadow);
        }

        .edit-head {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
        }

        .edit-body {
            padding: 1.25rem;
        }

        .step {
            width: 1.5rem;
            height: 1.5rem;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: .75rem;
            font-weight: 700;
            background: var(--accent-bg);
            color: var(--accent);
        }

        .mini-preview {
            width: 52px;
            height: 52px;
            object-fit: cover;
            border-radius: 9px;
            border: 1px solid var(--border);
        }

        .edit-nav {
            position: sticky;
            top: 76px;
            height: fit-content;
        }

        .edit-nav a {
            display: block;
            text-decoration: none;
            color: var(--text-secondary);
            padding: .45rem .65rem;
            border-radius: 8px;
            font-size: 12px;
        }

        .edit-nav a:hover {
            background: var(--hover-bg);
            color: var(--text);
        }

        @media (min-width:1100px) {
            .edit-layout {
                grid-template-columns: 240px 1fr;
            }
        }
    </style>

    <div class="max-w-7xl edit-layout">
        <aside class="hidden lg:block edit-card edit-nav p-4">
            <p class="text-xs font-bold mb-3" style="color:var(--text-secondary);">Alur Edit (Wedding GNV2)</p>
            <a href="#sec1">1. Template</a>
            <a href="#sec2">2. Cover & Pembuka</a>
            <a href="#sec3">3. Data Mempelai</a>
            <a href="#sec4">4. Countdown</a>
            <a href="#sec5">5. Acara & Susunan</a>
            <a href="#sec6">6. Lokasi</a>
            <a href="#sec7">7. Galeri</a>
            <a href="#sec8">8. Love Story</a>
            <a href="#sec9">9. RSVP & Ucapan</a>
            <a href="#sec10">10. Tanda Kasih</a>
            <a href="#sec11">11. Penutup</a>
            <a href="#sec12">12. Musik & Live</a>
        </aside>

        <div class="space-y-5">
            <form method="POST" action="{{ route('client.invitations.update', $invitation) }}"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @if($errors->any())
                    <div class="edit-card">
                        <div class="edit-body">
                            <p class="text-sm font-semibold mb-2" style="color: var(--danger);">Ada data yang belum valid:</p>
                            <ul class="text-xs space-y-1" style="color: var(--danger);">
                                @foreach($errors->all() as $error)
                                    <li>• {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <section id="sec1" class="edit-card">
                    <div class="edit-head">
                        <h3 class="font-semibold flex items-center gap-2"><span class="step">1</span>Template</h3>
                    </div>
                    <div class="edit-body grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Template</label>
                            <select name="template_id" class="form-input" required id="templateSelectEdit"
                                onchange="updateTemplatePreviewEdit()">
                                @foreach ($templates as $t)
                                    <option value="{{ $t->id }}"
                                        data-thumbnail="{{ $t->thumbnail ? asset('storage/' . $t->thumbnail) : '' }}" {{ $invitation->template_id == $t->id ? 'selected' : '' }}>
                                        {{ $t->name }} {{ $t->is_premium ? '⭐' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Paket</label>
                            <div class="form-input flex items-center">{{ $invitation->package->name ?? '-' }} —
                                Rp{{ number_format((float) ($invitation->package->price ?? 0), 0, ',', '.') }}</div>
                        </div>
                        <div id="templatePreviewWrapEdit" class="p-3 rounded-lg sm:col-span-2"
                            style="display:none; background: var(--bg-tertiary);">
                            <p class="text-xs mb-2" style="color: var(--text-secondary);">Preview Template</p>
                            <img id="templatePreviewEdit" src="" alt="Template Preview"
                                style="width:100%; max-height:220px; object-fit:cover; border-radius:10px; border:1px solid var(--border);">
                        </div>
                    </div>
                </section>

                <section id="sec2" class="edit-card">
                    <div class="edit-head">
                        <h3 class="font-semibold flex items-center gap-2"><span class="step">2</span>Cover & Teks Pembuka
                        </h3>
                    </div>
                    <div class="edit-body">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                            <div><label class="form-label">Judul Acara</label><input type="text" name="title"
                                    value="{{ old('title', $invitation->title) }}" class="form-input" required></div>
                            <div><label class="form-label">Jenis Acara</label>
                                <select name="event_type" class="form-input" required>
                                    @foreach (['wedding', 'birthday', 'graduation', 'corporate', 'other'] as $type)
                                        <option value="{{ $type }}" {{ $invitation->event_type === $type ? 'selected' : '' }}>
                                            {{ ucfirst($type) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mb-4"><label class="form-label">Cover Photo</label><input type="file" name="cover_photo"
                                class="form-input" accept="image/*">
                            @if ($invitation->cover_photo)
                                <div class="mt-2 flex items-center gap-2"><img
                                        src="{{ asset('storage/' . $invitation->cover_photo) }}" class="mini-preview"
                                        alt="Cover"><span class="text-xs" style="color:var(--text-secondary);">Cover saat
                            ini</span></div>@endif
                        </div>
                        <div><label class="form-label">Teks Pembuka (mis. Surah Ar-Rum)</label><textarea name="opening_text"
                                class="form-input" rows="4">{{ old('opening_text', $invitation->opening_text) }}</textarea>
                        </div>
                    </div>
                </section>

                <section id="sec3" class="edit-card">
                    <div class="edit-head">
                        <h3 class="font-semibold flex items-center gap-2"><span class="step">3</span>Data Mempelai</h3>
                    </div>
                    <div class="edit-body">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                            <div><label class="form-label">Mempelai Pria</label><input type="text" name="groom_name"
                                    value="{{ old('groom_name', $invitation->groom_name) }}" class="form-input"></div>
                            <div><label class="form-label">Mempelai Wanita</label><input type="text" name="bride_name"
                                    value="{{ old('bride_name', $invitation->bride_name) }}" class="form-input"></div>
                            <div><label class="form-label">Host</label><input type="text" name="host_name"
                                    value="{{ old('host_name', $invitation->host_name) }}" class="form-input"></div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                            <div><label class="form-label">Nama Orang Tua Pria</label><input type="text"
                                    name="groom_parent_name"
                                    value="{{ old('groom_parent_name', $invitation->groom_parent_name) }}"
                                    class="form-input"></div>
                            <div><label class="form-label">Nama Orang Tua Wanita</label><input type="text"
                                    name="bride_parent_name"
                                    value="{{ old('bride_parent_name', $invitation->bride_parent_name) }}"
                                    class="form-input"></div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                            <div><label class="form-label">Foto Mempelai Pria</label><input type="file" name="groom_photo"
                                    class="form-input" accept="image/*">@if($invitation->groom_photo)<img
                                        src="{{ asset('storage/' . $invitation->groom_photo) }}" class="mini-preview mt-2"
                                    alt="Groom">@endif</div>
                            <div><label class="form-label">Foto Mempelai Wanita</label><input type="file" name="bride_photo"
                                    class="form-input" accept="image/*">@if($invitation->bride_photo)<img
                                        src="{{ asset('storage/' . $invitation->bride_photo) }}" class="mini-preview mt-2"
                                    alt="Bride">@endif</div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                            <div><label class="form-label">Instagram Pria</label><input type="text" name="groom_instagram"
                                    value="{{ old('groom_instagram', $invitation->groom_instagram) }}" class="form-input">
                            </div>
                            <div><label class="form-label">Instagram Wanita</label><input type="text" name="bride_instagram"
                                    value="{{ old('bride_instagram', $invitation->bride_instagram) }}" class="form-input">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div><label class="form-label">Facebook Pria</label><input type="text" name="groom_facebook"
                                    value="{{ old('groom_facebook', $invitation->groom_facebook) }}" class="form-input">
                            </div>
                            <div><label class="form-label">Facebook Wanita</label><input type="text" name="bride_facebook"
                                    value="{{ old('bride_facebook', $invitation->bride_facebook) }}" class="form-input">
                            </div>
                        </div>
                    </div>
                </section>

                <section id="sec4" class="edit-card">
                    <div class="edit-head">
                        <h3 class="font-semibold flex items-center gap-2"><span class="step">4</span>Countdown & Waktu</h3>
                    </div>
                    <div class="edit-body grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div><label class="form-label">Tanggal Acara Utama</label><input type="date" name="event_date"
                                value="{{ old('event_date', optional($invitation->event_date)->format('Y-m-d')) }}"
                                class="form-input" required></div>
                        <div><label class="form-label">Waktu Acara Utama</label><input type="time" name="event_time"
                                value="{{ old('event_time', $invitation->event_time ? \Carbon\Carbon::parse($invitation->event_time)->format('H:i') : '') }}"
                                class="form-input" required></div>
                    </div>
                </section>

                <section id="sec5" class="edit-card"
                    x-data="{ events: {{ json_encode($invitation->events->count() > 0 ? $invitation->events->map(fn($e) => ['event_name' => $e->event_name, 'event_description' => $e->event_description, 'event_date' => $e->event_date ? \Carbon\Carbon::parse($e->event_date)->format('Y-m-d') : '', 'event_time' => $e->event_time, 'event_end_time' => $e->event_end_time, 'venue_name' => $e->venue_name, 'venue_address' => $e->venue_address, 'venue_maps_url' => $e->venue_maps_url])->values() : [['event_name' => 'Akad Nikah', 'event_description' => '', 'event_date' => '', 'event_time' => '', 'event_end_time' => '', 'venue_name' => '', 'venue_address' => '', 'venue_maps_url' => ''], ['event_name' => 'Resepsi', 'event_description' => '', 'event_date' => '', 'event_time' => '', 'event_end_time' => '', 'venue_name' => '', 'venue_address' => '', 'venue_maps_url' => '']]) }} }">
                    <div class="edit-head">
                        <h3 class="font-semibold flex items-center gap-2"><span class="step">5</span>Acara & Susunan</h3>
                        <button type="button"
                            @click="events.push({event_name:'',event_description:'',event_date:'',event_time:'',event_end_time:'',venue_name:'',venue_address:'',venue_maps_url:''})"
                            class="btn btn-secondary btn-sm"><i class="fas fa-plus mr-1"></i> Tambah</button>
                    </div>
                    <div class="edit-body">
                        <template x-for="(ev, idx) in events" :key="idx">
                            <div class="p-4 mb-3 rounded-lg"
                                style="background: var(--bg-tertiary); border: 1px solid var(--border);">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-xs font-bold" style="color: var(--accent);"
                                        x-text="'Acara ' + (idx+1)"></span>
                                    <button type="button" @click="events.splice(idx,1)" class="text-xs"
                                        style="color: var(--danger);" x-show="events.length > 1"><i
                                            class="fas fa-trash mr-1"></i> Hapus</button>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                                    <div><label class="form-label text-xs">Nama Acara</label><input type="text"
                                            :name="'events[' + idx + '][event_name]'" x-model="ev.event_name"
                                            class="form-input"></div>
                                    <div><label class="form-label text-xs">Tempat</label><input type="text"
                                            :name="'events[' + idx + '][venue_name]'" x-model="ev.venue_name"
                                            class="form-input"></div>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-3">
                                    <div><label class="form-label text-xs">Tanggal</label><input type="date"
                                            :name="'events[' + idx + '][event_date]'" x-model="ev.event_date"
                                            class="form-input"></div>
                                    <div><label class="form-label text-xs">Mulai</label><input type="time"
                                            :name="'events[' + idx + '][event_time]'" x-model="ev.event_time"
                                            class="form-input"></div>
                                    <div><label class="form-label text-xs">Selesai</label><input type="time"
                                            :name="'events[' + idx + '][event_end_time]'" x-model="ev.event_end_time"
                                            class="form-input"></div>
                                </div>
                                <div><label class="form-label text-xs">Alamat</label><input type="text"
                                        :name="'events[' + idx + '][venue_address]'" x-model="ev.venue_address"
                                        class="form-input"></div>
                                <div class="mt-3"><label class="form-label text-xs">Deskripsi</label><textarea
                                        :name="'events[' + idx + '][event_description]'" x-model="ev.event_description"
                                        class="form-input" rows="2"></textarea></div>
                            </div>
                        </template>
                    </div>
                </section>

                <section id="sec6" class="edit-card">
                    <div class="edit-head">
                        <h3 class="font-semibold flex items-center gap-2"><span class="step">6</span>Lokasi</h3>
                    </div>
                    <div class="edit-body">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                            <div><label class="form-label">Nama Tempat Utama</label><input type="text" name="venue_name"
                                    value="{{ old('venue_name', $invitation->venue_name) }}" class="form-input" required>
                            </div>
                            <div><label class="form-label">Google Maps URL</label><input type="url" name="google_maps_url"
                                    value="{{ old('google_maps_url', $invitation->google_maps_url) }}" class="form-input">
                            </div>
                        </div>
                        <div class="mb-4"><label class="form-label">Alamat Lengkap</label><textarea name="venue_address"
                                class="form-input" rows="2"
                                required>{{ old('venue_address', $invitation->venue_address) }}</textarea></div>

                        <div class="mb-4">
                            <label class="form-label font-semibold" style="color:var(--accent);">Pin Lokasi Peta</label>
                            <p class="text-xs mb-2" style="color:var(--text-secondary);">Klik pada peta atau geser marker untuk menentukan titik lokasi acara. Gunakan tombol di bawah untuk menemukan lokasi Anda saat ini.</p>
                            <div class="flex flex-wrap gap-2 mb-3">
                                <button type="button" id="btnMyLocation" class="btn btn-secondary btn-sm" onclick="locateMe()">
                                    <i class="fas fa-crosshairs mr-1"></i> Lokasi Saya
                                </button>
                                <span id="locateStatus" class="text-xs self-center" style="color:var(--text-secondary);"></span>
                            </div>
                            <div id="locationPickerMap"
                                style="height: 350px; border-radius: 10px; border: 1px solid var(--border); z-index: 1;">
                            </div>
                            <div class="grid grid-cols-2 gap-4 mt-3">
                                <div><label class="form-label text-xs">Latitude</label><input type="text" id="venue_lat"
                                        name="venue_lat" value="{{ old('venue_lat', $invitation->venue_lat) }}"
                                        class="form-input bg-gray-100" readonly></div>
                                <div><label class="form-label text-xs">Longitude</label><input type="text" id="venue_lng"
                                        name="venue_lng" value="{{ old('venue_lng', $invitation->venue_lng) }}"
                                        class="form-input bg-gray-100" readonly></div>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="sec7" class="edit-card">
                    <div class="edit-head">
                        <h3 class="font-semibold flex items-center gap-2"><span class="step">7</span>Galeri</h3>
                        <span class="text-xs font-semibold"
                            style="color: var(--text-secondary);">{{ $currentPhotos }}/{{ $maxPhotos }}</span>
                    </div>
                    <div class="edit-body">
                        <div class="mb-4"
                            style="background: var(--bg-tertiary); height: 4px; border-radius: 2px; overflow: hidden;">
                            <div
                                style="width: {{ $photoPercent }}%; height: 100%; border-radius: 2px; transition: width 0.3s; background: {{ $photoPercent >= 90 ? 'var(--danger)' : ($photoPercent >= 70 ? 'var(--warning)' : 'var(--accent)') }};">
                            </div>
                        </div>
                        @if ($invitation->photos->count())
                            <div class="grid grid-cols-3 sm:grid-cols-4 gap-3 mb-4">
                                @foreach ($invitation->photos as $photo)
                                    <div class="relative group"
                                        style="aspect-ratio: 1; border-radius: var(--radius-sm); overflow: hidden; border: 1px solid var(--border);">
                                        <img src="{{ asset('storage/' . $photo->file_path) }}" alt="{{ $photo->caption }}"
                                            class="w-full h-full object-cover">
                                        <div
                                            class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                            <button type="submit" form="delete-photo-{{ $photo->id }}"
                                                onclick="return confirm('Hapus foto ini?')"
                                                class="w-8 h-8 rounded-full flex items-center justify-center"
                                                style="background: rgba(255,59,48,0.8);">
                                                <i class="fas fa-trash text-white text-xs"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        @if ($currentPhotos < $maxPhotos)
                            <div class="grid grid-cols-1 sm:grid-cols-[1fr,180px,150px] gap-2 items-end">
                                <div>
                                    <label class="form-label">Upload Foto Baru</label>
                                    <input type="file" name="photo" class="form-input text-xs"
                                        accept="image/jpeg,image/png,image/webp" required form="galleryUploadForm">
                                </div>
                                <div>
                                    <label class="form-label">Caption</label>
                                    <input type="text" name="caption" class="form-input" placeholder="Opsional"
                                        form="galleryUploadForm">
                                </div>
                                <button type="submit" form="galleryUploadForm" class="btn btn-primary btn-sm">
                                    <i class="fas fa-upload mr-1"></i> Simpan Galeri
                                </button>
                            </div>
                        @endif
                    </div>
                </section>

                <section id="sec8" class="edit-card"
                    x-data="{ stories: {{ json_encode($invitation->loveStories->count() > 0 ? $invitation->loveStories->map(fn($s) => ['year' => $s->year, 'title' => $s->title, 'description' => $s->description, 'photo_path' => $s->photo_path])->values() : [['year' => '', 'title' => '', 'description' => '', 'photo_path' => '']]) }} }">
                    <div class="edit-head">
                        <h3 class="font-semibold flex items-center gap-2"><span class="step">8</span>Love Story</h3>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="stories.push({year:'',title:'',description:'',photo_path:''})"
                                class="btn btn-secondary btn-sm"><i class="fas fa-plus mr-1"></i> Tambah</button>
                            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save mr-1"></i> Simpan
                                Love Story</button>
                        </div>
                    </div>
                    <div class="edit-body">
                        <template x-for="(story, idx) in stories" :key="idx">
                            <div class="p-4 mb-3 rounded-lg"
                                style="background: var(--bg-tertiary); border: 1px solid var(--border);">
                                <div class="grid grid-cols-4 gap-3 mb-3">
                                    <div><label class="form-label text-xs">Tahun</label><input type="text"
                                            :name="'love_stories[' + idx + '][year]'" x-model="story.year"
                                            class="form-input"></div>
                                    <div class="col-span-3"><label class="form-label text-xs">Judul</label><input
                                            type="text" :name="'love_stories[' + idx + '][title]'" x-model="story.title"
                                            class="form-input"></div>
                                </div>
                                <div><label class="form-label text-xs">Deskripsi</label><textarea
                                        :name="'love_stories[' + idx + '][description]'" x-model="story.description"
                                        class="form-input" rows="2"></textarea></div>
                                <input type="hidden" :name="'love_stories[' + idx + '][photo_path]'"
                                    x-model="story.photo_path">
                                <template x-if="story.photo_path">
                                    <div class="mt-3 flex items-center gap-3">
                                        <img :src="'/storage/' + story.photo_path" alt="Foto story saat ini"
                                            class="mini-preview">
                                        <p class="text-xs" style="color: var(--text-secondary);">Foto saat ini tetap dipakai
                                            jika tidak upload baru.</p>
                                    </div>
                                </template>
                                <div class="mt-3"><label class="form-label text-xs">Foto Story</label><input type="file"
                                        :name="'love_story_photos[' + idx + ']'" class="form-input" accept="image/*"></div>
                            </div>
                        </template>
                    </div>
                </section>

                <section id="sec9" class="edit-card">
                    <div class="edit-head">
                        <h3 class="font-semibold flex items-center gap-2"><span class="step">9</span>RSVP & Ucapan</h3>
                    </div>
                    <div class="edit-body">
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <div class="p-3 rounded-lg" style="background: var(--bg-tertiary);">
                                <p class="text-xs" style="color:var(--text-secondary);">RSVP</p>
                                <p class="text-lg font-bold">{{ $invitation->rsvps->count() }}</p>
                            </div>
                            <div class="p-3 rounded-lg" style="background: var(--bg-tertiary);">
                                <p class="text-xs" style="color:var(--text-secondary);">Hadir</p>
                                <p class="text-lg font-bold">{{ $invitation->rsvps->where('status', 'attending')->count() }}
                                </p>
                            </div>
                            <div class="p-3 rounded-lg" style="background: var(--bg-tertiary);">
                                <p class="text-xs" style="color:var(--text-secondary);">Tidak Hadir</p>
                                <p class="text-lg font-bold">
                                    {{ $invitation->rsvps->where('status', 'not_attending')->count() }}</p>
                            </div>
                            <div class="p-3 rounded-lg" style="background: var(--bg-tertiary);">
                                <p class="text-xs" style="color:var(--text-secondary);">Ucapan</p>
                                <p class="text-lg font-bold">{{ $invitation->wishes->count() }}</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="sec10" class="edit-card"
                    x-data="{ accounts: {{ json_encode($invitation->bankAccounts->count() ? $invitation->bankAccounts->map(fn($a) => ['bank_name' => $a->bank_name, 'account_number' => $a->account_number, 'account_name' => $a->account_name])->values() : [['bank_name' => $invitation->bank_name ?? '', 'account_number' => $invitation->bank_account_number ?? '', 'account_name' => $invitation->bank_account_name ?? '']]) }} }">
                    <div class="edit-head">
                        <h3 class="font-semibold flex items-center gap-2"><span class="step">10</span>Tanda Kasih</h3>
                        <button type="button" @click="accounts.push({bank_name:'',account_number:'',account_name:''})"
                            class="btn btn-secondary btn-sm"><i class="fas fa-plus mr-1"></i> Tambah</button>
                    </div>
                    <div class="edit-body">
                        <template x-for="(acc, idx) in accounts" :key="idx">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-3 p-3 rounded-lg"
                                style="background: var(--bg-tertiary); border: 1px solid var(--border);">
                                <div><label class="form-label text-xs">Nama Bank</label><input type="text"
                                        :name="'bank_accounts[' + idx + '][bank_name]'" x-model="acc.bank_name"
                                        class="form-input"></div>
                                <div><label class="form-label text-xs">No. Rekening</label><input type="text"
                                        :name="'bank_accounts[' + idx + '][account_number]'" x-model="acc.account_number"
                                        class="form-input"></div>
                                <div><label class="form-label text-xs">Atas Nama</label><input type="text"
                                        :name="'bank_accounts[' + idx + '][account_name]'" x-model="acc.account_name"
                                        class="form-input"></div>
                            </div>
                        </template>
                        <div><label class="form-label">Alamat Kirim Hadiah</label><textarea name="gift_address"
                                class="form-input" rows="2">{{ old('gift_address', $invitation->gift_address) }}</textarea>
                        </div>
                    </div>
                </section>

                <section id="sec11" class="edit-card">
                    <div class="edit-head">
                        <h3 class="font-semibold flex items-center gap-2"><span class="step">11</span>Penutup</h3>
                    </div>
                    <div class="edit-body">
                        <div class="mb-4"><label class="form-label">Teks Penutup</label><textarea name="closing_text"
                                class="form-input" rows="3">{{ old('closing_text', $invitation->closing_text) }}</textarea>
                        </div>
                        <div><label class="form-label">Footer</label><input type="text" name="footer_text"
                                value="{{ old('footer_text', $invitation->footer_text) }}" class="form-input"></div>
                    </div>
                </section>

                <section id="sec12" class="edit-card">
                    <div class="edit-head">
                        <h3 class="font-semibold flex items-center gap-2"><span class="step">12</span>Musik & Live Streaming
                        </h3>
                    </div>
                    <div class="edit-body">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                            <div><label class="form-label">Upload Musik</label><input type="file" name="music_url"
                                    class="form-input" accept="audio/*"></div>
                            <div><label class="form-label">Pilih dari Library Musik</label>
                                <select name="music_track_id" class="form-input">
                                    <option value="">-- Tetap gunakan musik saat ini --</option>
                                    @foreach($musicTracks as $track)
                                        <option value="{{ $track->id }}">{{ $track->title ?: basename($track->file_path) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="inline-flex items-center gap-2 text-sm font-semibold">
                                <input type="hidden" name="livestream_enabled" value="0">
                                <input type="checkbox" name="livestream_enabled" value="1" id="livestream_enabled_edit" {{ old('livestream_enabled', $invitation->livestream_enabled) ? 'checked' : '' }}
                                    style="accent-color: var(--accent); width:16px; height:16px;">
                                Aktifkan Live Streaming
                            </label>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" id="livestream_fields_edit"
                            style="{{ old('livestream_enabled', $invitation->livestream_enabled) ? '' : 'display:none;' }}">
                            <div><label class="form-label">Link Live Streaming</label><input type="url"
                                    name="livestream_url" value="{{ old('livestream_url', $invitation->livestream_url) }}"
                                    class="form-input"></div>
                            <div><label class="form-label">Label Live Streaming</label><input type="text"
                                    name="livestream_label"
                                    value="{{ old('livestream_label', $invitation->livestream_label) }}" class="form-input">
                            </div>
                        </div>
                    </div>
                </section>

                <div class="flex items-center gap-3 pt-1">
                    <button type="submit" class="btn btn-primary text-sm"><i class="fas fa-save mr-2"></i> Update
                        Undangan</button>
                    <a href="{{ route('client.invitations.show', $invitation) }}"
                        class="btn btn-secondary text-sm">Batal</a>
                </div>
            </form>

            <form id="galleryUploadForm" method="POST" action="{{ route('client.invitations.photos.store', $invitation) }}"
                enctype="multipart/form-data" class="hidden">
                @csrf
            </form>
            @foreach ($invitation->photos as $photo)
                <form id="delete-photo-{{ $photo->id }}" method="POST"
                    action="{{ route('client.invitations.photos.destroy', [$invitation, $photo]) }}" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            @endforeach
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script>
        function updateTemplatePreviewEdit() {
            const sel = document.getElementById('templateSelectEdit');
            const opt = sel.options[sel.selectedIndex];
            const wrap = document.getElementById('templatePreviewWrapEdit');
            const img = document.getElementById('templatePreviewEdit');
            const thumb = opt?.dataset?.thumbnail || '';
            if (!thumb) { wrap.style.display = 'none'; img.src = ''; return; }
            img.src = thumb; wrap.style.display = 'block';
        }

        let pickerMap, pickerMarker;

        function updateCoordInputs(lat, lng) {
            document.getElementById('venue_lat').value = lat.toFixed(8);
            document.getElementById('venue_lng').value = lng.toFixed(8);
        }

        function locateMe() {
            const statusEl = document.getElementById('locateStatus');
            if (!("geolocation" in navigator)) {
                statusEl.textContent = '❌ Browser tidak mendukung geolokasi.';
                return;
            }
            statusEl.textContent = '⏳ Mencari lokasi Anda...';
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    if (pickerMap && pickerMarker) {
                        pickerMap.setView([lat, lng], 17);
                        pickerMarker.setLatLng([lat, lng]);
                        updateCoordInputs(lat, lng);
                    }
                    statusEl.textContent = '✅ Lokasi ditemukan! Geser marker jika perlu.';
                },
                (error) => {
                    const msgs = {
                        1: '❌ Izin lokasi ditolak.',
                        2: '❌ Lokasi tidak tersedia.',
                        3: '❌ Waktu mencari lokasi habis.'
                    };
                    statusEl.textContent = msgs[error.code] || '❌ Gagal mendapatkan lokasi.';
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        }

        document.addEventListener('DOMContentLoaded', () => {
            updateTemplatePreviewEdit();
            const toggle = document.getElementById('livestream_enabled_edit');
            const fields = document.getElementById('livestream_fields_edit');
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

                const hasExistingCoords = !isNaN(initialLat) && !isNaN(initialLng) && (initialLat !== 0 || initialLng !== 0);

                if (!hasExistingCoords) {
                    initialLat = -6.200000;
                    initialLng = 106.816666;
                }

                pickerMap = L.map('locationPickerMap').setView([initialLat, initialLng], hasExistingCoords ? 17 : 5);

                const hybrid = L.tileLayer('https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
                    maxZoom: 20,
                    subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                    attribution: '&copy; Google Maps'
                });

                const streets = L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                    maxZoom: 20,
                    subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                    attribution: '&copy; Google Maps'
                });

                hybrid.addTo(pickerMap);
                L.control.layers({ "Hybrid": hybrid, "Streets": streets }).addTo(pickerMap);

                pickerMarker = L.marker([initialLat, initialLng], { draggable: true }).addTo(pickerMap);

                pickerMarker.on('dragend', function() {
                    const pos = pickerMarker.getLatLng();
                    updateCoordInputs(pos.lat, pos.lng);
                });

                // Click on map to move marker
                pickerMap.on('click', function(e) {
                    pickerMarker.setLatLng(e.latlng);
                    updateCoordInputs(e.latlng.lat, e.latlng.lng);
                });

                // If no saved coords, auto-locate user
                if (!hasExistingCoords) {
                    locateMe();
                }
            }
        });
    </script>
@endsection