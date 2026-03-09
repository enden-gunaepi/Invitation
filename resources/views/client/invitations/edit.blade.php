@extends('layouts.client')
@section('title', 'Edit Undangan')
@section('page-title', 'Edit Undangan')
@section('page-subtitle', $invitation->title)

@section('content')
    <div class="max-w-3xl space-y-6">
        {{-- Photo Gallery & Upload --}}
        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-base" style="color: var(--accent);"><i class="fas fa-images mr-2"></i> Foto Galeri
                </h3>
                @php
                    $maxPhotos = $invitation->package->max_photos ?? 10;
                    $currentPhotos = $invitation->photos->count();
                    $photoPercent = $maxPhotos > 0 ? min(100, round(($currentPhotos / $maxPhotos) * 100)) : 0;
                @endphp
                <span class="text-xs font-semibold"
                    style="color: var(--text-secondary);">{{ $currentPhotos }}/{{ $maxPhotos }}</span>
            </div>
            <div class="mb-4" style="background: var(--bg-tertiary); height: 4px; border-radius: 2px; overflow: hidden;">
                <div
                    style="width: {{ $photoPercent }}%; height: 100%; border-radius: 2px; transition: width 0.3s;
                background: {{ $photoPercent >= 90 ? 'var(--danger)' : ($photoPercent >= 70 ? 'var(--warning)' : 'var(--accent)') }};">
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
                                <form method="POST"
                                    action="{{ route('client.invitations.photos.destroy', [$invitation, $photo]) }}"
                                    onsubmit="return confirm('Hapus foto ini?')">
                                    @csrf @method('DELETE')
                                    <button class="w-8 h-8 rounded-full flex items-center justify-center"
                                        style="background: rgba(255,59,48,0.8);"><i
                                            class="fas fa-trash text-white text-xs"></i></button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-6 mb-4"
                    style="color: var(--text-tertiary); border: 2px dashed var(--border); border-radius: var(--radius);"><i
                        class="fas fa-images text-2xl mb-2"></i>
                    <p class="text-sm">Belum ada foto</p>
                </div>
            @endif
            @if ($currentPhotos < $maxPhotos)
                <form method="POST" action="{{ route('client.invitations.photos.store', $invitation) }}"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="flex gap-2 items-end">
                        <div class="flex-1"><label class="form-label">Pilih Foto</label><input type="file" name="photo"
                                class="form-input text-xs" accept="image/jpeg,image/png,image/webp" required></div>
                        <div style="width:140px;"><label class="form-label">Caption</label><input type="text"
                                name="caption" class="form-input" placeholder="Opsional"></div>
                        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-upload mr-1"></i>
                            Upload</button>
                    </div>
                </form>
            @endif
        </div>

        {{-- Main Edit Form --}}
        <form method="POST" action="{{ route('client.invitations.update', $invitation) }}" enctype="multipart/form-data">
            @csrf @method('PUT')

            {{-- Template & Package --}}
            <div class="card p-6 mb-6">
                <h3 class="font-bold text-base mb-4" style="color: var(--accent);"><i class="fas fa-layer-group mr-2"></i>
                    Template & Paket</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div><label class="form-label">Template</label>
                        <select name="template_id" class="form-input" required id="templateSelectEdit" onchange="updateTemplatePreviewEdit()">
                            @foreach ($templates as $t)
                                <option value="{{ $t->id }}" data-thumbnail="{{ $t->thumbnail ? asset('storage/' . $t->thumbnail) : '' }}"
                                    {{ $invitation->template_id == $t->id ? 'selected' : '' }}>{{ $t->name }}
                                    {{ $t->is_premium ? '⭐' : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div><label class="form-label">Paket</label>
                        <select name="package_id" class="form-input" required id="packageSelectEdit" onchange="filterTemplatesByPackageEdit()">
                            @foreach ($packages as $p)
                                <option value="{{ $p->id }}" data-templates='@json($p->allowed_template_ids ?? [])'
                                    {{ $invitation->package_id == $p->id ? 'selected' : '' }}>{{ $p->name }} —
                                    Rp{{ number_format($p->price, 0, ',', '.') }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div id="templatePreviewWrapEdit" class="p-3 rounded-lg mt-4" style="display:none; background: var(--bg-tertiary);">
                    <p class="text-xs mb-2" style="color: var(--text-secondary);">Preview Template</p>
                    <img id="templatePreviewEdit" src="" alt="Template Preview"
                        style="width:100%; max-height:200px; object-fit:cover; border-radius:10px; border:1px solid var(--border);">
                </div>
            </div>

            {{-- Event Info --}}
            <div class="card p-6 mb-6">
                <h3 class="font-bold text-base mb-4" style="color: var(--accent);"><i class="fas fa-calendar-alt mr-2"></i>
                    Informasi Acara</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                    <div><label class="form-label">Jenis Acara</label>
                        <select name="event_type" class="form-input" required>
                            @foreach (['wedding', 'birthday', 'graduation', 'corporate', 'other'] as $type)
                                <option value="{{ $type }}"
                                    {{ $invitation->event_type === $type ? 'selected' : '' }}>{{ ucfirst($type) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div><label class="form-label">Judul Acara</label><input type="text" name="title"
                            value="{{ old('title', $invitation->title) }}" class="form-input" required></div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-5">
                    <div><label class="form-label">Mempelai Pria</label><input type="text" name="groom_name"
                            value="{{ old('groom_name', $invitation->groom_name) }}" class="form-input"></div>
                    <div><label class="form-label">Mempelai Wanita</label><input type="text" name="bride_name"
                            value="{{ old('bride_name', $invitation->bride_name) }}" class="form-input"></div>
                    <div><label class="form-label">Host</label><input type="text" name="host_name"
                            value="{{ old('host_name', $invitation->host_name) }}" class="form-input"></div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                    <div><label class="form-label">Nama Orang Tua Pria</label><input type="text" name="groom_parent_name"
                            value="{{ old('groom_parent_name', $invitation->groom_parent_name) }}" class="form-input"></div>
                    <div><label class="form-label">Nama Orang Tua Wanita</label><input type="text" name="bride_parent_name"
                            value="{{ old('bride_parent_name', $invitation->bride_parent_name) }}" class="form-input"></div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                    <div><label class="form-label">Instagram Pria</label><input type="text" name="groom_instagram"
                            value="{{ old('groom_instagram', $invitation->groom_instagram) }}" class="form-input" placeholder="username atau url"></div>
                    <div><label class="form-label">Instagram Wanita</label><input type="text" name="bride_instagram"
                            value="{{ old('bride_instagram', $invitation->bride_instagram) }}" class="form-input" placeholder="username atau url"></div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                    <div><label class="form-label">Facebook Pria</label><input type="text" name="groom_facebook"
                            value="{{ old('groom_facebook', $invitation->groom_facebook) }}" class="form-input" placeholder="url profile"></div>
                    <div><label class="form-label">Facebook Wanita</label><input type="text" name="bride_facebook"
                            value="{{ old('bride_facebook', $invitation->bride_facebook) }}" class="form-input" placeholder="url profile"></div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                    <div><label class="form-label">Tanggal</label><input type="date" name="event_date"
                            value="{{ old('event_date', $invitation->event_date->format('Y-m-d')) }}" class="form-input"
                            required></div>
                    <div><label class="form-label">Waktu</label><input type="time" name="event_time"
                            value="{{ old('event_time', \Carbon\Carbon::parse($invitation->event_time)->format('H:i')) }}"
                            class="form-input" required></div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div><label class="form-label">Nama Tempat</label><input type="text" name="venue_name"
                            value="{{ old('venue_name', $invitation->venue_name) }}" class="form-input" required></div>
                    <div><label class="form-label">Google Maps URL</label><input type="url" name="google_maps_url"
                            value="{{ old('google_maps_url', $invitation->google_maps_url) }}" class="form-input"></div>
                </div>
                <div class="mt-4"><label class="form-label">Alamat Lengkap</label>
                    <textarea name="venue_address" class="form-input" rows="2" required>{{ old('venue_address', $invitation->venue_address) }}</textarea>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mt-4">
                    <div><label class="form-label">Link Live Streaming (opsional)</label><input type="url" name="livestream_url"
                            value="{{ old('livestream_url', $invitation->livestream_url) }}" class="form-input"></div>
                    <div><label class="form-label">Label Live Streaming</label><input type="text" name="livestream_label"
                            value="{{ old('livestream_label', $invitation->livestream_label) }}" class="form-input" placeholder="Live Streaming"></div>
                </div>
            </div>

            {{-- Akad & Resepsi --}}
            <div class="card p-6 mb-6" x-data="{ events: {{ json_encode($invitation->events->count() > 0 ? $invitation->events->map(fn($e) => ['event_name' => $e->event_name, 'event_description' => $e->event_description, 'event_date' => $e->event_date->format('Y-m-d'), 'event_time' => $e->event_time, 'event_end_time' => $e->event_end_time, 'venue_name' => $e->venue_name, 'venue_address' => $e->venue_address, 'venue_maps_url' => $e->venue_maps_url])->values() : [['event_name' => 'Akad Nikah', 'event_description' => '', 'event_date' => '', 'event_time' => '', 'event_end_time' => '', 'venue_name' => '', 'venue_address' => '', 'venue_maps_url' => ''], ['event_name' => 'Resepsi', 'event_description' => '', 'event_date' => '', 'event_time' => '', 'event_end_time' => '', 'venue_name' => '', 'venue_address' => '', 'venue_maps_url' => '']]) }} }">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-base" style="color: var(--accent);"><i class="fas fa-mosque mr-2"></i> Akad
                        & Resepsi</h3>
                    <button type="button"
                        @click="events.push({event_name:'',event_description:'',event_date:'',event_time:'',event_end_time:'',venue_name:'',venue_address:'',venue_maps_url:''})"
                        class="btn btn-secondary btn-sm"><i class="fas fa-plus mr-1"></i> Tambah</button>
                </div>
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
                                    :name="'events[' + idx + '][event_name]'" x-model="ev.event_name" class="form-input"
                                    placeholder="Akad Nikah / Resepsi"></div>
                            <div><label class="form-label text-xs">Tempat</label><input type="text"
                                    :name="'events[' + idx + '][venue_name]'" x-model="ev.venue_name" class="form-input">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-3">
                            <div><label class="form-label text-xs">Tanggal</label><input type="date"
                                    :name="'events[' + idx + '][event_date]'" x-model="ev.event_date" class="form-input">
                            </div>
                            <div><label class="form-label text-xs">Mulai</label><input type="time"
                                    :name="'events[' + idx + '][event_time]'" x-model="ev.event_time" class="form-input">
                            </div>
                            <div><label class="form-label text-xs">Selesai</label><input type="time"
                                    :name="'events[' + idx + '][event_end_time]'" x-model="ev.event_end_time"
                                    class="form-input"></div>
                        </div>
                        <div><label class="form-label text-xs">Alamat</label><input type="text"
                                :name="'events[' + idx + '][venue_address]'" x-model="ev.venue_address" class="form-input">
                        </div>
                        <div class="mt-3"><label class="form-label text-xs">Deskripsi Rundown</label>
                            <textarea :name="'events[' + idx + '][event_description]'" x-model="ev.event_description" class="form-input" rows="2"
                                placeholder="Contoh: Prosesi akad dimulai dengan pembacaan ayat suci..."></textarea>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Love Stories --}}
            <div class="card p-6 mb-6" x-data="{ stories: {{ json_encode($invitation->loveStories->count() > 0 ? $invitation->loveStories->map(fn($s) => ['year' => $s->year, 'title' => $s->title, 'description' => $s->description, 'photo_path' => $s->photo_path])->values() : [['year' => '', 'title' => '', 'description' => '', 'photo_path' => '']]) }} }">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-base" style="color: var(--accent);"><i class="fas fa-heart mr-2"></i> Love
                        Stories</h3>
                    <button type="button" @click="stories.push({year:'',title:'',description:'',photo_path:''})"
                        class="btn btn-secondary btn-sm"><i class="fas fa-plus mr-1"></i> Tambah</button>
                </div>
                <template x-for="(story, idx) in stories" :key="idx">
                    <div class="p-4 mb-3 rounded-lg"
                        style="background: var(--bg-tertiary); border: 1px solid var(--border);">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-bold" style="color: var(--accent);"
                                x-text="'Story ' + (idx+1)"></span>
                            <button type="button" @click="stories.splice(idx,1)" class="text-xs"
                                style="color: var(--danger);" x-show="stories.length > 1"><i
                                    class="fas fa-trash mr-1"></i> Hapus</button>
                        </div>
                        <div class="grid grid-cols-4 gap-3 mb-3">
                            <div><label class="form-label text-xs">Tahun</label><input type="text"
                                    :name="'love_stories[' + idx + '][year]'" x-model="story.year" class="form-input"
                                    placeholder="2020"></div>
                            <div class="col-span-3"><label class="form-label text-xs">Judul</label><input type="text"
                                    :name="'love_stories[' + idx + '][title]'" x-model="story.title" class="form-input"
                                    placeholder="Pertama Bertemu"></div>
                        </div>
                        <div><label class="form-label text-xs">Deskripsi</label>
                            <textarea :name="'love_stories[' + idx + '][description]'" x-model="story.description" class="form-input" rows="2"
                                placeholder="Ceritakan momen spesial..."></textarea>
                        </div>
                        <div class="mt-3">
                            <label class="form-label text-xs">Foto Story (opsional)</label>
                            <input type="file" :name="'love_story_photos[' + idx + ']'" class="form-input" accept="image/*">
                            <template x-if="story.photo_path">
                                <div class="mt-2">
                                    <img :src="'{{ asset('storage') }}/' + story.photo_path" alt="Story Photo"
                                        style="width:56px; height:56px; object-fit:cover; border-radius:8px; border:1px solid var(--border);">
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Gift / Hadiah --}}
            <div class="card p-6 mb-6" x-data="{ accounts: {{ json_encode($invitation->bankAccounts->count() ? $invitation->bankAccounts->map(fn($a) => ['bank_name' => $a->bank_name, 'account_number' => $a->account_number, 'account_name' => $a->account_name])->values() : [['bank_name' => $invitation->bank_name ?? '', 'account_number' => $invitation->bank_account_number ?? '', 'account_name' => $invitation->bank_account_name ?? '']]) }} }">
                <h3 class="font-bold text-base mb-4" style="color: var(--accent);"><i class="fas fa-gift mr-2"></i>
                    Hadiah & Transfer</h3>
                <div class="flex items-center justify-between mb-3">
                    <p class="text-sm font-semibold">Daftar Rekening</p>
                    <button type="button" @click="accounts.push({bank_name:'',account_number:'',account_name:''})"
                        class="btn btn-secondary btn-sm"><i class="fas fa-plus mr-1"></i> Tambah Rekening</button>
                </div>
                <template x-for="(acc, idx) in accounts" :key="idx">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-3 p-3 rounded-lg"
                        style="background: var(--bg-tertiary); border: 1px solid var(--border);">
                        <div><label class="form-label text-xs">Nama Bank</label>
                            <input type="text" :name="'bank_accounts[' + idx + '][bank_name]'" x-model="acc.bank_name"
                                class="form-input"></div>
                        <div><label class="form-label text-xs">No. Rekening</label>
                            <input type="text" :name="'bank_accounts[' + idx + '][account_number]'" x-model="acc.account_number"
                                class="form-input"></div>
                        <div><label class="form-label text-xs">Atas Nama</label>
                            <div class="flex gap-2">
                                <input type="text" :name="'bank_accounts[' + idx + '][account_name]'" x-model="acc.account_name"
                                    class="form-input">
                                <button type="button" @click="accounts.splice(idx,1)" class="btn btn-secondary btn-sm"
                                    x-show="accounts.length > 1"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    </div>
                </template>
                <div><label class="form-label">Alamat Kirim Hadiah</label>
                    <textarea name="gift_address" class="form-input" rows="2"
                        placeholder="Alamat untuk pengiriman hadiah fisik...">{{ old('gift_address', $invitation->gift_address) }}</textarea>
                </div>
            </div>

            {{-- Text --}}
            <div class="card p-6 mb-6">
                <h3 class="font-bold text-base mb-4" style="color: var(--accent);"><i class="fas fa-align-left mr-2"></i>
                    Teks Undangan</h3>
                <div class="mb-4"><label class="form-label">Teks Pembuka</label>
                    <textarea name="opening_text" class="form-input" rows="3">{{ old('opening_text', $invitation->opening_text) }}</textarea>
                </div>
                <div><label class="form-label">Teks Penutup</label>
                    <textarea name="closing_text" class="form-input" rows="3">{{ old('closing_text', $invitation->closing_text) }}</textarea>
                </div>
            </div>

            {{-- Media --}}
            <div class="card p-6 mb-6">
                <h3 class="font-bold text-base mb-4" style="color: var(--accent);"><i class="fas fa-music mr-2"></i>
                    Media</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="form-label">Cover Photo</label><input type="file" name="cover_photo"
                            class="form-input" accept="image/*">
                        @if ($invitation->cover_photo)
                            <div class="mt-2 flex items-center gap-2"><img
                                    src="{{ asset('storage/' . $invitation->cover_photo) }}"
                                    class="w-12 h-12 rounded object-cover" style="border: 1px solid var(--border);"><span
                                    class="text-xs" style="color: var(--text-secondary);">Cover saat ini</span></div>
                        @endif
                        <div class="mt-4">
                            <label class="form-label">Foto Mempelai Pria</label>
                            <input type="file" name="groom_photo" class="form-input" accept="image/*">
                            @if($invitation->groom_photo)
                                <img src="{{ asset('storage/' . $invitation->groom_photo) }}" class="w-12 h-12 rounded object-cover mt-2" style="border:1px solid var(--border);">
                            @endif
                        </div>
                        <div class="mt-4">
                            <label class="form-label">Foto Mempelai Wanita</label>
                            <input type="file" name="bride_photo" class="form-input" accept="image/*">
                            @if($invitation->bride_photo)
                                <img src="{{ asset('storage/' . $invitation->bride_photo) }}" class="w-12 h-12 rounded object-cover mt-2" style="border:1px solid var(--border);">
                            @endif
                        </div>
                    </div>
                    <div class="mb-6">
                        <label class="form-label">Upload Musik (max 20MB)</label>
                        <input type="file" name="music_url" class="form-input" accept="audio/*">
                        @error('music_url')
                            <p class="text-xs mt-1" style="color: var(--danger);">{{ $message }}</p>
                        @enderror
                        @if ($invitation->music_url)
                            <p class="text-xs mt-2" style="color: var(--text-secondary);">Musik saat ini:
                                <a href="{{ asset('storage/' . $invitation->music_url) }}" target="_blank"
                                    style="color: var(--accent);">Preview audio</a>
                            </p>
                        @endif
                    </div>
                    <div class="mb-6">
                        <label class="form-label">Pilih dari Library Musik</label>
                        <select name="music_track_id" class="form-input">
                            <option value="">-- Tetap gunakan musik saat ini --</option>
                            @foreach($musicTracks as $track)
                                <option value="{{ $track->id }}">{{ $track->title ?: basename($track->file_path) }}</option>
                            @endforeach
                        </select>
                        @error('music_track_id')
                            <p class="text-xs mt-1" style="color: var(--danger);">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="card p-6 mb-6">
                <h3 class="font-bold text-base mb-4" style="color: var(--accent);"><i
                        class="fas fa-shoe-prints mr-2"></i> Footer</h3>
                <div><label class="form-label">Teks Footer</label><input type="text" name="footer_text"
                        value="{{ old('footer_text', $invitation->footer_text) }}" class="form-input"
                        placeholder="Made with ♥ by YourBrand"></div>
                <p class="text-xs mt-1" style="color: var(--text-tertiary);">Kosongkan untuk menggunakan footer default.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="btn btn-primary text-sm"><i class="fas fa-save mr-2"></i> Update
                    Undangan</button>
                <a href="{{ route('client.invitations.show', $invitation) }}" class="btn btn-secondary text-sm">Batal</a>
            </div>
        </form>
    </div>

    <script>
        function updateTemplatePreviewEdit() {
            const sel = document.getElementById('templateSelectEdit');
            const opt = sel.options[sel.selectedIndex];
            const wrap = document.getElementById('templatePreviewWrapEdit');
            const img = document.getElementById('templatePreviewEdit');
            const thumb = opt?.dataset?.thumbnail || '';
            if (!thumb) { wrap.style.display = 'none'; img.src = ''; return; }
            img.src = thumb;
            wrap.style.display = 'block';
        }
        function filterTemplatesByPackageEdit() {
            const packageSelect = document.getElementById('packageSelectEdit');
            const templateSelect = document.getElementById('templateSelectEdit');
            const selectedPackage = packageSelect.options[packageSelect.selectedIndex];
            const allowed = JSON.parse(selectedPackage?.dataset?.templates || '[]').map((id) => String(id));
            const useAll = allowed.length === 0;

            [...templateSelect.options].forEach((opt) => {
                const isAllowed = useAll || allowed.includes(opt.value);
                opt.hidden = !isAllowed;
                opt.disabled = !isAllowed;
                if (!isAllowed && opt.selected) {
                    const firstAllowed = [...templateSelect.options].find((o) => !o.disabled);
                    if (firstAllowed) firstAllowed.selected = true;
                }
            });
            updateTemplatePreviewEdit();
        }

        document.addEventListener('DOMContentLoaded', () => {
            filterTemplatesByPackageEdit();
            updateTemplatePreviewEdit();
        });
    </script>
@endsection
