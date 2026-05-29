@extends('layouts.client')
@section('title', $invitation->title)
@section('page-title', $invitation->title)
@section('page-subtitle', ucfirst($invitation->event_type) . ' - ' . $invitation->event_date->format('d M Y'))

@section('content')
<div class="card p-6 mb-6 flex flex-col md:flex-row items-center justify-between gap-6 overflow-hidden relative" style="background: linear-gradient(135deg, rgba(255,255,255,.9), rgba(248,250,252,.8)); border: 1px solid rgba(148, 163, 184, .22);">
    <div class="space-y-2 z-10">
        <h2 class="text-lg font-bold text-primary">Detail Undangan: {{ $invitation->title }}</h2>
        <p class="text-sm max-w-lg" style="color: var(--text-secondary);">Pantau analitik kunjungan, konfirmasi RSVP dari tamu, kelola daftar kolaborator, dan jadwalkan blast WhatsApp reminder secara real-time.</p>
    </div>
    <div class="shrink-0 z-10">
        <img src="{{ asset('assets/maskot/lihatundangan.png') }}" alt="Lihat Undangan Mascot" class="h-24 w-auto drop-shadow-sm transition-transform duration-300 hover:scale-105" style="animation: float 4s ease-in-out infinite;">
    </div>
</div>

<style>
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-8px); }
    }
