@extends('layouts.client')
@section('title', 'Edit Undangan')
@section('page-title', 'Edit Undangan')
@section('page-subtitle', $invitation->title)

@section('content')
<div class="max-w-3xl space-y-6">
    {{-- Photo Gallery & Upload --}}
    <div class="card p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-base" style="color: var(--accent);"><i class="fas fa-images mr-2"></i> Foto Galeri</h3>
            @php
                $maxPhotos = $invitation->package->max_photos ?? 10;
                $currentPhotos = $invitation->photos->count();
                $photoPercent = $maxPhotos > 0 ? min(100, round(($currentPhotos / $maxPhotos) * 100)) : 0;
            @endphp
            <span class="text-xs font-semibold" style="color: var(--text-secondary);">{{ $currentPhotos }}/{{ $maxPhotos }}</span>
        </div>
        <div class="mb-4" style="background: var(--bg-tertiary); height: 4px; border-radius: 2px; overflow: hidden;">
            <div style="width: {{ $photoPercent }}%; height: 100%; border-radius: 2px; transition: width 0.3s;
                background: {{ $photoPercent >= 90 ? 'var(--danger)' : ($photoPercent >= 70 ? 'var(--warning)' : 'var(--accent)') }};"></div>
        </div>
        @if($invitation->photos->count())
        <div class="grid grid-cols-3 sm:grid-cols-4 gap-3 mb-4">
            @foreach($invitation->photos as $photo)
            <div class="relative group" style="aspect-ratio: 1; border-radius: var(--radius-sm); overflow: hidden; border: 1px solid var(--border);">
                <img src="{{ asset('storage/' . $photo->file_path) }}" alt="{{ $photo->caption }}" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                    <form method="POST" action="{{ route('client.invitations.photos.destroy', [$invitation, $photo]) }}" onsubmit="return confirm('Hapus foto ini?')">
                        @csrf @method('DELETE')
                        <button class="w-8 h-8 rounded-full flex items-center justify-center" style="background: rgba(255,59,48,0.8);"><i class="fas fa-trash text-white text-xs"></i></button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-6 mb-4" style="color: var(--text-tertiary); border: 2px dashed var(--border); border-radius: var(--radius);"><i class="fas fa-images text-2xl mb-2"></i><p class="text-sm">Belum ada foto</p></div>
        @endif
        @if($currentPhotos < $maxPhotos)
        <form method="POST" action="{{ route('client.invitations.photos.store', $invitation) }}" enctype="multipart/form-data">
            @csrf
            <div class="flex gap-2 items-end">
                <div class="flex-1"><label class="form-label">Pilih Foto</label><input type="file" name="photo" class="form-input text-xs" accept="image/jpeg,image/png,image/webp" required></div>
                <div style="width:140px;"><label class="form-label">Caption</label><input type="text" name="caption" class="form-input" placeholder="Opsional"></div>
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-upload mr-1"></i> Upload</button>
            </div>
        </form>
        @endif
    </div>

    {{-- Main Edit Form --}}
    <form method="POST" action="{{ route('client.invitations.update', $invitation) }}" enctype="multipart/form-data">
        @csrf @method('PUT')

        {{-- Template & Package --}}
        <div class="card p-6 mb-6">
            <h3 class="font-bold text-base mb-4" style="color: var(--accent);"><i class="fas fa-layer-group mr-2"></i> Template & Paket</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div><label class="form-label">Template</label>
                    <select name="template_id" class="form-input" required>
                        @foreach($templates as $t)<option value="{{ $t->id }}" {{ $invitation->template_id == $t->id ? 'selected' : '' }}>{{ $t->name }} {{ $t->is_premium ? '⭐' : '' }}</option>@endforeach
                    </select></div>
                <div><label class="form-label">Paket</label>
                    <select name="package_id" class="form-input" required>
                        @foreach($packages as $p)<option value="{{ $p->id }}" {{ $invitation->package_id == $p->id ? 'selected' : '' }}>{{ $p->name }} — Rp{{ number_format($p->price, 0, ',', '.') }}</option>@endforeach
                    </select></div>
            </div>
        </div>

        {{-- Event Info --}}
        <div class="card p-6 mb-6">
            <h3 class="font-bold text-base mb-4" style="color: var(--accent);"><i class="fas fa-calendar-alt mr-2"></i> Informasi Acara</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                <div><label class="form-label">Jenis Acara</label>
                    <select name="event_type" class="form-input" required>
                        @foreach(['wedding', 'birthday', 'graduation', 'corporate', 'other'] as $type)
                        <option value="{{ $type }}" {{ $invitation->event_type === $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                        @endforeach
                    </select></div>
                <div><label class="form-label">Judul Acara</label><input type="text" name="title" value="{{ old('title', $invitation->title) }}" class="form-input" required></div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-5">
                <div><label class="form-label">Mempelai Pria</label><input type="text" name="groom_name" value="{{ old('groom_name', $invitation->groom_name) }}" class="form-input"></div>
                <div><label class="form-label">Mempelai Wanita</label><input type="text" name="bride_name" value="{{ old('bride_name', $invitation->bride_name) }}" class="form-input"></div>
                <div><label class="form-label">Host</label><input type="text" name="host_name" value="{{ old('host_name', $invitation->host_name) }}" class="form-input"></div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                <div><label class="form-label">Tanggal</label><input type="date" name="event_date" value="{{ old('event_date', $invitation->event_date->format('Y-m-d')) }}" class="form-input" required></div>
                <div><label class="form-label">Waktu</label><input type="time" name="event_time" value="{{ old('event_time', \Carbon\Carbon::parse($invitation->event_time)->format('H:i')) }}" class="form-input" required></div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div><label class="form-label">Nama Tempat</label><input type="text" name="venue_name" value="{{ old('venue_name', $invitation->venue_name) }}" class="form-input" required></div>
                <div><label class="form-label">Google Maps URL</label><input type="url" name="google_maps_url" value="{{ old('google_maps_url', $invitation->google_maps_url) }}" class="form-input"></div>
            </div>
            <div class="mt-4"><label class="form-label">Alamat Lengkap</label><textarea name="venue_address" class="form-input" rows="2" required>{{ old('venue_address', $invitation->venue_address) }}</textarea></div>
        </div>

        {{-- Akad & Resepsi --}}
        <div class="card p-6 mb-6" x-data="{ events: {{ json_encode($invitation->events->count() > 0 ? $invitation->events->map(fn($e) => ['event_name' => $e->event_name, 'event_date' => $e->event_date->format('Y-m-d'), 'event_time' => $e->event_time, 'event_end_time' => $e->event_end_time, 'venue_name' => $e->venue_name, 'venue_address' => $e->venue_address, 'venue_maps_url' => $e->venue_maps_url])->values() : [['event_name' => 'Akad Nikah', 'event_date' => '', 'event_time' => '', 'event_end_time' => '', 'venue_name' => '', 'venue_address' => '', 'venue_maps_url' => ''], ['event_name' => 'Resepsi', 'event_date' => '', 'event_time' => '', 'event_end_time' => '', 'venue_name' => '', 'venue_address' => '', 'venue_maps_url' => '']]) }} }">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-base" style="color: var(--accent);"><i class="fas fa-mosque mr-2"></i> Akad & Resepsi</h3>
                <button type="button" @click="events.push({event_name:'',event_date:'',event_time:'',event_end_time:'',venue_name:'',venue_address:'',venue_maps_url:''})" class="btn btn-secondary btn-sm"><i class="fas fa-plus mr-1"></i> Tambah</button>
            </div>
            <template x-for="(ev, idx) in events" :key="idx">
                <div class="p-4 mb-3 rounded-lg" style="background: var(--bg-tertiary); border: 1px solid var(--border);">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold" style="color: var(--accent);" x-text="'Acara ' + (idx+1)"></span>
                        <button type="button" @click="events.splice(idx,1)" class="text-xs" style="color: var(--danger);" x-show="events.length > 1"><i class="fas fa-trash mr-1"></i> Hapus</button>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                        <div><label class="form-label text-xs">Nama Acara</label><input type="text" :name="'events['+idx+'][event_name]'" x-model="ev.event_name" class="form-input" placeholder="Akad Nikah / Resepsi"></div>
                        <div><label class="form-label text-xs">Tempat</label><input type="text" :name="'events['+idx+'][venue_name]'" x-model="ev.venue_name" class="form-input"></div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-3">
                        <div><label class="form-label text-xs">Tanggal</label><input type="date" :name="'events['+idx+'][event_date]'" x-model="ev.event_date" class="form-input"></div>
                        <div><label class="form-label text-xs">Mulai</label><input type="time" :name="'events['+idx+'][event_time]'" x-model="ev.event_time" class="form-input"></div>
                        <div><label class="form-label text-xs">Selesai</label><input type="time" :name="'events['+idx+'][event_end_time]'" x-model="ev.event_end_time" class="form-input"></div>
                    </div>
                    <div><label class="form-label text-xs">Alamat</label><input type="text" :name="'events['+idx+'][venue_address]'" x-model="ev.venue_address" class="form-input"></div>
                </div>
            </template>
        </div>

        {{-- Love Stories --}}
        <div class="card p-6 mb-6" x-data="{ stories: {{ json_encode($invitation->loveStories->count() > 0 ? $invitation->loveStories->map(fn($s) => ['year' => $s->year, 'title' => $s->title, 'description' => $s->description])->values() : [['year' => '', 'title' => '', 'description' => '']]) }} }">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-base" style="color: var(--accent);"><i class="fas fa-heart mr-2"></i> Love Stories</h3>
                <button type="button" @click="stories.push({year:'',title:'',description:''})" class="btn btn-secondary btn-sm"><i class="fas fa-plus mr-1"></i> Tambah</button>
            </div>
            <template x-for="(story, idx) in stories" :key="idx">
                <div class="p-4 mb-3 rounded-lg" style="background: var(--bg-tertiary); border: 1px solid var(--border);">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold" style="color: var(--accent);" x-text="'Story ' + (idx+1)"></span>
                        <button type="button" @click="stories.splice(idx,1)" class="text-xs" style="color: var(--danger);" x-show="stories.length > 1"><i class="fas fa-trash mr-1"></i> Hapus</button>
                    </div>
                    <div class="grid grid-cols-4 gap-3 mb-3">
                        <div><label class="form-label text-xs">Tahun</label><input type="text" :name="'love_stories['+idx+'][year]'" x-model="story.year" class="form-input" placeholder="2020"></div>
                        <div class="col-span-3"><label class="form-label text-xs">Judul</label><input type="text" :name="'love_stories['+idx+'][title]'" x-model="story.title" class="form-input" placeholder="Pertama Bertemu"></div>
                    </div>
                    <div><label class="form-label text-xs">Deskripsi</label><textarea :name="'love_stories['+idx+'][description]'" x-model="story.description" class="form-input" rows="2" placeholder="Ceritakan momen spesial..."></textarea></div>
                </div>
            </template>
        </div>

        {{-- Gift / Hadiah --}}
        <div class="card p-6 mb-6">
            <h3 class="font-bold text-base mb-4" style="color: var(--accent);"><i class="fas fa-gift mr-2"></i> Hadiah & Transfer</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-4">
                <div><label class="form-label">Nama Bank</label><input type="text" name="bank_name" value="{{ old('bank_name', $invitation->bank_name) }}" class="form-input" placeholder="BCA, BNI, Mandiri..."></div>
                <div><label class="form-label">No. Rekening</label><input type="text" name="bank_account_number" value="{{ old('bank_account_number', $invitation->bank_account_number) }}" class="form-input" placeholder="1234567890"></div>
                <div><label class="form-label">Atas Nama</label><input type="text" name="bank_account_name" value="{{ old('bank_account_name', $invitation->bank_account_name) }}" class="form-input" placeholder="Nama pemilik rekening"></div>
            </div>
            <div><label class="form-label">Alamat Kirim Hadiah</label><textarea name="gift_address" class="form-input" rows="2" placeholder="Alamat untuk pengiriman hadiah fisik...">{{ old('gift_address', $invitation->gift_address) }}</textarea></div>
        </div>

        {{-- Text --}}
        <div class="card p-6 mb-6">
            <h3 class="font-bold text-base mb-4" style="color: var(--accent);"><i class="fas fa-align-left mr-2"></i> Teks Undangan</h3>
            <div class="mb-4"><label class="form-label">Teks Pembuka</label><textarea name="opening_text" class="form-input" rows="3">{{ old('opening_text', $invitation->opening_text) }}</textarea></div>
            <div><label class="form-label">Teks Penutup</label><textarea name="closing_text" class="form-input" rows="3">{{ old('closing_text', $invitation->closing_text) }}</textarea></div>
        </div>

        {{-- Media --}}
        <div class="card p-6 mb-6">
            <h3 class="font-bold text-base mb-4" style="color: var(--accent);"><i class="fas fa-music mr-2"></i> Media</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="form-label">Cover Photo</label><input type="file" name="cover_photo" class="form-input" accept="image/*">
                    @if($invitation->cover_photo)<div class="mt-2 flex items-center gap-2"><img src="{{ asset('storage/' . $invitation->cover_photo) }}" class="w-12 h-12 rounded object-cover" style="border: 1px solid var(--border);"><span class="text-xs" style="color: var(--text-secondary);">Cover saat ini</span></div>@endif
                </div>
                <div>
                    <label class="form-label">Background Music (MP3)</label><input type="file" name="music_url" class="form-input" accept="audio/mpeg,audio/mp3">
                    @if($invitation->music_url)<p class="text-xs mt-1" style="color: var(--success);"><i class="fas fa-check mr-1"></i> Music sudah diupload</p>@endif
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="card p-6 mb-6">
            <h3 class="font-bold text-base mb-4" style="color: var(--accent);"><i class="fas fa-shoe-prints mr-2"></i> Footer</h3>
            <div><label class="form-label">Teks Footer</label><input type="text" name="footer_text" value="{{ old('footer_text', $invitation->footer_text) }}" class="form-input" placeholder="Made with ♥ by YourBrand"></div>
            <p class="text-xs mt-1" style="color: var(--text-tertiary);">Kosongkan untuk menggunakan footer default.</p>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="btn btn-primary text-sm"><i class="fas fa-save mr-2"></i> Update Undangan</button>
            <a href="{{ route('client.invitations.show', $invitation) }}" class="btn btn-secondary text-sm">Batal</a>
        </div>
    </form>
</div>
@endsection
