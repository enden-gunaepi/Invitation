@extends('layouts.client')
@section('title', $invitation->title)
@section('page-title', $invitation->title)
@section('page-subtitle', ucfirst($invitation->event_type) . ' · ' . $invitation->event_date->format('d M Y'))

@section('content')
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
                        <input type="file" name="photo" class="form-input text-xs" accept="image/*" required>
                    </div>
                    <input type="text" name="caption" class="form-input" placeholder="Caption" style="max-width: 140px;">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-upload"></i></button>
                </form>
                @else
                <p class="text-xs text-center py-2" style="color: var(--text-secondary);">
                    <i class="fas fa-lock mr-1"></i> Batas foto tercapai. Upgrade paket untuk menambah foto.
                </p>
                @endif
            </div>
        </div>

        {{-- RSVP List --}}
        <div class="card overflow-hidden">
            <div class="px-6 py-4 border-b" style="border-color: var(--border);">
                <h3 class="font-bold text-base">RSVP ({{ $invitation->rsvps->count() }})</h3>
            </div>
            <div class="p-4">
                @forelse($invitation->rsvps as $rsvp)
                <div class="flex items-center gap-3 p-3 rounded-lg transition mb-1"
                     onmouseover="this.style.background='var(--hover-bg)'" onmouseout="this.style.background='transparent'">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold"
                        style="{{ $rsvp->status === 'attending' ? 'background:rgba(52,199,89,0.12);color:var(--success)' : ($rsvp->status === 'maybe' ? 'background:rgba(255,149,0,0.12);color:var(--warning)' : 'background:rgba(255,59,48,0.12);color:var(--danger)') }}">
                        {{ $rsvp->status === 'attending' ? '✓' : ($rsvp->status === 'maybe' ? '?' : '✗') }}
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold">{{ $rsvp->name }}</p>
                        <p class="text-xs" style="color: var(--text-secondary);">{{ $rsvp->pax }} orang · {{ $rsvp->created_at->diffForHumans() }}</p>
                    </div>
                    @if($rsvp->message)
                    <p class="text-xs max-w-xs truncate" style="color: var(--text-secondary);">{{ $rsvp->message }}</p>
                    @endif
                </div>
                @empty
                <p class="text-center text-sm py-6" style="color: var(--text-secondary);">Belum ada RSVP</p>
                @endforelse
            </div>
        </div>

        {{-- Wishes --}}
        <div class="card overflow-hidden">
            <div class="px-6 py-4 border-b" style="border-color: var(--border);">
                <h3 class="font-bold text-base">Ucapan ({{ $invitation->wishes->count() }})</h3>
            </div>
            <div class="p-4">
                @forelse($invitation->wishes as $wish)
                <div class="p-3 rounded-lg transition mb-2"
                     onmouseover="this.style.background='var(--hover-bg)'" onmouseout="this.style.background='transparent'">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-sm font-semibold">{{ $wish->name }}</span>
                        <span class="text-xs" style="color: var(--text-secondary);">{{ $wish->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-sm" style="color: var(--text-secondary);">{{ $wish->message }}</p>
                </div>
                @empty
                <p class="text-center text-sm py-6" style="color: var(--text-secondary);">Belum ada ucapan</p>
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
                        <i class="fas fa-paper-plane mr-2"></i> Submit untuk Review
                    </button>
                </form>
                @endif
                @if($invitation->isActive())
                <a href="{{ $invitation->getPublicUrl() }}" target="_blank" class="btn btn-secondary w-full text-center block text-sm py-3">
                    <i class="fas fa-external-link-alt mr-2"></i> Lihat Undangan
                </a>
                @endif
                <a href="{{ route('client.invitations.guests.index', $invitation) }}" class="btn btn-secondary w-full text-center block text-sm py-3">
                    <i class="fas fa-users mr-2"></i> Kelola Tamu
                </a>

                {{-- Payment / Checkout --}}
                @php
                    $payment = \App\Models\Payment::where('invitation_id', $invitation->id)->latest()->first();
                @endphp
                @if($payment && $payment->isPaid())
                <div class="p-3 rounded-lg text-center text-xs" style="background: rgba(52,199,89,0.08); color: var(--success);">
                    <i class="fas fa-check-circle mr-1"></i> Lunas — {{ $payment->paid_at?->format('d M Y') }}
                </div>
                @elseif($payment && $payment->isPending())
                <a href="{{ route('client.checkout.status', $invitation) }}" class="btn w-full text-center block text-sm py-3" style="background: rgba(255,149,0,0.1); color: var(--warning); border: 1px solid var(--warning);">
                    <i class="fas fa-clock mr-2"></i> Menunggu Pembayaran
                </a>
                @else
                <a href="{{ route('client.checkout.show', $invitation) }}" class="btn w-full text-center block text-sm py-3" style="background: linear-gradient(135deg, var(--accent), #5856d6); color: white;">
                    <i class="fas fa-credit-card mr-2"></i> Bayar Sekarang
                </a>
                @endif
            </div>
        </div>

        {{-- Package Info --}}
        <div class="card p-6">
            <h3 class="font-bold text-base mb-4">Paket — {{ $invitation->package->name ?? '-' }}</h3>
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
        </div>

        {{-- Stats --}}
        <div class="card p-6">
            <h3 class="font-bold text-base mb-4">Statistik</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between text-sm">
                    <span style="color: var(--text-secondary);"><i class="fas fa-eye mr-2 w-4"></i>Kunjungan</span>
                    <span class="font-bold">{{ number_format($invitation->view_count) }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span style="color: var(--success);"><i class="fas fa-check-circle mr-2 w-4"></i>Hadir</span>
                    <span class="font-bold" style="color: var(--success);">{{ $invitation->rsvps->where('status', 'attending')->count() }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span style="color: var(--warning);"><i class="fas fa-question-circle mr-2 w-4"></i>Maybe</span>
                    <span class="font-bold" style="color: var(--warning);">{{ $invitation->rsvps->where('status', 'maybe')->count() }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span style="color: var(--danger);"><i class="fas fa-times-circle mr-2 w-4"></i>Tidak</span>
                    <span class="font-bold" style="color: var(--danger);">{{ $invitation->rsvps->where('status', 'not_attending')->count() }}</span>
                </div>
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
