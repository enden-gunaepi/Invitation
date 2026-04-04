<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — {{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Antigravity Aesthetics */
        :root {
            --bg: #ffffff;
            --text-main: #111111;
            --text-muted: #666666;
            --border: #eaeaea;
            --btn-bg: #111111;
            --btn-text: #ffffff;
            --btn-hover: #333333;
            --input-bg: #fafafa;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', -apple-system, sans-serif; background: var(--bg); color: var(--text-main); -webkit-font-smoothing: antialiased; min-height: 100vh; display: flex; overflow: hidden; }
        
        .split-layout { display: flex; width: 100vw; height: 100vh; }

        /* Left Side: Animation */
        .visual-side {
            flex: 1;
            position: relative;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border-right: 1px solid var(--border);
        }

        #particleCanvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        .visual-content {
            position: relative;
            z-index: 10;
            text-align: center;
            max-width: 420px;
            pointer-events: none;
            background: radial-gradient(circle, rgba(255,255,255,1) 30%, rgba(255,255,255,0) 100%);
            padding: 3rem;
            border-radius: 50%;
        }
        
        .visual-content h2 { font-size: 2rem; font-weight: 500; letter-spacing: -0.03em; margin-bottom: 1rem; color: #111; }
        .visual-content p { color: var(--text-muted); line-height: 1.6; font-size: 0.95rem; }

        /* Right Side: Form */
        .form-side {
            flex: 1;
            max-width: 550px;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 3rem;
            position: relative;
            z-index: 10;
        }

        .auth-container { width: 100%; max-width: 380px; margin: 0 auto; }
        
        .auth-logo {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            font-size: 1.25rem;
            margin-bottom: 3rem;
            color: var(--text-main);
            text-decoration: none;
            letter-spacing: -0.02em;
        }
        
        .auth-header { margin-bottom: 2rem; }
        .auth-title { font-size: 1.75rem; font-weight: 500; letter-spacing: -0.03em; margin-bottom: 8px; }
        .auth-subtitle { font-size: 0.95rem; color: var(--text-muted); }

        .form-group { margin-bottom: 1.25rem; }
        .form-label { font-size: 0.85rem; font-weight: 500; color: var(--text-main); margin-bottom: 6px; display: block; }
        .form-input { 
            width: 100%; 
            padding: 0.75rem 1rem; 
            border: 1px solid var(--border); 
            border-radius: 8px; 
            background: var(--input-bg); 
            color: var(--text-main); 
            font-size: 0.95rem; 
            font-family: inherit; 
            transition: all 0.2s ease; 
        }
        .form-input:focus { outline: none; border-color: #111; background: #fff; box-shadow: 0 0 0 3px rgba(0,0,0,0.05); }
        .form-input::placeholder { color: #aaa; }

        .form-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; margin-top: 0.5rem; }
        .form-checkbox { display: flex; align-items: center; gap: 8px; font-size: 0.85rem; color: var(--text-muted); cursor: pointer; }
        .form-checkbox input { width: 16px; height: 16px; accent-color: #111; }
        .form-link { font-size: 0.85rem; color: var(--text-main); text-decoration: underline; font-weight: 500; }
        
        .btn-auth { 
            width: 100%; 
            padding: 0.8rem; 
            border: none; 
            border-radius: 8px; 
            background: var(--btn-bg); 
            color: var(--btn-text); 
            font-size: 0.95rem; 
            font-weight: 500; 
            cursor: pointer; 
            transition: all 0.2s ease; 
        }
        .btn-auth:hover { background: var(--btn-hover); transform: translateY(-1px); }

        .auth-footer { margin-top: 2rem; font-size: 0.9rem; color: var(--text-muted); }
        .auth-footer a { color: var(--text-main); text-decoration: underline; font-weight: 500; }

        .error-msg { font-size: 0.8rem; color: #ff3b30; margin-top: 6px; }
        .status-msg { padding: 12px 16px; border-radius: 8px; background: rgba(52,199,89,0.08); border: 1px solid rgba(52,199,89,0.15); color: #34c759; font-size: 0.85rem; margin-bottom: 1.5rem; }

        @media (max-width: 900px) {
            .visual-side { display: none; }
            .form-side { max-width: 100%; padding: 2rem; }
        }
    </style>
</head>
<body>
    @php
        $mainAdmin = \App\Models\User::where('role', 'admin')->first();
        $companyLogo = $mainAdmin && $mainAdmin->company_logo ? Storage::url($mainAdmin->company_logo) : null;
        $companyName = $mainAdmin && $mainAdmin->company_name ? $mainAdmin->company_name : config('app.name');
    @endphp

    <div class="split-layout">
        <!-- Visual & Animation Side -->
        <div class="visual-side">
            <canvas id="particleCanvas"></canvas>
            <div class="visual-content">
                <h2>The Standard</h2>
                <p>Welcome back to the unified control center. Oversee events, RSVPs, and operational pipelines with unmatched fluidity.</p>
            </div>
        </div>

        <!-- Form Side -->
        <div class="form-side">
            <div class="auth-container">
                <a href="/" class="auth-logo">
                    @if($companyLogo)
                        <img src="{{ $companyLogo }}" alt="Logo" style="height: 28px; border-radius: 4px;">
                    @else
                        <i class="fas fa-layer-group"></i>
                    @endif
                    {{ $companyName }}
                </a>

                <div class="auth-header">
                    <h1 class="auth-title">Log in</h1>
                    <p class="auth-subtitle">Enter your credentials to access your account</p>
                </div>

                @if(session('status'))
                    <div class="status-msg">{{ session('status') }}</div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Email address</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-input" placeholder="name@company.com" required autofocus>
                        @error('email')<p class="error-msg">{{ $message }}</p>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <input id="password" type="password" name="password" class="form-input" placeholder="••••••••" required>
                        @error('password')<p class="error-msg">{{ $message }}</p>@enderror
                    </div>

                    <div class="form-row">
                        <label class="form-checkbox">
                            <input type="checkbox" name="remember"> Remember me
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="form-link">Forgot password?</a>
                        @endif
                    </div>

                    <button type="submit" class="btn-auth">Sign in</button>
                </form>

                <div class="auth-footer">
                    Don't have an account? <a href="{{ route('register') }}">Sign up</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Interactive Canvas Script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const canvas = document.getElementById('particleCanvas');
            if(!canvas) return;
            const ctx = canvas.getContext('2d');
            let width, height;
            let particles = [];
            let mouse = { x: -1000, y: -1000, active: false };

            function init() {
                const parent = canvas.parentElement;
                width = canvas.width = parent.offsetWidth;
                height = canvas.height = parent.offsetHeight;
                createParticles();
            }

            function createParticles() {
                particles = [];
                const spacing = 35;
                const cols = Math.floor(width / spacing);
                const rows = Math.floor(height / spacing);

                for(let i = 0; i < cols; i++) {
                    for(let j = 0; j < rows; j++) {
                        const x = i * spacing + spacing / 2;
                        const y = j * spacing + spacing / 2;
                        
                        const normalizedX = x / width;
                        const normalizedY = y / height;
                        
                        // Hue formula matching aesthetic
                        let hue = 260; // purple base
                        if (normalizedX < 0.5) {
                            hue = 260 + (normalizedY * 100); 
                            if (hue > 360) hue = hue - 360; 
                        } else {
                            hue = 220 + (normalizedX * 40);
                        }

                        // Consistent dispersion logic
                        const probability = 1 - (normalizedX * 0.7);
                        
                        if (Math.random() < probability) {
                            particles.push({
                                baseX: x,
                                baseY: y,
                                x: x,
                                y: y,
                                size: 1.5,
                                color: `hsl(${hue}, 80%, 55%)`,
                                angle: Math.PI / 4,
                                floatingAngle: Math.random() * Math.PI * 2
                            });
                        }
                    }
                }
            }

            canvas.parentElement.addEventListener('mousemove', (e) => {
                const rect = canvas.getBoundingClientRect();
                mouse.x = e.clientX - rect.left;
                mouse.y = e.clientY - rect.top;
                mouse.active = true;
            });
            canvas.parentElement.addEventListener('mouseleave', () => mouse.active = false);
            window.addEventListener('resize', init);

            function animate() {
                ctx.clearRect(0, 0, width, height);

                particles.forEach(p => {
                    let targetX = p.baseX;
                    let targetY = p.baseY;

                    if (mouse.active) {
                        const dx = mouse.x - p.baseX;
                        const dy = mouse.y - p.baseY;
                        const dist = Math.sqrt(dx*dx + dy*dy);
                        
                        const maxDist = 180; 
                        if (dist < maxDist) {
                            const force = (maxDist - dist) / maxDist;
                            const pushX = (dx / dist) * force * -50; 
                            const pushY = (dy / dist) * force * -50;
                            targetX += pushX;
                            targetY += pushY;
                        }
                    }

                    targetX += Math.cos(p.floatingAngle) * 4;
                    targetY += Math.sin(p.floatingAngle) * 4;
                    p.floatingAngle += 0.015;

                    p.x += (targetX - p.x) * 0.08;
                    p.y += (targetY - p.y) * 0.08;

                    ctx.fillStyle = p.color;
                    ctx.beginPath();
                    ctx.save();
                    ctx.translate(p.x, p.y);
                    ctx.rotate(p.angle + Math.sin(p.floatingAngle)*0.1); 
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
