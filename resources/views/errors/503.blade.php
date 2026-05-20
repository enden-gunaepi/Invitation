<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>503 — Layanan Sedang Pemeliharaan</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #ffffff;
            --text-main: #111111;
            --text-muted: #666666;
            --border: #eaeaea;
            --btn-bg: #111111;
            --btn-text: #ffffff;
        }
        @media (prefers-color-scheme: dark) {
            :root {
                --bg: #0b0b0f;
                --text-main: #f3f4f6;
                --text-muted: #9ca3af;
                --border: #1f2937;
                --btn-bg: #f3f4f6;
                --btn-text: #0b0b0f;
            }
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background: var(--bg);
            color: var(--text-main);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 2rem;
            overflow: hidden;
        }
        .container {
            max-width: 480px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .mascot {
            height: 200px;
            width: auto;
            object-fit: contain;
            margin-bottom: 2.5rem;
            filter: drop-shadow(0 15px 25px rgba(0,0,0,0.1));
            animation: float 4s ease-in-out infinite;
        }
        h1 {
            font-size: 2.25rem;
            font-weight: 600;
            letter-spacing: -0.03em;
            margin-bottom: 0.75rem;
        }
        p {
            font-size: 1rem;
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 2rem;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.8rem 2rem;
            border-radius: 8px;
            background: var(--btn-bg);
            color: var(--btn-text);
            font-size: 0.95rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .btn:hover {
            transform: translateY(-2px);
            opacity: 0.9;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-12px); }
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="{{ asset('assets/maskot/maintenance.png') }}" alt="Maintenance Mascot" class="mascot">
        <h1>Layanan Sedang Pemeliharaan</h1>
        <p>Saat ini kami sedang melakukan pembaruan berkala untuk meningkatkan performa sistem. Kami akan segera kembali dalam beberapa saat.</p>
        <a href="/" class="btn">Segarkan Halaman</a>
    </div>
</body>
</html>
