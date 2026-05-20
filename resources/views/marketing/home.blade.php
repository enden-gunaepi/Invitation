<!DOCTYPE html>

<html class="scroll-smooth" lang="id">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>{{ config('app.name') }} | Perayaan Seumur Hidup</title>
    <meta name="description"
        content="Platform undangan digital estetik: template premium, RSVP real-time, dan operasional acara dalam satu sistem." />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Geist:wght@100..900&family=JetBrains+Mono:wght@100..800&display=swap"
        rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "surface-dim": "#dadada",
                        "on-secondary-container": "#636262",
                        "inverse-surface": "#2f3131",
                        "inverse-on-surface": "#f1f1f1",
                        "outline": "#7e7576",
                        "primary-fixed": "#e2e2e2",
                        "secondary-fixed-dim": "#c7c6c6",
                        "surface-bright": "#f9f9f9",
                        "surface-lowest": "#FFFFFF",
                        "surface-container": "#eeeeee",
                        "primary-container": "#1b1b1b",
                        "surface-variant": "#e2e2e2",
                        "error-container": "#ffdad6",
                        "surface-container-low": "#f3f3f3",
                        "tertiary": "#000000",
                        "editor-bg": "#111318",
                        "error": "#ba1a1a",
                        "surface-low": "#F3F3F3",
                        "on-primary-fixed": "#1b1b1b",
                        "wave-active": "#000000",
                        "tertiary-fixed": "#e1e0ff",
                        "on-tertiary-container": "#7073fe",
                        "primary-fixed-dim": "#c6c6c6",
                        "secondary": "#5e5e5e",
                        "surface-container-high": "#e8e8e8",
                        "outline-variant": "#cfc4c5",
                        "on-tertiary": "#ffffff",
                        "on-surface": "#1a1c1c",
                        "surface-tint": "#5e5e5e",
                        "surface-container-lowest": "#ffffff",
                        "on-secondary-fixed-variant": "#464747",
                        "on-primary": "#ffffff",
                        "outline-muted": "#E2E2E2",
                        "surface": "#ffffff",
                        "inverse-primary": "#c6c6c6",
                        "on-surface-variant": "#4c4546",
                        "on-error": "#ffffff",
                        "on-tertiary-fixed": "#07006c",
                        "on-secondary": "#ffffff",
                        "secondary-fixed": "#e4e2e2",
                        "on-error-container": "#93000a",
                        "on-primary-container": "#848484",
                        "on-primary-fixed-variant": "#474747",
                        "secondary-container": "#e1dfdf",
                        "tertiary-container": "#07006c",
                        "on-tertiary-fixed-variant": "#2f2ebe",
                        "primary": "#000000",
                        "background": "#ffffff",
                        "surface-container-highest": "#e2e2e2",
                        "tertiary-fixed-dim": "#c0c1ff",
                        "on-secondary-fixed": "#1b1c1c",
                        "on-background": "#1a1c1c"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "spacing": {
                        "gutter": "24px",
                        "container-max": "1280px",
                        "section-gap": "150px",
                        "internal-md": "24px",
                        "internal-sm": "12px",
                        "internal-lg": "32px"
                    },
                    "fontFamily": {
                        "display-lg": ["Geist"],
                        "label-caps": ["Geist"],
                        "body-md": ["Geist"],
                        "title-lg": ["Geist"],
                        "code": ["JetBrains Mono"],
                        "display-lg-mobile": ["Geist"],
                        "button": ["Geist"],
                        "headline-md": ["Geist"]
                    },
                    "fontSize": {
                        "display-lg": ["84px", {
                            "lineHeight": "0.95",
                            "letterSpacing": "-0.04em",
                            "fontWeight": "600"
                        }],
                        "label-caps": ["12px", {
                            "lineHeight": "1.0",
                            "letterSpacing": "0.05em",
                            "fontWeight": "600"
                        }],
                        "body-md": ["17px", {
                            "lineHeight": "1.6",
                            "letterSpacing": "0em",
                            "fontWeight": "400"
                        }],
                        "title-lg": ["28px", {
                            "lineHeight": "1.2",
                            "letterSpacing": "-0.01em",
                            "fontWeight": "500"
                        }],
                        "code": ["14px", {
                            "lineHeight": "1.5",
                            "fontWeight": "400"
                        }],
                        "display-lg-mobile": ["48px", {
                            "lineHeight": "1.1",
                            "letterSpacing": "-0.02em",
                            "fontWeight": "600"
                        }],
                        "button": ["14px", {
                            "lineHeight": "1.0",
                            "letterSpacing": "0.01em",
                            "fontWeight": "500"
                        }],
                        "headline-md": ["48px", {
                            "lineHeight": "1.1",
                            "letterSpacing": "-0.02em",
                            "fontWeight": "600"
                        }]
                    }
                },
            },
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        .text-balanced {
            text-wrap: balance;
        }

        .marquee-container {
            overflow: hidden;
            user-select: none;
            display: flex;
            gap: 4rem;
        }

        .marquee-content {
            flex-shrink: 0;
            display: flex;
            justify-content: space-around;
            min-width: 100%;
            gap: 4rem;
            animation: scroll 30s linear infinite;
        }

        @keyframes scroll {
            from {
                transform: translateX(0);
            }

            to {
                transform: translateX(-100%);
            }
        }
    </style>
