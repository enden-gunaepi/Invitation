<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coming Soon — {{ $brandAppName ?? config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #ffffff;
            --text-main: #111111;
            --text-muted: #666666;
            --pink-accent: #db2777; /* Rose/Pink Accent */
            --pink-light: rgba(219, 39, 119, 0.08);
            --border: #eaeaea;
        }
        @media (prefers-color-scheme: dark) {
            :root {
                --bg: #0b0b0f;
                --text-main: #f3f4f6;
                --text-muted: #9ca3af;
                --pink-accent: #ec4899; /* Vibrant Pink */
                --pink-light: rgba(236, 72, 153, 0.12);
                --border: #1f2937;
            }
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Geist', sans-serif;
            background: var(--bg);
            color: var(--text-main);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 2rem;
            position: relative;
            overflow: hidden;
        }
        /* Gradient glows for premium styling */
        .glow {
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, var(--pink-light) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
        }
        .glow-1 { top: -100px; left: -100px; }
        .glow-2 { bottom: -100px; right: -100px; }
        
        .container {
            max-width: 520px;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 10;
        }
        .mascot {
            height: 180px;
            width: auto;
            object-fit: contain;
            margin-bottom: 2rem;
            filter: drop-shadow(0 10px 20px rgba(0,0,0,0.05));
            animation: float 4s ease-in-out infinite;
        }
        .badge {
            display: inline-block;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: var(--pink-accent);
            background: var(--pink-light);
            padding: 0.4rem 1rem;
            border-radius: 9999px;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(236, 72, 153, 0.2);
        }
        h1 {
            font-size: 2.5rem;
            font-weight: 700;
            letter-spacing: -0.04em;
            margin-bottom: 1rem;
            line-height: 1.1;
        }
        h1 span {
            color: var(--pink-accent);
        }
        p {
            font-size: 1rem;
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 2.5rem;
            text-wrap: balance;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.8rem 2rem;
            border-radius: 9999px;
            background: var(--text-main);
            color: var(--bg);
            font-size: 0.95rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
            border: 1px solid var(--text-main);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .btn:hover {
            transform: translateY(-2px);
            background: transparent;
            color: var(--text-main);
            border-color: var(--text-main);
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body>
    <div class="glow glow-1"></div>
    <div class="glow glow-2"></div>
    
    <div class="container">
        <span class="badge">Fitur Demo</span>
        <img src="{{ asset('assets/maskot/pilihtemplate.png') }}" alt="Demo Coming Soon" class="mascot">
        <h1>Segera <span>Hadir</span></h1>
        <p>Kami sedang menyiapkan demo katalog interaktif terbaik agar Anda dapat mencoba mendesain & menyesuaikan undangan digital Anda secara langsung.</p>
        <a href="/" class="btn">Kembali ke Beranda</a>
    </div>
</body>
</html>
