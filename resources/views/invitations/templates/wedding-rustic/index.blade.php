<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $invitation->title }} — {{ $invitation->venue_name }}">
    <title>{{ $invitation->title }} — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Josefin+Sans:wght@300;400;500;600;700&family=Dancing+Script:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --primary: {{ $invitation->custom_colors['primary'] ?? '#8B6914' }};
            --secondary: {{ $invitation->custom_colors['secondary'] ?? '#2c1810' }};
            --accent: {{ $invitation->custom_colors['accent'] ?? '#f4e8d1' }};
            --bg: #1a0f0a;
            --bg-card: #2c1810;
            --text: #f4e8d1;
            --text-muted: #a89070;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Josefin Sans', sans-serif; background: var(--bg); color: var(--text); overflow-x: hidden; }

        /* Cover */
        .cover-section {
            min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center;
            position: relative; overflow: hidden;
            background: linear-gradient(180deg, var(--bg) 0%, #0d0805 60%, var(--bg) 100%);
        }
        .cover-section::before {
            content: ''; position: absolute; inset: 0;
            background: radial-gradient(ellipse at 50% 50%, rgba(139, 105, 20, 0.06) 0%, transparent 70%);
        }
        /* Leaf decoration */
        .leaf { position: absolute; opacity: 0.06; font-size: 80px; color: var(--primary); }
        .leaf-1 { top: 10%; left: 5%; transform: rotate(-30deg); animation: sway 8s ease-in-out infinite; }
        .leaf-2 { top: 15%; right: 8%; transform: rotate(20deg) scaleX(-1); animation: sway 10s ease-in-out infinite reverse; }
        .leaf-3 { bottom: 15%; left: 10%; transform: rotate(15deg); animation: sway 9s ease-in-out infinite; }
        .leaf-4 { bottom: 10%; right: 5%; transform: rotate(-25deg) scaleX(-1); animation: sway 7s ease-in-out infinite reverse; }
        @keyframes sway { 0%, 100% { transform: rotate(var(--r, -30deg)); } 50% { transform: rotate(calc(var(--r, -30deg) + 8deg)); } }

        .cover-frame {
            position: relative; z-index: 10; text-align: center; padding: 3rem 2rem;
            border: 2px solid rgba(139, 105, 20, 0.2); border-radius: 0;
            max-width: 480px;
        }
        .cover-frame::before, .cover-frame::after {
            content: ''; position: absolute; width: 30px; height: 30px; border-color: var(--primary); opacity: 0.4;
        }
        .cover-frame::before { top: -2px; left: -2px; border-top: 3px solid; border-left: 3px solid; }
        .cover-frame::after { bottom: -2px; right: -2px; border-bottom: 3px solid; border-right: 3px solid; }

        .cover-label { font-size: 0.65rem; letter-spacing: 0.5em; text-transform: uppercase; color: var(--text-muted); }
        .cover-names {
            font-family: 'Cormorant Garamond', serif; font-size: clamp(2.5rem, 7vw, 4.5rem);
            font-weight: 700; line-height: 1.2; margin: 1rem 0; color: var(--accent);
        }
        .cover-amp { font-family: 'Dancing Script', cursive; font-size: clamp(2rem, 5vw, 3rem); color: var(--primary); display: block; margin: 0.3rem 0; }
        .cover-date { font-size: 0.8rem; letter-spacing: 0.3em; color: var(--text-muted); margin-top: 1rem; }
        .cover-guest { font-size: 0.85rem; color: var(--text-muted); margin-top: 1rem; }
        .cover-guest strong { color: var(--primary); }

        .open-btn {
            margin-top: 2.5rem; padding: 14px 40px; background: transparent;
            border: 1px solid var(--primary); color: var(--primary);
            font-family: 'Josefin Sans', sans-serif; font-size: 0.75rem; font-weight: 600;
            letter-spacing: 0.25em; text-transform: uppercase; cursor: pointer;
            transition: all 0.4s ease; position: relative; overflow: hidden;
        }
        .open-btn::before { content: ''; position: absolute; inset: 0; background: var(--primary); transform: scaleX(0); transform-origin: left; transition: transform 0.4s ease; }
        .open-btn:hover::before { transform: scaleX(1); }
        .open-btn:hover { color: var(--bg); }
        .open-btn span { position: relative; z-index: 1; }

        /* Content */
        .invitation-content { display: none; }
        .invitation-content.visible { display: block; animation: fadeIn 1s ease; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        .inv-section { padding: 4rem 1.5rem; max-width: 700px; margin: 0 auto; text-align: center; }
        .section-label { font-size: 0.6rem; letter-spacing: 0.5em; text-transform: uppercase; color: var(--primary); font-weight: 600; margin-bottom: 0.8rem; }
        .section-title { font-family: 'Cormorant Garamond', serif; font-size: clamp(1.8rem, 5vw, 2.8rem); font-weight: 700; margin-bottom: 1rem; color: var(--accent); }
        .section-divider { width: 80px; height: 1px; background: var(--primary); margin: 1.2rem auto; opacity: 0.4; }
        .ornament-leaf { color: var(--primary); opacity: 0.3; font-size: 1.5rem; margin: 0.5rem 0; }

        /* Couple */
        .couple-name { font-family: 'Cormorant Garamond', serif; font-size: clamp(2rem, 6vw, 3.5rem); font-weight: 700; color: var(--accent); }
        .couple-amp { font-family: 'Dancing Script', cursive; font-size: clamp(2.5rem, 7vw, 4rem); color: var(--primary); display: block; margin: 0.3rem 0; opacity: 0.7; }

        /* Countdown */
        .countdown { display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap; }
        .countdown-item { background: rgba(139, 105, 20, 0.08); border: 1px solid rgba(139, 105, 20, 0.15); padding: 1.2rem 1rem; min-width: 70px; }
        .countdown-number { font-family: 'Cormorant Garamond', serif; font-size: 1.8rem; font-weight: 700; color: var(--primary); }
        .countdown-label { font-size: 0.55rem; text-transform: uppercase; letter-spacing: 0.15em; color: var(--text-muted); margin-top: 0.2rem; }

        /* Event */
        .event-card {
            background: rgba(139, 105, 20, 0.04); border: 1px solid rgba(139, 105, 20, 0.12);
            padding: 2rem; margin-bottom: 1rem; text-align: left; transition: all 0.3s ease;
        }
        .event-card:hover { background: rgba(139, 105, 20, 0.08); }
        .event-title { font-family: 'Cormorant Garamond', serif; font-size: 1.3rem; font-weight: 700; color: var(--accent); margin-bottom: 0.8rem; }

        /* Gallery */
        .gallery-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 6px; }
        .gallery-item { aspect-ratio: 1; overflow: hidden; background: rgba(139, 105, 20, 0.05); }
        .gallery-item img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s ease; filter: sepia(0.1); }
        .gallery-item:hover img { transform: scale(1.08); }

        /* Forms */
        .form-card { background: rgba(139, 105, 20, 0.04); border: 1px solid rgba(139, 105, 20, 0.1); padding: 2rem; max-width: 450px; margin: 0 auto; }
        .inv-input {
            width: 100%; background: rgba(255,255,255,0.03); border: 1px solid rgba(139, 105, 20, 0.15);
            padding: 12px 16px; color: var(--text); font-family: 'Josefin Sans', sans-serif; font-size: 13px;
            margin-bottom: 0.8rem; transition: all 0.3s ease;
        }
        .inv-input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 2px rgba(139, 105, 20, 0.15); }
        .inv-select { width: 100%; background: rgba(255,255,255,0.03); border: 1px solid rgba(139, 105, 20, 0.15); padding: 12px 16px; color: var(--text); font-family: 'Josefin Sans', sans-serif; font-size: 13px; margin-bottom: 0.8rem; appearance: none; }
        .inv-select option { background: var(--bg); }
        .inv-btn {
            width: 100%; padding: 14px; background: var(--primary); color: var(--bg);
            font-family: 'Josefin Sans', sans-serif; font-weight: 600; font-size: 0.75rem;
            letter-spacing: 0.15em; text-transform: uppercase; border: none; cursor: pointer; transition: all 0.3s ease;
        }
        .inv-btn:hover { opacity: 0.9; transform: translateY(-1px); }

        /* Wishes */
        .wish-item { background: rgba(139, 105, 20, 0.04); border-left: 2px solid var(--primary); padding: 1rem 1.2rem; margin-bottom: 0.6rem; text-align: left; }
        .wish-name { font-weight: 600; font-size: 0.8rem; color: var(--primary); margin-bottom: 0.2rem; }
        .wish-message { font-size: 0.8rem; color: var(--text-muted); line-height: 1.6; }

        /* Maps */
        .maps-container { border: 1px solid rgba(139, 105, 20, 0.15); overflow: hidden; }

        /* Music */
        .music-player {
            position: fixed; bottom: 20px; right: 20px; z-index: 100; width: 44px; height: 44px;
            background: var(--primary); display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: all 0.3s ease;
        }
        .music-player:hover { opacity: 0.8; }
        .music-player.playing { animation: spin 3s linear infinite; }
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

        .inv-footer { padding: 2.5rem 1rem; text-align: center; border-top: 1px solid rgba(139, 105, 20, 0.1); }
        .inv-toast { position: fixed; top: 20px; left: 50%; transform: translateX(-50%) translateY(-120%); background: var(--primary); color: var(--bg); padding: 12px 24px; font-weight: 600; font-size: 0.8rem; z-index: 200; transition: transform 0.5s ease; }
        .inv-toast.show { transform: translateX(-50%) translateY(0); }

        @media (max-width: 640px) { .inv-section { padding: 3rem 1rem; } .countdown { gap: 0.6rem; } .countdown-item { min-width: 60px; padding: 0.8rem; } .countdown-number { font-size: 1.4rem; } }
    </style>
