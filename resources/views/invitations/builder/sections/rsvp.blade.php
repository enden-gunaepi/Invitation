@if (!empty($isGnv2))
<section class="builder-section builder-gnv2-panel" id="builder-rsvp">
    <div class="builder-gnv2-content" style="width:min(100%, 980px);">
        <div class="builder-kicker">RSVP</div>
        <h2 class="builder-heading" style="font-size: clamp(34px, 7vw, 58px); color:#fff;">Konfirmasi Kehadiran</h2>
        <div class="builder-grid builder-gnv2-grid-2" style="margin-top:28px; align-items:start;">
            <div>
                @if ($demoMode)
                    <div class="builder-card" style="padding: 24px;">Form RSVP dinonaktifkan di mode demo.</div>
                @else
                    <form method="POST" action="{{ route('invitation.rsvp', $invitation->slug) }}" class="builder-gnv2-form">
                        @csrf
                        <input type="hidden" name="guest_id" value="{{ $guest?->id }}">
                        <input type="hidden" name="redirect_anchor" value="builder-rsvp">
                        <label>
                            <span>Nama :</span>
                            <input type="text" name="name" value="{{ old('name', $guest?->name) }}" required class="builder-gnv2-input" placeholder="Nama Anda">
                        </label>
                        <label>
                            <span>Jumlah Tamu :</span>
                            <input type="number" name="pax" min="1" max="10" value="{{ old('pax', 1) }}" class="builder-gnv2-input" placeholder="Jumlah tamu hadir">
                        </label>
                        <label>
                            <span>Nomor WhatsApp :</span>
                            <input type="text" name="phone" value="{{ old('phone', $guest?->phone) }}" class="builder-gnv2-input" placeholder="628123456789">
                        </label>
                        <label>
                            <span>Konfirmasi :</span>
                            <select name="status" class="builder-gnv2-select">
                                <option value="attending">Hadir</option>
                                <option value="maybe">Masih Tentatif</option>
                                <option value="not_attending">Tidak Hadir</option>
                            </select>
                        </label>
                        <label>
                            <span>Ucapan &amp; Doa :</span>
                            <textarea name="message" class="builder-gnv2-textarea" placeholder="Tulis ucapan dan doa untuk mempelai...">{{ old('message') }}</textarea>
                        </label>
                        <button type="submit" class="builder-gnv2-submit">Kirim RSVP</button>
                    </form>
                @endif
            </div>
            <div class="builder-grid">
                @forelse ($invitation->rsvps as $rsvp)
                    <article class="builder-gnv2-message-card">
                        <p style="font-family:var(--builder-heading-font); font-size:1rem; margin:0;">{{ $rsvp->name }}</p>
                        <p style="margin:4px 0 0; font-size:.74rem; opacity:.85;">{{ $rsvp->pax }} pax • {{ ['attending' => 'Hadir', 'maybe' => 'Tentatif', 'not_attending' => 'Tidak Hadir'][$rsvp->status] ?? $rsvp->status }}</p>
                        @if ($rsvp->message)
                            <p style="margin:8px 0 0; opacity:.92;">{{ $rsvp->message }}</p>
                        @endif
                    </article>
                @empty
                    <div class="builder-card" style="padding: 20px;">Belum ada RSVP yang tampil.</div>
                @endforelse
            </div>
        </div>
    </div>
</section>
@else
<section class="builder-section" id="builder-rsvp">
    <div class="builder-card" style="padding: 28px;">
        <div class="builder-kicker">RSVP</div>
        <h2 class="builder-heading" style="font-size: clamp(28px, 5vw, 44px);">Konfirmasi kehadiran</h2>
        @if ($demoMode)
            <p style="margin-top: 16px; color: rgba(43,31,36,0.78);">Form RSVP dinonaktifkan di mode demo. Gunakan undangan aktif untuk mencoba alur sebenarnya.</p>
        @else
            <form method="POST" action="{{ route('invitation.rsvp', $invitation->slug) }}" style="margin-top: 24px;">
                @csrf
                <input type="hidden" name="guest_id" value="{{ $guest?->id }}">
                <input type="hidden" name="redirect_anchor" value="builder-rsvp">
                <div class="builder-grid builder-grid-2">
                    <label style="display:block;">
                        <span style="display:block; margin-bottom:8px; font-weight:700;">Nama</span>
                        <input type="text" name="name" value="{{ old('name', $guest?->name) }}" required style="width:100%; padding:14px 16px; border-radius:18px; border:1px solid rgba(122,78,87,0.18);">
                    </label>
                    <label style="display:block;">
                        <span style="display:block; margin-bottom:8px; font-weight:700;">Nomor WhatsApp</span>
                        <input type="text" name="phone" value="{{ old('phone', $guest?->phone) }}" style="width:100%; padding:14px 16px; border-radius:18px; border:1px solid rgba(122,78,87,0.18);">
                    </label>
                </div>
                <div class="builder-grid builder-grid-2" style="margin-top: 16px;">
                    <label style="display:block;">
                        <span style="display:block; margin-bottom:8px; font-weight:700;">Status</span>
                        <select name="status" style="width:100%; padding:14px 16px; border-radius:18px; border:1px solid rgba(122,78,87,0.18);">
                            <option value="attending">Hadir</option>
                            <option value="maybe">Masih Tentatif</option>
                            <option value="not_attending">Tidak Hadir</option>
                        </select>
                    </label>
                    <label style="display:block;">
                        <span style="display:block; margin-bottom:8px; font-weight:700;">Jumlah Tamu</span>
                        <input type="number" name="pax" min="1" max="10" value="{{ old('pax', 1) }}" style="width:100%; padding:14px 16px; border-radius:18px; border:1px solid rgba(122,78,87,0.18);">
                    </label>
                </div>
                <label style="display:block; margin-top:16px;">
                    <span style="display:block; margin-bottom:8px; font-weight:700;">Pesan</span>
                    <textarea name="message" rows="4" style="width:100%; padding:14px 16px; border-radius:18px; border:1px solid rgba(122,78,87,0.18);">{{ old('message') }}</textarea>
                </label>
                <div class="builder-actions">
                    <button type="submit" class="builder-button builder-button-primary" style="border:none;">Kirim RSVP</button>
                </div>
            </form>
        @endif
    </div>
</section>
@endif
