<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light scroll-smooth">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="description" content="{{ $invitation->title }} - {{ $invitation->venue_name }}">
    <title>{{ $invitation->title }} - {{ config('app.name') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Inter:wght@100..900&display=swap" rel="stylesheet"/>
    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

    <!-- Tailwind CSS CDN (Self-contained for this specific design) -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "tertiary": "#735a31",
                        "tertiary-fixed-dim": "#e3c290",
                        "on-tertiary-fixed-variant": "#59431c",
                        "surface-container-low": "#f6f3f2",
                        "secondary": "#605e5a",
                        "surface": "#fbf9f8",
                        "on-secondary-fixed-variant": "#484743",
                        "secondary-container": "#e6e2dc",
                        "primary-container": "#b68d40",
                        "surface-container-high": "#eae7e7",
                        "on-tertiary-fixed": "#281900",
                        "on-primary-fixed": "#271900",
                        "on-secondary": "#ffffff",
                        "inverse-primary": "#eebf6d",
                        "on-background": "#1b1c1c",
                        "on-surface-variant": "#4e4538",
                        "on-primary-container": "#3c2900",
                        "inverse-surface": "#303030",
                        "on-tertiary": "#ffffff",
                        "on-secondary-container": "#666460",
                        "surface-container": "#f0eded",
                        "tertiary-fixed": "#ffdeac",
                        "secondary-fixed-dim": "#c9c6c0",
                        "on-error": "#ffffff",
                        "on-error-container": "#93000a",
                        "surface-dim": "#dcd9d9",
                        "secondary-fixed": "#e6e2dc",
                        "primary": "#7b580d",
                        "outline-variant": "#d2c5b3",
                        "surface-container-lowest": "#ffffff",
                        "primary-fixed-dim": "#eebf6d",
                        "on-secondary-fixed": "#1c1c18",
                        "surface-tint": "#7b580d",
                        "on-tertiary-container": "#3c2904",
                        "on-primary-fixed-variant": "#5e4200",
                        "inverse-on-surface": "#f3f0f0",
                        "tertiary-container": "#ac8f61",
                        "outline": "#807666",
                        "on-surface": "#1b1c1c",
                        "primary-fixed": "#ffdea8",
                        "surface-container-highest": "#e4e2e1",
                        "error-container": "#ffdad6",
                        "on-primary": "#ffffff",
                        "background": "#fbf9f8",
                        "surface-bright": "#fbf9f8",
                        "error": "#ba1a1a",
                        "surface-variant": "#e4e2e1"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "spacing": {
                        "stack-lg": "32px",
                        "stack-xl": "64px",
                        "gutter-grid": "16px",
                        "stack-sm": "8px",
                        "stack-md": "16px",
                        "margin-page": "24px"
                    },
                    "fontFamily": {
                        "headline-lg-mobile": ["Playfair Display"],
                        "body-lg": ["Inter"],
                        "headline-lg": ["Playfair Display"],
                        "headline-md": ["Playfair Display"],
                        "label-md": ["Inter"],
                        "label-sm": ["Inter"],
                        "display-lg": ["Playfair Display"],
                        "body-md": ["Inter"]
                    },
                    "fontSize": {
                        "headline-lg-mobile": ["28px", {"lineHeight": "36px", "fontWeight": "500"}],
                        "body-lg": ["18px", {"lineHeight": "28px", "fontWeight": "400"}],
                        "headline-lg": ["32px", {"lineHeight": "40px", "fontWeight": "500"}],
                        "headline-md": ["24px", {"lineHeight": "32px", "fontWeight": "500"}],
                        "label-md": ["14px", {"lineHeight": "20px", "letterSpacing": "0.05em", "fontWeight": "600"}],
                        "label-sm": ["12px", {"lineHeight": "16px", "letterSpacing": "0.03em", "fontWeight": "500"}],
                        "display-lg": ["48px", {"lineHeight": "56px", "letterSpacing": "-0.02em", "fontWeight": "600"}],
                        "body-md": ["16px", {"lineHeight": "24px", "fontWeight": "400"}]
                    }
                },
            },
        }
    </script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- General Styles -->
    <style>
        body {
            background-color: #fbf9f8;
            color: #1b1c1c;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 200, 'GRAD' 0, 'opsz' 24;
            display: inline-block;
            line-height: 1;
        }
        .glass-card {
            background: rgba(247, 243, 237, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(182, 141, 64, 0.2);
        }
        .glass-overlay {
            background: rgba(247, 243, 237, 0.2);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        /* Buttons & Inputs */
        .btn-glow {
            box-shadow: inset 0 1px 1px rgba(255, 255, 255, 0.3), 0 8px 24px rgba(47, 47, 47, 0.08);
        }
        .minimal-input {
            background: transparent;
            border: none;
            border-bottom: 1px solid #d2c5b3;
            padding: 12px 0;
            width: 100%;
            transition: border-color 0.3s ease;
        }
        .minimal-input:focus {
            outline: none;
            border-bottom-color: #b68d40;
            box-shadow: none;
        }
        
        /* Animations */
        .fade-in-scale { animation: fadeInScale 1.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        @keyframes fadeInScale {
            0% { opacity: 0; transform: scale(1.05); }
            100% { opacity: 1; transform: scale(1); }
        }
        .stagger-up {
            opacity: 0;
            transform: translateY(20px);
            animation: staggerUp 1s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        @keyframes staggerUp { 100% { opacity: 1; transform: translateY(0); } }
        @keyframes slow-spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        .animate-slow-spin { animation: slow-spin 8s linear infinite; }
        .scrolling-text { animation: marquee 20s linear infinite; }
        @keyframes marquee { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }
        
        /* Bottom Nav Active Dot */
        .active-dot::after {
            content: '';
            width: 4px;
            height: 4px;
            background: #7b580d;
            border-radius: 50%;
            position: absolute;
            bottom: -6px;
        }

        /* Prevent scroll when splash is active */
        .no-scroll { overflow: hidden; }
    </style>
</head>
<body class="font-body-md text-on-background selection:bg-primary-fixed selection:text-on-primary-fixed no-scroll" x-data="weddingApp()">

    @yield('content')

    <!-- Audio Player (Alpine JS) -->
    @if($invitation->music_url)
    <div class="fixed right-6 top-24 z-40 transition-opacity duration-500" 
         x-show="!showSplash" 
         x-cloak>
        <button @click="toggleAudio" class="glass-card p-3 rounded-full shadow-lg flex items-center gap-3 hover:opacity-90 transition-opacity">
            <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center" :class="{'animate-slow-spin': isPlaying}">
                <span class="material-symbols-outlined text-white text-xl" x-text="isPlaying ? 'music_note' : 'play_arrow'">music_note</span>
            </div>
            <div class="pr-2 text-left hidden sm:block">
                <p class="text-[10px] font-label-sm uppercase tracking-widest text-primary">Now Playing</p>
                <p class="text-label-md font-label-md text-on-surface truncate max-w-[120px]">Wedding Music</p>
            </div>
        </button>
        <audio id="wedding-audio" loop>
            <source src="{{ asset('storage/' . $invitation->music_url) }}" type="audio/mpeg">
        </audio>
    </div>
    @endif

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('weddingApp', () => ({
                showSplash: true,
                isPlaying: false,
                activeSection: 'hero',
                
                openInvitation() {
                    this.showSplash = false;
                    document.body.classList.remove('no-scroll');
                    
                    // Play audio if available
                    this.toggleAudio(true);
                    
                    // Scroll to top of hero
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },
                
                toggleAudio(forcePlay = false) {
                    const audio = document.getElementById('wedding-audio');
                    if(!audio) return;
                    
                    if(forcePlay === true || audio.paused) {
                        audio.play().then(() => {
                            this.isPlaying = true;
                        }).catch(e => console.log('Audio autoplay prevented'));
                    } else {
                        audio.pause();
                        this.isPlaying = false;
                    }
                }
            }));
        });
        
        // Scroll Animation Observer
        document.addEventListener('DOMContentLoaded', () => {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('opacity-100', 'translate-y-0');
                        entry.target.classList.remove('opacity-0', 'translate-y-10');
                    }
                });
            }, { threshold: 0.1 });

            document.querySelectorAll('[data-aos="fade-up"]').forEach(el => {
                el.classList.add('transition-all', 'duration-1000', 'opacity-0', 'translate-y-10');
                observer.observe(el);
            });
        });
    </script>
</body>
</html>