</head>
<body>
    @if(session('success'))
    <div class="inv-toast show" id="invToast">{{ session('success') }}</div>
    <script>setTimeout(() => document.getElementById('invToast').classList.remove('show'), 4000);</script>
    @endif

    <!-- Cover -->
    <section class="cover-section" id="cover">
        <div class="leaf leaf-1">🍃</div>
        <div class="leaf leaf-2">🌿</div>
        <div class="leaf leaf-3">🍂</div>
        <div class="leaf leaf-4">🌾</div>

        <div class="cover-frame" data-aos="fade-up" data-aos-duration="1200">
            <div class="cover-label" data-aos="fade-down" data-aos-delay="300">The Wedding of</div>
            <h1 class="cover-names" data-aos="fade-up" data-aos-delay="500">
                @if($invitation->event_type === 'wedding')
                    {{ $invitation->groom_name ?? '' }}
                    <span class="cover-amp">&</span>
                    {{ $invitation->bride_name ?? '' }}
                @else
                    {{ $invitation->title }}
                @endif
            </h1>
            <p class="cover-date" data-aos="fade-up" data-aos-delay="700">{{ $invitation->event_date->translatedFormat('l, d F Y') }}</p>
            @if(isset($guest))
            <p class="cover-guest" data-aos="fade-up" data-aos-delay="800">Kepada Yth. <strong>{{ $guest->name }}</strong></p>
            @endif
            <button class="open-btn" data-aos="fade-up" data-aos-delay="1000" onclick="openInvitation()">
                <span><i class="fas fa-envelope-open mr-2"></i> Buka Undangan</span>
            </button>
        </div>
    </section>

    <!-- Content -->
    <div class="invitation-content" id="invitationContent">
        @if($invitation->opening_text)
        <section class="inv-section">
            <div class="ornament-leaf" data-aos="fade-up">✦</div>
            <p class="text-sm leading-relaxed max-w-md mx-auto" style="color: var(--text-muted);" data-aos="fade-up" data-aos-delay="100">{{ $invitation->opening_text }}</p>
        </section>
        @endif

        @if($invitation->event_type === 'wedding')
        <section class="inv-section">
            <div class="section-label" data-aos="fade-up">Bride & Groom</div>
            <div class="section-divider" data-aos="fade-up" data-aos-delay="100"></div>
            <div data-aos="fade-up" data-aos-delay="200">
                <span class="couple-name">{{ $invitation->groom_name ?? 'Groom' }}</span>
                <span class="couple-amp">&</span>
                <span class="couple-name">{{ $invitation->bride_name ?? 'Bride' }}</span>
            </div>
        </section>
        @endif

        <section class="inv-section">
            <div class="section-label" data-aos="fade-up">Save The Date</div>
            <h2 class="section-title" data-aos="fade-up" data-aos-delay="100">Countdown</h2>
            <div class="section-divider" data-aos="fade-up" data-aos-delay="150"></div>
            <div class="countdown" data-aos="fade-up" data-aos-delay="200" id="countdown" data-date="{{ $invitation->event_date->format('Y-m-d') }}T{{ \Carbon\Carbon::parse($invitation->event_time)->format('H:i:s') }}">
                <div class="countdown-item"><div class="countdown-number" id="cd-days">0</div><div class="countdown-label">Hari</div></div>
                <div class="countdown-item"><div class="countdown-number" id="cd-hours">0</div><div class="countdown-label">Jam</div></div>
                <div class="countdown-item"><div class="countdown-number" id="cd-minutes">0</div><div class="countdown-label">Menit</div></div>
                <div class="countdown-item"><div class="countdown-number" id="cd-seconds">0</div><div class="countdown-label">Detik</div></div>
            </div>
        </section>

        <section class="inv-section">
            <div class="section-label" data-aos="fade-up">Acara</div>
            <h2 class="section-title" data-aos="fade-up" data-aos-delay="100">Jadwal</h2>
            <div class="section-divider" data-aos="fade-up" data-aos-delay="150"></div>
            <div class="event-card" data-aos="fade-up" data-aos-delay="200">
                <div class="event-title">{{ $invitation->venue_name }}</div>
                <div class="flex flex-col gap-1 text-sm" style="color: var(--text-muted);">
                    <p><i class="fas fa-calendar-alt mr-2" style="color: var(--primary); width: 16px;"></i>{{ $invitation->event_date->translatedFormat('l, d F Y') }}</p>
                    <p><i class="fas fa-clock mr-2" style="color: var(--primary); width: 16px;"></i>{{ \Carbon\Carbon::parse($invitation->event_time)->format('H:i') }} WIB</p>
                    <p><i class="fas fa-map-marker-alt mr-2" style="color: var(--primary); width: 16px;"></i>{{ $invitation->venue_address }}</p>
                </div>
            </div>
            @foreach($invitation->events as $event)
            <div class="event-card" data-aos="fade-up" data-aos-delay="{{ 250 + $loop->index * 100 }}">
                <div class="event-title">{{ $event->event_name }} — {{ $event->venue_name }}</div>
                <div class="flex flex-col gap-1 text-sm" style="color: var(--text-muted);">
                    <p><i class="fas fa-calendar-alt mr-2" style="color: var(--primary);"></i>{{ $event->event_date->format('d F Y') }}</p>
                    <p><i class="fas fa-clock mr-2" style="color: var(--primary);"></i>{{ $event->event_time }}</p>
                    <p><i class="fas fa-map-marker-alt mr-2" style="color: var(--primary);"></i>{{ $event->venue_address }}</p>
                </div>
            </div>
            @endforeach
        </section>

        @if($invitation->photos->count())
        <section class="inv-section">
            <div class="section-label" data-aos="fade-up">Gallery</div>
            <h2 class="section-title" data-aos="fade-up" data-aos-delay="100">Our Story</h2>
            <div class="section-divider" data-aos="fade-up" data-aos-delay="150"></div>
            <div class="gallery-grid">
                @foreach($invitation->photos as $photo)
                <div class="gallery-item" data-aos="zoom-in" data-aos-delay="{{ $loop->index * 80 }}">
                    <img src="{{ asset('storage/' . $photo->file_path) }}" alt="{{ $photo->caption ?? 'Photo' }}" loading="lazy">
                </div>
                @endforeach
            </div>
        </section>
        @endif

        @if($invitation->google_maps_url)
        <section class="inv-section">
            <div class="section-label" data-aos="fade-up">Lokasi</div>
            <div class="section-divider" data-aos="fade-up" data-aos-delay="100"></div>
            <div class="maps-container" data-aos="fade-up" data-aos-delay="200">
                <iframe src="{{ str_replace('/maps/', '/maps/embed/', $invitation->google_maps_url) }}" width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
            </div>
            <a href="{{ $invitation->google_maps_url }}" target="_blank" class="inline-flex items-center gap-2 mt-3 px-5 py-2 text-sm font-semibold transition" style="border: 1px solid var(--primary); color: var(--primary);" data-aos="fade-up" data-aos-delay="300">
                <i class="fas fa-map-marker-alt"></i> Google Maps
            </a>
        </section>
        @endif

        <section class="inv-section" id="rsvp">
            <div class="section-label" data-aos="fade-up">Konfirmasi</div>
            <h2 class="section-title" data-aos="fade-up" data-aos-delay="100">RSVP</h2>
            <div class="section-divider" data-aos="fade-up" data-aos-delay="150"></div>
            <div class="form-card" data-aos="fade-up" data-aos-delay="200">
                <form method="POST" action="{{ route('invitation.rsvp', $invitation->slug) }}">
                    @csrf
                    @if(isset($guest))
                        <input type="hidden" name="guest_id" value="{{ $guest->id }}"><input type="hidden" name="name" value="{{ $guest->name }}">
                        <p class="text-sm mb-3" style="color: var(--primary);">Halo, <strong>{{ $guest->name }}</strong></p>
                    @else
                        <input type="text" name="name" class="inv-input" placeholder="Nama Lengkap" required>
                    @endif
                    <input type="text" name="phone" class="inv-input" placeholder="No. HP (opsional)">
                    <select name="status" class="inv-select" required><option value="attending">✅ Hadir</option><option value="maybe">🤔 Mungkin</option><option value="not_attending">❌ Tidak Hadir</option></select>
                    <input type="number" name="pax" class="inv-input" value="1" min="1" max="10">
                    <textarea name="message" class="inv-input" rows="3" placeholder="Ucapan & doa" style="resize: none;"></textarea>
                    <button type="submit" class="inv-btn"><i class="fas fa-paper-plane mr-2"></i> Kirim</button>
                </form>
            </div>
        </section>

        <section class="inv-section" id="wishes">
            <div class="section-label" data-aos="fade-up">Ucapan</div>
            <h2 class="section-title" data-aos="fade-up" data-aos-delay="100">Ucapan & Doa</h2>
            <div class="section-divider" data-aos="fade-up" data-aos-delay="150"></div>
            <div class="form-card mb-6" data-aos="fade-up" data-aos-delay="200">
                <form method="POST" action="{{ route('invitation.wish', $invitation->slug) }}">
                    @csrf
                    <input type="text" name="name" class="inv-input" placeholder="Nama Anda" value="{{ $guest->name ?? '' }}" required>
                    <textarea name="message" class="inv-input" rows="3" placeholder="Tulis ucapan..." style="resize: none;" required></textarea>
                    <button type="submit" class="inv-btn"><i class="fas fa-heart mr-2"></i> Kirim Ucapan</button>
                </form>
            </div>
            <div style="max-width: 450px; margin: 0 auto;">
                @foreach($invitation->wishes as $wish)
                <div class="wish-item" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                    <div class="wish-name">{{ $wish->name }}</div>
                    <div class="wish-message">{{ $wish->message }}</div>
                </div>
                @endforeach
            </div>
        </section>

        @if($invitation->closing_text)
        <section class="inv-section">
            <div class="section-divider" data-aos="fade-up"></div>
            <p class="text-sm leading-relaxed max-w-md mx-auto italic" style="color: var(--text-muted); font-family: 'Cormorant Garamond', serif; font-size: 1rem;" data-aos="fade-up" data-aos-delay="100">{{ $invitation->closing_text }}</p>
            <div class="ornament-leaf mt-4" data-aos="fade-up" data-aos-delay="200">🍃</div>
        </section>
        @endif

        <footer class="inv-footer"><p class="text-xs" style="color: var(--text-muted); opacity: 0.5;">Made with ♥ by {{ config('app.name') }}</p></footer>
    </div>

    @if($invitation->music_url)
    <div class="music-player" id="musicPlayer" onclick="toggleMusic()"><i class="fas fa-music" style="color: var(--bg);"></i></div>
    <audio id="bgMusic" loop><source src="{{ asset('storage/' . $invitation->music_url) }}" type="audio/mpeg"></audio>
    @endif

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        function openInvitation() {
            document.getElementById('cover').style.display = 'none';
            document.getElementById('invitationContent').classList.add('visible');
            AOS.init({ duration: 800, once: true, offset: 50 });
            const a = document.getElementById('bgMusic'); if (a) a.play().then(() => document.getElementById('musicPlayer')?.classList.add('playing')).catch(() => {});
            startCountdown();
        }
        function startCountdown() { const el = document.getElementById('countdown'); if (!el) return; const t = new Date(el.dataset.date).getTime(); setInterval(() => { const d = t - Date.now(); if (d > 0) { document.getElementById('cd-days').textContent = Math.floor(d / 864e5); document.getElementById('cd-hours').textContent = Math.floor((d % 864e5) / 36e5); document.getElementById('cd-minutes').textContent = Math.floor((d % 36e5) / 6e4); document.getElementById('cd-seconds').textContent = Math.floor((d % 6e4) / 1e3); } }, 1000); }
        function toggleMusic() { const a = document.getElementById('bgMusic'), p = document.getElementById('musicPlayer'); a.paused ? (a.play(), p.classList.add('playing')) : (a.pause(), p.classList.remove('playing')); }
        AOS.init({ duration: 800, once: true });
    </script>
</body>
</html>
