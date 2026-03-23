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
    <style>
        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', sans-serif;
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
            background: rgba(23, 38, 54, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.22);
            border-radius: 8px;
            padding: 10px 12px;
            font-weight: 700;
            letter-spacing: .02em;
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
    </style>
</head>

<body class="bg-black text-white overflow-x-hidden">
    @php
        $guestName = $guest->name ?? 'Nama Tamu';
        $coupleName = trim(
            ($invitation->groom_name ?? 'Mempelai Pria') . ' & ' . ($invitation->bride_name ?? 'Mempelai Wanita'),
        );
        $coverImage = $invitation->cover_photo ? asset('storage/' . $invitation->cover_photo) : null;
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
            @if ($coverImage)
                <img src="{{ $coverImage }}" class="w-full h-full object-cover bg-zoom" alt="Cover">
            @else
                <div class="w-full h-full bg-gradient-to-b from-slate-900 to-black"></div>
            @endif
            <div class="absolute inset-0 bg-black/70"></div>
            <div class="absolute bottom-0 w-full h-1/2 bg-gradient-to-t from-black via-black/80 to-transparent"></div>
        </div>
        <div class="absolute bottom-0 left-0 w-28 opacity-30"><img src="ornament.png" class="ornament-float"
                alt=""></div>
        <div class="absolute bottom-0 right-0 w-28 opacity-30 rotate-180"><img src="ornament.png" class="ornament-float"
                alt=""></div>
        <div class="relative z-10 pb-16 px-6">
            <h1 class="font-title text-3xl md:text-5xl mb-3 tracking-wide">{{ $coupleName }}</h1>
            <p class="text-sm mb-6 opacity-80">{{ $eventDateText }}</p>
            <p class="text-sm opacity-80">Kepada Yth.</p>
            <h2 class="text-lg font-medium mb-6">{{ $guestName }}</h2>
            <button onclick="openInvitation()"
                class="bg-white/20 backdrop-blur-md px-6 py-3 rounded-full hover:bg-white/30 transition">Open
                Invitation</button>
        </div>
    </section>

    <section id="mainContent" class="hidden">
        <section class="relative min-h-screen flex items-end justify-center text-center text-white overflow-hidden">
            <div class="absolute inset-0">
                @if ($coverImage)
                    <img src="{{ $coverImage }}" class="w-full h-full object-cover" alt="Pembuka">
                @else
                    <div class="w-full h-full bg-gradient-to-b from-slate-800 to-black"></div>
                @endif
                <div class="absolute inset-0 bg-black/50"></div>
                <div class="absolute bottom-0 w-full h-2/3 bg-gradient-to-t from-black via-black/90 to-transparent">
                </div>
            </div>
            <div class="absolute bottom-0 left-0 w-40 opacity-30"><img src="ornament.png" class="ornament-float"
                    alt=""></div>
            <div class="absolute bottom-0 right-0 w-40 opacity-30 rotate-180"><img src="ornament.png"
                    class="ornament-float" alt="">
            </div>
            <div class="relative z-10 pb-20 px-6">
                <h1 class="font-title text-3xl md:text-5xl mb-4">{{ $coupleName }}</h1>
                <p class="text-sm opacity-80 mb-6">{{ $eventDateText }}</p>
                <p class="text-sm opacity-80">Kepada Yth.</p>
                <h2 class="text-lg font-medium mt-1">{{ $guestName }}</h2>
            </div>
        </section>

        <section class="relative min-h-screen flex items-end justify-center text-center text-white overflow-hidden">
            <div class="absolute inset-0">
                @if ($coverImage)
                    <img src="{{ $coverImage }}" class="w-full h-full object-cover bg-zoom" alt="Pembuka">
                @else
                    <div class="w-full h-full bg-gradient-to-b from-slate-800 to-black"></div>
                @endif
                <div class="absolute inset-0 bg-black/50"></div>
                <div class="absolute bottom-0 w-full h-2/3 bg-gradient-to-t from-black via-black/40 to-transparent">
                </div>
            </div>
            <div class="absolute bottom-0 left-0 w-40 opacity-30"><img src="ornament.png" class="ornament-float"
                    alt=""></div>
            <div class="absolute bottom-0 right-0 w-40 opacity-30 rotate-180"><img src="ornament.png"
                    class="ornament-float" alt="">
            </div>

            <div class="relative z-10 px-6 pb-14 w-full max-w-3xl">
                <article class="glass-card rounded-3xl px-6 py-8 md:px-10 md:py-10 text-center">
                    <h3 class="font-title text-3xl md:text-4xl mb-4">{{ $coupleName }}</h3>
                    <p class="text-sm md:text-base leading-relaxed opacity-95">{!! nl2br(e($openingText)) !!}</p>
                    <p class="text-xs mt-5 opacity-80 tracking-wide">QS. Ar-Rum: 21</p>
                </article>
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
                <div class="absolute inset-0 bg-black/50"></div>
                <div class="absolute bottom-0 w-full h-1/2 bg-gradient-to-t from-black via-black/30 to-transparent">
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
                <h2 class="font-title text-3xl md:text-4xl mb-3">{{ $invitation->bride_name ?? '-' }}</h2>
                <p class="text-sm opacity-90 mb-6 leading-relaxed">{{ $invitation->bride_parent_name ?? '-' }}</p>
                @if ($invitation->bride_instagram)
                    <a href="{{ $invitation->bride_instagram }}" target="_blank"
                        class="inline-block bg-white/20 backdrop-blur-md px-5 py-2 rounded-full mb-8 hover:bg-white/30 transition">@instagram</a>
                @endif
                <h3 class="font-title text-xl mb-2">{{ $coupleName }}</h3>
                <p class="text-sm opacity-80">{{ $eventDateText }}</p>
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
                <h2 class="font-title text-3xl md:text-4xl mb-3">{{ $invitation->groom_name ?? '-' }}</h2>
                <p class="text-sm opacity-90 mb-6 leading-relaxed">{{ $invitation->groom_parent_name ?? '-' }}</p>
                @if ($invitation->groom_instagram)
                    <a href="{{ $invitation->groom_instagram }}" target="_blank"
                        class="inline-block bg-white/20 backdrop-blur-md px-5 py-2 rounded-full mb-8 hover:bg-white/30 transition">@instagram</a>
                @endif
                <h3 class="font-title text-xl mb-2">{{ $coupleName }}</h3>
                <p class="text-sm opacity-80">{{ $eventDateText }}</p>
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
                <h3 class="font-title text-2xl md:text-3xl mb-6 italic">Menuju Hari Spesial Kami</h3>
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
                <p class="text-sm mb-6 opacity-90">Tekan tombol dibawah ini untuk mengirim ucapan dan konfirmasi
                    kehadiran</p>
                <a href="#rsvp"
                    class="inline-block bg-black/40 backdrop-blur-md px-6 py-3 rounded-full hover:bg-black/60 transition">Konfirmasi
                    & Kirim Ucapan</a>
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
                @if ($coverImage)
                    <img src="{{ $coverImage }}" class="w-full h-full object-cover" alt="Timeline">
                @endif
                <div class="absolute inset-0 bg-black/70"></div>
            </div>
            <div class="relative z-10 max-w-5xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="font-title text-2xl md:text-3xl mt-6">SUSUNAN ACARA</h2>
                </div>
                <div class="relative">
                    <div class="absolute left-1/2 top-0 bottom-0 w-[2px] bg-white/30 transform -translate-x-1/2"></div>
                    @foreach ($invitation->events as $event)
                        <div class="mb-12 flex flex-col md:flex-row items-center">
                            <div
                                class="md:w-1/2 md:pr-8 text-center md:text-right {{ $loop->odd ? '' : 'order-2 md:order-1' }}">
                                @if ($loop->odd)
                                    <p class="text-sm font-semibold">
                                        {{ $event->event_time ? \Carbon\Carbon::parse($event->event_time)->format('H:i') : '-' }}
                                        WIB</p>
                                @else
                                    <h3 class="font-semibold">{{ $event->event_name }}</h3>
                                    <p class="text-sm opacity-80">{{ $event->event_description }}</p>
                                @endif
                            </div>
                            <div class="w-4 h-4 bg-sky-300 rounded-full border-2 border-white z-10"></div>
                            <div
                                class="md:w-1/2 md:pl-8 text-center md:text-left {{ $loop->odd ? 'mt-4 md:mt-0' : 'order-1 md:order-2 mb-4 md:mb-0' }}">
                                @if ($loop->odd)
                                    <h3 class="font-semibold">{{ $event->event_name }}</h3>
                                    <p class="text-sm opacity-80">{{ $event->event_description }}</p>
                                @else
                                    <p class="text-sm font-semibold">
                                        {{ $event->event_time ? \Carbon\Carbon::parse($event->event_time)->format('H:i') : '-' }}
                                        WIB</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </section>

        <section
            class="relative min-h-screen flex flex-col items-center justify-center text-center text-white px-6 bg-[#9fbfd6] overflow-hidden">
            <div class="absolute top-0 left-0 w-40 opacity-20"><img src="ornament.png" alt=""></div>
            <div class="absolute top-0 right-0 w-40 opacity-20 rotate-180"><img src="ornament.png" alt="">
            </div>
            <div class="absolute bottom-0 left-0 w-40 opacity-20"><img src="ornament.png" alt=""></div>
            <div class="absolute bottom-0 right-0 w-40 opacity-20 rotate-180"><img src="ornament.png" alt="">
            </div>
            <div class="relative z-10 max-w-xl w-full">
                <h2 class="font-title text-xl mb-1">{{ $coupleName }}</h2>
                <p class="text-sm opacity-80 mb-6">{{ $eventDateText }}</p>
                <h3 class="font-title text-2xl mb-2">{{ $invitation->venue_name }}</h3>
                <p class="text-sm mb-6 leading-relaxed">{{ $invitation->venue_address }}</p>
                @if ($mapsEmbed)
                    <div class="rounded-2xl overflow-hidden shadow-xl border border-white/30 mb-6">
                        <iframe src="{{ $mapsEmbed }}" width="100%" height="250" style="border:0;"
                            loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                @endif
                @if ($mapsUrl)
                    <a href="{{ $mapsUrl }}" target="_blank"
                        class="inline-block bg-white/20 backdrop-blur-md px-6 py-3 rounded-full hover:bg-white/30 transition mb-6">Gunakan
                        Google Maps</a>
                @endif
            </div>
        </section>



        @if ($invitation->photos->count())
            <section class="relative px-6 py-20 bg-[#9fbfd6]">
                <div class="max-w-6xl mx-auto">
                    <h2 class="font-title text-3xl mb-8 text-center">Galeri</h2>
                    <div class="columns-2 md:columns-4 gap-4 [column-fill:_balance]">
                        @foreach ($invitation->photos as $photo)
                            <div class="mb-4 break-inside-avoid">
                                <img src="{{ asset('storage/' . $photo->file_path) }}" alt="Galeri foto"
                                    class="w-full h-auto object-contain rounded-xl shadow-lg shadow-black/20">
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif



        <section id="rsvp"
            class="relative min-h-screen flex items-center justify-center text-center text-white px-6 bg-[#9fbfd6] overflow-hidden">
            <div class="absolute top-0 left-0 w-40 opacity-20"><img src="ornament.png" alt=""></div>
            <div class="absolute bottom-0 right-0 w-40 opacity-20 rotate-180"><img src="ornament.png" alt="">
            </div>
            <div class="relative z-10 w-full max-w-xl">
                <h2 class="font-title text-2xl mb-2">Konfirmasi Kehadiran</h2>
                <p class="text-sm opacity-80 mb-8">Mohon kesediaannya untuk mengisi konfirmasi kehadiran</p>
                <form method="POST" action="{{ route('invitation.rsvp', $invitation->slug) }}" class="space-y-4">
                    @csrf
                    <div class="wish-form">
                        <label class="wish-label" for="rsvpName">Nama :</label>
                        <input id="rsvpName" type="text" name="name" value="{{ $guest->name ?? '' }}"
                            placeholder="Nama Anda" class="wish-line-input" required>

                        <label class="wish-label mt-4 block" for="rsvpPax">Jumlah Tamu :</label>
                        <input id="rsvpPax" type="number" name="pax" min="1" max="10"
                            value="1" placeholder="Jumlah tamu hadir" class="wish-line-input" required>

                        <label class="wish-label mt-4 block" for="rsvpStatus">Konfirmasi :</label>
                        <select id="rsvpStatus" name="status" class="wish-select" required>
                            <option value="attending">Hadir</option>
                            <option value="not_attending">Tidak Hadir</option>
                            <option value="maybe">Masih Ragu</option>
                        </select>

                        <label class="wish-label mt-4 block" for="rsvpAddress">Alamat :</label>
                        <input id="rsvpAddress" type="text" name="message" placeholder="Alamat" maxlength="500"
                            class="wish-line-input">

                        @if (!empty($guest?->id))
                            <input type="hidden" name="guest_id" value="{{ $guest->id }}">
                        @endif

                        <button type="submit" class="wish-submit">KIRIM KONFIRMASI &#9992;</button>
                    </div>
                </form>
                <div
                    class="mt-8 bg-white/30 backdrop-blur-md rounded-2xl p-4 max-h-[280px] overflow-y-auto text-left space-y-4">
                    <h3 class="text-sm font-semibold">Daftar Konfirmasi Kehadiran</h3>
                    @php
                        $statusLabels = [
                            'attending' => 'Hadir',
                            'not_attending' => 'Tidak Hadir',
                            'maybe' => 'Mungkin',
                        ];
                    @endphp
                    @foreach ($invitation->rsvps as $rsvp)
                        <article class="message-card list-rsvp-item">
                            <p class="message-author">{{ $rsvp->name }}</p>
                            <p class="message-meta">{{ $rsvp->pax }} pax •
                                {{ $statusLabels[$rsvp->status] ?? $rsvp->status }}</p>
                            @if ($rsvp->message)
                                <p class="message-body"><span class="opacity-80">Alamat:</span> {{ $rsvp->message }}
                                </p>
                            @endif
                        </article>
                    @endforeach
                    <div class="flex items-center justify-between pt-1 list-rsvp-pager">
                        <button type="button" class="pager-btn" data-prev>Prev</button>
                        <span class="text-xs opacity-90" data-page-info>Hal 1 / 1</span>
                        <button type="button" class="pager-btn" data-next>Next</button>
                    </div>
                </div>
            </div>
        </section>

        @if ($invitation->loveStories->count())
            <section class="relative px-6 py-20 bg-[#9fbfd6]">
                <div class="max-w-4xl mx-auto">
                    <h2 class="font-title text-3xl mb-8 text-center">Love Story</h2>
                    <div class="overflow-hidden rounded-2xl shadow-2xl border border-white/20">
                        @foreach ($invitation->loveStories as $story)
                            @php
                                $mediaFirst = $loop->odd;
                            @endphp
                            <article class="grid grid-cols-2">
                                <div class="{{ $mediaFirst ? 'order-1' : 'order-2' }}">
                                    @if ($story->photo_path)
                                        <img src="{{ asset('storage/' . $story->photo_path) }}"
                                            alt="{{ $story->title }}" class="w-full h-64 md:h-72 object-cover">
                                    @else
                                        <div class="w-full h-64 md:h-72 bg-black/40"></div>
                                    @endif
                                </div>
                                <div
                                    class="{{ $mediaFirst ? 'order-2' : 'order-1' }} bg-black/65 text-white p-4 md:p-6 flex flex-col justify-center">
                                    <h3 class="text-sm md:text-lg font-semibold uppercase tracking-wide">
                                        {{ $story->title }}</h3>
                                    @if ($story->year)
                                        <p class="text-xs md:text-sm mt-2 font-semibold opacity-90">
                                            {{ $story->year }}
                                        </p>
                                    @endif
                                    <p class="text-xs md:text-sm mt-3 opacity-85 leading-relaxed">
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
            <div class="relative z-10 w-full max-w-xl">
                <h2 class="font-title text-2xl mb-2">Ucapan & Doa</h2>
                <p class="text-sm opacity-80 mb-8">Tulis ucapan terbaik untuk mempelai</p>
                <form method="POST" action="{{ route('invitation.wish', $invitation->slug) }}"
                    class="wish-form mt-2">
                    @csrf
                    <label class="wish-label" for="wishName">Nama :</label>
                    <input id="wishName" type="text" name="name" value="{{ $guest->name ?? '' }}"
                        placeholder="Nama Anda" class="wish-line-input" required>
                    <label class="wish-label mt-5 block" for="wishMessage">Pesan untuk Mempelai :</label>
                    <textarea id="wishMessage" name="message" placeholder="Tulis ucapan dan doa..." class="wish-line-textarea" required></textarea>
                    <button type="submit" class="wish-submit">KIRIM &#9992;</button>
                </form>
                <div
                    class="mt-8 bg-white/30 backdrop-blur-md rounded-2xl p-4 max-h-[280px] overflow-y-auto text-left space-y-4">
                    <h3 class="text-sm font-semibold">Daftar Ucapan & Doa</h3>
                    @foreach ($invitation->wishes as $wish)
                        <article class="message-card list-wish-item">
                            <p class="message-author">{{ $wish->name }}</p>
                            <p class="message-body">{{ $wish->message }}</p>
                        </article>
                    @endforeach
                    <div class="flex items-center justify-between pt-1 list-wish-pager">
                        <button type="button" class="pager-btn" data-prev>Prev</button>
                        <span class="text-xs opacity-90" data-page-info>Hal 1 / 1</span>
                        <button type="button" class="pager-btn" data-next>Next</button>
                    </div>
                </div>
            </div>
        </section>

        <section
            class="relative min-h-screen flex items-center justify-center text-center text-white px-6 bg-[#9fbfd6] overflow-hidden">
            <div class="absolute top-0 left-0 w-40 opacity-20"><img src="ornament.png" alt=""></div>
            <div class="absolute bottom-0 right-0 w-40 opacity-20 rotate-180"><img src="ornament.png" alt="">
            </div>
            <div class="relative z-10 max-w-xl w-full">
                <h2 class="font-title text-xl mb-2">{{ $coupleName }}</h2>
                <p class="text-sm opacity-80 mb-4">{{ $eventDateText }}</p>
                <h3 class="font-title text-2xl italic mb-4">Tanda Kasih</h3>
                <p class="text-sm opacity-90 mb-8">Terima kasih telah menambah semangat kegembiraan pernikahan kami
                    dengan kehadiran dan hadiah indah Anda.</p>
                <div class="bg-white/20 backdrop-blur-md rounded-2xl p-6 mb-6 text-left space-y-6">
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
                                    class="bg-black/40 px-4 py-2 rounded-lg text-sm hover:bg-black/60 transition"
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
                            class="bg-black/40 px-5 py-2 rounded-lg text-sm hover:bg-black/60 transition"
                            type="button">Salin Alamat</button>
                    </div>
                @endif
            </div>
        </section>

        <section
            class="relative min-h-screen flex flex-col items-center justify-between text-center text-white px-6 overflow-hidden">
            <div class="absolute inset-0">
                @if ($coverImage)
                    <img src="{{ $coverImage }}" class="w-full h-full object-cover" alt="Closing">
                @endif
                <div class="absolute inset-0 bg-black/70"></div>
                <div class="absolute bottom-0 w-full h-1/2 bg-gradient-to-t from-black via-black/90 to-transparent">
                </div>
            </div>
            <div class="absolute top-0 left-0 w-40 opacity-20"><img src="ornament.png" alt=""></div>
            <div class="absolute top-0 right-0 w-40 opacity-20 rotate-180"><img src="ornament.png" alt="">
            </div>
            <div class="absolute bottom-0 left-0 w-40 opacity-20"><img src="ornament.png" alt=""></div>
            <div class="absolute bottom-0 right-0 w-40 opacity-20 rotate-180"><img src="ornament.png" alt="">
            </div>
            <div class="relative z-10 mt-16 max-w-xl">
                <p class="text-sm leading-relaxed opacity-90 mb-6">
                    {{ $invitation->closing_text ?: 'Merupakan suatu kebahagiaan dan kehormatan bagi kami, apabila Bapak/Ibu/Saudara/i berkenan hadir dan memberikan doa restu kepada kedua mempelai.' }}
                </p>
                <h2 class="font-title text-2xl mb-2">{{ $coupleName }}</h2>
                <p class="text-sm opacity-80">{{ $eventDateText }}</p>
                @if (!empty($guest))
                    <p class="text-xs mt-4 break-all opacity-80">{{ $guest->getInvitationUrl() }}</p>
                @endif
            </div>
            <div class="relative z-10 mb-8 text-xs opacity-60 max-w-md">
                <p>Music</p>
            </div>
        </section>
    </section>

    @if ($invitation->music_url)
        <audio id="bgMusic" loop>
            <source src="{{ asset('storage/' . $invitation->music_url) }}" type="audio/mpeg">
        </audio>
    @endif

    <div class="fixed right-2 top-1/2 -translate-y-1/2 z-50 flex flex-col gap-1.5">
        <button id="soundToggle" type="button" aria-label="Toggle suara" title="Suara: Off"
            class="w-8 h-8 rounded-full bg-black/55 hover:bg-black/75 border border-white/25 backdrop-blur-md flex items-center justify-center transition">
            <svg id="soundIconOn" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-white hidden"
                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M11 5 6 9H3v6h3l5 4V5Zm4.5 3.5a6 6 0 0 1 0 7" />
            </svg>
            <svg id="soundIconOff" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-white" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5 6 9H3v6h3l5 4V5Zm4 5 4 4m0-4-4 4" />
            </svg>
        </button>
        <button id="scrollToggle" type="button" aria-label="Toggle auto scroll" title="Auto Scroll: Off"
            class="w-8 h-8 rounded-full bg-black/55 hover:bg-black/75 border border-white/25 backdrop-blur-md flex items-center justify-center transition">
            <svg id="scrollIconOn" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-white hidden"
                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m0 0-4-4m4 4 4-4" />
            </svg>
            <svg id="scrollIconOff" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-white" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m0 0-4-4m4 4 4-4m5-13L3 21" />
            </svg>
        </button>
    </div>

    <script>
        const bgMusic = document.getElementById("bgMusic");
        const soundToggle = document.getElementById("soundToggle");
        const scrollToggle = document.getElementById("scrollToggle");
        const soundIconOn = document.getElementById("soundIconOn");
        const soundIconOff = document.getElementById("soundIconOff");
        const scrollIconOn = document.getElementById("scrollIconOn");
        const scrollIconOff = document.getElementById("scrollIconOff");
        let soundOn = false;
        let autoScrollOn = false;
        let autoScrollFrame = null;
        let visualInited = false;

        function initVisualEffects() {
            if (visualInited) return;
            visualInited = true;

            const sections = document.querySelectorAll("#mainContent section");
            const sectionAnims = ["anim-fade-up", "anim-slide-left", "anim-slide-right", "anim-zoom-in", "anim-pop-in",
                "anim-flip"
            ];
            sections.forEach((section, i) => {
                section.classList.add("reveal-section", sectionAnims[i % sectionAnims.length]);
            });

            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add("in-view");
                    }
                });
            }, {
                threshold: 0.14
            });

            sections.forEach((section) => observer.observe(section));

            document.querySelectorAll("#mainContent img, #mainContent iframe").forEach((asset) => {
                asset.classList.add("asset-soft");
            });

            const itemAnims = ["anim-fade-up", "anim-slide-left", "anim-slide-right", "anim-zoom-in", "anim-pop-in",
                "anim-flip"
            ];
            const animatedItems = document.querySelectorAll(
                "#mainContent h1, #mainContent h2, #mainContent h3, #mainContent p, #mainContent article, #mainContent .glass-card, #mainContent .rounded-2xl, #mainContent form, #mainContent button, #mainContent a"
            );
            animatedItems.forEach((item, i) => {
                item.classList.add("reveal-item", itemAnims[i % itemAnims.length]);
                item.style.setProperty("--delay", `${(i % 8) * 60}ms`);
                observer.observe(item);
            });
        }

        function openInvitation() {
            const cover = document.getElementById("cover");
            const main = document.getElementById("mainContent");
            cover.classList.add("opacity-0");
            setTimeout(() => {
                cover.style.display = "none";
                main.classList.remove("hidden");
                initVisualEffects();
                soundOn = true;
                updateSoundLabel();
                if (bgMusic) {
                    bgMusic.play().catch(() => {});
                }
                autoScrollOn = true;
                updateScrollLabel();
                if (!autoScrollFrame) {
                    runAutoScroll();
                }
            }, 700);
        }

        function copyText(text) {
            navigator.clipboard.writeText(text);
        }

        function updateSoundLabel() {
            if (soundToggle) {
                soundToggle.title = "Suara: " + (soundOn ? "On" : "Off");
            }
            if (soundIconOn && soundIconOff) {
                soundIconOn.classList.toggle("hidden", !soundOn);
                soundIconOff.classList.toggle("hidden", soundOn);
            }
        }

        function updateScrollLabel() {
            if (scrollToggle) {
                scrollToggle.title = "Auto Scroll: " + (autoScrollOn ? "On" : "Off");
            }
            if (scrollIconOn && scrollIconOff) {
                scrollIconOn.classList.toggle("hidden", !autoScrollOn);
                scrollIconOff.classList.toggle("hidden", autoScrollOn);
            }
        }

        function runAutoScroll() {
            if (!autoScrollOn) return;
            window.scrollBy({
                top: 1,
                left: 0,
                behavior: "auto"
            });
            autoScrollFrame = window.requestAnimationFrame(runAutoScroll);
        }

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

        if (scrollToggle) {
            scrollToggle.addEventListener("click", function() {
                autoScrollOn = !autoScrollOn;
                updateScrollLabel();
                if (autoScrollOn) {
                    runAutoScroll();
                } else if (autoScrollFrame) {
                    window.cancelAnimationFrame(autoScrollFrame);
                    autoScrollFrame = null;
                }
            });
        }

        updateSoundLabel();
        updateScrollLabel();

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
        initListPagination(".list-wish-item", ".list-wish-pager", 5);

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
    </script>
</body>

</html>
