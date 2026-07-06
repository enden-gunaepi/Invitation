{{-- Partial: Quick Edit Panel (dipakai di mobile & desktop) --}}
<div class="card p-5">
    <div class="flex items-center gap-2 mb-4">
        <div style="width:32px;height:32px;border-radius:9px;background:var(--accent-bg);display:flex;align-items:center;justify-content:center;">
            <i class="fas fa-sliders-h" style="color:var(--accent);font-size:14px;"></i>
        </div>
        <h3 class="font-bold text-sm">Edit Undangan Cepat</h3>
    </div>
    <div class="qe-grid">

        {{-- 1. Template --}}
        <button type="button" class="qe-btn" onclick="qeOpen('qe-modal-template')">
            <span class="qe-icon" style="background:rgba(139,92,246,0.12);">
                <i class="fas fa-palette" style="color:#8b5cf6;"></i>
            </span>
            <span class="qe-label">Template</span>
        </button>

        {{-- 2. Cover & Pembuka --}}
        <button type="button" class="qe-btn" onclick="qeOpen('qe-modal-cover')">
            <span class="qe-icon" style="background:rgba(59,130,246,0.12);">
                <i class="fas fa-image" style="color:#3b82f6;"></i>
            </span>
            <span class="qe-label">Cover & Pembuka</span>
        </button>

        {{-- 3. Data Mempelai --}}
        <button type="button" class="qe-btn" onclick="qeOpen('qe-modal-mempelai')">
            <span class="qe-icon" style="background:rgba(236,72,153,0.12);">
                <i class="fas fa-heart" style="color:#ec4899;"></i>
            </span>
            <span class="qe-label">Data Mempelai</span>
        </button>

        {{-- 4. Countdown & Waktu --}}
        <button type="button" class="qe-btn" onclick="qeOpen('qe-modal-waktu')">
            <span class="qe-icon" style="background:rgba(234,179,8,0.12);">
                <i class="fas fa-clock" style="color:#eab308;"></i>
            </span>
            <span class="qe-label">Countdown & Waktu</span>
        </button>

        {{-- 5. Acara & Susunan --}}
        <button type="button" class="qe-btn" onclick="qeOpen('qe-modal-acara')">
            <span class="qe-icon" style="background:rgba(249,115,22,0.12);">
                <i class="fas fa-calendar-check" style="color:#f97316;"></i>
            </span>
            <span class="qe-label">Acara & Susunan</span>
        </button>

        {{-- 6. Lokasi --}}
        <button type="button" class="qe-btn" onclick="qeOpen('qe-modal-lokasi')">
            <span class="qe-icon" style="background:rgba(16,185,129,0.12);">
                <i class="fas fa-map-marker-alt" style="color:#10b981;"></i>
            </span>
            <span class="qe-label">Lokasi</span>
        </button>

        {{-- 7. RSVP & Ucapan --}}
        <button type="button" class="qe-btn" onclick="qeOpen('qe-modal-rsvp')">
            <span class="qe-icon" style="background:rgba(99,102,241,0.12);">
                <i class="fas fa-comment-dots" style="color:#6366f1;"></i>
            </span>
            <span class="qe-label">RSVP & Ucapan</span>
        </button>

        {{-- 8. Tanda Kasih --}}
        <button type="button" class="qe-btn" onclick="qeOpen('qe-modal-kasih')">
            <span class="qe-icon" style="background:rgba(244,114,182,0.12);">
                <i class="fas fa-gift" style="color:#f472b6;"></i>
            </span>
            <span class="qe-label">Tanda Kasih</span>
        </button>

        {{-- 9. Penutup --}}
        <button type="button" class="qe-btn" onclick="qeOpen('qe-modal-penutup')">
            <span class="qe-icon" style="background:rgba(20,184,166,0.12);">
                <i class="fas fa-pen-nib" style="color:#14b8a6;"></i>
            </span>
            <span class="qe-label">Penutup</span>
        </button>

        {{-- 10. Musik & Live --}}
        <button type="button" class="qe-btn" onclick="qeOpen('qe-modal-musik')">
            <span class="qe-icon" style="background:rgba(168,85,247,0.12);">
                <i class="fas fa-music" style="color:#a855f7;"></i>
            </span>
            <span class="qe-label">Musik & Live</span>
        </button>

    </div>
</div>
