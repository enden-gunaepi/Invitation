{{-- Partial: Actions Panel (dipakai di mobile & desktop) --}}
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
            <div class="flex-1 p-2.5 rounded-lg text-xs break-all truncate" style="background:var(--bg-tertiary);color:var(--accent);">{{ $invitation->getPublicUrl() }}</div>
            <button type="button" onclick="navigator.clipboard.writeText('{{ $invitation->getPublicUrl() }}'); const icon = this.querySelector('i'); if(icon){ icon.className='fas fa-check'; setTimeout(()=>icon.className='fas fa-copy',2000); }"
                    class="qe-close-btn shrink-0" title="Copy Link" style="width:34px;height:34px;">
                <i class="fas fa-copy" style="font-size:13px;"></i>
            </button>
        </div>
    </div>
    @endif
</div>
