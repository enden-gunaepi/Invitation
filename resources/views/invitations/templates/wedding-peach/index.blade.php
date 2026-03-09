<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $invitation->title }} - {{ $invitation->venue_name }}">
    <title>{{ $invitation->title }} - {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --peach-50: #fff7f2;
            --peach-100: #ffefe5;
            --peach-200: #ffd8c2;
            --peach-300: #ffc2a2;
            --peach-500: #e89570;
            --peach-700: #9c6246;
            --rose: #d97979;
            --text-main: #4c3a32;
            --text-soft: #7a6359;
            --card: rgba(255, 255, 255, 0.78);
            --border: rgba(232, 149, 112, 0.28);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background:
                radial-gradient(circle at 10% -5%, rgba(255, 194, 162, 0.35), transparent 32%),
                radial-gradient(circle at 90% 5%, rgba(217, 121, 121, 0.20), transparent 34%),
                linear-gradient(180deg, var(--peach-50) 0%, #fffdfb 70%, #ffffff 100%);
            color: var(--text-main);
            overflow-x: hidden;
        }
        .bg-slideshow {
            position: fixed;
            inset: 0;
            z-index: -2;
            overflow: hidden;
        }
        .bg-slide {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0;
            transform: scale(1.16);
            transition: opacity 1.8s ease, transform 9s ease;
            filter: grayscale(35%) contrast(118%) brightness(68%);
        }
        .bg-slide.active {
            opacity: 0.9;
            transform: scale(1);
            animation: kenburn 9s ease forwards;
        }
        @keyframes kenburn {
            from { transform: scale(1.08) translate3d(0, 0, 0); }
            to { transform: scale(1) translate3d(-0.8%, -0.8%, 0); }
        }
        .bg-overlay {
            position: fixed;
            inset: 0;
            z-index: -1;
            background:
                radial-gradient(circle at 50% 35%, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.42) 75%),
                linear-gradient(180deg, rgba(0,0,0,0.48) 0%, rgba(0,0,0,0.56) 100%);
        }

        .cover {
            min-height: 100vh;
            display: grid;
            place-items: center;
            position: relative;
            padding: 1.5rem;
            overflow: hidden;
        }
        .cover::before,
        .cover::after {
            content: '';
            position: absolute;
            width: 340px;
            height: 340px;
            border-radius: 50%;
            z-index: 0;
            filter: blur(2px);
        }
        .cover::before {
            background: radial-gradient(circle, rgba(255, 194, 162, 0.45) 0%, rgba(255, 194, 162, 0.02) 75%);
            top: -110px;
            left: -110px;
        }
        .cover::after {
            background: radial-gradient(circle, rgba(217, 121, 121, 0.35) 0%, rgba(217, 121, 121, 0.02) 75%);
            right: -120px;
            bottom: -120px;
        }

        .peach-frame {
            width: min(680px, 100%);
            border: 1px solid rgba(232, 149, 112, 0.3);
            border-radius: 36px;
            background: linear-gradient(180deg, rgba(255,255,255,0.94) 0%, rgba(255,248,241,0.90) 100%);
            box-shadow: 0 20px 60px rgba(180, 110, 78, 0.18);
            padding: 2.1rem 1.6rem;
            text-align: center;
            position: relative;
            z-index: 1;
        }
        .floral {
            position: absolute;
            width: 80px;
            height: 80px;
            border-radius: 20px;
            background: radial-gradient(circle at 30% 30%, rgba(255, 194, 162, .8), rgba(232, 149, 112, .18));
            opacity: .65;
        }
        .floral.f1 { top: 10px; left: 10px; }
        .floral.f2 { top: 10px; right: 10px; }
        .floral.f3 { bottom: 10px; left: 10px; }
        .floral.f4 { bottom: 10px; right: 10px; }

        .label {
            letter-spacing: .26em;
            text-transform: uppercase;
            font-size: .68rem;
            color: var(--text-soft);
            margin-bottom: .9rem;
            font-weight: 600;
        }
        .title-main {
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(2.2rem, 9vw, 4.2rem);
            color: var(--peach-700);
            line-height: .98;
            margin-bottom: .6rem;
        }
        .title-sub {
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(1.3rem, 5vw, 2.1rem);
            color: var(--text-main);
            margin-bottom: 1rem;
        }
        .cover-date {
            font-size: .86rem;
            color: var(--text-soft);
            margin-bottom: 1.35rem;
        }
        .to-name {
            display: inline-block;
            padding: .55rem .95rem;
            border-radius: 999px;
            font-size: .76rem;
            color: var(--peach-700);
            border: 1px solid var(--border);
            background: rgba(255, 255, 255, .7);
            margin-bottom: 1.25rem;
        }
        .open-btn {
            border: 1px solid var(--peach-500);
            background: linear-gradient(135deg, #f6b494 0%, #e89570 100%);
            color: #fff;
            border-radius: 999px;
            padding: .82rem 1.4rem;
            font-weight: 600;
            font-size: .9rem;
            cursor: pointer;
            transition: .25s ease;
        }
        .open-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(232,149,112,.3); }

        .content { display: none; }
        .content.visible { display: block; animation: fadeIn .8s ease; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        .section {
            max-width: 760px;
            margin: 0 auto;
            padding: 3.2rem 1.25rem;
            text-align: center;
        }
        .section-alt {
            background: linear-gradient(180deg, rgba(255,239,229,.55) 0%, rgba(255,255,255,.7) 100%);
            border-top: 1px solid rgba(232,149,112,.12);
            border-bottom: 1px solid rgba(232,149,112,.12);
        }
        .section-kicker {
            text-transform: uppercase;
            font-size: .66rem;
            letter-spacing: .23em;
            color: var(--text-soft);
            font-weight: 600;
            margin-bottom: .8rem;
        }
        .section-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(1.8rem, 5vw, 2.6rem);
            color: var(--peach-700);
            margin-bottom: 1.05rem;
        }
        .lead {
            max-width: 560px;
            margin: 0 auto;
            font-size: .95rem;
            color: var(--text-soft);
            line-height: 1.8;
        }
        .divider {
            width: 70px;
            height: 2px;
            border-radius: 99px;
            margin: 0 auto 1.2rem;
            background: linear-gradient(90deg, transparent 0%, var(--peach-500) 50%, transparent 100%);
        }

        .name-main {
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(2.2rem, 7vw, 3.5rem);
            line-height: 1;
            margin-bottom: 1rem;
            color: var(--peach-700);
        }
        .event-card, .panel, .wish-item {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 1rem;
            text-align: left;
            backdrop-filter: blur(5px);
        }
        .event-grid, .story-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: .8rem;
            max-width: 560px;
            margin: 0 auto;
        }
        .timeline {
            position: relative;
            max-width: 680px;
            margin: 0 auto;
            padding: .4rem 0;
        }
        .timeline::before {
            content: '';
            position: absolute;
            left: 50%;
            top: 0;
            bottom: 0;
            width: 2px;
            background: rgba(232, 149, 112, 0.45);
            transform: translateX(-50%);
        }
        .timeline-item {
            width: 50%;
            padding: .5rem .9rem;
            position: relative;
        }
        .timeline-item.left { margin-left: 0; text-align: right; }
        .timeline-item.right { margin-left: 50%; text-align: left; }
        .timeline-dot {
            position: absolute;
            top: 22px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--peach-500);
            border: 2px solid #fff;
            box-shadow: 0 0 0 2px rgba(232, 149, 112, 0.3);
        }
        .timeline-item.left .timeline-dot { right: -6px; }
        .timeline-item.right .timeline-dot { left: -6px; }
        .story-photo {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 12px;
            border: 1px solid var(--border);
            margin-bottom: .7rem;
        }
        .couple-grid {
            position: relative;
            max-width: 780px;
            min-height: 720px;
            margin: 0 auto;
        }
        .couple-stage {
            position: relative;
            min-height: 720px;
            border-radius: 28px;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.24);
            box-shadow: 0 16px 48px rgba(0,0,0,0.36);
            background-size: cover;
            background-position: center;
            isolation: isolate;
        }
        .couple-stage::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 50% 45%, rgba(0,0,0,0.14) 0%, rgba(0,0,0,0.52) 75%),
                linear-gradient(180deg, rgba(0,0,0,0.35), rgba(0,0,0,0.64));
            z-index: 0;
        }
        .couple-card {
            position: absolute;
            width: min(340px, 44vw);
            color: #fff;
            text-align: left;
            padding: 1rem 1.1rem;
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,0.25);
            background: rgba(0,0,0,0.18);
            backdrop-filter: blur(5px);
            z-index: 1;
        }
        .couple-card.groom {
            left: 0;
            top: 90px;
            animation: driftA 5s ease-in-out infinite;
        }
        .couple-card.bride {
            right: 0;
            bottom: 90px;
            text-align: right;
            animation: driftB 5.4s ease-in-out infinite;
        }
        .couple-photo-ring {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 3px solid rgba(255,255,255,0.75);
            padding: 5px;
            background: rgba(0,0,0,0.28);
            margin-bottom: .8rem;
            animation: pulseRing 3.4s ease-in-out infinite;
        }
        .couple-card.bride .couple-photo-ring {
            margin-left: auto;
        }
        .couple-photo {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(255,255,255,0.45);
        }
        .couple-separator {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            color: rgba(255,255,255,0.95);
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(2rem, 5vw, 3.4rem);
            font-weight: 700;
            animation: fadeBlink 2.4s ease-in-out infinite;
            z-index: 1;
        }
        .identity-role {
            font-size: .76rem;
            color: rgba(255,255,255,0.78);
            margin-bottom: .2rem;
            letter-spacing: .04em;
        }
        .identity-name {
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(1.55rem, 2.9vw, 2.15rem);
            font-weight: 700;
            line-height: 1.05;
            margin-bottom: .55rem;
        }
        .identity-parent {
            font-size: .88rem;
            line-height: 1.7;
            color: rgba(255,255,255,0.88);
        }
        .social-links a {
            color: #fff;
            font-size: .82rem;
            margin-right: .6rem;
            text-decoration: none;
            font-weight: 600;
        }
        .social-links a:last-child { margin-right: 0; }
        .social-links .social-btn {
            display: inline-flex;
            align-items: center;
            gap: .36rem;
            border: 1px solid rgba(255,255,255,0.38);
            border-radius: 999px;
            padding: .25rem .62rem;
            background: rgba(0,0,0,0.2);
        }
        @keyframes pulseRing {
            0%, 100% { box-shadow: 0 0 0 0 rgba(255,255,255,0.18); }
            50% { box-shadow: 0 0 0 9px rgba(255,255,255,0.05); }
        }
        @keyframes driftA {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        @keyframes driftB {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(10px); }
        }
        @keyframes fadeBlink {
            0%, 100% { opacity: 0.95; }
            50% { opacity: 0.58; }
        }
        .gallery {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .6rem;
        }
        .gallery img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 14px;
            border: 1px solid var(--border);
        }
        .maps {
            width: 100%;
            border-radius: 16px;
            border: 1px solid var(--border);
            overflow: hidden;
            background: #fff;
        }
        .leaflet-map { width: 100%; height: 290px; }

        .input, .select, .textarea {
            width: 100%;
            border-radius: 12px;
            border: 1px solid var(--border);
            background: rgba(255,255,255,.86);
            color: var(--text-main);
            padding: .76rem .86rem;
            margin-bottom: .7rem;
            font-size: .88rem;
        }
        .textarea { resize: none; min-height: 88px; }
        .btn {
            width: 100%;
            border: 1px solid var(--peach-500);
            background: linear-gradient(135deg, #f6b494 0%, #e89570 100%);
            color: #fff;
            border-radius: 12px;
            padding: .82rem 1rem;
            font-weight: 600;
            font-size: .9rem;
            cursor: pointer;
        }
        .wish-list { max-width: 560px; margin: 1rem auto 0; display: grid; gap: .6rem; }
        .wish-list-scroll {
            max-height: 320px;
            overflow-y: auto;
            padding-right: .2rem;
        }
        .gift-tabs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: .5rem;
            margin-bottom: .9rem;
        }
        .gift-tab-btn {
            border: 1px solid var(--border);
            background: rgba(255,255,255,.8);
            color: var(--text-main);
            border-radius: 10px;
            padding: .6rem .7rem;
            font-size: .85rem;
            font-weight: 600;
            cursor: pointer;
        }
        .gift-tab-btn.active {
            background: linear-gradient(135deg, #f6b494 0%, #e89570 100%);
            color: #fff;
            border-color: var(--peach-500);
        }
        .gift-pane { display: none; }
        .gift-pane.active { display: block; }
        .wish-name { font-weight: 700; color: var(--peach-700); font-size: .85rem; margin-bottom: .2rem; }
        .wish-message { color: var(--text-soft); font-size: .86rem; line-height: 1.6; }

        .countdown {
            display: flex;
            justify-content: center;
            gap: .6rem;
            flex-wrap: wrap;
            margin-top: 1.2rem;
        }
        .cd-item {
            min-width: 72px;
            border: 1px solid var(--border);
            background: var(--card);
            border-radius: 12px;
            text-align: center;
            padding: .58rem .4rem;
        }
        .cd-num {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--peach-700);
            line-height: 1.1;
        }
        .cd-lbl {
            font-size: .66rem;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: var(--text-soft);
        }

        .music-player {
            position: fixed;
            right: 18px;
            bottom: 18px;
            width: 46px;
            height: 46px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f6b494, #e89570);
            border: 1px solid rgba(255,255,255,.5);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            box-shadow: 0 8px 22px rgba(176, 96, 65, .34);
            cursor: pointer;
            z-index: 99;
        }
        .music-player.playing { animation: spin 3.4s linear infinite; }
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

        .footer {
            text-align: center;
            padding: 2.2rem 1rem 2.8rem;
            color: var(--text-soft);
            font-size: .8rem;
            border-top: 1px solid rgba(232,149,112,.18);
        }

        @media (min-width: 700px) {
            .gallery { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        }
        @media (max-width: 720px) {
            .timeline::before { left: 10px; transform: none; }
            .timeline-item { width: 100%; margin-left: 0 !important; text-align: left !important; padding-left: 1.6rem; padding-right: .2rem; }
            .timeline-item .timeline-dot { left: 4px !important; right: auto !important; }
            .couple-grid { min-height: 840px; }
            .couple-stage { min-height: 840px; }
            .couple-card { width: calc(100% - 1rem); left: .5rem !important; right: .5rem !important; text-align: left !important; }
            .couple-card.groom { top: 70px; }
            .couple-card.bride { bottom: 70px; }
            .couple-card.bride .couple-photo-ring { margin-left: 0; }
        }
    </style>
</head>
<body>
    @php
        $bgSlides = collect();
        if ($invitation->groom_photo) {
            $bgSlides->push($invitation->groom_photo);
        }
        if ($invitation->bride_photo) {
            $bgSlides->push($invitation->bride_photo);
        }
        foreach ($invitation->photos as $photo) {
            $bgSlides->push($photo->file_path);
        }
        $bgSlides = $bgSlides->filter()->unique()->values();
    @endphp
    @if($bgSlides->count())
        <div class="bg-slideshow" id="bgSlideshow">
            @foreach($bgSlides as $slidePath)
                <img src="{{ asset('storage/' . $slidePath) }}" class="bg-slide {{ $loop->first ? 'active' : '' }}" alt="Background">
            @endforeach
        </div>
    @endif
    <div class="bg-overlay"></div>

    @php
        $recipient = $guest->name ?? request('to');
        $address = request('address');
    @endphp

    <section class="cover" id="cover">
        <div class="peach-frame" data-aos="zoom-in" data-aos-duration="900">
            <span class="floral f1"></span>
            <span class="floral f2"></span>
            <span class="floral f3"></span>
            <span class="floral f4"></span>

            <div class="label">The Wedding Of</div>
            <h1 class="title-main">{{ $invitation->groom_name ?: $invitation->host_name ?: 'Mempelai' }}</h1>
            <div class="title-sub">&amp;</div>
            <h2 class="title-main">{{ $invitation->bride_name ?: $invitation->title }}</h2>
            <div class="cover-date">{{ $invitation->event_date->format('d F Y') }}</div>
            @if($recipient)
                <div class="to-name">Kepada Yth: {{ $recipient }}{{ $address ? ' - ' . $address : '' }}</div>
            @endif
            <button class="open-btn" onclick="openInvitation()"><i class="fas fa-envelope-open-text mr-2"></i>Buka Undangan</button>
        </div>
    </section>

    <main class="content" id="content">
        <section class="section">
            <div class="section-kicker" data-aos="fade-up">Assalamu'alaikum</div>
            <h2 class="section-title" data-aos="fade-up" data-aos-delay="100">{{ $invitation->title }}</h2>
            <div class="divider" data-aos="fade-up" data-aos-delay="140"></div>
            <p class="lead" data-aos="fade-up" data-aos-delay="200">
                {{ $invitation->opening_text ?: 'Dengan memohon rahmat dan ridho Allah SWT, kami mengundang Bapak/Ibu/Saudara/i untuk hadir pada acara kami.' }}
            </p>
        </section>

        <section class="section section-alt">
            <div class="section-kicker" data-aos="fade-up">Mempelai</div>
            <h2 class="section-title" data-aos="fade-up" data-aos-delay="100">Bride &amp; Groom</h2>
            <div class="divider" data-aos="fade-up" data-aos-delay="150"></div>
            @php
                $coupleBg = $invitation->photos->first()->file_path ?? $invitation->groom_photo ?? $invitation->bride_photo;
            @endphp
            <div class="couple-grid">
                <div class="couple-stage" style="{{ $coupleBg ? "background-image: url('" . asset('storage/' . $coupleBg) . "');" : 'background: linear-gradient(180deg, #4d443e 0%, #2f2824 100%);' }}">
                    <div class="couple-card groom" data-aos="fade-right" data-aos-delay="200">
                        <div class="couple-photo-ring">
                            @if($invitation->groom_photo)
                                <img src="{{ asset('storage/' . $invitation->groom_photo) }}" class="couple-photo" alt="Groom">
                            @else
                                <div class="couple-photo flex items-center justify-center font-bold" style="background:var(--peach-100); color:var(--peach-700);">
                                    {{ strtoupper(substr($invitation->groom_name ?? 'G', 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div class="identity-role">Putra dari</div>
                        <div class="identity-name">{{ $invitation->groom_name ?: '-' }}</div>
                        @if($invitation->groom_parent_name)<div class="identity-parent">{{ $invitation->groom_parent_name }}</div>@endif
                        <div class="social-links mt-2">
                            @if($invitation->groom_instagram)<a class="social-btn" href="{{ str_starts_with($invitation->groom_instagram, 'http') ? $invitation->groom_instagram : 'https://instagram.com/' . ltrim($invitation->groom_instagram, '@') }}" target="_blank"><i class="fab fa-instagram"></i> Instagram</a>@endif
                            @if($invitation->groom_facebook)<a class="social-btn" href="{{ $invitation->groom_facebook }}" target="_blank"><i class="fab fa-facebook"></i> Facebook</a>@endif
                        </div>
                    </div>

                    <div class="couple-separator">&amp;</div>

                    <div class="couple-card bride" data-aos="fade-left" data-aos-delay="250">
                        <div class="couple-photo-ring">
                            @if($invitation->bride_photo)
                                <img src="{{ asset('storage/' . $invitation->bride_photo) }}" class="couple-photo" alt="Bride">
                            @else
                                <div class="couple-photo flex items-center justify-center font-bold" style="background:var(--peach-100); color:var(--peach-700);">
                                    {{ strtoupper(substr($invitation->bride_name ?? 'B', 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div class="identity-role">Putri dari</div>
                        <div class="identity-name">{{ $invitation->bride_name ?: '-' }}</div>
                        @if($invitation->bride_parent_name)<div class="identity-parent">{{ $invitation->bride_parent_name }}</div>@endif
                        <div class="social-links mt-2">
                            @if($invitation->bride_instagram)<a class="social-btn" href="{{ str_starts_with($invitation->bride_instagram, 'http') ? $invitation->bride_instagram : 'https://instagram.com/' . ltrim($invitation->bride_instagram, '@') }}" target="_blank"><i class="fab fa-instagram"></i> Instagram</a>@endif
                            @if($invitation->bride_facebook)<a class="social-btn" href="{{ $invitation->bride_facebook }}" target="_blank"><i class="fab fa-facebook"></i> Facebook</a>@endif
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="section-kicker" data-aos="fade-up">Countdown</div>
            <h2 class="section-title" data-aos="fade-up" data-aos-delay="100">Menuju Hari H</h2>
            <div class="divider" data-aos="fade-up" data-aos-delay="150"></div>
            <div class="countdown" id="countdown" data-date="{{ $invitation->event_date->format('Y-m-d') }}T{{ \Carbon\Carbon::parse($invitation->event_time)->format('H:i:s') }}">
                <div class="cd-item"><div class="cd-num" id="cd-days">0</div><div class="cd-lbl">Hari</div></div>
                <div class="cd-item"><div class="cd-num" id="cd-hours">0</div><div class="cd-lbl">Jam</div></div>
                <div class="cd-item"><div class="cd-num" id="cd-minutes">0</div><div class="cd-lbl">Menit</div></div>
                <div class="cd-item"><div class="cd-num" id="cd-seconds">0</div><div class="cd-lbl">Detik</div></div>
            </div>
        </section>

        <section class="section section-alt">
            <div class="section-kicker" data-aos="fade-up">Acara</div>
            <h2 class="section-title" data-aos="fade-up" data-aos-delay="100">Detail Acara</h2>
            <div class="divider" data-aos="fade-up" data-aos-delay="150"></div>
            @php
                $rundownItems = $invitation->events->count()
                    ? $invitation->events
                    : collect([
                        (object) [
                            'event_name' => $invitation->title,
                            'event_time' => $invitation->event_time,
                            'event_description' => $invitation->opening_text,
                        ],
                    ]);
            @endphp
            <div class="timeline">
                @foreach($rundownItems as $item)
                    <div class="timeline-item {{ $loop->odd ? 'left' : 'right' }}" data-aos="fade-up" data-aos-delay="{{ 170 + ($loop->index * 70) }}">
                        <span class="timeline-dot"></span>
                        <div class="event-card">
                            <strong>{{ \Carbon\Carbon::parse($item->event_time)->format('H:i') }} WIB</strong>
                            <p class="wish-name" style="margin-top:.35rem;">{{ $item->event_name }}</p>
                            @if(!empty($item->event_description))
                                <p class="wish-message">{{ $item->event_description }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            <div style="margin-top:1rem;" data-aos="fade-up" data-aos-delay="260">
                <a href="{{ $invitation->google_calendar_url }}" target="_blank" class="open-btn" style="display:inline-block;text-decoration:none;">
                    <i class="fas fa-calendar-plus mr-2"></i>Simpan ke Google Calendar
                </a>
            </div>
        </section>

        @if($invitation->loveStories->count())
            <section class="section">
                <div class="section-kicker" data-aos="fade-up">Story</div>
                <h2 class="section-title" data-aos="fade-up" data-aos-delay="100">Love Story</h2>
                <div class="divider" data-aos="fade-up" data-aos-delay="150"></div>
                <div class="story-grid">
                    @foreach($invitation->loveStories as $story)
                        <div class="wish-item" data-aos="fade-up" data-aos-delay="{{ 180 + ($loop->index * 70) }}">
                            @if($story->photo_path)
                                <img src="{{ asset('storage/' . $story->photo_path) }}" alt="{{ $story->title }}" class="story-photo">
                            @endif
                            @if($story->year)<div class="wish-name">{{ $story->year }}</div>@endif
                            <div class="wish-name">{{ $story->title }}</div>
                            @if($story->description)<div class="wish-message">{{ $story->description }}</div>@endif
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        @if($invitation->photos->count())
            <section class="section section-alt">
                <div class="section-kicker" data-aos="fade-up">Gallery</div>
                <h2 class="section-title" data-aos="fade-up" data-aos-delay="100">Our Moments</h2>
                <div class="divider" data-aos="fade-up" data-aos-delay="150"></div>
                <div class="gallery">
                    @foreach($invitation->photos as $photo)
                        <img src="{{ asset('storage/' . $photo->file_path) }}" alt="{{ $photo->caption ?? 'Gallery Photo' }}" loading="lazy" data-aos="zoom-in" data-aos-delay="{{ $loop->index * 60 }}">
                    @endforeach
                </div>
            </section>
        @endif

        @if($invitation->google_maps_url || $invitation->venue_address)
            <section class="section">
                <div class="section-kicker" data-aos="fade-up">Location</div>
                <h2 class="section-title" data-aos="fade-up" data-aos-delay="100">Peta Lokasi</h2>
                <div class="divider" data-aos="fade-up" data-aos-delay="150"></div>
                <div class="maps" data-aos="fade-up" data-aos-delay="220">
                    <div id="leafletMap" class="leaflet-map"
                        data-lat="{{ $invitation->venue_lat }}"
                        data-lng="{{ $invitation->venue_lng }}"
                        data-url="{{ $invitation->maps_deep_link }}"
                        data-title="{{ $invitation->venue_name }}"></div>
                </div>
                <div style="margin-top:1rem;display:flex;gap:.5rem;justify-content:center;flex-wrap:wrap;">
                    <a href="{{ $invitation->maps_deep_link }}" target="_blank" class="open-btn" style="display:inline-block;text-decoration:none;">
                        <i class="fas fa-location-dot mr-2"></i>Buka Google Maps
                    </a>
                    <a href="https://waze.com/ul?q={{ urlencode($invitation->venue_name . ' ' . $invitation->venue_address) }}" target="_blank" class="open-btn" style="display:inline-block;text-decoration:none;background:linear-gradient(135deg,#80d4ff,#3ba4e8);border-color:#3ba4e8;">
                        <i class="fas fa-route mr-2"></i>Buka di Waze
                    </a>
                </div>
            </section>
        @endif

        @if($invitation->livestream_url)
            @php
                $streamUrl = $invitation->livestream_url;
                $ytWatch = preg_match('~(?:youtube\.com/watch\?v=|youtu\.be/)([^&?/]+)~', $streamUrl, $m) ? $m[1] : null;
                $ytLive = preg_match('~youtube\.com/live/([^&?/]+)~', $streamUrl, $m2) ? $m2[1] : null;
                $videoId = $ytWatch ?: $ytLive;
                $embedUrl = $videoId ? 'https://www.youtube.com/embed/' . $videoId : null;
            @endphp
            <section class="section section-alt">
                <div class="section-kicker" data-aos="fade-up">Live</div>
                <h2 class="section-title" data-aos="fade-up" data-aos-delay="100">{{ $invitation->livestream_label ?: 'Live Streaming' }}</h2>
                <div class="divider" data-aos="fade-up" data-aos-delay="150"></div>
                @if($embedUrl)
                    <div class="maps" data-aos="fade-up" data-aos-delay="220">
                        <iframe src="{{ $embedUrl }}" width="100%" height="290" style="border:0;" allowfullscreen loading="lazy"></iframe>
                    </div>
                @endif
                <a href="{{ $invitation->livestream_url }}" target="_blank" class="open-btn" style="display:inline-block;margin-top:1rem;text-decoration:none;">
                    <i class="fas fa-video mr-2"></i>Buka Live Streaming
                </a>
            </section>
        @endif

        <section class="section section-alt" id="rsvp">
            <div class="section-kicker" data-aos="fade-up">Konfirmasi</div>
            <h2 class="section-title" data-aos="fade-up" data-aos-delay="100">RSVP</h2>
            <div class="divider" data-aos="fade-up" data-aos-delay="150"></div>
            <div class="panel" data-aos="fade-up" data-aos-delay="220" style="max-width:560px;margin:0 auto;">
                <form method="POST" action="{{ route('invitation.rsvp', $invitation->slug) }}">
                    @csrf
                    @if(isset($guest))
                        <input type="hidden" name="guest_id" value="{{ $guest->id }}">
                        <input type="hidden" name="name" value="{{ $guest->name }}">
                        <p class="wish-message" style="margin-bottom:.7rem;">Halo, <strong>{{ $guest->name }}</strong></p>
                    @else
                        <input type="text" name="name" class="input" placeholder="Nama lengkap" required>
                    @endif
                    <input type="text" name="phone" class="input" placeholder="No. HP (opsional)">
                    <select name="status" class="select" required>
                        <option value="attending">Hadir</option>
                        <option value="maybe">Belum Pasti</option>
                        <option value="not_attending">Tidak Hadir</option>
                    </select>
                    <input type="number" name="pax" class="input" value="1" min="1" max="10" placeholder="Jumlah tamu">
                    <textarea name="message" class="textarea" placeholder="Pesan (opsional)"></textarea>
                    <button type="submit" class="btn"><i class="fas fa-paper-plane mr-2"></i>Kirim Konfirmasi</button>
                </form>
            </div>

            @if($invitation->rsvps->whereNotNull('message')->count())
                <div class="wish-list wish-list-scroll">
                    @foreach($invitation->rsvps as $rsvp)
                        @if($rsvp->message)
                            <div class="wish-item" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                                <div class="wish-name">{{ $rsvp->name }} - {{ $rsvp->status }}</div>
                                <div class="wish-message">{{ $rsvp->message }}</div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </section>

        <section class="section" id="wishes">
            <div class="section-kicker" data-aos="fade-up">Ucapan</div>
            <h2 class="section-title" data-aos="fade-up" data-aos-delay="100">Wishes</h2>
            <div class="divider" data-aos="fade-up" data-aos-delay="150"></div>
            <div class="panel" data-aos="fade-up" data-aos-delay="220" style="max-width:560px;margin:0 auto;">
                <form method="POST" action="{{ route('invitation.wish', $invitation->slug) }}">
                    @csrf
                    <input type="text" name="name" class="input" placeholder="Nama Anda" value="{{ $guest->name ?? '' }}" required>
                    <textarea name="message" class="textarea" placeholder="Tulis ucapan & doa" required></textarea>
                    <button type="submit" class="btn"><i class="fas fa-heart mr-2"></i>Kirim Ucapan</button>
                </form>
            </div>
            <div class="wish-list">
                @foreach($invitation->wishes as $wish)
                    <div class="wish-item" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                        <div class="wish-name">{{ $wish->name }}</div>
                        <div class="wish-message">{{ $wish->message }}</div>
                    </div>
                @endforeach
            </div>
        </section>

        @if($invitation->bank_name || $invitation->gift_address || $invitation->bankAccounts->count())
            <section class="section section-alt">
                <div class="section-kicker" data-aos="fade-up">Gift</div>
                <h2 class="section-title" data-aos="fade-up" data-aos-delay="100">Hadiah</h2>
                <div class="divider" data-aos="fade-up" data-aos-delay="150"></div>
                <div class="panel" style="max-width:560px;margin:0 auto;" data-aos="fade-up" data-aos-delay="220">
                    <div class="gift-tabs">
                        <button type="button" class="gift-tab-btn active" data-target="gift-digital">Amplop Digital</button>
                        <button type="button" class="gift-tab-btn" data-target="gift-physical">Kirim Hadiah</button>
                    </div>
                    <div id="gift-digital" class="gift-pane active">
                        @if($invitation->bankAccounts->count())
                            @foreach($invitation->bankAccounts as $acc)
                                <div class="wish-item mb-2">
                                    <div class="wish-name">{{ $acc->bank_name }}</div>
                                    <div class="wish-message">{{ $acc->account_number }} a/n {{ $acc->account_name }}</div>
                                </div>
                            @endforeach
                        @elseif($invitation->bank_name)
                            <div class="wish-item">
                                <div class="wish-name">{{ $invitation->bank_name }}</div>
                                <div class="wish-message">{{ $invitation->bank_account_number }} a/n {{ $invitation->bank_account_name }}</div>
                            </div>
                        @else
                            <p class="wish-message">Data transfer belum tersedia.</p>
                        @endif
                    </div>
                    <div id="gift-physical" class="gift-pane">
                        @if($invitation->gift_address)
                            <p class="wish-message">Alamat hadiah: {{ $invitation->gift_address }}</p>
                        @else
                            <p class="wish-message">Alamat pengiriman hadiah belum tersedia.</p>
                        @endif
                    </div>
                </div>
            </section>
        @endif

        @if($invitation->closing_text)
            <section class="section">
                <div class="divider" data-aos="fade-up"></div>
                <p class="lead" data-aos="fade-up" data-aos-delay="100">{{ $invitation->closing_text }}</p>
            </section>
        @endif

        <footer class="footer">
            @if($invitation->footer_text)
                <p style="margin-bottom:.4rem;">{{ $invitation->footer_text }}</p>
            @endif
            <p>Made with love by {{ config('app.name') }}</p>
        </footer>
    </main>

    @if($invitation->music_url)
        <div class="music-player" id="musicPlayer" onclick="toggleMusic()"><i class="fas fa-music"></i></div>
        <audio id="bgMusic" loop>
            <source src="{{ asset('storage/' . $invitation->music_url) }}" type="audio/mpeg">
        </audio>
    @endif

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        let mapInitialized = false;

        function openInvitation() {
            document.getElementById('cover').style.display = 'none';
            document.getElementById('content').classList.add('visible');
            AOS.init({ duration: 850, once: true, offset: 60 });

            const audio = document.getElementById('bgMusic');
            const player = document.getElementById('musicPlayer');
            if (audio) {
                audio.play().then(() => player && player.classList.add('playing')).catch(() => {});
            }
            initLeafletMap();
            startBackgroundSlideshow();
            startCountdown();
        }

        function parseLatLngFromUrl(url) {
            if (!url) return null;
            const atMatch = url.match(/@(-?\d+\.?\d*),(-?\d+\.?\d*)/);
            if (atMatch) return [parseFloat(atMatch[1]), parseFloat(atMatch[2])];
            const qMatch = url.match(/q=(-?\d+\.?\d*),(-?\d+\.?\d*)/);
            if (qMatch) return [parseFloat(qMatch[1]), parseFloat(qMatch[2])];
            return null;
        }

        function initLeafletMap() {
            if (mapInitialized) return;
            const mapEl = document.getElementById('leafletMap');
            if (!mapEl) return;

            let lat = parseFloat(mapEl.dataset.lat || '');
            let lng = parseFloat(mapEl.dataset.lng || '');
            if (Number.isNaN(lat) || Number.isNaN(lng)) {
                const parsed = parseLatLngFromUrl(mapEl.dataset.url || '');
                if (!parsed) return;
                lat = parsed[0];
                lng = parsed[1];
            }

            const map = L.map(mapEl).setView([lat, lng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);
            L.marker([lat, lng]).addTo(map).bindPopup(mapEl.dataset.title || 'Lokasi Acara');
            mapInitialized = true;
        }

        function startBackgroundSlideshow() {
            const slides = document.querySelectorAll('#bgSlideshow .bg-slide');
            if (!slides.length || slides.length === 1) return;
            let idx = 0;
            setInterval(() => {
                slides[idx].classList.remove('active');
                idx = (idx + 1) % slides.length;
                slides[idx].classList.add('active');
            }, 5000);
        }

        function startCountdown() {
            const el = document.getElementById('countdown');
            if (!el) return;
            const target = new Date(el.dataset.date).getTime();
            setInterval(() => {
                const now = Date.now();
                const diff = target - now;
                if (diff < 0) return;

                document.getElementById('cd-days').textContent = Math.floor(diff / 86400000);
                document.getElementById('cd-hours').textContent = Math.floor((diff % 86400000) / 3600000);
                document.getElementById('cd-minutes').textContent = Math.floor((diff % 3600000) / 60000);
                document.getElementById('cd-seconds').textContent = Math.floor((diff % 60000) / 1000);
            }, 1000);
        }

        function toggleMusic() {
            const audio = document.getElementById('bgMusic');
            const player = document.getElementById('musicPlayer');
            if (!audio || !player) return;
            if (audio.paused) {
                audio.play();
                player.classList.add('playing');
            } else {
                audio.pause();
                player.classList.remove('playing');
            }
        }

        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.gift-tab-btn');
            if (!btn) return;
            document.querySelectorAll('.gift-tab-btn').forEach((el) => el.classList.remove('active'));
            btn.classList.add('active');
            const target = btn.getAttribute('data-target');
            document.querySelectorAll('.gift-pane').forEach((el) => el.classList.remove('active'));
            const pane = document.getElementById(target);
            if (pane) pane.classList.add('active');
        });

        AOS.init({ duration: 700, once: true });
    </script>
</body>
</html>
