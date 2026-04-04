<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - Platform Undangan Digital</title>
    <meta name="description" content="Platform undangan digital estetik: template premium, RSVP real-time, dan operasional acara dalam satu sistem.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --bg: #ffffff;
            --text-main: #111111;
            --text-muted: #666666;
            --border: #eaeaea;
            --btn-bg: #111111;
            --btn-text: #ffffff;
            --btn-hover: #333333;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--bg);
            color: var(--text-main);
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        .container { width: min(1200px, 100% - 3rem); margin: 0 auto; }

        /* Navbar */
        .nav {
            position: fixed;
            top: 0; left: 0; right: 0;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            z-index: 100;
            border-bottom: 1px solid rgba(0,0,0,0.04);
            height: 70px;
            display: flex;
            align-items: center;
        }
        
        .nav-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: min(1200px, 100% - 3rem);
            margin: 0 auto;
        }

        .logo-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: var(--text-main);
            font-weight: 600;
            font-size: 1.15rem;
            letter-spacing: -0.02em;
        }

        .logo-wrap img {
            height: 28px;
            width: auto;
            border-radius: 4px;
        }

        .nav-menu {
            display: flex;
            align-items: center;
            gap: 2rem;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .nav-menu a {
            color: var(--text-muted);
            text-decoration: none;
            transition: color 0.2s;
        }
        
        .nav-menu a:hover {
            color: var(--text-main);
        }

        .btn-dark {
            background: var(--btn-bg);
            color: var(--btn-text);
            padding: 0.6rem 1.2rem;
            border-radius: 999px;
            font-size: 0.9rem;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }
        
        .btn-dark:hover {
            background: var(--btn-hover);
            transform: translateY(-1px);
        }

        /* Hero Section */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            padding-top: 70px; /* offset nav */
            overflow: hidden;
        }

        #particleCanvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            pointer-events: none; /* Let clicks pass through */
        }

        .hero-content {
            position: relative;
            z-index: 10;
            text-align: center;
            max-width: 700px;
            background: radial-gradient(circle, rgba(255,255,255,1) 30%, rgba(255,255,255,0) 100%);
            padding: 3rem;
            border-radius: 50%;
        }

        .hero-logo-large {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            margin-bottom: 24px;
            font-size: 2rem;
            font-weight: 500;
            letter-spacing: -0.04em;
        }
        
        .hero-logo-large img {
            height: 42px;
            border-radius: 8px;
        }

        .hero h1 {
            font-size: clamp(2rem, 4vw, 2.75rem);
            font-weight: 400;
            letter-spacing: -0.03em;
            line-height: 1.2;
            margin-bottom: 16px;
            color: var(--text-main);
        }

        .hero p {
            font-size: 0.95rem;
            color: var(--text-muted);
            margin-bottom: 24px;
            line-height: 1.6;
        }

        .hero-links {
            font-size: 0.85rem;
            display: flex;
            justify-content: center;
            gap: 1rem;
        }

        .hero-links a {
            color: #0066cc;
            text-decoration: none;
        }
        
        .hero-links a:hover {
            text-decoration: underline;
        }

        /* Clean Sections */
        .section {
            padding: 6rem 0;
            background: #fafafa;
            border-top: 1px solid var(--border);
        }

        .section-alt {
            background: #ffffff;
        }

        .section-header {
            text-align: center;
            margin-bottom: 4rem;
        }

        .section-header h2 {
            font-size: 2rem;
            font-weight: 500;
            letter-spacing: -0.03em;
            margin-bottom: 12px;
        }
        
        .section-header p {
            color: var(--text-muted);
            max-width: 500px;
            margin: 0 auto;
            line-height: 1.6;
        }

        /* Templates Grid */
        .grid-3 {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
        }

        .clean-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .clean-card:hover {
            box-shadow: 0 12px 30px rgba(0,0,0,0.06);
            transform: translateY(-4px);
        }

        .template-thumb {
            aspect-ratio: 16/10;
            background: #f5f5f5;
            display: grid;
            place-items: center;
            border-bottom: 1px solid var(--border);
            color: #aaa;
        }
        
        .template-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .template-meta {
            padding: 1.25rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .template-meta h3 {
            font-size: 1rem;
            font-weight: 500;
        }

        .chip {
            font-size: 0.7rem;
            padding: 4px 10px;
            background: #f0f0f0;
            border-radius: 999px;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* Pricing Table */
        .pricing-card {
            padding: 2.5rem 2rem;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 16px;
            display: flex;
            flex-direction: column;
            position: relative;
        }
        
        .pricing-card.popular {
            border: 2px solid #111;
            box-shadow: 0 15px 35px rgba(0,0,0,0.05);
        }

        .pricing-badge {
            position: absolute;
            top: -12px;
            left: 50%;
            transform: translateX(-50%);
            background: #111;
            color: #fff;
            font-size: 0.7rem;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 999px;
            letter-spacing: 0.05em;
        }

        .price-title { font-size: 1.1rem; font-weight: 600; margin-bottom: 8px; }
        .price-desc { color: var(--text-muted); font-size: 0.85rem; margin-bottom: 2rem; line-height: 1.5; min-height: 40px; }
        
        .price-amount {
            font-size: 2.5rem;
            font-weight: 400;
            letter-spacing: -0.05em;
            margin-bottom: 2rem;
        }
        
        .price-amount span {
            font-size: 1rem;
            color: var(--text-muted);
            letter-spacing: 0;
        }

        .feature-list {
            list-style: none;
            margin-bottom: 2rem;
            flex: 1;
        }

        .feature-list li {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .feature-list li i {
            color: #111;
            font-size: 0.8rem;
        }

        /* About / Bottom CTA */
        .about-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }

        .about-text h3 {
            font-size: 1.75rem;
            font-weight: 500;
            letter-spacing: -0.03em;
            margin-bottom: 1.5rem;
        }
        
        .about-text p {
            color: var(--text-muted);
            font-size: 1.05rem;
            line-height: 1.7;
            margin-bottom: 1.5rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .stat-box {
            background: #fafafa;
            border: 1px solid var(--border);
            padding: 1.5rem;
            border-radius: 12px;
        }
        
        .stat-num {
            font-size: 2rem;
            font-weight: 400;
            letter-spacing: -0.05em;
            margin-bottom: 4px;
        }
        
        .stat-lbl {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .footer {
            text-align: center;
            padding: 3rem 0;
            color: var(--text-muted);
            font-size: 0.85rem;
            border-top: 1px solid var(--border);
        }

        @media (max-width: 768px) {
            .nav-menu { display: none; }
            .about-grid { grid-template-columns: 1fr; gap: 2rem; }
            .hero-content { padding: 1.5rem; }
        }
    </style>
</head>
<body>

    @php
        // Mengambil logo perusahaan dari Admin (jika ada) setup multi-tenant
        $mainAdmin = \App\Models\User::where('role', 'admin')->first();
        $companyLogo = $mainAdmin && $mainAdmin->company_logo ? Storage::url($mainAdmin->company_logo) : null;
        $companyName = $mainAdmin && $mainAdmin->company_name ? $mainAdmin->company_name : config('app.name');
    @endphp

    <!-- Navigation -->
    <nav class="nav">
        <div class="nav-inner">
            <a href="/" class="logo-wrap">
                @if($companyLogo)
                    <img src="{{ $companyLogo }}" alt="Logo">
                @else
                    <i class="fas fa-layer-group text-xl"></i>
                @endif
                {{ $companyName }}
            </a>
            <div class="nav-menu">
                <a href="#templates">Templates</a>
                <a href="#pricing">Pricing</a>
                <a href="#about">About</a>
                <a href="{{ route('marketing.trial') }}">Trial</a>
            </div>
            <a href="{{ route('login') }}" class="btn-dark">
                Dashboard <i class="fas fa-arrow-right text-[10px]"></i>
            </a>
        </div>
    </nav>

    <!-- Canvas Animation Hero -->
    <section class="hero">
        <canvas id="particleCanvas"></canvas>
        
        <div class="hero-content">
            <div class="hero-logo-large">
                @if($companyLogo)
                    <img src="{{ $companyLogo }}" alt="Logo">
                @else
                    <i class="fas fa-layer-group text-2xl"></i>
                @endif
                {{ $companyName }}
            </div>
            <h1>You have successfully configured the platform.</h1>
            <p>Ready to deploy premium digital invitations with aesthetic designs, realtime RSVPs, and automated payments.</p>
            <p class="text-xs text-gray-500 mb-6" style="margin-top: -12px;">Move your cursor around to interact with the antigravity grid.</p>
            
            <div class="hero-links">
                <a href="{{ route('register') }}">Join Network</a> <span style="color:#ddd">|</span> 
                <a href="{{ route('marketing.trial') }}">Explore Demo</a>
            </div>
        </div>
    </section>

    <!-- Templates Section -->
    <section id="templates" class="section">
        <div class="container">
            <div class="section-header">
                <h2>Design Library</h2>
                <p>Minimalist, elegant, and fully responsive templates engineered for optimal reading experiences on all devices.</p>
            </div>
            
            <div class="grid-3">
                @foreach($templates as $template)
                <div class="clean-card">
                    <div class="template-thumb">
                        @if($template->thumbnail)
                            <img src="{{ asset('storage/' . $template->thumbnail) }}" alt="{{ $template->name }}">
                        @else
                            <i class="fas fa-images text-3xl"></i>
                        @endif
                    </div>
                    <div class="template-meta">
                        <h3>{{ $template->name }}</h3>
                        <div class="chip">{{ ucfirst($template->category) }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="section section-alt">
        <div class="container">
            <div class="section-header">
                <h2>Simple, transparent pricing</h2>
                <p>No hidden fees. Choose the right tier for your operational needs.</p>
            </div>

            <div class="grid-3">
                @foreach($packages as $package)
                <div class="pricing-card {{ $package->is_recommended ? 'popular' : '' }}">
                    @if($package->is_recommended)
                        <div class="pricing-badge">RECOMMENDED</div>
                    @endif
                    <div class="price-title">{{ $package->name }}</div>
                    <div class="price-desc">{{ $package->description }}</div>
                    <div class="price-amount">
                        Rp{{ number_format((float) $package->price, 0, ',', '.') }}
                        <span>/ {{ ($package->billing_type ?? 'one_time') === 'subscription' ? ($package->billing_cycle ?? 'mo') : 'once' }}</span>
                    </div>
                    
                    <ul class="feature-list">
                        <li><i class="fas fa-check"></i> Max {{ number_format((int) $package->max_guests) }} guests</li>
                        <li><i class="fas fa-check"></i> {{ number_format((int) $package->max_photos) }} gallery photos</li>
                        <li><i class="fas fa-check"></i> {{ number_format((int) ($package->max_invitations ?? 1)) }} active links</li>
                        @foreach(array_slice($package->features ?? [], 0, 4) as $feature)
                        <li><i class="fas fa-check"></i> {{ $feature }}</li>
                        @endforeach
                    </ul>
                    
                    <a href="{{ route('register') }}" class="btn-dark" style="text-align:center; justify-content:center; width: 100%;">Get Started</a>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="section">
        <div class="container">
            <div class="about-grid">
                <div class="about-text">
                    <h3>Engineered for scale.</h3>
                    <p>Designed perfectly for creative studios and wedding vendors. Everything from trial funnels, secure checkouts, guest management, to realtime analytics is baked directly into the core.</p>
                    <p>Provide an incredible experience for your clients without worrying about the underlying architecture.</p>
                    <a href="{{ route('login') }}" class="btn-dark mt-4">Access Dashboard</a>
                </div>
                
                <div class="stats-grid">
                    <div class="stat-box">
                        <div class="stat-num">{{ $templates->count() }}+</div>
                        <div class="stat-lbl">Premium Themes</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-num">{{ $packages->count() }}</div>
                        <div class="stat-lbl">Tier Options</div>
                    </div>
                    <div class="stat-box" style="grid-column: span 2;">
                        <div class="stat-num text-center">Realtime</div>
                        <div class="stat-lbl text-center">RSVP Analytics & Cloud Infrastructure</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            &copy; {{ date('Y') }} {{ $companyName }}. Crafted with precision.
        </div>
    </footer>

    <!-- Interactive Canvas Script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const canvas = document.getElementById('particleCanvas');
            const ctx = canvas.getContext('2d');
            let width, height;
            let particles = [];
            // Track mouse - default out of bounds so anim looks calm
            let mouse = { x: -1000, y: -1000, active: false };

            function init() {
                // We make the canvas cover the window, but within hero
                width = canvas.width = window.innerWidth;
                const hero = document.querySelector('.hero');
                height = canvas.height = hero.offsetHeight;
                createParticles();
            }

            function createParticles() {
                particles = [];
                // The grid density
                const spacing = 35;
                const cols = Math.floor(width / spacing);
                const rows = Math.floor(height / spacing);

                for(let i = 0; i < cols; i++) {
                    for(let j = 0; j < rows; j++) {
                        // Offset loosely to the center forming a general dispersion
                        const x = i * spacing + spacing / 2;
                        const y = j * spacing + spacing / 2;
                        
                        // Calculating distance to center to fake a spherical/radial distribution gradient
                        const cx = width/2;
                        const cy = height/2;
                        const distFromCenter = Math.sqrt((cx-x)**2 + (cy-y)**2);
                        
                        // Introduce empty space if outside a "sphere" or make it random. Let's make it map the whole screen.
                        
                        // Define color palette matching reference: purple to orange to blue
                        // Left side purple, bottom left orange, right side blueish
                        const normalizedX = x / width;
                        const normalizedY = y / height;
                        
                        // Hue formula for exact aesthetic
                        let hue = 260; // purple base
                        if (normalizedX < 0.5) {
                            // Left side - shift towards orange/red (0-60)
                            hue = 260 + (normalizedY * 100); 
                            if (hue > 360) hue = hue - 360; 
                        } else {
                            // Right side - shift towards cool blues
                            hue = 220 + (normalizedX * 40);
                        }

                        // We only want the left side to be extremely dense, right side sparse
                        // like the antigravity example image.
                        const probability = 1 - (normalizedX * 0.8);
                        
                        if (Math.random() < probability) {
                            particles.push({
                                baseX: x,
                                baseY: y,
                                x: x,
                                y: y,
                                size: 1.5,
                                color: `hsl(${hue}, 80%, 55%)`,
                                // Slanted dash angle
                                angle: Math.PI / 4,
                                floatingAngle: Math.random() * Math.PI * 2
                            });
                        }
                    }
                }
            }

            // Mouse interaction
            window.addEventListener('mousemove', (e) => {
                const rect = canvas.getBoundingClientRect();
                mouse.x = e.clientX - rect.left;
                mouse.y = e.clientY - rect.top;
                mouse.active = true;
            });
            window.addEventListener('mouseleave', () => mouse.active = false);
            window.addEventListener('resize', init);

            function animate() {
                ctx.clearRect(0, 0, width, height);

                particles.forEach(p => {
                    // Interaction logic
                    let targetX = p.baseX;
                    let targetY = p.baseY;

                    if (mouse.active) {
                        const dx = mouse.x - p.baseX;
                        const dy = mouse.y - p.baseY;
                        const dist = Math.sqrt(dx*dx + dy*dy);
                        
                        // Radius of cursor repulsion
                        const maxDist = 180; 
                        
                        if (dist < maxDist) {
                            // Calculate force pushing away
                            const force = (maxDist - dist) / maxDist;
                            // Push radially outward
                            const pushX = (dx / dist) * force * -50; 
                            const pushY = (dy / dist) * force * -50;
                            
                            targetX += pushX;
                            targetY += pushY;
                        }
                    }

                    // Gentle floating wave effect independent of mouse
                    targetX += Math.cos(p.floatingAngle) * 4;
                    targetY += Math.sin(p.floatingAngle) * 4;
                    p.floatingAngle += 0.015;

                    // Ease position towards target
                    p.x += (targetX - p.x) * 0.08;
                    p.y += (targetY - p.y) * 0.08;

                    // Draw a modern slanted dash instead of a circle
                    ctx.fillStyle = p.color;
                    ctx.beginPath();
                    ctx.save();
                    ctx.translate(p.x, p.y);
                    // Add slight rotation jitter based on floating
                    ctx.rotate(p.angle + Math.sin(p.floatingAngle)*0.1); 
                    // Draw tiny rectangles
                    ctx.fillRect(-2.5, -1, 5, 2);
                    ctx.restore();
                });

                requestAnimationFrame(animate);
            }
            
            init();
            animate();
        });
    </script>
</body>
</html>
