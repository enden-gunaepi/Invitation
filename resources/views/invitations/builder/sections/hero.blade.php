@php
    $coverImage = $assetUrl($invitation->cover_photo ?: $invitation->template?->thumbnail);
    $inviteeName = $guest?->name ?: ($personalization['greeting_name'] ?? null);
@endphp
@if (!empty($isGnv2))
<section class="builder-section builder-gnv2-panel">
    <div class="builder-gnv2-frame">
        @if ($coverImage)
            <img src="{{ $coverImage }}" alt="{{ $invitation->title }}" class="builder-gnv2-image">
        @endif
        <div class="builder-gnv2-overlay-dark"></div>
        <div class="builder-gnv2-bottom-fade"></div>
        <div class="builder-gnv2-ornament" style="left: -24px; bottom: -26px;"></div>
        <div class="builder-gnv2-ornament" style="right: -24px; bottom: -26px;"></div>
    </div>
    <div class="builder-gnv2-content" style="display:flex; min-height:100vh; flex-direction:column; justify-content:flex-end; padding-bottom:64px;">
        <div class="builder-kicker">Undangan Digital</div>
        <h1 class="builder-heading" style="font-size: clamp(40px, 9vw, 76px); color:#fff;">{{ $invitation->title }}</h1>
        <p style="margin: 14px 0 0; font-size: 14px; opacity: 0.82;">{{ optional($invitation->event_date)->translatedFormat('d.m.y') }}</p>
        @if ($inviteeName)
            <p style="margin: 24px 0 0; font-size: 13px; opacity: 0.82;">Kepada Yth.</p>
            <h2 style="margin: 6px 0 0; font-size: 22px; font-weight: 500;">{{ $inviteeName }}</h2>
        @endif
        <div class="builder-actions" style="justify-content:center;">
            @if (!empty($invitation->maps_deep_link))
                <a href="{{ $demoMode ? $invitation->maps_deep_link : route('invitation.map.click', ['slug' => $invitation->slug, 'token' => $guest?->token]) }}" class="builder-button" style="background:rgba(255,255,255,0.2); color:#fff; backdrop-filter:blur(10px);">Lihat Lokasi</a>
            @endif
            @if (!$demoMode)
                <a href="#builder-rsvp" class="builder-button" style="background:rgba(255,255,255,0.12); color:#fff; border:1px solid rgba(255,255,255,0.2);">Open Invitation</a>
            @endif
        </div>
    </div>
</section>
@else
<section class="builder-section">
    <div class="builder-card" style="overflow: hidden;">
        <div style="display:grid; gap:0; align-items:stretch; {{ ($section['variant'] ?? '') === 'cover-split' ? 'grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));' : '' }}">
            <div style="padding: clamp(28px, 6vw, 72px); position: relative; background: linear-gradient(145deg, rgba(255,248,243,0.95), rgba(255,255,255,0.8));">
                <div class="builder-kicker">Undangan Digital</div>
                <h1 class="builder-heading" style="font-size: clamp(38px, 7vw, 74px); line-height: 0.96;">
                    {{ $invitation->title }}
                </h1>
                <p style="margin: 20px 0 0; max-width: 620px; color: rgba(43, 31, 36, 0.82);">
                    {{ $invitation->opening_text ?: 'Dengan penuh sukacita kami mengundang Anda untuk hadir dan menjadi bagian dari momen terbaik kami.' }}
                </p>
                <div style="margin-top: 28px; font-size: clamp(28px, 4vw, 44px); font-family: var(--builder-heading-font);">
                    {{ $invitation->groom_name ?: $invitation->host_name ?: 'Nama Tuan Rumah' }}
                    @if ($invitation->bride_name)
                        <span style="opacity: 0.5;">&amp;</span> {{ $invitation->bride_name }}
                    @endif
                </div>
                <div style="margin-top: 16px; color: var(--builder-accent); font-weight: 700;">
                    {{ optional($invitation->event_date)->translatedFormat('d F Y') }} • {{ \Carbon\Carbon::parse($invitation->event_time)->format('H:i') }}
                </div>
                @if ($inviteeName)
                    <div style="margin-top: 22px; padding: 14px 18px; border-radius: 18px; background: rgba(183, 110, 121, 0.08); display:inline-block;">
                        Untuk {{ $inviteeName }}
                    </div>
                @endif
                <div class="builder-actions">
                @if (!empty($invitation->maps_deep_link))
                        <a href="{{ $demoMode ? $invitation->maps_deep_link : route('invitation.map.click', ['slug' => $invitation->slug, 'token' => $guest?->token]) }}" class="builder-button builder-button-primary">Lihat Lokasi</a>
                    @endif
                    @if (!$demoMode)
                        <a href="#builder-rsvp" class="builder-button builder-button-secondary">Konfirmasi Kehadiran</a>
                    @endif
                </div>
            </div>
            @if ($coverImage)
                <div style="min-height: 340px; background: url('{{ $coverImage }}') center/cover;"></div>
            @endif
        </div>
    </div>
</section>
@endif
