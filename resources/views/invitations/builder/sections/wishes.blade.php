@if (!empty($isGnv2))
<section class="builder-section builder-gnv2-section-blue" style="padding: 80px 20px;">
    <div class="builder-container">
        <div class="builder-kicker" style="color:#fff;">Wishes</div>
        <h2 class="builder-heading" style="font-size: clamp(34px, 7vw, 58px); color:#fff;">Ucapan &amp; Doa</h2>
        <div class="builder-grid builder-gnv2-grid-2" style="margin-top:28px; align-items:start;">
            <div class="builder-grid">
                @forelse ($invitation->wishes as $wish)
                    <article class="builder-gnv2-message-card">
                        <p style="font-family:var(--builder-heading-font); font-size:1rem; margin:0;">{{ $wish->name }}</p>
                        <p style="margin:8px 0 0; opacity:.92;">{{ $wish->message }}</p>
                    </article>
                @empty
                    <div class="builder-card" style="padding: 20px;">Belum ada ucapan yang tampil.</div>
                @endforelse
            </div>
            <div>
                @if ($demoMode)
                    <div class="builder-card" style="padding: 24px;">Form ucapan dinonaktifkan di mode demo.</div>
                @else
                    <form method="POST" action="{{ route('invitation.wish', $invitation->slug) }}" class="builder-gnv2-form">
                        @csrf
                        <label>
                            <span>Nama :</span>
                            <input type="text" name="name" value="{{ old('name') }}" required class="builder-gnv2-input" placeholder="Nama Anda">
                        </label>
                        <label>
                            <span>Ucapan :</span>
                            <textarea name="message" required class="builder-gnv2-textarea" placeholder="Tulis ucapan dan doa untuk mempelai...">{{ old('message') }}</textarea>
                        </label>
                        <button type="submit" class="builder-gnv2-submit">Kirim Ucapan</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</section>
@else
<section class="builder-section">
    <div class="builder-card" style="padding: 28px;">
        <div class="builder-kicker">Ucapan</div>
        <h2 class="builder-heading" style="font-size: clamp(28px, 5vw, 44px);">Tinggalkan doa dan pesan hangat</h2>
        <div class="builder-grid builder-grid-2" style="margin-top: 24px;">
            <div class="builder-grid">
                @forelse ($invitation->wishes as $wish)
                    <article style="padding: 18px 20px; border-radius: 22px; background: rgba(255,255,255,0.8); border:1px solid rgba(122,78,87,0.08);">
                        <strong>{{ $wish->name }}</strong>
                        <p style="margin: 8px 0 0; color: rgba(43,31,36,0.78);">{{ $wish->message }}</p>
                    </article>
                @empty
                    <p style="color: rgba(43,31,36,0.72);">Belum ada ucapan yang tampil.</p>
                @endforelse
            </div>
            <div>
                @if ($demoMode)
                    <p style="color: rgba(43,31,36,0.78);">Form ucapan dinonaktifkan di mode demo.</p>
                @else
                    <form method="POST" action="{{ route('invitation.wish', $invitation->slug) }}">
                        @csrf
                        <input type="hidden" name="redirect_anchor" value="builder-rsvp">
                        <label style="display:block;">
                            <span style="display:block; margin-bottom:8px; font-weight:700;">Nama</span>
                            <input type="text" name="name" value="{{ old('name') }}" required style="width:100%; padding:14px 16px; border-radius:18px; border:1px solid rgba(122,78,87,0.18);">
                        </label>
                        <label style="display:block; margin-top:16px;">
                            <span style="display:block; margin-bottom:8px; font-weight:700;">Ucapan</span>
                            <textarea name="message" rows="6" required style="width:100%; padding:14px 16px; border-radius:18px; border:1px solid rgba(122,78,87,0.18);">{{ old('message') }}</textarea>
                        </label>
                        <div class="builder-actions">
                            <button type="submit" class="builder-button builder-button-primary" style="border:none;">Kirim Ucapan</button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</section>
@endif
