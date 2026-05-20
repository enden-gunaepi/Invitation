@php
    $photos = $invitation->photos;
    if ($photos->isEmpty()) {
        $fallbackPhotos = collect([$invitation->cover_photo, $invitation->groom_photo, $invitation->bride_photo])
            ->filter()
            ->values();
        $photos = $fallbackPhotos->map(fn ($path) => (object) ['file_path' => $path, 'caption' => null]);
    }
@endphp
@if ($photos->isNotEmpty())
@if (!empty($isGnv2))
<section class="builder-section builder-gnv2-section-blue" style="padding: 80px 20px;">
    <div class="builder-container">
        <div class="builder-kicker" style="color:#ffffff;">Gallery</div>
        <h2 class="builder-heading" style="font-size: clamp(34px, 7vw, 58px); color:#fff;">Momen Kami</h2>
        <div class="builder-grid builder-grid-3" style="margin-top: 28px;">
            @foreach ($photos as $photo)
                @php $photoUrl = $assetUrl($photo->file_path ?? null); @endphp
                @continue(empty($photoUrl))
                <figure style="margin:0; overflow:hidden; border-radius:24px;">
                    <img src="{{ $photoUrl }}" alt="{{ $photo->caption ?? 'Galeri undangan' }}" style="width:100%; height:100%; object-fit:cover; aspect-ratio: 3 / 4;">
                </figure>
            @endforeach
        </div>
    </div>
</section>
@else
<section class="builder-section">
    <div class="builder-card" style="padding: 28px;">
        <div class="builder-kicker">Galeri</div>
        <h2 class="builder-heading" style="font-size: clamp(28px, 5vw, 44px);">Fragmen yang ingin kami kenang</h2>
        <div class="builder-grid builder-grid-3" style="margin-top: 24px;">
            @foreach ($photos as $photo)
                @php $photoUrl = $assetUrl($photo->file_path ?? null); @endphp
                @continue(empty($photoUrl))
                <figure style="margin: 0; overflow: hidden; border-radius: 24px; background: rgba(255,255,255,0.6); min-height: 220px;">
                    <img src="{{ $photoUrl }}" alt="{{ $photo->caption ?? 'Galeri undangan' }}" style="width: 100%; height: 100%; object-fit: cover; aspect-ratio: 1 / 1;">
                </figure>
            @endforeach
        </div>
    </div>
</section>
@endif
@endif
