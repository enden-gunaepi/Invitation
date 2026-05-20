@php
    $groomPhoto = $assetUrl($invitation->groom_photo ?: $invitation->template?->thumbnail);
    $bridePhoto = $assetUrl($invitation->bride_photo ?: $invitation->template?->thumbnail);
@endphp
@if (!empty($isGnv2))
    @foreach ([
        ['name' => $invitation->bride_name ?: 'Mempelai Wanita', 'parent' => $invitation->bride_parent_name, 'photo' => $bridePhoto, 'social' => $invitation->bride_instagram],
        ['name' => $invitation->groom_name ?: 'Mempelai Pria', 'parent' => $invitation->groom_parent_name, 'photo' => $groomPhoto, 'social' => $invitation->groom_instagram],
    ] as $index => $person)
        <section class="builder-section builder-gnv2-panel">
            <div class="builder-gnv2-frame">
                @if ($person['photo'] ?: $assetUrl($invitation->cover_photo ?: $invitation->template?->thumbnail))
                    <img src="{{ $person['photo'] ?: $assetUrl($invitation->cover_photo ?: $invitation->template?->thumbnail) }}" alt="{{ $person['name'] }}" class="builder-gnv2-image">
                @endif
                <div class="{{ $index === 1 ? 'builder-gnv2-overlay-blue' : 'builder-gnv2-overlay-dark' }}"></div>
                <div class="builder-gnv2-bottom-fade"></div>
                <div class="builder-gnv2-ornament" style="left: -18px; top: -18px;"></div>
                <div class="builder-gnv2-ornament" style="right: -18px; bottom: -18px;"></div>
            </div>
            <div class="builder-gnv2-content">
                <h2 class="builder-heading" style="font-size: clamp(36px, 7vw, 60px); color:#fff;">{{ $person['name'] }}</h2>
                @if ($person['parent'])
                    <p style="margin: 12px auto 0; max-width: 520px; opacity:0.88;">{{ $person['parent'] }}</p>
                @endif
                @if ($person['social'])
                    <div class="builder-actions" style="justify-content:center;">
                        <a href="{{ $person['social'] }}" target="_blank" class="builder-button" style="background:rgba(255,255,255,0.2); color:#fff;">@instagram</a>
                    </div>
                @endif
            </div>
        </section>
    @endforeach
@else
<section class="builder-section">
    <div class="builder-card" style="padding: 28px;">
        <div class="builder-kicker">Mempelai</div>
        <h2 class="builder-heading" style="font-size: clamp(30px, 5vw, 48px);">Perjalanan kami menuju hari istimewa</h2>
        <div class="builder-grid builder-grid-2" style="margin-top: 26px;">
            <article style="padding: 20px; border-radius: 24px; background: rgba(183, 110, 121, 0.06);">
                @if ($groomPhoto)
                    <img src="{{ $groomPhoto }}" alt="{{ $invitation->groom_name }}" style="width: 100%; aspect-ratio: 4/5; object-fit: cover; border-radius: 22px; margin-bottom: 18px;">
                @endif
                <h3 class="builder-heading" style="font-size: 30px;">{{ $invitation->groom_name ?: 'Mempelai Pria' }}</h3>
                @if ($invitation->groom_parent_name)
                    <p style="margin: 8px 0 0; color: rgba(43,31,36,0.75);">{{ $invitation->groom_parent_name }}</p>
                @endif
            </article>
            <article style="padding: 20px; border-radius: 24px; background: rgba(122, 78, 87, 0.06);">
                @if ($bridePhoto)
                    <img src="{{ $bridePhoto }}" alt="{{ $invitation->bride_name }}" style="width: 100%; aspect-ratio: 4/5; object-fit: cover; border-radius: 22px; margin-bottom: 18px;">
                @endif
                <h3 class="builder-heading" style="font-size: 30px;">{{ $invitation->bride_name ?: 'Mempelai Wanita' }}</h3>
                @if ($invitation->bride_parent_name)
                    <p style="margin: 8px 0 0; color: rgba(43,31,36,0.75);">{{ $invitation->bride_parent_name }}</p>
                @endif
            </article>
        </div>
    </div>
</section>
@endif