</head>

<body class="bg-background text-on-background font-body-md selection:bg-primary selection:text-white">

    @php
        $mainAdmin = \App\Models\User::where('role', 'admin')->first();
        $companyLogo = $mainAdmin && $mainAdmin->company_logo ? Storage::url($mainAdmin->company_logo) : null;
        $companyName = $mainAdmin && $mainAdmin->company_name ? $mainAdmin->company_name : config('app.name');
    @endphp

    <!-- TopNavBar -->
    <nav class="fixed top-0 w-full z-50 bg-white/80 backdrop-blur-3xl border-b border-outline-variant/30">
        <div class="flex justify-between items-center px-gutter py-4 max-w-container-max mx-auto">
            <div class="flex items-center gap-3">
                @if ($companyLogo)
                    <img src="{{ $companyLogo }}" alt="{{ $companyName }}" class="h-7 w-auto">
                @endif
                <span class="text-[20px] font-semibold text-primary tracking-tight leading-none">Janji Suci Kita</span>
            </div>
            <div class="hidden md:flex gap-internal-lg">
                <a class="font-button text-button text-primary border-b-2 border-primary pb-1 transition-colors duration-300"
                    href="#hero">Beranda</a>
                <a class="font-button text-button text-on-secondary-container hover:text-primary transition-colors duration-300"
                    href="#paket">Paket</a>
                <a class="font-button text-button text-on-secondary-container hover:text-primary transition-colors duration-300"
                    href="#details">Detail</a>
                <a class="font-button text-button text-on-secondary-container hover:text-primary transition-colors duration-300"
                    href="#gallery">Galeri</a>
            </div>
            <a href="{{ route('login') }}"
                class="bg-primary text-on-primary font-button text-button px-6 py-2 rounded-full active:scale-95 transition-transform">
                Dashboard
            </a>
        </div>
    </nav>

    <main>
        <!-- Hero Section -->
        <section class="relative min-h-screen flex flex-col items-center justify-center pt-32 px-gutter overflow-hidden"
            id="hero">
            <div class="text-center max-w-[900px] z-10 mb-16">
                <h1
                    class="font-display-lg text-display-lg-mobile md:text-display-lg text-primary text-balanced mb-internal-md">
                    Perayaan Seumur Hidup Anda
                </h1>
                <p class="font-body-md text-body-md text-on-surface-variant max-w-[600px] mx-auto">
                    Platform undangan digital yang menghadirkan pengalaman premium — dari desain elegan, RSVP real-time,
                    hingga manajemen tamu yang sempurna.
                </p>
                <div class="flex flex-wrap justify-center gap-4 mt-internal-lg">
                    <a href="{{ route('register') }}"
                        class="bg-primary text-on-primary font-button text-button px-8 py-3 rounded-full active:scale-95 transition-transform inline-block">
                        Mulai Buat Undangan
                    </a>
                    <a href="{{ route('marketing.trial') }}"
                        class="border border-primary text-primary font-button text-button px-8 py-3 rounded-full hover:bg-primary hover:text-on-primary transition-colors inline-block">
                        Lihat Demo
                    </a>
                </div>
            </div>
            <div class="w-full max-w-container-max h-[600px] relative rounded-xl overflow-hidden shadow-sm">
                <img alt="Foto Undangan Digital Premium" class="w-full h-full object-cover"
                    src="https://images.unsplash.com/photo-1519741497674-611481863552?w=1400&auto=format&fit=crop&q=80" />
                <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
            </div>
        </section>

        <!-- Mitra / Partners Section -->
        <section class="py-24 bg-white border-y border-outline-variant/20 overflow-hidden">
            <div class="max-w-container-max mx-auto px-gutter mb-12 text-center">
                <span class="font-label-caps text-label-caps text-on-surface-variant tracking-[0.2em] uppercase">Mitra
                    Kami</span>
            </div>
            <div class="marquee-container">
                <div class="marquee-content flex items-center">
                    <img src="{{ asset('logo/logo satrbit-08.png') }}"
                        class="h-10 md:h-12 w-auto object-contain filter grayscale opacity-60 hover:grayscale-0 hover:opacity-100 transition-all duration-300"
                        alt="PT Starbit Creative Solutions">
                    <img src="{{ asset('logo/GNVISUAL (3).png') }}"
                        class="h-10 md:h-12 w-auto object-contain filter grayscale opacity-60 hover:grayscale-0 hover:opacity-100 transition-all duration-300"
                        alt="GNVisual">
                    <img src="{{ asset('logo/LOGO-05.png') }}"
                        class="h-10 md:h-12 w-auto object-contain filter grayscale opacity-60 hover:grayscale-0 hover:opacity-100 transition-all duration-300"
                        alt="Partner Logo 05">
                    <img src="{{ asset('logo/LOGO putih-03 (2).png') }}"
                        class="h-10 md:h-12 w-auto object-contain filter invert opacity-60 hover:opacity-100 transition-all duration-300"
                        alt="Partner Logo Putih">
                    <!-- Repeat for seamless layout -->
                    <img src="{{ asset('logo/logo satrbit-08.png') }}"
                        class="h-10 md:h-12 w-auto object-contain filter grayscale opacity-60 hover:grayscale-0 hover:opacity-100 transition-all duration-300"
                        alt="PT Starbit Creative Solutions">
                    <img src="{{ asset('logo/GNVISUAL (3).png') }}"
                        class="h-10 md:h-12 w-auto object-contain filter grayscale opacity-60 hover:grayscale-0 hover:opacity-100 transition-all duration-300"
                        alt="GNVisual">
                    <img src="{{ asset('logo/LOGO-05.png') }}"
                        class="h-10 md:h-12 w-auto object-contain filter grayscale opacity-60 hover:grayscale-0 hover:opacity-100 transition-all duration-300"
                        alt="Partner Logo 05">
                    <img src="{{ asset('logo/LOGO putih-03 (2).png') }}"
                        class="h-10 md:h-12 w-auto object-contain filter invert opacity-60 hover:opacity-100 transition-all duration-300"
                        alt="Partner Logo Putih">
                </div>
                <!-- Duplicate for infinite effect -->
                <div aria-hidden="true" class="marquee-content flex items-center">
                    <img src="{{ asset('logo/logo satrbit-08.png') }}"
                        class="h-10 md:h-12 w-auto object-contain filter grayscale opacity-60 hover:grayscale-0 hover:opacity-100 transition-all duration-300"
                        alt="PT Starbit Creative Solutions">
                    <img src="{{ asset('logo/GNVISUAL (3).png') }}"
                        class="h-10 md:h-12 w-auto object-contain filter grayscale opacity-60 hover:grayscale-0 hover:opacity-100 transition-all duration-300"
                        alt="GNVisual">
                    <img src="{{ asset('logo/LOGO-05.png') }}"
                        class="h-10 md:h-12 w-auto object-contain filter grayscale opacity-60 hover:grayscale-0 hover:opacity-100 transition-all duration-300"
                        alt="Partner Logo 05">
                    <img src="{{ asset('logo/LOGO putih-03 (2).png') }}"
                        class="h-10 md:h-12 w-auto object-contain filter invert opacity-60 hover:opacity-100 transition-all duration-300"
                        alt="Partner Logo Putih">
                    <!-- Repeat for seamless layout -->
                    <img src="{{ asset('logo/logo satrbit-08.png') }}"
                        class="h-10 md:h-12 w-auto object-contain filter grayscale opacity-60 hover:grayscale-0 hover:opacity-100 transition-all duration-300"
                        alt="PT Starbit Creative Solutions">
                    <img src="{{ asset('logo/GNVISUAL (3).png') }}"
                        class="h-10 md:h-12 w-auto object-contain filter grayscale opacity-60 hover:grayscale-0 hover:opacity-100 transition-all duration-300"
                        alt="GNVisual">
                    <img src="{{ asset('logo/LOGO-05.png') }}"
                        class="h-10 md:h-12 w-auto object-contain filter grayscale opacity-60 hover:grayscale-0 hover:opacity-100 transition-all duration-300"
                        alt="Partner Logo 05">
                    <img src="{{ asset('logo/LOGO putih-03 (2).png') }}"
                        class="h-10 md:h-12 w-auto object-contain filter invert opacity-60 hover:opacity-100 transition-all duration-300"
                        alt="Partner Logo Putih">
                </div>
            </div>
        </section>

        <!-- Paket Langganan Section -->
        <section class="py-section-gap px-gutter max-w-container-max mx-auto" id="paket">
            <div class="text-center mb-16">
                <h2 class="font-headline-md text-headline-md text-primary mb-4">Paket Langganan</h2>
                <p class="font-body-md text-on-surface-variant max-w-xl mx-auto">Pilih paket terbaik untuk mengabadikan
                    momen suci Anda dengan undangan digital yang elegan.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @forelse($packages as $index => $package)
                    @php
                        $isPopular = $package->is_recommended ?? false;
                        $isMiddle = $index === 1 || ($packages->count() <= 1 && $index === 0);
                    @endphp
                    @if ($isPopular)
                        <!-- Popular / Featured Package -->
                        <div
                            class="bg-primary text-on-primary p-internal-lg rounded-xl flex flex-col shadow-2xl relative overflow-hidden scale-105">
                            <div
                                class="absolute top-0 right-0 bg-white/20 px-4 py-1 text-[10px] font-bold uppercase tracking-widest text-white">
                                Populer</div>
                            <h3 class="font-title-lg text-title-lg mb-2">{{ $package->name }}</h3>
                            <div class="mb-6">
                                <span
                                    class="text-4xl font-bold">Rp{{ number_format((float) $package->price, 0, ',', '.') }}</span>
                                <span
                                    class="text-on-primary/70 text-sm">/{{ ($package->billing_type ?? 'one_time') === 'subscription' ? $package->billing_cycle ?? 'bulan' : 'sekali bayar' }}</span>
                            </div>
                            <ul class="space-y-4 mb-10 flex-grow">
                                <li class="flex items-center gap-2 text-body-md"><span
                                        class="material-symbols-outlined text-sm">check_circle</span> Max
                                    {{ number_format((int) $package->max_guests) }} tamu</li>
                                <li class="flex items-center gap-2 text-body-md"><span
                                        class="material-symbols-outlined text-sm">check_circle</span>
                                    {{ number_format((int) $package->max_photos) }} foto galeri</li>
                                <li class="flex items-center gap-2 text-body-md"><span
                                        class="material-symbols-outlined text-sm">check_circle</span>
                                    {{ number_format((int) ($package->max_invitations ?? 1)) }} link aktif</li>
                                @foreach (array_slice($package->features ?? [], 0, 4) as $feature)
                                    <li class="flex items-center gap-2 text-body-md"><span
                                            class="material-symbols-outlined text-sm">check_circle</span>
                                        {{ $feature }}</li>
                                @endforeach
                            </ul>
                            <a href="{{ route('register') }}"
                                class="w-full bg-white text-primary font-button py-3 rounded-full active:scale-95 transition-transform text-center block">Pilih
                                Paket</a>
                        </div>
                    @else
                        <!-- Standard Package -->
                        <div
                            class="bg-white border border-outline-variant p-internal-lg rounded-xl flex flex-col hover:shadow-xl transition-shadow duration-300">
                            <h3 class="font-title-lg text-title-lg mb-2">{{ $package->name }}</h3>
                            <div class="mb-6">
                                <span
                                    class="text-4xl font-bold">Rp{{ number_format((float) $package->price, 0, ',', '.') }}</span>
                                <span
                                    class="text-on-surface-variant text-sm">/{{ ($package->billing_type ?? 'one_time') === 'subscription' ? $package->billing_cycle ?? 'bulan' : 'sekali bayar' }}</span>
                            </div>
                            <ul class="space-y-4 mb-10 flex-grow">
                                <li class="flex items-center gap-2 text-body-md text-on-surface-variant"><span
                                        class="material-symbols-outlined text-primary text-sm">check_circle</span> Max
                                    {{ number_format((int) $package->max_guests) }} tamu</li>
                                <li class="flex items-center gap-2 text-body-md text-on-surface-variant"><span
                                        class="material-symbols-outlined text-primary text-sm">check_circle</span>
                                    {{ number_format((int) $package->max_photos) }} foto galeri</li>
                                <li class="flex items-center gap-2 text-body-md text-on-surface-variant"><span
                                        class="material-symbols-outlined text-primary text-sm">check_circle</span>
                                    {{ number_format((int) ($package->max_invitations ?? 1)) }} link aktif</li>
                                @foreach (array_slice($package->features ?? [], 0, 4) as $feature)
                                    <li class="flex items-center gap-2 text-body-md text-on-surface-variant"><span
                                            class="material-symbols-outlined text-primary text-sm">check_circle</span>
                                        {{ $feature }}</li>
                                @endforeach
                            </ul>
                            <a href="{{ route('register') }}"
                                class="w-full border border-primary text-primary font-button py-3 rounded-full hover:bg-primary hover:text-on-primary transition-colors text-center block">Pilih
                                Paket</a>
                        </div>
                    @endif
                @empty
                    <!-- Fallback static packages if no data -->
                    <!-- Silver -->
                    <div
                        class="bg-white border border-outline-variant p-internal-lg rounded-xl flex flex-col hover:shadow-xl transition-shadow duration-300">
                        <h3 class="font-title-lg text-title-lg mb-2">Silver</h3>
                        <div class="mb-6">
                            <span class="text-4xl font-bold">Rp 150k</span>
                            <span class="text-on-surface-variant text-sm">/pernikahan</span>
                        </div>
                        <ul class="space-y-4 mb-10 flex-grow">
                            <li class="flex items-center gap-2 text-body-md text-on-surface-variant"><span
                                    class="material-symbols-outlined text-primary text-sm">check_circle</span> Undangan
                                Digital Standar</li>
                            <li class="flex items-center gap-2 text-body-md text-on-surface-variant"><span
                                    class="material-symbols-outlined text-primary text-sm">check_circle</span> Fitur
                                RSVP Dasar</li>
                            <li class="flex items-center gap-2 text-body-md text-on-surface-variant"><span
                                    class="material-symbols-outlined text-primary text-sm">check_circle</span> Galeri
                                10 Foto</li>
                            <li class="flex items-center gap-2 text-body-md text-on-surface-variant"><span
                                    class="material-symbols-outlined text-primary text-sm">check_circle</span> Masa
                                Aktif 1 Bulan</li>
                        </ul>
                        <a href="{{ route('register') }}"
                            class="w-full border border-primary text-primary font-button py-3 rounded-full hover:bg-primary hover:text-on-primary transition-colors text-center block">Pilih
                            Paket</a>
                    </div>
                    <!-- Gold -->
                    <div
                        class="bg-primary text-on-primary p-internal-lg rounded-xl flex flex-col shadow-2xl relative overflow-hidden scale-105">
                        <div
                            class="absolute top-0 right-0 bg-white/20 px-4 py-1 text-[10px] font-bold uppercase tracking-widest text-white">
                            Populer</div>
                        <h3 class="font-title-lg text-title-lg mb-2">Gold</h3>
                        <div class="mb-6">
                            <span class="text-4xl font-bold">Rp 350k</span>
                            <span class="text-on-primary/70 text-sm">/pernikahan</span>
                        </div>
                        <ul class="space-y-4 mb-10 flex-grow">
                            <li class="flex items-center gap-2 text-body-md"><span
                                    class="material-symbols-outlined text-sm">check_circle</span> Desain Premium &amp;
                                Eksklusif</li>
                            <li class="flex items-center gap-2 text-body-md"><span
                                    class="material-symbols-outlined text-sm">check_circle</span> RSVP &amp; Manajemen
                                Tamu</li>
                            <li class="flex items-center gap-2 text-body-md"><span
                                    class="material-symbols-outlined text-sm">check_circle</span> Galeri Foto &amp;
                                Video Tanpa Batas</li>
                            <li class="flex items-center gap-2 text-body-md"><span
                                    class="material-symbols-outlined text-sm">check_circle</span> Integrasi Musik &amp;
                                Maps</li>
                            <li class="flex items-center gap-2 text-body-md"><span
                                    class="material-symbols-outlined text-sm">check_circle</span> Masa Aktif 1 Tahun
                            </li>
                        </ul>
                        <a href="{{ route('register') }}"
                            class="w-full bg-white text-primary font-button py-3 rounded-full active:scale-95 transition-transform text-center block">Pilih
                            Paket</a>
                    </div>
                    <!-- Platinum -->
                    <div
                        class="bg-white border border-outline-variant p-internal-lg rounded-xl flex flex-col hover:shadow-xl transition-shadow duration-300">
                        <h3 class="font-title-lg text-title-lg mb-2">Platinum</h3>
                        <div class="mb-6">
                            <span class="text-4xl font-bold">Rp 750k</span>
                            <span class="text-on-surface-variant text-sm">/pernikahan</span>
                        </div>
                        <ul class="space-y-4 mb-10 flex-grow">
                            <li class="flex items-center gap-2 text-body-md text-on-surface-variant"><span
                                    class="material-symbols-outlined text-primary text-sm">check_circle</span> Semua
                                Fitur Paket Gold</li>
                            <li class="flex items-center gap-2 text-body-md text-on-surface-variant"><span
                                    class="material-symbols-outlined text-primary text-sm">check_circle</span> Custom
                                Domain</li>
                            <li class="flex items-center gap-2 text-body-md text-on-surface-variant"><span
                                    class="material-symbols-outlined text-primary text-sm">check_circle</span> Buku
                                Tamu Digital QR Code</li>
                            <li class="flex items-center gap-2 text-body-md text-on-surface-variant"><span
                                    class="material-symbols-outlined text-primary text-sm">check_circle</span> Masa
                                Aktif Selamanya</li>
                        </ul>
                        <a href="{{ route('register') }}"
                            class="w-full border border-primary text-primary font-button py-3 rounded-full hover:bg-primary hover:text-on-primary transition-colors text-center block">Pilih
                            Paket</a>
                    </div>
                @endforelse
            </div>
        </section>

        <!-- Feature Section: Tentang Platform -->
        <section class="py-section-gap px-gutter max-w-container-max mx-auto" id="story">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-internal-lg items-center">
                <div class="md:col-span-7 rounded-xl overflow-hidden h-[700px]">
                    <img alt="Platform Undangan Digital" class="w-full h-full object-cover"
                        src="https://images.unsplash.com/photo-1606216794074-735e91aa2c92?w=900&auto=format&fit=crop&q=80" />
                </div>
                <div class="md:col-span-5 flex flex-col justify-center">
                    <span class="font-label-caps text-label-caps text-primary mb-4 tracking-widest">Platform
                        Kami</span>
                    <h2 class="font-headline-md text-headline-md text-primary mb-internal-md">Dirancang untuk Momen
                        Berharga</h2>
                    <div class="space-y-6 font-body-md text-body-md text-on-surface-variant leading-relaxed">
                        <p>{{ $companyName }} adalah platform undangan digital yang menggabungkan estetika modern
                            dengan teknologi terkini. Setiap detail dirancang untuk memastikan momen istimewa Anda
                            terabadikan dengan sempurna.</p>
                        <p>Dari template eksklusif, manajemen tamu real-time, hingga integrasi musik dan peta, semua
                            tersedia dalam satu sistem yang elegan dan mudah digunakan.</p>
                    </div>
                    <div class="flex gap-6 mt-8">
                        <div>
                            <div class="text-3xl font-bold text-primary">{{ $templates->count() }}+</div>
                            <div class="text-sm text-on-surface-variant mt-1">Template Premium</div>
                        </div>
                        <div>
                            <div class="text-3xl font-bold text-primary">{{ $packages->count() }}</div>
                            <div class="text-sm text-on-surface-variant mt-1">Pilihan Paket</div>
                        </div>
                        <div>
                            <div class="text-3xl font-bold text-primary">100%</div>
                            <div class="text-sm text-on-surface-variant mt-1">Digital & Elegan</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Event Details / Template Showcase Section -->
        <section class="py-section-gap bg-surface-container-low" id="details">
            <div class="max-w-container-max mx-auto px-gutter">
                <div class="text-center mb-16">
                    <h2 class="font-headline-md text-headline-md text-primary mb-4">Fitur Unggulan</h2>
                    <p class="font-body-md text-body-md text-on-surface-variant">Semua yang Anda butuhkan untuk
                        undangan digital yang sempurna.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-internal-md">
                    <!-- Feature 1 -->
                    <div
                        class="bg-white p-internal-lg rounded-xl border border-outline-variant transition-all hover:shadow-md">
                        <div class="w-12 h-12 bg-surface-container rounded-lg flex items-center justify-center mb-6">
                            <span class="material-symbols-outlined text-primary">devices</span>
                        </div>
                        <h3 class="font-title-lg text-title-lg mb-2">Template Premium</h3>
                        <p class="font-body-md text-on-surface-variant">Pilihan desain eksklusif yang responsif di
                            semua perangkat. Tampilan elegan yang mencerminkan keunikan cinta Anda.</p>
                    </div>
                    <!-- Feature 2 -->
                    <div
                        class="bg-white p-internal-lg rounded-xl border border-outline-variant transition-all hover:shadow-md">
                        <div class="w-12 h-12 bg-surface-container rounded-lg flex items-center justify-center mb-6">
                            <span class="material-symbols-outlined text-primary">group</span>
                        </div>
                        <h3 class="font-title-lg text-title-lg mb-2">Manajemen Tamu</h3>
                        <p class="font-body-md text-on-surface-variant">Kelola daftar tamu, konfirmasi RSVP real-time,
                            dan lacak kehadiran dengan mudah dari dashboard Anda.</p>
                    </div>
                    <!-- Feature 3 -->
                    <div
                        class="bg-white p-internal-lg rounded-xl border border-outline-variant transition-all hover:shadow-md">
                        <div class="w-12 h-12 bg-surface-container rounded-lg flex items-center justify-center mb-6">
                            <span class="material-symbols-outlined text-primary">music_note</span>
                        </div>
                        <h3 class="font-title-lg text-title-lg mb-2">Musik &amp; Maps</h3>
                        <p class="font-body-md text-on-surface-variant">Tambahkan musik latar romantis dan integrasi
                            Google Maps agar tamu mudah menemukan lokasi acara Anda.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Design Library / Templates Showcase -->
        @if ($templates->count() > 0)
            <section class="py-section-gap px-gutter max-w-container-max mx-auto" id="templates">
                <div class="text-center mb-16">
                    <h2 class="font-headline-md text-headline-md text-primary mb-4">Koleksi Template</h2>
                    <p class="font-body-md text-on-surface-variant max-w-xl mx-auto">Template minimalis, elegan, dan
                        sepenuhnya responsif yang dirancang untuk pengalaman membaca terbaik.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    @foreach ($templates->take(6) as $template)
                        <div
                            class="bg-white border border-outline-variant rounded-xl overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                            <div class="aspect-[16/10] bg-surface-container overflow-hidden">
                                @if ($template->thumbnail)
                                    <img src="{{ asset('storage/' . $template->thumbnail) }}"
                                        alt="{{ $template->name }}"
                                        class="w-full h-full object-cover transition-transform duration-700 hover:scale-105">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <span class="material-symbols-outlined text-5xl text-outline/30">image</span>
                                    </div>
                                @endif
                            </div>
                            <div class="p-5 flex justify-between items-center">
                                <h3 class="font-title-lg text-[18px] font-medium">{{ $template->name }}</h3>
                                <span
                                    class="text-xs px-3 py-1 bg-surface-container rounded-full text-on-surface-variant font-medium">{{ ucfirst($template->category) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- Strong CTA Section -->
        <section class="py-32 px-gutter bg-primary text-center">
            <div class="max-w-3xl mx-auto">
                <h2 class="font-display-lg-mobile md:text-headline-md text-on-primary mb-8 leading-tight">Abadikan
                    Momen Indah Anda Selamanya</h2>
                <p class="font-body-md text-on-primary/80 mb-12">Rancang undangan pernikahan digital yang mewakili
                    keunikan cinta Anda bersama {{ $companyName }}.</p>
                <a href="{{ route('register') }}"
                    class="bg-white text-primary font-button px-12 py-5 rounded-full text-lg hover:bg-surface-container transition-colors active:scale-95 duration-200 inline-block">
                    Mulai Buat Undangan
                </a>
            </div>
        </section>

        <!-- Photo Gallery -->
        <section class="py-section-gap px-gutter max-w-container-max mx-auto" id="gallery">
            <h2 class="font-headline-md text-headline-md text-primary mb-16 text-center">Momen Berharga</h2>
            <div class="grid grid-cols-12 gap-6 md:auto-rows-[300px]">
                <div class="col-span-12 md:col-span-8 md:row-span-2 rounded-xl overflow-hidden">
                    <img alt="Gallery Image 1"
                        class="w-full h-full object-cover transition-transform duration-700 hover:scale-105"
                        src="https://images.unsplash.com/photo-1519225421980-715cb0215aed?w=900&auto=format&fit=crop&q=80" />
                </div>
                <div class="col-span-12 md:col-span-4 md:row-span-1 rounded-xl overflow-hidden">
                    <img alt="Gallery Image 2"
                        class="w-full h-full object-cover transition-transform duration-700 hover:scale-105"
                        src="https://images.unsplash.com/photo-1511285560929-80b456503681?w=600&auto=format&fit=crop&q=80" />
                </div>
                <div class="col-span-12 md:col-span-4 md:row-span-1 rounded-xl overflow-hidden">
                    <img alt="Gallery Image 3"
                        class="w-full h-full object-cover transition-transform duration-700 hover:scale-105"
                        src="https://images.unsplash.com/photo-1606800052052-a08af7148866?w=600&auto=format&fit=crop&q=80" />
                </div>
                <div class="col-span-12 md:col-span-4 md:row-span-1 rounded-xl overflow-hidden">
                    <img alt="Gallery Image 4"
                        class="w-full h-full object-cover transition-transform duration-700 hover:scale-105"
                        src="https://images.unsplash.com/photo-1465495976277-4387d4b0b4c6?w=600&auto=format&fit=crop&q=80" />
                </div>
                <div class="col-span-12 md:col-span-8 md:row-span-1 rounded-xl overflow-hidden">
                    <img alt="Gallery Image 5"
                        class="w-full h-full object-cover transition-transform duration-700 hover:scale-105"
                        src="https://images.unsplash.com/photo-1478146059778-26028b07395a?w=900&auto=format&fit=crop&q=80" />
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-outline-variant/30 w-full py-section-gap">
        <div class="max-w-container-max mx-auto px-gutter grid grid-cols-1 md:grid-cols-4 gap-12 mb-16">
            <div class="col-span-1 md:col-span-2">
                <div class="font-display-lg-mobile text-display-lg-mobile text-primary leading-none mb-6">
                    {{ $companyName }}
                </div>
                <p class="font-body-md text-on-surface-variant max-w-sm mb-4">Membantu pasangan di seluruh Indonesia
                    menciptakan undangan digital yang elegan, personal, dan tak terlupakan.</p>
                <p class="text-sm text-on-surface-variant/80 max-w-sm leading-relaxed">
                    Aplikasi <strong>janjisucikita.com</strong> ini dibuat oleh <strong>1112Project</strong> dari
                    <strong>PT Starbit Creative Solutions</strong>, dan didesain tampilannya oleh
                    <strong>GNVisual</strong>.
                </p>
            </div>
            <div>
                <h4 class="font-label-caps text-label-caps mb-6 uppercase tracking-wider">Platform</h4>
                <ul class="space-y-4">
                    <li><a class="font-body-md text-on-surface-variant hover:text-primary transition-all duration-200"
                            href="#paket">Paket</a></li>
                    <li><a class="font-body-md text-on-surface-variant hover:text-primary transition-all duration-200"
                            href="#templates">Template</a></li>
                    <li><a class="font-body-md text-on-surface-variant hover:text-primary transition-all duration-200"
                            href="{{ route('marketing.trial') }}">Demo Trial</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-label-caps text-label-caps mb-6 uppercase tracking-wider">Akun</h4>
                <ul class="space-y-4">
                    <li><a class="font-body-md text-on-surface-variant hover:text-primary transition-all duration-200"
                            href="{{ route('login') }}">Masuk</a></li>
                    <li><a class="font-body-md text-on-surface-variant hover:text-primary transition-all duration-200"
                            href="{{ route('register') }}">Daftar</a></li>
                </ul>
            </div>
        </div>
        <div
            class="flex flex-col items-center gap-internal-lg text-center px-gutter pt-8 border-t border-outline-variant/30">
            <div class="flex flex-wrap justify-center gap-6">
                <a class="font-body-md text-on-surface-variant hover:text-primary transition-all duration-200 underline underline-offset-4"
                    href="https://www.instagram.com/1112.project" target="_blank"
                    rel="noopener noreferrer">Instagram</a>
                <a class="font-body-md text-on-surface-variant hover:text-primary transition-all duration-200 underline underline-offset-4"
                    href="https://wa.me/6282119904113" target="_blank" rel="noopener noreferrer">WhatsApp</a>
                <a class="font-body-md text-on-surface-variant hover:text-primary transition-all duration-200 underline underline-offset-4"
                    href="#">Email</a>
            </div>
            <div class="font-body-md text-on-surface-variant/60 text-sm">
                &copy; {{ date('Y') }} {{ $companyName }}. Semua hak dilindungi.
            </div>
        </div>
    </footer>

    <script>
        // Smooth scroll interaction for navigation
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                const target = document.querySelector(targetId);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Intersection Observer for fade-in effects
        const observerOptions = {
            threshold: 0.1
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('opacity-100', 'translate-y-0');
                    entry.target.classList.remove('opacity-0', 'translate-y-10');
                }
            });
        }, observerOptions);

        document.querySelectorAll('section > div').forEach(el => {
            el.classList.add('transition-all', 'duration-1000', 'opacity-0', 'translate-y-10');
            observer.observe(el);
        });

        // Active nav link on scroll
        const sections = document.querySelectorAll('section[id]');
        const navLinks = document.querySelectorAll('nav a[href^="#"]');

        window.addEventListener('scroll', () => {
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                if (pageYOffset >= sectionTop - 100) {
                    current = section.getAttribute('id');
                }
            });
            navLinks.forEach(link => {
                link.classList.remove('border-b-2', 'border-primary', 'text-primary');
                link.classList.add('text-on-secondary-container');
                if (link.getAttribute('href') === `#${current}`) {
                    link.classList.add('border-b-2', 'border-primary', 'text-primary');
                    link.classList.remove('text-on-secondary-container');
                }
            });
        });
    </script>
</body>

</html>
