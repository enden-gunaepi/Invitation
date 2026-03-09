<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $invitation->title }} — {{ $invitation->venue_name }}">
    <title>{{ $invitation->title }} — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&family=Pacifico&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --primary: {{ $invitation->custom_colors['primary'] ?? '#FF6B6B' }};
            --secondary: {{ $invitation->custom_colors['secondary'] ?? '#4ECDC4' }};
            --accent: {{ $invitation->custom_colors['accent'] ?? '#FFE66D' }};
            --purple: #A855F7;
            --pink: #EC4899;
            --bg: #0f0a1a;
            --bg-card: rgba(255,255,255,0.04);
            --text: #f8fafc;
            --text-muted: #a1a1aa;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: var(--bg); color: var(--text); overflow-x: hidden; }

        /* Cover */
        .cover-section {
            min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center;
            position: relative; overflow: hidden;
            background: linear-gradient(135deg, #0f0a1a 0%, #1a0a2e 50%, #0a1628 100%);
        }
        .cover-section::before { content: ''; position: absolute; inset: 0; background: radial-gradient(circle at 30% 40%, rgba(236,72,153,0.12) 0%, transparent 50%), radial-gradient(circle at 70% 60%, rgba(78,205,196,0.1) 0%, transparent 50%), radial-gradient(circle at 50% 80%, rgba(168,85,247,0.08) 0%, transparent 40%); }

        /* Confetti */
        .confetti-container { position: absolute; inset: 0; overflow: hidden; pointer-events: none; }
        .confetti {
            position: absolute; width: 10px; height: 10px; opacity: 0;
            animation: confettiFall linear infinite;
        }
        .confetti:nth-child(odd) { border-radius: 50%; }
        .confetti:nth-child(3n) { width: 8px; height: 14px; border-radius: 2px; }
        @keyframes confettiFall {
            0% { opacity: 0; transform: translateY(-10vh) rotate(0deg); }
            10% { opacity: 0.8; }
            90% { opacity: 0.5; }
            100% { opacity: 0; transform: translateY(110vh) rotate(720deg); }
        }

        .cover-content { position: relative; z-index: 10; text-align: center; padding: 2rem; }
        .cover-emoji { font-size: 4rem; margin-bottom: 1rem; animation: bounce 2s ease-in-out infinite; }
        @keyframes bounce { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-15px); } }

        .cover-title {
            font-family: 'Pacifico', cursive; font-size: clamp(2.5rem, 8vw, 5rem);
            background: linear-gradient(135deg, var(--primary), var(--pink), var(--purple), var(--secondary));
            background-size: 300% 300%; -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            animation: gradient 6s ease infinite; line-height: 1.3;
        }
        @keyframes gradient { 0% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } 100% { background-position: 0% 50%; } }

        .cover-subtitle { font-size: 1rem; font-weight: 400; color: var(--text-muted); margin-top: 0.5rem; }
        .cover-date {
            margin-top: 1.5rem; display: inline-block; padding: 8px 24px; border-radius: 100px;
            background: linear-gradient(135deg, rgba(255,107,107,0.15), rgba(78,205,196,0.15));
            border: 1px solid rgba(255,107,107,0.2); font-size: 0.8rem; font-weight: 600; color: var(--primary);
        }
        .cover-guest { margin-top: 1rem; font-size: 0.85rem; color: var(--text-muted); }
        .cover-guest strong { color: var(--secondary); }

        .open-btn {
            margin-top: 2.5rem; padding: 16px 48px; border: none; border-radius: 100px;
            background: linear-gradient(135deg, var(--primary), var(--pink));
            color: white; font-family: 'Poppins', sans-serif; font-size: 0.85rem; font-weight: 700;
            letter-spacing: 0.1em; cursor: pointer; transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(255,107,107,0.3);
        }
        .open-btn:hover { transform: translateY(-3px) scale(1.02); box-shadow: 0 8px 30px rgba(255,107,107,0.4); }

        /* Content */
        .invitation-content { display: none; }
        .invitation-content.visible { display: block; animation: fadeIn 1s ease; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        .inv-section { padding: 4rem 1.5rem; max-width: 700px; margin: 0 auto; text-align: center; }
        .section-label { font-size: 0.65rem; letter-spacing: 0.4em; text-transform: uppercase; color: var(--secondary); font-weight: 700; margin-bottom: 0.6rem; }
        .section-title { font-family: 'Pacifico', cursive; font-size: clamp(1.8rem, 5vw, 2.8rem); background: linear-gradient(135deg, var(--primary), var(--purple)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 1rem; }
        .section-divider { width: 60px; height: 3px; background: linear-gradient(90deg, var(--primary), var(--secondary)); border-radius: 2px; margin: 1rem auto; }

        /* Event Name */
        .host-name { font-family: 'Pacifico', cursive; font-size: clamp(2rem, 6vw, 3.5rem); background: linear-gradient(135deg, var(--primary), var(--pink), var(--purple)); background-size: 200% 200%; -webkit-background-clip: text; -webkit-text-fill-color: transparent; animation: gradient 4s ease infinite; }

        /* Countdown */
        .countdown { display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap; }
        .countdown-item {
            background: linear-gradient(135deg, rgba(255,107,107,0.08), rgba(168,85,247,0.08));
            border: 1px solid rgba(255,107,107,0.15); border-radius: 20px;
            padding: 1.2rem 1rem; min-width: 75px;
        }
        .countdown-number { font-size: 1.8rem; font-weight: 800; background: linear-gradient(135deg, var(--primary), var(--purple)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .countdown-label { font-size: 0.6rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--text-muted); margin-top: 0.2rem; }

        /* Event Cards */
        .event-card {
            background: linear-gradient(135deg, rgba(255,107,107,0.05), rgba(78,205,196,0.05));
            border: 1px solid rgba(255,107,107,0.1); border-radius: 20px; padding: 1.5rem;
            margin-bottom: 1rem; transition: all 0.3s ease;
        }
        .event-card:hover { transform: translateY(-3px); border-color: rgba(255,107,107,0.3); }

        /* Gallery */
        .gallery-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 8px; }
        .gallery-item { border-radius: 16px; overflow: hidden; aspect-ratio: 1; }
        .gallery-item img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
        .gallery-item:hover img { transform: scale(1.1); }

        /* Forms */
        .form-card {
            background: linear-gradient(135deg, rgba(255,107,107,0.04), rgba(168,85,247,0.04));
            border: 1px solid rgba(255,107,107,0.1); border-radius: 24px; padding: 2rem;
            max-width: 450px; margin: 0 auto;
        }
        .inv-input {
            width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,107,107,0.12);
            border-radius: 14px; padding: 13px 16px; color: var(--text); font-family: 'Poppins', sans-serif;
            font-size: 13px; margin-bottom: 0.8rem; transition: all 0.3s ease;
        }
        .inv-input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(255,107,107,0.12); }
        .inv-select { width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,107,107,0.12); border-radius: 14px; padding: 13px 16px; color: var(--text); font-family: 'Poppins', sans-serif; font-size: 13px; margin-bottom: 0.8rem; appearance: none; }
        .inv-select option { background: var(--bg); }
        .inv-btn {
            width: 100%; padding: 14px; border: none; border-radius: 14px;
            background: linear-gradient(135deg, var(--primary), var(--pink));
            color: white; font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 0.8rem;
            letter-spacing: 0.05em; cursor: pointer; transition: all 0.3s ease;
        }
        .inv-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(255,107,107,0.3); }

        /* Wishes */
        .wish-item {
            background: linear-gradient(135deg, rgba(255,107,107,0.04), rgba(168,85,247,0.04));
            border: 1px solid rgba(255,107,107,0.08); border-radius: 16px; padding: 1rem 1.2rem;
            margin-bottom: 0.6rem; text-align: left;
        }
        .wish-name { font-weight: 700; font-size: 0.8rem; color: var(--primary); margin-bottom: 0.2rem; }
        .wish-message { font-size: 0.8rem; color: var(--text-muted); line-height: 1.6; }

        .maps-container { border-radius: 20px; overflow: hidden; border: 1px solid rgba(255,107,107,0.1); }

        /* Music */
        .music-player {
            position: fixed; bottom: 20px; right: 20px; z-index: 100; width: 48px; height: 48px;
            border-radius: 50%; background: linear-gradient(135deg, var(--primary), var(--pink));
            display: flex; align-items: center; justify-content: center; cursor: pointer;
            box-shadow: 0 4px 15px rgba(255,107,107,0.3); transition: all 0.3s ease;
        }
        .music-player.playing { animation: spin 3s linear infinite; }
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

        .inv-footer { padding: 2.5rem 1rem; text-align: center; }
        .inv-toast { position: fixed; top: 20px; left: 50%; transform: translateX(-50%) translateY(-120%); background: linear-gradient(135deg, var(--primary), var(--pink)); color: white; padding: 12px 28px; border-radius: 100px; font-weight: 700; font-size: 0.8rem; z-index: 200; transition: transform 0.5s ease; }
        .inv-toast.show { transform: translateX(-50%) translateY(0); }

        @media (max-width: 640px) { .inv-section { padding: 3rem 1rem; } .countdown { gap: 0.6rem; } .countdown-item { min-width: 60px; padding: 0.8rem; } .countdown-number { font-size: 1.4rem; } }
    </style>
