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

    /* ===================== QUICK EDIT PANEL ===================== */
    .qe-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
    }

    .qe-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 7px;
        padding: 14px 8px 12px;
        border-radius: 12px;
        border: 1px solid var(--border);
        background: var(--bg-tertiary);
        cursor: pointer;
        text-align: center;
        transition: all 0.22s cubic-bezier(0.34, 1.56, 0.64, 1);
        text-decoration: none;
        color: var(--text);
        font-size: 0;
        outline: none;
    }

    .qe-btn:hover, .qe-btn:focus {
        transform: translateY(-3px) scale(1.03);
        border-color: var(--accent);
        background: var(--accent-bg);
        box-shadow: 0 6px 18px rgba(0,0,0,0.12);
    }

    .qe-btn:active {
        transform: translateY(0) scale(0.98);
    }

    .qe-btn .qe-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        transition: all 0.22s ease;
        flex-shrink: 0;
    }

    .qe-btn:hover .qe-icon {
        transform: scale(1.15) rotate(-4deg);
    }

    .qe-btn .qe-label {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.02em;
        color: var(--text-secondary);
        line-height: 1.3;
        transition: color 0.22s ease;
    }

    .qe-btn:hover .qe-label {
        color: var(--accent);
    }

    /* ===================== MODAL ===================== */
    .qe-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.55);
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
        z-index: 9000;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 12px;
        animation: qeFadeIn 0.2s ease;
    }

    .qe-modal-overlay.hidden {
        display: none;
    }

    @keyframes qeFadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .qe-modal-card {
        background: var(--bg-secondary);
        border-radius: 18px;
        border: 1px solid var(--border);
        width: 100%;
        max-width: 600px;
        max-height: 92vh;
        overflow-y: auto;
        box-shadow: 0 32px 80px rgba(0,0,0,0.3), 0 0 0 1px rgba(255,255,255,0.05);
        animation: qeSlideUp 0.28s cubic-bezier(0.34, 1.56, 0.64, 1);
        scrollbar-width: thin;
    }

    @keyframes qeSlideUp {
        from { transform: translateY(40px) scale(0.97); opacity: 0; }
        to   { transform: translateY(0)    scale(1);    opacity: 1; }
    }

    .qe-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 20px 16px;
        border-bottom: 1px solid var(--border);
        position: sticky;
        top: 0;
        background: var(--bg-secondary);
        z-index: 1;
        border-radius: 18px 18px 0 0;
    }

    .qe-modal-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 700;
        font-size: 15px;
    }

    .qe-modal-title .modal-icon {
        width: 34px;
        height: 34px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        flex-shrink: 0;
    }

    .qe-close-btn {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        border: 1px solid var(--border);
        background: var(--bg-tertiary);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 15px;
        color: var(--text-secondary);
        transition: all 0.18s ease;
        flex-shrink: 0;
    }

    .qe-close-btn:hover {
        background: var(--danger);
        border-color: var(--danger);
        color: white;
        transform: rotate(90deg);
    }

    .qe-modal-body {
        padding: 20px;
    }

    .qe-modal-footer {
        padding: 14px 20px 18px;
        border-top: 1px solid var(--border);
        display: flex;
        gap: 8px;
        justify-content: flex-end;
        position: sticky;
        bottom: 0;
        background: var(--bg-secondary);
        border-radius: 0 0 18px 18px;
    }

    /* Mobile: Quick Edit Panel di atas */
    .qe-mobile-panel {
        margin-bottom: 16px;
    }

    @media (min-width: 1024px) {
        .qe-mobile-panel { display: none; }
    }

    .qe-desktop-panel {
        display: none;
    }

    @media (min-width: 1024px) {
        .qe-desktop-panel { display: block; }
    }
</style>

{{-- ===== QUICK EDIT PANEL: MOBILE (tampil di atas pada mobile) ===== --}}
<div class="qe-mobile-panel">
    @include('client.invitations.partials.quick-edit-panel')
