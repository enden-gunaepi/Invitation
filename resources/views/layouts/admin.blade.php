<!DOCTYPE html>
<html lang="id" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true', sidebarOpen: false }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --sidebar-w: 260px;
            --topbar-h: 56px;
            --radius: 12px;
            --radius-sm: 8px;
            /* Light */
            --bg: #f5f5f7;
            --bg-secondary: #ffffff;
            --bg-tertiary: #f0f0f2;
            --border: #e5e5ea;
            --text: #1d1d1f;
            --text-secondary: #86868b;
            --text-tertiary: #aeaeb2;
            --accent: #0071e3;
            --accent-hover: #0077ED;
            --accent-bg: rgba(0,113,227,0.08);
            --sidebar-bg: rgba(245,245,247,0.85);
            --card-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.06);
            --hover-bg: rgba(0,0,0,0.04);
            --danger: #ff3b30;
            --success: #34c759;
            --warning: #ff9500;
        }
        .dark {
            --bg: #1c1c1e;
            --bg-secondary: #2c2c2e;
            --bg-tertiary: #3a3a3c;
            --border: #38383a;
            --text: #f5f5f7;
            --text-secondary: #98989d;
            --text-tertiary: #636366;
            --accent: #0a84ff;
            --accent-hover: #409cff;
            --accent-bg: rgba(10,132,255,0.12);
            --sidebar-bg: rgba(28,28,30,0.92);
            --card-shadow: 0 1px 3px rgba(0,0,0,0.2), 0 1px 2px rgba(0,0,0,0.15);
            --hover-bg: rgba(255,255,255,0.06);
            --danger: #ff453a;
            --success: #30d158;
            --warning: #ff9f0a;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; background: var(--bg); color: var(--text); -webkit-font-smoothing: antialiased; font-size: 14px; }

        /* ===== SIDEBAR ===== */
        .sidebar {
            position: fixed; top: 0; left: 0; bottom: 0; width: var(--sidebar-w); z-index: 50;
            background: var(--sidebar-bg); backdrop-filter: blur(20px) saturate(180%);
            border-right: 1px solid var(--border); display: flex; flex-direction: column;
            transition: transform 0.3s cubic-bezier(0.4,0,0.2,1);
        }
        .sidebar-header { padding: 16px 20px; display: flex; align-items: center; gap: 10px; }
        .sidebar-logo { width: 32px; height: 32px; border-radius: 8px; background: linear-gradient(135deg, var(--accent), #5856d6); display: flex; align-items: center; justify-content: center; color: white; font-size: 14px; }
        .sidebar-brand { font-size: 15px; font-weight: 700; color: var(--text); }

        .sidebar-nav { flex: 1; overflow-y: auto; padding: 8px 12px; }
        .nav-section { margin-bottom: 20px; }
        .nav-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-tertiary); padding: 0 8px; margin-bottom: 4px; }

        .nav-item { display: flex; align-items: center; gap: 10px; padding: 8px 10px; border-radius: var(--radius-sm); color: var(--text-secondary); font-size: 13px; font-weight: 500; text-decoration: none; transition: all 0.15s ease; margin-bottom: 1px; cursor: pointer; }
        .nav-item:hover { background: var(--hover-bg); color: var(--text); }
        .nav-item.active { background: var(--accent-bg); color: var(--accent); font-weight: 600; }
        .nav-item i { width: 18px; text-align: center; font-size: 14px; }
        .nav-badge { margin-left: auto; font-size: 11px; font-weight: 600; background: var(--danger); color: white; padding: 1px 7px; border-radius: 10px; min-width: 18px; text-align: center; }

        .sidebar-footer { padding: 12px 16px; border-top: 1px solid var(--border); }
        .user-trigger { width: 100%; background: transparent; border: 0; padding: 0; text-align: left; cursor: pointer; }
        .user-info { display: flex; align-items: center; gap: 10px; }
        .user-avatar { width: 32px; height: 32px; border-radius: 50%; background: var(--accent-bg); display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; color: var(--accent); }
        .user-name { font-size: 13px; font-weight: 600; color: var(--text); }
        .user-role { font-size: 11px; color: var(--text-secondary); }
        .user-menu {
            margin-top: 10px;
            border: 1px solid var(--border);
            background: var(--bg-secondary);
            border-radius: 10px;
            padding: 6px;
        }
        .user-menu a, .user-menu button {
            width: 100%;
            border: 0;
            background: transparent;
            color: var(--text-secondary);
            text-align: left;
            font-size: 12px;
            padding: 7px 8px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 7px;
            text-decoration: none;
            cursor: pointer;
        }
        .user-menu a:hover, .user-menu button:hover { background: var(--hover-bg); color: var(--text); }
        .user-menu .danger { color: var(--danger); }

        /* ===== TOPBAR ===== */
        .topbar {
            position: fixed; top: 0; right: 0; left: var(--sidebar-w); height: var(--topbar-h); z-index: 40;
            background: var(--sidebar-bg); backdrop-filter: blur(20px) saturate(180%);
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between; padding: 0 24px;
        }
        .topbar-left { display: flex; align-items: center; gap: 12px; }
        .topbar-title { font-size: 15px; font-weight: 700; color: var(--text); }
        .topbar-subtitle { font-size: 12px; color: var(--text-secondary); }
        .topbar-right { display: flex; align-items: center; gap: 8px; }

        .topbar-btn { width: 36px; height: 36px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--bg-secondary); color: var(--text-secondary); display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.15s ease; font-size: 14px; }
        .topbar-btn:hover { background: var(--hover-bg); color: var(--text); border-color: var(--text-tertiary); }
        .mobile-toggle { display: none; }

        /* Dark/Light Toggle */
        .theme-toggle { position: relative; width: 44px; height: 24px; border-radius: 12px; background: var(--bg-tertiary); border: 1px solid var(--border); cursor: pointer; transition: all 0.3s ease; }
        .theme-toggle .toggle-dot { position: absolute; top: 2px; left: 2px; width: 18px; height: 18px; border-radius: 50%; background: var(--bg-secondary); box-shadow: 0 1px 3px rgba(0,0,0,0.15); transition: transform 0.3s cubic-bezier(0.4,0,0.2,1); display: flex; align-items: center; justify-content: center; font-size: 10px; }
        .dark .theme-toggle { background: var(--accent); border-color: var(--accent); }
        .dark .theme-toggle .toggle-dot { transform: translateX(20px); }

        /* ===== MAIN ===== */
        .main-content { margin-left: var(--sidebar-w); padding-top: var(--topbar-h); min-height: 100vh; }
        .page-content { padding: 24px; max-width: 1400px; }
        .page-shell { display: grid; gap: 18px; }

        /* ===== COMPONENTS ===== */
        .card { background: var(--bg-secondary); border: 1px solid var(--border); border-radius: var(--radius); box-shadow: var(--card-shadow); transition: all 0.2s ease; }
        .card:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.08); }

        .stat-card { padding: 20px; }
        .stat-icon { width: 36px; height: 36px; border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; font-size: 16px; margin-bottom: 12px; }
        .stat-value { font-size: 24px; font-weight: 700; color: var(--text); line-height: 1; }
        .stat-label { font-size: 12px; color: var(--text-secondary); margin-top: 4px; }

        /* Table */
        .table-container { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        thead th { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-secondary); padding: 10px 16px; text-align: left; border-bottom: 1px solid var(--border); }
        tbody td { padding: 12px 16px; font-size: 13px; border-bottom: 1px solid var(--border); color: var(--text); }
        tbody tr { transition: background 0.1s ease; }
        tbody tr:hover { background: var(--hover-bg); }
        tbody tr:last-child td { border-bottom: none; }

        /* Badges */
        .badge { display: inline-flex; align-items: center; padding: 2px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; }
        .badge-success { background: rgba(52,199,89,0.12); color: var(--success); }
        .badge-warning { background: rgba(255,149,0,0.12); color: var(--warning); }
        .badge-danger { background: rgba(255,59,48,0.12); color: var(--danger); }
        .badge-info { background: var(--accent-bg); color: var(--accent); }
        .badge-default { background: var(--hover-bg); color: var(--text-secondary); }

        .badge-active { @extend .badge-success; background: rgba(52,199,89,0.12); color: var(--success); }
        .badge-pending { background: rgba(255,149,0,0.12); color: var(--warning); }
        .badge-draft { background: var(--hover-bg); color: var(--text-secondary); }
        .badge-rejected { background: rgba(255,59,48,0.12); color: var(--danger); }
        .badge-admin { background: rgba(88,86,214,0.12); color: #5856d6; }
        .badge-client { background: var(--accent-bg); color: var(--accent); }

        /* Forms */
        .form-label { font-size: 13px; font-weight: 500; color: var(--text); margin-bottom: 6px; display: block; }
        .form-input { width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: var(--radius-sm); background: var(--bg-secondary); color: var(--text); font-size: 14px; font-family: inherit; transition: all 0.2s ease; }
        .form-input:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-bg); }
        .form-input::placeholder { color: var(--text-tertiary); }
        select.form-input { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2386868b' d='M6 8.825L1.175 4 2.238 2.938 6 6.7l3.763-3.762L10.825 4z'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; padding-right: 32px; }

        /* Buttons */
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 9px 16px; border-radius: var(--radius-sm); font-size: 13px; font-weight: 600; font-family: inherit; cursor: pointer; transition: all 0.15s ease; border: none; text-decoration: none; }
        .btn-primary { background: var(--accent); color: white; }
        .btn-primary:hover { background: var(--accent-hover); }
        .btn-secondary { background: var(--bg-tertiary); color: var(--text); border: 1px solid var(--border); }
        .btn-secondary:hover { background: var(--hover-bg); border-color: var(--text-tertiary); }
        .btn-danger { background: rgba(255,59,48,0.1); color: var(--danger); }
        .btn-danger:hover { background: rgba(255,59,48,0.18); }
        .btn-sm { padding: 6px 10px; font-size: 12px; }
        .btn-icon { width: 32px; height: 32px; padding: 0; justify-content: center; }

        /* Search */
        .search-bar { position: relative; }
        .search-bar input { padding-left: 36px; }
        .search-bar i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-tertiary); font-size: 13px; }

        /* Pagination */
        .pagination { display: flex; gap: 4px; }
        .pagination a, .pagination span { padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 500; text-decoration: none; }

        /* Toast */
        .toast {
            position: fixed; top: 16px; right: 16px; z-index: 200;
            background: var(--bg-secondary); border: 1px solid var(--border);
            border-radius: var(--radius); box-shadow: 0 4px 20px rgba(0,0,0,0.12);
            padding: 14px 20px; font-size: 13px; font-weight: 500;
            display: flex; align-items: center; gap: 10px;
            animation: slideIn 0.3s ease, fadeOut 0.3s ease 4s forwards;
        }
        @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        @keyframes fadeOut { to { opacity: 0; transform: translateY(-10px); } }

        /* Mobile Overlay */
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 45; }

        /* Mobile Bottom Dock */
        .mobile-dock {
            position: fixed;
            left: 50%;
            bottom: 14px;
            transform: translateX(-50%);
            width: min(360px, calc(100% - 24px));
            height: 62px;
            border-radius: 22px;
            background: linear-gradient(180deg, #0f172a, #111827);
            border: 1px solid rgba(148, 163, 184, .22);
            box-shadow: 0 16px 35px rgba(2, 6, 23, .45);
            z-index: 80;
            display: none;
            align-items: center;
            padding: 0 10px;
        }
        .mobile-dock-track {
            width: 100%;
            display: flex;
            gap: 10px;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            scrollbar-width: none;
            -ms-overflow-style: none;
            padding: 0 2px;
        }
        .mobile-dock-track::-webkit-scrollbar { display: none; }
        .mobile-dock-slot {
            flex: 0 0 calc((100% - 20px) / 3);
            display: flex;
            justify-content: center;
            scroll-snap-align: start;
        }
        .mobile-dock-link {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            color: rgba(255,255,255,.82);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 1rem;
            transition: all .2s ease;
        }
        .mobile-dock-link:hover { color: #fff; transform: translateY(-1px); }
        .mobile-dock-link.active {
            background: rgba(96, 165, 250, .18);
            color: #60a5fa;
            box-shadow: inset 0 0 0 1px rgba(96, 165, 250, .35);
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 1024px) {
            .sidebar { display: none; }
            .sidebar-overlay { display: none !important; }
            .topbar {
                position: relative;
                left: 0;
            }
            .main-content {
                margin-left: 0;
                padding-top: 0;
            }
            .mobile-toggle { display: none !important; }
            .mobile-dock { display: flex; }
            .main-content { padding-bottom: 88px; }
        }
        @media (max-width: 640px) {
            .page-content { padding: 16px; }
            .topbar { padding: 0 16px; }
        }
    </style>
</head>
<body>
    {{-- Toast --}}
    @if(session('success'))
    <div class="toast"><i class="fas fa-check-circle" style="color: var(--success);"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="toast"><i class="fas fa-exclamation-circle" style="color: var(--danger);"></i> {{ session('error') }}</div>
    @endif

    {{-- Mobile Overlay --}}
    <div class="sidebar-overlay" :class="{ 'open': sidebarOpen }" @click="sidebarOpen = false"></div>

    {{-- Sidebar --}}
    <aside class="sidebar" :class="{ 'open': sidebarOpen }">
        <div class="sidebar-header">
            <div class="sidebar-logo"><i class="fas fa-envelope-open-text"></i></div>
            <span class="sidebar-brand">InvitePro</span>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section">
                <div class="nav-label">Menu</div>
                <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-house"></i> Dashboard
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-label">Manajemen</div>
                <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i> Users
                </a>
                <a href="{{ route('admin.invitations.index') }}" class="nav-item {{ request()->routeIs('admin.invitations.*') ? 'active' : '' }}">
                    <i class="fas fa-envelope"></i> Undangan
                    @php $pendingCount = \App\Models\Invitation::where('status', 'pending')->count(); @endphp
                    @if($pendingCount > 0)<span class="nav-badge">{{ $pendingCount }}</span>@endif
                </a>
                <a href="{{ route('admin.templates.index') }}" class="nav-item {{ request()->routeIs('admin.templates.*') ? 'active' : '' }}">
                    <i class="fas fa-palette"></i> Template
                </a>
                <a href="{{ route('admin.packages.index') }}" class="nav-item {{ request()->routeIs('admin.packages.*') ? 'active' : '' }}">
                    <i class="fas fa-cube"></i> Paket
                </a>
                <a href="{{ route('admin.payments.index') }}" class="nav-item {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                    <i class="fas fa-credit-card"></i> Pembayaran
                    @php $pendingPayments = \App\Models\Payment::where('payment_status', 'pending')->count(); @endphp
                    @if($pendingPayments > 0)<span class="nav-badge" style="background: var(--warning);">{{ $pendingPayments }}</span>@endif
                </a>
                <a href="{{ route('admin.affiliate.index') }}" class="nav-item {{ request()->routeIs('admin.affiliate.index') ? 'active' : '' }}">
                    <i class="fas fa-hand-holding-dollar"></i> Affiliate
                    @php $pendingCommissions = \App\Models\AffiliateCommission::where('status', 'pending')->count(); @endphp
                    @if($pendingCommissions > 0)<span class="nav-badge" style="background: var(--warning);">{{ $pendingCommissions }}</span>@endif
                </a>
                <a href="{{ route('admin.affiliate.payouts') }}" class="nav-item {{ request()->routeIs('admin.affiliate.payouts*') ? 'active' : '' }}">
                    <i class="fas fa-wallet"></i> Affiliate Payout
                    @php $pendingPayouts = \App\Models\PayoutRequest::where('status', 'pending')->count(); @endphp
                    @if($pendingPayouts > 0)<span class="nav-badge" style="background: var(--warning);">{{ $pendingPayouts }}</span>@endif
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-label">Sistem</div>
                <a href="{{ route('admin.payment-gateway.index') }}" class="nav-item {{ request()->routeIs('admin.payment-gateway.*') ? 'active' : '' }}">
                    <i class="fas fa-plug"></i> Payment Gateway
                </a>
                <a href="{{ route('admin.system.reliability') }}" class="nav-item {{ request()->routeIs('admin.system.reliability') ? 'active' : '' }}">
                    <i class="fas fa-heart-pulse"></i> Reliability
                    @php $failedJobsCount = \Illuminate\Support\Facades\DB::table('failed_jobs')->count(); @endphp
                    @if($failedJobsCount > 0)<span class="nav-badge">{{ $failedJobsCount }}</span>@endif
                </a>
                <a href="{{ route('admin.settings.index') }}" class="nav-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <i class="fas fa-gear"></i> Pengaturan
                </a>
            </div>
        </nav>

        <div class="sidebar-footer" x-data="{ userMenuOpen: false }">
            <button class="user-trigger" @click="userMenuOpen = !userMenuOpen">
                <div class="user-info">
                    <div class="user-avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
                    <div>
                        <div class="user-name">{{ auth()->user()->name }}</div>
                        <div class="user-role">Administrator</div>
                    </div>
                </div>
            </button>
            <div class="user-menu" x-show="userMenuOpen" x-transition>
                <a href="{{ route('profile.edit') }}"><i class="fas fa-user-gear"></i> Profile Settings</a>
                <a href="{{ route('admin.settings.index') }}"><i class="fas fa-building"></i> Company Settings</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="danger"><i class="fas fa-arrow-right-from-bracket"></i> Logout</button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Topbar --}}
    <header class="topbar">
        <div class="topbar-left">
            <button class="topbar-btn mobile-toggle" @click="sidebarOpen = !sidebarOpen">
                <i class="fas fa-bars"></i>
            </button>
            <div>
                <div class="topbar-title">@yield('page-title', 'Dashboard')</div>
                @hasSection('page-subtitle')
                <div class="topbar-subtitle">@yield('page-subtitle')</div>
                @endif
            </div>
        </div>
        <div class="topbar-right">
            {{-- Theme toggle --}}
            <div class="theme-toggle" @click="darkMode = !darkMode" title="Toggle Dark/Light Mode">
                <div class="toggle-dot">
                    <span x-show="!darkMode">☀️</span>
                    <span x-show="darkMode">🌙</span>
                </div>
            </div>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="main-content">
        <div class="page-content page-shell">
            @yield('content')
        </div>
    </main>

    <nav class="mobile-dock" aria-label="Mobile Navigation">
        <div class="mobile-dock-track" data-mobile-dock-track>
            <div class="mobile-dock-slot">
                <a href="{{ route('admin.dashboard') }}" class="mobile-dock-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" title="Dashboard">
                    <i class="fas fa-house"></i>
                </a>
            </div>
            <div class="mobile-dock-slot">
                <a href="{{ route('admin.users.index') }}" class="mobile-dock-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" title="Users">
                    <i class="fas fa-users"></i>
                </a>
            </div>
            <div class="mobile-dock-slot">
                <a href="{{ route('admin.invitations.index') }}" class="mobile-dock-link {{ request()->routeIs('admin.invitations.*') ? 'active' : '' }}" title="Undangan">
                    <i class="fas fa-envelope"></i>
                </a>
            </div>
            <div class="mobile-dock-slot">
                <a href="{{ route('admin.templates.index') }}" class="mobile-dock-link {{ request()->routeIs('admin.templates.*') ? 'active' : '' }}" title="Template">
                    <i class="fas fa-palette"></i>
                </a>
            </div>
            <div class="mobile-dock-slot">
                <a href="{{ route('admin.packages.index') }}" class="mobile-dock-link {{ request()->routeIs('admin.packages.*') ? 'active' : '' }}" title="Paket">
                    <i class="fas fa-cube"></i>
                </a>
            </div>
            <div class="mobile-dock-slot">
                <a href="{{ route('admin.payments.index') }}" class="mobile-dock-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}" title="Pembayaran">
                    <i class="fas fa-credit-card"></i>
                </a>
            </div>
            <div class="mobile-dock-slot">
                <a href="{{ route('admin.affiliate.index') }}" class="mobile-dock-link {{ request()->routeIs('admin.affiliate.index') ? 'active' : '' }}" title="Affiliate">
                    <i class="fas fa-hand-holding-dollar"></i>
                </a>
            </div>
            <div class="mobile-dock-slot">
                <a href="{{ route('admin.affiliate.payouts') }}" class="mobile-dock-link {{ request()->routeIs('admin.affiliate.payouts*') ? 'active' : '' }}" title="Payout">
                    <i class="fas fa-wallet"></i>
                </a>
            </div>
            <div class="mobile-dock-slot">
                <a href="{{ route('admin.payment-gateway.index') }}" class="mobile-dock-link {{ request()->routeIs('admin.payment-gateway.*') ? 'active' : '' }}" title="Gateway">
                    <i class="fas fa-plug"></i>
                </a>
            </div>
            <div class="mobile-dock-slot">
                <a href="{{ route('admin.system.reliability') }}" class="mobile-dock-link {{ request()->routeIs('admin.system.reliability') ? 'active' : '' }}" title="Reliability">
                    <i class="fas fa-heart-pulse"></i>
                </a>
            </div>
            <div class="mobile-dock-slot">
                <a href="{{ route('admin.settings.index') }}" class="mobile-dock-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" title="Pengaturan">
                    <i class="fas fa-gear"></i>
                </a>
            </div>
        </div>
    </nav>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const track = document.querySelector('[data-mobile-dock-track]');
            if (!track) return;

            const key = 'admin_mobile_dock_scroll';
            const saved = localStorage.getItem(key);
            if (saved !== null) {
                track.scrollLeft = parseInt(saved, 10) || 0;
            } else {
                const active = track.querySelector('.mobile-dock-link.active');
                if (active) {
                    active.scrollIntoView({ behavior: 'auto', inline: 'center', block: 'nearest' });
                }
            }

            track.addEventListener('scroll', function () {
                localStorage.setItem(key, String(track.scrollLeft));
            }, { passive: true });
        });
    </script>
</body>
</html>