</head>
<body>
    @if(session('success'))
    <div class="inv-toast show" id="invToast">{{ session('success') }}</div>
    <script>setTimeout(() => document.getElementById('invToast').classList.remove('show'), 4000);</script>
    @endif

    <section class="cover-section" id="cover">
        <div class="confetti-container">
            @for($i = 0; $i < 30; $i++)
            <div class="confetti" style="left: {{ rand(2, 98) }}%; background: {{ ['#FF6B6B','#4ECDC4','#FFE66D','#A855F7','#EC4899','#06B6D4'][rand(0,5)] }}; animation-delay: {{ $i * 0.3 }}s; animation-duration: {{ rand(4, 8) }}s;"></div>
            @endfor
        </div>
        <div class="cover-content">
            <div class="cover-emoji" data-aos="zoom-in">🎂</div>
            <h1 class="cover-title" data-aos="fade-up" data-aos-delay="200">
                @if($invitation->event_type === 'birthday')
                    {{ $invitation->host_name ?? $invitation->title }}
                @else
                    {{ $invitation->title }}
                @endif
            </h1>
            <p class="cover-subtitle" data-aos="fade-up" data-aos-delay="400">
                @if($invitation->event_type === 'birthday')
                    You're Invited to the Party! 🎉
                @else
                    {{ ucfirst($invitation->event_type) }} Celebration
                @endif
            </p>
            <div class="cover-date" data-aos="fade-up" data-aos-delay="600">📅 {{ $invitation->event_date->translatedFormat('d F Y') }}</div>
            @if(isset($guest))
            <p class="cover-guest" data-aos="fade-up" data-aos-delay="700">Hey, <strong>{{ $guest->name }}</strong>! 👋</p>
            @endif
            <button class="open-btn" data-aos="fade-up" data-aos-delay="900" onclick="openInvitation()">
                🎁 Buka Undangan
            </button>
        </div>
    </section>

    <div class="invitation-content" id="invitationContent">
        @if($invitation->opening_text)
        <section class="inv-section">
            <p class="text-sm leading-relaxed max-w-md mx-auto" style="color: var(--text-muted);" data-aos="fade-up">{{ $invitation->opening_text }}</p>
        </section>
        @endif

        <section class="inv-section">
            <div class="section-label" data-aos="fade-up">🎈 Celebration</div>
            <div class="section-divider" data-aos="fade-up" data-aos-delay="100"></div>
            <div data-aos="zoom-in" data-aos-delay="200">
                <span class="host-name">
                    @if($invitation->event_type === 'wedding')
                        {{ $invitation->groom_name ?? '' }} & {{ $invitation->bride_name ?? '' }}
                    @else
                        {{ $invitation->host_name ?? $invitation->title }}
                    @endif
                </span>
            </div>
        </section>

        <section class="inv-section">
            <div class="section-label" data-aos="fade-up">⏰ Countdown</div>
            <h2 class="section-title" data-aos="fade-up" data-aos-delay="100">Menuju Hari H!</h2>
            <div class="countdown" data-aos="fade-up" data-aos-delay="200" id="countdown" data-date="{{ $invitation->event_date->format('Y-m-d') }}T{{ \Carbon\Carbon::parse($invitation->event_time)->format('H:i:s') }}">
                <div class="countdown-item"><div class="countdown-number" id="cd-days">0</div><div class="countdown-label">Hari</div></div>
                <div class="countdown-item"><div class="countdown-number" id="cd-hours">0</div><div class="countdown-label">Jam</div></div>
                <div class="countdown-item"><div class="countdown-number" id="cd-minutes">0</div><div class="countdown-label">Menit</div></div>
                <div class="countdown-item"><div class="countdown-number" id="cd-seconds">0</div><div class="countdown-label">Detik</div></div>
            </div>
        </section>

        <section class="inv-section">
            <div class="section-label" data-aos="fade-up">📍 Detail Acara</div>
            <h2 class="section-title" data-aos="fade-up" data-aos-delay="100">Kapan & Dimana</h2>
            <div class="section-divider" data-aos="fade-up" data-aos-delay="150"></div>
            <div class="event-card" data-aos="fade-up" data-aos-delay="200">
                <h3 class="font-bold text-lg mb-2" style="color: var(--primary);">{{ $invitation->venue_name }}</h3>
                <div class="flex flex-col gap-1 text-sm" style="color: var(--text-muted);">
                    <p>📅 {{ $invitation->event_date->translatedFormat('l, d F Y') }}</p>
                    <p>🕐 {{ \Carbon\Carbon::parse($invitation->event_time)->format('H:i') }} WIB</p>
                    <p>📍 {{ $invitation->venue_address }}</p>
                </div>
            </div>
            @foreach($invitation->events as $event)
            <div class="event-card" data-aos="fade-up" data-aos-delay="{{ 250 + $loop->index * 100 }}">
                <h3 class="font-bold text-lg mb-2" style="color: var(--secondary);">{{ $event->event_name }}</h3>
                <div class="flex flex-col gap-1 text-sm" style="color: var(--text-muted);">
                    <p>📅 {{ $event->event_date->format('d F Y') }} · 🕐 {{ $event->event_time }}</p>
                    <p>📍 {{ $event->venue_name }}, {{ $event->venue_address }}</p>
                </div>
            </div>
            @endforeach
        </section>

        @if($invitation->photos->count())
        <section class="inv-section">
            <div class="section-label" data-aos="fade-up">📸 Galeri</div>
            <h2 class="section-title" data-aos="fade-up" data-aos-delay="100">Moments</h2>
            <div class="section-divider" data-aos="fade-up" data-aos-delay="150"></div>
            <div class="gallery-grid">
                @foreach($invitation->photos as $photo)
                <div class="gallery-item" data-aos="zoom-in" data-aos-delay="{{ $loop->index * 80 }}"><img src="{{ asset('storage/' . $photo->file_path) }}" alt="{{ $photo->caption ?? 'Photo' }}" loading="lazy"></div>
                @endforeach
            </div>
        </section>
        @endif

        @if($invitation->google_maps_url)
        <section class="inv-section">
            <div class="section-label" data-aos="fade-up">🗺️ Lokasi</div>
            <div class="maps-container" data-aos="fade-up" data-aos-delay="200">
                <iframe src="{{ str_replace('/maps/', '/maps/embed/', $invitation->google_maps_url) }}" width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
            </div>
            <a href="{{ $invitation->google_maps_url }}" target="_blank" class="inline-flex items-center gap-2 mt-3 px-5 py-2 rounded-full text-sm font-semibold transition" style="background: linear-gradient(135deg, rgba(255,107,107,0.1), rgba(78,205,196,0.1)); border: 1px solid rgba(255,107,107,0.2); color: var(--primary);" data-aos="fade-up">
                📍 Buka Maps
            </a>
        </section>
        @endif

        @if($invitation->loveStories->count())
        <section class="inv-section" id="love-story">
            <div class="section-label" data-aos="fade-up">Love Story</div>
            <h2 class="section-title" data-aos="fade-up" data-aos-delay="100">Perjalanan Cinta</h2>
            <div class="section-divider" data-aos="fade-up" data-aos-delay="150"></div>
            <div style="max-width: 450px; margin: 0 auto;">
                @foreach($invitation->loveStories as $story)
                <div class="wish-item" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                    @if($story->year)<div class="wish-name">{{ $story->year }}</div>@endif
                    <div class="wish-name">{{ $story->title }}</div>
                    @if($story->description)<div class="wish-message">{{ $story->description }}</div>@endif
                </div>
                @endforeach
            </div>
        </section>
        @endif

        <section class="inv-section" id="rsvp">
            <div class="section-label" data-aos="fade-up">✉️ Konfirmasi</div>
            <h2 class="section-title" data-aos="fade-up" data-aos-delay="100">RSVP</h2>
            <div class="section-divider" data-aos="fade-up" data-aos-delay="150"></div>
            <div class="form-card" data-aos="fade-up" data-aos-delay="200">
                <form method="POST" action="{{ route('invitation.rsvp', $invitation->slug) }}">
                    @csrf
                    @if(isset($guest))
                        <input type="hidden" name="guest_id" value="{{ $guest->id }}"><input type="hidden" name="name" value="{{ $guest->name }}">
                        <p class="text-sm mb-3" style="color: var(--secondary);">Hey <strong>{{ $guest->name }}</strong>! 🎉</p>
                    @else
                        <input type="text" name="name" class="inv-input" placeholder="Nama Kamu" required>
                    @endif
                    <input type="text" name="phone" class="inv-input" placeholder="No. HP">
                    <select name="status" class="inv-select" required><option value="attending">🎉 Hadir!</option><option value="maybe">🤔 Mungkin</option><option value="not_attending">😢 Gak Bisa</option></select>
                    <input type="number" name="pax" class="inv-input" value="1" min="1" max="10" placeholder="Berapa orang?">
                    <textarea name="message" class="inv-input" rows="3" placeholder="Tulis ucapan... 💬" style="resize: none;"></textarea>
                    <button type="submit" class="inv-btn">🚀 Kirim!</button>
                </form>
            </div>
            @if($invitation->rsvps->whereNotNull('message')->count())
            <div style="max-width: 450px; margin: 24px auto 0;">
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

        <section class="inv-section" id="wishes">
            <div class="section-label" data-aos="fade-up">💬 Ucapan</div>
            <h2 class="section-title" data-aos="fade-up" data-aos-delay="100">Wishes</h2>
            <div class="section-divider" data-aos="fade-up" data-aos-delay="150"></div>
            <div class="form-card mb-6" data-aos="fade-up" data-aos-delay="200">
                <form method="POST" action="{{ route('invitation.wish', $invitation->slug) }}">
                    @csrf
                    <input type="text" name="name" class="inv-input" placeholder="Nama Kamu" value="{{ $guest->name ?? '' }}" required>
                    <textarea name="message" class="inv-input" rows="3" placeholder="Tulis ucapan & doa 🙏" style="resize: none;" required></textarea>
                    <button type="submit" class="inv-btn">💝 Kirim Ucapan</button>
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
            <p class="text-sm leading-relaxed max-w-md mx-auto" style="color: var(--text-muted);" data-aos="fade-up">{{ $invitation->closing_text }}</p>
            <div class="mt-4" data-aos="fade-up" data-aos-delay="100" style="font-size: 2rem;">🎉🎂🎈</div>
        </section>
        @endif

        <footer class="inv-footer"><p class="text-xs" style="color: var(--text-muted); opacity: 0.4;">Made with 💖 by {{ config('app.name') }}</p></footer>
    </div>

    @if($invitation->music_url)
    <div class="music-player" id="musicPlayer" onclick="toggleMusic()"><i class="fas fa-music text-white text-sm"></i></div>
    <audio id="bgMusic" loop><source src="{{ asset('storage/' . $invitation->music_url) }}" type="audio/mpeg"></audio>
    @endif

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        function openInvitation() { document.getElementById('cover').style.display = 'none'; document.getElementById('invitationContent').classList.add('visible'); AOS.init({ duration: 800, once: true, offset: 50 }); const a = document.getElementById('bgMusic'); if (a) a.play().then(() => document.getElementById('musicPlayer')?.classList.add('playing')).catch(() => {}); startCountdown(); }
        function startCountdown() { const el = document.getElementById('countdown'); if (!el) return; const t = new Date(el.dataset.date).getTime(); setInterval(() => { const d = t - Date.now(); if (d > 0) { document.getElementById('cd-days').textContent = Math.floor(d / 864e5); document.getElementById('cd-hours').textContent = Math.floor((d % 864e5) / 36e5); document.getElementById('cd-minutes').textContent = Math.floor((d % 36e5) / 6e4); document.getElementById('cd-seconds').textContent = Math.floor((d % 6e4) / 1e3); } }, 1000); }
        function toggleMusic() { const a = document.getElementById('bgMusic'), p = document.getElementById('musicPlayer'); a.paused ? (a.play(), p.classList.add('playing')) : (a.pause(), p.classList.remove('playing')); }
        AOS.init({ duration: 800, once: true });
    </script>
</body>
</html>
