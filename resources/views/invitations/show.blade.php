<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $invitation->title }} — {{ $invitation->venue_name }}">
    <title>{{ $invitation->title }} — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;0,800;1,400;1,600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Great+Vibes&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --primary: {{ $invitation->custom_colors['primary'] ?? '#D4AF37' }};
            --secondary: {{ $invitation->custom_colors['secondary'] ?? '#1a1a2e' }};
            --accent: {{ $invitation->custom_colors['accent'] ?? '#f5f0e1' }};
            --text: #f1f5f9;
            --text-muted: #94a3b8;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--secondary);
            color: var(--text);
            overflow-x: hidden;
        }

        /* ===== COVER SECTION ===== */
        .cover-section {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            background: linear-gradient(180deg, var(--secondary) 0%, #0f0f23 100%);
        }
        .cover-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse at 20% 50%, rgba(212, 175, 55, 0.08) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 30%, rgba(212, 175, 55, 0.05) 0%, transparent 50%),
                radial-gradient(ellipse at 50% 80%, rgba(168, 85, 247, 0.04) 0%, transparent 40%);
            animation: coverAura 10s ease-in-out infinite;
        }
        @keyframes coverAura {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        /* Particle background */
        .particles {
            position: absolute;
            inset: 0;
            overflow: hidden;
        }
        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: var(--primary);
            border-radius: 50%;
            opacity: 0;
            animation: particleFloat 8s ease-in-out infinite;
        }
        @keyframes particleFloat {
            0% { opacity: 0; transform: translateY(100vh) scale(0); }
            20% { opacity: 0.6; }
            80% { opacity: 0.3; }
            100% { opacity: 0; transform: translateY(-20vh) scale(1); }
        }

        .cover-content {
            position: relative;
            z-index: 10;
            text-align: center;
            padding: 2rem;
        }
        .cover-ornament {
            font-size: 2rem;
            color: var(--primary);
            opacity: 0.6;
            letter-spacing: 0.5em;
        }
        .cover-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2.5rem, 7vw, 5rem);
            font-weight: 700;
            line-height: 1.2;
            margin: 1.5rem 0;
            background: linear-gradient(135deg, var(--primary) 0%, #f5e6a3 50%, var(--primary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-size: 200% auto;
            animation: shimmer 4s ease-in-out infinite;
        }
        @keyframes shimmer {
            0%, 100% { background-position: 0% center; }
            50% { background-position: 200% center; }
        }
        .cover-subtitle {
            font-family: 'Great Vibes', cursive;
            font-size: clamp(1.5rem, 4vw, 2.5rem);
            color: var(--primary);
            opacity: 0.8;
        }
        .cover-date {
            margin-top: 2rem;
            font-size: 0.9rem;
            letter-spacing: 0.3em;
            text-transform: uppercase;
            color: var(--text-muted);
        }
        .open-btn {
            margin-top: 3rem;
            padding: 16px 48px;
            background: transparent;
            border: 2px solid var(--primary);
            color: var(--primary);
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.85rem;
            font-weight: 700;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            border-radius: 100px;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        .open-btn::before {
            content: '';
            position: absolute;
            inset: 0;
            background: var(--primary);
            transform: translateY(100%);
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .open-btn:hover::before { transform: translateY(0); }
        .open-btn:hover { color: var(--secondary); }
        .open-btn span { position: relative; z-index: 1; }

        /* ===== MAIN CONTENT ===== */
        .invitation-content {
            display: none;
        }
        .invitation-content.visible {
            display: block;
            animation: revealContent 1.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        @keyframes revealContent {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Section styling */
        .inv-section {
            padding: 5rem 1.5rem;
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
        }
        .section-label {
            font-size: 0.7rem;
            letter-spacing: 0.4em;
            text-transform: uppercase;
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 1rem;
        }
        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.8rem, 5vw, 3rem);
            font-weight: 700;
            margin-bottom: 1.5rem;
        }
        .section-divider {
            width: 60px;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--primary), transparent);
            margin: 1.5rem auto;
        }

        /* Couple Section */
        .couple-names {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2rem, 6vw, 3.5rem);
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary), #f5e6a3);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .couple-ampersand {
            font-family: 'Great Vibes', cursive;
            font-size: clamp(3rem, 8vw, 5rem);
            color: var(--primary);
            opacity: 0.6;
            display: block;
            margin: 0.5rem 0;
        }

        /* Countdown */
        .countdown {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            flex-wrap: wrap;
        }
        .countdown-item {
            background: rgba(212, 175, 55, 0.08);
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 16px;
            padding: 1.5rem 1.2rem;
            min-width: 80px;
            text-align: center;
        }
        .countdown-number {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary);
        }
        .countdown-label {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: var(--text-muted);
            margin-top: 0.3rem;
        }

        /* Event Cards */
        .event-card {
            background: rgba(212, 175, 55, 0.05);
            border: 1px solid rgba(212, 175, 55, 0.15);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }
        .event-card:hover {
            background: rgba(212, 175, 55, 0.08);
            transform: translateY(-4px);
        }

        /* Gallery */
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 0.8rem;
        }
        .gallery-item {
            border-radius: 16px;
            overflow: hidden;
            aspect-ratio: 1;
            background: rgba(212, 175, 55, 0.05);
        }
        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .gallery-item:hover img { transform: scale(1.1); }

        /* RSVP Form */
        .rsvp-form {
            background: rgba(212, 175, 55, 0.03);
            border: 1px solid rgba(212, 175, 55, 0.12);
            border-radius: 24px;
            padding: 2.5rem;
            max-width: 500px;
            margin: 0 auto;
        }
        .inv-input {
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(212, 175, 55, 0.15);
            border-radius: 12px;
            padding: 14px 18px;
            color: var(--text);
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 14px;
            transition: all 0.3s ease;
            margin-bottom: 1rem;
        }
        .inv-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.15);
        }
        .inv-select {
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(212, 175, 55, 0.15);
            border-radius: 12px;
            padding: 14px 18px;
            color: var(--text);
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 14px;
            margin-bottom: 1rem;
            appearance: none;
        }
        .inv-select option { background: var(--secondary); }
        .inv-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, var(--primary), #c9a227);
            color: var(--secondary);
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 700;
            font-size: 0.85rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .inv-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(212, 175, 55, 0.3); }

        /* Wishes */
        .wish-item {
            background: rgba(212, 175, 55, 0.04);
            border: 1px solid rgba(212, 175, 55, 0.1);
            border-radius: 16px;
            padding: 1.2rem;
            margin-bottom: 0.8rem;
            text-align: left;
        }
        .wish-name {
            font-weight: 700;
            font-size: 0.85rem;
            color: var(--primary);
            margin-bottom: 0.3rem;
        }
        .wish-message {
            font-size: 0.85rem;
            color: var(--text-muted);
            line-height: 1.6;
        }

        /* Maps */
        .maps-container {
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid rgba(212, 175, 55, 0.15);
        }

        /* Music Player */
        .music-player {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 100;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), #c9a227);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 20px rgba(212, 175, 55, 0.3);
            transition: all 0.3s ease;
            animation: pulse 2s ease-in-out infinite;
        }
        .music-player:hover { transform: scale(1.1); }
        .music-player.playing { animation: rotate 3s linear infinite; }
        @keyframes pulse {
            0%, 100% { box-shadow: 0 4px 20px rgba(212, 175, 55, 0.3); }
            50% { box-shadow: 0 4px 30px rgba(212, 175, 55, 0.5); }
        }
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Footer */
        .inv-footer {
            padding: 3rem 1.5rem;
            text-align: center;
            border-top: 1px solid rgba(212, 175, 55, 0.1);
        }

        /* Toast */
        .inv-toast {
            position: fixed;
            top: 24px;
            left: 50%;
            transform: translateX(-50%) translateY(-100%);
            background: linear-gradient(135deg, var(--primary), #c9a227);
            color: var(--secondary);
            padding: 14px 28px;
            border-radius: 100px;
            font-weight: 700;
            font-size: 0.85rem;
            z-index: 200;
            transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .inv-toast.show { transform: translateX(-50%) translateY(0); }

        /* Responsive */
        @media (max-width: 640px) {
            .inv-section { padding: 3rem 1rem; }
            .countdown { gap: 0.8rem; }
            .countdown-item { min-width: 65px; padding: 1rem; }
            .countdown-number { font-size: 1.5rem; }
        }
    </style>
</head>
<body>
    {{-- Toast --}}
    @if(session('success'))
    <div class="inv-toast show" id="invToast">{{ session('success') }}</div>
    <script>setTimeout(() => document.getElementById('invToast').classList.remove('show'), 4000);</script>
    @endif

    {{-- Cover / Opening --}}
    <section class="cover-section" id="cover">
        <div class="particles">
            @for($i = 0; $i < 20; $i++)
            <div class="particle" style="left: {{ rand(5, 95) }}%; animation-delay: {{ $i * 0.4 }}s; animation-duration: {{ rand(6, 12) }}s;"></div>
            @endfor
        </div>
        <div class="cover-content">
            <div class="cover-ornament" data-aos="fade-down" data-aos-duration="1000">✦ ✦ ✦</div>
            <p class="cover-subtitle" data-aos="fade-up" data-aos-delay="300">The Wedding of</p>
            <h1 class="cover-title" data-aos="zoom-in" data-aos-delay="600">
                @if($invitation->event_type === 'wedding')
                    {{ $invitation->groom_name ?? '' }}<br>
                    <span class="couple-ampersand">&</span>
                    {{ $invitation->bride_name ?? '' }}
                @else
                    {{ $invitation->title }}
                @endif
            </h1>
            <p class="cover-date" data-aos="fade-up" data-aos-delay="900">
                {{ $invitation->event_date->translatedFormat('l, d F Y') }}
            </p>
            @if(isset($guest))
            <p class="text-sm text-[var(--text-muted)] mt-4" data-aos="fade-up" data-aos-delay="1000">
                Kepada Yth. <strong class="text-[var(--primary)]">{{ $guest->name }}</strong>
            </p>
            @endif
            <button class="open-btn" data-aos="fade-up" data-aos-delay="1200" onclick="openInvitation()">
                <span><i class="fas fa-envelope-open mr-2"></i> Buka Undangan</span>
            </button>
        </div>
    </section>

    {{-- Main Invitation Content --}}
    <div class="invitation-content" id="invitationContent">

        {{-- Opening Text --}}
        @if($invitation->opening_text)
        <section class="inv-section">
            <div class="section-label" data-aos="fade-up">Bismillahirrahmanirrahim</div>
            <div class="section-divider" data-aos="fade-up" data-aos-delay="100"></div>
            <p class="text-[var(--text-muted)] leading-relaxed max-w-lg mx-auto" data-aos="fade-up" data-aos-delay="200">
                {{ $invitation->opening_text }}
            </p>
        </section>
        @endif

        {{-- Couple Section --}}
        @if($invitation->event_type === 'wedding')
        <section class="inv-section">
            <div class="section-label" data-aos="fade-up">The Bride & Groom</div>
            <div class="section-divider" data-aos="fade-up" data-aos-delay="100"></div>
            <div data-aos="zoom-in" data-aos-delay="200">
                <span class="couple-names">{{ $invitation->groom_name ?? 'Groom' }}</span>
                <span class="couple-ampersand">&</span>
                <span class="couple-names">{{ $invitation->bride_name ?? 'Bride' }}</span>
            </div>
        </section>
        @endif

        {{-- Countdown --}}
        <section class="inv-section">
            <div class="section-label" data-aos="fade-up">Menuju Hari Bahagia</div>
            <h2 class="section-title" data-aos="fade-up" data-aos-delay="100">Countdown</h2>
            <div class="section-divider" data-aos="fade-up" data-aos-delay="150"></div>
            <div class="countdown" data-aos="fade-up" data-aos-delay="200" id="countdown"
                 data-date="{{ $invitation->event_date->format('Y-m-d') }}T{{ \Carbon\Carbon::parse($invitation->event_time)->format('H:i:s') }}">
                <div class="countdown-item">
                    <div class="countdown-number" id="cd-days">0</div>
                    <div class="countdown-label">Hari</div>
                </div>
                <div class="countdown-item">
                    <div class="countdown-number" id="cd-hours">0</div>
                    <div class="countdown-label">Jam</div>
                </div>
                <div class="countdown-item">
                    <div class="countdown-number" id="cd-minutes">0</div>
                    <div class="countdown-label">Menit</div>
                </div>
                <div class="countdown-item">
                    <div class="countdown-number" id="cd-seconds">0</div>
                    <div class="countdown-label">Detik</div>
                </div>
            </div>
        </section>

        {{-- Event Details --}}
        <section class="inv-section">
            <div class="section-label" data-aos="fade-up">Acara</div>
            <h2 class="section-title" data-aos="fade-up" data-aos-delay="100">Jadwal Acara</h2>
            <div class="section-divider" data-aos="fade-up" data-aos-delay="150"></div>

            {{-- Main Event --}}
            <div class="event-card" data-aos="fade-up" data-aos-delay="200">
                <div class="text-sm text-[var(--primary)] font-bold mb-2 uppercase tracking-wider">{{ ucfirst($invitation->event_type) }}</div>
                <h3 class="font-bold text-xl mb-3" style="font-family: 'Playfair Display', serif;">{{ $invitation->venue_name }}</h3>
                <div class="flex flex-col gap-2 text-sm text-[var(--text-muted)]">
                    <p><i class="fas fa-calendar-alt text-[var(--primary)] mr-2 w-4"></i>{{ $invitation->event_date->translatedFormat('l, d F Y') }}</p>
                    <p><i class="fas fa-clock text-[var(--primary)] mr-2 w-4"></i>{{ \Carbon\Carbon::parse($invitation->event_time)->format('H:i') }} WIB</p>
                    <p><i class="fas fa-map-marker-alt text-[var(--primary)] mr-2 w-4"></i>{{ $invitation->venue_address }}</p>
                </div>
            </div>

            {{-- Sub Events --}}
            @foreach($invitation->events as $event)
            <div class="event-card" data-aos="fade-up" data-aos-delay="{{ 250 + $loop->index * 100 }}">
                <div class="text-sm text-[var(--primary)] font-bold mb-2 uppercase tracking-wider">{{ $event->event_name }}</div>
                <h3 class="font-bold text-lg mb-2" style="font-family: 'Playfair Display', serif;">{{ $event->venue_name }}</h3>
                <div class="flex flex-col gap-2 text-sm text-[var(--text-muted)]">
                    <p><i class="fas fa-calendar-alt text-[var(--primary)] mr-2"></i>{{ $event->event_date->format('d F Y') }}</p>
                    <p><i class="fas fa-clock text-[var(--primary)] mr-2"></i>{{ $event->event_time }}</p>
                    <p><i class="fas fa-map-marker-alt text-[var(--primary)] mr-2"></i>{{ $event->venue_address }}</p>
                </div>
            </div>
            @endforeach
        </section>

        {{-- Gallery --}}
        @if($invitation->photos->count())
        <section class="inv-section">
            <div class="section-label" data-aos="fade-up">Galeri</div>
            <h2 class="section-title" data-aos="fade-up" data-aos-delay="100">Our Moments</h2>
            <div class="section-divider" data-aos="fade-up" data-aos-delay="150"></div>
            <div class="gallery-grid">
                @foreach($invitation->photos as $photo)
                <div class="gallery-item" data-aos="zoom-in" data-aos-delay="{{ $loop->index * 100 }}">
                    <img src="{{ asset('storage/' . $photo->file_path) }}" alt="{{ $photo->caption ?? 'Photo' }}" loading="lazy" decoding="async">
                </div>
                @endforeach
            </div>
        </section>
        @endif

        {{-- Google Maps --}}
        @if($invitation->google_maps_url)
        <section class="inv-section">
            <div class="section-label" data-aos="fade-up">Lokasi</div>
            <h2 class="section-title" data-aos="fade-up" data-aos-delay="100">Peta Lokasi</h2>
            <div class="section-divider" data-aos="fade-up" data-aos-delay="150"></div>
            <div class="maps-container" data-aos="fade-up" data-aos-delay="200">
                <iframe src="{{ str_replace('/maps/', '/maps/embed/', $invitation->google_maps_url) }}"
                        width="100%" height="350" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
            </div>
            <a href="{{ $invitation->google_maps_url }}" target="_blank"
               class="inline-flex items-center gap-2 mt-4 px-6 py-3 rounded-full border border-[var(--primary)] text-[var(--primary)] text-sm font-semibold hover:bg-[rgba(212,175,55,0.1)] transition"
               data-aos="fade-up" data-aos-delay="300">
                <i class="fas fa-map-marker-alt"></i> Buka di Google Maps
            </a>
        </section>
        @endif

        {{-- RSVP --}}
        <section class="inv-section" id="rsvp">
            <div class="section-label" data-aos="fade-up">Konfirmasi</div>
            <h2 class="section-title" data-aos="fade-up" data-aos-delay="100">RSVP</h2>
            <div class="section-divider" data-aos="fade-up" data-aos-delay="150"></div>
            <p class="text-[var(--text-muted)] text-sm mb-6" data-aos="fade-up" data-aos-delay="200">
                Konfirmasi kehadiran Anda untuk hari bahagia kami
            </p>

            <div class="rsvp-form" data-aos="fade-up" data-aos-delay="250">
                <form method="POST" action="{{ route('invitation.rsvp', $invitation->slug) }}">
                    @csrf
                    @if(isset($guest))
                        <input type="hidden" name="guest_id" value="{{ $guest->id }}">
                        <input type="hidden" name="name" value="{{ $guest->name }}">
                        <p class="text-sm mb-4 text-[var(--primary)]">Halo, <strong>{{ $guest->name }}</strong></p>
                    @else
                        <input type="text" name="name" class="inv-input" placeholder="Nama Lengkap" required>
                    @endif
                    <input type="text" name="phone" class="inv-input" placeholder="No. HP (opsional)">
                    <select name="status" class="inv-select" required>
                        <option value="attending">✅ Hadir</option>
                        <option value="maybe">🤔 Belum Pasti</option>
                        <option value="not_attending">❌ Tidak Hadir</option>
                    </select>
                    <input type="number" name="pax" class="inv-input" value="1" min="1" max="10" placeholder="Jumlah yang hadir">
                    <textarea name="message" class="inv-input" rows="3" placeholder="Ucapan & doa (opsional)" style="resize: none;"></textarea>
                    <button type="submit" class="inv-btn">
                        <i class="fas fa-paper-plane mr-2"></i> Kirim Konfirmasi
                    </button>
                </form>
            </div>
        </section>

        {{-- Wishes --}}
        <section class="inv-section" id="wishes">
            <div class="section-label" data-aos="fade-up">Ucapan</div>
            <h2 class="section-title" data-aos="fade-up" data-aos-delay="100">Ucapan & Doa</h2>
            <div class="section-divider" data-aos="fade-up" data-aos-delay="150"></div>

            {{-- Wish Form --}}
            <div class="rsvp-form mb-6" data-aos="fade-up" data-aos-delay="200">
                <form method="POST" action="{{ route('invitation.wish', $invitation->slug) }}">
                    @csrf
                    <input type="text" name="name" class="inv-input" placeholder="Nama Anda" value="{{ $guest->name ?? '' }}" required>
                    <textarea name="message" class="inv-input" rows="3" placeholder="Tulis ucapan & doa restu..." style="resize: none;" required></textarea>
                    <button type="submit" class="inv-btn">
                        <i class="fas fa-heart mr-2"></i> Kirim Ucapan
                    </button>
                </form>
            </div>

            {{-- Wish List --}}
            <div class="max-w-500 mx-auto" style="max-width: 500px;">
                @foreach($invitation->wishes as $wish)
                <div class="wish-item" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                    <div class="wish-name">{{ $wish->name }}</div>
                    <div class="wish-message">{{ $wish->message }}</div>
                    <div class="text-xs text-slate-600 mt-2">{{ $wish->created_at->diffForHumans() }}</div>
                </div>
                @endforeach
            </div>
        </section>

        {{-- Closing --}}
        @if($invitation->closing_text)
        <section class="inv-section">
            <div class="section-divider" data-aos="fade-up"></div>
            <p class="text-[var(--text-muted)] leading-relaxed max-w-lg mx-auto italic" data-aos="fade-up" data-aos-delay="100"
               style="font-family: 'Playfair Display', serif;">
                {{ $invitation->closing_text }}
            </p>
            <div class="cover-ornament mt-6" data-aos="fade-up" data-aos-delay="200">✦</div>
        </section>
        @endif

        {{-- Save to Calendar --}}
        <section class="inv-section" style="padding-top: 0;">
            <a href="https://calendar.google.com/calendar/render?action=TEMPLATE&text={{ urlencode($invitation->title) }}&dates={{ $invitation->event_date->format('Ymd') }}T{{ \Carbon\Carbon::parse($invitation->event_time)->format('His') }}/{{ $invitation->event_date->format('Ymd') }}T{{ \Carbon\Carbon::parse($invitation->event_end_time ?? $invitation->event_time)->addHours(2)->format('His') }}&location={{ urlencode($invitation->venue_name . ', ' . $invitation->venue_address) }}"
               target="_blank"
               class="inline-flex items-center gap-2 px-6 py-3 rounded-full border border-[var(--primary)] text-[var(--primary)] text-sm font-semibold hover:bg-[rgba(212,175,55,0.1)] transition"
               data-aos="fade-up">
                <i class="fas fa-calendar-plus"></i> Simpan ke Google Calendar
            </a>
        </section>

        {{-- Footer --}}
        <footer class="inv-footer">
            <p class="text-xs text-slate-600">
                Made with <span class="text-[var(--primary)]">♥</span> by <strong>{{ config('app.name') }}</strong>
            </p>
        </footer>
    </div>

    {{-- Music Player --}}
    @if($invitation->music_url)
    <div class="music-player" id="musicPlayer" onclick="toggleMusic()">
        <i class="fas fa-music text-[var(--secondary)]" id="musicIcon"></i>
    </div>
    <audio id="bgMusic" loop>
        <source src="{{ $invitation->music_signed_url ?? asset('storage/' . $invitation->music_url) }}" type="audio/mpeg">
    </audio>
    @endif

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Open invitation
        function openInvitation() {
            document.getElementById('cover').style.display = 'none';
            document.getElementById('invitationContent').classList.add('visible');
            AOS.init({ duration: 800, once: true, offset: 50 });

            // Auto play music
            const audio = document.getElementById('bgMusic');
            if (audio) {
                audio.play().then(() => {
                    document.getElementById('musicPlayer').classList.add('playing');
                }).catch(() => {});
            }

            // Start countdown
            startCountdown();
        }

        // Countdown
        function startCountdown() {
            const el = document.getElementById('countdown');
            if (!el) return;
            const targetDate = new Date(el.dataset.date).getTime();

            setInterval(() => {
                const now = new Date().getTime();
                const diff = targetDate - now;

                if (diff > 0) {
                    document.getElementById('cd-days').textContent = Math.floor(diff / (1000 * 60 * 60 * 24));
                    document.getElementById('cd-hours').textContent = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    document.getElementById('cd-minutes').textContent = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    document.getElementById('cd-seconds').textContent = Math.floor((diff % (1000 * 60)) / 1000);
                }
            }, 1000);
        }

        // Music toggle
        function toggleMusic() {
            const audio = document.getElementById('bgMusic');
            const player = document.getElementById('musicPlayer');
            if (audio.paused) {
                audio.play();
                player.classList.add('playing');
            } else {
                audio.pause();
                player.classList.remove('playing');
            }
        }

        // Init AOS for cover
        AOS.init({ duration: 800, once: true });
    </script>
</body>
</html>
