<!DOCTYPE html>
<html lang="id"
    x-data="{ darkMode: localStorage.getItem('darkMode') === 'true', sidebarOpen: false, sidebarExpanded: localStorage.getItem('adminSidebarExpanded') === 'true' }"
    x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val));
$watch('sidebarExpanded', val => localStorage.setItem('adminSidebarExpanded', val))" :class="{ 'dark': darkMode }">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        if (localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
    @stack('head')
    <style>
        [x-cloak] { display: none !important; }
        
        /* Layout Variables for UI Components */
        :root {
            --bg-secondary: #ffffff;
            --border: #f1f5f9;
            --text: #1e293b;
            --text-secondary: #64748b;
            --text-tertiary: #94a3b8;
            --accent: #0071e3;
            --accent-hover: #0077ED;
            --accent-bg: rgba(0, 113, 227, 0.08);
            --danger: #ff3b30;
            --success: #34c759;
            --warning: #ff9500;
            --info: #0071e3;
            --radius-sm: 8px;
            --hover-bg: rgba(0, 0, 0, 0.04);
        }

        .dark {
            --bg-secondary: #1E293B;
            --border: #334155;
            --text: #F8FAFC;
            --text-secondary: #94A3B8;
            --text-tertiary: #64748B;
            --accent: #60A5FA;
            --accent-hover: #3B82F6;
            --accent-bg: rgba(96, 165, 250, 0.15);
            --hover-bg: rgba(255, 255, 255, 0.06);
            --danger: #ef4444;
            --success: #10b981;
            --warning: #f59e0b;
            --info: #3b82f6;
        }

        /* Core Component Styles */
        .card {
            background-color: var(--bg-secondary);
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 1px 2px rgba(0, 0, 0, 0.06);
            border: 1px solid var(--border);
            padding: 1.5rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .dark .card {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2), 0 2px 4px rgba(0, 0, 0, 0.15);
        }
        
        .btn {
            display: inline-flex; align-items: center; gap: 6px; padding: 9px 16px;
            border-radius: var(--radius-sm); font-size: 13px; font-weight: 600;
            cursor: pointer; transition: all 0.15s ease; border: none; text-decoration: none;
        }
        .btn-primary { background: var(--accent); color: white; }
        .btn-primary:hover { background: var(--accent-hover); transform: translateY(-1px); }
        .btn-secondary { background: var(--hover-bg); color: var(--text); border: 1px solid var(--border); }
        .btn-secondary:hover { background: rgba(0,0,0,0.08); }
        .dark .btn-secondary:hover { background: rgba(255,255,255,0.1); }
        .btn-danger { background: rgba(255, 59, 48, 0.1); color: var(--danger); }
        .btn-danger:hover { background: rgba(255, 59, 48, 0.18); }
        
        .form-label { font-size: 13px; font-weight: 500; color: var(--text); margin-bottom: 6px; display: block; }
        .form-input {
            width: 100%; padding: 10px 14px; border: 1px solid var(--border);
            border-radius: var(--radius-sm); background: var(--bg-secondary);
            color: var(--text); font-size: 14px; transition: all 0.2s ease;
        }
        .form-input:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-bg); }

        .badge {
            display: inline-flex; align-items: center; padding: 2px 10px;
            border-radius: 6px; font-size: 11px; font-weight: 600;
        }
        .badge-success, .badge-active { background: rgba(52, 199, 89, 0.12); color: var(--success); }
        .badge-warning, .badge-pending { background: rgba(255, 149, 0, 0.12); color: var(--warning); }
        .badge-danger { background: rgba(255, 59, 48, 0.12); color: var(--danger); }
        .badge-info { background: var(--accent-bg); color: var(--accent); }
        .badge-default, .badge-draft { background: var(--hover-bg); color: var(--text-secondary); }

        .table-container { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        thead th {
            font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;
            color: var(--text-secondary); padding: 10px 16px; text-align: left; border-bottom: 1px solid var(--border);
        }
        tbody td { padding: 12px 16px; font-size: 13px; border-bottom: 1px solid var(--border); color: var(--text); }
        tbody tr { transition: background 0.1s ease; }
        tbody tr:hover { background: var(--hover-bg); }
        tbody tr:last-child td { border-bottom: none; }

        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

        .animate-slide-up { animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        
        .nav-item-active {
            background: rgba(59, 130, 246, 0.15);
            color: #60A5FA !important;
            font-weight: 600;
            position: relative;
        }
        .nav-item-active::before {
            content: '';
            position: absolute; left: 0; top: 0; bottom: 0; width: 3px;
            background: #3B82F6; border-radius: 0 4px 4px 0;
            box-shadow: 0 0 10px rgba(59, 130, 246, 0.5);
        }
    </style>
</head>

<body class="bg-slate-100 dark:bg-slate-950 text-slate-800 dark:text-slate-100 font-sans antialiased overflow-hidden transition-colors duration-300 lg:p-4">
    
    <!-- Toasts -->
    @if (session('success'))
        <div class="fixed top-6 right-6 z-[9999] bg-white dark:bg-slate-800 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 px-5 py-3 rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.1)] flex items-center gap-3 animate-slide-up">
            <i class="fas fa-check-circle text-lg"></i> <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif
    @if (session('error'))
        <div class="fixed top-6 right-6 z-[9999] bg-white dark:bg-slate-800 border border-red-500/30 text-red-600 dark:text-red-400 px-5 py-3 rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.1)] flex items-center gap-3 animate-slide-up">
            <i class="fas fa-exclamation-circle text-lg"></i> <span class="text-sm font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Mobile Sidebar Overlay -->
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-40 lg:hidden transition-opacity duration-300"
         :class="sidebarOpen ? 'opacity-100 visible' : 'opacity-0 invisible'" @click="sidebarOpen = false"></div>

    <!-- Sidebar -->
    <aside class="fixed top-0 left-0 h-full lg:top-4 lg:bottom-4 lg:h-auto z-50 bg-[#1B244A] dark:bg-slate-900 shadow-2xl lg:rounded-[24px] lg:rounded-r-[32px] transition-all duration-300 overflow-hidden flex flex-col border-r lg:border border-white/5 dark:border-white/10 group"
        :class="[
            sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
            sidebarExpanded ? 'w-[260px]' : 'w-[260px] lg:w-[88px]'
        ]">
        
        <!-- Sidebar Profile -->
        <div class="p-6 pb-4 flex items-center gap-4 relative shrink-0">
            @if(auth()->user()->company_logo)
                <div class="w-11 h-11 rounded-[14px] overflow-hidden shrink-0 border border-white/10 shadow-lg transition-all duration-300"
                    :class="!sidebarExpanded ? 'lg:mx-auto lg:w-10 lg:h-10' : ''">
                    <img src="{{ Storage::url(auth()->user()->company_logo) }}" alt="Logo" class="w-full h-full object-cover">
                </div>
            @else
                <div class="w-11 h-11 rounded-[14px] bg-blue-500/20 text-blue-400 flex items-center justify-center font-bold text-xl shrink-0 border border-blue-500/30 shadow-lg transition-all duration-300"
                    :class="!sidebarExpanded ? 'lg:mx-auto lg:w-10 lg:h-10' : ''">
                    {{ substr(auth()->user()->company_name ?? auth()->user()->name, 0, 1) }}
                </div>
            @endif
            
            <div class="flex-1 overflow-hidden transition-all duration-300 whitespace-nowrap"
                 :class="!sidebarExpanded ? 'lg:opacity-0 lg:w-0' : 'opacity-100 w-auto'">
                <div class="font-bold text-white text-sm truncate">{{ auth()->user()->company_name ?? auth()->user()->name }}</div>
                <div class="text-[11px] text-blue-300/70 font-medium tracking-wide">Administrator</div>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 overflow-y-auto scrollbar-hide px-3 pb-6 flex flex-col gap-6 w-full">
            
            <!-- Menu Utama -->
            <div class="flex flex-col gap-1 w-full relative">
                <div class="text-[10px] font-bold text-white/30 uppercase tracking-widest mb-1 px-3 transition-opacity duration-300"
                     :class="!sidebarExpanded ? 'lg:opacity-0' : 'opacity-100'">Menu</div>
                
                <a href="{{ route('admin.dashboard') }}"
                    class="group relative flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-white/60 hover:text-white hover:bg-white/5 transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'nav-item-active' : '' }}" title="Dashboard">
                    <i class="fas fa-house text-[18px] w-6 text-center transition-transform group-hover:scale-110"></i>
                    <span class="font-medium text-sm whitespace-nowrap transition-all duration-300"
                          :class="!sidebarExpanded ? 'lg:opacity-0 lg:w-0 lg:invisible' : 'opacity-100 w-auto visible'">Dashboard</span>
                </a>
            </div>

            <!-- Manajemen -->
            <div class="flex flex-col gap-1 w-full relative">
                <div class="text-[10px] font-bold text-white/30 uppercase tracking-widest mb-1 px-3 transition-opacity duration-300"
                     :class="!sidebarExpanded ? 'lg:opacity-0' : 'opacity-100'">Manajemen</div>
                     
                <a href="{{ route('admin.users.index') }}"
                    class="group relative flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-white/60 hover:text-white hover:bg-white/5 transition-all duration-200 {{ request()->routeIs('admin.users.*') ? 'nav-item-active' : '' }}" title="Users">
                    <i class="fas fa-users text-[18px] w-6 text-center transition-transform group-hover:scale-110"></i>
                    <span class="font-medium text-sm whitespace-nowrap transition-all duration-300"
                          :class="!sidebarExpanded ? 'lg:opacity-0 lg:w-0 lg:invisible' : 'opacity-100 w-auto visible'">Users</span>
                </a>
                
                <a href="{{ route('admin.invitations.index') }}"
                    class="group relative flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-white/60 hover:text-white hover:bg-white/5 transition-all duration-200 {{ request()->routeIs('admin.invitations.*') ? 'nav-item-active' : '' }}" title="Undangan">
                    <i class="fas fa-envelope text-[18px] w-6 text-center transition-transform group-hover:scale-110"></i>
                    <span class="font-medium text-sm whitespace-nowrap flex-1 transition-all duration-300"
                          :class="!sidebarExpanded ? 'lg:opacity-0 lg:w-0 lg:invisible' : 'opacity-100 w-auto visible'">Undangan</span>
                    @php $pendingCount = \App\Models\Invitation::where('status', 'pending')->count(); @endphp
                    @if ($pendingCount > 0)
                        <span class="bg-blue-500/20 text-blue-400 font-bold text-[10px] px-2 py-0.5 rounded-full ml-auto" :class="!sidebarExpanded ? 'hidden' : ''">{{ $pendingCount }}</span>
                    @endif
                </a>
                
                <a href="{{ route('admin.templates.index') }}"
                    class="group relative flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-white/60 hover:text-white hover:bg-white/5 transition-all duration-200 {{ request()->routeIs('admin.templates.*') ? 'nav-item-active' : '' }}" title="Template">
                    <i class="fas fa-palette text-[18px] w-6 text-center transition-transform group-hover:scale-110"></i>
                    <span class="font-medium text-sm whitespace-nowrap transition-all duration-300"
                          :class="!sidebarExpanded ? 'lg:opacity-0 lg:w-0 lg:invisible' : 'opacity-100 w-auto visible'">Template</span>
                </a>
                
                <a href="{{ route('admin.packages.index') }}"
                    class="group relative flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-white/60 hover:text-white hover:bg-white/5 transition-all duration-200 {{ request()->routeIs('admin.packages.*') ? 'nav-item-active' : '' }}" title="Paket">
                    <i class="fas fa-cube text-[18px] w-6 text-center transition-transform group-hover:scale-110"></i>
                    <span class="font-medium text-sm whitespace-nowrap transition-all duration-300"
                          :class="!sidebarExpanded ? 'lg:opacity-0 lg:w-0 lg:invisible' : 'opacity-100 w-auto visible'">Paket</span>
                </a>
                
                <a href="{{ route('admin.payments.index') }}"
                    class="group relative flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-white/60 hover:text-white hover:bg-white/5 transition-all duration-200 {{ request()->routeIs('admin.payments.*') ? 'nav-item-active' : '' }}" title="Pembayaran">
                    <i class="fas fa-credit-card text-[18px] w-6 text-center transition-transform group-hover:scale-110"></i>
                    <span class="font-medium text-sm whitespace-nowrap flex-1 transition-all duration-300"
                          :class="!sidebarExpanded ? 'lg:opacity-0 lg:w-0 lg:invisible' : 'opacity-100 w-auto visible'">Pembayaran</span>
                    @php $pendingPayments = \App\Models\Payment::where('payment_status', 'pending')->count(); @endphp
                    @if ($pendingPayments > 0)
                        <span class="bg-amber-500/20 text-amber-500 font-bold text-[10px] px-2 py-0.5 rounded-full ml-auto" :class="!sidebarExpanded ? 'hidden' : ''">{{ $pendingPayments }}</span>
                    @endif
                </a>

                <a href="{{ route('admin.affiliate.index') }}"
                    class="group relative flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-white/60 hover:text-white hover:bg-white/5 transition-all duration-200 {{ request()->routeIs('admin.affiliate.index') ? 'nav-item-active' : '' }}" title="Affiliate">
                    <i class="fas fa-hand-holding-dollar text-[18px] w-6 text-center transition-transform group-hover:scale-110"></i>
                    <span class="font-medium text-sm whitespace-nowrap flex-1 transition-all duration-300"
                          :class="!sidebarExpanded ? 'lg:opacity-0 lg:w-0 lg:invisible' : 'opacity-100 w-auto visible'">Affiliate</span>
                    @php $pendingCommissions = \App\Models\AffiliateCommission::where('status', 'pending')->count(); @endphp
                    @if ($pendingCommissions > 0)
                        <span class="bg-amber-500/20 text-amber-500 font-bold text-[10px] px-2 py-0.5 rounded-full ml-auto" :class="!sidebarExpanded ? 'hidden' : ''">{{ $pendingCommissions }}</span>
                    @endif
                </a>

                <a href="{{ route('admin.affiliate.payouts') }}"
                    class="group relative flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-white/60 hover:text-white hover:bg-white/5 transition-all duration-200 {{ request()->routeIs('admin.affiliate.payouts*') ? 'nav-item-active' : '' }}" title="Affiliate Payout">
                    <i class="fas fa-wallet text-[18px] w-6 text-center transition-transform group-hover:scale-110"></i>
                    <span class="font-medium text-sm whitespace-nowrap flex-1 transition-all duration-300"
                          :class="!sidebarExpanded ? 'lg:opacity-0 lg:w-0 lg:invisible' : 'opacity-100 w-auto visible'">Affiliate Payout</span>
                    @php $pendingPayouts = \App\Models\PayoutRequest::where('status', 'pending')->count(); @endphp
                    @if ($pendingPayouts > 0)
                        <span class="bg-amber-500/20 text-amber-500 font-bold text-[10px] px-2 py-0.5 rounded-full ml-auto" :class="!sidebarExpanded ? 'hidden' : ''">{{ $pendingPayouts }}</span>
                    @endif
                </a>
            </div>
            
            <!-- Sistem -->
            <div class="flex flex-col gap-1 w-full relative">
                <div class="text-[10px] font-bold text-white/30 uppercase tracking-widest mb-1 px-3 transition-opacity duration-300"
                     :class="!sidebarExpanded ? 'lg:opacity-0' : 'opacity-100'">Sistem</div>
                     
                <a href="{{ route('admin.payment-gateway.index') }}"
                    class="group relative flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-white/60 hover:text-white hover:bg-white/5 transition-all duration-200 {{ request()->routeIs('admin.payment-gateway.*') ? 'nav-item-active' : '' }}" title="Payment Gateway">
                    <i class="fas fa-plug text-[18px] w-6 text-center transition-transform group-hover:scale-110"></i>
                    <span class="font-medium text-sm whitespace-nowrap transition-all duration-300"
                          :class="!sidebarExpanded ? 'lg:opacity-0 lg:w-0 lg:invisible' : 'opacity-100 w-auto visible'">Payment Gateway</span>
                </a>
                <a href="{{ route('admin.system.reliability') }}"
                    class="group relative flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-white/60 hover:text-white hover:bg-white/5 transition-all duration-200 {{ request()->routeIs('admin.system.reliability') ? 'nav-item-active' : '' }}" title="Reliability">
                    <i class="fas fa-heart-pulse text-[18px] w-6 text-center transition-transform group-hover:scale-110"></i>
                    <span class="font-medium text-sm whitespace-nowrap flex-1 transition-all duration-300"
                          :class="!sidebarExpanded ? 'lg:opacity-0 lg:w-0 lg:invisible' : 'opacity-100 w-auto visible'">Reliability</span>
                    @php $failedJobsCount = \Illuminate\Support\Facades\DB::table('failed_jobs')->count(); @endphp
                    @if ($failedJobsCount > 0)
                        <span class="bg-red-500/20 text-red-400 font-bold text-[10px] px-2 py-0.5 rounded-full ml-auto" :class="!sidebarExpanded ? 'hidden' : ''">{{ $failedJobsCount }}</span>
                    @endif
                </a>
            </div>
        </nav>

        <!-- Footer Profile -->
        <div class="px-3 py-4 mt-auto border-t border-white/5 bg-black/10 shrink-0">
            <a href="{{ route('admin.settings.index') }}"
                class="group relative flex items-center gap-3.5 px-3.5 py-2.5 rounded-[14px] bg-white/5 text-white/80 hover:text-white hover:bg-white/10 transition-all duration-200 {{ request()->routeIs('admin.settings.*') ? 'ring-1 ring-white/20' : '' }}" title="Pengaturan">
                <i class="fas fa-gear text-[18px] w-6 text-center transition-transform group-hover:scale-110"></i>
                <span class="font-medium text-sm whitespace-nowrap transition-all duration-300"
                      :class="!sidebarExpanded ? 'lg:opacity-0 lg:w-0 lg:invisible' : 'opacity-100 w-auto visible'">Pengaturan</span>
            </a>
        </div>
    </aside>

    <!-- App Wrapper -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden transition-all duration-300"
        :class="sidebarExpanded ? 'lg:ml-[268px]' : 'lg:ml-[96px]'">
        
        <!-- Topbar -->
        <header class="sticky top-0 z-40 bg-white/90 dark:bg-slate-900/90 backdrop-blur-2xl border-b border-slate-200/60 dark:border-slate-800/60 lg:rounded-b-[20px] shadow-sm px-5 h-[64px] flex items-center justify-between transition-colors duration-300 shrink-0">
            <div class="flex items-center gap-3.5 relative z-10">
                <button @click="window.innerWidth > 1024 ? sidebarExpanded = !sidebarExpanded : sidebarOpen = !sidebarOpen"
                    class="w-[38px] h-[38px] rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 flex items-center justify-center hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-blue-600 dark:hover:text-blue-400 transition-all hover:shadow-sm focus:outline-none active:scale-95" title="Toggle Sidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="flex flex-col justify-center">
                    <h1 class="text-[15px] font-bold text-slate-800 dark:text-slate-100 leading-tight">@yield('page-title', 'Dashboard')</h1>
                    @hasSection('page-subtitle')
                        <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 mt-0.5">@yield('page-subtitle')</p>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-3 relative z-10" x-data="{ userMenuOpen: false }">
                <!-- Theme Toggle -->
                <button @click="darkMode = !darkMode"
                    class="w-[38px] h-[38px] rounded-full bg-slate-100 dark:bg-slate-800 border border-transparent hover:border-slate-200 dark:hover:border-slate-700 text-slate-500 dark:text-slate-400 flex items-center justify-center hover:text-blue-600 dark:hover:text-blue-400 transition-all focus:outline-none group">
                    <i class="fas transition-transform group-hover:scale-110" :class="darkMode ? 'fa-sun text-amber-500' : 'fa-moon'"></i>
                </button>

                <!-- User Dropdown Menu -->
                <div class="relative">
                    <button @click="userMenuOpen = !userMenuOpen"
                        class="flex items-center gap-2.5 pl-1.5 pr-3 py-1.5 rounded-full bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 hover:bg-white dark:hover:bg-slate-800 hover:shadow-sm transition-all focus:outline-none">
                        @if(auth()->user()->avatar)
                            <div class="w-7 h-7 rounded-full overflow-hidden border border-slate-200 dark:border-slate-600 shrink-0">
                                <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                            </div>
                        @else
                            <div class="w-7 h-7 rounded-full bg-blue-100 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400 flex items-center justify-center font-bold text-[11px] shrink-0">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                        @endif
                        <span class="text-[13px] font-semibold text-slate-700 dark:text-slate-200 hidden sm:block tracking-tight">{{ auth()->user()->name }}</span>
                        <i class="fas fa-chevron-down text-[10px] text-slate-400 pl-1"></i>
                    </button>
                    
                    <div x-show="userMenuOpen" @click.outside="userMenuOpen = false"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                        class="absolute right-0 mt-3 w-56 rounded-[18px] bg-white dark:bg-slate-800 shadow-[0_10px_40px_rgba(0,0,0,0.12)] border border-slate-100 dark:border-slate-700 py-2 z-50 overflow-hidden"
                        x-cloak>
                        <div class="px-5 py-3 border-b border-slate-100 dark:border-slate-700 mb-1 bg-slate-50 dark:bg-slate-800/80">
                            <p class="text-[13px] font-bold text-slate-800 dark:text-white truncate">{{ auth()->user()->name }}</p>
                            <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 truncate mt-0.5">{{ auth()->user()->email }}</p>
                        </div>
                        <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-3 px-5 py-2.5 text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                            <i class="fas fa-gear w-4 text-center"></i> Pengaturan
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="block w-full m-0">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-3 px-5 py-2.5 text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 hover:text-red-700 dark:hover:text-red-300 transition-colors text-left">
                                <i class="fas fa-arrow-right-from-bracket w-4 text-center"></i> Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="flex-1 overflow-y-auto scrollbar-hide bg-white dark:bg-[#0B1120] lg:rounded-tl-[32px] lg:shadow-[-5px_-5px_25px_rgba(0,0,0,0.02)] lg:border-l lg:border-t border-slate-200/50 dark:border-slate-800 mt-4 relative z-10 transition-colors duration-300">
            <div class="p-5 pb-28 lg:pb-12 min-h-full">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Mobile Bottom Navigation Dock -->
    <nav class="lg:hidden fixed bottom-4 left-4 right-4 bg-slate-900/95 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl z-50 p-2 py-2.5 shadow-[0_10px_40px_rgba(0,0,0,0.3)]">
        <div class="flex gap-2 overflow-x-auto snap-x snap-mandatory scrollbar-hide px-1" data-mobile-dock-track>
            <div class="snap-start shrink-0 w-1/4 sm:w-1/5 flex justify-center">
                <a href="{{ route('admin.dashboard') }}"
                    class="w-12 h-12 flex flex-col items-center justify-center gap-1 rounded-xl transition-all duration-300 {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/40' : 'text-slate-400 hover:text-white hover:bg-white/10' }}">
                    <i class="fas fa-house text-lg"></i>
                </a>
            </div>
            <div class="snap-start shrink-0 w-1/4 sm:w-1/5 flex justify-center">
                <a href="{{ route('admin.invitations.index') }}"
                    class="w-12 h-12 flex flex-col items-center justify-center gap-1 rounded-xl transition-all duration-300 {{ request()->routeIs('admin.invitations.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/40' : 'text-slate-400 hover:text-white hover:bg-white/10' }}">
                    <i class="fas fa-envelope text-lg"></i>
                </a>
            </div>
            <div class="snap-start shrink-0 w-1/4 sm:w-1/5 flex justify-center">
                <a href="{{ route('admin.users.index') }}"
                    class="w-12 h-12 flex flex-col items-center justify-center gap-1 rounded-xl transition-all duration-300 {{ request()->routeIs('admin.users.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/40' : 'text-slate-400 hover:text-white hover:bg-white/10' }}">
                    <i class="fas fa-users text-lg"></i>
                </a>
            </div>
            <div class="snap-start shrink-0 w-1/4 sm:w-1/5 flex justify-center">
                <a href="{{ route('admin.payments.index') }}"
                    class="w-12 h-12 flex flex-col items-center justify-center gap-1 rounded-xl transition-all duration-300 {{ request()->routeIs('admin.payments.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/40' : 'text-slate-400 hover:text-white hover:bg-white/10' }}">
                    <i class="fas fa-credit-card text-lg"></i>
                </a>
            </div>
            <div class="snap-start shrink-0 w-1/4 sm:w-1/5 flex justify-center">
                <a href="{{ route('admin.affiliate.index') }}"
                    class="w-12 h-12 flex flex-col items-center justify-center gap-1 rounded-xl transition-all duration-300 {{ request()->routeIs('admin.affiliate.index') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/40' : 'text-slate-400 hover:text-white hover:bg-white/10' }}">
                    <i class="fas fa-hand-holding-dollar text-lg"></i>
                </a>
            </div>
            <div class="snap-start shrink-0 w-1/4 sm:w-1/5 flex justify-center">
                <a href="{{ route('admin.affiliate.payouts') }}"
                    class="w-12 h-12 flex flex-col items-center justify-center gap-1 rounded-xl transition-all duration-300 {{ request()->routeIs('admin.affiliate.payouts*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/40' : 'text-slate-400 hover:text-white hover:bg-white/10' }}">
                    <i class="fas fa-wallet text-lg"></i>
                </a>
            </div>
            <div class="snap-start shrink-0 w-1/4 sm:w-1/5 flex justify-center">
                <a href="{{ route('admin.templates.index') }}"
                    class="w-12 h-12 flex flex-col items-center justify-center gap-1 rounded-xl transition-all duration-300 {{ request()->routeIs('admin.templates.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/40' : 'text-slate-400 hover:text-white hover:bg-white/10' }}">
                    <i class="fas fa-palette text-lg"></i>
                </a>
            </div>
            <div class="snap-start shrink-0 w-1/4 sm:w-1/5 flex justify-center">
                <a href="{{ route('admin.packages.index') }}"
                    class="w-12 h-12 flex flex-col items-center justify-center gap-1 rounded-xl transition-all duration-300 {{ request()->routeIs('admin.packages.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/40' : 'text-slate-400 hover:text-white hover:bg-white/10' }}">
                    <i class="fas fa-cube text-lg"></i>
                </a>
            </div>
            <div class="snap-start shrink-0 w-1/4 sm:w-1/5 flex justify-center">
                <a href="{{ route('admin.payment-gateway.index') }}"
                    class="w-12 h-12 flex flex-col items-center justify-center gap-1 rounded-xl transition-all duration-300 {{ request()->routeIs('admin.payment-gateway.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/40' : 'text-slate-400 hover:text-white hover:bg-white/10' }}">
                    <i class="fas fa-plug text-lg"></i>
                </a>
            </div>
            <div class="snap-start shrink-0 w-1/4 sm:w-1/5 flex justify-center">
                <a href="{{ route('admin.system.reliability') }}"
                    class="w-12 h-12 flex flex-col items-center justify-center gap-1 rounded-xl transition-all duration-300 {{ request()->routeIs('admin.system.reliability') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/40' : 'text-slate-400 hover:text-white hover:bg-white/10' }}">
                    <i class="fas fa-heart-pulse text-lg"></i>
                </a>
            </div>
            <div class="snap-start shrink-0 w-1/4 sm:w-1/5 flex justify-center">
                <a href="{{ route('admin.settings.index') }}"
                    class="w-12 h-12 flex flex-col items-center justify-center gap-1 rounded-xl transition-all duration-300 {{ request()->routeIs('admin.settings.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/40' : 'text-slate-400 hover:text-white hover:bg-white/10' }}">
                    <i class="fas fa-gear text-lg"></i>
                </a>
            </div>
        </div>
    </nav>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const track = document.querySelector('[data-mobile-dock-track]');
            if (!track) return;
            const key = 'admin_mobile_dock_scroll_new';
            const saved = localStorage.getItem(key);
            if (saved !== null) { track.scrollLeft = parseInt(saved, 10) || 0; }
            else {
                const active = track.querySelector('.bg-blue-600');
                if (active) active.scrollIntoView({ behavior: 'auto', inline: 'center', block: 'nearest' });
            }
            track.addEventListener('scroll', () => localStorage.setItem(key, String(track.scrollLeft)), { passive: true });
        });
    </script>
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
        class="fixed bottom-6 right-6 z-[9999] rounded-2xl overflow-hidden shadow-2xl transition-all duration-300 border border-amber-400/30"
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
            <div class="text-gray-400 text-[10px] leading-relaxed mb-3">Traktir kopi agar update makin ngebut! ☕</div>
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
</body>
</html>