</style>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Main Content --}}
    <div class="lg:col-span-2 space-y-6">
        {{-- Event Info Card --}}
        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-base">Informasi Acara</h3>
                <span class="badge badge-{{ $invitation->status }}">{{ ucfirst($invitation->status) }}</span>
            </div>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div><span style="color: var(--text-secondary);">Jenis Acara</span><p class="font-semibold mt-1">{{ ucfirst($invitation->event_type) }}</p></div>
                <div><span style="color: var(--text-secondary);">Tanggal</span><p class="font-semibold mt-1">{{ $invitation->event_date->format('d F Y') }}</p></div>
                <div><span style="color: var(--text-secondary);">Waktu</span><p class="font-semibold mt-1">{{ $invitation->event_time }}</p></div>
                <div><span style="color: var(--text-secondary);">Tempat</span><p class="font-semibold mt-1">{{ $invitation->venue_name }}</p></div>
                @if($invitation->groom_name)
                <div><span style="color: var(--text-secondary);">Mempelai Pria</span><p class="font-semibold mt-1">{{ $invitation->groom_name }}</p></div>
                @endif
                @if($invitation->bride_name)
                <div><span style="color: var(--text-secondary);">Mempelai Wanita</span><p class="font-semibold mt-1">{{ $invitation->bride_name }}</p></div>
                @endif
            </div>
            <div class="mt-4 p-3 rounded-lg text-sm" style="background: var(--bg-tertiary); color: var(--text-secondary);">
                <i class="fas fa-map-marker-alt mr-2" style="color: var(--accent);"></i> {{ $invitation->venue_address }}
            </div>
        </div>

        {{-- Admin Notes --}}
        @if($invitation->admin_notes)
        <div class="card p-6" style="border-color: var(--warning);">
            <h3 class="font-bold text-sm mb-2" style="color: var(--warning);"><i class="fas fa-sticky-note mr-2"></i> Catatan Admin</h3>
            <p class="text-sm" style="color: var(--text-secondary);">{{ $invitation->admin_notes }}</p>
        </div>
        @endif

        {{-- Photo Gallery + Upload --}}
        <div class="card overflow-hidden">
            <div class="px-6 py-4 border-b" style="border-color: var(--border);">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-bold text-base">Foto ({{ $currentPhotos }}/{{ $maxPhotos }})</h3>
                </div>
                @php $photoPercent = $maxPhotos > 0 ? min(100, round(($currentPhotos / $maxPhotos) * 100)) : 0; @endphp
                <div style="background: var(--bg-tertiary); height: 4px; border-radius: 2px; overflow: hidden;">
                    <div style="width: {{ $photoPercent }}%; height: 100%; border-radius: 2px; transition: width 0.3s;
                        background: {{ $photoPercent >= 90 ? 'var(--danger)' : ($photoPercent >= 70 ? 'var(--warning)' : 'var(--accent)') }};"></div>
                </div>
            </div>
            <div class="p-4">
                @if($invitation->photos->count())
                <div class="grid grid-cols-3 sm:grid-cols-4 gap-2 mb-4">
                    @foreach($invitation->photos as $photo)
                    <div class="relative group" style="aspect-ratio: 1; border-radius: var(--radius-sm); overflow: hidden;">
                        <img src="{{ asset('storage/' . $photo->file_path) }}" alt="{{ $photo->caption }}" class="w-full h-full object-cover">
                        <form method="POST" action="{{ route('client.invitations.photos.destroy', [$invitation, $photo]) }}" onsubmit="return confirm('Hapus foto ini?')"
                               class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition">
                            @csrf @method('DELETE')
                            <button class="w-6 h-6 rounded-full flex items-center justify-center" style="background: rgba(0,0,0,0.6);">
                                <i class="fas fa-times text-white text-xs"></i>
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
                @endif

                @if($currentPhotos < $maxPhotos)
                <form method="POST" action="{{ route('client.invitations.photos.store', $invitation) }}" enctype="multipart/form-data" class="flex gap-2 items-end">
                    @csrf
                    <div class="flex-1">
                        <label class="form-label text-[10px] uppercase font-bold tracking-wider mb-1 block" style="color: var(--text-secondary);">Upload beberapa foto sekaligus</label>
                        <input type="file" name="photos[]" class="form-input text-xs" accept="image/*" required multiple>
                    </div>
                    <div class="w-36">
                        <label class="form-label text-[10px] uppercase font-bold tracking-wider mb-1 block" style="color: var(--text-secondary);">Caption (Opsional)</label>
                        <input type="text" name="caption" class="form-input" placeholder="Caption">
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm h-[38px] flex items-center justify-center px-4" title="Upload Foto"><i class="fas fa-upload mr-1"></i> Upload</button>
                </form>
                @else
                <p class="text-xs text-center py-2" style="color: var(--text-secondary);">
                    <i class="fas fa-lock mr-1"></i> Batas foto tercapai. Upgrade paket untuk menambah foto.
                </p>
                @endif
            </div>
        </div>

        {{-- Love Story Management --}}
        <div class="card overflow-hidden">
            <div class="px-6 py-4 border-b" style="border-color: var(--border);">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-base"><i class="fas fa-heart mr-2" style="color: var(--accent);"></i>Love Story</h3>
                    <span class="badge badge-default">{{ $invitation->loveStories->count() }} Story</span>
                </div>
                <p class="text-xs mt-1" style="color: var(--text-secondary);">Kelola timeline love story untuk section template undangan.</p>
            </div>
            <div class="p-4">
                <form
                    method="POST"
                    action="{{ route('client.invitations.love-stories.update', $invitation) }}"
                    enctype="multipart/form-data"
                    x-data="{ stories: {{ json_encode($invitation->loveStories->count() > 0 ? $invitation->loveStories->map(fn($s) => ['year' => $s->year, 'title' => $s->title, 'description' => $s->description, 'photo_path' => $s->photo_path])->values() : [['year' => '', 'title' => '', 'description' => '', 'photo_path' => '']]) }} }"
                    class="space-y-3"
                >
                    @csrf

                    <div class="flex items-center justify-between">
                        <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--text-secondary);">Daftar Story</p>
                    </div>

                    <template x-for="(story, idx) in stories" :key="idx">
                        <div class="p-4 rounded-lg space-y-3" style="background: var(--bg-tertiary); border: 1px solid var(--border);">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold" style="color: var(--accent);" x-text="'Story ' + (idx + 1)"></span>
                                <button type="button" @click="stories.splice(idx, 1)" x-show="stories.length > 1" class="text-xs" style="color: var(--danger);">
                                    <i class="fas fa-trash mr-1"></i>Hapus
                                </button>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                                <div>
                                    <label class="form-label text-xs">Tahun</label>
                                    <input type="text" :name="'love_stories[' + idx + '][year]'" x-model="story.year" class="form-input text-sm">
                                </div>
                                <div class="sm:col-span-3">
                                    <label class="form-label text-xs">Judul</label>
                                    <input type="text" :name="'love_stories[' + idx + '][title]'" x-model="story.title" class="form-input text-sm">
                                </div>
                            </div>

                            <div>
                                <label class="form-label text-xs">Cerita</label>
                                <textarea :name="'love_stories[' + idx + '][description]'" x-model="story.description" class="form-input text-sm" rows="3"></textarea>
                            </div>

                            <input type="hidden" :name="'love_stories[' + idx + '][photo_path]'" x-model="story.photo_path">

                            <template x-if="story.photo_path">
                                <div class="flex items-center gap-3 rounded-lg px-3 py-2" style="background: rgba(255,255,255,0.5); border: 1px solid var(--border);">
                                    <img
                                        :src="'/storage/' + story.photo_path"
                                        alt="Foto story saat ini"
                                        class="rounded-md object-cover shrink-0"
                                        style="width: 72px; height: 72px; border: 1px solid var(--border);"
                                    >
                                    <div class="min-w-0">
                                        <p class="text-[11px] font-semibold uppercase tracking-wider" style="color: var(--accent);">Preview Foto</p>
                                        <span class="text-xs block" style="color: var(--text-secondary);">Foto story saat ini</span>
                                    </div>
                                </div>
                            </template>

                            <div>
                                <label class="form-label text-xs">Ganti / Upload Foto Story</label>
                                <input type="file" :name="'love_story_photos[' + idx + ']'" class="form-input text-xs" accept="image/*">
                            </div>
                        </div>
                    </template>

                    <div class="flex items-center justify-between gap-3 pt-1">
                        <button type="button" @click="stories.push({ year: '', title: '', description: '', photo_path: '' })" class="btn btn-secondary btn-sm text-xs">
                            <i class="fas fa-plus mr-1"></i>Tambah Story
                        </button>

                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-save mr-1"></i>Simpan Love Story
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- IG Story Template Upload --}}
        <div class="card overflow-hidden">
            <div class="px-6 py-4 border-b" style="border-color: var(--border);">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-base"><i class="fab fa-instagram mr-2" style="color: var(--accent);"></i>Template IG Story</h3>
                    @if($invitation->ig_story_photo)
                        <span class="badge badge-success">Uploaded</span>
                    @endif
                </div>
                <p class="text-xs mt-1" style="color: var(--text-secondary);">Upload gambar template IG Story untuk tamu download di halaman undangan.</p>
            </div>
            <div class="p-4">
                @if($invitation->ig_story_photo)
                <div class="flex items-start gap-4 mb-4">
                    <div class="relative group" style="width: 120px; border-radius: var(--radius-sm); overflow: hidden;">
                        <img src="{{ asset('storage/' . $invitation->ig_story_photo) }}" alt="IG Story Template" class="w-full h-auto object-cover rounded-lg shadow-sm">
                    </div>
                    <div class="flex-1">
                        <p class="text-xs mb-2" style="color: var(--text-secondary);">Template IG Story sudah diupload. Tamu dapat mendownload gambar ini dari halaman undangan.</p>
                        <form method="POST" action="{{ route('client.invitations.ig-story.destroy', $invitation) }}" onsubmit="return confirm('Hapus template IG Story ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm text-xs">
                                <i class="fas fa-trash mr-1"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
                @endif

                <form method="POST" action="{{ route('client.invitations.ig-story.upload', $invitation) }}" enctype="multipart/form-data" class="flex gap-2 items-end">
                    @csrf
                    <div class="flex-1">
                        <label class="form-label text-[10px] uppercase font-bold tracking-wider mb-1 block" style="color: var(--text-secondary);">{{ $invitation->ig_story_photo ? 'Ganti gambar IG Story' : 'Upload gambar IG Story' }}</label>
                        <input type="file" name="ig_story_photo" class="form-input text-xs" accept="image/*" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm h-[38px] flex items-center justify-center px-4" title="Upload IG Story"><i class="fas fa-upload mr-1"></i> Upload</button>
                </form>
                @error('ig_story_photo')
                    <p class="text-xs mt-2" style="color: var(--danger);">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- RSVP & Ucapan combined --}}
        <div class="card overflow-hidden">
            <div class="px-6 py-4 border-b" style="border-color: var(--border);">
                <h3 class="font-bold text-base">RSVP & Ucapan ({{ $invitation->rsvps->count() }})</h3>
            </div>
            <div class="p-4">
                @forelse($invitation->rsvps as $rsvp)
                <div class="flex flex-col gap-2 p-3 rounded-lg transition mb-1 border-b last:border-0"
                     style="border-color: var(--border);"
                     onmouseover="this.style.background='var(--hover-bg)'" onmouseout="this.style.background='transparent'">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-[10px] font-bold shrink-0"
                            style="{{ $rsvp->status === 'attending' ? 'background:rgba(52,199,89,0.12);color:var(--success)' : ($rsvp->status === 'maybe' ? 'background:rgba(255,149,0,0.12);color:var(--warning)' : 'background:rgba(255,59,48,0.12);color:var(--danger)') }}">
                            {{ $rsvp->status === 'attending' ? 'OK' : ($rsvp->status === 'maybe' ? '?' : 'X') }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-semibold truncate">{{ $rsvp->name }}</span>
                                @if($rsvp->phone || $rsvp->normalized_phone)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $rsvp->normalized_phone ?: $rsvp->phone) }}" 
                                   target="_blank" class="text-green-500 hover:text-green-600 transition-colors" title="Kirim WhatsApp">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                                @endif
                                <span class="text-[10px] ml-auto whitespace-nowrap" style="color: var(--text-tertiary);">{{ $rsvp->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-[11px]" style="color: var(--text-secondary);">{{ $rsvp->pax }} orang — {{ $rsvp->status === 'attending' ? 'Hadir' : ($rsvp->status === 'maybe' ? 'Ragu' : 'Tidak Hadir') }}</p>
                        </div>
                    </div>
                    @if($rsvp->message)
                    <div class="pl-11 pr-2 pb-1">
                        <div class="p-2.5 rounded-lg text-xs border bg-slate-50 dark:bg-slate-800/50 border-slate-100 dark:border-slate-700/50" style="color: var(--text-secondary);">
                            <i class="fas fa-quote-left text-[9px] opacity-20 mr-1"></i>
                            {{ $rsvp->message }}
                        </div>
                    </div>
                    @endif
                </div>
                @empty
                <p class="text-center text-sm py-6" style="color: var(--text-secondary);">Belum ada data</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="space-y-6">
        {{-- Actions --}}
        <div class="card p-6">
            <h3 class="font-bold text-base mb-4">Aksi</h3>
            <div class="space-y-3">
                <a href="{{ route('client.invitations.edit', $invitation) }}" class="btn btn-primary w-full text-center block text-sm py-3">
                    <i class="fas fa-edit mr-2"></i> Edit Undangan
                </a>
                @if($invitation->status === 'draft')
                <form method="POST" action="{{ route('client.invitations.submit', $invitation) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-secondary w-full text-sm py-3" style="color: var(--warning);">
                        <i class="fas fa-paper-plane mr-2"></i> Publikasikan Undangan
                    </button>
                </form>
                <div class="p-3 rounded-lg text-xs" style="background: rgba(245,158,11,0.08); color: var(--warning);">
                    <i class="fas fa-file-alt mr-1"></i> Undangan ini masih draft dan belum dipublikasikan ke publik.
                </div>
                @elseif($invitation->status === 'pending')
                <form method="POST" action="{{ route('client.invitations.submit', $invitation) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-secondary w-full text-sm py-3" style="color: var(--warning);">
                        <i class="fas fa-bolt mr-2"></i> Aktifkan Sekarang
                    </button>
                </form>
                <div class="p-3 rounded-lg text-xs" style="background: rgba(245,158,11,0.08); color: var(--warning);">
                    <i class="fas fa-clock mr-1"></i> Status pending adalah data lama. Publikasikan ulang untuk langsung aktif tanpa review admin.
                </div>
                @elseif($invitation->status === 'active')
                <div class="p-3 rounded-lg text-xs" style="background: rgba(52,199,89,0.08); color: var(--success);">
                    <i class="fas fa-check-circle mr-1"></i> Undangan sudah dipublikasikan dan aktif ke publik.
                </div>
                @endif
                @if($invitation->isActive())
                <a href="{{ $invitation->getPublicUrl() }}" target="_blank" class="btn btn-secondary w-full text-center block text-sm py-3">
                    <i class="fas fa-external-link-alt mr-2"></i> Lihat Undangan
                </a>
                @endif
                <a href="{{ route('client.invitations.guests.index', $invitation) }}" class="btn btn-secondary w-full text-center block text-sm py-3">
                    <i class="fas fa-users mr-2"></i> Kelola Tamu
                </a>
                <a href="{{ $invitation->google_calendar_url }}" target="_blank" class="btn btn-secondary w-full text-center block text-sm py-3">
                    <i class="fas fa-calendar-plus mr-2"></i> Google Calendar
                </a>
                <a href="{{ $invitation->maps_deep_link }}" target="_blank" class="btn btn-secondary w-full text-center block text-sm py-3">
                    <i class="fas fa-map-location-dot mr-2"></i> Maps Deep Link
                </a>
                @if($invitation->livestream_enabled && $invitation->livestream_url)
                <a href="{{ $invitation->livestream_url }}" target="_blank" class="btn btn-secondary w-full text-center block text-sm py-3">
                    <i class="fas fa-video mr-2"></i> Live Streaming
                </a>
                @endif

                {{-- Paket Akun --}}
                @if(!empty($activePackage))
                <div class="p-3 rounded-lg text-center text-xs" style="background: rgba(52,199,89,0.08); color: var(--success);">
                    <i class="fas fa-check-circle mr-1"></i> Paket Aktif: {{ $activePackage->name }}
                </div>
                @else
                <a href="{{ route('client.packages.select') }}" class="btn w-full text-center block text-sm py-3" style="background: linear-gradient(135deg, var(--accent), #5856d6); color: white;">
                    <i class="fas fa-credit-card mr-2"></i> Pilih Paket Dulu
                </a>
                @endif
            </div>
        </div>

        {{-- Package Info --}}
        <div class="card p-6">
            <h3 class="font-bold text-base mb-4">Paket - {{ $activePackage->name ?? ($invitation->package->name ?? '-') }}</h3>
            <div class="space-y-3">
                {{-- Guest Limit --}}
                <div>
                    <div class="flex items-center justify-between text-xs mb-1">
                        <span style="color: var(--text-secondary);"><i class="fas fa-users mr-1"></i> Tamu</span>
                        <span class="font-semibold">{{ $currentGuests }}/{{ $maxGuests }}</span>
                    </div>
                    @php $gP = $maxGuests > 0 ? min(100, round(($currentGuests / $maxGuests) * 100)) : 0; @endphp
                    <div style="background: var(--bg-tertiary); height: 4px; border-radius: 2px; overflow: hidden;">
                        <div style="width: {{ $gP }}%; height: 100%; border-radius: 2px; background: {{ $gP >= 90 ? 'var(--danger)' : 'var(--accent)' }};"></div>
                    </div>
                </div>
                {{-- Photo Limit --}}
                <div>
                    <div class="flex items-center justify-between text-xs mb-1">
                        <span style="color: var(--text-secondary);"><i class="fas fa-image mr-1"></i> Foto</span>
                        <span class="font-semibold">{{ $currentPhotos }}/{{ $maxPhotos }}</span>
                    </div>
                    @php $pP = $maxPhotos > 0 ? min(100, round(($currentPhotos / $maxPhotos) * 100)) : 0; @endphp
                    <div style="background: var(--bg-tertiary); height: 4px; border-radius: 2px; overflow: hidden;">
                        <div style="width: {{ $pP }}%; height: 100%; border-radius: 2px; background: {{ $pP >= 90 ? 'var(--danger)' : 'var(--accent)' }};"></div>
                    </div>
                </div>
                {{-- Invitation Limit --}}
                <div>
                    <div class="flex items-center justify-between text-xs mb-1">
                        <span style="color: var(--text-secondary);"><i class="fas fa-layer-group mr-1"></i> Undangan</span>
                        <span class="font-semibold">{{ $currentInvitations }}/{{ $maxInvitations }}</span>
                    </div>
                    @php $iP = $maxInvitations > 0 ? min(100, round(($currentInvitations / $maxInvitations) * 100)) : 0; @endphp
                    <div style="background: var(--bg-tertiary); height: 4px; border-radius: 2px; overflow: hidden;">
                        <div style="width: {{ $iP }}%; height: 100%; border-radius: 2px; background: {{ $iP >= 90 ? 'var(--danger)' : 'var(--accent)' }};"></div>
                    </div>
                </div>
            </div>
            {{-- Features --}}
            @if($invitation->package->features)
            <div class="mt-4 pt-4" style="border-top: 1px solid var(--border);">
                <p class="text-xs font-semibold mb-2" style="color: var(--text-secondary);">Fitur Paket</p>
                @foreach($invitation->package->features as $feature)
                <div class="flex items-center gap-2 text-xs mb-1">
                    <i class="fas fa-check text-xs" style="color: var(--success);"></i>
                    <span>{{ $feature }}</span>
                </div>
                @endforeach
            </div>
            @endif

            @if($nextPackage && !empty($upsellReasons))
            <div class="mt-4 pt-4 rounded-lg p-3" style="background: rgba(245,158,11,.09); border:1px solid rgba(245,158,11,.25);">
                <p class="text-xs font-semibold mb-2" style="color: #f59e0b;">
                    <i class="fas fa-rocket mr-1"></i> Rekomendasi Upgrade: {{ $nextPackage->name }}
                </p>
                @foreach($upsellReasons as $reason)
                    <p class="text-xs mb-1" style="color: var(--text-secondary);">- {{ $reason }}</p>
                @endforeach
                <form method="POST" action="{{ route('client.invitations.upgrade-suggested', $invitation) }}" class="mt-2">
                    @csrf
                    <button type="submit" class="btn btn-secondary w-full text-center block text-xs py-2" style="color:#f59e0b;border-color:rgba(245,158,11,.35);">
                        Upgrade Paket 1 Klik
                    </button>
                </form>
            </div>
            @endif
        </div>

        {{-- Stats --}}
        <div class="card p-6">
            <div class="flex items-start justify-between gap-3 mb-4">
                <div>
                    <h3 class="font-bold text-base">Statistik</h3>
                    <p id="analytics-status" class="text-[11px] mt-1" style="color: var(--text-secondary);">
                        {{ $invitation->isActive() ? 'Auto refresh setiap 60 detik saat tab aktif.' : 'Refresh manual tersedia. Auto refresh dimatikan untuk undangan nonaktif.' }}
                    </p>
                </div>
                <button
                    type="button"
                    id="refresh-analytics-btn"
                    class="btn btn-secondary btn-sm text-xs"
                    style="white-space: nowrap;"
                >
                    <i class="fas fa-rotate-right mr-1"></i> Refresh
                </button>
            </div>
            <div class="space-y-3">
                <div class="flex items-center justify-between text-sm">
                    <span style="color: var(--text-secondary);"><i class="fas fa-eye mr-2 w-4"></i>Kunjungan</span>
                    <span class="font-bold">{{ number_format($invitation->view_count) }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span style="color: var(--success);"><i class="fas fa-check-circle mr-2 w-4"></i>Hadir</span>
                    <span id="an-attending" class="font-bold" style="color: var(--success);">{{ $invitation->rsvps->where('status', 'attending')->count() }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span style="color: var(--warning);"><i class="fas fa-question-circle mr-2 w-4"></i>Maybe</span>
                    <span id="an-maybe" class="font-bold" style="color: var(--warning);">{{ $invitation->rsvps->where('status', 'maybe')->count() }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span style="color: var(--danger);"><i class="fas fa-times-circle mr-2 w-4"></i>Tidak</span>
                    <span id="an-not-attending" class="font-bold" style="color: var(--danger);">{{ $invitation->rsvps->where('status', 'not_attending')->count() }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span style="color: var(--text-secondary);"><i class="fas fa-user-group mr-2 w-4"></i>Total Pax Hadir</span>
                    <span id="an-pax" class="font-bold">{{ $invitation->rsvps->where('status', 'attending')->sum('pax') }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span style="color: var(--text-secondary);"><i class="fas fa-qrcode mr-2 w-4"></i>Check-in</span>
                    <span id="an-checkin" class="font-bold">{{ $invitation->guests->whereNotNull('checked_in_at')->count() }}/{{ $invitation->guests->count() }}</span>
                </div>
            </div>
            <div class="mt-4 pt-3" style="border-top:1px solid var(--border);">
                <p class="text-xs font-semibold mb-2" style="color: var(--text-secondary);">Kategori RSVP (Live)</p>
                <div id="an-categories" class="space-y-1 text-xs" style="color: var(--text-secondary);"></div>
            </div>
            <div class="mt-4 pt-3" style="border-top:1px solid var(--border);">
                <p class="text-xs font-semibold mb-2" style="color: var(--text-secondary);">Funnel Undangan</p>
                <div class="space-y-1 text-xs" style="color: var(--text-secondary);">
                    <div>Terkirim: <strong id="fn-sent">0</strong></div>
                    <div>Dibuka: <strong id="fn-opened">0</strong> (<span id="fn-open-rate">0</span>%)</div>
                    <div>Klik Maps: <strong id="fn-map">0</strong> (<span id="fn-map-rate">0</span>%)</div>
                    <div>RSVP: <strong id="fn-rsvp">0</strong> (<span id="fn-rsvp-rate">0</span>%)</div>
                    <div>Check-in: <strong id="fn-checkin">0</strong> (<span id="fn-checkin-rate">0</span>%)</div>
                </div>
            </div>
        </div>

        {{-- WhatsApp Blast Reminder --}}
        <div class="card p-6">
            <h3 class="font-bold text-base mb-4">WhatsApp Reminder</h3>
            <form method="POST" action="{{ route('client.invitations.reminders.store', $invitation) }}" class="space-y-3">
                @csrf
                <div>
                    <label class="form-label">Audience</label>
                    <select name="audience" class="form-input">
                        <option value="all_guests">Semua Tamu Ber-no HP</option>
                        <option value="no_rsvp">Belum RSVP</option>
                        <option value="not_checked_in">Belum Check-in</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Jadwal Kirim</label>
                    <input type="datetime-local" name="scheduled_at" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Template Pesan</label>
                    <textarea name="message_template" class="form-input" rows="4" required>Halo {name}, ini pengingat untuk acara {event} pada {date} {time} di {venue}. Detail undangan: {link}</textarea>
                </div>
                <button class="btn btn-primary w-full text-sm">
                    <i class="fab fa-whatsapp mr-2"></i> Jadwalkan Blast
                </button>
            </form>
            @if($invitation->reminderCampaigns->count())
            <div class="mt-4 pt-4" style="border-top:1px solid var(--border);">
                <p class="text-xs font-semibold mb-2" style="color: var(--text-secondary);">Histori Campaign</p>
                <div class="space-y-2">
                    @foreach($invitation->reminderCampaigns->take(5) as $campaign)
                        <div class="p-2 rounded-lg text-xs" style="background: var(--bg-tertiary);">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold">{{ strtoupper($campaign->channel) }} - {{ $campaign->audience }} @if($campaign->source === 'auto') <span class="badge badge-default">AUTO</span> @endif</span>
                                <span class="badge badge-{{ $campaign->status === 'sent' ? 'success' : ($campaign->status === 'failed' ? 'danger' : ($campaign->status === 'cancelled' ? 'default' : 'warning')) }}">{{ $campaign->status }}</span>
                            </div>
                            <div style="color: var(--text-secondary);">{{ $campaign->scheduled_at?->format('d M Y H:i') }} | Sent {{ $campaign->sent_count }} / Failed {{ $campaign->failed_count }}</div>
                            @if($campaign->status === 'scheduled')
                            <form method="POST" action="{{ route('client.invitations.reminders.cancel', [$invitation, $campaign]) }}" class="mt-2">
                                @csrf @method('PATCH')
                                <button class="btn btn-secondary btn-sm w-full">Batalkan Campaign</button>
                            </form>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Collaborators --}}
        <div class="card p-6">
            <h3 class="font-bold text-base mb-4">Kolaborator Editor</h3>
            @if((int) auth()->id() === (int) $invitation->user_id)
            <form method="POST" action="{{ route('client.invitations.collaborators.store', $invitation) }}" class="space-y-2 mb-3">
                @csrf
                <input type="email" name="email" class="form-input" placeholder="email client editor" required>
                <button class="btn btn-primary w-full text-sm">Undang Editor</button>
            </form>
            @endif
            <div class="space-y-2">
                @forelse($invitation->collaborators as $collab)
                <div class="p-3 rounded-lg text-xs" style="background: var(--bg-tertiary);">
                    <div class="flex items-center justify-between">
                        <span class="font-semibold">{{ $collab->user->name ?? 'User' }} ({{ $collab->user->email ?? '-' }})</span>
                        <span class="badge badge-{{ $collab->status === 'accepted' ? 'success' : 'warning' }}">{{ $collab->status }}</span>
                    </div>
                    @if((int) auth()->id() === (int) $invitation->user_id)
                    <form method="POST" action="{{ route('client.invitations.collaborators.destroy', [$invitation, $collab]) }}" class="mt-2">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm w-full">Hapus</button>
                    </form>
                    @elseif((int) auth()->id() === (int) $collab->user_id && $collab->status !== 'accepted')
                    <form method="POST" action="{{ route('client.collaborators.accept', $collab) }}" class="mt-2">
                        @csrf @method('PATCH')
                        <button class="btn btn-secondary btn-sm w-full">Terima Kolaborasi</button>
                    </form>
                    @endif
                </div>
                @empty
                <p class="text-xs" style="color: var(--text-secondary);">Belum ada kolaborator.</p>
                @endforelse
            </div>
        </div>

        {{-- Backup --}}
        <div class="card p-6">
            <h3 class="font-bold text-base mb-4">Backup & Restore</h3>
            <form method="POST" action="{{ route('client.invitations.backups.store', $invitation) }}" class="space-y-2 mb-3">
                @csrf
                <input type="text" name="label" class="form-input" placeholder="Label backup (opsional)">
                <button class="btn btn-primary w-full text-sm">Buat Backup</button>
            </form>
            <div class="space-y-2">
                @forelse($invitation->backups->take(8) as $backup)
                <div class="p-3 rounded-lg text-xs" style="background: var(--bg-tertiary);">
                    <div class="font-semibold">{{ $backup->label ?: 'Backup' }}</div>
                    <div style="color: var(--text-secondary);">{{ $backup->created_at?->format('d M Y H:i') }}</div>
                    <form method="POST" action="{{ route('client.invitations.backups.restore', [$invitation, $backup]) }}" class="mt-2">
                        @csrf
                        <button class="btn btn-secondary btn-sm w-full">Restore ke Draft Baru</button>
                    </form>
                </div>
                @empty
                <p class="text-xs" style="color: var(--text-secondary);">Belum ada backup.</p>
                @endforelse
            </div>
        </div>

        {{-- Vendor CRM --}}
        <div class="card p-6">
            <h3 class="font-bold text-base mb-4">CRM Vendor (WO/Fotografer)</h3>
            <form method="POST" action="{{ route('client.invitations.vendors.store', $invitation) }}" class="space-y-3 mb-4">
                @csrf
                <div class="grid grid-cols-2 gap-2">
                    <select name="category" class="form-input" required>
                        <option value="wo">Wedding Organizer</option>
                        <option value="photographer">Fotografer</option>
                        <option value="makeup">Makeup</option>
                        <option value="entertainment">Entertainment</option>
                        <option value="other">Lainnya</option>
                    </select>
                    <select name="status" class="form-input" required>
                        <option value="new">New</option>
                        <option value="contacted">Contacted</option>
                        <option value="negotiation">Negotiation</option>
                        <option value="deal">Deal</option>
                        <option value="lost">Lost</option>
                    </select>
                </div>
                <input type="text" name="vendor_name" class="form-input" placeholder="Nama Vendor" required>
                <div class="grid grid-cols-2 gap-2">
                    <input type="text" name="contact_name" class="form-input" placeholder="PIC">
                    <input type="text" name="phone" class="form-input" placeholder="No HP">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <input type="text" name="instagram" class="form-input" placeholder="Instagram">
                    <input type="number" name="offered_price" class="form-input" placeholder="Penawaran Harga">
                </div>
                <input type="date" name="follow_up_date" class="form-input">
                <textarea name="notes" class="form-input" rows="2" placeholder="Catatan"></textarea>
                <button class="btn btn-primary w-full text-sm"><i class="fas fa-plus mr-2"></i>Tambah Vendor</button>
            </form>

            <div class="space-y-2">
                @forelse($invitation->vendorLeads->take(8) as $vendor)
                    <div class="p-3 rounded-lg" style="background: var(--bg-tertiary);">
                        <div class="flex items-center justify-between mb-1">
                            <p class="text-sm font-semibold">{{ $vendor->vendor_name }}</p>
                            <span class="badge badge-{{ $vendor->status === 'deal' ? 'success' : ($vendor->status === 'lost' ? 'danger' : 'warning') }}">{{ $vendor->status }}</span>
                        </div>
                        <p class="text-xs" style="color: var(--text-secondary);">{{ strtoupper($vendor->category) }} | {{ $vendor->phone ?: '-' }} | {{ $vendor->instagram ?: '-' }}</p>
                        @if($vendor->offered_price)
                            <p class="text-xs mt-1">Harga: <strong>Rp{{ number_format($vendor->offered_price, 0, ',', '.') }}</strong></p>
                        @endif
                        <div class="mt-2 flex gap-2">
                            <form method="POST" action="{{ route('client.invitations.vendors.update', [$invitation, $vendor]) }}" class="flex gap-2 flex-1">
                                @csrf @method('PATCH')
                                <select name="status" class="form-input text-xs" style="padding:6px 8px;">
                                    @foreach(['new','contacted','negotiation','deal','lost'] as $st)
                                        <option value="{{ $st }}" {{ $vendor->status === $st ? 'selected' : '' }}>{{ $st }}</option>
                                    @endforeach
                                </select>
                                <button class="btn btn-secondary btn-sm">Update</button>
                            </form>
                            <form method="POST" action="{{ route('client.invitations.vendors.destroy', [$invitation, $vendor]) }}" onsubmit="return confirm('Hapus vendor ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-xs" style="color: var(--text-secondary);">Belum ada data vendor.</p>
                @endforelse
            </div>
        </div>

        {{-- Share Link --}}
        @if($invitation->isActive())
        <div class="card p-6">
            <h3 class="font-bold text-base mb-3">Share Link</h3>
            <div class="p-3 rounded-lg text-xs break-all mb-3" style="background: var(--bg-tertiary); color: var(--accent);" id="invite-url">
                {{ $invitation->getPublicUrl() }}
            </div>
            <button onclick="navigator.clipboard.writeText(document.getElementById('invite-url').textContent.trim()); this.textContent='Tersalin!'; setTimeout(() => this.textContent='Copy Link', 2000);"
                    class="btn btn-secondary w-full text-sm">
                Copy Link
            </button>
        </div>
        @endif
    </div>
</div>

<div class="mt-4">
    <a href="{{ route('client.invitations.index') }}" class="text-sm font-semibold" style="color: var(--accent);">
        <i class="fas fa-arrow-left mr-1"></i> Kembali ke daftar
    </a>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const url = "{{ route('client.invitations.analytics', $invitation) }}";
        const catEl = document.getElementById('an-categories');
        const refreshButton = document.getElementById('refresh-analytics-btn');
        const statusEl = document.getElementById('analytics-status');
        const isInvitationActive = @json($invitation->isActive());
        const pollingIntervalMs = 60000;
        let pollingId = null;
        let isRefreshing = false;

        function setStatus(message) {
            if (statusEl) {
                statusEl.textContent = message;
            }
        }

        async function refreshAnalytics(source = 'auto') {
            if (isRefreshing) {
                return;
            }

            isRefreshing = true;
            if (refreshButton) {
                refreshButton.disabled = true;
            }
            setStatus(source === 'manual' ? 'Memuat statistik terbaru...' : 'Menyegarkan statistik...');

            try {
                const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                if (!res.ok) {
                    setStatus('Gagal memuat statistik. Coba refresh manual.');
                    return;
                }
                const data = await res.json();
                document.getElementById('an-attending').textContent = data.attending ?? 0;
                document.getElementById('an-maybe').textContent = data.maybe ?? 0;
                document.getElementById('an-not-attending').textContent = data.not_attending ?? 0;
                document.getElementById('an-pax').textContent = data.attending_pax ?? 0;
                document.getElementById('an-checkin').textContent = `${data.checked_in ?? 0}/${data.total_guests ?? 0}`;

                if (catEl) {
                    const rows = Array.isArray(data.categories) ? data.categories : [];
                    catEl.innerHTML = rows.length
                        ? rows.map((row) => `<div>${row.category}: <strong>${row.total}</strong></div>`).join('')
                        : '<div>Belum ada data kategori.</div>';
                }

                const funnel = data.funnel || {};
                const conv = funnel.conversion || {};
                const byId = (id, value) => {
                    const el = document.getElementById(id);
                    if (el) el.textContent = value ?? 0;
                };
                byId('fn-sent', funnel.sent ?? 0);
                byId('fn-opened', funnel.opened ?? 0);
                byId('fn-map', funnel.map_clicked ?? 0);
                byId('fn-rsvp', funnel.rsvp_submitted ?? 0);
                byId('fn-checkin', funnel.checked_in ?? 0);
                byId('fn-open-rate', conv.open_rate ?? 0);
                byId('fn-map-rate', conv.map_rate ?? 0);
                byId('fn-rsvp-rate', conv.rsvp_rate ?? 0);
                byId('fn-checkin-rate', conv.checkin_rate ?? 0);

                const generatedAt = data.generated_at ? new Date(data.generated_at.replace(' ', 'T')) : null;
                if (generatedAt && !Number.isNaN(generatedAt.getTime())) {
                    setStatus(`Terakhir diperbarui ${generatedAt.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' })}.`);
                } else {
                    setStatus('Statistik berhasil diperbarui.');
                }
            } catch (e) {
                setStatus('Gagal memuat statistik. Coba refresh manual.');
            } finally {
                isRefreshing = false;
                if (refreshButton) {
                    refreshButton.disabled = false;
                }
            }
        }

        function stopPolling() {
            if (pollingId) {
                clearInterval(pollingId);
                pollingId = null;
            }
        }

        function startPolling() {
            if (!isInvitationActive || document.hidden || pollingId) {
                return;
            }

            pollingId = setInterval(() => {
                if (!document.hidden) {
                    refreshAnalytics();
                }
            }, pollingIntervalMs);
        }

        if (refreshButton) {
            refreshButton.addEventListener('click', () => refreshAnalytics('manual'));
        }

        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                stopPolling();
                if (isInvitationActive) {
                    setStatus('Auto refresh dijeda saat tab tidak aktif.');
                }
                return;
            }

            if (isInvitationActive) {
                refreshAnalytics();
                startPolling();
            }
        });

        refreshAnalytics('initial');
        startPolling();
    })();
</script>
@endpush