</div>

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
                                @php
                                    $rsvpWhatsapp = $rsvp->guest?->phone ?: ($rsvp->normalized_phone ?: $rsvp->phone);
                                @endphp
                                @if($rsvpWhatsapp)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $rsvpWhatsapp) }}" 
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
        {{-- ===== QUICK EDIT PANEL: DESKTOP (di sidebar, paling atas) ===== --}}
        <div class="qe-desktop-panel">
            @include('client.invitations.partials.quick-edit-panel')
        </div>
        {{-- Actions --}}
        <div class="card p-5">
            <div class="flex items-center gap-2 mb-4">
                <div style="width:32px;height:32px;border-radius:9px;background:var(--accent-bg);display:flex;align-items:center;justify-content:center;">
                    <i class="fas fa-bolt" style="color:var(--accent);font-size:14px;"></i>
                </div>
                <h3 class="font-bold text-sm">Aksi</h3>
            </div>

            {{-- Status badge --}}
            @if($invitation->status === 'draft')
            <div class="flex items-center gap-2 px-3 py-2 rounded-xl mb-3 text-xs font-medium" style="background:rgba(245,158,11,0.09);color:var(--warning);border:1px solid rgba(245,158,11,0.2);">
                <i class="fas fa-file-alt"></i> Draft — belum dipublikasikan
            </div>
            @elseif($invitation->status === 'pending')
            <div class="flex items-center gap-2 px-3 py-2 rounded-xl mb-3 text-xs font-medium" style="background:rgba(245,158,11,0.09);color:var(--warning);border:1px solid rgba(245,158,11,0.2);">
                <i class="fas fa-clock"></i> Pending — publikasikan ulang untuk aktif
            </div>
            @elseif($invitation->status === 'active')
            <div class="flex items-center gap-2 px-3 py-2 rounded-xl mb-3 text-xs font-medium" style="background:rgba(52,199,89,0.09);color:var(--success);border:1px solid rgba(52,199,89,0.2);">
                <i class="fas fa-check-circle"></i> Aktif — undangan sudah dipublikasikan
            </div>
            @endif

            <div class="qe-grid">

                {{-- Toggle Status --}}
                <form method="POST" action="{{ route('client.invitations.toggle-status', $invitation) }}" class="contents">
                    @csrf @method('PATCH')
                    @if($invitation->status === 'active' && $invitation->isActive())
                    <button type="submit" class="qe-btn" style="border-color:rgba(255,59,48,0.3);">
                        <span class="qe-icon" style="background:rgba(255,59,48,0.12);">
                            <i class="fas fa-toggle-off" style="color:#ff3b30;"></i>
                        </span>
                        <span class="qe-label">Nonaktifkan</span>
                    </button>
                    @else
                    <button type="submit" class="qe-btn">
                        <span class="qe-icon" style="background:rgba(52,199,89,0.12);">
                            <i class="fas fa-paper-plane" style="color:#34c759;"></i>
                        </span>
                        <span class="qe-label">{{ $invitation->status === 'pending' ? 'Aktifkan Sekarang' : 'Aktifkan' }}</span>
                    </button>
                    @endif
                </form>

                {{-- Lihat Undangan --}}
                @if($invitation->isActive())
                <a href="{{ $invitation->getPublicUrl() }}" target="_blank" class="qe-btn">
                    <span class="qe-icon" style="background:rgba(59,130,246,0.12);">
                        <i class="fas fa-external-link-alt" style="color:#3b82f6;"></i>
                    </span>
                    <span class="qe-label">Lihat Undangan</span>
                </a>
                @else
                <button type="button" disabled class="qe-btn" style="opacity:0.4;cursor:not-allowed;">
                    <span class="qe-icon" style="background:rgba(148,163,184,0.12);">
                        <i class="fas fa-eye-slash" style="color:#94a3b8;"></i>
                    </span>
                    <span class="qe-label">Belum Aktif</span>
                </button>
                @endif

                {{-- Kelola Tamu --}}
                <a href="{{ route('client.invitations.guests.index', $invitation) }}" class="qe-btn">
                    <span class="qe-icon" style="background:rgba(99,102,241,0.12);">
                        <i class="fas fa-users" style="color:#6366f1;"></i>
                    </span>
                    <span class="qe-label">Kelola Tamu</span>
                </a>

                {{-- Google Calendar --}}
                <a href="{{ $invitation->google_calendar_url }}" target="_blank" class="qe-btn">
                    <span class="qe-icon" style="background:rgba(234,179,8,0.12);">
                        <i class="fas fa-calendar-plus" style="color:#eab308;"></i>
                    </span>
                    <span class="qe-label">Google Calendar</span>
                </a>

                {{-- Maps --}}
                <a href="{{ $invitation->maps_deep_link }}" target="_blank" class="qe-btn">
                    <span class="qe-icon" style="background:rgba(16,185,129,0.12);">
                        <i class="fas fa-map-location-dot" style="color:#10b981;"></i>
                    </span>
                    <span class="qe-label">Maps</span>
                </a>

                {{-- Live Streaming --}}
                @if($invitation->livestream_enabled && $invitation->livestream_url)
                <a href="{{ $invitation->livestream_url }}" target="_blank" class="qe-btn">
                    <span class="qe-icon" style="background:rgba(239,68,68,0.12);">
                        <i class="fas fa-video" style="color:#ef4444;"></i>
                    </span>
                    <span class="qe-label">Live Streaming</span>
                </a>
                @endif

                {{-- Paket / Upgrade --}}
                @if(!empty($activePackage))
                <div class="qe-btn" style="cursor:default; border-color:rgba(52,199,89,0.3);">
                    <span class="qe-icon" style="background:rgba(52,199,89,0.12);">
                        <i class="fas fa-box-open" style="color:#34c759;"></i>
                    </span>
                    <span class="qe-label">{{ $activePackage->name }}</span>
                </div>
                @else
                <a href="{{ route('client.packages.select') }}" class="qe-btn" style="border-color:rgba(var(--accent-rgb),.35);">
                    <span class="qe-icon" style="background:var(--accent-bg);">
                        <i class="fas fa-credit-card" style="color:var(--accent);"></i>
                    </span>
                    <span class="qe-label">Pilih Paket</span>
                </a>
                @endif

            </div>

            {{-- Share Link --}}
            @if($invitation->isActive())
            <div class="mt-4 pt-4" style="border-top:1px solid var(--border);">
                <p class="text-xs font-semibold mb-2" style="color:var(--text-secondary);">Link Undangan</p>
                <div class="flex items-center gap-2">
                    <div class="flex-1 p-2.5 rounded-lg text-xs break-all truncate" style="background:var(--bg-tertiary);color:var(--accent);" id="invite-url-new">{{ $invitation->getPublicUrl() }}</div>
                    <button onclick="navigator.clipboard.writeText(document.getElementById('invite-url-new').textContent.trim()); this.innerHTML='<i class=\'fas fa-check\'></i>'; setTimeout(()=>this.innerHTML='<i class=\'fas fa-copy\'></i>',2000);"
                            class="qe-close-btn shrink-0" title="Copy Link" style="width:34px;height:34px;">
                        <i class="fas fa-copy" style="font-size:13px;"></i>
                    </button>
                </div>
            </div>
            @endif
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

    </div>
</div>

<div class="mt-4">
    <a href="{{ route('client.invitations.index') }}" class="text-sm font-semibold" style="color: var(--accent);">
        <i class="fas fa-arrow-left mr-1"></i> Kembali ke daftar
    </a>
</div>
{{-- ====================================================================== --}}
{{-- ========================== 10 QUICK EDIT MODALS ======================= --}}
{{-- ====================================================================== --}}

