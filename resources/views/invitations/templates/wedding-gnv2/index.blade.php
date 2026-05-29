<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $invitation->title }} - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Inter:wght@300;400;500&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <style>
        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', sans-serif;
        }

        section {
            scroll-snap-align: start;
            scroll-snap-stop: always;
        }

        .font-title {
            font-family: 'Playfair Display', serif;
        }

        .glass-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.24), rgba(255, 255, 255, 0.08));
            border: 1px solid rgba(255, 255, 255, 0.28);
            backdrop-filter: blur(12px);
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.28);
        }

        .reveal-section {
            opacity: 0;
            filter: blur(6px);
            transition: opacity .9s ease, transform .9s cubic-bezier(.22, .61, .36, 1), filter .9s ease;
            will-change: transform, opacity, filter;
        }

        .reveal-section.in-view {
            opacity: 1;
            transform: none !important;
            filter: blur(0);
        }

        .anim-fade-up {
            transform: translateY(40px);
        }

        .anim-fade-up-subtle {
            transform: translateY(20px);
        }

        .anim-slide-left {
            transform: translateX(-56px);
        }

        .anim-slide-right {
            transform: translateX(56px);
        }

        .anim-zoom-in {
            transform: scale(.9);
        }

        .anim-pop-in {
            transform: translateY(14px) scale(.84);
        }

        .anim-flip {
            transform: perspective(900px) rotateY(14deg) scale(.94);
            transform-origin: center;
        }

        .reveal-item {
            opacity: 0;
            filter: blur(4px);
            transition: opacity .8s ease, transform .8s cubic-bezier(.22, .61, .36, 1), filter .8s ease;
            transition-delay: var(--delay, 0ms);
            will-change: transform, opacity, filter;
        }

        .reveal-item.in-view {
            opacity: 1;
            transform: none !important;
            filter: blur(0);
        }

        .asset-soft {
            transition: transform .7s ease, filter .7s ease, opacity .7s ease;
        }

        .asset-soft:hover {
            transform: scale(1.02);
            filter: saturate(1.08);
        }

        .pager-btn {
            border: 1px solid rgba(255, 255, 255, 0.4);
            background: rgba(0, 0, 0, 0.35);
            color: #fff;
            border-radius: 999px;
            padding: 6px 12px;
            font-size: 12px;
            transition: background .2s ease, opacity .2s ease;
        }

        .pager-btn:hover {
            background: rgba(0, 0, 0, 0.55);
        }

        .pager-btn:disabled {
            opacity: .45;
            cursor: not-allowed;
        }

        .message-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.22), rgba(255, 255, 255, 0.12));
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 14px;
            padding: 12px 14px;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.18);
        }

        .message-author {
            font-family: 'Playfair Display', serif;
            font-size: .88rem;
            letter-spacing: .02em;
            line-height: 1.2;
            margin-bottom: 2px;
        }

        .message-meta {
            font-size: .7rem;
            opacity: .85;
        }

        .message-body {
            margin-top: 6px;
            font-size: .82rem;
            line-height: 1.45;
            opacity: .92;
        }

        .wish-form {
            text-align: left;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.08), rgba(255, 255, 255, 0.03));
            border: 1px solid rgba(255, 255, 255, 0.22);
            border-radius: 16px;
            padding: 14px 14px 12px;
            backdrop-filter: blur(8px);
        }

        .wish-label {
            font-family: 'Playfair Display', serif;
            font-size: 1.12rem;
            line-height: 1;
            margin-bottom: 8px;
            opacity: .96;
        }

        .wish-line-input,
        .wish-line-textarea {
            width: 100%;
            border: 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.58);
            background: transparent;
            color: #fff;
            padding: 8px 2px 9px;
            outline: none;
            border-radius: 0;
        }

        .wish-line-input::placeholder,
        .wish-line-textarea::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .wish-line-input:focus,
        .wish-line-textarea:focus {
            border-bottom-color: rgba(255, 255, 255, 0.95);
        }

        .wish-line-textarea {
            min-height: 90px;
            resize: vertical;
        }

        .wish-submit {
            width: 100%;
            margin-top: 14px;
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 999px;
            padding: 12px 16px;
            font-weight: 500;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #fff;
            transition: all 0.3s ease;
        }

        .wish-submit:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        .wish-select {
            width: 100%;
            border: 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.58);
            background: transparent;
            color: #fff;
            padding: 8px 2px 9px;
            outline: none;
            border-radius: 0;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
        }

        .wish-select option {
            color: #111827;
            background: #ffffff;
        }

        .bg-zoom {
            animation: bgZoom 18s ease-in-out infinite alternate;
        }

        .ornament-float {
            animation: ornamentFloat 6s ease-in-out infinite;
        }

        @keyframes bgZoom {
            from {
                transform: scale(1);
            }

            to {
                transform: scale(1.06);
            }
        }

        @keyframes ornamentFloat {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-8px);
            }
        }

        #map {
            width: 100%;
            height: 280px;
            border-radius: 1rem;
            z-index: 1;
            background: transparent;
            position: relative;
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        #mapWrapper iframe {
            width: 100%;
            height: 280px;
            border-radius: 1rem;
        }

        .toast-notification {
            position: fixed;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%) translateY(100px);
            opacity: 0;
            visibility: hidden;
            background: rgba(0, 0, 0, 0.85);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 9999px;
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            z-index: 9999;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            font-size: 0.875rem;
            pointer-events: none;
        }

        .toast-notification.show {
            transform: translateX(-50%) translateY(0);
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }

        .loading-spinner {
            display: none;
            width: 1.25rem;
            height: 1.25rem;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        button:disabled .loading-spinner {
            display: inline-block;
        }

        button:disabled .btn-text {
            display: none;
        }

        @keyframes pulse-glow {

            0%,
            100% {
                box-shadow: 0 0 0 0px rgba(159, 191, 214, 0.5);
                border-color: rgba(159, 191, 214, 0.4);
            }

            50% {
                box-shadow: 0 0 0 10px rgba(159, 191, 214, 0);
                border-color: rgba(159, 191, 214, 1);
            }
        }

        .pulse-glow-active {
            animation: pulse-glow 2s infinite !important;
            border-color: #9fbfd6 !important;
        }

        .pulse-glow-active svg {
            color: #9fbfd6 !important;
        }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
</head>

<body class="bg-black text-white overflow-x-hidden">
    @php
        $guestName = $guest->name ?? 'Nama Tamu';
        $coupleName = trim(
            ($invitation->bride_name ?? 'Mempelai Wanita') . ' & ' . ($invitation->groom_name ?? 'Mempelai Pria'),
        );
        $coverImage = $invitation->cover_photo ? asset('storage/' . $invitation->cover_photo) : null;
        $slideshowImages = [];
        if ($invitation->cover_photo) {
            $slideshowImages[] = asset('storage/' . $invitation->cover_photo);
        }
        foreach ($invitation->photos as $photo) {
            $slideshowImages[] = asset('storage/' . $photo->file_path);
        }
        $slideshowImages = array_values(array_unique($slideshowImages));

        $groomPhoto = $invitation->groom_photo ? asset('storage/' . $invitation->groom_photo) : null;
        $bridePhoto = $invitation->bride_photo ? asset('storage/' . $invitation->bride_photo) : null;
        $eventDateText = $invitation->event_date ? $invitation->event_date->translatedFormat('d.m.y') : '-';
        $eventDateIso = $invitation->event_date ? $invitation->event_date->format('Y-m-d') : null;
        $eventTimeIso = $invitation->event_time
            ? \Carbon\Carbon::parse($invitation->event_time)->format('H:i:s')
            : '00:00:00';
        $openingText =
            $invitation->opening_text ?:
            'Dan di antara tanda-tanda kebesaran-Nya ialah Dia menciptakan untukmu pasangan hidup dari jenismu sendiri, agar kamu cenderung dan merasa tenteram kepadanya.';
        $mapsEmbed =
            $invitation->venue_lat && $invitation->venue_lng
                ? 'https://www.google.com/maps?q=' .
                    $invitation->venue_lat .
                    ',' .
                    $invitation->venue_lng .
                    '&z=15&output=embed'
                : ($invitation->google_maps_url
                    ? 'https://www.google.com/maps?output=embed&q=' . urlencode($invitation->google_maps_url)
                    : 'https://www.google.com/maps?output=embed&q=' .
                        urlencode(trim((string) ($invitation->venue_name . ' ' . $invitation->venue_address))));
        $mapsUrl =
            $invitation->google_maps_url ?:
            ($invitation->venue_lat && $invitation->venue_lng
                ? 'https://www.google.com/maps?q=' . $invitation->venue_lat . ',' . $invitation->venue_lng
                : null);
    @endphp

    <section id="cover"
        class="relative h-screen w-full flex items-end justify-center text-center overflow-hidden transition-opacity duration-700">
        <div class="absolute inset-0">
            @if (count($slideshowImages) > 0)
                <div class="absolute inset-0 w-full h-full overflow-hidden bg-slideshow">
                    @foreach ($slideshowImages as $index => $imgUrl)
                        <img src="{{ $imgUrl }}"
                            class="absolute inset-0 w-full h-full object-cover bg-zoom transition-opacity duration-[2000ms] ease-in-out {{ $index === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0' }}"
                            alt="Cover {{ $index + 1 }}">
                    @endforeach
                </div>
            @else
                <div class="w-full h-full bg-gradient-to-b from-slate-900 to-black"></div>
            @endif
            <div class="absolute inset-0 bg-black/35 z-20"></div>
            <div class="absolute bottom-0 w-full h-1/2 bg-gradient-to-t from-black/85 via-black/50 to-transparent z-30">
            </div>
        </div>
        <div class="absolute bottom-0 left-0 w-28 opacity-30"><img src="ornament.png" class="ornament-float"
                alt=""></div>
        <div class="absolute bottom-0 right-0 w-28 opacity-30 rotate-180"><img src="ornament.png" class="ornament-float"
                alt=""></div>
        <div class="relative z-40 pb-16 px-6">
            <h1 class="font-title text-xl md:text-2xl mb-3 tracking-wide">{{ $coupleName }}</h1>
            <p class="text-sm mb-6 opacity-80">{{ $eventDateText }}</p>
            <p class="text-sm opacity-80">Kepada Yth.</p>
            <h2 class="text-md font-medium mb-6">{{ $guestName }}</h2>
            <button onclick="openInvitation()"
                class="bg-transparent border border-white/40 backdrop-blur-md px-8 py-2.5 rounded-full hover:bg-white/20 transition text-xs uppercase tracking-widest">Open
                Invitation</button>
        </div>
    </section>

    <section id="mainContent" class="hidden">
        <section class="relative min-h-screen flex items-end justify-center text-center text-white overflow-hidden">
            <div class="absolute inset-0">
                @if (count($slideshowImages) > 0)
                    <div class="absolute inset-0 w-full h-full overflow-hidden bg-slideshow">
                        @foreach ($slideshowImages as $index => $imgUrl)
                            <img src="{{ $imgUrl }}"
                                class="absolute inset-0 w-full h-full object-cover bg-zoom transition-opacity duration-[2000ms] ease-in-out {{ $index === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0' }}"
                                alt="Pembuka {{ $index + 1 }}">
                        @endforeach
                    </div>
                @else
                    <div class="w-full h-full bg-gradient-to-b from-slate-800 to-black"></div>
                @endif
                <div class="absolute inset-0 bg-black/35 z-20"></div>
                <div
                    class="absolute bottom-0 w-full h-2/3 bg-gradient-to-t from-black/85 via-black/45 to-transparent z-30">
                </div>
            </div>
            <div class="absolute bottom-0 left-0 w-40 opacity-30"><img src="ornament.png" class="ornament-float"
                    alt=""></div>
            <div class="absolute bottom-0 right-0 w-40 opacity-30 rotate-180"><img src="ornament.png"
                    class="ornament-float" alt="">
            </div>
            <div class="relative z-40 pb-20 px-6">
                <h1 class="font-title text-xl md:text-2xl mb-4">{{ $coupleName }}</h1>
                <p class="text-sm opacity-80 mb-6">{{ $eventDateText }}</p>
                <p class="text-sm opacity-80">Kepada Yth.</p>
                <h2 class="text-lg font-medium mt-1">{{ $guestName }}</h2>
            </div>
        </section>

        <section class="relative min-h-screen flex items-end justify-center text-center text-white overflow-hidden">
            <div class="absolute inset-0">
                @if (count($slideshowImages) > 0)
                    <div class="absolute inset-0 w-full h-full overflow-hidden bg-slideshow">
                        @foreach ($slideshowImages as $index => $imgUrl)
                            <img src="{{ $imgUrl }}"
                                class="absolute inset-0 w-full h-full object-cover bg-zoom transition-opacity duration-[2000ms] ease-in-out {{ $index === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0' }}"
                                alt="Pembuka {{ $index + 1 }}">
                        @endforeach
                    </div>
                @else
                    <div class="w-full h-full bg-gradient-to-b from-slate-800 to-black"></div>
                @endif
                <div class="absolute inset-0 bg-black/30 z-20"></div>
                <div
                    class="absolute bottom-0 w-full h-2/3 bg-gradient-to-t from-black/75 via-black/30 to-transparent z-30">
                </div>
            </div>
            <div class="absolute bottom-0 left-0 w-40 opacity-30"><img src="ornament.png" class="ornament-float"
                    alt=""></div>
            <div class="absolute bottom-0 right-0 w-40 opacity-30 rotate-180"><img src="ornament.png"
                    class="ornament-float" alt="">
            </div>

            <div class="relative z-40 px-6 pb-14 w-full max-w-3xl">


                <p class="text-sm md:text-base leading-relaxed opacity-95">{!! nl2br(e($openingText)) !!}</p>
                <p class="text-xs mt-5 opacity-80 tracking-wide">QS. Ar-Rum: 21</p>

            </div>
        </section>

        <section class="relative min-h-screen flex items-center justify-center text-white text-center overflow-hidden">
            <div class="absolute inset-0">
                @if ($bridePhoto)
                    <img src="{{ $bridePhoto }}" class="w-full h-full object-cover bg-zoom" alt="Bride">
                @elseif($coverImage)
                    <img src="{{ $coverImage }}" class="w-full h-full object-cover bg-zoom" alt="Cover">
                @else
                    <div class="w-full h-full bg-gradient-to-b from-slate-900 to-black"></div>
                @endif
                <div class="absolute inset-0 bg-black/30"></div>
                <div class="absolute bottom-0 w-full h-1/2 bg-gradient-to-t from-black/75 via-black/20 to-transparent">
                </div>
            </div>
            <div class="absolute top-0 left-0 w-32 opacity-30"><img src="ornament.png" class="ornament-float"
                    alt=""></div>
            <div class="absolute top-0 right-0 w-32 opacity-30 rotate-180"><img src="ornament.png"
                    class="ornament-float" alt=""></div>
            <div class="absolute bottom-0 left-0 w-40 opacity-30"><img src="ornament.png" class="ornament-float"
                    alt=""></div>
            <div class="absolute bottom-0 right-0 w-40 opacity-30 rotate-180"><img src="ornament.png"
                    class="ornament-float" alt="">
            </div>
            <div class="relative z-10 px-6 max-w-xl">
                <h2 class="font-title text-xl md:text-2xl mb-3">{{ $invitation->bride_name ?? '-' }}</h2>
                <p class="text-sm opacity-90 mb-6 leading-relaxed">{{ $invitation->bride_parent_name ?? '-' }}</p>
                @if ($invitation->bride_instagram)
                    <a href="{{ $invitation->bride_instagram }}" target="_blank"
                        class="inline-block bg-transparent border border-white/40 backdrop-blur-md px-6 py-2 rounded-full mb-8 hover:bg-white/20 transition text-[10px] uppercase tracking-widest">@instagram</a>
                @endif

            </div>
        </section>

        <section class="relative min-h-screen flex items-center justify-center text-white text-center overflow-hidden">
            <div class="absolute inset-0">
                @if ($groomPhoto)
                    <img src="{{ $groomPhoto }}" class="w-full h-full object-cover bg-zoom" alt="Groom">
                @elseif($coverImage)
                    <img src="{{ $coverImage }}" class="w-full h-full object-cover bg-zoom" alt="Cover">
                @else
                    <div class="w-full h-full bg-gradient-to-b from-slate-900 to-black"></div>
                @endif
                <div class="absolute inset-0 bg-[#9fbfd6]/30"></div>
                <div
                    class="absolute bottom-0 w-full h-1/2 bg-gradient-to-t from-[#9fbfd6]/100 via-[#9fbfd6]/40 to-transparent">
                </div>
            </div>
            <div class="absolute top-0 left-0 w-32 opacity-30"><img src="ornament.png" class="ornament-float"
                    alt=""></div>
            <div class="absolute top-0 right-0 w-32 opacity-30 rotate-180"><img src="ornament.png"
                    class="ornament-float" alt=""></div>
            <div class="absolute bottom-0 left-0 w-40 opacity-30"><img src="ornament.png" class="ornament-float"
                    alt=""></div>
            <div class="absolute bottom-0 right-0 w-40 opacity-30 rotate-180"><img src="ornament.png"
                    class="ornament-float" alt="">
            </div>
            <div class="relative z-10 px-6 max-w-xl">
                <h2 class="font-title text-xl md:text-2xl mb-3">{{ $invitation->groom_name ?? '-' }}</h2>
                <p class="text-sm opacity-90 mb-6 leading-relaxed">{{ $invitation->groom_parent_name ?? '-' }}</p>
                @if ($invitation->groom_instagram)
                    <a href="{{ $invitation->groom_instagram }}" target="_blank"
                        class="inline-block bg-transparent border border-white/40 backdrop-blur-md px-6 py-2 rounded-full mb-8 hover:bg-white/20 transition text-[10px] uppercase tracking-widest">@instagram</a>
                @endif

            </div>
        </section>

        <section
            class="relative min-h-screen flex items-center justify-center text-center text-white px-6 bg-[#9fbfd6] overflow-hidden">
            <div class="absolute top-0 left-0 w-40 opacity-20"><img src="ornament.png" alt=""></div>
            <div class="absolute top-0 right-0 w-40 opacity-20 rotate-180"><img src="ornament.png" alt="">
            </div>
            <div class="absolute bottom-0 left-0 w-40 opacity-20"><img src="ornament.png" alt=""></div>
            <div class="absolute bottom-0 right-0 w-40 opacity-20 rotate-180"><img src="ornament.png" alt="">
            </div>
            <div class="relative z-10 max-w-xl">
                <h2 class="font-title text-xl mb-2">{{ $coupleName }}</h2>
                <p class="text-sm opacity-80 mb-6">{{ $eventDateText }}</p>
                <h3 class="font-title text-xl md:text-3xl mb-6 ">Menuju Hari Spesial Kami</h3>
                <div class="flex justify-center gap-3 mb-8 flex-wrap">
                    <div class="bg-black/40 px-4 py-3 rounded-lg">
                        <p id="days" class="text-lg font-bold">0</p>
                        <p class="text-xs">Hari</p>
                    </div>
                    <div class="bg-black/40 px-4 py-3 rounded-lg">
                        <p id="hours" class="text-lg font-bold">0</p>
                        <p class="text-xs">Jam</p>
                    </div>
                    <div class="bg-black/40 px-4 py-3 rounded-lg">
                        <p id="minutes" class="text-lg font-bold">0</p>
                        <p class="text-xs">Menit</p>
                    </div>
                    <div class="bg-black/40 px-4 py-3 rounded-lg">
                        <p id="seconds" class="text-lg font-bold">0</p>
                        <p class="text-xs">Detik</p>
                    </div>
                </div>

            </div>
        </section>

        {{-- <section
            class="relative min-h-screen flex items-center justify-center text-center text-white px-6 bg-[#9fbfd6] overflow-hidden">
            <div class="absolute top-0 left-0 w-40 opacity-20"><img src="ornament.png" alt=""></div>
            <div class="absolute top-0 right-0 w-40 opacity-20 rotate-180"><img src="ornament.png" alt="">
            </div>
            <div class="absolute bottom-0 left-0 w-40 opacity-20"><img src="ornament.png" alt=""></div>
            <div class="absolute bottom-0 right-0 w-40 opacity-20 rotate-180"><img src="ornament.png" alt="">
            </div>
            <div class="relative z-10 max-w-xl">
                <h2 class="font-title text-2xl mb-2">{{ $coupleName }}</h2>
                <p class="text-sm opacity-80 mb-8">{{ $eventDateText }}</p>
                @forelse($invitation->events as $event)
                    <h3 class="font-title text-xl mb-2">{{ $event->event_name }}</h3>
                    <p class="text-sm mb-1">
                        {{ $event->event_date ? \Carbon\Carbon::parse($event->event_date)->translatedFormat('l, d F Y') : $eventDateText }}
                    </p>
                    <p class="text-sm mb-3">Pukul
                        {{ $event->event_time ? \Carbon\Carbon::parse($event->event_time)->format('H:i') : '-' }} WIB
                    </p>
                    @if ($event->event_description)
                        <p class="text-sm opacity-90 leading-relaxed mb-6">{{ $event->event_description }}</p>
                    @endif
                @empty
                    <h3 class="font-title text-xl mb-2">{{ $invitation->venue_name }}</h3>
                    <p class="text-sm mb-1">{{ $eventDateText }}</p>
                    <p class="text-sm mb-6">Pukul
                        {{ $invitation->event_time ? \Carbon\Carbon::parse($invitation->event_time)->format('H:i') : '-' }}
                        WIB</p>
                @endforelse
                <h3 class="font-title text-xl mb-2">{{ $invitation->venue_name }}</h3>
                <p class="text-sm opacity-90 leading-relaxed">{{ $invitation->venue_address }}</p>
            </div>
        </section> --}}

        <section class="relative min-h-screen text-white px-6 py-20 overflow-hidden">
            <div class="absolute inset-0">
                @if (count($slideshowImages) > 0)
                    <div class="absolute inset-0 w-full h-full overflow-hidden bg-slideshow">
                        @foreach ($slideshowImages as $index => $imgUrl)
                            <img src="{{ $imgUrl }}"
                                class="absolute inset-0 w-full h-full object-cover bg-zoom transition-opacity duration-[2000ms] ease-in-out {{ $index === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0' }}"
                                alt="Timeline {{ $index + 1 }}">
                        @endforeach
                    </div>
                @else
                    <div class="w-full h-full bg-gradient-to-b from-slate-900 to-black"></div>
                @endif
                <div class="absolute inset-0 bg-black/35 z-20"></div>
            </div>
            <div class="relative z-40 max-w-5xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="font-title text-2xl md:text-3xl mt-6 tracking-[0.18em]">SUSUNAN ACARA</h2>
                    <div class="mt-5 max-w-xl mx-auto space-y-3">
                        <h1 class="font-title text-xl md:text-2xl">{{ $invitation->venue_name }}</h1>
                        <p class="text-sm md:text-base text-white/80 leading-relaxed">{{ $invitation->venue_address }}
                        </p>
                        <div id="userDistance" class="text-xs text-white/75 hidden">
                            <span id="distanceText">Menghitung jarak...</span>
                        </div>
                    </div>
                </div>
                <div class="max-w-3xl mx-auto space-y-5">
                    @forelse ($invitation->events as $event)
                        @php
                            $eventDate = $event->event_date
                                ? \Carbon\Carbon::parse($event->event_date)
                                : $invitation->event_date;
                            $eventMonth = $eventDate ? $eventDate->translatedFormat('M') : '-';
                            $eventDay = $eventDate ? $eventDate->format('d') : '-';
                            $eventYear = $eventDate ? $eventDate->format('Y') : '-';
                            $eventStart = $event->event_time
                                ? \Carbon\Carbon::parse($event->event_time)->format('H:i')
                                : '-';
                            $eventEnd = $event->event_end_time
                                ? \Carbon\Carbon::parse($event->event_end_time)->format('H:i')
                                : null;
                            $eventVenue = $event->venue_name ?: $invitation->venue_name;
                            $eventAddress = $event->venue_address ?: $invitation->venue_address;
                            $eventMapsUrl = $event->venue_maps_url ?: $mapsUrl;
                            $calendarDate = $eventDate ? $eventDate->format('Ymd') : null;
                            $calendarStart = $event->event_time
                                ? \Carbon\Carbon::parse($event->event_time)->format('His')
                                : '000000';
                            $calendarEnd = $event->event_end_time
                                ? \Carbon\Carbon::parse($event->event_end_time)->format('His')
                                : \Carbon\Carbon::parse($event->event_time ?: '00:00')
                                    ->addHours(2)
                                    ->format('His');
                            $calendarUrl = $calendarDate
                                ? 'https://calendar.google.com/calendar/render?action=TEMPLATE&text=' .
                                    urlencode($event->event_name ?: $invitation->title) .
                                    '&dates=' .
                                    $calendarDate .
                                    'T' .
                                    $calendarStart .
                                    '/' .
                                    $calendarDate .
                                    'T' .
                                    $calendarEnd .
                                    '&location=' .
                                    urlencode(trim(($eventVenue ?: '') . ' ' . ($eventAddress ?: '')))
                                : null;
                        @endphp
                        <article
                            class="rounded-[30px] border border-white/15 bg-black/25 px-6 py-8 text-center backdrop-blur-md shadow-[0_18px_40px_rgba(0,0,0,0.18)]">
                            <h3 class="font-title text-3xl mb-5">{{ $event->event_name }}</h3>
                            <div class="flex items-center justify-center gap-4 mb-5">
                                <div class="min-w-[56px] text-center">
                                    <p class="text-xs uppercase tracking-[0.2em] text-white/60">{{ $eventMonth }}
                                    </p>
                                </div>
                                <div class="h-12 w-px bg-white/30"></div>
                                <div class="min-w-[76px] text-center">
                                    <p class="text-5xl leading-none font-title">{{ $eventDay }}</p>
                                </div>
                                <div class="h-12 w-px bg-white/30"></div>
                                <div class="min-w-[56px] text-center">
                                    <p class="text-xs uppercase tracking-[0.2em] text-white/60">{{ $eventYear }}
                                    </p>
                                </div>
                            </div>
                            <p class="text-base font-medium mb-1">
                                {{ $eventStart }}{{ $eventEnd ? ' - ' . $eventEnd : '' }} WIB
                            </p>
                            @if ($eventVenue)
                                <p class="text-sm text-white/85">{{ $eventVenue }}</p>
                            @endif
                            @if ($eventAddress)
                                <p class="text-sm text-white/70 mt-1">{{ $eventAddress }}</p>
                            @endif
                            @if ($event->event_description)
                                <p class="text-sm text-white/65 leading-relaxed mt-3 max-w-xl mx-auto">
                                    {{ $event->event_description }}</p>
                            @endif

                            <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                                @if ($calendarUrl)
                                    <a href="{{ $calendarUrl }}" target="_blank"
                                        class="inline-flex items-center gap-2 rounded-full bg-white px-5 py-2.5 text-[11px] font-medium tracking-[0.06em] text-slate-900 transition hover:bg-[#dce8f4]">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path
                                                d="M6 2a1 1 0 012 0v1h4V2a1 1 0 112 0v1h1a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h1V2zm9 5H5v7h10V7z" />
                                        </svg>
                                        <span>Save The Date</span>
                                    </a>
                                @endif
                                @if ($eventMapsUrl)
                                    <a href="{{ $eventMapsUrl }}" target="_blank"
                                        class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-[#9fbfd6] px-5 py-2.5 text-[11px] font-medium tracking-[0.06em] text-slate-950 transition hover:bg-[#b8d0e3]">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M12.293 7.293a1 1 0 011.414 0L16 9.586V8a1 1 0 112 0v4a1 1 0 01-1 1h-4a1 1 0 110-2h1.586l-2.293-2.293a1 1 0 010-1.414zM3 5a2 2 0 012-2h4a1 1 0 110 2H5v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H5a2 2 0 01-2-2V5z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <span>Map Navigation</span>
                                    </a>
                                @endif
                            </div>
                        </article>
                    @empty
                        <article
                            class="mx-auto max-w-2xl rounded-[30px] border border-white/15 bg-black/25 px-6 py-8 text-center backdrop-blur-md shadow-[0_18px_40px_rgba(0,0,0,0.18)]">
                            <h3 class="font-title text-xl mb-5">{{ $invitation->venue_name }}</h3>
                            <div class="flex items-center justify-center gap-4 mb-5">
                                <div class="min-w-[56px] text-center">
                                    <p class="text-xs uppercase tracking-[0.2em] text-white/60">
                                        {{ $invitation->event_date ? $invitation->event_date->translatedFormat('M') : '-' }}
                                    </p>
                                </div>
                                <div class="h-12 w-px bg-white/30"></div>
                                <div class="min-w-[76px] text-center">
                                    <p class="text-5xl leading-none font-title">
                                        {{ $invitation->event_date ? $invitation->event_date->format('d') : '-' }}</p>
                                </div>
                                <div class="h-12 w-px bg-white/30"></div>
                                <div class="min-w-[56px] text-center">
                                    <p class="text-xs uppercase tracking-[0.2em] text-white/60">
                                        {{ $invitation->event_date ? $invitation->event_date->format('Y') : '-' }}</p>
                                </div>
                            </div>
                            <p class="text-base font-medium mb-1">
                                {{ $invitation->event_time ? \Carbon\Carbon::parse($invitation->event_time)->format('H:i') : '-' }}
                                WIB</p>
                            <p class="text-sm text-white/70 mt-1">{{ $invitation->venue_address }}</p>
                            <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                                @if ($invitation->google_calendar_url)
                                    <a href="{{ $invitation->google_calendar_url }}" target="_blank"
                                        class="inline-flex items-center gap-2 rounded-full bg-white px-5 py-2.5 text-[11px] font-medium tracking-[0.06em] text-slate-900 transition hover:bg-[#dce8f4]">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path
                                                d="M6 2a1 1 0 012 0v1h4V2a1 1 0 112 0v1h1a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h1V2zm9 5H5v7h10V7z" />
                                        </svg>
                                        <span>Save The Date</span>
                                    </a>
                                @endif
                                @if ($mapsUrl)
                                    <a href="{{ $mapsUrl }}" target="_blank"
                                        class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-[#9fbfd6] px-5 py-2.5 text-[11px] font-medium tracking-[0.06em] text-slate-950 transition hover:bg-[#b8d0e3]">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M12.293 7.293a1 1 0 011.414 0L16 9.586V8a1 1 0 112 0v4a1 1 0 01-1 1h-4a1 1 0 110-2h1.586l-2.293-2.293a1 1 0 010-1.414zM3 5a2 2 0 012-2h4a1 1 0 110 2H5v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H5a2 2 0 01-2-2V5z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <span>Map Navigation</span>
                                    </a>
                                @endif
                            </div>
                        </article>
                    @endforelse
                </div>
            </div>

        </section>



        @if ($invitation->photos->count())
            <section class="relative px-6 py-20 bg-[#9fbfd6] overflow-hidden">
                <div class="absolute top-0 left-0 w-40 opacity-20"><img src="ornament.png" alt=""></div>
                <div class="absolute bottom-0 right-0 w-40 opacity-20 rotate-180"><img src="ornament.png"
                        alt=""></div>
                <div class="max-w-4xl mx-auto relative z-10">
                    <h2 class="font-title text-xl mb-2 text-center text-white">Galeri</h2>
                    <p class="text-sm md:text-base opacity-85 text-center mb-10 text-white italic tracking-wide">Setiap
                        potret menceritakan kisah cinta kami yang abadi</p>

                    <div class="relative px-12 mb-6">
                        <div thumbsSlider="" class="swiper gallery-thumbs">
                            <div class="swiper-wrapper">
                                @foreach ($invitation->photos as $photo)
                                    <div
                                        class="swiper-slide cursor-pointer opacity-60 transition-all duration-300 rounded-lg">
                                        <img src="{{ asset('storage/' . $photo->file_path) }}"
                                            class="w-full h-16 md:h-24 object-cover rounded-lg border-2 border-transparent">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div
                            class="swiper-button-prev !text-white !left-0 after:!text-sm bg-white/20 hover:bg-white/40 backdrop-blur-sm w-8 h-8 rounded-full transition-all">
                        </div>
                        <div
                            class="swiper-button-next !text-white !right-0 after:!text-sm bg-white/20 hover:bg-white/40 backdrop-blur-sm w-8 h-8 rounded-full transition-all">
                        </div>
                    </div>

                    <div class="swiper gallery-main rounded-2xl overflow-hidden shadow-2xl ring-1 ring-white/30">
                        <div class="swiper-wrapper">
                            @foreach ($invitation->photos as $photo)
                                <div class="swiper-slide">
                                    <a href="{{ asset('storage/' . $photo->file_path) }}" data-fancybox="gallery"
                                        data-caption="{{ $photo->caption ?: '' }}">
                                        <img src="{{ asset('storage/' . $photo->file_path) }}"
                                            alt="{{ $photo->caption ?: '' }}"
                                            class="w-full h-[60vh] object-cover hover:scale-105 transition-transform duration-700 cursor-pointer">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>
        @endif

        @if ($invitation->ig_story_photo)
            @php
                $igStoryDate = $invitation->event_date ? $invitation->event_date->format('d · m · Y') : '-';
                $igCoupleName = trim(
                    ($invitation->bride_name ?? 'Mempelai Wanita') .
                        ' & ' .
                        ($invitation->groom_name ?? 'Mempelai Pria'),
                );
            @endphp
            <section
                class="relative py-20 flex items-center justify-center text-center text-white px-6 bg-[#9fbfd6] overflow-hidden">
                <div class="absolute top-0 left-0 w-40 opacity-20"><img src="ornament.png" alt=""></div>
                <div class="absolute top-0 right-0 w-40 opacity-20 rotate-180"><img src="ornament.png"
                        alt=""></div>
                <div class="absolute bottom-0 left-0 w-40 opacity-20"><img src="ornament.png" alt=""></div>
                <div class="absolute bottom-0 right-0 w-40 opacity-20 rotate-180"><img src="ornament.png"
                        alt=""></div>
                <div class="relative z-10 max-w-md w-full">
                    <h2 class="font-title text-xl md:text-3xl mb-2">Instagram Story</h2>
                    <p class="text-sm opacity-80 mb-8">Bagikan momen bahagia kami di Instagram Story kamu</p>
                    <div class="glass-card rounded-2xl p-3 mb-6">
                        <canvas id="igStoryCanvas" class="w-full h-auto rounded-xl shadow-lg"
                            style="aspect-ratio: 9/16;"></canvas>
                    </div>
                    <button type="button" id="downloadIgStory"
                        class="inline-flex items-center gap-2 bg-transparent border border-white/50 backdrop-blur-md px-8 py-2.5 rounded-full hover:bg-white/20 hover:text-white transition text-xs uppercase tracking-widest mt-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                            <polyline points="7 10 12 15 17 10" />
                            <line x1="12" y1="15" x2="12" y2="3" />
                        </svg>
                        Download Template
                    </button>
                </div>
            </section>
            <script>
                (function() {
                    const CANVAS_W = 1080;
                    const CANVAS_H = 1920;
                    const coupleNameText = @json($igCoupleName);
                    const dateText = @json($igStoryDate);
                    const websiteUrl = 'janjisucikita.com';
                    const igHandle = '-';
                    const photoSrc = @json(asset('storage/' . $invitation->ig_story_photo));

                    const scriptFont = new FontFace('GreatVibes',
                        'url(https://fonts.gstatic.com/s/greatvibes/v18/RWmMoKWR9v4ksMfaWd_JN9XFiaQ.woff2)');
                    const sansFont = new FontFace('Inter',
                        'url(https://fonts.gstatic.com/s/inter/v18/UcCO3FwrK3iLTeHuS_nVMrMxCp50SjIw2boKoduKmMEVuLyfAZ9hiA.woff2)'
                    );

                    Promise.all([scriptFont.load(), sansFont.load()]).then(function(fonts) {
                        fonts.forEach(function(f) {
                            document.fonts.add(f);
                        });
                        renderIgStory();
                    }).catch(function() {
                        renderIgStory();
                    });

                    function renderIgStory() {
                        const canvas = document.getElementById('igStoryCanvas');
                        if (!canvas) return;
                        canvas.width = CANVAS_W;
                        canvas.height = CANVAS_H;
                        const ctx = canvas.getContext('2d');

                        const img = new Image();
                        img.crossOrigin = 'anonymous';
                        img.onload = function() {
                            // -- Draw background photo (cover fit) --
                            const imgRatio = img.width / img.height;
                            const canvasRatio = CANVAS_W / CANVAS_H;
                            let sx = 0,
                                sy = 0,
                                sw = img.width,
                                sh = img.height;
                            if (imgRatio > canvasRatio) {
                                sw = img.height * canvasRatio;
                                sx = (img.width - sw) / 2;
                            } else {
                                sh = img.width / canvasRatio;
                                sy = (img.height - sh) / 2;
                            }
                            ctx.drawImage(img, sx, sy, sw, sh, 0, 0, CANVAS_W, CANVAS_H);

                            // -- Bottom gradient overlay --
                            const gradStart = CANVAS_H * 0.45;
                            const grad = ctx.createLinearGradient(0, gradStart, 0, CANVAS_H);
                            grad.addColorStop(0, 'rgba(100, 140, 170, 0)');
                            grad.addColorStop(0.35, 'rgba(80, 125, 155, 0.55)');
                            grad.addColorStop(0.6, 'rgba(65, 110, 140, 0.82)');
                            grad.addColorStop(1, 'rgba(50, 95, 125, 0.95)');
                            ctx.fillStyle = grad;
                            ctx.fillRect(0, gradStart, CANVAS_W, CANVAS_H - gradStart);

                            // -- Couple name (script font) --
                            const nameY = CANVAS_H * 0.66;
                            ctx.textAlign = 'center';
                            ctx.fillStyle = '#ffffff';
                            ctx.font = '700 72px GreatVibes, cursive';
                            ctx.shadowColor = 'rgba(0,0,0,0.4)';
                            ctx.shadowBlur = 8;
                            ctx.fillText(coupleNameText, CANVAS_W / 2, nameY);
                            ctx.shadowBlur = 0;

                            // -- Date --
                            const dateY = nameY + 60;
                            ctx.font = '300 36px Inter, sans-serif';
                            ctx.letterSpacing = '4px';
                            ctx.fillStyle = 'rgba(255,255,255,0.9)';
                            ctx.fillText(dateText, CANVAS_W / 2, dateY);

                            // -- "Wish" label --
                            const wishLabelY = dateY + 56;
                            ctx.font = '400 30px Inter, sans-serif';
                            ctx.fillStyle = 'rgba(255,255,255,0.8)';
                            ctx.fillText('Wish', CANVAS_W / 2, wishLabelY);

                            // -- White/cream box for wish space --
                            const boxMargin = 60;
                            const boxTop = wishLabelY + 24;
                            const boxWidth = CANVAS_W - (boxMargin * 2);
                            const boxHeight = 360;
                            const boxRadius = 16;

                            ctx.fillStyle = 'rgba(245, 240, 232, 0.92)';
                            ctx.beginPath();
                            ctx.moveTo(boxMargin + boxRadius, boxTop);
                            ctx.lineTo(boxMargin + boxWidth - boxRadius, boxTop);
                            ctx.quadraticCurveTo(boxMargin + boxWidth, boxTop, boxMargin + boxWidth, boxTop + boxRadius);
                            ctx.lineTo(boxMargin + boxWidth, boxTop + boxHeight - boxRadius);
                            ctx.quadraticCurveTo(boxMargin + boxWidth, boxTop + boxHeight, boxMargin + boxWidth - boxRadius,
                                boxTop + boxHeight);
                            ctx.lineTo(boxMargin + boxRadius, boxTop + boxHeight);
                            ctx.quadraticCurveTo(boxMargin, boxTop + boxHeight, boxMargin, boxTop + boxHeight - boxRadius);
                            ctx.lineTo(boxMargin, boxTop + boxRadius);
                            ctx.quadraticCurveTo(boxMargin, boxTop, boxMargin + boxRadius, boxTop);
                            ctx.closePath();
                            ctx.fill();

                            // -- Bottom footer: website URL & IG handle --
                            const footerY = CANVAS_H - 60;
                            ctx.font = '400 24px Inter, sans-serif';
                            ctx.fillStyle = 'rgba(255,255,255,0.7)';
                            ctx.textAlign = 'left';
                            ctx.fillText(websiteUrl, boxMargin, footerY);
                            ctx.textAlign = 'right';
                            ctx.fillText(igHandle, CANVAS_W - boxMargin, footerY);
                            ctx.textAlign = 'center';
                        };
                        img.onerror = function() {
                            ctx.fillStyle = '#3c1e14';
                            ctx.fillRect(0, 0, CANVAS_W, CANVAS_H);
                            ctx.fillStyle = '#fff';
                            ctx.font = '400 32px Inter, sans-serif';
                            ctx.textAlign = 'center';
                            ctx.fillText('Gagal memuat gambar', CANVAS_W / 2, CANVAS_H / 2);
                        };
                        img.src = photoSrc;
                    }

                    document.getElementById('downloadIgStory')?.addEventListener('click', function() {
                        const canvas = document.getElementById('igStoryCanvas');
                        if (!canvas) return;
                        const link = document.createElement('a');
                        link.download = 'ig-story-' + coupleNameText.replace(/\s+/g, '-').toLowerCase() + '.png';
                        link.href = canvas.toDataURL('image/png');
                        link.click();
                    });
                })();
            </script>
        @endif

        <section id="rsvp"
            class="relative min-h-screen flex items-center justify-center text-center text-white px-6 bg-[#9fbfd6] overflow-hidden">
            <div class="absolute top-0 left-0 w-40 opacity-20"><img src="ornament.png" alt=""></div>
            <div class="absolute bottom-0 right-0 w-40 opacity-20 rotate-180"><img src="ornament.png" alt="">
            </div>
            <div class="relative z-10 w-full max-w-xl">
                <h2 class="font-title text-xl mb-2">Konfirmasi Kehadiran</h2>
                <p class="text-sm opacity-80 mb-8">Mohon kesediaannya untuk mengisi konfirmasi kehadiran</p>
                <form id="rsvpForm" method="POST" action="{{ route('invitation.rsvp', $invitation->slug) }}"
                    class="space-y-4">
                    @csrf
                    <div class="wish-form">
                        <label class="wish-label" for="rsvpName">Nama :</label>
                        <input id="rsvpName" type="text" name="name" value="{{ $guest->name ?? '' }}"
                            placeholder="Nama Anda" class="wish-line-input" required>

                        <label class="wish-label mt-4 block" for="rsvpPax">Jumlah Tamu :</label>
                        <input id="rsvpPax" type="number" name="pax" min="1" max="10"
                            value="1" placeholder="Jumlah tamu hadir" class="wish-line-input" required>

                        <input type="hidden" name="phone" value="-">

                        <label class="wish-label mt-4 block" for="rsvpStatus">Konfirmasi :</label>
                        <select id="rsvpStatus" name="status" class="wish-select" required>
                            <option value="attending">Hadir</option>
                            <option value="not_attending">Tidak Hadir</option>
                            <option value="maybe">Masih Ragu</option>
                        </select>

                        <label class="wish-label mt-4 block" for="rsvpAddress">Ucapan & Doa :</label>
                        <textarea id="rsvpAddress" name="message" placeholder="Tulis ucapan dan doa untuk mempelai..."
                            class="wish-line-textarea" required></textarea>

                        @if (!empty($guest?->id))
                            <input type="hidden" name="guest_id" value="{{ $guest->id }}">
                        @endif

                        <button type="submit" class="wish-submit flex items-center justify-center gap-2">
                            <span class="btn-text">KIRIM KONFIRMASI &#9992;</span>
                            <div class="loading-spinner"></div>
                        </button>
                    </div>
                </form>
            </div>
        </section>

        <section
            class="relative min-h-screen flex items-center justify-center text-center text-white px-6 overflow-hidden">
            <div class="absolute inset-0">
                @if (count($slideshowImages) > 0)
                    <div class="absolute inset-0 w-full h-full overflow-hidden bg-slideshow">
                        @foreach ($slideshowImages as $index => $imgUrl)
                            <img src="{{ $imgUrl }}"
                                class="absolute inset-0 w-full h-full object-cover bg-zoom transition-opacity duration-[2000ms] ease-in-out {{ $index === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0' }}"
                                alt="Ucapan {{ $index + 1 }}">
                        @endforeach
                    </div>
                @else
                    <div class="w-full h-full bg-gradient-to-b from-slate-900 to-black"></div>
                @endif
                <div class="absolute inset-0 bg-black/35 z-20"></div>
            </div>
            <div class="absolute top-0 left-0 w-40 opacity-20"><img src="ornament.png" alt=""></div>
            <div class="absolute top-0 right-0 w-40 opacity-20 rotate-180"><img src="ornament.png" alt="">
            </div>
            <div class="absolute bottom-0 left-0 w-40 opacity-20"><img src="ornament.png" alt=""></div>
            <div class="absolute bottom-0 right-0 w-40 opacity-20 rotate-180"><img src="ornament.png" alt="">
            </div>
            <div class="relative z-40 w-full max-w-xl">
                <h2 class="font-title text-xl mb-2">Ucapan & Doa</h2>
                <p class="text-sm opacity-80 mb-8">Ucapan dan doa dari tamu undangan</p>
                <div class="bg-white/20 backdrop-blur-lg rounded-3xl p-4 shadow-2xl border border-white/30">
                    <div id="rsvpListContainer" class="max-h-[400px] overflow-y-auto text-left space-y-2 pr-2">
                        @php
                            $statusLabels = [
                                'attending' => 'Hadir',
                                'not_attending' => 'Tidak Hadir',
                                'maybe' => 'Mungkin',
                            ];
                        @endphp
                        @foreach ($invitation->rsvps as $rsvp)
                            <article class="bg-transparent border border-white/30 rounded-lg p-2.5 shadow-md">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex-shrink-0 w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                                        <span
                                            class="text-white text-xs font-semibold">{{ mb_strtoupper(mb_substr($rsvp->name, 0, 1)) }}</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="message-author font-semibold text-sm text-white truncate">
                                            {{ $rsvp->name }}</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        @if ($rsvp->status === 'attending')
                                            <div class="w-5 h-5 rounded-full bg-green-500/30 flex items-center justify-center"
                                                title="Hadir">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-green-400"
                                                    viewBox="0 0 256 256">
                                                    <path d="M0 0h256v256H0z" fill="none" />
                                                    <path fill="currentColor"
                                                        d="M152.5 156.54a72 72 0 1 0-89 0a124 124 0 0 0-48.69 35.74a12 12 0 0 0 18.38 15.44C46.88 191.42 71 172 108 172s61.12 19.42 74.81 35.72a12 12 0 1 0 18.38-15.44a123.9 123.9 0 0 0-48.69-35.74M60 100a48 48 0 1 1 48 48a48.05 48.05 0 0 1-48-48m192.49 36.49l-32 32a12 12 0 0 1-17 0l-16-16a12 12 0 0 1 17-17L212 143l23.51-23.52a12 12 0 1 1 17 17Z" />
                                                </svg>
                                            </div>
                                        @elseif ($rsvp->status === 'not_attending')
                                            <div class="w-5 h-5 rounded-full bg-red-500/30 flex items-center justify-center"
                                                title="Tidak Hadir">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-red-400"
                                                    viewBox="0 0 24 24">
                                                    <path d="M0 0h24v24H0z" fill="none" />
                                                    <path fill="none" stroke="currentColor" stroke-linecap="round"
                                                        stroke-linejoin="round" stroke-width="2"
                                                        d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0-8 0M6 21v-2a4 4 0 0 1 4-4h3.5m2.5 4a3 3 0 1 0 6 0a3 3 0 1 0-6 0m1 2l4-4" />
                                                </svg>
                                            </div>
                                        @else
                                            <div class="w-5 h-5 rounded-full bg-yellow-500/30 flex items-center justify-center"
                                                title="Masih Ragu">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="w-3 h-3 text-yellow-400" viewBox="0 0 512 512">
                                                    <path d="M0 0h512v512H0z" fill="none" />
                                                    <path fill="currentColor" fill-rule="evenodd"
                                                        d="M213.333 42.667c41.238 0 74.667 33.429 74.667 74.666c0 39.863-31.238 72.43-70.57 74.556l-4.097.111c-41.237 0-74.666-33.429-74.666-74.667c0-39.862 31.238-72.43 70.57-74.556zm148.835 384l.499-.499v.499zm-298.168 0h170.667v-42.172l.494-.495H106.667v-34.133l.11-4.142c2.057-38.365 32.515-68.392 69.223-68.392h74.667l3.908.114c22.622 1.322 42.501 14.047 54.242 32.897l30.667-30.667c-20.476-27.372-52.644-45.01-88.817-45.01H176l-4.617.096C111.668 237.253 64 287.834 64 349.867zm192-33.336l9.331-9.331h.002l52.444-52.444l-.001-.001l33.131-33.131l.001.002l8.883-8.884l54.667 54.667L310.667 448H256zm-74.667-275.998c0-17.673 14.327-32 32-32s32 14.327 32 32s-14.327 32-32 32s-32-14.327-32-32m228 122.667L464 294.667l-34.458 34.457l-54.666-54.667z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                @if ($rsvp->message)
                                    <p class="message-body text-sm leading-relaxed text-white pl-11 mt-2">
                                        {{ $rsvp->message }}</p>
                                @endif
                            </article>
                        @endforeach
                        <div class="flex items-center justify-between pt-4 list-rsvp-pager">
                            <button type="button" class="pager-btn" data-prev>Prev</button>
                            <span class="text-xs opacity-90" data-page-info>Hal 1 / 1</span>
                            <button type="button" class="pager-btn" data-next>Next</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @if ($invitation->loveStories->count())
            <section class="relative px-6 py-20 overflow-hidden">
                <div class="absolute inset-0">
                    @if ($invitation->photos->first())
                        <img src="{{ asset('storage/' . $invitation->photos->first()->file_path) }}"
                            alt="Love Story Background" class="w-full h-full object-cover">
                    @elseif($coverImage)
                        <img src="{{ $coverImage }}" alt="Love Story Background"
                            class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-[#9fbfd6]"></div>
                    @endif
                    <div class="absolute inset-0 bg-black/45"></div>
                    <div class="absolute inset-0 bg-gradient-to-b from-white/10 via-black/10 to-black/60"></div>
                </div>
                <div class="absolute top-0 left-0 w-40 opacity-20"><img src="ornament.png" alt=""></div>
                <div class="absolute top-0 right-0 w-40 opacity-20 rotate-180"><img src="ornament.png"
                        alt=""></div>
                <div class="absolute bottom-0 left-0 w-40 opacity-20"><img src="ornament.png" alt=""></div>
                <div class="absolute bottom-0 right-0 w-40 opacity-20 rotate-180"><img src="ornament.png"
                        alt=""></div>
                <div class="max-w-4xl mx-auto relative z-10">
                    <h2 class="font-title text-3xl md:text-4xl mb-3 text-center text-white">Kisah Cinta</h2>
                    <p class="text-sm md:text-base opacity-85 text-center mb-8 text-white tracking-wide">Kisah
                        perjalanan kami menuju hari istimewa</p>

                    <div class="relative pl-12 md:pl-16 space-y-10">
                        <div
                            class="absolute left-4 md:left-6 top-6 bottom-6 w-px border-l border-dashed border-white/50">
                        </div>
                        @foreach ($invitation->loveStories as $story)
                            <article
                                class="relative rounded-[30px] overflow-visible border border-white/20 bg-white/10 backdrop-blur-xl shadow-[0_20px_45px_rgba(15,23,42,0.22)]">
                                <div
                                    class="absolute -left-12 md:-left-14 top-14 flex h-7 w-7 items-center justify-center rounded-full bg-white/90 text-[#5f82b4] shadow-[0_8px_18px_rgba(0,0,0,0.16)]">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24">
                                        <path d="M0 0h24v24H0z" fill="none" />
                                        <g fill="none">
                                            <path
                                                d="m12.594 23.258l-.012.002l-.071.035l-.02.004l-.014-.004l-.071-.036q-.016-.004-.024.006l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427q-.004-.016-.016-.018m.264-.113l-.014.002l-.184.093l-.01.01l-.003.011l.018.43l.005.012l.008.008l.201.092q.019.005.029-.008l.004-.014l-.034-.614q-.005-.019-.02-.022m-.715.002a.02.02 0 0 0-.027.006l-.006.014l-.034.614q.001.018.017.024l.015-.002l.201-.093l.01-.008l.003-.011l.018-.43l-.003-.012l-.01-.01z" />
                                            <path fill="currentColor"
                                                d="M9.498 5.793c1.42-1.904 3.555-2.46 5.519-1.925c2.12.577 3.984 2.398 4.603 4.934q.048.195.083.39a4.45 4.45 0 0 0-2.774-.07c-1.287-.952-2.881-1.112-4.298-.59c-1.775.655-3.161 2.316-3.482 4.406c-.41 2.676 1.22 5.08 3.525 7.124l.388.336c-.313.022-.631-.027-.935-.092a10 10 0 0 1-.466-.112l-.537-.15C6.35 18.701 3.154 16.6 2.237 13.46c-.732-2.506-.028-5.015 1.52-6.575c1.434-1.445 3.56-2.031 5.741-1.092m1.628 7.448c.428-2.792 3.657-4.168 5.315-1.772a.104.104 0 0 0 .144.025c2.377-1.684 4.94.713 4.387 3.483q-.48 2.41-4.47 4l-.435.17l-.263.108c-.227.089-.467.16-.684.122c-.216-.038-.417-.188-.6-.348l-.31-.28q-3.47-2.986-3.084-5.508" />
                                        </g>
                                    </svg>
                                </div>
                                <div class="relative">
                                    @if ($story->photo_path)
                                        <img src="{{ asset('storage/' . $story->photo_path) }}"
                                            alt="{{ $story->title }}"
                                            class="w-full h-56 md:h-[320px] object-cover rounded-t-[30px]">
                                    @else
                                        <div class="w-full h-56 md:h-[320px] bg-white/10 rounded-t-[30px]"></div>
                                    @endif
                                    <div
                                        class="absolute inset-x-0 bottom-0 h-28 bg-gradient-to-b from-transparent via-white/12 to-white/8">
                                    </div>
                                </div>
                                <div class="relative -mt-5 px-4 pb-6 pt-2 md:px-10 text-center text-white">
                                    <p
                                        class="font-title text-lg md:text-[2rem] text-white drop-shadow-[0_2px_12px_rgba(0,0,0,0.24)]">
                                        {{ $story->title }}
                                    </p>
                                    @if ($story->year)
                                        <p class="text-xs md:text-sm mt-1 uppercase tracking-[0.24em] text-white/70">
                                            {{ $story->year }}
                                        </p>
                                    @endif
                                    <p class="text-sm md:text-base mt-3 leading-8 text-white/90 max-w-2xl mx-auto">
                                        {{ $story->description }}
                                    </p>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif



        <section
            class="relative min-h-screen flex items-center justify-center text-center text-white px-6 bg-[#9fbfd6] overflow-hidden">
            <div class="absolute top-0 left-0 w-40 opacity-20"><img src="ornament.png" alt=""></div>
            <div class="absolute bottom-0 right-0 w-40 opacity-20 rotate-180"><img src="ornament.png" alt="">
            </div>
            <div class="relative z-10 max-w-xl w-full">

                <h3 class="font-title text-xl  mb-4">Tanda Kasih</h3>
                <p class="text-sm opacity-90 mb-8">Doa restu dan kehadiran Anda sudah menjadi kebahagiaan tersendiri
                    bagi kami. Namun, apabila Anda ingin memberikan tanda kasih, kami telah menyediakan fitur berikut.
                </p>
                <div class="p-6 mb-6 text-left space-y-6">
                    @foreach ($invitation->bankAccounts as $acc)
                        <div>
                            <div class="flex items-center justify-between gap-3 mb-2">
                                <p class="text-sm opacity-90 font-semibold">{{ $acc->account_name }}</p>
                                <div class="flex items-center gap-2">
                                    <img src="{{ \App\Support\BankLogo::assetUrl($acc->bank_name) }}"
                                        alt="Logo {{ $acc->bank_name }}"
                                        class="w-12 h-8 rounded-md object-contain bg-white/90 p-1">
                                    {{-- <p class="text-sm opacity-90 font-semibold">{{ $acc->bank_name }}</p> --}}
                                </div>
                            </div>
                            <p class="text-lg font-bold mb-2">{{ $acc->account_number }}</p>
                            <div class="flex items-center gap-3 mb-2">
                                <button onclick="copyText('{{ $acc->account_number }}')"
                                    class="bg-transparent border border-white/40 px-5 py-2 rounded-full text-[10px] uppercase tracking-widest hover:bg-white/20 transition"
                                    type="button">Salin Rekening</button>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if ($invitation->gift_address)
                    <div class="bg-white/20 backdrop-blur-md rounded-2xl p-6">
                        <h4 class="font-semibold mb-3">Kirim Kado</h4>
                        <p class="text-sm mb-4 leading-relaxed">{{ $invitation->gift_address }}</p>
                        <button onclick="copyText('{{ addslashes($invitation->gift_address) }}')"
                            class="bg-transparent border border-white/40 px-5 py-2 rounded-full text-[10px] uppercase tracking-widest hover:bg-white/20 transition mt-2"
                            type="button">Salin Alamat</button>
                    </div>
                @endif
            </div>
        </section>

        <section
            class="relative min-h-screen flex flex-col items-center justify-between text-center text-white px-6 overflow-hidden">
            <div class="absolute inset-0">
                @if (count($slideshowImages) > 0)
                    <div class="absolute inset-0 w-full h-full overflow-hidden bg-slideshow">
                        @foreach ($slideshowImages as $index => $imgUrl)
                            <img src="{{ $imgUrl }}"
                                class="absolute inset-0 w-full h-full object-cover bg-zoom transition-opacity duration-[2000ms] ease-in-out {{ $index === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0' }}"
                                alt="Closing {{ $index + 1 }}">
                        @endforeach
                    </div>
                @else
                    <div class="w-full h-full bg-gradient-to-b from-slate-900 to-black"></div>
                @endif
                <div class="absolute inset-0 bg-black/35 z-20"></div>
                <div
                    class="absolute bottom-0 w-full h-1/2 bg-gradient-to-t from-black/85 via-black/50 to-transparent z-30">
                </div>
            </div>
            <div class="absolute top-0 left-0 w-40 opacity-20"><img src="ornament.png" alt=""></div>
            <div class="absolute top-0 right-0 w-40 opacity-20 rotate-180"><img src="ornament.png" alt="">
            </div>
            <div class="absolute bottom-0 left-0 w-40 opacity-20"><img src="ornament.png" alt=""></div>
            <div class="absolute bottom-0 right-0 w-40 opacity-20 rotate-180"><img src="ornament.png" alt="">
            </div>
            <div class="relative z-40 mt-20 max-w-xl">
                <p class="text-sm leading-relaxed opacity-90 mb-6">
                    {{ $invitation->closing_text ?: 'Merupakan suatu kebahagiaan dan kehormatan bagi kami, apabila Bapak/Ibu/Saudara/i berkenan hadir dan memberikan doa restu kepada kedua mempelai.' }}
                </p>
                <h2 class="font-title text-xl mb-2">{{ $coupleName }}</h2>
                @if (!empty($guest))
                    <p class="text-xs mt-4 break-all opacity-80">{{ $guest->getInvitationUrl() }}</p>
                @endif
            </div>
            <div class="relative z-40 mb-10 text-center flex flex-col items-center">
                <p class="text-[10px] uppercase tracking-[0.2em] opacity-60 mb-2">Digital Invitation by</p>
                <a href="https://janjisucikita.com" target="_blank"
                    class="inline-flex items-center justify-center gap-2 text-sm font-semibold tracking-wider hover:text-[#9fbfd6] transition-colors">
                    <img src="{{ asset('logo/logoputih.png') }}" alt="Logo"
                        class="h-5 w-auto object-contain opacity-90">
                    janjisucikita.com
                </a>
            </div>
        </section>
    </section>

    @if ($invitation->music_url)
        <audio id="bgMusic" loop>
            <source src="{{ asset('storage/' . $invitation->music_url) }}" type="audio/mpeg">
        </audio>
    @endif

    <div class="fixed right-4 bottom-4 z-50 flex flex-col gap-2">
        <button id="soundToggle" type="button" aria-label="Toggle suara" title="Suara: Off"
            class="w-8 h-8 rounded-full bg-slate-900/80 hover:bg-slate-950 border border-white/20 backdrop-blur-lg flex items-center justify-center transition-all duration-300 hover:scale-110 active:scale-95 shadow-[0_3px_15px_rgba(0,0,0,0.4)] hidden">
            <!-- Music SVG (On) -->
            <svg id="soundIconOn" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-white hidden"
                viewBox="0 0 24 24">
                <path d="M0 0h24v24H0z" fill="none" />
                <g fill="none">
                    <path fill="currentColor" fill-rule="evenodd"
                        d="M13 16.753V14H8.818a3.249 3.249 0 1 0 .403 6.472l.557-.07A3.68 3.68 0 0 0 13 16.754"
                        clip-rule="evenodd" />
                    <path stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"
                        d="M13 8v-.611c0-1.619 0-2.428.474-2.987s1.272-.693 2.868-.96L18.7 3.05c.136-.022.204-.034.24.006s.02.106-.013.24l-.895 3.581c-.015.06-.023.09-.044.11s-.05.026-.111.038zm0 0v6m0 0v2.753a3.68 3.68 0 0 1-3.222 3.65l-.557.07A3.249 3.249 0 1 1 8.818 14z" />
                </g>
            </svg>
            <!-- Music SVG (Off / Slashed) -->
            <svg id="soundIconOff" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-white"
                viewBox="0 0 24 24">
                <path d="M0 0h24v24H0z" fill="none" />
                <g fill="none">
                    <path fill="currentColor" fill-rule="evenodd"
                        d="M13 16.753V14H8.818a3.249 3.249 0 1 0 .403 6.472l.557-.07A3.68 3.68 0 0 0 13 16.754"
                        clip-rule="evenodd" />
                    <path stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"
                        d="M13 8v-.611c0-1.619 0-2.428.474-2.987s1.272-.693 2.868-.96L18.7 3.05c.136-.022.204-.034.24.006s.02.106-.013.24l-.895 3.581c-.015.06-.023.09-.044.11s-.05.026-.111.038zm0 0v6m0 0v2.753a3.68 3.68 0 0 1-3.222 3.65l-.557.07A3.249 3.249 0 1 1 8.818 14z" />
                    <path d="M4 4l16 16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                </g>
            </svg>
        </button>
        <button id="scrollTopBtn" type="button" aria-label="Scroll ke atas" title="Scroll ke atas"
            class="w-8 h-8 rounded-full bg-slate-900/80 hover:bg-slate-950 border border-white/20 backdrop-blur-lg flex items-center justify-center transition-all duration-300 hover:scale-110 active:scale-95 shadow-[0_3px_15px_rgba(0,0,0,0.4)]">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 19V5M5 12l7-7 7 7" />
            </svg>
        </button>
    </div>

    <div id="toast" class="toast-notification">Berhasil disalin!</div>

    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
    <script>
        const bgMusic = document.getElementById("bgMusic");
        const soundToggle = document.getElementById("soundToggle");
        const scrollTopBtn = document.getElementById("scrollTopBtn");
        const soundIconOn = document.getElementById("soundIconOn");
        const soundIconOff = document.getElementById("soundIconOff");
        let soundOn = true;
        let autoScrollOn = true;
        let autoScrollFrame = null;
        let visualInited = false;

        function initVisualEffects() {
            if (visualInited) return;
            visualInited = true;

            const sections = document.querySelectorAll("#mainContent section");
            sections.forEach((section, i) => {
                section.classList.add("reveal-section", "anim-fade-up-subtle");
            });

            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add("in-view");
                        // We removed initMap from here to avoid race conditions with opacity transitions
                    }
                });
            }, {
                threshold: 0.1
            });

            sections.forEach((section) => observer.observe(section));

            document.querySelectorAll("#mainContent img, #mainContent iframe").forEach((asset) => {
                asset.classList.add("asset-soft");
            });

            const itemAnims = ["anim-fade-up", "anim-slide-left", "anim-slide-right", "anim-zoom-in", "anim-pop-in",
                "anim-flip"
            ];
            // Exclude the map wrapper (#mapWrapper) from reveal-item animations
            const animatedItems = document.querySelectorAll(
                "#mainContent h1, #mainContent h2, #mainContent h3, #mainContent p, #mainContent article, #mainContent .glass-card, #mainContent .rounded-2xl:not(#mapWrapper), #mainContent form, #mainContent button, #mainContent a"
            );
            animatedItems.forEach((item, i) => {
                item.classList.add("reveal-item", itemAnims[i % itemAnims.length]);
                item.style.setProperty("--delay", `${(i % 8) * 60}ms`);
                observer.observe(item);
            });
        }

        function showToast(message = "Berhasil disalin!") {
            const toast = document.getElementById("toast");
            toast.textContent = message;
            toast.classList.add("show");
            setTimeout(() => {
                toast.classList.remove("show");
            }, 3000);
        }

        function copyText(text) {
            navigator.clipboard.writeText(text).then(() => {
                showToast("Berhasil disalin ke clipboard!");
            });
        }

        function openInvitation(instant = false) {
            const cover = document.getElementById("cover");
            const main = document.getElementById("mainContent");

            const doOpen = () => {
                cover.style.display = "none";
                main.classList.remove("hidden");
                initVisualEffects();
                soundOn = true;
                updateSoundLabel();
                if (bgMusic) {
                    bgMusic.play().catch(() => {});
                }
                autoScrollOn = true;
                if (!autoScrollFrame) {
                    runAutoScroll();
                }
                // Calculate distance using geolocation
                calculateDistance();
            };

            if (instant) {
                doOpen();
            } else {
                cover.classList.add("opacity-0");
                setTimeout(doOpen, 700);
            }
        }

        function calculateDistance() {
            const lat = parseFloat(@json($invitation->venue_lat ?? '0'));
            const lng = parseFloat(@json($invitation->venue_lng ?? '0'));

            if (isNaN(lat) || isNaN(lng) || (lat === 0 && lng === 0)) {
                console.log('No valid coordinates for distance calculation');
                return;
            }

            if ('geolocation' in navigator) {
                navigator.geolocation.getCurrentPosition((pos) => {
                    const userLat = pos.coords.latitude;
                    const userLng = pos.coords.longitude;

                    // Calculate distance using Haversine formula
                    const R = 6371; // Earth's radius in km
                    const dLat = (lat - userLat) * Math.PI / 180;
                    const dLng = (lng - userLng) * Math.PI / 180;
                    const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                        Math.cos(userLat * Math.PI / 180) * Math.cos(lat * Math.PI / 180) *
                        Math.sin(dLng / 2) * Math.sin(dLng / 2);
                    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                    const distKm = (R * c).toFixed(1);

                    const distEl = document.getElementById('userDistance');
                    const distText = document.getElementById('distanceText');
                    if (distEl && distText) {
                        distEl.classList.remove('hidden');
                        distText.textContent = 'Anda berjarak sekitar ' + distKm + ' km dari lokasi acara.';
                    }
                }, (error) => {
                    console.log('Geolocation error:', error);
                });
            }
        }

        // AJAX Form Submission
        async function handleFormSubmit(formId, listContainerId, pagerClass) {
            const form = document.getElementById(formId);
            if (!form) return;

            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const submitBtn = form.querySelector('button[type="submit"]');
                submitBtn.disabled = true;

                // Set a valid phone number placeholder if empty or placeholder value
                const phoneInput = form.querySelector('input[name="phone"]');
                if (phoneInput && (!phoneInput.value || phoneInput.value === '-')) {
                    phoneInput.value = '6280000000000';
                }

                try {
                    const formData = new FormData(form);
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    const result = await response.json();

                    if (response.ok) {
                        showToast(result.message || "Data berhasil dikirim!");
                        form.reset();
                        // Reload the page content part (simplified: fetch the same page and extract the list)
                        const refreshRes = await fetch(window.location.href);
                        const html = await refreshRes.text();
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');

                        const newList = doc.getElementById(listContainerId);
                        if (newList) {
                            document.getElementById(listContainerId).innerHTML = newList.innerHTML;
                            // Re-init pagination
                            if (formId === 'rsvpForm') {
                                initListPagination(".list-rsvp-item", ".list-rsvp-pager", 5);
                            } else {
                                initListPagination(".list-wish-item", ".list-wish-pager", 5);
                            }
                        }
                    } else {
                        // Handle validation errors from Laravel
                        let errorMsg = result.message || "Terjadi kesalahan!";
                        if (result.errors) {
                            const firstKey = Object.keys(result.errors)[0];
                            errorMsg = result.errors[firstKey][0];
                        }
                        showToast(errorMsg);
                    }
                } catch (error) {
                    console.error(error);
                    showToast("Gagal mengirim data.");
                } finally {
                    submitBtn.disabled = false;
                }
            });
        }

        handleFormSubmit('rsvpForm', 'rsvpListContainer', '.list-rsvp-pager');

        function updateSoundLabel() {
            if (soundToggle) {
                soundToggle.title = "Suara: " + (soundOn ? "On" : "Off");
                if (soundOn) {
                    soundToggle.classList.add('pulse-glow-active');
                } else {
                    soundToggle.classList.remove('pulse-glow-active');
                }
            }
            if (soundIconOn && soundIconOff) {
                soundIconOn.classList.toggle("hidden", !soundOn);
                soundIconOff.classList.toggle("hidden", soundOn);
            }
        }

        let lastScrollTime = 0;

        function runAutoScroll(timestamp) {
            if (!autoScrollOn) return;
            if (timestamp - lastScrollTime > 20) {
                window.scrollBy({
                    top: 1,
                    left: 0,
                    behavior: "auto"
                });
                lastScrollTime = timestamp;
            }
            autoScrollFrame = window.requestAnimationFrame(runAutoScroll);
        }

        // Disable auto-scroll and enable snap on manual interaction
        let snapEnabled = false;
        ['wheel', 'touchstart', 'keydown'].forEach(evt => {
            window.addEventListener(evt, () => {
                if (autoScrollOn) {
                    autoScrollOn = false;
                    if (autoScrollFrame) {
                        window.cancelAnimationFrame(autoScrollFrame);
                        autoScrollFrame = null;
                    }
                }
                if (!snapEnabled) {
                    document.documentElement.style.scrollSnapType = 'y proximity';
                    snapEnabled = true;
                }
            }, {
                passive: true
            });
        });

        if (soundToggle) {
            soundToggle.addEventListener("click", function() {
                soundOn = !soundOn;
                updateSoundLabel();
                if (!bgMusic) return;
                if (soundOn) {
                    bgMusic.play().catch(() => {});
                } else {
                    bgMusic.pause();
                }
            });
        }

        if (scrollTopBtn) {
            scrollTopBtn.addEventListener("click", function() {
                window.scrollTo({
                    top: 0,
                    behavior: "smooth"
                });
            });
        }

        updateSoundLabel();

        const formInputs = document.querySelectorAll('input, select, textarea');
        formInputs.forEach(input => {
            input.addEventListener('focus', () => {
                if (autoScrollOn) {
                    autoScrollOn = false;
                    if (autoScrollFrame) {
                        window.cancelAnimationFrame(autoScrollFrame);
                        autoScrollFrame = null;
                    }
                }
            });
        });

        function initListPagination(itemSelector, pagerSelector, pageSize = 5) {
            const items = Array.from(document.querySelectorAll(itemSelector));
            const pager = document.querySelector(pagerSelector);
            if (!pager) return;

            const prevBtn = pager.querySelector("[data-prev]");
            const nextBtn = pager.querySelector("[data-next]");
            const info = pager.querySelector("[data-page-info]");
            if (!prevBtn || !nextBtn || !info) return;

            const totalPages = Math.max(1, Math.ceil(items.length / pageSize));
            let page = 1;

            const render = () => {
                const start = (page - 1) * pageSize;
                const end = start + pageSize;
                items.forEach((item, index) => {
                    item.style.display = (index >= start && index < end) ? "" : "none";
                });
                info.textContent = `Hal ${page} / ${totalPages}`;
                prevBtn.disabled = page <= 1;
                nextBtn.disabled = page >= totalPages;
                pager.style.display = items.length > pageSize ? "flex" : "none";
            };

            prevBtn.addEventListener("click", () => {
                if (page > 1) {
                    page -= 1;
                    render();
                }
            });

            nextBtn.addEventListener("click", () => {
                if (page < totalPages) {
                    page += 1;
                    render();
                }
            });

            render();
        }

        initListPagination(".list-rsvp-item", ".list-rsvp-pager", 5);

        @if ($eventDateIso)
            const targetDate = new Date("{{ $eventDateIso }}T{{ $eventTimeIso }}").getTime();

            function updateCountdown() {
                const now = new Date().getTime();
                const gap = Math.max(0, targetDate - now);
                const days = Math.floor(gap / (1000 * 60 * 60 * 24));
                const hours = Math.floor((gap / (1000 * 60 * 60)) % 24);
                const minutes = Math.floor((gap / (1000 * 60)) % 60);
                const seconds = Math.floor((gap / 1000) % 60);
                const daysEl = document.getElementById("days");
                const hoursEl = document.getElementById("hours");
                const minutesEl = document.getElementById("minutes");
                const secondsEl = document.getElementById("seconds");
                if (daysEl) daysEl.innerText = days;
                if (hoursEl) hoursEl.innerText = hours;
                if (minutesEl) minutesEl.innerText = minutes;
                if (secondsEl) secondsEl.innerText = seconds;
            }
            setInterval(updateCountdown, 1000);
            updateCountdown();
        @endif

        // Initialize Fancybox Gallery with thumbnails strip at the bottom
        Fancybox.bind('[data-fancybox="gallery"]', {
            Thumbs: {
                type: "classic",
                autoStart: true
            },
            Toolbar: {
                display: {
                    left: ["infobar"],
                    middle: [],
                    right: ["slideshow", "thumbs", "close"],
                },
            },
            Carousel: {
                transition: "fade",
            },
            Images: {
                zoom: true,
            }
        });

        // Prevent right-click on gallery images and Fancybox modal to protect wedding photos from easy downloads
        document.addEventListener('contextmenu', function(e) {
            if (e.target.closest('.gallery-item img') || e.target.closest('.fancybox__content img')) {
                e.preventDefault();
            }
        });

        // Synced Multi-Section Slideshow automatic cross-fade
        (function() {
            const slideshowContainers = Array.from(document.querySelectorAll('.bg-slideshow'));
            if (slideshowContainers.length === 0) return;

            const maxImages = Math.max(...slideshowContainers.map(c => c.getElementsByTagName('img').length));
            if (maxImages <= 1) return;

            let activeIdx = 0;
            const intervalTime = 5000; // Ganti foto setiap 5 detik

            setInterval(() => {
                const nextIdx = (activeIdx + 1) % maxImages;

                slideshowContainers.forEach(container => {
                    const images = Array.from(container.getElementsByTagName('img'));
                    if (images.length <= 1) return;

                    const prevImg = images[activeIdx % images.length];
                    const nextImg = images[nextIdx % images.length];

                    if (prevImg) {
                        prevImg.classList.remove('opacity-100', 'z-10');
                        prevImg.classList.add('opacity-0', 'z-0');
                    }
                    if (nextImg) {
                        nextImg.classList.remove('opacity-0', 'z-0');
                        nextImg.classList.add('opacity-100', 'z-10');
                    }
                });

                activeIdx = nextIdx;
            }, intervalTime);
        })();
    </script>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var galleryThumbs = new Swiper(".gallery-thumbs", {
                spaceBetween: 12,
                slidesPerView: 3,
                freeMode: true,
                watchSlidesProgress: true,
                breakpoints: {
                    640: {
                        slidesPerView: 4
                    },
                    768: {
                        slidesPerView: 5
                    },
                    1024: {
                        slidesPerView: 6
                    }
                }
            });
            var galleryMain = new Swiper(".gallery-main", {
                spaceBetween: 10,
                navigation: {
                    nextEl: ".swiper-button-next",
                    prevEl: ".swiper-button-prev",
                },
                autoplay: {
                    delay: 3500,
                    disableOnInteraction: false,
                },
                thumbs: {
                    swiper: galleryThumbs
                }
            });
        });
    </script>
    <style>
        .gallery-thumbs .swiper-slide-thumb-active {
            opacity: 1 !important;
        }

        .gallery-thumbs .swiper-slide-thumb-active img {
            border-color: white;
            transform: scale(1.05);
        }
    </style>
</body>

</html>
