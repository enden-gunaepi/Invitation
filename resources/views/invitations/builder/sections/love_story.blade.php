@if ($invitation->loveStories->isNotEmpty())
@if (!empty($isGnv2))
<section class="builder-section builder-gnv2-panel">
    <div class="builder-gnv2-frame">
        @if ($assetUrl($invitation->cover_photo ?: $invitation->template?->thumbnail))
            <img src="{{ $assetUrl($invitation->cover_photo ?: $invitation->template?->thumbnail) }}" alt="Timeline" class="builder-gnv2-image">
        @endif
        <div class="builder-gnv2-overlay-dark"></div>
        <div class="builder-gnv2-bottom-fade"></div>
    </div>
    <div class="builder-gnv2-content" style="width:min(100%, 920px);">
        <div class="builder-kicker">Love Story</div>
        <h2 class="builder-heading" style="font-size: clamp(34px, 7vw, 58px); color:#fff;">Jejak langkah kami</h2>
        <div class="builder-grid" style="margin-top: 28px; text-align:left;">
            @foreach ($invitation->loveStories as $story)
                <article class="builder-card" style="padding:22px; display:grid; gap:18px; {{ $story->photo_path ? 'grid-template-columns:minmax(0,180px) minmax(0,1fr); align-items:center;' : '' }}">
                    @if ($story->photo_path)
                        <img src="{{ $assetUrl($story->photo_path) }}" alt="{{ $story->title }}" style="width:100%; aspect-ratio:1/1; object-fit:cover; border-radius:18px;">
                    @endif
                    <div>
                        @if ($story->year)
                            <div style="font-size:12px; letter-spacing:.14em; text-transform:uppercase; opacity:.82;">{{ $story->year }}</div>
                        @endif
                        <h3 class="builder-heading" style="font-size: 30px; color:#fff; margin-top:8px;">{{ $story->title }}</h3>
                        <p style="margin:10px 0 0; opacity:.9;">{{ $story->description }}</p>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@else
<section class="builder-section">
    <div class="builder-card" style="padding: 28px;">
        <div class="builder-kicker">Cerita Kami</div>
        <h2 class="builder-heading" style="font-size: clamp(28px, 5vw, 44px);">Jejak langkah menuju hari ini</h2>
        <div class="builder-grid" style="margin-top: 24px;">
            @foreach ($invitation->loveStories as $story)
                @php $storyPhoto = $assetUrl($story->photo_path ?? null); @endphp
                <article style="display:grid; gap:18px; padding:20px; border-radius:24px; background: rgba(255,255,255,0.72); border:1px solid rgba(122,78,87,0.08); {{ $storyPhoto ? 'grid-template-columns: minmax(0, 180px) minmax(0, 1fr); align-items:center;' : '' }}">
                    @if ($storyPhoto)
                        <img src="{{ $storyPhoto }}" alt="{{ $story->title }}" style="width: 100%; aspect-ratio: 1 / 1; object-fit: cover; border-radius: 20px;">
                    @endif
                    <div>
                        @if ($story->year)
                            <div style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.14em; color: var(--builder-accent); font-weight: 700;">{{ $story->year }}</div>
                        @endif
                        <h3 class="builder-heading" style="font-size: 28px; margin-top: 8px;">{{ $story->title }}</h3>
                        @if ($story->description)
                            <p style="margin: 10px 0 0; color: rgba(43,31,36,0.78);">{{ $story->description }}</p>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif
@endif
