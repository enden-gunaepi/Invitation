<!DOCTYPE html>

<html class="scroll-smooth" lang="id">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>{{ $brandAppName ?? config('app.name') }} | Perayaan Seumur Hidup</title>
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

        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
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

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }
    </style>
</head>

<body class="bg-background text-on-background font-body-md selection:bg-primary selection:text-white">

    <!-- TopNavBar -->
    <nav class="fixed top-0 w-full z-50 bg-white/80 backdrop-blur-3xl border-b border-outline-variant/30">
        <div
            class="absolute bottom-0 left-0 w-full h-[2px] bg-gradient-to-r from-transparent via-pink-500 to-transparent opacity-70">
        </div>
        <div class="flex justify-between items-center px-gutter py-4 max-w-container-max mx-auto">
            <div class="flex items-center gap-3">
                @if ($brandLogoUrl)
                    <img src="{{ $brandLogoUrl }}" alt="{{ $brandName }}" class="h-7 w-auto">
                @endif
                <span class="text-[20px] font-semibold text-primary tracking-tight leading-none">{{ $brandName }}</span>
            </div>
            <div class="hidden md:flex gap-internal-lg">
                <a class="font-button text-button text-on-secondary-container hover:text-pink-600 transition-colors duration-300"
                    href="{{ route('marketing.home') }}#hero">Beranda</a>
                <a class="font-button text-button text-on-secondary-container hover:text-pink-600 transition-colors duration-300"
                    href="{{ route('marketing.home') }}#paket">Paket</a>
                <a class="font-button text-button text-on-secondary-container hover:text-pink-600 transition-colors duration-300"
                    href="{{ route('marketing.home') }}#details">Detail</a>
                <a class="font-button text-button text-on-secondary-container hover:text-pink-600 transition-colors duration-300"
                    href="{{ route('marketing.home') }}#gallery">Galeri</a>
            </div>
            <a href="{{ route('login') }}"
                class="bg-primary hover:bg-pink-600 text-on-primary font-button text-button px-6 py-2 rounded-full active:scale-95 transition-all">
                Login
            </a>
        </div>
    </nav>

        <!-- Header Banner Section -->
        <section class="pt-32 pb-10 px-gutter max-w-container-max mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Banner 1: Undangan Pernikahan -->
                <div class="relative w-full h-[250px] md:h-[300px] rounded-2xl overflow-hidden group">
                    <img src="https://images.unsplash.com/photo-1519741497674-611481863552?w=800&auto=format&fit=crop&q=80" alt="Undangan Pernikahan" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                    <div class="absolute inset-0 bg-black/20"></div>
                    <div class="absolute inset-0 flex flex-col justify-center items-end p-8 text-right">
                        <h2 class="font-display-lg-mobile md:text-5xl text-white font-bold leading-tight drop-shadow-md">UNDANGAN<br><span class="text-pink-300">PERNIKAHAN</span></h2>
                    </div>
                </div>
                <!-- Banner 2: Undangan Lainnya -->
                <div class="relative w-full h-[250px] md:h-[300px] rounded-2xl overflow-hidden group">
                    <img src="https://images.unsplash.com/photo-1511285560929-80b456503681?w=800&auto=format&fit=crop&q=80" alt="Undangan Lainnya" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105 filter grayscale">
                    <div class="absolute inset-0 bg-black/40"></div>
                    <div class="absolute inset-0 flex flex-col justify-center items-start p-8">
                        <h2 class="font-display-lg-mobile md:text-5xl text-white font-bold leading-tight drop-shadow-md">UNDANGAN<br>LAINNYA</h2>
                    </div>
                </div>
            </div>
        </section>

        <!-- Filters Section -->
        <section class="py-6 px-gutter max-w-container-max mx-auto border-b border-outline-variant/30 mb-12">
            <div class="flex flex-wrap justify-center gap-4 filter-buttons">
                @php
                    $categories = ['Semua' => $templates->count()];
                    foreach($templates->groupBy('category') as $key => $items) {
                        $categories[ucfirst($key)] = $items->count();
                    }
                @endphp
                
                @foreach($categories as $name => $count)
                    <button data-filter="{{ strtolower($name) }}" class="filter-btn group flex items-center gap-2 px-6 py-2 rounded-full border border-pink-200 bg-white hover:bg-pink-50 transition-all text-sm font-medium {{ $name === 'Semua' ? 'active-filter bg-pink-100 border-pink-300 text-pink-700' : 'text-on-surface-variant' }}">
                        {{ $name }}
                        <span class="bg-surface-container text-xs px-2 py-0.5 rounded-full text-on-surface-variant group-[.active-filter]:bg-pink-200 group-[.active-filter]:text-pink-800">{{ $count }}</span>
                    </button>
                @endforeach
            </div>
        </section>

        <!-- Templates Container -->
        <section class="pb-32 px-gutter max-w-container-max mx-auto min-h-[50vh]">
            <!-- Carousel for Featured/Popular templates -->
            <div id="templates-carousel" class="mb-16">
                <div class="flex overflow-x-auto snap-x snap-mandatory gap-6 pb-8 scrollbar-hide">
                    @foreach ($templates->take(5) as $template)
                        <div class="flex-none w-[85vw] md:w-[400px] snap-center bg-white border border-outline-variant rounded-xl overflow-hidden hover:shadow-xl transition-all duration-300">
                            <!-- Template Card Content (same as home.blade.php) -->
                            <div class="aspect-[16/10] bg-surface-container overflow-hidden relative group">
                                @if ($template->thumbnail)
                                    <img src="{{ asset('storage/' . $template->thumbnail) }}" alt="{{ $template->name }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <span class="material-symbols-outlined text-5xl text-outline/30">image</span>
                                    </div>
                                @endif
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity duration-300 backdrop-blur-[2px]">
                                    <a href="{{ route('templates.demo', $template) }}" target="_blank" class="bg-white text-primary px-6 py-2 rounded-full font-button text-sm mr-3 hover:bg-pink-50 transition-colors transform translate-y-4 group-hover:translate-y-0 duration-300">Demo</a>
                                    <a href="{{ route('register') }}" class="bg-pink-500 text-white px-6 py-2 rounded-full font-button text-sm hover:bg-pink-600 transition-colors transform translate-y-4 group-hover:translate-y-0 duration-300">Gunakan</a>
                                </div>
                            </div>
                            <div class="p-5 flex justify-between items-center bg-white">
                                <h3 class="font-title-lg text-[18px] font-medium">{{ $template->name }}</h3>
                                <span class="text-xs px-3 py-1 bg-pink-50 text-pink-600 rounded-full font-medium border border-pink-100">{{ ucfirst($template->category) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Grid for All Other Templates -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8" id="templates-grid">
                @foreach ($templates as $index => $template)
                    <div class="template-item {{ strtolower($template->category) }} {{ $index < 5 ? 'is-carousel-duplicate' : '' }} bg-white border border-outline-variant rounded-xl overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1 {{ $index < 5 ? 'hidden' : '' }}">
                        <!-- Template Card Content -->
                        <div class="aspect-[16/10] bg-surface-container overflow-hidden relative group">
                            @if ($template->thumbnail)
                                <img src="{{ asset('storage/' . $template->thumbnail) }}" alt="{{ $template->name }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <span class="material-symbols-outlined text-5xl text-outline/30">image</span>
                                </div>
                            @endif
                            <!-- Overlay Buttons -->
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity duration-300 backdrop-blur-[2px]">
                                <a href="{{ route('templates.demo', $template) }}" target="_blank" class="bg-white text-primary px-6 py-2 rounded-full font-button text-sm mr-3 hover:bg-pink-50 transition-colors transform translate-y-4 group-hover:translate-y-0 duration-300">Demo</a>
                                <a href="{{ route('register') }}" class="bg-pink-500 text-white px-6 py-2 rounded-full font-button text-sm hover:bg-pink-600 transition-colors transform translate-y-4 group-hover:translate-y-0 duration-300">Gunakan</a>
                            </div>
                            <!-- Category Badge positioned like the image -->
                            <div class="absolute bottom-3 left-3">
                                <span class="text-[10px] px-3 py-1 bg-yellow-400 text-yellow-900 rounded-full font-bold uppercase tracking-widest shadow-sm">{{ ucfirst($template->category) }}</span>
                            </div>
                            <!-- Top center label -->
                            <div class="absolute top-3 left-1/2 -translate-x-1/2">
                                <span class="text-xs px-4 py-1.5 bg-white/90 backdrop-blur-sm text-primary rounded-full font-semibold shadow-sm">{{ $template->name }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div id="no-results" class="hidden text-center py-20">
                <span class="material-symbols-outlined text-6xl text-outline-variant mb-4">search_off</span>
                <h3 class="text-xl font-medium text-primary">Tidak ada template</h3>
                <p class="text-on-surface-variant mt-2">Belum ada template untuk kategori ini.</p>
            </div>
        </section>

        <!-- Javascript for filtering -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const filterBtns = document.querySelectorAll('.filter-btn');
                const templateItems = document.querySelectorAll('.template-item');
                const noResults = document.getElementById('no-results');
                const carouselSection = document.getElementById('templates-carousel');
                
                filterBtns.forEach(btn => {
                    btn.addEventListener('click', () => {
                        // Update active state
                        filterBtns.forEach(b => {
                            b.classList.remove('active-filter', 'bg-pink-100', 'border-pink-300', 'text-pink-700');
                            b.classList.add('text-on-surface-variant');
                        });
                        
                        btn.classList.add('active-filter', 'bg-pink-100', 'border-pink-300', 'text-pink-700');
                        btn.classList.remove('text-on-surface-variant');
                        
                        const filter = btn.getAttribute('data-filter');
                        let visibleCount = 0;
                        
                        if (filter === 'semua') {
                            carouselSection.style.display = 'block';
                            templateItems.forEach(item => {
                                if (item.classList.contains('is-carousel-duplicate')) {
                                    item.classList.add('hidden');
                                } else {
                                    item.classList.remove('hidden');
                                    visibleCount++;
                                }
                            });
                        } else {
                            carouselSection.style.display = 'none';
                            templateItems.forEach(item => {
                                if (item.classList.contains(filter)) {
                                    item.classList.remove('hidden');
                                    visibleCount++;
                                } else {
                                    item.classList.add('hidden');
                                }
                            });
                        }
                        
                        if (visibleCount === 0 && filter !== 'semua') {
                            noResults.classList.remove('hidden');
                        } else {
                            noResults.classList.add('hidden');
                        }
                    });
                });
            });
        </script>

    <!-- Footer -->
    <footer class="bg-white border-t border-outline-variant/30 w-full py-section-gap">
        <div class="max-w-container-max mx-auto px-gutter grid grid-cols-1 md:grid-cols-4 gap-12 mb-16">
            <div class="col-span-1 md:col-span-2">
                <div class="font-display-lg-mobile text-display-lg-mobile text-primary leading-none mb-6">
                    {{ $brandName }}
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
                    <li><a class="font-body-md text-on-surface-variant hover:text-pink-600 transition-all duration-200"
                            href="{{ route('marketing.home') }}#paket">Paket</a></li>
                    <li><a class="font-body-md text-on-surface-variant hover:text-pink-600 transition-all duration-200"
                            href="{{ route('marketing.templates') }}">Template</a></li>
                    <li><a class="font-body-md text-on-surface-variant hover:text-pink-600 transition-all duration-200"
                            href="{{ route('marketing.templates') }}">Demo Trial</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-label-caps text-label-caps mb-6 uppercase tracking-wider">Akun</h4>
                <ul class="space-y-4">
                    <li><a class="font-body-md text-on-surface-variant hover:text-pink-600 transition-all duration-200"
                            href="{{ route('login') }}">Masuk</a></li>
                    <li><a class="font-body-md text-on-surface-variant hover:text-pink-600 transition-all duration-200"
                            href="{{ route('register') }}">Daftar</a></li>
                </ul>
            </div>
        </div>
        <div
            class="flex flex-col items-center gap-internal-lg text-center px-gutter pt-8 border-t border-outline-variant/30">
            <div class="flex flex-col items-center mb-2">
                <img src="{{ asset('assets/maskot/hubungikami.png') }}" alt="Mascot Hubungi Kami"
                    class="h-16 w-auto mb-2 drop-shadow-sm transition-transform duration-300 hover:scale-110"
                    style="animation: float 4s ease-in-out infinite; animation-delay: 2s;">
                <span class="text-xs uppercase tracking-wider text-on-surface-variant/70 font-medium">Hubungi
                    Kami</span>
            </div>
            <div class="flex flex-wrap justify-center gap-6">
                <a class="font-body-md text-on-surface-variant hover:text-pink-600 transition-all duration-200 underline underline-offset-4"
                    href="https://www.instagram.com/1112Project" target="_blank"
                    rel="noopener noreferrer">Instagram</a>
                <a class="font-body-md text-on-surface-variant hover:text-pink-600 transition-all duration-200 underline underline-offset-4"
                    href="https://wa.me/6282119904113" target="_blank" rel="noopener noreferrer">WhatsApp</a>
                <a class="font-body-md text-on-surface-variant hover:text-pink-600 transition-all duration-200 underline underline-offset-4"
                    href="#">Email</a>
            </div>
            <div class="font-body-md text-on-surface-variant/60 text-sm">
                &copy; {{ date('Y') }} {{ $brandName }}. Semua hak dilindungi.
            </div>
        </div>
    </footer>

    <script>
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
    </script>
</body>

</html>
