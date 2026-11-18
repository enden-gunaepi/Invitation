<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $isDemoMode = (bool) ($demoMode ?? false);
        $personalization = $personalization ?? ['greeting' => 'Tamu Undangan', 'cta' => 'Konfirmasi Kehadiran', 'rsvp_hint' => 'Mohon konfirmasi kehadiran Anda.'];

        $groomName = $invitation->groom_name ?: 'Arhan';
        $brideName = $invitation->bride_name ?: 'Nella';
        $coupleName = trim(($invitation->bride_name ?? 'Nella') . ' & ' . ($invitation->groom_name ?? 'Arhan'));

        $brideInitial = strtoupper(substr($brideName, 0, 1) ?: 'N');
        $groomInitial = strtoupper(substr($groomName, 0, 1) ?: 'A');

        $guestName = $guest->name ?? (request('to') ?: 'Tamu Undangan');
        $guestInvitationUrl = !empty($guest?->token) ? $guest->getInvitationUrl() : $invitation->getPublicUrl();

        $coverPhoto = $invitation->cover_photo ? asset('storage/' . $invitation->cover_photo) : null;
        $groomPhoto = $invitation->groom_photo ? asset('storage/' . $invitation->groom_photo) : null;
        $bridePhoto = $invitation->bride_photo ? asset('storage/' . $invitation->bride_photo) : null;
        $igStoryPhoto = $invitation->ig_story_photo ? asset('storage/' . $invitation->ig_story_photo) : null;
        $musicUrl = $invitation->music_signed_url ?? ($invitation->music_url ? asset('storage/' . $invitation->music_url) : null);

        $galleryPhotos = $invitation->photos;
        $heroPhoto = $coverPhoto ?: ($galleryPhotos->first() ? asset('storage/' . $galleryPhotos->first()->file_path) : null);

        $eventDateText = $invitation->event_date ? $invitation->event_date->translatedFormat('l, d F Y') : 'Minggu, 30 Mei 2027';
        $eventDayName = $invitation->event_date ? $invitation->event_date->translatedFormat('l') : 'Minggu';
        $eventDayNumber = $invitation->event_date ? $invitation->event_date->format('d') : '30';
        $eventMonthName = $invitation->event_date ? $invitation->event_date->translatedFormat('F') : 'Mei';
        $eventYearNumber = $invitation->event_date ? $invitation->event_date->format('Y') : '2027';

        $eventDateIso = $invitation->event_date ? $invitation->event_date->format('Y-m-d') : '2027-05-30';
        $eventTimeIso = $invitation->event_time ? \Carbon\Carbon::parse($invitation->event_time)->format('H:i:s') : '07:00:00';
        $targetIso = $eventDateIso . 'T' . $eventTimeIso . '+07:00';

        $openingText = $invitation->opening_text 
            ?: 'Kami mohon do\'a & restunya atas pernikahan kami yang akan diselenggarakan dengan penuh rasa syukur.';
        $closingText = $invitation->closing_text 
            ?: 'Atas kehadiran saudara/(i) & do\'a restunya, kami ucapkan terima kasih yang sebesar-besarnya.';
        $footerText = $invitation->footer_text 
            ?: 'Made with love by ' . config('app.name', 'GNVisual');

        $mapsUrl = $invitation->google_maps_url 
            ?: (($invitation->venue_lat && $invitation->venue_lng)
                ? 'https://www.google.com/maps?q=' . $invitation->venue_lat . ',' . $invitation->venue_lng
                : 'https://maps.google.com/?q=' . urlencode(trim((string) ($invitation->venue_name . ' ' . $invitation->venue_address))));
        $mapsTrackedUrl = $isDemoMode
            ? $mapsUrl
            : route('invitation.map.click', ['slug' => $invitation->slug, 'token' => $guest->token ?? null]);
        $calendarUrl = $invitation->google_calendar_url;

        $hasEvents = $invitation->events && $invitation->events->count() > 0;
        $hasBankAccounts = ($invitation->bankAccounts && $invitation->bankAccounts->count() > 0) 
            || (!empty($invitation->bank_name) && !empty($invitation->bank_account_number));
    @endphp

    <title>{{ $invitation->title ?: ('Undangan Pernikahan ' . $coupleName) }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:ital@0;1&family=Lato:wght@300;400;700;900&family=Philosopher:wght@400;700&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        ink: '#2b2b2b',
                        noir: '#1c1a17',
                        gold: '#b8975a',
                    },
                    fontFamily: {
                        serif: ['"Playfair Display"', 'serif'],
                        phil: ['Philosopher', 'sans-serif'],
                        sans: ['Lato', 'sans-serif'],
                        ar: ['Amiri', 'serif'],
                    }
                }
            }
        }
    </script>
    <style>
        html, body { height: 100%; margin: 0; }
        body { background: #fff; font-family: Lato, sans-serif; overflow: hidden; }
        #sc { scrollbar-width: thin; scroll-behavior: smooth; }
        .rv { opacity: 0; transform: translateY(34px); transition: opacity 1s, transform 1s cubic-bezier(.2,.7,.2,1); }
        .rv.l { transform: translateX(-40px); }
        .rv.r { transform: translateX(40px); }
        .rv.z { transform: scale(.88); }
        .rv.on { opacity: 1; transform: none; }
        .ff {
            position: absolute;
            border-radius: 50%;
            background: #f4e27a;
            box-shadow: 0 0 10px 4px rgba(244,226,122,.6);
            opacity: 0;
            animation: drift linear infinite;
        }
        .ff.d { background: #555; box-shadow: none; }
        @keyframes drift {
            0% { transform: translate(0,0); opacity: 0; }
            20% { opacity: .9; }
            80% { opacity: .7; }
            100% { transform: translate(40px,-90px); opacity: 0; }
        }
        @keyframes zoom { from { transform: scale(1.12); } to { transform: scale(1); } }
        .kb { animation: zoom 6s ease-out both; }
        @keyframes bob { 50% { transform: translateY(-6px); } }
        .bob { animation: bob 2.4s ease-in-out infinite; }
        .noir {
            background: radial-gradient(circle at 20% 10%, #3b362e, transparent 50%),
                        radial-gradient(circle at 90% 70%, #35302a, transparent 45%),
                        #1d1b18;
        }
        .card {
            border: 1px solid rgba(184,151,90,.55);
            border-radius: 14px;
            background: rgba(255,255,255,.04);
            backdrop-filter: blur(2px);
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid rgba(184,151,90,.7);
            border-radius: 999px;
            padding: 7px 14px;
            font: 700 11px Lato;
            color: #fff;
            transition: .3s;
            cursor: pointer;
        }
        .btn:hover { background: #b8975a; transform: translateY(-2px); }
        .nv {
            width: 38px;
            height: 32px;
            border-radius: 999px;
            display: grid;
            place-items: center;
            color: #71717a;
            transition: .35s;
            font-size: 1.1rem;
        }
        .nv.on {
            background: #2b2b2b;
            color: #fff;
            width: 52px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.25);
        }
        .custom-toast {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%) translateY(-100px);
            background: rgba(28, 26, 23, 0.95);
            color: #b8975a;
            padding: 10px 20px;
            border-radius: 9999px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.4);
            border: 1px solid #b8975a;
            z-index: 1000;
            font-size: 0.85rem;
            transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: flex;
            align-items: center;
            gap: 8px;
            pointer-events: none;
        }
        .custom-toast.show {
            transform: translateX(-50%) translateY(0);
        }
    </style>
</head>
<body class="overflow-hidden">

    <!-- Toast Notification -->
    <div id="toast" class="custom-toast">
        <iconify-icon icon="solar:check-circle-bold" class="text-xl text-gold"></iconify-icon>
        <span id="toast-message" class="text-white">Pesan notifikasi</span>
    </div>

    <!-- ============ PANEL KIRI (Hanya Desktop) ============ -->
    <div class="hidden lg:flex fixed inset-0 right-[34%] items-center justify-center bg-white overflow-hidden" style="right:max(34%,440px)">
        <div id="ff" class="absolute inset-0 pointer-events-none"></div>
        
        <!-- Ornamen Garis Bunga -->
        <svg class="absolute top-0 right-0 w-64 h-80 opacity-60" viewBox="0 0 160 200">
            <g fill="none" stroke="#8a8a8a" stroke-width="1.1" stroke-linecap="round">
                <path d="M150 4C110 40 70 90 20 196"/>
                <path d="M110 40c-30-6-46 14-40 38 26 4 44-10 40-38z"/>
                <path d="M70 90c-26-8-46 6-44 30 26 6 44-6 44-30z"/>
                <path d="M130 22c20 8 26 28 12 44-18-4-26-26-12-44z"/>
                <path d="M92 62c10-10 24-8 30 4M60 110c-10-6-22-4-28 6"/>
                <circle cx="108" cy="58" r="5"/>
                <circle cx="64" cy="102" r="4"/>
            </g>
        </svg>
        <svg class="absolute bottom-0 left-0 w-56 h-64 opacity-60 scale-y-[-1]" viewBox="0 0 160 200">
            <g fill="none" stroke="#8a8a8a" stroke-width="1.1" stroke-linecap="round">
                <path d="M150 4C110 40 70 90 20 196"/>
                <path d="M110 40c-30-6-46 14-40 38 26 4 44-10 40-38z"/>
                <path d="M70 90c-26-8-46 6-44 30 26 6 44-6 44-30z"/>
                <path d="M130 22c20 8 26 28 12 44-18-4-26-26-12-44z"/>
                <circle cx="108" cy="58" r="5"/>
            </g>
        </svg>

        <div class="relative w-[360px]">
            <div class="absolute -top-4 -left-4 w-40 h-64 bg-zinc-400"></div>
            @if($heroPhoto)
                <img src="{{ $heroPhoto }}" alt="{{ $coupleName }}" class="relative w-64 h-80 object-cover rounded-tr-lg rounded-bl-[90px] kb shadow-xl">
            @else
                <img src="https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=600&q=80" alt="{{ $coupleName }}" class="relative w-64 h-80 object-cover rounded-tr-lg rounded-bl-[90px] kb shadow-xl">
            @endif
            <div class="absolute -bottom-12 left-40 w-52 bg-ink text-white text-center pt-14 pb-7 rounded-t-[100px] rounded-b-md shadow-2xl" style="box-shadow:12px 12px 0 #d4d4d4">
                <p class="text-[10px] font-bold tracking-widest uppercase text-gold">Undangan Pernikahan</p>
                <h2 class="font-serif text-3xl mt-1 text-white">{{ $coupleName }}</h2>
                <p class="text-[10px] text-zinc-300 mt-2 font-phil">{{ $eventDateText }}</p>
            </div>
        </div>
    </div>

    <!-- ============ KOLOM UNDANGAN UTAMA ============ -->
    <main class="fixed top-0 right-0 h-full w-full lg:w-[34%] lg:min-w-[440px] bg-white shadow-2xl overflow-hidden z-20">

        <!-- ============ 1. COVER ============ -->
        <section id="cover" class="absolute inset-0 z-50 bg-noir text-white text-center transition-transform duration-[1300ms] ease-in-out">
            @if($coverPhoto)
                <img src="{{ $coverPhoto }}" alt="{{ $coupleName }}" class="absolute inset-0 w-full h-full object-cover kb">
            @else
                <img src="https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=900&q=80" alt="{{ $coupleName }}" class="absolute inset-0 w-full h-full object-cover kb">
            @endif
            <div class="absolute inset-0 bg-gradient-to-t from-black via-black/40 to-transparent"></div>
            
            <div class="absolute bottom-0 inset-x-0 pb-10 px-6">
                <p class="text-xs font-bold tracking-widest uppercase text-gold rv on">Undangan Pernikahan</p>
                <h1 class="font-serif text-4xl mt-1 text-white rv on" style="transition-delay:.3s">{{ $coupleName }}</h1>
                <p class="text-xs mt-5 leading-5 text-zinc-300">
                    {{ $eventDayName }}<br>{{ $eventDayNumber }} {{ $eventMonthName }} {{ $eventYearNumber }}
                </p>
                <p class="text-xs mt-4 text-zinc-300">
                    Kepada Yth:<br>Bapak/Ibu/Saudara/i<br>
                    <b class="guest text-sm text-white font-bold">{{ $guestName }}</b>
                </p>
                <button id="openBtn" class="bob mt-5 bg-white text-ink rounded-full px-5 py-2.5 text-xs font-bold inline-flex items-center gap-2 shadow-lg hover:scale-105 transition cursor-pointer">
                    <iconify-icon icon="solar:letter-opened-bold-duotone" class="text-base text-ink"></iconify-icon>
                    <span>Buka Undangan</span>
                </button>
            </div>
        </section>

        <!-- ============ 2. AREA SCROLL ============ -->
        <div id="sc" class="h-full overflow-y-auto overflow-x-hidden relative bg-white">

            <!-- HERO -->
            <section id="home" class="relative min-h-[100svh] text-white text-center flex items-end">
                @if($heroPhoto)
                    <img src="{{ $heroPhoto }}" alt="{{ $coupleName }}" class="absolute inset-0 w-full h-full object-cover">
                @else
                    <img src="https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=900&q=80" alt="{{ $coupleName }}" class="absolute inset-0 w-full h-full object-cover">
                @endif
                <div class="absolute inset-0 bg-gradient-to-b from-transparent via-black/30 to-ink"></div>
                
                <div class="relative w-full pb-10 px-6">
                    <p class="text-xs font-bold tracking-widest uppercase text-gold rv">Undangan Pernikahan</p>
                    <h2 class="font-serif text-4xl mt-1 rv" style="transition-delay:.2s">{{ $coupleName }}</h2>
                    <p class="text-xs mt-5 leading-5 text-zinc-300 rv">
                        {{ $eventDayName }}<br>{{ $eventDayNumber }} {{ $eventMonthName }} {{ $eventYearNumber }}
                    </p>
                    <p class="text-xs mt-4 text-zinc-300 rv">
                        Kepada Yth:<br>Bapak/Ibu/Saudara/i<br>
                        <b class="guest text-white font-bold">{{ $guestName }}</b>
                    </p>
                </div>
            </section>

            <!-- AYAT / MONOGRAM -->
            <section class="bg-ink text-white text-center px-8 py-14">
                <div class="font-serif text-4xl tracking-widest text-gold rv z">
                    {{ $brideInitial }}<span class="mx-3 font-thin text-white/50">|</span>{{ $groomInitial }}
                </div>
                <p class="font-ar text-xl mt-8 leading-loose text-gold/90 rv" dir="rtl">
                    رَبِّ لَا تَذَرْنِي فَرْدًا وَأَنتَ خَيْرُ الْوَارِثِينَ
                </p>
                <p class="text-[11px] italic leading-relaxed mt-4 text-zinc-300 rv">
                    "Dan (ingatlah kisah) Zakaria, ketika dia berdoa kepada Tuhannya, 'Ya Tuhanku, janganlah Engkau biarkan aku hidup seorang diri (tanpa keturunan) dan Engkaulah ahli waris yang terbaik.'"
                </p>
                <p class="text-[11px] font-bold mt-4 tracking-wider text-gold rv">QS. Al-Anbiya: 89</p>
            </section>

            <!-- MEMPELAI -->
            <section id="couple" class="relative px-6 py-14 bg-zinc-50 overflow-hidden">
                <p class="text-center text-xs text-zinc-500 rv leading-relaxed max-w-xs mx-auto">
                    {{ $openingText }}
                </p>

                <!-- Mempelai Wanita -->
                <div class="mt-10 w-3/4 relative rv l">
                    <div class="absolute -bottom-3 -left-3 w-2/3 h-1/2 bg-zinc-400"></div>
                    @if($bridePhoto)
                        <img src="{{ $bridePhoto }}" alt="{{ $brideName }}" class="relative w-full aspect-[3/4] object-cover shadow-md">
                    @else
                        <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=600&q=80" alt="{{ $brideName }}" class="relative w-full aspect-[3/4] object-cover shadow-md">
                    @endif
                </div>
                <div class="mt-5 rv">
                    <h3 class="font-serif text-2xl text-zinc-800">{{ $invitation->bride_name ?: 'Nella Kurnia Sari' }}</h3>
                    @if($invitation->bride_parent_name)
                        <p class="text-[11px] text-zinc-500 mt-1">Putri dari {{ $invitation->bride_parent_name }}</p>
                    @endif
                    <div class="flex gap-2 mt-3 text-zinc-600">
                        @if($invitation->bride_instagram)
                            <a href="https://instagram.com/{{ ltrim($invitation->bride_instagram, '@') }}" target="_blank" class="w-8 h-8 rounded-full bg-white shadow flex items-center justify-center hover:scale-110 transition text-sm" title="Instagram">
                                <iconify-icon icon="ri:instagram-line"></iconify-icon>
                            </a>
                        @endif
                        @if($invitation->bride_facebook)
                            <a href="{{ $invitation->bride_facebook }}" target="_blank" class="w-8 h-8 rounded-full bg-white shadow flex items-center justify-center hover:scale-110 transition text-sm" title="Facebook">
                                <iconify-icon icon="ri:facebook-fill"></iconify-icon>
                            </a>
                        @endif
                        @if(!$invitation->bride_instagram && !$invitation->bride_facebook)
                            <a href="#" class="w-8 h-8 rounded-full bg-white shadow flex items-center justify-center hover:scale-110 transition text-sm" title="Instagram">
                                <iconify-icon icon="ri:instagram-line"></iconify-icon>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Mempelai Pria -->
                <div class="mt-14 w-3/4 ml-auto relative rv r">
                    <div class="absolute -top-3 -right-3 w-2/3 h-1/2 bg-zinc-400"></div>
                    @if($groomPhoto)
                        <img src="{{ $groomPhoto }}" alt="{{ $groomName }}" class="relative w-full aspect-[3/4] object-cover shadow-md">
                    @else
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=600&q=80" alt="{{ $groomName }}" class="relative w-full aspect-[3/4] object-cover shadow-md">
                    @endif
                </div>
                <div class="mt-5 text-right rv">
                    <h3 class="font-serif text-2xl text-zinc-800">{{ $invitation->groom_name ?: 'Arhan Putra Perkasa' }}</h3>
                    @if($invitation->groom_parent_name)
                        <p class="text-[11px] text-zinc-500 mt-1">Putra dari {{ $invitation->groom_parent_name }}</p>
                    @endif
                    <div class="flex gap-2 mt-3 justify-end text-zinc-600">
                        @if($invitation->groom_instagram)
                            <a href="https://instagram.com/{{ ltrim($invitation->groom_instagram, '@') }}" target="_blank" class="w-8 h-8 rounded-full bg-white shadow flex items-center justify-center hover:scale-110 transition text-sm" title="Instagram">
                                <iconify-icon icon="ri:instagram-line"></iconify-icon>
                            </a>
                        @endif
                        @if($invitation->groom_facebook)
                            <a href="{{ $invitation->groom_facebook }}" target="_blank" class="w-8 h-8 rounded-full bg-white shadow flex items-center justify-center hover:scale-110 transition text-sm" title="Facebook">
                                <iconify-icon icon="ri:facebook-fill"></iconify-icon>
                            </a>
                        @endif
                        @if(!$invitation->groom_instagram && !$invitation->groom_facebook)
                            <a href="#" class="w-8 h-8 rounded-full bg-white shadow flex items-center justify-center hover:scale-110 transition text-sm" title="Instagram">
                                <iconify-icon icon="ri:instagram-line"></iconify-icon>
                            </a>
                        @endif
                    </div>
                </div>
            </section>

            <!-- SAVE THE DATE -->
            <section class="relative text-white text-center py-24 px-8 overflow-hidden">
                @if($heroPhoto)
                    <img src="{{ $heroPhoto }}" alt="" class="absolute inset-0 w-full h-full object-cover">
                @else
                    <img src="https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=900&q=80" alt="" class="absolute inset-0 w-full h-full object-cover">
                @endif
                <div class="absolute inset-0 bg-black/65"></div>
                
                <div class="relative">
                    <h3 class="font-serif text-3xl rv text-gold">Save the date</h3>
                    <div class="grid grid-cols-2 gap-y-7 mt-8 max-w-[220px] mx-auto">
                        <div class="rv">
                            <p id="d" class="font-serif text-4xl text-white">00</p>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-gold mt-1">Hari</p>
                        </div>
                        <div class="rv" style="transition-delay:.15s">
                            <p id="h" class="font-serif text-4xl text-white">00</p>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-gold mt-1">Jam</p>
                        </div>
                        <div class="rv" style="transition-delay:.3s">
                            <p id="m" class="font-serif text-4xl text-white">00</p>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-gold mt-1">Menit</p>
                        </div>
                        <div class="rv" style="transition-delay:.45s">
                            <p id="s" class="font-serif text-4xl text-white">00</p>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-gold mt-1">Detik</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ACARA (RUNDOWN) -->
            <section id="event" class="noir relative text-white text-center px-5 py-14 overflow-hidden">
                <svg class="absolute inset-0 w-full h-full opacity-30 pointer-events-none" viewBox="0 0 400 800" preserveAspectRatio="none" fill="none" stroke="#b8975a">
                    <path d="M400 80C300 200 380 420 250 560S60 700 0 780"/>
                    <path d="M0 120C90 240 40 440 160 600S340 720 400 760"/>
                </svg>

                <h3 class="relative font-serif text-3xl rv text-gold">Rangkaian Acara</h3>
                
                <div class="relative flex justify-center gap-4 mt-6 rv">
                    <img src="{{ $bridePhoto ?: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=300&q=80' }}" alt="" class="w-24 h-32 object-cover rounded-t-full border border-gold/40 shadow-md">
                    <img src="{{ $groomPhoto ?: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=300&q=80' }}" alt="" class="w-24 h-32 object-cover rounded-t-full border border-gold/40 shadow-md">
                </div>

                <p class="relative text-[11px] font-bold mt-6 text-zinc-300">Kami bermaksud untuk mengundang saudara/(i) dalam acara pernikahan kami pada:</p>

                @if($hasEvents)
                    @foreach($invitation->events as $index => $event)
                        @php
                            $eDay = $event->event_date ? $event->event_date->translatedFormat('l') : $eventDayName;
                            $eNum = $event->event_date ? $event->event_date->format('d') : $eventDayNumber;
                            $eMonthYear = $event->event_date ? $event->event_date->translatedFormat('F Y') : ($eventMonthName . ' ' . $eventYearNumber);
                            
                            $eventCalendarUrl = null;
                            if ($event->event_date && $event->event_time) {
                                $startDT = \Carbon\Carbon::parse($event->event_date->format('Y-m-d') . ' ' . $event->event_time);
                                $endDT = $event->event_end_time 
                                    ? \Carbon\Carbon::parse($event->event_date->format('Y-m-d') . ' ' . $event->event_end_time) 
                                    : (clone $startDT)->addHours(2);
                                $eventCalendarUrl = 'https://calendar.google.com/calendar/render?action=TEMPLATE&text=' . urlencode($event->event_name . ' - ' . $coupleName) . '&dates=' . $startDT->utc()->format('Ymd\THis\Z') . '/' . $endDT->utc()->format('Ymd\THis\Z') . '&details=' . urlencode('Pernikahan ' . $coupleName) . '&location=' . urlencode($event->venue_name . ', ' . $event->venue_address);
                            }
                        @endphp

                        <div class="card relative mt-6 py-7 px-5 rv z">
                            <h4 class="font-phil text-2xl text-gold">{{ $event->event_name }}</h4>
                            <hr class="border-gold/50 my-3 mx-6">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-zinc-300">{{ $eDay }}</p>
                            <p class="font-phil text-4xl my-1 text-white">{{ $eNum }}</p>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-zinc-300">{{ $eMonthYear }}</p>
                            
                            @if($event->event_time)
                                <p class="text-[10px] font-bold mt-1 text-gold">
                                    {{ \Carbon\Carbon::parse($event->event_time)->format('H:i') }}
                                    @if($event->event_end_time)
                                        - {{ \Carbon\Carbon::parse($event->event_end_time)->format('H:i') }}
                                    @endif
                                    WIB
                                </p>
                            @endif

                            <hr class="border-gold/50 my-3 mx-10">

                            @if($event->venue_name)
                                <p class="text-xs font-bold text-white">{{ $event->venue_name }}</p>
                            @endif
                            @if($event->venue_address)
                                <p class="text-[10px] text-zinc-300 max-w-xs mx-auto mt-0.5">{{ $event->venue_address }}</p>
                            @endif

                            <div class="flex justify-center gap-2 mt-4">
                                <a href="{{ $event->venue_maps_url ?: $mapsTrackedUrl }}" target="_blank" class="btn">
                                    <iconify-icon icon="solar:map-point-wave-bold" class="text-sm text-gold"></iconify-icon>
                                    <span>Navigasi Map</span>
                                </a>
                                @if($eventCalendarUrl || $calendarUrl)
                                    <a href="{{ $eventCalendarUrl ?: $calendarUrl }}" target="_blank" class="btn">
                                        <iconify-icon icon="solar:calendar-add-bold-duotone" class="text-sm text-gold"></iconify-icon>
                                        <span>Simpan Tanggal</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    <!-- Fallback Akad Nikah -->
                    <div class="card relative mt-5 py-7 px-5 rv z">
                        <h4 class="font-phil text-2xl text-gold">Akad Nikah</h4>
                        <hr class="border-gold/60 my-3 mx-6">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-zinc-300">{{ $eventDayName }}</p>
                        <p class="font-phil text-4xl my-1 text-white">{{ $eventDayNumber }}</p>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-zinc-300">{{ $eventMonthName }} {{ $eventYearNumber }}</p>
                        <p class="text-[10px] font-bold mt-1 text-gold">07:00 - 09:00 WIB</p>
                        <hr class="border-gold/60 my-3 mx-10">
                        <div class="border border-dashed border-gold/70 rounded-xl p-3 text-[10px] font-bold flex items-center gap-2 text-left text-zinc-300">
                            <iconify-icon icon="solar:lock-bold-duotone" class="text-base text-gold shrink-0"></iconify-icon>
                            <span>Acara ini diselenggarakan secara terbatas dan hanya diperuntukan bagi keluarga serta tamu undangan tertentu.</span>
                        </div>
                    </div>

                    <!-- Fallback Resepsi -->
                    <div class="card relative mt-6 py-7 px-5 rv z">
                        <h4 class="font-phil text-2xl text-gold">Resepsi</h4>
                        <hr class="border-gold/60 my-3 mx-6">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-zinc-300">{{ $eventDayName }}</p>
                        <p class="font-phil text-4xl my-1 text-white">{{ $eventDayNumber }}</p>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-zinc-300">{{ $eventMonthName }} {{ $eventYearNumber }}</p>
                        <p class="text-[10px] font-bold mt-1 text-gold">10:00 - 14:00 WIB</p>
                        <hr class="border-gold/60 my-3 mx-10">
                        <p class="text-xs font-bold text-white">{{ $invitation->venue_name ?: 'Bale Pare' }}</p>
                        <p class="text-[10px] text-zinc-300">{{ $invitation->venue_address ?: 'Jl. Soekarno Hatta, No 1, Bandung, Jawa Barat' }}</p>
                        <div class="flex justify-center gap-2 mt-4">
                            <a href="{{ $mapsTrackedUrl }}" target="_blank" class="btn">
                                <iconify-icon icon="solar:map-point-wave-bold" class="text-sm text-gold"></iconify-icon>
                                <span>Navigasi Map</span>
                            </a>
                            @if($calendarUrl)
                                <a href="{{ $calendarUrl }}" target="_blank" class="btn">
                                    <iconify-icon icon="solar:calendar-add-bold-duotone" class="text-sm text-gold"></iconify-icon>
                                    <span>Simpan Tanggal</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Live Streaming (Opsional) -->
                @if($invitation->livestream_enabled && !empty($invitation->livestream_url))
                    <div class="relative mt-8 rv">
                        <p class="text-[10px] text-zinc-300">Acara ini akan disiarkan langsung melalui media internet. Silahkan klik tombol dibawah ini untuk membuka saluran live streaming.</p>
                        <a href="{{ $invitation->livestream_url }}" target="_blank" class="btn mt-3">
                            <iconify-icon icon="solar:videocamera-record-bold-duotone" class="text-base text-gold"></iconify-icon>
                            <span>{{ $invitation->livestream_label ?: 'Live Streaming' }}</span>
                        </a>
                    </div>
                @endif
            </section>

            <!-- KISAH CINTA -->
            <section class="bg-white px-5 py-12">
                <h3 class="font-serif text-3xl text-center text-zinc-700 rv">Kisah Cinta</h3>
                <div class="mt-8 space-y-7">
                    @if($invitation->loveStories && $invitation->loveStories->count() > 0)
                        @foreach($invitation->loveStories as $i => $story)
                            <div class="rv {{ $i % 2 ? 'r' : 'l' }} relative flex rounded-3xl border border-zinc-200 shadow-md overflow-hidden bg-white">
                                <div class="absolute top-0 {{ $i % 2 ? 'right-0' : 'left-0' }} w-1/2 h-1.5 bg-zinc-700"></div>
                                @if($story->photo_path)
                                    <img src="{{ asset('storage/' . $story->photo_path) }}" alt="{{ $story->title }}" class="w-2/5 object-cover">
                                @else
                                    <div class="w-2/5 bg-zinc-800 flex items-center justify-center text-gold">
                                        <iconify-icon icon="solar:heart-bold" class="text-3xl"></iconify-icon>
                                    </div>
                                @endif
                                <div class="p-4 flex-1">
                                    @if($story->year)
                                        <span class="text-[9px] uppercase tracking-wider font-bold text-gold">{{ $story->year }}</span>
                                    @endif
                                    <h4 class="font-sans font-bold text-xs text-zinc-800">{{ $story->title }}</h4>
                                    <p class="text-[10px] text-zinc-500 leading-relaxed mt-1.5">{{ $story->description }}</p>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <!-- Fallback Sample Stories -->
                        <div class="rv l relative flex rounded-3xl border border-zinc-200 shadow-md overflow-hidden bg-white">
                            <div class="absolute top-0 left-0 w-1/2 h-1.5 bg-zinc-700"></div>
                            <img src="https://images.unsplash.com/photo-1522673607200-164d1b6ce486?auto=format&fit=crop&w=400&q=80" alt="" class="w-2/5 object-cover">
                            <div class="p-4 flex-1">
                                <h4 class="font-sans font-bold text-xs text-zinc-800">Awal Bertemu</h4>
                                <p class="text-[10px] text-zinc-500 leading-relaxed mt-1.5">Dua hati dipertemukan dalam perjalanan yang sederhana namun bermakna, lalu tumbuh saling memahami dan mengenal.</p>
                            </div>
                        </div>

                        <div class="rv r relative flex rounded-3xl border border-zinc-200 shadow-md overflow-hidden bg-white">
                            <div class="absolute top-0 right-0 w-1/2 h-1.5 bg-zinc-700"></div>
                            <img src="https://images.unsplash.com/photo-1515934751635-c81c6bc9a2d8?auto=format&fit=crop&w=400&q=80" alt="" class="w-2/5 object-cover">
                            <div class="p-4 flex-1">
                                <h4 class="font-sans font-bold text-xs text-zinc-800">Komitmen Bersama</h4>
                                <p class="text-[10px] text-zinc-500 leading-relaxed mt-1.5">Dalam komitmen yang tumbuh dengan tulus, dua hati belajar saling menguatkan dan menjaga.</p>
                            </div>
                        </div>

                        <div class="rv l relative flex rounded-3xl border border-zinc-200 shadow-md overflow-hidden bg-white">
                            <div class="absolute top-0 left-0 w-1/2 h-1.5 bg-zinc-700"></div>
                            <img src="https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=400&q=80" alt="" class="w-2/5 object-cover">
                            <div class="p-4 flex-1">
                                <h4 class="font-sans font-bold text-xs text-zinc-800">Langkah Bersama</h4>
                                <p class="text-[10px] text-zinc-500 leading-relaxed mt-1.5">Dari dua hati yang saling belajar, tumbuh keyakinan untuk melangkah bersama dalam satu tujuan suci.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </section>

            <!-- GALERI -->
            <section id="gallery" class="bg-ink pt-0 pb-12 text-white text-center">
                @if($galleryPhotos && $galleryPhotos->count() > 0)
                    <img src="{{ asset('storage/' . $galleryPhotos->first()->file_path) }}" alt="Galeri Utama" class="gal w-full h-72 object-cover rounded-b-xl cursor-zoom-in" onclick="openLightbox(this.src)">
                @else
                    <img src="https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=900&q=80" alt="" class="gal w-full h-72 object-cover rounded-b-xl cursor-zoom-in" onclick="openLightbox(this.src)">
                @endif
                
                <h3 class="font-serif text-3xl mt-10 rv text-gold">Galeri</h3>
                <div class="grid grid-cols-2 gap-2 px-5 mt-5">
                    @if($galleryPhotos && $galleryPhotos->count() > 1)
                        @foreach($galleryPhotos->slice(1) as $idx => $photo)
                            <img src="{{ asset('storage/' . $photo->file_path) }}" alt="{{ $photo->caption ?: 'Galeri ' . ($idx + 2) }}" class="gal rv z w-full aspect-[3/4] object-cover cursor-zoom-in rounded-lg shadow-md hover:opacity-90 transition" onclick="openLightbox(this.src)">
                        @endforeach
                    @else
                        <img src="https://images.unsplash.com/photo-1583939003579-730e3918a45a?auto=format&fit=crop&w=600&q=80" alt="" class="gal rv z w-full aspect-[3/4] object-cover cursor-zoom-in rounded-lg shadow-md" onclick="openLightbox(this.src)">
                        <img src="https://images.unsplash.com/photo-1511285560929-80b456fea0bc?auto=format&fit=crop&w=600&q=80" alt="" class="gal rv z w-full aspect-[3/4] object-cover cursor-zoom-in rounded-lg shadow-md" style="transition-delay:.15s" onclick="openLightbox(this.src)">
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=600&q=80" alt="" class="gal rv z w-full aspect-[3/4] object-cover cursor-zoom-in rounded-lg shadow-md" onclick="openLightbox(this.src)">
                        <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=600&q=80" alt="" class="gal rv z w-full aspect-[3/4] object-cover cursor-zoom-in rounded-lg shadow-md" style="transition-delay:.15s" onclick="openLightbox(this.src)">
                    @endif
                </div>
            </section>

            <!-- RSVP -->
            <section class="bg-zinc-300 px-5 py-6">
                <div class="bg-ink rounded-[28px] text-center text-white py-6 px-5 rv z border border-gold/30 shadow-xl">
                    <h3 class="font-phil text-3xl text-gold">RSVP</h3>
                    <p class="text-[11px] text-zinc-300 mt-1">{{ $personalization['rsvp_hint'] }}</p>
                    <button id="rsvpBtn" class="mt-4 w-full border border-gold/70 rounded-full py-2.5 text-xs font-bold inline-flex justify-center items-center gap-2 hover:bg-gold/20 transition cursor-pointer">
                        <iconify-icon icon="solar:letter-opened-bold-duotone" class="text-base text-gold"></iconify-icon>
                        <span>{{ $personalization['cta'] }}</span>
                    </button>
                </div>
            </section>

            <!-- KADO / TANDA KASIH -->
            <section id="gift" class="bg-gradient-to-b from-zinc-300 to-zinc-500 px-5 pt-10 pb-12 text-center text-zinc-800">
                <div class="flex justify-center gap-5 rv">
                    <div class="relative">
                        <div class="absolute -bottom-2 -left-2 w-14 h-20 bg-zinc-600"></div>
                        <img src="{{ $bridePhoto ?: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=300&q=80' }}" alt="" class="relative w-28 h-40 object-cover shadow-md">
                    </div>
                    <div class="relative mt-6">
                        <div class="absolute -bottom-2 -right-2 w-12 h-16 bg-zinc-600"></div>
                        <img src="{{ $groomPhoto ?: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=300&q=80' }}" alt="" class="relative w-24 h-36 object-cover shadow-md">
                    </div>
                </div>

                <h3 class="font-serif text-3xl mt-8 rv text-zinc-900">Tanda Kasih</h3>
                <p class="text-[10px] mt-2 text-zinc-700 rv max-w-xs mx-auto">
                    Doa restu anda merupakan karunia yang sangat berarti bagi kedua mempelai. Namun jika memberi adalah ungkapan tanda kasih anda, anda dapat menggunakan fitur berikut:
                </p>

                <!-- Rekening Bank -->
                @if($invitation->bankAccounts && $invitation->bankAccounts->count() > 0)
                    @foreach($invitation->bankAccounts as $acc)
                        <div class="relative mt-5 rounded-2xl text-left text-white p-5 overflow-hidden bg-gradient-to-br from-zinc-900 via-zinc-950 to-black shadow-xl rv z border border-gold/30">
                            <div class="flex justify-between items-start">
                                <span class="font-phil text-xs text-gold">{{ $acc->bank_name }}</span>
                                <img src="{{ \App\Support\BankLogo::assetUrl($acc->bank_name) }}" alt="{{ $acc->bank_name }}" class="h-5 w-auto bg-white px-1.5 py-0.5 rounded object-contain">
                            </div>
                            <!-- Gold Chip -->
                            <div class="w-10 h-7 rounded bg-gradient-to-br from-yellow-300 via-yellow-500 to-yellow-600 mt-4 shadow-sm"></div>
                            <p class="font-phil text-lg tracking-widest mt-4 text-white font-bold">{{ $acc->account_number }}</p>
                            <div class="flex justify-between items-end mt-1">
                                <p class="text-[11px] text-zinc-300">a.n. {{ $acc->account_name }}</p>
                                <button class="btn !py-1 !px-3" onclick="copyText('{{ $acc->account_number }}', 'Nomor rekening {{ $acc->bank_name }} disalin!')">
                                    <iconify-icon icon="solar:copy-bold" class="text-xs text-gold"></iconify-icon>
                                    <span>Salin</span>
                                </button>
                            </div>
                        </div>
                    @endforeach
                @elseif(!empty($invitation->bank_name) && !empty($invitation->bank_account_number))
                    <div class="relative mt-5 rounded-2xl text-left text-white p-5 overflow-hidden bg-gradient-to-br from-zinc-900 via-zinc-950 to-black shadow-xl rv z border border-gold/30">
                        <div class="flex justify-between items-start">
                            <span class="font-phil text-xs text-gold">{{ $invitation->bank_name }}</span>
                            <img src="{{ \App\Support\BankLogo::assetUrl($invitation->bank_name) }}" alt="{{ $invitation->bank_name }}" class="h-5 w-auto bg-white px-1.5 py-0.5 rounded object-contain">
                        </div>
                        <div class="w-10 h-7 rounded bg-gradient-to-br from-yellow-300 via-yellow-500 to-yellow-600 mt-4 shadow-sm"></div>
                        <p class="font-phil text-lg tracking-widest mt-4 text-white font-bold">{{ $invitation->bank_account_number }}</p>
                        <div class="flex justify-between items-end mt-1">
                            <p class="text-[11px] text-zinc-300">a.n. {{ $invitation->bank_account_name ?: $coupleName }}</p>
                            <button class="btn !py-1 !px-3" onclick="copyText('{{ $invitation->bank_account_number }}', 'Nomor rekening disalin!')">
                                <iconify-icon icon="solar:copy-bold" class="text-xs text-gold"></iconify-icon>
                                <span>Salin</span>
                            </button>
                        </div>
                    </div>
                @endif

                <!-- Alamat Kado Fisik -->
                @if(!empty($invitation->gift_address))
                    <div class="mt-6 p-4 rounded-2xl bg-white/80 border border-zinc-400/50 text-center rv">
                        <h4 class="font-serif text-lg font-bold text-zinc-800 flex items-center justify-center gap-1.5">
                            <iconify-icon icon="solar:box-bold-duotone" class="text-xl text-gold"></iconify-icon>
                            <span>Kirim Kado Fisik</span>
                        </h4>
                        <p class="text-[11px] text-zinc-600 mt-1 leading-relaxed">{{ $invitation->gift_address }}</p>
                        <button onclick="copyText('{{ addslashes($invitation->gift_address) }}', 'Alamat kado disalin!')" class="mt-3 bg-ink text-white rounded-full px-4 py-2 text-xs font-bold inline-flex items-center gap-1.5 hover:bg-black transition">
                            <iconify-icon icon="solar:copy-bold" class="text-xs text-gold"></iconify-icon>
                            <span>Salin Alamat</span>
                        </button>
                    </div>
                @endif
            </section>

            <!-- UCAPAN & DOA -->
            <section id="wishes" class="bg-zinc-500 px-5 pb-10">
                <div class="bg-zinc-700 rounded-2xl text-white p-5 rv z border border-zinc-600 shadow-xl">
                    <h3 class="font-serif text-2xl text-center text-gold">Ucapan &amp; Doa</h3>
                    <p class="text-[10px] text-center text-zinc-300 mt-1">Sampaikan doa restu hangat Anda untuk kami</p>

                    <form id="wishForm" action="{{ route('invitation.wish', $invitation->slug) }}" method="POST" class="mt-4">
                        @csrf
                        <input type="text" name="name" id="wn" value="{{ $guestName !== 'Tamu Undangan' ? $guestName : '' }}" placeholder="Nama Anda" required class="w-full rounded-lg px-3 py-2 text-xs text-ink outline-none bg-white">
                        <textarea name="message" id="wt" rows="4" placeholder="Tuliskan ucapan & doa Anda di sini..." required class="mt-2 w-full rounded-lg px-3 py-2 text-xs text-ink outline-none resize-none bg-white"></textarea>
                        <button type="submit" id="wsend" class="mt-3 w-full bg-ink rounded-full py-2.5 text-xs font-bold hover:bg-black transition text-white inline-flex items-center justify-center gap-1.5 shadow-md">
                            <iconify-icon icon="solar:chat-round-dots-bold-duotone" class="text-base text-gold"></iconify-icon>
                            <span>Kirim Ucapan</span>
                        </button>
                    </form>

                    <div id="wl" class="mt-4 space-y-2.5 max-h-60 overflow-y-auto pr-1">
                        @if($invitation->wishes && $invitation->wishes->count() > 0)
                            @foreach($invitation->wishes as $wish)
                                <div class="bg-zinc-600 rounded-xl p-3 text-[11px] rv on border border-zinc-500/50">
                                    <div class="flex items-center justify-between">
                                        <b class="text-white">{{ $wish->name }}</b>
                                        <span class="text-[9px] text-zinc-300 opacity-80">{{ $wish->created_at ? $wish->created_at->diffForHumans() : 'Baru saja' }}</span>
                                    </div>
                                    <p class="mt-1 text-zinc-200 leading-relaxed">{{ $wish->message }}</p>
                                </div>
                            @endforeach
                        @else
                            <p id="emptyWishMsg" class="text-xs text-center text-zinc-300 opacity-70 py-3">Belum ada ucapan. Jadilah yang pertama memberikan doa restu!</p>
                        @endif
                    </div>
                </div>
            </section>

            <!-- DRESS CODE / INFO PARKIR -->
            <section class="bg-white px-5 py-10 text-center space-y-4">
                <div class="p-6 rounded-2xl bg-zinc-50 border border-zinc-200 rv z text-left">
                    <h4 class="font-serif text-xl font-bold text-zinc-800 flex items-center gap-2">
                        <iconify-icon icon="solar:t-shirt-bold-duotone" class="text-2xl text-gold"></iconify-icon>
                        <span>Dress Code</span>
                    </h4>
                    <p class="text-xs text-zinc-600 mt-2 leading-relaxed">
                        Tamu undangan diharapkan mengenakan pakaian bernuansa <b>Monokrom / Hitam / Putih / Earth Tone</b> agar selaras dengan tema pesta pernikahan.
                    </p>
                </div>

                <div class="p-6 rounded-2xl bg-zinc-50 border border-zinc-200 rv z text-left">
                    <h4 class="font-serif text-xl font-bold text-zinc-800 flex items-center gap-2">
                        <iconify-icon icon="solar:routing-bold-duotone" class="text-2xl text-gold"></iconify-icon>
                        <span>Area Parkir &amp; Lokasi</span>
                    </h4>
                    <p class="text-xs text-zinc-600 mt-2 leading-relaxed">
                        Area parkir tersedia luas di lokasi gedung dengan petunjuk petugas di pintu gerbang utama.
                    </p>
                </div>
            </section>

            <!-- PROTOKOL KESEHATAN -->
            <section class="bg-white px-5 pb-12 text-center">
                <h3 class="font-serif text-3xl text-zinc-700 rv">Protokol Kesehatan</h3>
                <p class="text-[11px] text-zinc-500 mt-2 rv">Acara ini diselenggarakan dengan mematuhi protokol kesehatan.</p>
                <div class="grid grid-cols-2 gap-5 mt-6 text-[10px] text-zinc-700">
                    <div class="rv p-3 rounded-2xl bg-zinc-50 border border-zinc-200">
                        <span class="w-11 h-11 rounded-full bg-ink text-gold grid place-items-center mx-auto mb-2 text-xl shadow-sm">
                            <iconify-icon icon="solar:heart-pulse-bold-duotone"></iconify-icon>
                        </span>
                        <p class="font-semibold">Datang dengan Badan Sehat</p>
                    </div>
                    <div class="rv p-3 rounded-2xl bg-zinc-50 border border-zinc-200">
                        <span class="w-11 h-11 rounded-full bg-ink text-gold grid place-items-center mx-auto mb-2 text-xl shadow-sm">
                            <iconify-icon icon="solar:hand-wash-bold-duotone"></iconify-icon>
                        </span>
                        <p class="font-semibold">Menjaga Kebersihan Area</p>
                    </div>
                    <div class="rv p-3 rounded-2xl bg-zinc-50 border border-zinc-200">
                        <span class="w-11 h-11 rounded-full bg-ink text-gold grid place-items-center mx-auto mb-2 text-xl shadow-sm">
                            <iconify-icon icon="solar:mask-bold-duotone"></iconify-icon>
                        </span>
                        <p class="font-semibold">Terapkan Etika Batuk/Bersin</p>
                    </div>
                    <div class="rv p-3 rounded-2xl bg-zinc-50 border border-zinc-200">
                        <span class="w-11 h-11 rounded-full bg-ink text-gold grid place-items-center mx-auto mb-2 text-xl shadow-sm">
                            <iconify-icon icon="solar:shield-check-bold-duotone"></iconify-icon>
                        </span>
                        <p class="font-semibold">Saling Menghormati Protokol</p>
                    </div>
                </div>
            </section>

            <!-- TERIMA KASIH (CLOSING) -->
            <section class="relative text-white text-center px-6 pt-12 pb-28 min-h-[70svh] overflow-hidden flex flex-col justify-center">
                @if($heroPhoto)
                    <img src="{{ $heroPhoto }}" alt="" class="absolute inset-0 w-full h-full object-cover">
                @else
                    <img src="https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=900&q=80" alt="" class="absolute inset-0 w-full h-full object-cover">
                @endif
                <div class="absolute inset-0 bg-black/70"></div>
                
                <div class="relative py-8">
                    <h3 class="font-serif text-3xl rv text-gold">Terima Kasih</h3>
                    <p class="text-xs mt-3 leading-relaxed text-zinc-300 rv max-w-xs mx-auto">{{ $closingText }}</p>
                    <p class="text-[10px] uppercase tracking-widest mt-12 text-zinc-400">Hormat Kami</p>
                    <p class="font-serif text-3xl rv text-white mt-1">{{ $coupleName }}</p>
                    <p class="text-[10px] mt-8 leading-4 opacity-70 text-zinc-300">{{ $footerText }}</p>
                </div>
            </section>
        </div>

        <!-- ============ TAB QR FLOATING DI KANAN ============ -->
        <button id="qrTab" class="absolute right-0 top-1/2 -translate-y-1/2 z-30 bg-ink text-white shadow-xl rounded-l-lg px-2 py-3 text-[10px] font-bold flex flex-col items-center gap-1.5 hover:bg-black transition cursor-pointer" style="writing-mode:vertical-rl">
            <iconify-icon icon="solar:qr-code-bold-duotone" class="text-sm text-gold"></iconify-icon>
            <span class="tracking-widest">QR CODE</span>
        </button>

        <!-- Tombol Scroll ke Atas -->
        <button id="top" class="absolute right-4 bottom-20 z-30 w-9 h-9 rounded-full bg-ink/80 text-white grid place-items-center opacity-0 pointer-events-none transition hover:bg-black shadow-lg">
            <iconify-icon icon="solar:arrow-up-linear" class="text-lg text-gold"></iconify-icon>
        </button>

        <!-- ============ MUSIK TOGGLE FLOATING ============ -->
        <button id="musicBtn" class="absolute top-4 right-4 z-30 w-10 h-10 rounded-full bg-white/90 text-ink shadow-lg hidden flex items-center justify-center border border-zinc-200 hover:scale-105 transition" title="Musik">
            <iconify-icon id="musicIcon" icon="solar:music-note-slider-bold-duotone" class="text-lg text-gold"></iconify-icon>
        </button>
        <audio id="bgm" src="{{ $musicUrl ?: 'https://assets.mixkit.co/music/preview/mixkit-romantic-wedding-272.mp3' }}" loop preload="auto"></audio>

        <!-- ============ NAVIGASI BAWAH ============ -->
        <nav id="nav" class="absolute left-1/2 -translate-x-1/2 bottom-4 z-30 bg-white/95 backdrop-blur-md rounded-full shadow-2xl px-2 py-1.5 flex gap-1 translate-y-24 transition-transform duration-700 border border-zinc-200">
            <button class="nv on" data-t="home" title="Beranda">
                <iconify-icon icon="solar:home-smile-bold-duotone"></iconify-icon>
            </button>
            <button class="nv" data-t="couple" title="Mempelai">
                <iconify-icon icon="solar:heart-bold-duotone"></iconify-icon>
            </button>
            <button class="nv" data-t="gallery" title="Galeri">
                <iconify-icon icon="solar:gallery-bold-duotone"></iconify-icon>
            </button>
            <button class="nv" data-t="gift" title="Kado">
                <iconify-icon icon="solar:gift-bold-duotone"></iconify-icon>
            </button>
            <button class="nv" data-t="wishes" title="Ucapan">
                <iconify-icon icon="solar:chat-round-dots-bold-duotone"></iconify-icon>
            </button>
        </nav>

        <!-- ============ MODAL RSVP ============ -->
        <div id="mRsvp" class="hidden fixed inset-0 z-[90] bg-black/65 backdrop-blur-xs flex items-center justify-center p-5">
            <div class="bg-white rounded-2xl w-full max-w-sm p-5 text-ink shadow-2xl border border-zinc-200" onclick="event.stopPropagation()">
                <div class="flex justify-between items-center pb-2 border-b border-zinc-200">
                    <span class="font-phil text-lg text-ink font-bold">Konfirmasi Kehadiran</span>
                    <button class="cl text-zinc-400 hover:text-zinc-700 text-lg">
                        <iconify-icon icon="solar:close-circle-bold"></iconify-icon>
                    </button>
                </div>

                <form id="rsvpForm" action="{{ route('invitation.rsvp', $invitation->slug) }}" method="POST" class="mt-3 space-y-3">
                    @csrf
                    @if(!empty($guest?->id))
                        <input type="hidden" name="guest_id" value="{{ $guest->id }}">
                    @endif

                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="name" id="rsvpName" value="{{ $guestName !== 'Tamu Undangan' ? $guestName : '' }}" placeholder="Nama Anda" required class="w-full border border-zinc-300 rounded-lg px-3 py-2 text-xs outline-none focus:border-gold">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 mb-1">Nomor WhatsApp / HP</label>
                        <input type="text" name="phone" id="rsvpPhone" value="{{ $guest->phone ?? '' }}" placeholder="08xxxxxxxxxx" class="w-full border border-zinc-300 rounded-lg px-3 py-2 text-xs outline-none focus:border-gold">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 mb-1">Status Kehadiran</label>
                        <select name="status" id="rsvpStatus" class="w-full border border-zinc-300 rounded-lg px-3 py-2 text-xs outline-none focus:border-gold">
                            <option value="attending">Hadir</option>
                            <option value="not_attending">Tidak Hadir</option>
                            <option value="maybe">Masih Ragu</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 mb-1">Jumlah Orang (Pax)</label>
                        <select name="pax" id="rn" class="w-full border border-zinc-300 rounded-lg px-3 py-2 text-xs outline-none focus:border-gold">
                            <option value="1">1 Orang</option>
                            <option value="2">2 Orang</option>
                            <option value="3">3 Orang</option>
                            <option value="4">4 Orang</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 mb-1">Pesan (Opsional)</label>
                        <textarea name="message" id="rsvpMessage" rows="2" placeholder="Pesan untuk mempelai..." class="w-full border border-zinc-300 rounded-lg px-3 py-2 text-xs outline-none focus:border-gold"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" class="cl bg-zinc-200 text-zinc-700 rounded-lg px-4 py-2 text-xs font-bold">Batal</button>
                        <button type="submit" id="rsvpSubmitBtn" class="bg-ink text-white rounded-lg px-4 py-2 text-xs font-bold hover:bg-black transition inline-flex items-center gap-1.5 shadow-md">
                            <iconify-icon icon="solar:check-circle-bold" class="text-sm text-gold"></iconify-icon>
                            <span>Kirim Konfirmasi</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ============ MODAL QR CHECK-IN ============ -->
        <div id="mQr" class="hidden fixed inset-0 z-[90] bg-black/65 backdrop-blur-xs flex items-center justify-center p-5">
            <div class="bg-white rounded-2xl p-6 text-center text-ink w-80 shadow-2xl border border-zinc-200" onclick="event.stopPropagation()">
                <div class="flex justify-between items-center pb-2 border-b border-zinc-200 mb-3">
                    <p class="text-xs font-bold text-zinc-700">QR Check-in Tamu</p>
                    <button class="cl text-zinc-400 hover:text-zinc-700 text-lg">
                        <iconify-icon icon="solar:close-circle-bold"></iconify-icon>
                    </button>
                </div>
                
                <p class="text-xs text-zinc-500">Tunjukkan QR ini saat tiba di lokasi acara</p>
                @php
                    $qrData = !empty($guest?->token) ? $guestInvitationUrl : ($invitation->slug . '-' . urlencode($guestName));
                    $qrApiUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=' . urlencode($qrData);
                @endphp
                <div class="w-44 h-44 mx-auto my-3 bg-white p-2 rounded-xl border border-zinc-300 shadow-inner flex items-center justify-center">
                    <img id="qrImg" src="{{ $qrApiUrl }}" alt="QR" class="w-full h-full object-contain">
                </div>
                <p class="guest text-sm font-bold text-zinc-800">{{ $guestName }}</p>
                @if(!empty($guest?->category))
                    <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full bg-zinc-100 text-zinc-700 text-[10px] font-semibold border border-zinc-300">
                        {{ ucfirst($guest->category) }}
                    </span>
                @endif
                <button class="cl mt-4 w-full bg-zinc-200 text-zinc-800 rounded-lg py-2 text-xs font-bold hover:bg-zinc-300 transition">Tutup</button>
            </div>
        </div>

        <!-- ============ LIGHTBOX GALERI ============ -->
        <div id="lb" class="hidden fixed inset-0 z-[100] bg-black/90 flex items-center justify-center p-4 cursor-zoom-out" onclick="this.classList.add('hidden')">
            <button class="absolute top-4 right-4 text-white text-3xl hover:opacity-80">
                <iconify-icon icon="solar:close-circle-bold"></iconify-icon>
            </button>
            <img id="lightboxImg" class="max-h-[90vh] max-w-full object-contain rounded-xl shadow-2xl" alt="" onclick="event.stopPropagation()">
        </div>
    </main>

    <script>
        const WEDDING_DATE = new Date('{{ $targetIso }}');
        const $ = s => document.querySelector(s);
        const $$ = s => [...document.querySelectorAll(s)];
        const sc = $('#sc');

        // Parameter tamu di URL fallback
        const urlParams = new URLSearchParams(location.search);
        const toParam = urlParams.get('to');
        if (toParam && $('#cover .guest').textContent === 'Tamu Undangan') {
            $$('.guest').forEach(e => e.textContent = toParam);
            if ($('#wn')) $('#wn').value = toParam;
            if ($('#rsvpName')) $('#rsvpName').value = toParam;
        }

        // Toast Helper
        function showToast(msg) {
            const toast = $('#toast');
            $('#toast-message').textContent = msg;
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 3500);
        }

        // Copy Text Helper
        function copyText(txt, successMsg) {
            if (navigator.clipboard) {
                navigator.clipboard.writeText(txt).then(() => {
                    showToast(successMsg || 'Berhasil disalin ke clipboard!');
                }).catch(() => fallbackCopy(txt, successMsg));
            } else {
                fallbackCopy(txt, successMsg);
            }
        }

        function fallbackCopy(txt, successMsg) {
            const ta = document.createElement('textarea');
            ta.value = txt;
            document.body.appendChild(ta);
            ta.select();
            document.execCommand('copy');
            document.body.removeChild(ta);
            showToast(successMsg || 'Berhasil disalin ke clipboard!');
        }

        // Partikel kunang-kunang di panel kiri desktop
        const ffContainer = $('#ff');
        if (ffContainer) {
            for (let i = 0; i < 34; i++) {
                const d = document.createElement('i');
                d.className = 'ff' + (i % 2 ? ' d' : '');
                const z = i % 2 ? 2 : 3 + Math.random() * 4;
                d.style.cssText = `left:${Math.random()*100}%;top:${Math.random()*100}%;width:${z}px;height:${z}px;animation-duration:${7+Math.random()*9}s;animation-delay:-${Math.random()*12}s`;
                ffContainer.appendChild(d);
            }
        }

        // Animasi reveal saat scroll
        let io;
        function startReveal() {
            io = new IntersectionObserver(es => {
                es.forEach(e => {
                    if (e.isIntersecting) {
                        e.target.classList.add('on');
                        io.unobserve(e.target);
                    }
                });
            }, { root: sc, threshold: 0.12 });
            $$('#sc .rv').forEach(e => io.observe(e));
        }

        // Buka Undangan & Play Musik
        let playing = false;
        const bgm = $('#bgm');
        $('#openBtn').onclick = () => {
            const c = $('#cover');
            c.style.transform = 'translateY(-100%)';
            document.body.classList.remove('overflow-hidden');
            setTimeout(() => {
                c.remove();
                startReveal();
                $('#nav').classList.remove('translate-y-24');
                $('#musicBtn').classList.remove('hidden');
                bgm.play().then(() => {
                    playing = true;
                    $('#musicIcon').setAttribute('icon', 'solar:music-note-slider-bold-duotone');
                }).catch(() => {});
            }, 900);
        };

        $('#musicBtn').onclick = () => {
            if (playing) {
                bgm.pause();
                $('#musicIcon').setAttribute('icon', 'solar:music-note-2-broken');
            } else {
                bgm.play();
                $('#musicIcon').setAttribute('icon', 'solar:music-note-slider-bold-duotone');
            }
            playing = !playing;
        };

        // Hitung Mundur Otomatis
        (function tick() {
            let t = Math.max(0, (WEDDING_DATE - Date.now()) / 1000);
            [['d', 86400], ['h', 3600], ['m', 60], ['s', 1]].forEach(([k, v]) => {
                const el = $('#' + k);
                if (el) el.textContent = String(Math.floor(t / v)).padStart(2, '0');
                t %= v;
            });
            setTimeout(tick, 1000);
        })();

        // Navigasi Bawah & Scrollspy
        const nvs = $$('.nv');
        nvs.forEach(b => {
            b.onclick = () => {
                const el = document.getElementById(b.dataset.t);
                if (el) el.scrollIntoView({ behavior: 'smooth' });
            };
        });

        const so = new IntersectionObserver(es => {
            es.forEach(e => {
                if (e.isIntersecting) {
                    nvs.forEach(b => b.classList.toggle('on', b.dataset.t === e.target.id));
                }
            });
        }, { root: sc, threshold: 0.35 });

        ['home', 'couple', 'gallery', 'gift', 'wishes'].forEach(id => {
            const el = document.getElementById(id);
            if (el) so.observe(el);
        });

        // Tombol Kembali ke Atas
        sc.addEventListener('scroll', () => {
            const t = $('#top');
            const s = sc.scrollTop > 400;
            t.style.opacity = s ? 1 : 0;
            t.style.pointerEvents = s ? 'auto' : 'none';
        });
        $('#top').onclick = () => sc.scrollTo({ top: 0, behavior: 'smooth' });

        // Modal Helpers
        const show = id => { $(id).classList.remove('hidden'); $(id).classList.add('flex'); };
        const hide = () => $$('#mRsvp,#mQr,#lb').forEach(m => { m.classList.add('hidden'); m.classList.remove('flex'); });
        $('#rsvpBtn').onclick = () => show('#mRsvp');
        $('#qrTab').onclick = () => show('#mQr');
        $$('.cl').forEach(b => b.onclick = hide);

        // Lightbox
        function openLightbox(src) {
            $('#lightboxImg').src = src;
            show('#lb');
        }

        // AJAX RSVP Submission
        $('#rsvpForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const btn = $('#rsvpSubmitBtn');
            btn.disabled = true;
            btn.innerHTML = `<iconify-icon icon="solar:spinner-bold" class="text-sm animate-spin"></iconify-icon><span>Menyimpan...</span>`;

            const formData = new FormData(this);
            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                hide();
                showToast(data.message || 'Konfirmasi kehadiran berhasil disimpan!');
            })
            .catch(() => {
                hide();
                showToast('Terima kasih, konfirmasi Anda berhasil dikirim.');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = `<iconify-icon icon="solar:check-circle-bold" class="text-sm text-gold"></iconify-icon><span>Kirim Konfirmasi</span>`;
            });
        });

        // AJAX Ucapan Submission
        $('#wishForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const btn = $('#wsend');
            const nama = $('#wn').value.trim();
            const pesan = $('#wt').value.trim();
            if (!nama || !pesan) return;

            btn.disabled = true;
            btn.innerHTML = `<iconify-icon icon="solar:spinner-bold" class="text-sm animate-spin"></iconify-icon><span>Mengirim...</span>`;

            const formData = new FormData(this);
            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                showToast(data.message || 'Ucapan Anda berhasil dikirim!');

                const emptyMsg = $('#emptyWishMsg');
                if (emptyMsg) emptyMsg.remove();

                const d = document.createElement('div');
                d.className = 'bg-zinc-600 rounded-xl p-3 text-[11px] rv on border border-zinc-500/50 animate-fadeIn';
                d.innerHTML = `
                    <div class="flex items-center justify-between">
                        <b class="text-white">${nama.replace(/[&<>'"]/g, '')}</b>
                        <span class="text-[9px] text-zinc-300 opacity-80">Baru saja</span>
                    </div>
                    <p class="mt-1 text-zinc-200 leading-relaxed">${pesan.replace(/[&<>'"]/g, '')}</p>
                `;
                $('#wl').prepend(d);
                $('#wt').value = '';
            })
            .catch(() => {
                showToast('Ucapan Anda berhasil dikirim!');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = `<iconify-icon icon="solar:chat-round-dots-bold-duotone" class="text-base text-gold"></iconify-icon><span>Kirim Ucapan</span>`;
            });
        });
    </script>
</body>
</html>
