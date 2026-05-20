@if (!empty($isGnv2))
<section class="builder-section builder-gnv2-panel">
    <div class="builder-gnv2-frame">
        @if ($assetUrl($invitation->cover_photo ?: $invitation->template?->thumbnail))
            <img src="{{ $assetUrl($invitation->cover_photo ?: $invitation->template?->thumbnail) }}" alt="Closing" class="builder-gnv2-image">
        @endif
        <div class="builder-gnv2-overlay-dark"></div>
        <div class="builder-gnv2-bottom-fade"></div>
    </div>
    <div class="builder-gnv2-content" style="display:flex; min-height:100vh; flex-direction:column; justify-content:space-between; padding-block:64px 32px;">
        <div>
            <p style="max-width: 720px; margin: 0 auto; opacity: .9;">
                {{ $invitation->closing_text ?: $invitation->footer_text ?: 'Merupakan suatu kebahagiaan dan kehormatan bagi kami apabila Anda berkenan hadir dan memberikan doa restu kepada kedua mempelai.' }}
            </p>
            <div style="margin-top: 18px; font-family: var(--builder-heading-font); font-size: clamp(28px, 6vw, 48px);">
                {{ $invitation->groom_name ?: $invitation->host_name ?: 'Keluarga Besar' }}
                @if ($invitation->bride_name)
                    <span style="opacity: 0.5;">&amp;</span> {{ $invitation->bride_name }}
                @endif
            </div>
            <p style="margin-top: 8px; opacity: .8;">{{ optional($invitation->event_date)->translatedFormat('d F Y') }}</p>
        </div>
        <div style="font-size: 12px; opacity: .6;">Music</div>
    </div>
</section>
@else
<section class="builder-section">
    <div class="builder-card" style="padding: 26px 28px; text-align: center;">
        <div class="builder-kicker">Terima Kasih</div>
        <p style="max-width: 720px; margin: 0 auto; color: rgba(43,31,36,0.78);">
            {{ $invitation->closing_text ?: $invitation->footer_text ?: 'Terima kasih atas doa, waktu, dan perhatian yang Anda berikan untuk hari istimewa kami.' }}
        </p>
        <div style="margin-top: 18px; font-family: var(--builder-heading-font); font-size: 30px;">
            {{ $invitation->groom_name ?: $invitation->host_name ?: 'Keluarga Besar' }}
            @if ($invitation->bride_name)
                <span style="opacity: 0.5;">&amp;</span> {{ $invitation->bride_name }}
            @endif
        </div>
    </div>
</section>
@endif