{{-- MODAL 1: Template --}}
<div id="qe-modal-template" class="qe-modal-overlay hidden" onclick="qeCloseOnOverlay(event, this)">
    <div class="qe-modal-card">
        <div class="qe-modal-header">
            <div class="qe-modal-title">
                <span class="modal-icon" style="background: rgba(139,92,246,0.12); color: #8b5cf6;"><i class="fas fa-palette"></i></span>
                <span>Pilih Template</span>
            </div>
            <button class="qe-close-btn" onclick="qeClose('qe-modal-template')"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" action="{{ route('client.invitations.update', $invitation) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            {{-- Kirim semua required field agar validasi tidak gagal --}}
            <input type="hidden" name="title" value="{{ $invitation->title }}">
            <input type="hidden" name="event_type" value="{{ $invitation->event_type }}">
            <input type="hidden" name="event_date" value="{{ optional($invitation->event_date)->format('Y-m-d') }}">
            <input type="hidden" name="event_time" value="{{ $invitation->event_time ? \Carbon\Carbon::parse($invitation->event_time)->format('H:i') : '' }}">
            <input type="hidden" name="venue_name" value="{{ $invitation->venue_name }}">
            <input type="hidden" name="venue_address" value="{{ $invitation->venue_address }}">
            <div class="qe-modal-body">
                <div class="mb-4">
                    <label class="form-label">Template Undangan</label>
                    <select name="template_id" class="form-input" id="qeTemplateSelect" onchange="qeUpdateTemplatePreview()" required>
                        @foreach ($templates as $t)
                            <option value="{{ $t->id }}"
                                data-thumbnail="{{ $t->thumbnail ? asset('storage/' . $t->thumbnail) : '' }}"
                                {{ $invitation->template_id == $t->id ? 'selected' : '' }}>
                                {{ $t->name }} {{ $t->is_premium ? '⭐' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div id="qeTemplatePreviewWrap" class="p-3 rounded-xl" style="display:none; background: var(--bg-tertiary);">
                    <p class="text-xs mb-2" style="color: var(--text-secondary);">Preview Template</p>
                    <img id="qeTemplatePreviewImg" src="" alt="Preview" style="width:100%; max-height:200px; object-fit:cover; border-radius:10px; border:1px solid var(--border);">
                </div>
            </div>
            <div class="qe-modal-footer">
                <button type="button" onclick="qeClose('qe-modal-template')" class="btn btn-secondary btn-sm">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-check mr-1"></i>Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL 2: Cover & Pembuka --}}
<div id="qe-modal-cover" class="qe-modal-overlay hidden" onclick="qeCloseOnOverlay(event, this)">
    <div class="qe-modal-card">
        <div class="qe-modal-header">
            <div class="qe-modal-title">
                <span class="modal-icon" style="background: rgba(59,130,246,0.12); color: #3b82f6;"><i class="fas fa-image"></i></span>
                <span>Cover & Teks Pembuka</span>
            </div>
            <button class="qe-close-btn" onclick="qeClose('qe-modal-cover')"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" action="{{ route('client.invitations.update', $invitation) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <input type="hidden" name="template_id" value="{{ $invitation->template_id }}">
            <input type="hidden" name="event_date" value="{{ optional($invitation->event_date)->format('Y-m-d') }}">
            <input type="hidden" name="event_time" value="{{ $invitation->event_time ? \Carbon\Carbon::parse($invitation->event_time)->format('H:i') : '' }}">
            <input type="hidden" name="venue_name" value="{{ $invitation->venue_name }}">
            <input type="hidden" name="venue_address" value="{{ $invitation->venue_address }}">
            <div class="qe-modal-body space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Judul Acara</label>
                        <input type="text" name="title" value="{{ $invitation->title }}" class="form-input" required>
                    </div>
                    <div>
                        <label class="form-label">Jenis Acara</label>
                        <select name="event_type" class="form-input" required>
                            @foreach (['wedding', 'birthday', 'graduation', 'corporate', 'other'] as $type)
                                <option value="{{ $type }}" {{ $invitation->event_type === $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="form-label">Cover Photo</label>
                    <input type="file" name="cover_photo" class="form-input" accept="image/*">
                    @if ($invitation->cover_photo)
                        <div class="mt-2 flex items-center gap-3 p-2 rounded-lg" style="background: var(--bg-tertiary);">
                            <img src="{{ asset('storage/' . $invitation->cover_photo) }}" class="w-14 h-14 object-cover rounded-lg border" style="border-color: var(--border);" alt="Cover">
                            <span class="text-xs" style="color:var(--text-secondary);">Cover foto saat ini</span>
                        </div>
                    @endif
                </div>
                <div>
                    <label class="form-label">Teks Pembuka</label>
                    <textarea name="opening_text" class="form-input" rows="4">{{ $invitation->opening_text }}</textarea>
                </div>
            </div>
            <div class="qe-modal-footer">
                <button type="button" onclick="qeClose('qe-modal-cover')" class="btn btn-secondary btn-sm">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-check mr-1"></i>Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL 3: Data Mempelai --}}
<div id="qe-modal-mempelai" class="qe-modal-overlay hidden" onclick="qeCloseOnOverlay(event, this)">
    <div class="qe-modal-card">
        <div class="qe-modal-header">
            <div class="qe-modal-title">
                <span class="modal-icon" style="background: rgba(236,72,153,0.12); color: #ec4899;"><i class="fas fa-heart"></i></span>
                <span>Data Mempelai</span>
            </div>
            <button class="qe-close-btn" onclick="qeClose('qe-modal-mempelai')"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" action="{{ route('client.invitations.update', $invitation) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <input type="hidden" name="template_id" value="{{ $invitation->template_id }}">
            <input type="hidden" name="title" value="{{ $invitation->title }}">
            <input type="hidden" name="event_type" value="{{ $invitation->event_type }}">
            <input type="hidden" name="event_date" value="{{ optional($invitation->event_date)->format('Y-m-d') }}">
            <input type="hidden" name="event_time" value="{{ $invitation->event_time ? \Carbon\Carbon::parse($invitation->event_time)->format('H:i') : '' }}">
            <input type="hidden" name="venue_name" value="{{ $invitation->venue_name }}">
            <input type="hidden" name="venue_address" value="{{ $invitation->venue_address }}">
            <div class="qe-modal-body space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="form-label text-xs">Mempelai Pria</label>
                        <input type="text" name="groom_name" value="{{ $invitation->groom_name }}" class="form-input">
                    </div>
                    <div>
                        <label class="form-label text-xs">Mempelai Wanita</label>
                        <input type="text" name="bride_name" value="{{ $invitation->bride_name }}" class="form-input">
                    </div>
                    <div>
                        <label class="form-label text-xs">Host</label>
                        <input type="text" name="host_name" value="{{ $invitation->host_name }}" class="form-input">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="form-label text-xs">Orang Tua Pria</label>
                        <input type="text" name="groom_parent_name" value="{{ $invitation->groom_parent_name }}" class="form-input">
                    </div>
                    <div>
                        <label class="form-label text-xs">Orang Tua Wanita</label>
                        <input type="text" name="bride_parent_name" value="{{ $invitation->bride_parent_name }}" class="form-input">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="form-label text-xs">Foto Mempelai Pria</label>
                        <input type="file" name="groom_photo" class="form-input" accept="image/*">
                        @if($invitation->groom_photo)
                            <img src="{{ asset('storage/' . $invitation->groom_photo) }}" class="w-12 h-12 object-cover rounded-lg mt-2" alt="Groom">
                        @endif
                    </div>
                    <div>
                        <label class="form-label text-xs">Foto Mempelai Wanita</label>
                        <input type="file" name="bride_photo" class="form-input" accept="image/*">
                        @if($invitation->bride_photo)
                            <img src="{{ asset('storage/' . $invitation->bride_photo) }}" class="w-12 h-12 object-cover rounded-lg mt-2" alt="Bride">
                        @endif
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="form-label text-xs">Instagram Pria</label>
                        <input type="text" name="groom_instagram" value="{{ $invitation->groom_instagram }}" class="form-input" placeholder="@username">
                    </div>
                    <div>
                        <label class="form-label text-xs">Instagram Wanita</label>
                        <input type="text" name="bride_instagram" value="{{ $invitation->bride_instagram }}" class="form-input" placeholder="@username">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="form-label text-xs">Facebook Pria</label>
                        <input type="text" name="groom_facebook" value="{{ $invitation->groom_facebook }}" class="form-input">
                    </div>
                    <div>
                        <label class="form-label text-xs">Facebook Wanita</label>
                        <input type="text" name="bride_facebook" value="{{ $invitation->bride_facebook }}" class="form-input">
                    </div>
                </div>
            </div>
            <div class="qe-modal-footer">
                <button type="button" onclick="qeClose('qe-modal-mempelai')" class="btn btn-secondary btn-sm">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-check mr-1"></i>Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL 4: Countdown & Waktu --}}
<div id="qe-modal-waktu" class="qe-modal-overlay hidden" onclick="qeCloseOnOverlay(event, this)">
    <div class="qe-modal-card">
        <div class="qe-modal-header">
            <div class="qe-modal-title">
                <span class="modal-icon" style="background: rgba(234,179,8,0.12); color: #eab308;"><i class="fas fa-clock"></i></span>
                <span>Countdown & Waktu</span>
            </div>
            <button class="qe-close-btn" onclick="qeClose('qe-modal-waktu')"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" action="{{ route('client.invitations.update', $invitation) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <input type="hidden" name="template_id" value="{{ $invitation->template_id }}">
            <input type="hidden" name="title" value="{{ $invitation->title }}">
            <input type="hidden" name="event_type" value="{{ $invitation->event_type }}">
            <input type="hidden" name="venue_name" value="{{ $invitation->venue_name }}">
            <input type="hidden" name="venue_address" value="{{ $invitation->venue_address }}">
            <div class="qe-modal-body">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Tanggal Acara Utama</label>
                        <input type="date" name="event_date" value="{{ optional($invitation->event_date)->format('Y-m-d') }}" class="form-input" required>
                    </div>
                    <div>
                        <label class="form-label">Waktu Acara Utama</label>
                        <input type="time" name="event_time" value="{{ $invitation->event_time ? \Carbon\Carbon::parse($invitation->event_time)->format('H:i') : '' }}" class="form-input" required>
                    </div>
                </div>
                <p class="text-xs mt-3" style="color: var(--text-secondary);"><i class="fas fa-info-circle mr-1"></i>Tanggal & waktu ini digunakan untuk countdown di undangan.</p>
            </div>
            <div class="qe-modal-footer">
                <button type="button" onclick="qeClose('qe-modal-waktu')" class="btn btn-secondary btn-sm">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-check mr-1"></i>Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL 5: Acara & Susunan --}}
<div id="qe-modal-acara" class="qe-modal-overlay hidden" onclick="qeCloseOnOverlay(event, this)">
    <div class="qe-modal-card">
        <div class="qe-modal-header">
            <div class="qe-modal-title">
                <span class="modal-icon" style="background: rgba(249,115,22,0.12); color: #f97316;"><i class="fas fa-calendar-check"></i></span>
                <span>Acara & Susunan</span>
            </div>
            <button class="qe-close-btn" onclick="qeClose('qe-modal-acara')"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" action="{{ route('client.invitations.update', $invitation) }}" enctype="multipart/form-data"
              x-data="{ events: {{ json_encode($invitation->events->count() > 0
                  ? $invitation->events->map(fn($e) => [
                      'event_name' => $e->event_name,
                      'event_description' => $e->event_description,
                      'event_date' => $e->event_date ? \Carbon\Carbon::parse($e->event_date)->format('Y-m-d') : '',
                      'event_time' => $e->event_time,
                      'event_end_time' => $e->event_end_time,
                      'venue_name' => $e->venue_name,
                      'venue_address' => $e->venue_address,
                      'venue_maps_url' => $e->venue_maps_url
                  ])->values()
                  : [['event_name' => 'Akad Nikah', 'event_description' => '', 'event_date' => '', 'event_time' => '', 'event_end_time' => '', 'venue_name' => '', 'venue_address' => '', 'venue_maps_url' => ''], ['event_name' => 'Resepsi', 'event_description' => '', 'event_date' => '', 'event_time' => '', 'event_end_time' => '', 'venue_name' => '', 'venue_address' => '', 'venue_maps_url' => '']]) }} }">
            @csrf @method('PUT')
            <input type="hidden" name="template_id" value="{{ $invitation->template_id }}">
            <input type="hidden" name="title" value="{{ $invitation->title }}">
            <input type="hidden" name="event_type" value="{{ $invitation->event_type }}">
            <input type="hidden" name="event_date" value="{{ optional($invitation->event_date)->format('Y-m-d') }}">
            <input type="hidden" name="event_time" value="{{ $invitation->event_time ? \Carbon\Carbon::parse($invitation->event_time)->format('H:i') : '' }}">
            <input type="hidden" name="venue_name" value="{{ $invitation->venue_name }}">
            <input type="hidden" name="venue_address" value="{{ $invitation->venue_address }}">
            <div class="qe-modal-body">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--text-secondary);">Daftar Acara</p>
                    <button type="button" @click="events.push({event_name:'',event_description:'',event_date:'',event_time:'',event_end_time:'',venue_name:'',venue_address:'',venue_maps_url:''})" class="btn btn-secondary btn-sm text-xs">
                        <i class="fas fa-plus mr-1"></i>Tambah
                    </button>
                </div>
                <template x-for="(ev, idx) in events" :key="idx">
                    <div class="p-4 mb-3 rounded-xl space-y-3" style="background: var(--bg-tertiary); border: 1px solid var(--border);">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold" style="color: var(--accent);" x-text="'Acara ' + (idx+1)"></span>
                            <button type="button" @click="events.splice(idx,1)" class="text-xs" style="color: var(--danger);" x-show="events.length > 1"><i class="fas fa-trash mr-1"></i>Hapus</button>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div><label class="form-label text-xs">Nama Acara</label><input type="text" :name="'events[' + idx + '][event_name]'" x-model="ev.event_name" class="form-input"></div>
                            <div><label class="form-label text-xs">Tempat</label><input type="text" :name="'events[' + idx + '][venue_name]'" x-model="ev.venue_name" class="form-input"></div>
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <div><label class="form-label text-xs">Tanggal</label><input type="date" :name="'events[' + idx + '][event_date]'" x-model="ev.event_date" class="form-input"></div>
                            <div><label class="form-label text-xs">Mulai</label><input type="time" :name="'events[' + idx + '][event_time]'" x-model="ev.event_time" class="form-input"></div>
                            <div><label class="form-label text-xs">Selesai</label><input type="time" :name="'events[' + idx + '][event_end_time]'" x-model="ev.event_end_time" class="form-input"></div>
                        </div>
                        <div><label class="form-label text-xs">Alamat</label><input type="text" :name="'events[' + idx + '][venue_address]'" x-model="ev.venue_address" class="form-input"></div>
                        <div><label class="form-label text-xs">Deskripsi</label><textarea :name="'events[' + idx + '][event_description]'" x-model="ev.event_description" class="form-input" rows="2"></textarea></div>
                    </div>
                </template>
            </div>
            <div class="qe-modal-footer">
                <button type="button" onclick="qeClose('qe-modal-acara')" class="btn btn-secondary btn-sm">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-check mr-1"></i>Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL 6: Lokasi --}}
<div id="qe-modal-lokasi" class="qe-modal-overlay hidden" onclick="qeCloseOnOverlay(event, this)">
    <div class="qe-modal-card">
        <div class="qe-modal-header">
            <div class="qe-modal-title">
                <span class="modal-icon" style="background: rgba(16,185,129,0.12); color: #10b981;"><i class="fas fa-map-marker-alt"></i></span>
                <span>Lokasi Acara</span>
            </div>
            <button class="qe-close-btn" onclick="qeClose('qe-modal-lokasi')"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" action="{{ route('client.invitations.update', $invitation) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <input type="hidden" name="template_id" value="{{ $invitation->template_id }}">
            <input type="hidden" name="title" value="{{ $invitation->title }}">
            <input type="hidden" name="event_type" value="{{ $invitation->event_type }}">
            <input type="hidden" name="event_date" value="{{ optional($invitation->event_date)->format('Y-m-d') }}">
            <input type="hidden" name="event_time" value="{{ $invitation->event_time ? \Carbon\Carbon::parse($invitation->event_time)->format('H:i') : '' }}">
            <div class="qe-modal-body space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Nama Tempat Utama</label>
                        <input type="text" name="venue_name" value="{{ $invitation->venue_name }}" class="form-input" required>
                    </div>
                    <div>
                        <label class="form-label">Google Maps URL</label>
                        <input type="url" name="google_maps_url" value="{{ $invitation->google_maps_url }}" class="form-input" id="qeModalMapsUrl">
                    </div>
                </div>
                <div>
                    <label class="form-label">Alamat Lengkap</label>
                    <textarea name="venue_address" class="form-input" rows="2" required>{{ $invitation->venue_address }}</textarea>
                </div>
                <div>
                    <label class="form-label font-semibold" style="color:var(--accent);">Pin Lokasi Peta</label>
                    <p class="text-xs mb-2" style="color:var(--text-secondary);">Klik pada peta atau geser marker untuk menentukan titik lokasi.</p>
                    <div class="flex gap-2 mb-2">
                        <button type="button" onclick="qeLocateMe()" class="btn btn-secondary btn-sm"><i class="fas fa-crosshairs mr-1"></i>Lokasi Saya</button>
                        <span id="qeLocateStatus" class="text-xs self-center" style="color:var(--text-secondary);"></span>
                    </div>
                    <div id="qeLocationMap" style="height:280px; border-radius:10px; border:1px solid var(--border); z-index:1;"></div>
                    <div class="grid grid-cols-2 gap-3 mt-3">
                        <div><label class="form-label text-xs">Latitude</label><input type="text" id="qeVenueLat" name="venue_lat" value="{{ $invitation->venue_lat }}" class="form-input" readonly style="background:var(--bg-tertiary);"></div>
                        <div><label class="form-label text-xs">Longitude</label><input type="text" id="qeVenueLng" name="venue_lng" value="{{ $invitation->venue_lng }}" class="form-input" readonly style="background:var(--bg-tertiary);"></div>
                    </div>
                </div>
            </div>
            <div class="qe-modal-footer">
                <button type="button" onclick="qeClose('qe-modal-lokasi')" class="btn btn-secondary btn-sm">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-check mr-1"></i>Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL 7: RSVP & Ucapan --}}
<div id="qe-modal-rsvp" class="qe-modal-overlay hidden" onclick="qeCloseOnOverlay(event, this)">
    <div class="qe-modal-card">
        <div class="qe-modal-header">
            <div class="qe-modal-title">
                <span class="modal-icon" style="background: rgba(99,102,241,0.12); color: #6366f1;"><i class="fas fa-comment-dots"></i></span>
                <span>RSVP & Ucapan</span>
            </div>
            <button class="qe-close-btn" onclick="qeClose('qe-modal-rsvp')"><i class="fas fa-times"></i></button>
        </div>
        <div class="qe-modal-body">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4">
                <div class="p-4 rounded-xl text-center" style="background: var(--bg-tertiary);">
                    <p class="text-2xl font-bold" style="color: var(--accent);">{{ $invitation->rsvps->count() }}</p>
                    <p class="text-xs mt-1" style="color:var(--text-secondary);">Total RSVP</p>
                </div>
                <div class="p-4 rounded-xl text-center" style="background: rgba(52,199,89,0.08);">
                    <p class="text-2xl font-bold" style="color: var(--success);">{{ $invitation->rsvps->where('status', 'attending')->count() }}</p>
                    <p class="text-xs mt-1" style="color:var(--text-secondary);">Hadir</p>
                </div>
                <div class="p-4 rounded-xl text-center" style="background: rgba(255,149,0,0.08);">
                    <p class="text-2xl font-bold" style="color: var(--warning);">{{ $invitation->rsvps->where('status', 'maybe')->count() }}</p>
                    <p class="text-xs mt-1" style="color:var(--text-secondary);">Ragu</p>
                </div>
                <div class="p-4 rounded-xl text-center" style="background: rgba(255,59,48,0.08);">
                    <p class="text-2xl font-bold" style="color: var(--danger);">{{ $invitation->rsvps->where('status', 'not_attending')->count() }}</p>
                    <p class="text-xs mt-1" style="color:var(--text-secondary);">Tidak Hadir</p>
                </div>
            </div>
            <p class="text-sm text-center py-4" style="color: var(--text-secondary);"><i class="fas fa-info-circle mr-1"></i>Data RSVP & ucapan dikelola otomatis oleh tamu dari halaman undangan.</p>
            <a href="{{ route('client.invitations.guests.index', $invitation) }}" class="btn btn-primary w-full text-center block">
                <i class="fas fa-users mr-2"></i>Kelola Tamu & RSVP
            </a>
        </div>
        <div class="qe-modal-footer">
            <button type="button" onclick="qeClose('qe-modal-rsvp')" class="btn btn-secondary btn-sm">Tutup</button>
        </div>
    </div>
</div>

{{-- MODAL 8: Tanda Kasih --}}
<div id="qe-modal-kasih" class="qe-modal-overlay hidden" onclick="qeCloseOnOverlay(event, this)">
    <div class="qe-modal-card">
        <div class="qe-modal-header">
            <div class="qe-modal-title">
                <span class="modal-icon" style="background: rgba(244,114,182,0.12); color: #f472b6;"><i class="fas fa-gift"></i></span>
                <span>Tanda Kasih</span>
            </div>
            <button class="qe-close-btn" onclick="qeClose('qe-modal-kasih')"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" action="{{ route('client.invitations.update', $invitation) }}" enctype="multipart/form-data"
              x-data="{ accounts: {{ json_encode($invitation->bankAccounts->count()
                  ? $invitation->bankAccounts->map(fn($a) => ['bank_name' => $a->bank_name, 'account_number' => $a->account_number, 'account_name' => $a->account_name])->values()
                  : [['bank_name' => '', 'account_number' => '', 'account_name' => '']]) }} }">
            @csrf @method('PUT')
            <input type="hidden" name="template_id" value="{{ $invitation->template_id }}">
            <input type="hidden" name="title" value="{{ $invitation->title }}">
            <input type="hidden" name="event_type" value="{{ $invitation->event_type }}">
            <input type="hidden" name="event_date" value="{{ optional($invitation->event_date)->format('Y-m-d') }}">
            <input type="hidden" name="event_time" value="{{ $invitation->event_time ? \Carbon\Carbon::parse($invitation->event_time)->format('H:i') : '' }}">
            <input type="hidden" name="venue_name" value="{{ $invitation->venue_name }}">
            <input type="hidden" name="venue_address" value="{{ $invitation->venue_address }}">
            <div class="qe-modal-body">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--text-secondary);">Rekening Bank</p>
                    <button type="button" @click="accounts.push({bank_name:'',account_number:'',account_name:''})" class="btn btn-secondary btn-sm text-xs">
                        <i class="fas fa-plus mr-1"></i>Tambah
                    </button>
                </div>
                <template x-for="(acc, idx) in accounts" :key="idx">
                    <div class="p-3 mb-3 rounded-xl" style="background: var(--bg-tertiary); border: 1px solid var(--border);">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold" style="color: var(--accent);" x-text="'Rekening ' + (idx+1)"></span>
                            <button type="button" @click="accounts.splice(idx,1)" x-show="accounts.length > 1" class="text-xs" style="color: var(--danger);"><i class="fas fa-trash"></i></button>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                            <div><label class="form-label text-xs">Nama Bank</label><input type="text" :name="'bank_accounts[' + idx + '][bank_name]'" x-model="acc.bank_name" class="form-input"></div>
                            <div><label class="form-label text-xs">No. Rekening</label><input type="text" :name="'bank_accounts[' + idx + '][account_number]'" x-model="acc.account_number" class="form-input"></div>
                            <div><label class="form-label text-xs">Atas Nama</label><input type="text" :name="'bank_accounts[' + idx + '][account_name]'" x-model="acc.account_name" class="form-input"></div>
                        </div>
                    </div>
                </template>
                <div class="mt-3">
                    <label class="form-label">Alamat Kirim Hadiah</label>
                    <textarea name="gift_address" class="form-input" rows="2">{{ $invitation->gift_address }}</textarea>
                </div>
            </div>
            <div class="qe-modal-footer">
                <button type="button" onclick="qeClose('qe-modal-kasih')" class="btn btn-secondary btn-sm">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-check mr-1"></i>Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL 9: Penutup --}}
<div id="qe-modal-penutup" class="qe-modal-overlay hidden" onclick="qeCloseOnOverlay(event, this)">
    <div class="qe-modal-card">
        <div class="qe-modal-header">
            <div class="qe-modal-title">
                <span class="modal-icon" style="background: rgba(20,184,166,0.12); color: #14b8a6;"><i class="fas fa-pen-nib"></i></span>
                <span>Teks Penutup</span>
            </div>
            <button class="qe-close-btn" onclick="qeClose('qe-modal-penutup')"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" action="{{ route('client.invitations.update', $invitation) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <input type="hidden" name="template_id" value="{{ $invitation->template_id }}">
            <input type="hidden" name="title" value="{{ $invitation->title }}">
            <input type="hidden" name="event_type" value="{{ $invitation->event_type }}">
            <input type="hidden" name="event_date" value="{{ optional($invitation->event_date)->format('Y-m-d') }}">
            <input type="hidden" name="event_time" value="{{ $invitation->event_time ? \Carbon\Carbon::parse($invitation->event_time)->format('H:i') : '' }}">
            <input type="hidden" name="venue_name" value="{{ $invitation->venue_name }}">
            <input type="hidden" name="venue_address" value="{{ $invitation->venue_address }}">
            <div class="qe-modal-body space-y-4">
                <div>
                    <label class="form-label">Teks Penutup</label>
                    <textarea name="closing_text" class="form-input" rows="4">{{ $invitation->closing_text }}</textarea>
                </div>
                <div>
                    <label class="form-label">Footer / Tagline</label>
                    <input type="text" name="footer_text" value="{{ $invitation->footer_text }}" class="form-input" placeholder="contoh: #AhmadDanSiti2026">
                </div>
            </div>
            <div class="qe-modal-footer">
                <button type="button" onclick="qeClose('qe-modal-penutup')" class="btn btn-secondary btn-sm">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-check mr-1"></i>Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL 10: Musik & Live Streaming --}}
<div id="qe-modal-musik" class="qe-modal-overlay hidden" onclick="qeCloseOnOverlay(event, this)">
    <div class="qe-modal-card">
        <div class="qe-modal-header">
            <div class="qe-modal-title">
                <span class="modal-icon" style="background: rgba(168,85,247,0.12); color: #a855f7;"><i class="fas fa-music"></i></span>
                <span>Musik & Live Streaming</span>
            </div>
            <button class="qe-close-btn" onclick="qeClose('qe-modal-musik')"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" action="{{ route('client.invitations.update', $invitation) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <input type="hidden" name="template_id" value="{{ $invitation->template_id }}">
            <input type="hidden" name="title" value="{{ $invitation->title }}">
            <input type="hidden" name="event_type" value="{{ $invitation->event_type }}">
            <input type="hidden" name="event_date" value="{{ optional($invitation->event_date)->format('Y-m-d') }}">
            <input type="hidden" name="event_time" value="{{ $invitation->event_time ? \Carbon\Carbon::parse($invitation->event_time)->format('H:i') : '' }}">
            <input type="hidden" name="venue_name" value="{{ $invitation->venue_name }}">
            <input type="hidden" name="venue_address" value="{{ $invitation->venue_address }}">
            <div class="qe-modal-body space-y-4">
                <div>
                    <label class="form-label">Upload Musik Baru</label>
                    <input type="file" name="music_url" class="form-input" accept="audio/*">
                    @if($invitation->music_url)
                        <p class="text-xs mt-1" style="color: var(--text-secondary);"><i class="fas fa-music mr-1" style="color: var(--success);"></i>Musik aktif sudah terpasang.</p>
                    @endif
                </div>
                <div>
                    <label class="form-label">Pilih dari Library Musik</label>
                    <select name="music_track_id" class="form-input">
                        <option value="">-- Tetap gunakan musik saat ini --</option>
                        @foreach($musicTracks as $track)
                            <option value="{{ $track->id }}">{{ $track->title ?: basename($track->file_path) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="p-3 rounded-xl" style="background: var(--bg-tertiary); border: 1px solid var(--border);">
                    <label class="inline-flex items-center gap-2 text-sm font-semibold cursor-pointer">
                        <input type="hidden" name="livestream_enabled" value="0">
                        <input type="checkbox" name="livestream_enabled" value="1" id="qeLivestreamToggle"
                            {{ $invitation->livestream_enabled ? 'checked' : '' }}
                            onchange="document.getElementById('qeLivestreamFields').style.display = this.checked ? '' : 'none'"
                            style="accent-color: var(--accent); width:16px; height:16px;">
                        <i class="fas fa-video" style="color: var(--accent);"></i> Aktifkan Live Streaming
                    </label>
                    <div id="qeLivestreamFields" class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-3" style="{{ $invitation->livestream_enabled ? '' : 'display:none;' }}">
                        <div>
                            <label class="form-label text-xs">Link Live Streaming</label>
                            <input type="url" name="livestream_url" value="{{ $invitation->livestream_url }}" class="form-input">
                        </div>
                        <div>
                            <label class="form-label text-xs">Label Live Streaming</label>
                            <input type="text" name="livestream_label" value="{{ $invitation->livestream_label }}" class="form-input">
                        </div>
                    </div>
                </div>
            </div>
            <div class="qe-modal-footer">
                <button type="button" onclick="qeClose('qe-modal-musik')" class="btn btn-secondary btn-sm">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-check mr-1"></i>Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Leaflet CSS untuk modal lokasi --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />

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

{{-- ===================== QUICK EDIT MODAL JS ===================== --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
(function () {
    // ─── Open / Close ────────────────────────────────────────────────
    window.qeOpen = function (id) {
        const el = document.getElementById(id);
        if (!el) return;
        el.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        // Re-init leaflet map kalau modal lokasi dibuka
        if (id === 'qe-modal-lokasi') {
            setTimeout(qeInitMap, 120);
        }
    };

    window.qeClose = function (id) {
        const el = document.getElementById(id);
        if (!el) return;
        el.classList.add('hidden');
        document.body.style.overflow = '';
    };

    window.qeCloseOnOverlay = function (event, overlay) {
        if (event.target === overlay) {
            overlay.classList.add('hidden');
            document.body.style.overflow = '';
        }
    };

    // Tutup semua modal dengan Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.qe-modal-overlay:not(.hidden)').forEach(function (m) {
                m.classList.add('hidden');
            });
            document.body.style.overflow = '';
        }
    });

    // ─── Template Preview ─────────────────────────────────────────────
    window.qeUpdateTemplatePreview = function () {
        const sel = document.getElementById('qeTemplateSelect');
        if (!sel) return;
        const opt = sel.options[sel.selectedIndex];
        const wrap = document.getElementById('qeTemplatePreviewWrap');
        const img  = document.getElementById('qeTemplatePreviewImg');
        const thumb = opt?.dataset?.thumbnail || '';
        if (!thumb) {
            if (wrap) wrap.style.display = 'none';
            if (img)  img.src = '';
            return;
        }
        if (img)  img.src = thumb;
        if (wrap) wrap.style.display = 'block';
    };

    // ─── Leaflet Map (Modal Lokasi) ───────────────────────────────────
    let qePickerMap = null;
    let qePickerMarker = null;
    let qePickerAccuracyCircle = null;
    let qeGeoWatchId = null;
    let qeMapInited = false;

    function qeUpdateCoordInputs(lat, lng) {
        const latEl = document.getElementById('qeVenueLat');
        const lngEl = document.getElementById('qeVenueLng');
        if (latEl) latEl.value = lat.toFixed(8);
        if (lngEl) lngEl.value = lng.toFixed(8);
    }

    function qeClearAccuracyCircle() {
        if (qePickerMap && qePickerAccuracyCircle) {
            qePickerMap.removeLayer(qePickerAccuracyCircle);
            qePickerAccuracyCircle = null;
        }
    }

    function qeUpdatePickerPosition(lat, lng, accuracy, zoom) {
        if (!qePickerMap) return;
        zoom = zoom || qePickerMap.getZoom();
        if (!qePickerMarker) {
            qePickerMarker = L.marker([lat, lng], { draggable: true }).addTo(qePickerMap);
            qePickerMarker.on('dragend', function () {
                const pos = qePickerMarker.getLatLng();
                qeUpdateCoordInputs(pos.lat, pos.lng);
                qeClearAccuracyCircle();
            });
        }
        qePickerMap.setView([lat, lng], zoom);
        qePickerMarker.setLatLng([lat, lng]);
        qeUpdateCoordInputs(lat, lng);
        qeClearAccuracyCircle();

        if (typeof accuracy === 'number' && Number.isFinite(accuracy) && accuracy > 0) {
            qePickerAccuracyCircle = L.circle([lat, lng], {
                radius: accuracy,
                color: '#2563eb',
                weight: 1,
                fillColor: '#60a5fa',
                fillOpacity: 0.12,
            }).addTo(qePickerMap);
        }
    }

    window.qeInitMap = function () {
        if (typeof L === 'undefined') return;
        const latEl = document.getElementById('qeVenueLat');
        const lngEl = document.getElementById('qeVenueLng');
        let initialLat = parseFloat(latEl ? latEl.value : '');
        let initialLng = parseFloat(lngEl ? lngEl.value : '');
        const hasCoords = !isNaN(initialLat) && !isNaN(initialLng) && (initialLat !== 0 || initialLng !== 0);
        if (!hasCoords) { initialLat = -2.5489; initialLng = 118.0149; }

        if (!qeMapInited) {
            qePickerMap = L.map('qeLocationMap').setView([initialLat, initialLng], hasCoords ? 17 : 5);
            const hybrid  = L.tileLayer('https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', { maxZoom: 20, subdomains: ['mt0','mt1','mt2','mt3'], attribution: '&copy; Google Maps' });
            const streets = L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', { maxZoom: 20, subdomains: ['mt0','mt1','mt2','mt3'], attribution: '&copy; Google Maps' });
            hybrid.addTo(qePickerMap);
            L.control.layers({ 'Hybrid': hybrid, 'Streets': streets }).addTo(qePickerMap);

            if (hasCoords) {
                qePickerMarker = L.marker([initialLat, initialLng], { draggable: true }).addTo(qePickerMap);
                qePickerMarker.on('dragend', function () {
                    const pos = qePickerMarker.getLatLng();
                    qeUpdateCoordInputs(pos.lat, pos.lng);
                    qeClearAccuracyCircle();
                });
            }

            qePickerMap.on('click', function (e) {
                qeUpdatePickerPosition(e.latlng.lat, e.latlng.lng, null, qePickerMap.getZoom());
                qeClearAccuracyCircle();
            });

            // Auto-parse coords dari input Google Maps URL
            const mapsUrlInput = document.getElementById('qeModalMapsUrl');
            if (mapsUrlInput) {
                mapsUrlInput.addEventListener('input', function () {
                    const coords = qeParseMapsUrl(this.value.trim());
                    if (coords) qeUpdatePickerPosition(coords.lat, coords.lng, null, 17);
                });
            }

            qeMapInited = true;
        }
        qePickerMap.invalidateSize();
    };

    function qeParseMapsUrl(url) {
        let m = url.match(/@(-?\d+\.?\d*),(-?\d+\.?\d*)/);
        if (m) return { lat: parseFloat(m[1]), lng: parseFloat(m[2]) };
        m = url.match(/[?&](q|query)=(-?\d+\.?\d*),(-?\d+\.?\d*)/);
        if (m) return { lat: parseFloat(m[2]), lng: parseFloat(m[3]) };
        m = url.match(/(-?\d+\.?\d*),\s*(-?\d+\.?\d*)/);
        if (m) return { lat: parseFloat(m[1]), lng: parseFloat(m[2]) };
        return null;
    }

    window.qeLocateMe = function () {
        const statusEl = document.getElementById('qeLocateStatus');
        if (!('geolocation' in navigator)) {
            if (statusEl) statusEl.textContent = 'Browser tidak mendukung geolokasi.';
            return;
        }
        if (qeGeoWatchId !== null) { navigator.geolocation.clearWatch(qeGeoWatchId); qeGeoWatchId = null; }

        if (statusEl) statusEl.textContent = 'Mencari lokasi...';
        let bestPosition = null;
        let finished = false;

        const finish = function (label) {
            if (finished) return;
            finished = true;
            if (qeGeoWatchId !== null) { navigator.geolocation.clearWatch(qeGeoWatchId); qeGeoWatchId = null; }
            if (!bestPosition) { if (statusEl) statusEl.textContent = 'Gagal mendapatkan lokasi.'; return; }
            const acc = bestPosition.coords.accuracy;
            if (!isFinite(acc) || acc > 500) { if (statusEl) statusEl.textContent = `Akurasi rendah (${Math.round(acc||0)}m). Aktifkan GPS lalu coba lagi.`; return; }
            qeUpdatePickerPosition(bestPosition.coords.latitude, bestPosition.coords.longitude, acc, acc <= 35 ? 19 : 18);
            if (statusEl) statusEl.textContent = `${label}. Akurasi ~${Math.round(acc)}m.`;
        };

        qeGeoWatchId = navigator.geolocation.watchPosition(
            function (pos) {
                const acc = pos.coords.accuracy ?? Infinity;
                if (!bestPosition || acc < (bestPosition.coords.accuracy ?? Infinity)) {
                    bestPosition = pos;
                    if (acc <= 500 && qePickerMap) qeUpdatePickerPosition(pos.coords.latitude, pos.coords.longitude, acc, acc <= 35 ? 19 : 18);
                    if (statusEl) statusEl.textContent = `Menyempurnakan... ${Math.round(acc)}m`;
                }
                if (acc <= 20) finish('Lokasi akurat ditemukan');
            },
            function (err) {
                const msgs = { 1:'Izin ditolak.', 2:'Lokasi tidak tersedia.', 3:'Waktu habis.' };
                if (bestPosition) { finish('Lokasi terbaik dipakai'); return; }
                if (statusEl) statusEl.textContent = msgs[err.code] || 'Gagal.';
            },
            { enableHighAccuracy: true, timeout: 25000, maximumAge: 0 }
        );
        setTimeout(function () { if (!finished) finish('Lokasi terbaik ditemukan'); }, 9000);
    };
})();
</script>
@endpush
