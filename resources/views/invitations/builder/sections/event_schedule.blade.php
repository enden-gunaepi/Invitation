@php
    $events = $invitation->events->isNotEmpty()
        ? $invitation->events
        : collect([
            (object) [
                'event_name' => $invitation->title,
                'event_description' => null,
                'event_date' => $invitation->event_date,
                'event_time' => $invitation->event_time,
                'event_end_time' => $invitation->event_end_time,
                'venue_name' => $invitation->venue_name,
                'venue_address' => $invitation->venue_address,
                'venue_maps_url' => $invitation->google_maps_url,
            ],
        ]);
@endphp
@if ($events->isNotEmpty())
@if (!empty($isGnv2))
<section class="builder-section builder-gnv2-panel builder-gnv2-section-blue">
    <div class="builder-gnv2-ornament" style="left: -20px; top: -20px; border-color: rgba(255,255,255,0.16);"></div>
    <div class="builder-gnv2-ornament" style="right: -20px; bottom: -20px; border-color: rgba(255,255,255,0.16);"></div>
    <div class="builder-gnv2-content">
        <div class="builder-kicker" style="color:#ffffff;">Save The Date</div>
        <h2 class="builder-heading" style="font-size: clamp(34px, 7vw, 58px); color:#fff;">{{ optional($invitation->event_date)->translatedFormat('d F Y') }}</h2>
        <div class="builder-grid builder-gnv2-grid-2" style="margin-top: 28px;">
            @foreach ($events as $event)
                <article class="builder-card" style="padding: 24px; text-align:left; color:#fff;">
                    <div style="font-size:12px; text-transform:uppercase; letter-spacing:.14em; opacity:.82;">{{ optional($event->event_date)->translatedFormat('l') }}</div>
                    <h3 class="builder-heading" style="font-size: 30px; margin-top: 12px; color:#fff;">{{ $event->event_name }}</h3>
                    <p style="margin:10px 0 0; font-weight:700;">{{ \Carbon\Carbon::parse($event->event_time)->format('H:i') }} @if(!empty($event->event_end_time)) - {{ \Carbon\Carbon::parse($event->event_end_time)->format('H:i') }} @endif WIB</p>
                    <p style="margin:10px 0 0;">{{ $event->venue_name }}</p>
                    <p style="margin:6px 0 0; opacity:0.88;">{{ $event->venue_address }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>
@else
<section class="builder-section">
    <div class="builder-card" style="padding: 28px;">
        <div class="builder-kicker">Rangkaian Acara</div>
        <h2 class="builder-heading" style="font-size: clamp(28px, 5vw, 44px);">Simpan tanggalnya</h2>
        <div class="builder-grid builder-grid-2" style="margin-top: 24px;">
            @foreach ($events as $event)
                <article style="padding: 22px; border-radius: 24px; background: rgba(255, 248, 243, 0.88); border: 1px solid rgba(122, 78, 87, 0.1);">
                    <div style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.14em; color: var(--builder-accent); font-weight: 700;">{{ optional($event->event_date)->translatedFormat('l, d F Y') }}</div>
                    <h3 class="builder-heading" style="font-size: 28px; margin-top: 12px;">{{ $event->event_name }}</h3>
                    <p style="margin: 12px 0 0; font-weight: 700;">{{ \Carbon\Carbon::parse($event->event_time)->format('H:i') }} @if(!empty($event->event_end_time)) - {{ \Carbon\Carbon::parse($event->event_end_time)->format('H:i') }} @endif WIB</p>
                    <p style="margin: 12px 0 0;">{{ $event->venue_name }}</p>
                    <p style="margin: 6px 0 0; color: rgba(43,31,36,0.75);">{{ $event->venue_address }}</p>
                    @if (!empty($event->event_description))
                        <p style="margin: 12px 0 0; color: rgba(43,31,36,0.75);">{{ $event->event_description }}</p>
                    @endif
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif
@endif
