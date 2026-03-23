<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - Undangan Digital Estetik</title>
    <meta name="description" content="Platform undangan digital estetik: template premium, RSVP real-time, dan operasional acara dalam satu sistem.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Fraunces:opsz,wght@9..144,500;9..144,700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --bg: #fbfaf8;
            --panel: rgba(255, 255, 255, 0.78);
            --line: rgba(28, 29, 31, 0.12);
            --text: #171717;
            --muted: #696b71;
            --brand: #c05a3f;
            --brand-soft: #f5ddd3;
            --mint: #98d0c4;
            --shadow: 0 18px 50px rgba(30, 20, 14, 0.08);
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            color: var(--text);
            font-family: 'Manrope', sans-serif;
            background:
                radial-gradient(900px 500px at 0% -10%, rgba(192,90,63,.14), transparent 60%),
                radial-gradient(700px 420px at 100% 0%, rgba(152,208,196,.2), transparent 65%),
                var(--bg);
        }
        .container { width: min(1120px, calc(100% - 2rem)); margin: 0 auto; }
        .nav {
            position: sticky;
            top: 12px;
            z-index: 40;
            margin: 12px auto 0;
            width: min(1120px, calc(100% - 2rem));
            border: 1px solid var(--line);
            background: var(--panel);
            backdrop-filter: blur(10px);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: .8rem 1rem;
            box-shadow: var(--shadow);
        }
        .logo {
            font-family: 'Fraunces', serif;
            font-weight: 700;
            font-size: 1.1rem;
            letter-spacing: .02em;
        }
        .nav-links { display: flex; align-items: center; gap: 1rem; }
        .nav-links a {
            color: var(--muted);
            font-size: .88rem;
            text-decoration: none;
            font-weight: 700;
        }
        .btn, .btn-outline {
            border-radius: 999px;
            padding: .62rem 1rem;
            text-decoration: none;
            font-weight: 800;
            font-size: .82rem;
            display: inline-flex;
            align-items: center;
            gap: .4rem;
        }
        .btn {
            background: linear-gradient(120deg, #d07157, #c05a3f);
            color: #fff;
            border: 1px solid #c05a3f;
        }
        .btn-outline {
            color: var(--text);
            border: 1px solid var(--line);
            background: rgba(255,255,255,.7);
        }

        .hero { padding: 4.2rem 0 3rem; }
        .hero-wrap {
            display: grid;
            grid-template-columns: 1.1fr .9fr;
            gap: 1.2rem;
            align-items: stretch;
        }
        .hero-main, .hero-side {
            border: 1px solid var(--line);
            border-radius: 22px;
            background: var(--panel);
            backdrop-filter: blur(6px);
            box-shadow: var(--shadow);
        }
        .hero-main { padding: 2.2rem; }
        .eyebrow {
            display: inline-flex;
            border: 1px solid rgba(192,90,63,.25);
            background: var(--brand-soft);
            color: #8e3f2b;
            border-radius: 999px;
            padding: .36rem .72rem;
            font-size: .72rem;
            font-weight: 800;
            letter-spacing: .06em;
            text-transform: uppercase;
            margin-bottom: .9rem;
        }
        h1 {
            margin: 0 0 .8rem;
            font-family: 'Fraunces', serif;
            font-size: clamp(2rem, 5vw, 3.4rem);
            line-height: 1.06;
        }
        .hero p { color: var(--muted); font-size: .95rem; line-height: 1.8; margin: 0 0 1.1rem; }
        .hero-cta { display: flex; flex-wrap: wrap; gap: .6rem; margin-top: 1.2rem; }
        .hero-stat {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: .55rem;
            margin-top: 1.2rem;
        }
        .hero-stat .item {
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: .66rem;
            background: rgba(255,255,255,.7);
        }
        .hero-stat .num { font-weight: 900; font-size: 1.02rem; }
        .hero-stat .lbl { color: var(--muted); font-size: .72rem; }
        .hero-side { padding: 1rem; display: grid; gap: .7rem; align-content: start; }
        .niche {
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: .75rem;
            display: flex;
            align-items: center;
            gap: .7rem;
            background: rgba(255,255,255,.74);
        }
        .niche i { width: 34px; height: 34px; border-radius: 10px; display: grid; place-items: center; background: #fff; border: 1px solid var(--line); }
        .niche h3 { margin: 0; font-size: .88rem; }
        .niche p { margin: .2rem 0 0; font-size: .73rem; line-height: 1.5; color: var(--muted); }

        .section { padding: 1.3rem 0 2.8rem; }
        .section-head { margin-bottom: 1rem; }
        .section-head h2 {
            margin: 0;
            font-family: 'Fraunces', serif;
            font-size: clamp(1.5rem, 3vw, 2.2rem);
        }
        .section-head p { margin: .4rem 0 0; color: var(--muted); font-size: .9rem; }

        .template-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: .8rem;
        }
        .template-card {
            border: 1px solid var(--line);
            border-radius: 16px;
            overflow: hidden;
            background: rgba(255,255,255,.82);
            box-shadow: var(--shadow);
        }
        .thumb {
            aspect-ratio: 16/10;
            background: linear-gradient(135deg, #ead6c8, #d6e8e2);
            display: grid;
            place-items: center;
            border-bottom: 1px solid var(--line);
        }
        .thumb img { width: 100%; height: 100%; object-fit: cover; }
        .template-body { padding: .8rem; }
        .template-name { margin: 0; font-size: .9rem; font-weight: 800; }
        .chip {
            display: inline-flex;
            margin-top: .45rem;
            border: 1px solid var(--line);
            border-radius: 999px;
            font-size: .67rem;
            font-weight: 800;
            padding: .2rem .52rem;
            color: var(--muted);
        }

        .pricing {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: .8rem;
        }
        .price-card {
            border: 1px solid var(--line);
            border-radius: 16px;
            background: rgba(255,255,255,.82);
            padding: 1rem;
            box-shadow: var(--shadow);
            position: relative;
        }
        .price-card.recommended { border-color: rgba(192,90,63,.35); background: linear-gradient(160deg, rgba(255,255,255,.88), rgba(245,221,211,.54)); }
        .badge {
            position: absolute;
            top: .7rem;
            right: .7rem;
            border-radius: 999px;
            border: 1px solid rgba(192,90,63,.25);
            background: var(--brand-soft);
            color: #8e3f2b;
            font-size: .62rem;
            font-weight: 900;
            padding: .22rem .5rem;
            text-transform: uppercase;
        }
        .price-card h3 { margin: 0 0 .24rem; font-size: 1rem; }
        .price-desc { margin: 0 0 .62rem; color: var(--muted); font-size: .76rem; line-height: 1.6; min-height: 34px; }
        .price { font-weight: 900; font-size: 1.42rem; margin: 0 0 .4rem; }
        .price small { font-size: .7rem; color: var(--muted); font-weight: 700; }
        .feature { font-size: .76rem; color: #404247; margin: .3rem 0; display: flex; gap: .4rem; align-items: flex-start; }

        .about {
            border: 1px solid var(--line);
            border-radius: 20px;
            background: linear-gradient(140deg, rgba(255,255,255,.86), rgba(255,255,255,.72));
            box-shadow: var(--shadow);
            padding: 1.4rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        .about p { color: var(--muted); line-height: 1.8; font-size: .9rem; }
        .about-list .row {
            border: 1px dashed var(--line);
            border-radius: 12px;
            padding: .65rem .7rem;
            background: rgba(255,255,255,.65);
            margin-bottom: .5rem;
            font-size: .82rem;
            font-weight: 700;
        }

        .cta {
            margin: 2.2rem 0 3rem;
            border-radius: 22px;
            border: 1px solid rgba(192,90,63,.35);
            background: linear-gradient(120deg, #f6dfd7 0%, #f4eee9 45%, #e8f4f1 100%);
            padding: 1.5rem;
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .8rem;
        }
        .cta h3 { margin: 0; font-family: 'Fraunces', serif; font-size: clamp(1.4rem, 3.5vw, 2rem); }
        .cta p { margin: .35rem 0 0; color: #5f5350; font-size: .9rem; }
        .footer { color: var(--muted); font-size: .78rem; padding-bottom: 1.2rem; }

        @media (max-width: 980px) {
            .hero-wrap, .about { grid-template-columns: 1fr; }
            .template-grid, .pricing { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 700px) {
            .nav-links a { display: none; }
            .template-grid, .pricing, .hero-stat { grid-template-columns: 1fr; }
            .hero-main { padding: 1.2rem; }
            .cta { flex-direction: column; align-items: flex-start; }
        }
    </style>
</head>
<body>
    <nav class="nav">
        <div class="logo">{{ config('app.name') }}</div>
        <div class="nav-links">
            <a href="#template">Template</a>
            <a href="#paket">Paket</a>
            <a href="#about">About</a>
            <a href="{{ route('marketing.trial') }}" class="btn-outline">Trial 3 Menit</a>
            <a href="{{ route('register') }}" class="btn">Mulai Sekarang</a>
        </div>
    </nav>

    <main class="container">
        <section class="hero">
            <div class="hero-wrap">
                <article class="hero-main">
                    <div class="eyebrow">Undangan Digital Premium</div>
                    <h1>Minimalis, estetik, dan siap jual untuk semua jenis acara.</h1>
                    <p>Kelola undangan dari desain, RSVP, check-in, sampai pembayaran dalam satu dashboard yang rapi. Dibangun untuk konversi tinggi dan pengalaman tamu yang nyaman.</p>
                    <div class="hero-cta">
                        <a href="{{ route('marketing.trial') }}" class="btn"><i class="fas fa-bolt"></i> Trial Instan</a>
                        <a href="{{ route('login') }}" class="btn-outline"><i class="fas fa-right-to-bracket"></i> Masuk Dashboard</a>
                    </div>
                    <div class="hero-stat">
                        <div class="item">
                            <div class="num">{{ $templates->count() }}+</div>
                            <div class="lbl">Template Aktif</div>
                        </div>
                        <div class="item">
                            <div class="num">{{ $packages->count() }}</div>
                            <div class="lbl">Pilihan Paket</div>
                        </div>
                        <div class="item">
                            <div class="num">Realtime</div>
                            <div class="lbl">RSVP Analytics</div>
                        </div>
                    </div>
                </article>
                <aside class="hero-side">
                    @foreach($niches as $key => $niche)
                        <a class="niche" href="{{ route('marketing.niche', $key) }}" style="text-decoration:none; color:inherit;">
                            <i class="fas {{ $niche['icon'] }}" style="color: {{ $niche['color'] }};"></i>
                            <div>
                                <h3>{{ $niche['name'] }}</h3>
                                <p>{{ $niche['description'] }}</p>
                            </div>
                        </a>
                    @endforeach
                </aside>
            </div>
        </section>

        <section id="template" class="section">
            <div class="section-head">
                <h2>Show Template</h2>
                <p>Template yang mudah dikustom, mobile-friendly, dan sudah siap untuk live invitation.</p>
            </div>
            <div class="template-grid">
                @foreach($templates as $template)
                    <article class="template-card">
                        <div class="thumb">
                            @if($template->thumbnail)
                                <img src="{{ asset('storage/' . $template->thumbnail) }}" alt="{{ $template->name }}">
                            @else
                                <i class="fas fa-images" style="font-size:2rem;color:#826d66;"></i>
                            @endif
                        </div>
                        <div class="template-body">
                            <p class="template-name">{{ $template->name }}</p>
                            <span class="chip">{{ ucfirst($template->category) }}{{ $template->is_premium ? ' • Premium' : '' }}</span>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <section id="paket" class="section">
            <div class="section-head">
                <h2>Paket</h2>
                <p>Fleksibel untuk kebutuhan personal sampai operasional event skala besar.</p>
            </div>
            <div class="pricing">
                @foreach($packages as $package)
                    <article class="price-card {{ $package->is_recommended ? 'recommended' : '' }}">
                        @if($package->badge_text)
                            <span class="badge">{{ $package->badge_text }}</span>
                        @endif
                        <h3>{{ $package->name }}</h3>
                        <p class="price-desc">{{ $package->description }}</p>
                        <p class="price">Rp{{ number_format((float) $package->price, 0, ',', '.') }} <small>/ {{ ($package->billing_type ?? 'one_time') === 'subscription' ? ($package->billing_cycle ?? 'bulan') : 'sekali bayar' }}</small></p>
                        <div class="feature"><i class="fas fa-user-group"></i> Maks {{ number_format((int) $package->max_guests) }} tamu</div>
                        <div class="feature"><i class="fas fa-image"></i> Maks {{ number_format((int) $package->max_photos) }} foto</div>
                        <div class="feature"><i class="fas fa-layer-group"></i> Maks {{ number_format((int) ($package->max_invitations ?? 1)) }} undangan</div>
                        @foreach(array_slice($package->features ?? [], 0, 3) as $feature)
                            <div class="feature"><i class="fas fa-check"></i> {{ $feature }}</div>
                        @endforeach
                    </article>
                @endforeach
            </div>
        </section>

        <section id="about" class="section">
            <div class="section-head">
                <h2>About</h2>
                <p>Platform ini dibangun untuk tim undangan digital yang ingin tumbuh cepat, rapi, dan profitabel.</p>
            </div>
            <article class="about">
                <div>
                    <p>
                        Dari funnel trial, billing, affiliate, hingga operasional hari-H, semua alur sudah disatukan supaya owner fokus jualan dan client fokus di momen acara.
                        Tampilan tetap estetik, performa tetap ringan, dan pengelolaan data tetap terstruktur.
                    </p>
                </div>
                <div class="about-list">
                    <div class="row">RSVP, ucapan, dan analytics real-time</div>
                    <div class="row">Check-in QR + seating plan untuk hari-H</div>
                    <div class="row">Payment, affiliate, payout, dan billing lengkap</div>
                    <div class="row">Template bisa berkembang sesuai niche market</div>
                </div>
            </article>
        </section>

        <section class="cta">
            <div>
                <h3>Siap mulai jualan undangan digital yang lebih premium?</h3>
                <p>Aktifkan trial 3 menit, pilih template, lalu publish undangan pertama Anda hari ini.</p>
            </div>
            <div style="display:flex; gap:.55rem; flex-wrap:wrap;">
                <a href="{{ route('marketing.trial') }}" class="btn"><i class="fas fa-wand-magic-sparkles"></i> Coba Trial</a>
                <a href="{{ route('register') }}" class="btn-outline"><i class="fas fa-user-plus"></i> Buat Akun</a>
            </div>
        </section>

        <footer class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. Crafted for memorable invitations.
        </footer>
    </main>
</body>
</html>
