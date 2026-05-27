<!DOCTYPE html>
<html lang="id" data-layout-ready="false"
    x-data="{
        darkMode: localStorage.getItem('darkMode') === 'true',
        sidebarOpen: false,
        sidebarExpanded: localStorage.getItem('clientSidebarExpanded') !== 'false',
        companyModalOpen: false,
        isMobile: window.innerWidth < 1024,
        hydrated: false,
        get sidebarWidth() { return this.sidebarExpanded ? '260px' : '72px'; },
        get mainMargin() { return this.isMobile ? '0px' : this.sidebarWidth; }
    }"
    x-init="
        $watch('darkMode', val => localStorage.setItem('darkMode', val));
        const syncLayoutVars = () => {
            document.documentElement.style.setProperty('--client-sidebar-width', sidebarExpanded ? '260px' : '72px');
            document.documentElement.style.setProperty('--client-main-offset', isMobile ? '0px' : (sidebarExpanded ? '260px' : '72px'));
        };
        $watch('sidebarExpanded', val => { localStorage.setItem('clientSidebarExpanded', val); syncLayoutVars(); });
        window.addEventListener('resize', () => { isMobile = window.innerWidth < 1024; syncLayoutVars(); });
        syncLayoutVars();
        requestAnimationFrame(() => {
            hydrated = true;
            document.documentElement.setAttribute('data-layout-ready', 'true');
        });
    "
    :class="{ 'dark': darkMode }">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') â€” Client</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@100..900&family=JetBrains+Mono:wght@100..800&display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        (() => {
            const doc = document.documentElement;
            const sidebarExpanded = localStorage.getItem('clientSidebarExpanded') !== 'false';
            const isMobile = window.innerWidth < 1024;
            doc.style.setProperty('--client-sidebar-width', sidebarExpanded ? '260px' : '72px');
            doc.style.setProperty('--client-main-offset', isMobile ? '0px' : (sidebarExpanded ? '260px' : '72px'));
        })();
        if (localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
    @stack('head')
    <style>
        [x-cloak] { display: none !important; }

        :root {
            --client-sidebar-width: 260px;
            --client-main-offset: 260px;
        }

        /* Geist Font */
        body {
            font-family: 'Geist', -apple-system, BlinkMacSystemFont, sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        /* Material Symbols */
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        /* Design tokens â€” same palette as landing page */
        :root {
            --surface: #f9f9f9;
            --surface-lowest: #ffffff;
            --surface-container: #eeeeee;
            --surface-container-low: #f3f3f3;
            --surface-container-high: #e8e8e8;
            --outline-variant: #cfc4c5;
            --on-surface: #1a1c1c;
            --on-surface-variant: #4c4546;
            --primary: #db2777; /* Pink accent */
            --on-primary: #ffffff;
            --secondary-container: #fce7f3; /* Pink-100 */
            --on-secondary-container: #be185d; /* Pink-700 */
            --error: #ba1a1a;
            --success: #15803d;
            --warning: #a16207;

            /* Legacy compat vars (used by child views) */
            --bg-secondary: #ffffff;
            --border: #eeeeee;
            --text: #1a1c1c;
            --text-secondary: #4c4546;
            --text-tertiary: #7e7576;
            --accent: #db2777;
            --accent-hover: #be185d;
            --accent-bg: rgba(219, 39, 119, 0.08);
            --danger: #ba1a1a;
            --success-clr: #15803d;
            --warning-clr: #a16207;
            --info: #db2777;
            --radius-sm: 8px;
            --hover-bg: rgba(219, 39, 119, 0.04);
        }

        .dark {
            --surface: #111318;
            --surface-lowest: #1a1c1c;
            --surface-container: #2a2a2a;
            --surface-container-low: #222222;
            --surface-container-high: #333333;
            --outline-variant: #3a3a3a;
            --on-surface: #e8e8e8;
            --on-surface-variant: #9e9e9e;
            --primary: #f472b6; /* Pink-400 */
            --on-primary: #1a1a1a;
            --secondary-container: rgba(244, 114, 182, 0.15); /* Pink tint */
            --on-secondary-container: #fbcfe8; /* Pink-200 */

            --bg-secondary: #1a1c1c;
            --border: #2a2a2a;
            --text: #e8e8e8;
            --text-secondary: #9e9e9e;
            --text-tertiary: #6e6e6e;
            --accent: #f472b6;
            --accent-hover: #f9a8d4;
            --accent-bg: rgba(244, 114, 182, 0.12);
            --hover-bg: rgba(255, 255, 255, 0.05);
        }

        /* Sidebar glass effect */
        .glass-sidebar {
            backdrop-filter: blur(48px);
            -webkit-backdrop-filter: blur(48px);
        }

        /* Scrollbar hide */
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

        /* Animations */
        .animate-slide-up { animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        /* Nav active state â€” landing page style */
        .nav-item-active {
            background: var(--secondary-container) !important;
            color: var(--on-secondary-container) !important;
            font-weight: 500;
        }

        /* Shared component styles (used by child views) */
        .card {
            background-color: var(--surface-lowest);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            border: 1px solid var(--outline-variant);
            padding: 1.5rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .dark .card {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .btn {
            display: inline-flex; align-items: center; gap: 6px; padding: 9px 16px;
            border-radius: var(--radius-sm); font-size: 13px; font-weight: 600;
            cursor: pointer; transition: all 0.15s ease; border: none; text-decoration: none;
            font-family: 'Geist', sans-serif;
        }
        .btn-primary { background: var(--accent); color: var(--on-primary); }
        .btn-primary:hover { background: var(--accent-hover); transform: translateY(-1px); }
        .btn-secondary { background: var(--hover-bg); color: var(--text); border: 1px solid var(--border); }
        .btn-secondary:hover { background: rgba(0,0,0,0.08); }
        .dark .btn-secondary:hover { background: rgba(255,255,255,0.08); }
        .btn-danger { background: rgba(186, 26, 26, 0.08); color: var(--danger); }
        .btn-danger:hover { background: rgba(186, 26, 26, 0.14); }

        .form-label { font-size: 13px; font-weight: 500; color: var(--text); margin-bottom: 6px; display: block; }
        .form-input {
            width: 100%; padding: 10px 14px; border: 1px solid var(--border);
            border-radius: var(--radius-sm); background: var(--bg-secondary);
            color: var(--text); font-size: 14px; transition: all 0.2s ease;
            font-family: 'Geist', sans-serif;
        }
        .form-input:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-bg); }

        .badge {
            display: inline-flex; align-items: center; padding: 2px 10px;
            border-radius: 9999px; font-size: 11px; font-weight: 600;
        }
        .badge-success, .badge-active { background: rgba(21, 128, 61, 0.10); color: var(--success-clr, #15803d); }
        .badge-warning, .badge-pending { background: rgba(161, 98, 7, 0.10); color: var(--warning-clr, #a16207); }
        .badge-danger { background: rgba(186, 26, 26, 0.10); color: var(--danger); }
        .badge-info { background: var(--accent-bg); color: var(--accent); }
        .badge-default, .badge-draft { background: var(--hover-bg); color: var(--text-secondary); }

        .stat-card { padding: 20px; text-align: left; }
        .stat-icon {
            width: 40px; height: 40px; border-radius: var(--radius-sm);
            display: flex; align-items: center; justify-content: center; font-size: 16px; margin-bottom: 12px;
        }
        .stat-value { font-size: 24px; font-weight: 700; color: var(--text); }
        .stat-label { font-size: 12px; color: var(--text-secondary); margin-top: 4px; }

        .table-container { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        thead th {
            font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;
            color: var(--on-surface-variant); padding: 10px 16px; text-align: left;
            border-bottom: 1px solid var(--outline-variant);
        }
        tbody td { padding: 12px 16px; font-size: 13px; border-bottom: 1px solid var(--outline-variant); color: var(--on-surface); }
        tbody tr { transition: background 0.1s ease; }
        tbody tr:hover { background: var(--hover-bg); }
        tbody tr:last-child td { border-bottom: none; }

        .mobile-shell {
            padding: 1.5rem;
            padding-bottom: 7.5rem;
            min-height: 100%;
        }

        .mobile-dock {
            position: relative;
            backdrop-filter: blur(28px);
            -webkit-backdrop-filter: blur(28px);
            box-shadow: 0 18px 50px rgba(15, 23, 42, 0.12);
            overflow: visible;
        }

        .mobile-bottom-nav {
            display: none;
        }

        .mobile-dock::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            background:
                radial-gradient(circle at 10% 0%, rgba(255,255,255,0.95) 0, rgba(255,255,255,0) 14%),
                radial-gradient(circle at 50% -26px, rgba(219, 39, 119, 0.08) 0, rgba(219, 39, 119, 0) 24%),
                linear-gradient(180deg, rgba(255,255,255,0.45), rgba(255,255,255,0.02));
            pointer-events: none;
        }

        .mobile-dock-item {
            position: relative;
            width: 3rem;
            height: 3rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 9999px;
            color: var(--on-surface-variant);
            transition: transform 0.28s ease, color 0.28s ease, background 0.28s ease, box-shadow 0.28s ease;
        }

        .mobile-dock-item.is-active {
            color: var(--accent);
            background: var(--secondary-container);
            box-shadow: none;
            transform: translateY(-0.95rem);
        }

        .mobile-dock-item.is-active::before {
            content: '';
            position: absolute;
            top: -0.55rem;
            left: 50%;
            width: 5.5rem;
            height: 2.4rem;
            transform: translateX(-50%);
            border-radius: 9999px;
            background: transparent;
            z-index: -1;
        }

        .mobile-dock-rail {
            position: absolute;
            top: 0.7rem;
            height: 1.25rem;
            border-radius: 9999px 9999px 0 0;
            background: linear-gradient(180deg, rgba(219, 39, 119, 0.10), rgba(219, 39, 119, 0.02));
            filter: blur(0.2px);
            transition: left 0.25s ease;
            pointer-events: none;
        }

        @media (max-width: 1023px) {
            .mobile-bottom-nav {
                display: block;
                position: fixed;
                left: 0.75rem;
                right: 0.75rem;
                bottom: 0.75rem;
                z-index: 10000;
            }

            .card {
                border-radius: 20px;
                padding: 1rem;
                box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
            }

            .btn {
                min-height: 2.75rem;
                justify-content: center;
            }

            .table-container {
                margin-inline: -0.15rem;
            }

            table thead th,
            table tbody td {
                padding-left: 0.85rem;
                padding-right: 0.85rem;
            }
        }

        @media (max-width: 767px) {
            .mobile-shell {
                padding: 1rem;
                padding-bottom: 8rem;
            }

            .mobile-dock {
                border-radius: 1.85rem;
            }

            .card,
            .elegant-card {
                border-radius: 22px !important;
            }

            .elegant-card {
                padding: 1.1rem !important;
                box-shadow: 0 12px 30px rgba(15, 23, 42, 0.05);
            }

            .elegant-card:hover {
                transform: none;
            }
        }
    </style>
</head>

<body class="text-[--on-surface] font-sans antialiased overflow-hidden transition-colors duration-300"
      style="background-color: var(--surface);">

    @php
        $activeClientSubscription = auth()->user()
            ?->packageSubscriptions()
            ->with('package')
            ->where('status', 'active')
            ->latest('id')
            ->first();
        $clientPackageName = $activeClientSubscription?->package?->name ?? 'Free';
    @endphp

    <!-- Toasts -->
    @if (session('success'))
        <div data-toast class="fixed top-6 right-6 z-[9999] border px-5 py-3 rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] flex items-center gap-3 animate-slide-up transition-all duration-500"
             style="background: var(--surface-lowest); border-color: rgba(21,128,61,0.2); color: #15803d;">
            <span class="material-symbols-outlined" style="font-size:18px;">check_circle</span>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif
    @if (session('error'))
        <div data-toast class="fixed top-6 right-6 z-[9999] border px-5 py-3 rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] flex items-center gap-3 animate-slide-up transition-all duration-500"
             style="background: var(--surface-lowest); border-color: rgba(186,26,26,0.2); color: #ba1a1a;">
            <span class="material-symbols-outlined" style="font-size:18px;">error</span>
            <span class="text-sm font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Mobile Sidebar Overlay -->
    <div class="fixed inset-0 bg-black/30 backdrop-blur-sm z-40 lg:hidden transition-opacity duration-300"
         :class="sidebarOpen ? 'opacity-100 visible' : 'opacity-0 invisible'" @click="sidebarOpen = false"></div>

    <!-- Sidebar -->
    <aside
        class="fixed top-0 left-0 h-full z-50 flex flex-col border-r glass-sidebar overflow-hidden"
        style="border-right-width: 1px; width: var(--client-sidebar-width); transition: none;"
        :style="{
            width: sidebarExpanded ? '260px' : '72px',
            transform: isMobile ? (sidebarOpen ? 'translateX(0)' : 'translateX(-260px)') : 'translateX(0)',
            background: darkMode ? 'rgba(17,19,24,0.96)' : 'rgba(249,249,249,0.92)',
            borderColor: 'var(--outline-variant)',
            transition: hydrated ? 'width 0.3s cubic-bezier(0.4,0,0.2,1), transform 0.3s cubic-bezier(0.4,0,0.2,1), background 0.3s ease' : 'none'
        }">

        <!-- Brand -->
        <a href="{{ route('client.dashboard') }}" class="px-6 py-5 flex items-center gap-3 shrink-0 border-b cursor-pointer hover:bg-black/5 dark:hover:bg-white/5 transition-colors" style="border-color: var(--outline-variant); min-height: 72px;">
            <div class="w-8 h-8 flex items-center justify-center shrink-0 font-bold text-sm overflow-visible"
                 style="{{ $brandLogoUrl ? 'background: transparent; color: var(--on-surface);' : 'background: var(--primary); color: var(--on-primary); border-radius: 0.75rem;' }}">
                @if($brandLogoUrl)
                    <img src="{{ $brandLogoUrl }}" alt="Logo" class="max-w-full max-h-full object-contain">
                @else
                    {{ substr($brandName ?? auth()->user()->name, 0, 1) }}
                @endif
            </div>
            <div class="whitespace-nowrap overflow-hidden"
                 style="transition: opacity 0.25s ease, width 0.3s cubic-bezier(0.4,0,0.2,1);"
                 :style="{ opacity: (!isMobile && !sidebarExpanded) ? '0' : '1', width: (!isMobile && !sidebarExpanded) ? '0' : 'auto' }">
                <div class="text-[15px] font-semibold leading-none" style="color: var(--on-surface); letter-spacing: -0.01em;">{{ $brandName ?? 'Janji Suci Kita' }}</div>
                <div class="mt-1 inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.12em]"
                     style="background: var(--secondary-container); color: var(--on-secondary-container);">
                    <span class="material-symbols-outlined" style="font-size: 13px;">workspace_premium</span>
                    <span>{{ $clientPackageName }}</span>
                </div>
            </div>
        </a>

        <!-- Navigation Links -->
        <nav class="flex-1 overflow-y-auto scrollbar-hide px-3 py-4 flex flex-col gap-5">

            <!-- Menu Utama -->
            <div class="flex flex-col gap-0.5">
                <div class="text-[10px] font-semibold px-3 mb-1.5 uppercase tracking-widest"
                     style="color: var(--on-surface-variant); transition: opacity 0.25s ease;"
                     :style="{ opacity: (!isMobile && !sidebarExpanded) ? '0' : '0.5' }">Menu</div>

                <a href="{{ route('client.dashboard') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('client.dashboard') ? 'nav-item-active' : '' }}"
                    style="{{ !request()->routeIs('client.dashboard') ? 'color: var(--on-surface-variant);' : '' }}"
                    onmouseover="{{ !request()->routeIs('client.dashboard') ? 'this.style.background=\"var(--surface-container)\"' : '' }}"
                    onmouseout="{{ !request()->routeIs('client.dashboard') ? 'this.style.background=\"\"' : '' }}"
                    title="Dashboard">
                    <span class="material-symbols-outlined shrink-0" style="font-size: 20px;">dashboard</span>
                    <span class="text-sm whitespace-nowrap overflow-hidden"
                          style="transition: opacity 0.2s ease, max-width 0.3s cubic-bezier(0.4,0,0.2,1);"
                          :style="{ opacity: (!isMobile && !sidebarExpanded) ? '0' : '1', maxWidth: (!isMobile && !sidebarExpanded) ? '0' : '200px' }">Dashboard</span>
                </a>

                <a href="{{ route('client.balance.index') }}"
                    class="flex items-center justify-between px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('client.balance.*') ? 'nav-item-active' : '' }}"
                    style="{{ !request()->routeIs('client.balance.*') ? 'color: var(--on-surface-variant);' : '' }}"
                    onmouseover="{{ !request()->routeIs('client.balance.*') ? 'this.style.background=\"var(--surface-container)\"' : '' }}"
                    onmouseout="{{ !request()->routeIs('client.balance.*') ? 'this.style.background=\"\"' : '' }}"
                    title="Dompet & Saldo">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined shrink-0" style="font-size: 20px;">account_balance_wallet</span>
                        <span class="text-sm whitespace-nowrap overflow-hidden"
                              style="transition: opacity 0.2s ease, max-width 0.3s cubic-bezier(0.4,0,0.2,1);"
                              :style="{ opacity: (!isMobile && !sidebarExpanded) ? '0' : '1', maxWidth: (!isMobile && !sidebarExpanded) ? '0' : '200px' }">Dompet & Saldo</span>
                    </div>
                    <span x-show="!isMobile && sidebarExpanded" class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-pink-100 dark:bg-pink-900/30 text-[var(--accent)] whitespace-nowrap">
                        Rp {{ number_format(auth()->user()->balance, 0, ',', '.') }}
                    </span>
                </a>
            </div>

            <!-- Undangan -->
            <div class="flex flex-col gap-0.5">
                <div class="text-[10px] font-semibold px-3 mb-1.5 uppercase tracking-widest"
                     style="color: var(--on-surface-variant); transition: opacity 0.25s ease;"
                     :style="{ opacity: (!isMobile && !sidebarExpanded) ? '0' : '0.5' }">Undangan</div>

                <a href="{{ route('client.invitations.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('client.invitations.index') ? 'nav-item-active' : '' }}"
                    style="{{ !request()->routeIs('client.invitations.index') ? 'color: var(--on-surface-variant);' : '' }}"
                    onmouseover="{{ !request()->routeIs('client.invitations.index') ? 'this.style.background=\"var(--surface-container)\"' : '' }}"
                    onmouseout="{{ !request()->routeIs('client.invitations.index') ? 'this.style.background=\"\"' : '' }}"
                    title="Undangan Saya">
                    <span class="material-symbols-outlined shrink-0" style="font-size: 20px;">mail</span>
                    <span class="text-sm whitespace-nowrap overflow-hidden"
                          style="transition: opacity 0.2s ease, max-width 0.3s cubic-bezier(0.4,0,0.2,1);"
                          :style="{ opacity: (!isMobile && !sidebarExpanded) ? '0' : '1', maxWidth: (!isMobile && !sidebarExpanded) ? '0' : '200px' }">Undangan Saya</span>
                </a>

                <a href="{{ route('client.invitations.create') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('client.invitations.create') ? 'nav-item-active' : '' }}"
                    style="{{ !request()->routeIs('client.invitations.create') ? 'color: var(--on-surface-variant);' : '' }}"
                    onmouseover="{{ !request()->routeIs('client.invitations.create') ? 'this.style.background=\"var(--surface-container)\"' : '' }}"
                    onmouseout="{{ !request()->routeIs('client.invitations.create') ? 'this.style.background=\"\"' : '' }}"
                    title="Buat Undangan">
                    <span class="material-symbols-outlined shrink-0" style="font-size: 20px;">add_circle</span>
                    <span class="text-sm whitespace-nowrap overflow-hidden"
                          style="transition: opacity 0.2s ease, max-width 0.3s cubic-bezier(0.4,0,0.2,1);"
                          :style="{ opacity: (!isMobile && !sidebarExpanded) ? '0' : '1', maxWidth: (!isMobile && !sidebarExpanded) ? '0' : '200px' }">Buat Undangan</span>
                </a>

                <a href="{{ route('client.templates.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('client.templates.*') ? 'nav-item-active' : '' }}"
                    style="{{ !request()->routeIs('client.templates.*') ? 'color: var(--on-surface-variant);' : '' }}"
                    onmouseover="{{ !request()->routeIs('client.templates.*') ? 'this.style.background=\"var(--surface-container)\"' : '' }}"
                    onmouseout="{{ !request()->routeIs('client.templates.*') ? 'this.style.background=\"\"' : '' }}"
                    title="Katalog Template">
                    <span class="material-symbols-outlined shrink-0" style="font-size: 20px;">layers</span>
                    <span class="text-sm whitespace-nowrap overflow-hidden"
                          style="transition: opacity 0.2s ease, max-width 0.3s cubic-bezier(0.4,0,0.2,1);"
                          :style="{ opacity: (!isMobile && !sidebarExpanded) ? '0' : '1', maxWidth: (!isMobile && !sidebarExpanded) ? '0' : '200px' }">Katalog Template</span>
                </a>

                <a href="{{ route('client.packages.select') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('client.packages.*') ? 'nav-item-active' : '' }}"
                    style="{{ !request()->routeIs('client.packages.*') ? 'color: var(--on-surface-variant);' : '' }}"
                    onmouseover="{{ !request()->routeIs('client.packages.*') ? 'this.style.background=\"var(--surface-container)\"' : '' }}"
                    onmouseout="{{ !request()->routeIs('client.packages.*') ? 'this.style.background=\"\"' : '' }}"
                    title="Pilih Paket">
                    <span class="material-symbols-outlined shrink-0" style="font-size: 20px;">inventory_2</span>
                    <span class="text-sm whitespace-nowrap overflow-hidden"
                          style="transition: opacity 0.2s ease, max-width 0.3s cubic-bezier(0.4,0,0.2,1);"
                          :style="{ opacity: (!isMobile && !sidebarExpanded) ? '0' : '1', maxWidth: (!isMobile && !sidebarExpanded) ? '0' : '200px' }">Pilih Paket</span>
                </a>

                <a href="{{ route('client.affiliate.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('client.affiliate.*') ? 'nav-item-active' : '' }}"
                    style="{{ !request()->routeIs('client.affiliate.*') ? 'color: var(--on-surface-variant);' : '' }}"
                    onmouseover="{{ !request()->routeIs('client.affiliate.*') ? 'this.style.background=\"var(--surface-container)\"' : '' }}"
                    onmouseout="{{ !request()->routeIs('client.affiliate.*') ? 'this.style.background=\"\"' : '' }}"
                    title="Affiliate">
                    <span class="material-symbols-outlined shrink-0" style="font-size: 20px;">hub</span>
                    <span class="text-sm whitespace-nowrap overflow-hidden"
                          style="transition: opacity 0.2s ease, max-width 0.3s cubic-bezier(0.4,0,0.2,1);"
                          :style="{ opacity: (!isMobile && !sidebarExpanded) ? '0' : '1', maxWidth: (!isMobile && !sidebarExpanded) ? '0' : '200px' }">Affiliate</span>
                </a>
            </div>

            <!-- Wedding Planner -->
            <div class="flex flex-col gap-0.5">
                <div class="text-[10px] font-semibold px-3 mb-1.5 uppercase tracking-widest"
                     style="color: var(--on-surface-variant); transition: opacity 0.25s ease;"
                     :style="{ opacity: (!isMobile && !sidebarExpanded) ? '0' : '0.5' }">Planner</div>

                <a href="{{ route('client.planner.dashboard') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('client.planner.dashboard') ? 'nav-item-active' : '' }}"
                    style="{{ !request()->routeIs('client.planner.dashboard') ? 'color: var(--on-surface-variant);' : '' }}"
                    onmouseover="{{ !request()->routeIs('client.planner.dashboard') ? 'this.style.background=\"var(--surface-container)\"' : '' }}"
                    onmouseout="{{ !request()->routeIs('client.planner.dashboard') ? 'this.style.background=\"\"' : '' }}"
                    title="Planner">
                    <span class="material-symbols-outlined shrink-0" style="font-size: 20px;">favorite</span>
                    <span class="text-sm whitespace-nowrap overflow-hidden"
                          style="transition: opacity 0.2s ease, max-width 0.3s cubic-bezier(0.4,0,0.2,1);"
                          :style="{ opacity: (!isMobile && !sidebarExpanded) ? '0' : '1', maxWidth: (!isMobile && !sidebarExpanded) ? '0' : '200px' }">Planner</span>
                </a>

                <a href="{{ route('client.planner.checklist.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('client.planner.checklist.*') ? 'nav-item-active' : '' }}"
                    style="{{ !request()->routeIs('client.planner.checklist.*') ? 'color: var(--on-surface-variant);' : '' }}"
                    onmouseover="{{ !request()->routeIs('client.planner.checklist.*') ? 'this.style.background=\"var(--surface-container)\"' : '' }}"
                    onmouseout="{{ !request()->routeIs('client.planner.checklist.*') ? 'this.style.background=\"\"' : '' }}"
                    title="Checklist">
                    <span class="material-symbols-outlined shrink-0" style="font-size: 20px;">checklist</span>
                    <span class="text-sm whitespace-nowrap overflow-hidden"
                          style="transition: opacity 0.2s ease, max-width 0.3s cubic-bezier(0.4,0,0.2,1);"
                          :style="{ opacity: (!isMobile && !sidebarExpanded) ? '0' : '1', maxWidth: (!isMobile && !sidebarExpanded) ? '0' : '200px' }">Checklist</span>
                </a>

                <a href="{{ route('client.planner.budget.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('client.planner.budget.*') ? 'nav-item-active' : '' }}"
                    style="{{ !request()->routeIs('client.planner.budget.*') ? 'color: var(--on-surface-variant);' : '' }}"
                    onmouseover="{{ !request()->routeIs('client.planner.budget.*') ? 'this.style.background=\"var(--surface-container)\"' : '' }}"
                    onmouseout="{{ !request()->routeIs('client.planner.budget.*') ? 'this.style.background=\"\"' : '' }}"
                    title="Budget">
                    <span class="material-symbols-outlined shrink-0" style="font-size: 20px;">account_balance_wallet</span>
                    <span class="text-sm whitespace-nowrap overflow-hidden"
                          style="transition: opacity 0.2s ease, max-width 0.3s cubic-bezier(0.4,0,0.2,1);"
                          :style="{ opacity: (!isMobile && !sidebarExpanded) ? '0' : '1', maxWidth: (!isMobile && !sidebarExpanded) ? '0' : '200px' }">Budget</span>
                </a>

                <a href="{{ route('client.planner.vendors.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('client.planner.vendors.*') ? 'nav-item-active' : '' }}"
                    style="{{ !request()->routeIs('client.planner.vendors.*') ? 'color: var(--on-surface-variant);' : '' }}"
                    onmouseover="{{ !request()->routeIs('client.planner.vendors.*') ? 'this.style.background=\"var(--surface-container)\"' : '' }}"
                    onmouseout="{{ !request()->routeIs('client.planner.vendors.*') ? 'this.style.background=\"\"' : '' }}"
                    title="Vendor">
                    <span class="material-symbols-outlined shrink-0" style="font-size: 20px;">storefront</span>
                    <span class="text-sm whitespace-nowrap overflow-hidden"
                          style="transition: opacity 0.2s ease, max-width 0.3s cubic-bezier(0.4,0,0.2,1);"
                          :style="{ opacity: (!isMobile && !sidebarExpanded) ? '0' : '1', maxWidth: (!isMobile && !sidebarExpanded) ? '0' : '200px' }">Vendor</span>
                </a>

                <a href="{{ route('client.planner.advisor.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('client.planner.advisor.*') ? 'nav-item-active' : '' }}"
                    style="{{ !request()->routeIs('client.planner.advisor.*') ? 'color: var(--on-surface-variant);' : '' }}"
                    onmouseover="{{ !request()->routeIs('client.planner.advisor.*') ? 'this.style.background=\"var(--surface-container)\"' : '' }}"
                    onmouseout="{{ !request()->routeIs('client.planner.advisor.*') ? 'this.style.background=\"\"' : '' }}"
                    title="AI Advisor">
                    <span class="material-symbols-outlined shrink-0" style="font-size: 20px;">smart_toy</span>
                    <span class="text-sm whitespace-nowrap overflow-hidden"
                          style="transition: opacity 0.2s ease, max-width 0.3s cubic-bezier(0.4,0,0.2,1);"
                          :style="{ opacity: (!isMobile && !sidebarExpanded) ? '0' : '1', maxWidth: (!isMobile && !sidebarExpanded) ? '0' : '200px' }">AI Advisor</span>
                </a>
            </div>
        </nav>

        <!-- Footer / Profile -->
        <div class="px-3 py-4 shrink-0 border-t" style="border-color: var(--outline-variant);">
            <a href="{{ route('profile.edit') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 w-full {{ request()->routeIs('profile.*') ? 'nav-item-active' : '' }}"
                style="{{ !request()->routeIs('profile.*') ? 'color: var(--on-surface-variant);' : '' }}"
                onmouseover="{{ !request()->routeIs('profile.*') ? 'this.style.background=\"var(--surface-container)\"' : '' }}"
                onmouseout="{{ !request()->routeIs('profile.*') ? 'this.style.background=\"\"' : '' }}"
                title="Pengaturan Profil">
                <span class="material-symbols-outlined shrink-0" style="font-size: 20px;">manage_accounts</span>
                <span class="text-sm whitespace-nowrap overflow-hidden"
                      style="transition: opacity 0.2s ease, max-width 0.3s cubic-bezier(0.4,0,0.2,1);"
                      :style="{ opacity: (!isMobile && !sidebarExpanded) ? '0' : '1', maxWidth: (!isMobile && !sidebarExpanded) ? '0' : '200px' }">Pengaturan</span>
            </a>
        </div>
    </aside>

    <!-- App Wrapper -->
    <div
        class="flex flex-col h-screen overflow-hidden"
        style="margin-left: var(--client-main-offset); transition: none;"
        :style="{ marginLeft: isMobile ? '0px' : (sidebarExpanded ? '260px' : '72px'), transition: hydrated ? 'margin-left 0.3s cubic-bezier(0.4,0,0.2,1)' : 'none' }">

        <!-- Topbar -->
        <header class="sticky top-0 z-40 flex items-center justify-between px-4 sm:px-5 shrink-0 border-b glass-sidebar"
                style="height: 64px; transition: background 0.3s ease, border-color 0.3s ease;"
                :style="{
                    background: darkMode ? 'rgba(17,19,24,0.92)' : 'rgba(249,249,249,0.88)',
                    borderColor: 'var(--outline-variant)'
                }">
            <div class="flex items-center gap-3">
                <!-- Toggle Sidebar — minimalist floating icon -->
                <button @click="isMobile ? (sidebarOpen = !sidebarOpen) : (sidebarExpanded = !sidebarExpanded)"
                    class="flex items-center justify-center w-8 h-8 rounded-full shrink-0 transition-all duration-200 focus:outline-none"
                    style="color: var(--on-surface-variant);"
                    :style="{ background: 'transparent' }"
                    @mouseenter="$el.style.background = darkMode ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.05)'; $el.style.color = 'var(--on-surface)'"
                    @mouseleave="$el.style.background = 'transparent'; $el.style.color = 'var(--on-surface-variant)'"
                    title="Toggle Sidebar">
                    <span class="material-symbols-outlined" style="font-size: 22px; transition: transform 0.3s cubic-bezier(0.4,0,0.2,1);"
                          :style="{ transform: (!isMobile && sidebarExpanded) ? 'rotate(0deg)' : 'rotate(180deg)' }"
                          x-text="isMobile ? 'menu' : 'chevron_left'"></span>
                </button>

                <!-- Page Title -->
                <div class="flex flex-col justify-center min-w-0">
                    <h1 class="text-[14px] sm:text-[15px] font-semibold leading-tight truncate" style="color: var(--on-surface); letter-spacing: -0.01em;">@yield('page-title', 'Dashboard')</h1>
                    @hasSection('page-subtitle')
                        <p class="text-[11px] mt-0.5 truncate max-w-[48vw] sm:max-w-none" style="color: var(--on-surface-variant);">@yield('page-subtitle')</p>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0" x-data="{ userMenuOpen: false }">
                <!-- Theme Toggle -->
                <button @click="darkMode = !darkMode"
                    class="w-9 h-9 rounded-lg flex items-center justify-center transition-all focus:outline-none"
                    style="color: var(--on-surface-variant); background: var(--surface-container);"
                    onmouseover="this.style.background='var(--surface-container-high)'"
                    onmouseout="this.style.background='var(--surface-container)'">
                    <span class="material-symbols-outlined" style="font-size: 20px;" :class="darkMode ? 'text-amber-500' : ''"
                          x-text="darkMode ? 'light_mode' : 'dark_mode'"></span>
                </button>

                <!-- Divider -->
                <div class="hidden sm:block w-px h-5 mx-1" style="background: var(--outline-variant);"></div>

                <!-- User Dropdown -->
                <div class="relative">
                    <button @click="userMenuOpen = !userMenuOpen"
                        class="flex items-center gap-2 px-2 sm:px-3 py-1.5 rounded-full transition-all focus:outline-none"
                        style="background: var(--surface-container); color: var(--on-surface);"
                        onmouseover="this.style.background='var(--surface-container-high)'"
                        onmouseout="this.style.background='var(--surface-container)'">
                        @if(auth()->user()->avatar)
                            <div class="w-6 h-6 rounded-full overflow-hidden shrink-0">
                                <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                            </div>
                        @else
                            <div class="w-6 h-6 rounded-full flex items-center justify-center font-semibold text-[11px] shrink-0"
                                 style="background: var(--primary); color: var(--on-primary);">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                        @endif
                        <span class="text-[13px] font-medium hidden sm:block" style="color: var(--on-surface);">{{ auth()->user()->name }}</span>
                        <span class="material-symbols-outlined" style="font-size: 14px; color: var(--on-surface-variant);">expand_more</span>
                    </button>

                    <div x-show="userMenuOpen" @click.outside="userMenuOpen = false"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                        class="absolute right-0 mt-2 w-52 rounded-xl overflow-hidden shadow-[0_8px_32px_rgba(0,0,0,0.08)] z-50 border"
                        style="background: var(--surface-lowest); border-color: var(--outline-variant);"
                        x-cloak>
                        <div class="px-4 py-3 border-b" style="background: var(--surface-container-low); border-color: var(--outline-variant);">
                            <p class="text-[13px] font-semibold truncate" style="color: var(--on-surface);">{{ auth()->user()->name }}</p>
                            <p class="text-[11px] truncate mt-0.5" style="color: var(--on-surface-variant);">{{ auth()->user()->email }}</p>
                        </div>
                        <a href="{{ route('profile.edit') }}"
                           class="flex items-center gap-3 px-4 py-2.5 text-sm transition-colors"
                           style="color: var(--on-surface-variant);"
                           onmouseover="this.style.background='var(--surface-container)'; this.style.color='var(--on-surface)'"
                           onmouseout="this.style.background=''; this.style.color='var(--on-surface-variant)'">
                            <span class="material-symbols-outlined" style="font-size: 17px;">manage_accounts</span> Setelan Profil
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="block w-full m-0">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center gap-3 px-4 py-2.5 text-sm transition-colors text-left"
                                style="color: #ba1a1a;"
                                onmouseover="this.style.background='rgba(186,26,26,0.06)'"
                                onmouseout="this.style.background=''">
                                <span class="material-symbols-outlined" style="font-size: 17px;">logout</span> Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="flex-1 overflow-y-auto scrollbar-hide transition-colors duration-300" style="background: var(--surface);">
            <div class="mobile-shell lg:p-6 lg:pb-10">
                @yield('content')
            </div>
        </main>
    </div>

    @php
        $mobileDockItems = [
            [
                'label' => 'Dashboard',
                'route' => route('client.dashboard'),
                'active' => request()->routeIs('client.dashboard'),
                'icon' => 'home',
            ],
            [
                'label' => 'Undangan',
                'route' => route('client.invitations.index'),
                'active' => request()->routeIs('client.invitations.*'),
                'icon' => 'search',
            ],
            [
                'label' => 'Saldo',
                'route' => route('client.balance.index'),
                'active' => request()->routeIs('client.balance.*'),
                'icon' => 'account_balance_wallet',
            ],
            [
                'label' => 'Affiliate',
                'route' => route('client.affiliate.index'),
                'active' => request()->routeIs('client.affiliate.*'),
                'icon' => 'schedule',
            ],
            [
                'label' => 'Profil',
                'route' => route('profile.edit'),
                'active' => request()->routeIs('profile.*'),
                'icon' => 'person',
            ],
        ];
    @endphp

    <nav class="mobile-bottom-nav rounded-[2rem] px-3 pt-3 pb-[max(0.6rem,env(safe-area-inset-bottom))] border mobile-dock"
         :style="{ background: darkMode ? 'rgba(17,19,24,0.92)' : 'rgba(255,255,255,0.88)', borderColor: darkMode ? 'rgba(255,255,255,0.06)' : 'rgba(15,23,42,0.06)' }">
        <div class="relative flex items-end justify-between gap-1">
            @php
                $activeIndex = collect($mobileDockItems)->search(fn ($item) => $item['active']);
                $activeIndex = $activeIndex === false ? 0 : $activeIndex;
                $railWidth = '20%';
                $railLeft = 'calc(' . $activeIndex . ' * 20% + 6%)';
            @endphp
            <div class="mobile-dock-rail" style="left: {{ $railLeft }}; width: calc({{ $railWidth }} - 12%);"></div>
            @foreach($mobileDockItems as $item)
                <a href="{{ $item['route'] }}"
                    class="mobile-dock-item {{ $item['active'] ? 'is-active' : '' }}"
                    aria-label="{{ $item['label'] }}"
                    title="{{ $item['label'] }}">
                    <span class="material-symbols-outlined" style="font-size: 22px;">{{ $item['icon'] }}</span>
                </a>
            @endforeach
        </div>
        <div class="mt-2 flex justify-center">
            <div class="h-1.5 w-24 rounded-full" style="background: rgba(148, 163, 184, 0.55);"></div>
        </div>
    </nav>
    @stack('scripts')

    {{-- Floating Donation Ad Card (Only for Free Users) --}}
    @php
        $hasPaidSubscription = auth()->check() && (
            auth()->user()->isAdmin() ||
            auth()->user()->packageSubscriptions()->where('status', 'active')->whereHas('package', function($q) {
                $q->where('price', '>', 0);
            })->exists()
        );
    @endphp

    @if(!$hasPaidSubscription)
    <div x-data="{
            showAd: false,
            init() {
                const hideUntil = localStorage.getItem('hideSaweriaAdUntil');
                if (!hideUntil || Date.now() > parseInt(hideUntil)) { this.showAd = true; }
            },
            closeAd() {
                this.showAd = false;
                localStorage.setItem('hideSaweriaAdUntil', Date.now() + (10 * 60 * 1000));
            }
        }" x-show="showAd" x-cloak x-transition:enter="transition ease-out duration-500"
        x-transition:enter-start="opacity-0 translate-y-10" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-10"
        class="hidden lg:block fixed bottom-6 right-6 z-[9999] rounded-2xl overflow-hidden shadow-2xl transition-all duration-300 border border-amber-400/30"
        :style="sidebarExpanded ? 'width: 212px;' : 'width: 56px; height: 56px; border-radius: 16px;'">

        <button x-show="sidebarExpanded" @click="closeAd()"
            class="absolute top-2 right-2 w-5 h-5 flex items-center justify-center rounded-full bg-black/20 text-gray-400 hover:text-white hover:bg-black/40 transition-colors z-10"
            title="Tutup iklan">
            <i class="fas fa-times text-[10px]"></i>
        </button>

        <div x-show="sidebarExpanded" class="p-4 pt-5 text-center bg-gradient-to-b from-slate-800 to-slate-900 border-t border-slate-700">
            <div class="w-10 h-10 mx-auto bg-amber-400 text-slate-900 rounded-full flex items-center justify-center text-lg shadow-[0_0_15px_rgba(251,191,36,0.25)] mb-3 relative animate-bounce">
                <i class="fas fa-coffee"></i>
            </div>
            <div class="text-amber-400 text-[11px] font-bold mb-1 uppercase tracking-wider">Dukung Kami</div>
            <div class="text-gray-400 text-[10px] leading-relaxed mb-3">Traktir kopi agar update makin ngebut! â˜•</div>
            <a href="https://saweria.co/gunaepi" target="_blank"
                class="block w-full py-1.5 bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 text-slate-900 text-[11px] font-bold rounded-lg transition-all shadow-md hover:-translate-y-0.5">
                Donasi
            </a>
        </div>

        <div x-show="!sidebarExpanded" class="w-full h-full bg-gradient-to-br from-amber-400 to-amber-500 flex items-center justify-center relative cursor-pointer hover:scale-105 transition-transform" @click="window.open('https://saweria.co/gunaepi')">
            <i class="fas fa-coffee text-slate-900 text-xl shadow-[0_0_15px_rgba(251,191,36,0.3)]"></i>
            <button @click.prevent.stop="closeAd()"
                class="absolute -top-1.5 -right-1.5 w-4 h-4 flex items-center justify-center rounded-full bg-red-500 text-white hover:bg-red-600 transition-colors shadow-sm">
                <i class="fas fa-times text-[8px]"></i>
            </button>
        </div>
    </div>
    @endif

    <!-- Company Info Modal -->
    <div x-show="companyModalOpen" x-cloak class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
        <!-- Backdrop -->
        <div x-show="companyModalOpen" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="absolute inset-0 bg-black/40 backdrop-blur-md"
             @click="companyModalOpen = false"></div>

        <!-- Modal Card -->
        <div x-show="companyModalOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-8 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-8 scale-95"
             class="relative w-full shadow-2xl overflow-hidden border border-gray-100 dark:border-white/5"
             style="max-width: 420px; border-radius: 24px; background: var(--surface-lowest);"
             @click.stop>
            
            <!-- Close Button -->
            <button @click="companyModalOpen = false" style="position: absolute; top: 16px; right: 16px; z-index: 20; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 50%; background: rgba(255,255,255,0.8); backdrop-filter: blur(8px); color: #333; cursor: pointer; border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <i class="fas fa-times" style="font-size: 14px;"></i>
            </button>

            <!-- Top Image/Logo Section -->
            <div style="position: relative; width: 100%; height: 200px; background: linear-gradient(135deg, #fdf2f8, #fbcfe8); display: flex; align-items: center; justify-content: center; overflow: hidden;">
                @if($brandLogoUrl)
                    <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; padding: 20px;">
                        <img src="{{ $brandLogoUrl }}" alt="Company Cover" style="max-width: 100%; max-height: 100%; object-fit: contain; filter: drop-shadow(0 4px 12px rgba(0,0,0,0.08));">
                    </div>
                @else
                    <i class="fas fa-building" style="font-size: 72px; color: rgba(219, 39, 119, 0.3); position: absolute;"></i>
                @endif
                
                <!-- Badges -->
                <div style="position: absolute; top: 16px; left: 16px; display: flex; flex-wrap: wrap; gap: 8px; z-index: 10;">
                    <span style="padding: 4px 10px; font-size: 10px; font-weight: 600; color: white; background: rgba(0,0,0,0.5); backdrop-filter: blur(8px); border-radius: 8px; border: 1px solid rgba(255,255,255,0.2); display: flex; align-items: center; gap: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        <i class="fas fa-star text-yellow-400"></i> Client
                    </span>
                    <span style="padding: 4px 10px; font-size: 10px; font-weight: 600; color: white; background: rgba(0,0,0,0.5); backdrop-filter: blur(8px); border-radius: 8px; border: 1px solid rgba(255,255,255,0.2); display: flex; align-items: center; gap: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        <i class="fas fa-check-circle text-green-400"></i> Active
                    </span>
                </div>
            </div>

            <!-- Content Section -->
            <div style="padding: 24px 32px 32px 32px; position: relative; z-index: 20; background: var(--surface-lowest);">
                <h2 style="font-size: 24px; font-weight: 700; color: var(--text); margin-top: 0; margin-bottom: 16px; font-family: 'Geist', sans-serif; letter-spacing: -0.02em;">
                    {{ $brandName ?? 'My Company' }}
                </h2>
                
                <div style="font-size: 14px; color: var(--text-secondary); line-height: 1.6; margin-bottom: 24px;">
                    <p style="margin-top: 0; margin-bottom: 8px;"><strong style="color: var(--text); font-weight: 600;">Pemilik:</strong> {{ auth()->user()->name }}</p>
                    <p style="margin-top: 0; margin-bottom: 8px;"><strong style="color: var(--text); font-weight: 600;">Email:</strong> {{ auth()->user()->email }}</p>
                    <p style="margin-top: 0; margin-bottom: 0;"><strong style="color: var(--text); font-weight: 600;">Bergabung:</strong> {{ auth()->user()->created_at->format('d M Y') }}</p>
                </div>

                <!-- Action Buttons -->
                <div style="margin-top: 8px;">
                    <a href="{{ route('profile.edit') }}" 
                       style="display: flex; align-items: center; justify-content: center; width: 100%; padding: 12px 24px; background: #1a1a1a; color: white; border-radius: 9999px; font-size: 14px; font-weight: 600; text-decoration: none; transition: opacity 0.2s; box-shadow: 0 4px 12px rgba(0,0,0,0.1);"
                       onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                        Pengaturan Perusahaan
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('[data-toast]').forEach((toast) => {
            window.setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-2', 'pointer-events-none');
                window.setTimeout(() => toast.remove(), 500);
            }, 4000);
        });
    </script>
</body>
</html>
