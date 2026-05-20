@if ($invitation->venue_name || $invitation->venue_address)
@if (!empty($isGnv2))
<section class="builder-section builder-gnv2-panel">
    <div class="builder-gnv2-content" style="width:min(100%, 940px);">
        <div class="builder-kicker">Location</div>
        <h2 class="builder-heading" style="font-size: clamp(34px, 7vw, 58px); color:#fff;">Temukan kami di sini</h2>
        <div class="builder-grid builder-gnv2-grid-2" style="margin-top:28px; text-align:left;">
            <div class="builder-card" style="padding: 24px;">
                <h3 class="builder-heading" style="font-size: 30px; color:#fff;">{{ $invitation->venue_name }}</h3>
                <p style="margin:12px 0 0; opacity:.9;">{{ $invitation->venue_address }}</p>
                <div class="builder-actions">
                    <a href="{{ $demoMode ? $invitation->maps_deep_link : route('invitation.map.click', ['slug' => $invitation->slug, 'token' => $guest?->token]) }}" class="builder-button" style="background:rgba(255,255,255,0.2); color:#fff;">Open Maps</a>
                </div>
            </div>
            <div class="builder-card" style="padding: 24px; min-height: 260px; display:flex; align-items:center; justify-content:center; text-align:center;">
                <div>
                    <div style="font-size:40px; font-family:var(--builder-heading-font);">Map</div>
                    <p style="margin:10px 0 0; opacity:.88;">Buka Google Maps untuk melihat rute menuju lokasi acara.</p>
                </div>
            </div>
        </div>
    </div>
</section>
@else
<section class="builder-section">
    <div class="builder-card" style="padding: 28px;">
        <div class="builder-kicker">Lokasi</div>
        <h2 class="builder-heading" style="font-size: clamp(28px, 5vw, 44px);">Temukan kami di sini</h2>
        <div class="builder-grid builder-grid-2" style="margin-top: 24px;">
            <div style="padding: 22px; border-radius: 24px; background: rgba(255,248,243,0.88);">
                <h3 class="builder-heading" style="font-size: 28px;">{{ $invitation->venue_name }}</h3>
                <p style="margin: 12px 0 0; color: rgba(43,31,36,0.78);">{{ $invitation->venue_address }}</p>
                <div class="builder-actions">
                    <a href="{{ $demoMode ? $invitation->maps_deep_link : route('invitation.map.click', ['slug' => $invitation->slug, 'token' => $guest?->token]) }}" class="builder-button builder-button-primary">Buka Google Maps</a>
                </div>
            </div>
            <div style="min-height: 240px; border-radius: 24px; background: linear-gradient(135deg, rgba(183,110,121,0.12), rgba(122,78,87,0.14)); display:flex; align-items:center; justify-content:center; padding:24px; text-align:center;">
                <div>
                    <div style="font-size: 32px; font-weight: 700; color: var(--builder-accent);">MAP</div>
                    <p style="margin: 12px 0 0; color: rgba(43,31,36,0.72);">Gunakan tombol di samping untuk membuka rute menuju lokasi acara.</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endif
@endif
