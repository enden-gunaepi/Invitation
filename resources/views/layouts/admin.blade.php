<!DOCTYPE html>
<html lang="id" data-layout-ready="false" x-data="{
    darkMode: localStorage.getItem('darkMode') === 'true',
    sidebarOpen: false,
    sidebarExpanded: localStorage.getItem('adminSidebarExpanded') !== 'false',
    companyModalOpen: false,
    isMobile: window.innerWidth < 1024,
    hydrated: false,
    get sidebarWidth() { return this.sidebarExpanded ? '260px' : '72px'; },
    get mainMargin() { return this.isMobile ? '0px' : this.sidebarWidth; }
}" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val));
const syncLayoutVars = () => {
    document.documentElement.style.setProperty('--admin-sidebar-width', sidebarExpanded ? '260px' : '72px');
    document.documentElement.style.setProperty('--admin-main-offset', isMobile ? '0px' : (sidebarExpanded ? '260px' : '72px'));
};
$watch('sidebarExpanded', val => { localStorage.setItem('adminSidebarExpanded', val);
    syncLayoutVars(); });
window.addEventListener('resize', () => { isMobile = window.innerWidth < 1024;
    syncLayoutVars(); });
syncLayoutVars();
requestAnimationFrame(() => {
    hydrated = true;
    document.documentElement.setAttribute('data-layout-ready', 'true');
});"
    :class="{ 'dark': darkMode }">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Geist:wght@100..900&family=JetBrains+Mono:wght@100..800&display=swap"
        rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        (() => {
            const doc = document.documentElement;
            const sidebarExpanded = localStorage.getItem('adminSidebarExpanded') !== 'false';
            const isMobile = window.innerWidth < 1024;
            doc.style.setProperty('--admin-sidebar-width', sidebarExpanded ? '260px' : '72px');
            doc.style.setProperty('--admin-main-offset', isMobile ? '0px' : (sidebarExpanded ? '260px' : '72px'));
        })();
        if (localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia(
                '(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
    @stack('head')
    <style>
        [x-cloak] {
            display: none !important;
        }

        :root {
            --admin-sidebar-width: 260px;
            --admin-main-offset: 260px;
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
            --primary: #db2777;
            /* Pink accent */
            --on-primary: #ffffff;
            --secondary-container: #fce7f3;
            /* Pink-100 */
            --on-secondary-container: #be185d;
            /* Pink-700 */
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
            --primary: #f472b6;
            /* Pink-400 */
            --on-primary: #1a1a1a;
            --secondary-container: rgba(244, 114, 182, 0.15);
            /* Pink tint */
            --on-secondary-container: #fbcfe8;
            /* Pink-200 */

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
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Animations */
        .animate-slide-up {
            animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Nav active state — landing page style */
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
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 16px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
            border: none;
            text-decoration: none;
            font-family: 'Geist', sans-serif;
        }

        .btn-primary {
            background: var(--accent);
            color: var(--on-primary);
        }

        .btn-primary:hover {
            background: var(--accent-hover);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: var(--hover-bg);
            color: var(--text);
            border: 1px solid var(--border);
        }

        .btn-secondary:hover {
            background: rgba(0, 0, 0, 0.08);
        }

        .dark .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.08);
        }

        .btn-danger {
            background: rgba(186, 26, 26, 0.08);
            color: var(--danger);
        }

        .btn-danger:hover {
            background: rgba(186, 26, 26, 0.14);
        }

        .form-label {
            font-size: 13px;
            font-weight: 500;
            color: var(--text);
            margin-bottom: 6px;
            display: block;
        }

        .form-input {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            background: var(--bg-secondary);
            color: var(--text);
            font-size: 14px;
            transition: all 0.2s ease;
            font-family: 'Geist', sans-serif;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-bg);
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 2px 10px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-success,
        .badge-active {
            background: rgba(21, 128, 61, 0.10);
            color: var(--success-clr, #15803d);
        }

        .badge-warning,
        .badge-pending {
            background: rgba(161, 98, 7, 0.10);
            color: var(--warning-clr, #a16207);
        }

        .badge-danger {
            background: rgba(186, 26, 26, 0.10);
            color: var(--danger);
        }

        .badge-info {
            background: var(--accent-bg);
            color: var(--accent);
        }

        .badge-default,
        .badge-draft {
            background: var(--hover-bg);
            color: var(--text-secondary);
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--on-surface-variant);
            padding: 10px 16px;
            text-align: left;
            border-bottom: 1px solid var(--outline-variant);
        }

        tbody td {
            padding: 12px 16px;
            font-size: 13px;
            border-bottom: 1px solid var(--outline-variant);
            color: var(--on-surface);
        }

        tbody tr {
            transition: background 0.1s ease;
        }

        tbody tr:hover {
            background: var(--hover-bg);
        }

        tbody tr:last-child td {
            border-bottom: none;
        }
    </style>
</head>

<body class="text-[--on-surface] font-sans antialiased overflow-hidden transition-colors duration-300"
    style="background-color: var(--surface);">

    <!-- Toasts -->
    @if (session('success'))
        <div data-toast
            class="fixed top-6 right-6 z-[9999] bg-[--surface-lowest] border border-green-500/20 text-green-700 dark:text-green-400 px-5 py-3 rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] flex items-center gap-3 animate-slide-up transition-all duration-500"
            style="border-color: var(--outline-variant);">
            <span class="material-symbols-outlined text-green-600" style="font-size:18px;">check_circle</span>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif
    @if (session('error'))
        <div data-toast
            class="fixed top-6 right-6 z-[9999] bg-[--surface-lowest] border text-red-700 dark:text-red-400 px-5 py-3 rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] flex items-center gap-3 animate-slide-up transition-all duration-500"
            style="border-color: rgba(186,26,26,0.3);">
            <span class="material-symbols-outlined text-red-600" style="font-size:18px;">error</span>
            <span class="text-sm font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Mobile Sidebar Overlay -->
    <div class="fixed inset-0 bg-black/30 backdrop-blur-sm z-40 lg:hidden transition-opacity duration-300"
        :class="sidebarOpen ? 'opacity-100 visible' : 'opacity-0 invisible'" @click="sidebarOpen = false"></div>

    <!-- Sidebar -->
    <aside class="fixed top-0 left-0 h-full z-50 flex flex-col border-r glass-sidebar overflow-hidden"
        style="border-right-width: 1px; width: var(--admin-sidebar-width); transition: none;"
        :style="{
            width: sidebarExpanded ? '260px' : '72px',
            transform: isMobile ? (sidebarOpen ? 'translateX(0)' : 'translateX(-260px)') : 'translateX(0)',
            background: darkMode ? 'rgba(17,19,24,0.96)' : 'rgba(249,249,249,0.92)',
            borderColor: 'var(--outline-variant)',
            transition: hydrated ?
                'width 0.3s cubic-bezier(0.4,0,0.2,1), transform 0.3s cubic-bezier(0.4,0,0.2,1), background 0.3s ease' :
                'none'
        }">

        <!-- Brand -->
        <a href="{{ route('admin.dashboard') }}"
            class="px-6 py-5 flex items-center gap-3 shrink-0 border-b cursor-pointer hover:bg-black/5 dark:hover:bg-white/5 transition-colors"
            style="border-color: var(--outline-variant); min-height: 72px;">
            <div class="w-8 h-8 flex items-center justify-center shrink-0 font-bold text-sm overflow-visible"
                style="{{ $brandLogoUrl ? 'background: transparent; color: var(--on-surface);' : 'background: var(--primary); color: var(--on-primary); border-radius: 0.75rem;' }}">
                @if ($brandLogoUrl)
                    <img src="{{ $brandLogoUrl }}" alt="Logo" class="max-w-full max-h-full object-contain">
                @else
                    {{ substr($brandName ?? 'J', 0, 1) }}
                @endif
            </div>
            <div class="whitespace-nowrap overflow-hidden"
                style="transition: opacity 0.25s ease, width 0.3s cubic-bezier(0.4,0,0.2,1);"
                :style="{ opacity: (!isMobile && !sidebarExpanded) ? '0' : '1', width: (!isMobile && !sidebarExpanded) ? '0' :
                        'auto' }">
                <div class="text-[15px] font-semibold leading-none"
                    style="color: var(--on-surface); letter-spacing: -0.01em;">{{ $brandName ?? 'Janji Suci Kita' }}
                </div>
                <div class="text-[11px] mt-0.5"
                    style="color: var(--on-surface-variant); letter-spacing: 0.04em; text-transform: uppercase;">
                    Administrator</div>
            </div>
        </a>

        <!-- Navigation Links -->
        <nav class="flex-1 overflow-y-auto scrollbar-hide px-3 py-4 flex flex-col gap-5">

            <!-- Menu Utama -->
            <div class="flex flex-col gap-0.5">
                <div class="text-[10px] font-semibold px-3 mb-1.5 uppercase tracking-widest"
                    style="color: var(--on-surface-variant); transition: opacity 0.25s ease;"
                    :style="{ opacity: (!isMobile && !sidebarExpanded) ? '0' : '0.5' }">Menu</div>

                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'nav-item-active' : '' }}"
                    style="{{ !request()->routeIs('admin.dashboard') ? 'color: var(--on-surface-variant);' : '' }}"
                    onmouseover="{{ !request()->routeIs('admin.dashboard') ? 'this.style.background=\"var(--surface-container)\"' : '' }}"
                    onmouseout="{{ !request()->routeIs('admin.dashboard') ? 'this.style.background=\"\"' : '' }}"
                    title="Dashboard">
                    <span class="material-symbols-outlined shrink-0" style="font-size: 20px;">dashboard</span>
                    <span class="text-sm whitespace-nowrap overflow-hidden"
                        style="transition: opacity 0.2s ease, max-width 0.3s cubic-bezier(0.4,0,0.2,1);"
                        :style="{ opacity: (!isMobile && !sidebarExpanded) ? '0' : '1', maxWidth: (!isMobile && !
                                sidebarExpanded) ? '0' : '200px' }">Dashboard</span>
                </a>
            </div>

            <!-- Manajemen -->
            <div class="flex flex-col gap-0.5">
                <div class="text-[10px] font-semibold px-3 mb-1.5 uppercase tracking-widest"
                    style="color: var(--on-surface-variant); transition: opacity 0.25s ease;"
                    :style="{ opacity: (!isMobile && !sidebarExpanded) ? '0' : '0.5' }">Manajemen</div>

                <a href="{{ route('admin.users.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.users.*') ? 'nav-item-active' : '' }}"
                    style="{{ !request()->routeIs('admin.users.*') ? 'color: var(--on-surface-variant);' : '' }}"
                    onmouseover="{{ !request()->routeIs('admin.users.*') ? 'this.style.background=\"var(--surface-container)\"' : '' }}"
                    onmouseout="{{ !request()->routeIs('admin.users.*') ? 'this.style.background=\"\"' : '' }}"
                    title="Users">
                    <span class="material-symbols-outlined shrink-0" style="font-size: 20px;">group</span>
                    <span class="text-sm whitespace-nowrap overflow-hidden"
                        style="transition: opacity 0.2s ease, max-width 0.3s cubic-bezier(0.4,0,0.2,1);"
                        :style="{ opacity: (!isMobile && !sidebarExpanded) ? '0' : '1', maxWidth: (!isMobile && !
                                sidebarExpanded) ? '0' : '200px' }">Users</span>
                </a>

                <a href="{{ route('admin.balance.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.balance.*') ? 'nav-item-active' : '' }}"
                    style="{{ !request()->routeIs('admin.balance.*') ? 'color: var(--on-surface-variant);' : '' }}"
                    onmouseover="{{ !request()->routeIs('admin.balance.*') ? 'this.style.background=\"var(--surface-container)\"' : '' }}"
                    onmouseout="{{ !request()->routeIs('admin.balance.*') ? 'this.style.background=\"\"' : '' }}"
                    title="Saldo">
                    <span class="material-symbols-outlined shrink-0"
                        style="font-size: 20px;">account_balance_wallet</span>
                    <span class="text-sm whitespace-nowrap overflow-hidden"
                        style="transition: opacity 0.2s ease, max-width 0.3s cubic-bezier(0.4,0,0.2,1);"
                        :style="{ opacity: (!isMobile && !sidebarExpanded) ? '0' : '1', maxWidth: (!isMobile && !
                                sidebarExpanded) ? '0' : '200px' }">Saldo</span>
                </a>

                <a href="{{ route('admin.invitations.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.invitations.*') ? 'nav-item-active' : '' }}"
                    style="{{ !request()->routeIs('admin.invitations.*') ? 'color: var(--on-surface-variant);' : '' }}"
                    onmouseover="{{ !request()->routeIs('admin.invitations.*') ? 'this.style.background=\"var(--surface-container)\"' : '' }}"
                    onmouseout="{{ !request()->routeIs('admin.invitations.*') ? 'this.style.background=\"\"' : '' }}"
                    title="Undangan">
                    <span class="material-symbols-outlined shrink-0" style="font-size: 20px;">mail</span>
                    <span class="text-sm whitespace-nowrap overflow-hidden flex-1"
                        style="transition: opacity 0.2s ease, max-width 0.3s cubic-bezier(0.4,0,0.2,1);"
                        :style="{ opacity: (!isMobile && !sidebarExpanded) ? '0' : '1', maxWidth: (!isMobile && !
                                sidebarExpanded) ? '0' : '200px' }">Undangan</span>
                    @php $pendingCount = \App\Models\Invitation::where('status', 'pending')->count(); @endphp
                    @if ($pendingCount > 0)
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full ml-auto shrink-0"
                            style="background: var(--secondary-container); color: var(--on-secondary-container); transition: opacity 0.2s ease;"
                            :style="{ opacity: (!isMobile && !sidebarExpanded) ? '0' : '1' }">{{ $pendingCount }}</span>
                    @endif
                </a>

                <a href="{{ route('admin.templates.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.templates.*') ? 'nav-item-active' : '' }}"
                    style="{{ !request()->routeIs('admin.templates.*') ? 'color: var(--on-surface-variant);' : '' }}"
                    onmouseover="{{ !request()->routeIs('admin.templates.*') ? 'this.style.background=\"var(--surface-container)\"' : '' }}"
                    onmouseout="{{ !request()->routeIs('admin.templates.*') ? 'this.style.background=\"\"' : '' }}"
                    title="Template">
                    <span class="material-symbols-outlined shrink-0" style="font-size: 20px;">layers</span>
                    <span class="text-sm whitespace-nowrap overflow-hidden"
                        style="transition: opacity 0.2s ease, max-width 0.3s cubic-bezier(0.4,0,0.2,1);"
                        :style="{ opacity: (!isMobile && !sidebarExpanded) ? '0' : '1', maxWidth: (!isMobile && !
                                sidebarExpanded) ? '0' : '200px' }">Template</span>
                </a>

                <a href="{{ route('admin.packages.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.packages.*') ? 'nav-item-active' : '' }}"
                    style="{{ !request()->routeIs('admin.packages.*') ? 'color: var(--on-surface-variant);' : '' }}"
                    onmouseover="{{ !request()->routeIs('admin.packages.*') ? 'this.style.background=\"var(--surface-container)\"' : '' }}"
                    onmouseout="{{ !request()->routeIs('admin.packages.*') ? 'this.style.background=\"\"' : '' }}"
                    title="Paket">
                    <span class="material-symbols-outlined shrink-0" style="font-size: 20px;">inventory_2</span>
                    <span class="text-sm whitespace-nowrap overflow-hidden"
                        style="transition: opacity 0.2s ease, max-width 0.3s cubic-bezier(0.4,0,0.2,1);"
                        :style="{ opacity: (!isMobile && !sidebarExpanded) ? '0' : '1', maxWidth: (!isMobile && !
                                sidebarExpanded) ? '0' : '200px' }">Paket</span>
                </a>

                <a href="{{ route('admin.payments.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.payments.*') ? 'nav-item-active' : '' }}"
                    style="{{ !request()->routeIs('admin.payments.*') ? 'color: var(--on-surface-variant);' : '' }}"
                    onmouseover="{{ !request()->routeIs('admin.payments.*') ? 'this.style.background=\"var(--surface-container)\"' : '' }}"
                    onmouseout="{{ !request()->routeIs('admin.payments.*') ? 'this.style.background=\"\"' : '' }}"
                    title="Pembayaran">
                    <span class="material-symbols-outlined shrink-0" style="font-size: 20px;">payments</span>
                    <span class="text-sm whitespace-nowrap overflow-hidden flex-1"
                        style="transition: opacity 0.2s ease, max-width 0.3s cubic-bezier(0.4,0,0.2,1);"
                        :style="{ opacity: (!isMobile && !sidebarExpanded) ? '0' : '1', maxWidth: (!isMobile && !
                                sidebarExpanded) ? '0' : '200px' }">Pembayaran</span>
                    @php $pendingPayments = \App\Models\Payment::where('payment_status', 'pending')->count(); @endphp
                    @if ($pendingPayments > 0)
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full ml-auto shrink-0"
                            style="background: rgba(161,98,7,0.12); color: #a16207; transition: opacity 0.2s ease;"
                            :style="{ opacity: (!isMobile && !sidebarExpanded) ? '0' : '1' }">{{ $pendingPayments }}</span>
                    @endif
                </a>

                {{-- <a href="{{ route('admin.integration.payment-gateway') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.integration.payment-gateway*') || request()->routeIs('admin.payment-gateway.*') ? 'nav-item-active' : '' }}"
                    style="{{ !(request()->routeIs('admin.integration.payment-gateway*') || request()->routeIs('admin.payment-gateway.*')) ? 'color: var(--on-surface-variant);' : '' }}"
                    onmouseover="{{ !(request()->routeIs('admin.integration.payment-gateway*') || request()->routeIs('admin.payment-gateway.*')) ? 'this.style.background=\"var(--surface-container)\"' : '' }}"
                    onmouseout="{{ !(request()->routeIs('admin.integration.payment-gateway*') || request()->routeIs('admin.payment-gateway.*')) ? 'this.style.background=\"\"' : '' }}"
                    title="Payment Gateway">
                    <span class="material-symbols-outlined shrink-0" style="font-size: 20px;">credit_card</span>
                    <span class="text-sm whitespace-nowrap overflow-hidden"
                          style="transition: opacity 0.2s ease, max-width 0.3s cubic-bezier(0.4,0,0.2,1);"
                          :style="{ opacity: (!isMobile && !sidebarExpanded) ? '0' : '1', maxWidth: (!isMobile && !sidebarExpanded) ? '0' : '200px' }">Payment Gateway</span>
                </a> --}}

                <a href="{{ route('admin.affiliate.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.affiliate.index') ? 'nav-item-active' : '' }}"
                    style="{{ !request()->routeIs('admin.affiliate.index') ? 'color: var(--on-surface-variant);' : '' }}"
                    onmouseover="{{ !request()->routeIs('admin.affiliate.index') ? 'this.style.background=\"var(--surface-container)\"' : '' }}"
                    onmouseout="{{ !request()->routeIs('admin.affiliate.index') ? 'this.style.background=\"\"' : '' }}"
                    title="Affiliate">
                    <span class="material-symbols-outlined shrink-0" style="font-size: 20px;">hub</span>
                    <span class="text-sm whitespace-nowrap overflow-hidden flex-1"
                        style="transition: opacity 0.2s ease, max-width 0.3s cubic-bezier(0.4,0,0.2,1);"
                        :style="{ opacity: (!isMobile && !sidebarExpanded) ? '0' : '1', maxWidth: (!isMobile && !
                                sidebarExpanded) ? '0' : '200px' }">Affiliate</span>
                    @php $pendingCommissions = \App\Models\AffiliateCommission::where('status', 'pending')->count(); @endphp
                    @if ($pendingCommissions > 0)
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full ml-auto shrink-0"
                            style="background: rgba(161,98,7,0.12); color: #a16207; transition: opacity 0.2s ease;"
                            :style="{ opacity: (!isMobile && !sidebarExpanded) ? '0' : '1' }">{{ $pendingCommissions }}</span>
                    @endif
                </a>

                <a href="{{ route('admin.affiliate.payouts') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.affiliate.payouts*') ? 'nav-item-active' : '' }}"
                    style="{{ !request()->routeIs('admin.affiliate.payouts*') ? 'color: var(--on-surface-variant);' : '' }}"
                    onmouseover="{{ !request()->routeIs('admin.affiliate.payouts*') ? 'this.style.background=\"var(--surface-container)\"' : '' }}"
                    onmouseout="{{ !request()->routeIs('admin.affiliate.payouts*') ? 'this.style.background=\"\"' : '' }}"
                    title="Affiliate Payout">
                    <span class="material-symbols-outlined shrink-0"
                        style="font-size: 20px;">account_balance_wallet</span>
                    <span class="text-sm whitespace-nowrap overflow-hidden flex-1"
                        style="transition: opacity 0.2s ease, max-width 0.3s cubic-bezier(0.4,0,0.2,1);"
                        :style="{ opacity: (!isMobile && !sidebarExpanded) ? '0' : '1', maxWidth: (!isMobile && !
                                sidebarExpanded) ? '0' : '200px' }">Affiliate
                        Payout</span>
                    @php $pendingPayouts = \App\Models\PayoutRequest::where('status', 'pending')->count(); @endphp
                    @if ($pendingPayouts > 0)
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full ml-auto shrink-0"
                            style="background: rgba(161,98,7,0.12); color: #a16207; transition: opacity 0.2s ease;"
                            :style="{ opacity: (!isMobile && !sidebarExpanded) ? '0' : '1' }">{{ $pendingPayouts }}</span>
                    @endif
                </a>
            </div>

            <!-- Sistem -->
            <div class="flex flex-col gap-0.5">
                <div class="text-[10px] font-semibold px-3 mb-1.5 uppercase tracking-widest"
                    style="color: var(--on-surface-variant); transition: opacity 0.25s ease;"
                    :style="{ opacity: (!isMobile && !sidebarExpanded) ? '0' : '0.5' }">Sistem</div>

                @php $isIntegration = request()->routeIs('admin.integration.*'); @endphp
                <a href="{{ route('admin.integration.telegram') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 {{ $isIntegration ? 'nav-item-active' : '' }}"
                    style="{{ !$isIntegration ? 'color: var(--on-surface-variant);' : '' }}"
                    onmouseover="{{ !$isIntegration ? 'this.style.background=\"var(--surface-container)\"' : '' }}"
                    onmouseout="{{ !$isIntegration ? 'this.style.background=\"\"' : '' }}" title="Integrasi">
                    <span class="material-symbols-outlined shrink-0" style="font-size: 20px;">hub</span>
                    <span class="text-sm whitespace-nowrap overflow-hidden"
                        style="transition: opacity 0.2s ease, max-width 0.3s cubic-bezier(0.4,0,0.2,1);"
                        :style="{ opacity: (!isMobile && !sidebarExpanded) ? '0' : '1', maxWidth: (!isMobile && !
                                sidebarExpanded) ? '0' : '200px' }">Integrasi</span>
                </a>

                <a href="{{ route('admin.system.reliability') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.system.reliability') ? 'nav-item-active' : '' }}"
                    style="{{ !request()->routeIs('admin.system.reliability') ? 'color: var(--on-surface-variant);' : '' }}"
                    onmouseover="{{ !request()->routeIs('admin.system.reliability') ? 'this.style.background=\"var(--surface-container)\"' : '' }}"
                    onmouseout="{{ !request()->routeIs('admin.system.reliability') ? 'this.style.background=\"\"' : '' }}"
                    title="Reliability">
                    <span class="material-symbols-outlined shrink-0" style="font-size: 20px;">monitor_heart</span>
                    <span class="text-sm whitespace-nowrap overflow-hidden flex-1"
                        style="transition: opacity 0.2s ease, max-width 0.3s cubic-bezier(0.4,0,0.2,1);"
                        :style="{ opacity: (!isMobile && !sidebarExpanded) ? '0' : '1', maxWidth: (!isMobile && !
                                sidebarExpanded) ? '0' : '200px' }">Reliability</span>
                    @php $failedJobsCount = \Illuminate\Support\Facades\DB::table('failed_jobs')->count(); @endphp
                    @if ($failedJobsCount > 0)
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full ml-auto shrink-0"
                            style="background: rgba(186,26,26,0.10); color: #ba1a1a; transition: opacity 0.2s ease;"
                            :style="{ opacity: (!isMobile && !sidebarExpanded) ? '0' : '1' }">{{ $failedJobsCount }}</span>
                    @endif
                </a>

                <a href="{{ route('admin.system.media-maintenance') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.system.media-maintenance*') ? 'nav-item-active' : '' }}"
                    style="{{ !request()->routeIs('admin.system.media-maintenance*') ? 'color: var(--on-surface-variant);' : '' }}"
                    onmouseover="{{ !request()->routeIs('admin.system.media-maintenance*') ? 'this.style.background=\"var(--surface-container)\"' : '' }}"
                    onmouseout="{{ !request()->routeIs('admin.system.media-maintenance*') ? 'this.style.background=\"\"' : '' }}"
                    title="Media Maintenance">
                    <span class="material-symbols-outlined shrink-0" style="font-size: 20px;">folder_delete</span>
                    <span class="text-sm whitespace-nowrap overflow-hidden flex-1"
                        style="transition: opacity 0.2s ease, max-width 0.3s cubic-bezier(0.4,0,0.2,1);"
                        :style="{ opacity: (!isMobile && !sidebarExpanded) ? '0' : '1', maxWidth: (!isMobile && !
                                sidebarExpanded) ? '0' : '200px' }">Media
                        Maintenance</span>
                </a>
            </div>
        </nav>

        <!-- Footer / Settings -->
        <div class="px-3 py-4 shrink-0 border-t" style="border-color: var(--outline-variant);">
            <a href="{{ route('admin.settings.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 w-full {{ request()->routeIs('admin.settings.*') ? 'nav-item-active' : '' }}"
                style="{{ !request()->routeIs('admin.settings.*') ? 'color: var(--on-surface-variant);' : '' }}"
                onmouseover="{{ !request()->routeIs('admin.settings.*') ? 'this.style.background=\"var(--surface-container)\"' : '' }}"
                onmouseout="{{ !request()->routeIs('admin.settings.*') ? 'this.style.background=\"\"' : '' }}"
                title="Pengaturan">
                <span class="material-symbols-outlined shrink-0" style="font-size: 20px;">settings</span>
                <span class="text-sm whitespace-nowrap overflow-hidden"
                    style="transition: opacity 0.2s ease, max-width 0.3s cubic-bezier(0.4,0,0.2,1);"
                    :style="{ opacity: (!isMobile && !sidebarExpanded) ? '0' : '1', maxWidth: (!isMobile && !sidebarExpanded) ?
                            '0' : '200px' }">Pengaturan</span>
            </a>
        </div>
    </aside>


    <!-- App Wrapper -->
    <div class="flex flex-col h-screen overflow-hidden"
        style="margin-left: var(--admin-main-offset); transition: none;"
        :style="{ marginLeft: mainMargin, transition: hydrated ? 'margin-left 0.3s cubic-bezier(0.4,0,0.2,1)' : 'none' }">

        <!-- Topbar -->
        <header class="sticky top-0 z-40 flex items-center justify-between px-5 shrink-0 border-b glass-sidebar"
            style="height: 64px; transition: background 0.3s ease, border-color 0.3s ease;"
            :style="{
                background: darkMode ? 'rgba(17,19,24,0.92)' : 'rgba(249,249,249,0.88)',
                borderColor: 'var(--outline-variant)'
            }">
            <div class="flex items-center gap-3">
                <!-- Toggle Sidebar — minimalist floating icon -->
                <button @click="isMobile ? (sidebarOpen = !sidebarOpen) : (sidebarExpanded = !sidebarExpanded)"
                    class="flex items-center justify-center w-8 h-8 rounded-full shrink-0 transition-all duration-200 focus:outline-none"
                    style="color: var(--on-surface-variant);" :style="{ background: 'transparent' }"
                    @mouseenter="$el.style.background = darkMode ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.05)'; $el.style.color = 'var(--on-surface)'"
                    @mouseleave="$el.style.background = 'transparent'; $el.style.color = 'var(--on-surface-variant)'"
                    title="Toggle Sidebar">
                    <span class="material-symbols-outlined"
                        style="font-size: 22px; transition: transform 0.3s cubic-bezier(0.4,0,0.2,1);"
                        :style="{ transform: (!isMobile && sidebarExpanded) ? 'rotate(0deg)' : 'rotate(180deg)' }"
                        x-text="isMobile ? 'menu' : 'chevron_left'"></span>
                </button>

                <!-- Page Title -->
                <div class="flex flex-col justify-center">
                    <h1 class="text-[15px] font-semibold leading-tight"
                        style="color: var(--on-surface); letter-spacing: -0.01em;">@yield('page-title', 'Dashboard')</h1>
                    @hasSection('page-subtitle')
                        <p class="text-[11px] mt-0.5" style="color: var(--on-surface-variant);">@yield('page-subtitle')</p>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-2" x-data="{ userMenuOpen: false }">
                <!-- Theme Toggle -->
                <button @click="darkMode = !darkMode"
                    class="w-9 h-9 rounded-lg flex items-center justify-center transition-all focus:outline-none group"
                    style="color: var(--on-surface-variant); background: var(--surface-container);"
                    onmouseover="this.style.background='var(--surface-container-high)'"
                    onmouseout="this.style.background='var(--surface-container)'">
                    <span class="material-symbols-outlined" style="font-size: 20px;"
                        :class="darkMode ? 'text-amber-500' : ''"
                        x-text="darkMode ? 'light_mode' : 'dark_mode'"></span>
                </button>

                <!-- Divider -->
                <div class="w-px h-5 mx-1" style="background: var(--outline-variant);"></div>

                <!-- User Dropdown -->
                <div class="relative">
                    <button @click="userMenuOpen = !userMenuOpen"
                        class="flex items-center gap-2.5 px-3 py-1.5 rounded-full transition-all focus:outline-none"
                        style="background: var(--surface-container); color: var(--on-surface);"
                        onmouseover="this.style.background='var(--surface-container-high)'"
                        onmouseout="this.style.background='var(--surface-container)'">
                        @if (auth()->user()->avatar)
                            <div class="w-6 h-6 rounded-full overflow-hidden shrink-0">
                                <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="Avatar"
                                    class="w-full h-full object-cover">
                            </div>
                        @else
                            <div class="w-6 h-6 rounded-full flex items-center justify-center font-semibold text-[11px] shrink-0"
                                style="background: var(--primary); color: var(--on-primary);">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                        @endif
                        <span class="text-[13px] font-medium hidden sm:block"
                            style="color: var(--on-surface);">{{ auth()->user()->name }}</span>
                        <span class="material-symbols-outlined"
                            style="font-size: 14px; color: var(--on-surface-variant);">expand_more</span>
                    </button>

                    <div x-show="userMenuOpen" @click.outside="userMenuOpen = false"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                        class="absolute right-0 mt-2 w-52 rounded-xl overflow-hidden shadow-[0_8px_32px_rgba(0,0,0,0.08)] z-50 border"
                        style="background: var(--surface-lowest); border-color: var(--outline-variant);" x-cloak>
                        <div class="px-4 py-3 border-b"
                            style="background: var(--surface-container-low); border-color: var(--outline-variant);">
                            <p class="text-[13px] font-semibold truncate" style="color: var(--on-surface);">
                                {{ auth()->user()->name }}</p>
                            <p class="text-[11px] truncate mt-0.5" style="color: var(--on-surface-variant);">
                                {{ auth()->user()->email }}</p>
                        </div>
                        <a href="{{ route('admin.settings.index') }}"
                            class="flex items-center gap-3 px-4 py-2.5 text-sm transition-colors"
                            style="color: var(--on-surface-variant);"
                            onmouseover="this.style.background='var(--surface-container)'; this.style.color='var(--on-surface)'"
                            onmouseout="this.style.background=''; this.style.color='var(--on-surface-variant)'">
                            <span class="material-symbols-outlined" style="font-size: 17px;">settings</span>
                            Pengaturan
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="block w-full m-0">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center gap-3 px-4 py-2.5 text-sm transition-colors text-left"
                                style="color: #ba1a1a;" onmouseover="this.style.background='rgba(186,26,26,0.06)'"
                                onmouseout="this.style.background=''">
                                <span class="material-symbols-outlined" style="font-size: 17px;">logout</span> Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="flex-1 overflow-y-auto scrollbar-hide transition-colors duration-300"
            style="background: var(--surface-lowest);">
            <div class="p-6 pb-28 lg:pb-10 min-h-full">
                @yield('content')
            </div>
        </main>
    </div>

    <nav class="lg:hidden fixed bottom-4 left-4 right-4 rounded-2xl shadow-xl z-50 p-2 border"
        :style="{ background: darkMode ? 'rgba(17,19,24,0.95)' : 'rgba(249,249,249,0.95)',
        borderColor: 'var(--outline-variant)' }"
        style="backdrop-filter: blur(24px);">
        <div class="flex gap-1 overflow-x-auto snap-x snap-mandatory scrollbar-hide px-1" data-mobile-dock-track>
            <div class="snap-start shrink-0 w-1/5 flex justify-center">
                <a href="{{ route('admin.dashboard') }}"
                    class="w-11 h-11 flex flex-col items-center justify-center gap-1 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? '' : '' }}"
                    style="{{ request()->routeIs('admin.dashboard') ? 'background: var(--primary); color: var(--on-primary);' : 'color: var(--on-surface-variant);' }}">
                    <span class="material-symbols-outlined" style="font-size: 22px;">dashboard</span>
                </a>
            </div>
            <div class="snap-start shrink-0 w-1/5 flex justify-center">
                <a href="{{ route('admin.invitations.index') }}"
                    class="w-11 h-11 flex flex-col items-center justify-center gap-1 rounded-xl transition-all duration-200"
                    style="{{ request()->routeIs('admin.invitations.*') ? 'background: var(--primary); color: var(--on-primary);' : 'color: var(--on-surface-variant);' }}">
                    <span class="material-symbols-outlined" style="font-size: 22px;">mail</span>
                </a>
            </div>
            <div class="snap-start shrink-0 w-1/5 flex justify-center">
                <a href="{{ route('admin.users.index') }}"
                    class="w-11 h-11 flex flex-col items-center justify-center gap-1 rounded-xl transition-all duration-200"
                    style="{{ request()->routeIs('admin.users.*') ? 'background: var(--primary); color: var(--on-primary);' : 'color: var(--on-surface-variant);' }}">
                    <span class="material-symbols-outlined" style="font-size: 22px;">group</span>
                </a>
            </div>
            <div class="snap-start shrink-0 w-1/5 flex justify-center">
                <a href="{{ route('admin.payments.index') }}"
                    class="w-11 h-11 flex flex-col items-center justify-center gap-1 rounded-xl transition-all duration-200"
                    style="{{ request()->routeIs('admin.payments.*') ? 'background: var(--primary); color: var(--on-primary);' : 'color: var(--on-surface-variant);' }}">
                    <span class="material-symbols-outlined" style="font-size: 22px;">payments</span>
                </a>
            </div>
            <div class="snap-start shrink-0 w-1/5 flex justify-center">
                <a href="{{ route('admin.integration.payment-gateway') }}"
                    class="w-11 h-11 flex flex-col items-center justify-center gap-1 rounded-xl transition-all duration-200"
                    style="{{ request()->routeIs('admin.integration.payment-gateway*') || request()->routeIs('admin.payment-gateway.*') ? 'background: var(--primary); color: var(--on-primary);' : 'color: var(--on-surface-variant);' }}">
                    <span class="material-symbols-outlined" style="font-size: 22px;">credit_card</span>
                </a>
            </div>
            <div class="snap-start shrink-0 w-1/5 flex justify-center">
                <a href="{{ route('admin.settings.index') }}"
                    class="w-11 h-11 flex flex-col items-center justify-center gap-1 rounded-xl transition-all duration-200"
                    style="{{ request()->routeIs('admin.settings.*') ? 'background: var(--primary); color: var(--on-primary);' : 'color: var(--on-surface-variant);' }}">
                    <span class="material-symbols-outlined" style="font-size: 22px;">settings</span>
                </a>
            </div>
        </div>
    </nav>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const track = document.querySelector('[data-mobile-dock-track]');
            if (!track) return;
            const key = 'admin_mobile_dock_scroll_new';
            const saved = localStorage.getItem(key);
            if (saved !== null) {
                track.scrollLeft = parseInt(saved, 10) || 0;
            }
            track.addEventListener('scroll', () => localStorage.setItem(key, String(track.scrollLeft)), {
                passive: true
            });
        });
    </script>
    @stack('scripts')

    {{-- Floating Donation Ad Card (Only for Free Users) --}}
    @php
        $hasPaidSubscription =
            auth()->check() &&
            (auth()->user()->isAdmin() ||
                auth()
                    ->user()
                    ->packageSubscriptions()
                    ->where('status', 'active')
                    ->whereHas('package', function ($q) {
                        $q->where('price', '>', 0);
                    })
                    ->exists());
    @endphp

    @if (!$hasPaidSubscription)
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
            class="fixed bottom-6 right-6 z-[9999] rounded-2xl overflow-hidden shadow-2xl transition-all duration-300 border border-amber-400/30"
            :style="sidebarExpanded ? 'width: 212px;' : 'width: 56px; height: 56px; border-radius: 16px;'">

            <button x-show="sidebarExpanded" @click="closeAd()"
                class="absolute top-2 right-2 w-5 h-5 flex items-center justify-center rounded-full bg-black/20 text-gray-400 hover:text-white hover:bg-black/40 transition-colors z-10"
                title="Tutup iklan">
                <i class="fas fa-times text-[10px]"></i>
            </button>

            <div x-show="sidebarExpanded"
                class="p-4 pt-5 text-center bg-gradient-to-b from-slate-800 to-slate-900 border-t border-slate-700">
                <div
                    class="w-10 h-10 mx-auto bg-amber-400 text-slate-900 rounded-full flex items-center justify-center text-lg shadow-[0_0_15px_rgba(251,191,36,0.25)] mb-3 relative animate-bounce">
                    <i class="fas fa-coffee"></i>
                </div>
                <div class="text-amber-400 text-[11px] font-bold mb-1 uppercase tracking-wider">Dukung Kami</div>
                <div class="text-gray-400 text-[10px] leading-relaxed mb-3">Traktir kopi agar update makin ngebut! ☕
                </div>
                <a href="https://saweria.co/gunaepi" target="_blank"
                    class="block w-full py-1.5 bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 text-slate-900 text-[11px] font-bold rounded-lg transition-all shadow-md hover:-translate-y-0.5">
                    Donasi
                </a>
            </div>

            <div x-show="!sidebarExpanded"
                class="w-full h-full bg-gradient-to-br from-amber-400 to-amber-500 flex items-center justify-center relative cursor-pointer hover:scale-105 transition-transform"
                @click="window.open('https://saweria.co/gunaepi')">
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
        <div x-show="companyModalOpen" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="absolute inset-0 bg-black/40 backdrop-blur-md"
            @click="companyModalOpen = false"></div>

        <!-- Modal Card -->
        <div x-show="companyModalOpen" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-8 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-8 scale-95"
            class="relative w-full shadow-2xl overflow-hidden border border-gray-100 dark:border-white/5"
            style="max-width: 420px; border-radius: 24px; background: var(--surface-lowest);" @click.stop>

            <!-- Close Button -->
            <button @click="companyModalOpen = false"
                style="position: absolute; top: 16px; right: 16px; z-index: 20; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 50%; background: rgba(255,255,255,0.8); backdrop-filter: blur(8px); color: #333; cursor: pointer; border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <i class="fas fa-times" style="font-size: 14px;"></i>
            </button>

            <!-- Top Image/Logo Section -->
            <div
                style="position: relative; width: 100%; height: 200px; background: linear-gradient(135deg, #fdf2f8, #fbcfe8); display: flex; align-items: center; justify-content: center; overflow: hidden;">
                @if ($brandLogoUrl)
                    <!-- Wrap logo in a safe container so it's not cropped awkwardly -->
                    <div
                        style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; padding: 20px;">
                        <img src="{{ $brandLogoUrl }}" alt="Company Cover"
                            style="max-width: 100%; max-height: 100%; object-fit: contain; filter: drop-shadow(0 4px 12px rgba(0,0,0,0.08));">
                    </div>
                @else
                    <i class="fas fa-building"
                        style="font-size: 72px; color: rgba(219, 39, 119, 0.3); position: absolute;"></i>
                @endif

                <!-- Badges -->
                <div
                    style="position: absolute; top: 16px; left: 16px; display: flex; flex-wrap: wrap; gap: 8px; z-index: 10;">
                    <span
                        style="padding: 4px 10px; font-size: 10px; font-weight: 600; color: white; background: rgba(0,0,0,0.5); backdrop-filter: blur(8px); border-radius: 8px; border: 1px solid rgba(255,255,255,0.2); display: flex; align-items: center; gap: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        <i class="fas fa-rocket text-pink-400"></i> Pro Plan
                    </span>
                    <span
                        style="padding: 4px 10px; font-size: 10px; font-weight: 600; color: white; background: rgba(0,0,0,0.5); backdrop-filter: blur(8px); border-radius: 8px; border: 1px solid rgba(255,255,255,0.2); display: flex; align-items: center; gap: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        <i class="fas fa-shield-alt text-blue-400"></i> Verified
                    </span>
                </div>
            </div>

            <!-- Content Section -->
            <div
                style="padding: 24px 32px 32px 32px; position: relative; z-index: 20; background: var(--surface-lowest);">
                <h2
                    style="font-size: 24px; font-weight: 700; color: var(--text); margin-top: 0; margin-bottom: 16px; font-family: 'Geist', sans-serif; letter-spacing: -0.02em;">
                    {{ $brandName ?? 'Company Profile' }}
                </h2>

                <div style="font-size: 14px; color: var(--text-secondary); line-height: 1.6; margin-bottom: 24px;">
                    <p style="margin-top: 0; margin-bottom: 8px;"><strong
                            style="color: var(--text); font-weight: 600;">Email:</strong>
                        {{ $brandEmail ?? auth()->user()->email }}</p>
                    @if ($brandPhone)
                        <p style="margin-top: 0; margin-bottom: 8px;"><strong
                                style="color: var(--text); font-weight: 600;">Phone:</strong> {{ $brandPhone }}</p>
                    @endif
                    @if ($brandAddress)
                        <p style="margin-top: 0; margin-bottom: 0;"><strong
                                style="color: var(--text); font-weight: 600;">Address:</strong> {{ $brandAddress }}
                        </p>
                    @endif
                </div>

                <!-- Action Buttons -->
                <div style="margin-top: 8px;">
                    <a href="{{ route('admin.settings.index') }}"
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
