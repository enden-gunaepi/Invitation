<!DOCTYPE html>
<html lang="id" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — {{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --bg: #f5f5f7; --bg-secondary: #ffffff; --bg-tertiary: #f0f0f2; --border: #e5e5ea;
            --text: #1d1d1f; --text-secondary: #86868b; --text-tertiary: #aeaeb2;
            --accent: #0071e3; --accent-hover: #0077ED; --accent-bg: rgba(0,113,227,0.08);
            --card-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 4px 12px rgba(0,0,0,0.06);
            --radius: 12px; --radius-sm: 8px;
        }
        .dark {
            --bg: #1c1c1e; --bg-secondary: #2c2c2e; --bg-tertiary: #3a3a3c; --border: #38383a;
            --text: #f5f5f7; --text-secondary: #98989d; --text-tertiary: #636366;
            --accent: #0a84ff; --accent-hover: #409cff; --accent-bg: rgba(10,132,255,0.12);
            --card-shadow: 0 1px 3px rgba(0,0,0,0.2), 0 4px 12px rgba(0,0,0,0.15);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', -apple-system, sans-serif; background: var(--bg); color: var(--text); -webkit-font-smoothing: antialiased; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }

        .auth-container { width: 100%; max-width: 420px; }
        .auth-header { text-align: center; margin-bottom: 32px; }
        .auth-logo { width: 48px; height: 48px; border-radius: 12px; background: linear-gradient(135deg, var(--accent), #5856d6); display: inline-flex; align-items: center; justify-content: center; color: white; font-size: 20px; margin-bottom: 16px; }
        .auth-title { font-size: 22px; font-weight: 700; margin-bottom: 6px; }
        .auth-subtitle { font-size: 14px; color: var(--text-secondary); }

        .auth-card { background: var(--bg-secondary); border: 1px solid var(--border); border-radius: var(--radius); box-shadow: var(--card-shadow); padding: 32px; }

        .form-group { margin-bottom: 20px; }
        .form-label { font-size: 13px; font-weight: 500; color: var(--text); margin-bottom: 6px; display: block; }
        .input-wrapper { position: relative; }
        .input-wrapper i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-tertiary); font-size: 14px; }
        .form-input { width: 100%; padding: 11px 14px 11px 40px; border: 1px solid var(--border); border-radius: var(--radius-sm); background: var(--bg); color: var(--text); font-size: 14px; font-family: inherit; transition: all 0.2s ease; }
        .form-input:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-bg); }
        .form-input::placeholder { color: var(--text-tertiary); }

        .form-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; }
        .form-checkbox { display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--text-secondary); cursor: pointer; }
        .form-checkbox input { width: 16px; height: 16px; accent-color: var(--accent); }
        .form-link { font-size: 13px; color: var(--accent); text-decoration: none; font-weight: 500; }
        .form-link:hover { text-decoration: underline; }

        .btn-auth { width: 100%; padding: 12px; border: none; border-radius: var(--radius-sm); background: var(--accent); color: white; font-size: 14px; font-weight: 600; font-family: inherit; cursor: pointer; transition: all 0.2s ease; }
        .btn-auth:hover { background: var(--accent-hover); }

        .auth-footer { text-align: center; margin-top: 20px; font-size: 13px; color: var(--text-secondary); }
        .auth-footer a { color: var(--accent); text-decoration: none; font-weight: 600; }
        .auth-footer a:hover { text-decoration: underline; }

        .error-msg { font-size: 12px; color: #ff3b30; margin-top: 6px; }
        .status-msg { padding: 12px 16px; border-radius: var(--radius-sm); background: rgba(52,199,89,0.08); border: 1px solid rgba(52,199,89,0.15); color: #34c759; font-size: 13px; margin-bottom: 20px; }

        .theme-btn { position: fixed; top: 16px; right: 16px; width: 36px; height: 36px; border-radius: 50%; background: var(--bg-secondary); border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 14px; transition: all 0.2s ease; }
        .theme-btn:hover { background: var(--bg-tertiary); }
    </style>
</head>
<body>
    <button class="theme-btn" @click="darkMode = !darkMode" title="Toggle theme">
        <span x-show="!darkMode">🌙</span>
        <span x-show="darkMode">☀️</span>
    </button>

    <div class="auth-container">
        <div class="auth-header">
            <div class="auth-logo"><i class="fas fa-envelope-open-text"></i></div>
            <h1 class="auth-title">Selamat Datang</h1>
            <p class="auth-subtitle">Masuk ke akun Anda untuk melanjutkan</p>
        </div>

        <div class="auth-card">
            @if(session('status'))
                <div class="status-msg">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope"></i>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-input" placeholder="nama@email.com" required autofocus>
                    </div>
                    @error('email')<p class="error-msg">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input id="password" type="password" name="password" class="form-input" placeholder="••••••••" required>
                    </div>
                    @error('password')<p class="error-msg">{{ $message }}</p>@enderror
                </div>

                <div class="form-row">
                    <label class="form-checkbox">
                        <input type="checkbox" name="remember"> Ingat saya
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="form-link">Lupa password?</a>
                    @endif
                </div>

                <button type="submit" class="btn-auth">Masuk</button>
            </form>
        </div>

        <div class="auth-footer">
            Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a>
        </div>
    </div>
</body>
</html>
