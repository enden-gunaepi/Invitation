<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $isDemoMode = (bool) ($demoMode ?? false);
        $personalization = $personalization ?? ['greeting' => 'Tamu Undangan', 'cta' => 'Konfirmasi Kehadiran', 'rsvp_hint' => 'Mohon konfirmasi kehadiran Anda.'];
        
        $groomName = $invitation->groom_name ?: 'Raka';
        $brideName = $invitation->bride_name ?: 'Vea';
        $coupleName = trim(($invitation->bride_name ?? 'Vea') . ' & ' . ($invitation->groom_name ?? 'Raka'));
        
        $guestName = $guest->name ?? (request('to') ?: 'Tamu Undangan');
        $guestInvitationUrl = !empty($guest?->token) ? $guest->getInvitationUrl() : $invitation->getPublicUrl();

        $coverPhoto = $invitation->cover_photo ? asset('storage/' . $invitation->cover_photo) : null;
        $groomPhoto = $invitation->groom_photo ? asset('storage/' . $invitation->groom_photo) : null;
        $bridePhoto = $invitation->bride_photo ? asset('storage/' . $invitation->bride_photo) : null;
        $igStoryPhoto = $invitation->ig_story_photo ? asset('storage/' . $invitation->ig_story_photo) : null;
        $musicUrl = $invitation->music_url ? asset('storage/' . $invitation->music_url) : null;

        $galleryPhotos = $invitation->photos;
        $heroPhoto = $coverPhoto ?: ($galleryPhotos->first() ? asset('storage/' . $galleryPhotos->first()->file_path) : null);
        $desktopBgPhoto = $galleryPhotos->count() > 1 
            ? asset('storage/' . $galleryPhotos[1]->file_path) 
            : ($coverPhoto ?: ($galleryPhotos->first() ? asset('storage/' . $galleryPhotos->first()->file_path) : null));

        $eventDateText = $invitation->event_date ? $invitation->event_date->translatedFormat('l, d F Y') : 'Rabu, 30 Desember 2026';
        $eventDateShort = $invitation->event_date ? $invitation->event_date->translatedFormat('d F Y') : '30 Desember 2026';
        $eventDateIso = $invitation->event_date ? $invitation->event_date->format('Y-m-d') : '2026-12-30';
        $eventTimeIso = $invitation->event_time ? \Carbon\Carbon::parse($invitation->event_time)->format('H:i:s') : '08:00:00';
        $targetIso = $eventDateIso . 'T' . $eventTimeIso . '+07:00';

        $openingText = $invitation->opening_text 
            ?: 'Dan di antara tanda-tanda kekuasaan-Nya ialah Dia menciptakan pasangan-pasangan untukmu dari jenismu sendiri, supaya kamu merasa tenteram kepadanya, dan dijadikan-Nya di antaramu rasa kasih dan sayang.';
        $closingText = $invitation->closing_text 
            ?: 'Merupakan kebahagiaan bagi kami apabila Bapak/Ibu/Saudara/i berkenan hadir dan memberikan doa restu.';
        $footerText = $invitation->footer_text 
            ?: 'Dibuat dengan segenap cinta oleh ' . config('app.name', 'GNVisual');

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

    <title>{{ $invitation->title ?: ('The Wedding of ' . $coupleName) }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400&family=Marcellus&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        serif: ['Marcellus', 'serif'],
                        body: ['Cormorant Garamond', 'serif']
                    },
                    colors: {
                        cream: '#FFF5F5',
                        peach: '#F7D7D1',
                        rose: '#E3B5BE',
                        charcoal: '#494949',
                        darkcharcoal: '#242223',
                        brown: '#332E30'
                    }
                }
            }
        };
    </script>
    <style>
        html { scroll-behavior: smooth; }
        body { font-family: 'Cormorant Garamond', serif; background: #242223; color: #FFF5F5; }
        .ph { background: linear-gradient(135deg, #494949, #2B292A); }
        .reveal { opacity: 0; transform: translateY(40px); transition: all 1s cubic-bezier(.2, .7, .2, 1); }
        .reveal.show { opacity: 1; transform: none; }
        .reveal-l { opacity: 0; transform: translateX(-50px); transition: all 1s; }
        .reveal-l.show { opacity: 1; transform: none; }
        .reveal-r { opacity: 0; transform: translateX(50px); transition: all 1s; }
        .reveal-r.show { opacity: 1; transform: none; }
        .zoom { opacity: 0; transform: scale(.85); transition: all 1s; }
        .zoom.show { opacity: 1; transform: none; }
        @keyframes kenburns { from { transform: scale(1); } to { transform: scale(1.12); } }
        .kb { animation: kenburns 18s ease-in-out infinite alternate; }
        @keyframes fall {
            0% { transform: translate3d(0, -10vh, 0) rotate(0); opacity: 0; }
            10% { opacity: .85; }
            100% { transform: translate3d(60px, 110vh, 0) rotate(360deg); opacity: 0; }
        }
        .petal {
            position: fixed; top: 0; width: 8px; height: 8px; border-radius: 50% 0 50% 0;
            background: #E3B5BE; pointer-events: none; z-index: 5; animation: fall linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        @keyframes floaty { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-8px); } }
        .floaty { animation: floaty 3s ease-in-out infinite; }
        #panel::-webkit-scrollbar { width: 0; }
        #cover { transition: transform 1.2s cubic-bezier(.7, 0, .2, 1); }
        #cover.hide { transform: translateY(-100%); pointer-events: none; }
        .glass { background: rgba(46, 43, 44, 0.5); backdrop-filter: blur(10px); border: 1px solid rgba(227, 181, 190, 0.25); }
        .toast-msg {
            position: fixed; bottom: 80px; left: 50%; transform: translateX(-50%);
            background: #FFF5F5; color: #494949; padding: 10px 20px; border-radius: 9999px;
            font-size: 13px; font-weight: 600; z-index: 100; box-shadow: 0 10px 30px rgba(0,0,0,0.35);
            border: 1px solid #E3B5BE; opacity: 0; pointer-events: none; transition: opacity 0.3s ease;
        }
        .toast-msg.show { opacity: 1; }
        iconify-icon { display: inline-flex; vertical-align: middle; }
    </style>
</head>
<body class="overflow-hidden">

    <!-- DEMO BADGE -->
    @if($isDemoMode)
        <div class="fixed left-3 top-3 z-[95] bg-rose text-darkcharcoal text-xs font-bold px-3 py-1 rounded-full shadow-lg flex items-center gap-1.5 border border-cream/50">
            <iconify-icon icon="solar:info-circle-bold" class="text-sm"></iconify-icon>
            Demo Mode
        </div>
    @endif

    <!-- TOAST NOTIFICATION -->
    <div id="toast" class="toast-msg flex items-center gap-1.5">
        <iconify-icon icon="solar:check-circle-bold" class="text-base text-rose"></iconify-icon>
        <span id="toastText">Tersalin ke clipboard</span>
    </div>

    <!-- LOADING SPINNER -->
    <div id="loader" class="fixed inset-0 z-[100] bg-darkcharcoal flex items-center justify-center transition-opacity duration-700">
        <div class="w-8 h-8 border-2 border-rose/30 border-t-rose rounded-full" style="animation: spin 1s linear infinite"></div>
    </div>

    <!-- LATAR DESKTOP (kiri, hanya tampil pada layar besar lg) -->
    <div class="hidden lg:block fixed inset-0 overflow-hidden ph">
        @if($desktopBgPhoto)
            <img src="{{ $desktopBgPhoto }}" class="kb w-full h-full object-cover" alt="{{ $coupleName }}" onerror="this.style.display='none'">
        @endif
        <div class="absolute inset-0 bg-darkcharcoal/60"></div>
        <div class="absolute left-16 bottom-16 max-w-lg z-10">
            <p class="font-serif tracking-[.3em] text-sm text-peach uppercase">THE WEDDING OF</p>
            <h1 class="font-serif text-6xl mt-3 text-cream leading-tight">{{ $coupleName }}</h1>
            <p class="mt-4 text-peach text-lg flex items-center gap-2">
                <iconify-icon icon="solar:calendar-date-linear" class="text-xl text-rose"></iconify-icon>
                {{ $eventDateText }}
            </p>
            @if($invitation->venue_name)
                <p class="mt-1 text-sm text-cream/70 flex items-center gap-2">
                    <iconify-icon icon="solar:map-point-linear" class="text-base text-rose"></iconify-icon>
                    {{ $invitation->venue_name }}
                </p>
            @endif
        </div>
    </div>

    <!-- KOLOM UNDANGAN MOBILE / DESKTOP KANAN -->
    <main class="fixed inset-y-0 right-0 w-full lg:w-[450px] bg-brown shadow-2xl flex flex-col border-l border-rose/10">

        <!-- ================= COVER ================= -->
        <section id="cover" class="absolute inset-0 z-40 ph overflow-hidden">
            @if($coverPhoto ?: $heroPhoto)
                <img src="{{ $coverPhoto ?: $heroPhoto }}" class="kb absolute inset-0 w-full h-full object-cover" alt="Cover" onerror="this.style.display='none'">
            @endif
            <div class="absolute inset-0 bg-gradient-to-b from-darkcharcoal/40 via-transparent to-darkcharcoal/95"></div>
            <p class="absolute top-8 w-full text-center font-serif text-sm tracking-widest zoom show text-peach uppercase">
                The Wedding of
            </p>
            <div class="absolute bottom-0 w-full px-8 pb-10 text-center">
                <h1 class="font-serif text-5xl leading-tight reveal show text-cream">
                    {!! nl2br(e($brideName)) !!}<br>&amp;<br>{!! nl2br(e($groomName)) !!}
                </h1>
                <p class="font-serif text-xs mt-4 tracking-wider text-peach flex items-center justify-center gap-1.5">
                    <iconify-icon icon="solar:calendar-linear" class="text-sm text-rose"></iconify-icon>
                    {{ $eventDateText }}
                </p>
                
                <!-- Countdown Cover -->
                <div id="cdCover" class="flex justify-center gap-3 mt-4"></div>

                <div class="text-left mt-8 text-sm">
                    <p class="text-peach text-xs uppercase tracking-wider flex items-center gap-1">
                        <iconify-icon icon="solar:user-heart-linear" class="text-xs"></iconify-icon>
                        {{ $personalization['greeting'] ?? 'Kepada Yth:' }}
                    </p>
                    <p id="guest" class="font-semibold text-lg text-cream mt-0.5">{{ $guestName }}</p>
                </div>

                <button id="openBtn" type="button" class="floaty mt-5 mx-auto flex items-center gap-2 bg-cream text-charcoal rounded-full px-7 py-3 font-serif text-sm hover:bg-peach active:scale-95 transition shadow-lg border border-rose/30">
                    <iconify-icon icon="solar:letter-opened-linear" class="text-lg text-rose"></iconify-icon>
                    <span class="font-semibold">Buka Undangan</span>
                </button>
            </div>
        </section>

        <!-- ================= ISI UNDANGAN (SCROLL PANEL) ================= -->
        <div id="panel" class="h-full overflow-y-auto relative flex-1">

            <!-- 1. HERO SECTION -->
            <section id="home" class="relative min-h-screen ph overflow-hidden flex items-end">
                @if($heroPhoto ?: $coverPhoto)
                    <img src="{{ $heroPhoto ?: $coverPhoto }}" class="kb absolute inset-0 w-full h-full object-cover" alt="Hero" onerror="this.style.display='none'">
                @endif
                <div class="absolute inset-0 bg-gradient-to-b from-darkcharcoal/30 via-transparent to-darkcharcoal/95"></div>
                <p class="absolute top-8 w-full text-center font-serif text-sm tracking-widest text-peach uppercase">
                    The Wedding of
                </p>
                <div class="relative w-full text-center px-6 pb-20 pt-32">
                    <h2 class="font-serif text-5xl leading-tight zoom text-cream">
                        {{ $brideName }}<br>&amp;<br>{{ $groomName }}
                    </h2>
                    <p class="text-sm mt-3 font-serif text-peach flex items-center justify-center gap-1.5">
                        <iconify-icon icon="solar:calendar-date-linear" class="text-base text-rose"></iconify-icon>
                        {{ $eventDateText }}
                    </p>
                    <div id="cdHero" class="flex justify-center gap-3 mt-4"></div>
                    <div class="mt-8 flex justify-center gap-3">
                        @if($calendarUrl)
                            <a href="{{ $calendarUrl }}" target="_blank" class="glass px-4 py-2 rounded-full text-xs hover:bg-rose/20 transition flex items-center gap-1.5 text-cream border border-rose/30">
                                <iconify-icon icon="solar:calendar-add-linear" class="text-base text-peach"></iconify-icon>
                                <span>Simpan Tanggal</span>
                            </a>
                        @endif
                        <a href="#acara" class="glass px-4 py-2 rounded-full text-xs hover:bg-rose/20 transition flex items-center gap-1.5 text-cream border border-rose/30">
                            <iconify-icon icon="solar:map-point-linear" class="text-base text-peach"></iconify-icon>
                            <span>Lihat Lokasi</span>
                        </a>
                    </div>
                </div>
            </section>

            <!-- 2. QUOTE / PEMBUKA -->
            <section class="bg-cream text-charcoal px-8 py-14 text-center relative shadow-inner border-y border-rose/20">
                <div class="text-7xl font-serif leading-none opacity-40 text-rose reveal">“</div>
                <p class="italic text-base md:text-lg leading-relaxed reveal text-charcoal/90">
                    {!! nl2br(e($openingText)) !!}
                </p>
                <p class="mt-4 text-xs font-semibold tracking-widest uppercase reveal text-rose flex items-center justify-center gap-1">
                    <iconify-icon icon="solar:book-bookmark-minimalistic-linear" class="text-sm"></iconify-icon>
                    <span>QS. Ar-Rum: 21</span>
                </p>
            </section>

            <!-- 3. MEMPELAI -->
            <section id="mempelai" class="relative px-6 py-14 text-center ph">
                @if($desktopBgPhoto)
                    <img src="{{ $desktopBgPhoto }}" class="absolute inset-0 w-full h-full object-cover opacity-15" alt="" onerror="this.style.display='none'">
                @endif
                <div class="relative">
                    <p class="text-xs uppercase tracking-widest text-peach reveal">Assalamu’alaikum Wr. Wb.</p>
                    <p class="text-sm mt-2 max-w-sm mx-auto reveal leading-relaxed text-cream/90">
                        Tanpa mengurangi rasa hormat, kami bermaksud mengundang Bapak/Ibu/Saudara/i untuk hadir dalam momen bahagia pernikahan kami:
                    </p>

                    <!-- Mempelai Wanita -->
                    <div class="mt-10 reveal-l">
                        <div class="w-56 h-72 mx-auto rounded-t-full overflow-hidden ph border-4 border-rose/30 shadow-2xl flex items-center justify-center">
                            @if($bridePhoto)
                                <img src="{{ $bridePhoto }}" class="w-full h-full object-cover" alt="{{ $brideName }}" onerror="this.style.display='none'">
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center text-rose/50 bg-darkcharcoal/60">
                                    <iconify-icon icon="solar:user-heart-rounded-linear" class="text-6xl"></iconify-icon>
                                    <span class="text-xs font-serif mt-2 tracking-wider text-peach">Mempelai Wanita</span>
                                </div>
                            @endif
                        </div>
                        <h3 class="font-serif text-3xl mt-5 text-cream">{{ $invitation->bride_name ?: 'Mempelai Wanita' }}</h3>
                        @if($invitation->bride_parent_name)
                            <p class="text-xs text-peach mt-1.5 max-w-xs mx-auto">{{ $invitation->bride_parent_name }}</p>
                        @endif
                        <div class="flex justify-center gap-3 mt-3 text-sm">
                            @if($invitation->bride_instagram)
                                <a href="{{ Str::startsWith($invitation->bride_instagram, 'http') ? $invitation->bride_instagram : 'https://instagram.com/' . ltrim($invitation->bride_instagram, '@') }}" target="_blank" class="glass px-3 py-1.5 rounded-full text-xs hover:bg-rose/20 transition flex items-center gap-1.5 text-cream border border-rose/30">
                                    <iconify-icon icon="ri:instagram-line" class="text-sm text-rose"></iconify-icon>
                                    <span>{{ Str::startsWith($invitation->bride_instagram, '@') ? $invitation->bride_instagram : '@Instagram' }}</span>
                                </a>
                            @endif
                            @if($invitation->bride_facebook)
                                <a href="{{ Str::startsWith($invitation->bride_facebook, 'http') ? $invitation->bride_facebook : 'https://facebook.com/' . $invitation->bride_facebook }}" target="_blank" class="glass px-3 py-1.5 rounded-full text-xs hover:bg-rose/20 transition flex items-center gap-1.5 text-cream border border-rose/30">
                                    <iconify-icon icon="ri:facebook-circle-line" class="text-sm text-rose"></iconify-icon>
                                    <span>Facebook</span>
                                </a>
                            @endif
                        </div>
                    </div>

                    <p class="font-serif text-5xl my-8 zoom text-rose">&amp;</p>

                    <!-- Mempelai Pria -->
                    <div class="reveal-r">
                        <div class="w-56 h-72 mx-auto rounded-t-full overflow-hidden ph border-4 border-rose/30 shadow-2xl flex items-center justify-center">
                            @if($groomPhoto)
                                <img src="{{ $groomPhoto }}" class="w-full h-full object-cover" alt="{{ $groomName }}" onerror="this.style.display='none'">
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center text-rose/50 bg-darkcharcoal/60">
                                    <iconify-icon icon="solar:user-rounded-linear" class="text-6xl"></iconify-icon>
                                    <span class="text-xs font-serif mt-2 tracking-wider text-peach">Mempelai Pria</span>
                                </div>
                            @endif
                        </div>
                        <h3 class="font-serif text-3xl mt-5 text-cream">{{ $invitation->groom_name ?: 'Mempelai Pria' }}</h3>
                        @if($invitation->groom_parent_name)
                            <p class="text-xs text-peach mt-1.5 max-w-xs mx-auto">{{ $invitation->groom_parent_name }}</p>
                        @endif
                        <div class="flex justify-center gap-3 mt-3 text-sm">
                            @if($invitation->groom_instagram)
                                <a href="{{ Str::startsWith($invitation->groom_instagram, 'http') ? $invitation->groom_instagram : 'https://instagram.com/' . ltrim($invitation->groom_instagram, '@') }}" target="_blank" class="glass px-3 py-1.5 rounded-full text-xs hover:bg-rose/20 transition flex items-center gap-1.5 text-cream border border-rose/30">
                                    <iconify-icon icon="ri:instagram-line" class="text-sm text-rose"></iconify-icon>
                                    <span>{{ Str::startsWith($invitation->groom_instagram, '@') ? $invitation->groom_instagram : '@Instagram' }}</span>
                                </a>
                            @endif
                            @if($invitation->groom_facebook)
                                <a href="{{ Str::startsWith($invitation->groom_facebook, 'http') ? $invitation->groom_facebook : 'https://facebook.com/' . $invitation->groom_facebook }}" target="_blank" class="glass px-3 py-1.5 rounded-full text-xs hover:bg-rose/20 transition flex items-center gap-1.5 text-cream border border-rose/30">
                                    <iconify-icon icon="ri:facebook-circle-line" class="text-sm text-rose"></iconify-icon>
                                    <span>Facebook</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </section>

            <!-- 4. ACARA -->
            <section id="acara" class="relative px-6 py-14 text-center ph">
                <div class="relative space-y-6">
                    <h2 class="font-serif text-3xl reveal text-cream">Rangkaian Acara</h2>
                    <p class="text-sm reveal text-peach max-w-sm mx-auto">
                        Dengan memohon rahmat dan ridho Allah SWT, acara akan dilaksanakan pada:
                    </p>

                    @if($hasEvents)
                        @foreach($invitation->events as $event)
                            @php
                                $eventDateFormatted = $event->event_date ? \Carbon\Carbon::parse($event->event_date)->translatedFormat('l, d F Y') : $eventDateText;
                                $eventTimeStart = $event->event_time ? \Carbon\Carbon::parse($event->event_time)->format('H:i') : null;
                                $eventTimeEnd = $event->event_end_time ? \Carbon\Carbon::parse($event->event_end_time)->format('H:i') : 'Selesai';
                                $eventTimeFormatted = $eventTimeStart ? ($eventTimeStart . ' - ' . $eventTimeEnd . ' WIB') : 'Waktu menyusul';
                                $eventVenue = $event->venue_name ?: $invitation->venue_name;
                                $eventAddress = $event->venue_address ?: $invitation->venue_address;
                                $eventMap = $event->venue_maps_url ?: $mapsTrackedUrl;
                            @endphp
                            <div class="glass rounded-2xl p-6 reveal text-center shadow-xl border border-rose/25">
                                <h3 class="font-serif text-2xl text-cream">{{ $event->event_name }}</h3>
                                <p class="mt-2 text-sm text-peach">
                                    {{ $eventDateFormatted }}<br>
                                    Pukul {{ $eventTimeFormatted }}
                                </p>
                                @if($eventVenue)
                                    <p class="mt-3 text-sm font-semibold text-cream flex items-center justify-center gap-1.5">
                                        <iconify-icon icon="solar:buildings-2-linear" class="text-sm text-rose"></iconify-icon>
                                        {{ $eventVenue }}
                                    </p>
                                @endif
                                @if($eventAddress)
                                    <p class="text-xs text-cream/70 mt-1">{{ $eventAddress }}</p>
                                @endif
                                @if($event->event_description)
                                    <p class="text-xs italic text-peach/80 mt-2">{{ $event->event_description }}</p>
                                @endif
                                <div class="flex gap-2 justify-center mt-5 text-xs">
                                    @if($eventMap)
                                        <a href="{{ $eventMap }}" target="_blank" class="bg-charcoal hover:bg-darkcharcoal text-cream rounded-full px-4 py-2 border border-rose/30 transition flex items-center gap-1.5">
                                            <iconify-icon icon="solar:map-point-wave-bold" class="text-sm text-rose"></iconify-icon>
                                            <span>Navigasi Map</span>
                                        </a>
                                    @endif
                                    @if($calendarUrl)
                                        <a href="{{ $calendarUrl }}" target="_blank" class="bg-rose/20 hover:bg-rose/30 text-cream rounded-full px-4 py-2 border border-rose/40 transition flex items-center gap-1.5">
                                            <iconify-icon icon="solar:calendar-add-bold" class="text-sm text-peach"></iconify-icon>
                                            <span>Simpan Tanggal</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @else
                        <!-- Default Rundown Fallback dari data utama invitation -->
                        <div class="glass rounded-2xl p-6 reveal text-center shadow-xl border border-rose/25">
                            <h3 class="font-serif text-2xl text-cream">Akad Nikah</h3>
                            <p class="mt-2 text-sm text-peach">
                                {{ $eventDateText }}<br>
                                Pukul {{ $invitation->event_time ? \Carbon\Carbon::parse($invitation->event_time)->format('H:i') : '08:00' }} WIB – Selesai
                            </p>
                            @if($invitation->venue_name)
                                <p class="mt-3 text-sm font-semibold text-cream flex items-center justify-center gap-1.5">
                                    <iconify-icon icon="solar:buildings-2-linear" class="text-sm text-rose"></iconify-icon>
                                    {{ $invitation->venue_name }}
                                </p>
                            @endif
                            @if($invitation->venue_address)
                                <p class="text-xs text-cream/70 mt-1">{{ $invitation->venue_address }}</p>
                            @endif
                            <div class="flex gap-2 justify-center mt-5 text-xs">
                                <a href="{{ $mapsTrackedUrl }}" target="_blank" class="bg-charcoal hover:bg-darkcharcoal text-cream rounded-full px-4 py-2 border border-rose/30 transition flex items-center gap-1.5">
                                    <iconify-icon icon="solar:map-point-wave-bold" class="text-sm text-rose"></iconify-icon>
                                    <span>Navigasi Map</span>
                                </a>
                                @if($calendarUrl)
                                    <a href="{{ $calendarUrl }}" target="_blank" class="bg-rose/20 hover:bg-rose/30 text-cream rounded-full px-4 py-2 border border-rose/40 transition flex items-center gap-1.5">
                                        <iconify-icon icon="solar:calendar-add-bold" class="text-sm text-peach"></iconify-icon>
                                        <span>Simpan Tanggal</span>
                                    </a>
                                @endif
                            </div>
                        </div>

                        <div class="glass rounded-2xl p-6 reveal text-center shadow-xl border border-rose/25">
                            <h3 class="font-serif text-2xl text-cream">Resepsi</h3>
                            <p class="mt-2 text-sm text-peach">
                                {{ $eventDateText }}<br>
                                Pukul {{ $invitation->event_time ? \Carbon\Carbon::parse($invitation->event_time)->addHours(2)->format('H:i') : '11:00' }} WIB – Selesai
                            </p>
                            @if($invitation->venue_name)
                                <p class="mt-3 text-sm font-semibold text-cream flex items-center justify-center gap-1.5">
                                    <iconify-icon icon="solar:buildings-2-linear" class="text-sm text-rose"></iconify-icon>
                                    {{ $invitation->venue_name }}
                                </p>
                            @endif
                            @if($invitation->venue_address)
                                <p class="text-xs text-cream/70 mt-1">{{ $invitation->venue_address }}</p>
                            @endif
                            <div class="flex gap-2 justify-center mt-5 text-xs">
                                <a href="{{ $mapsTrackedUrl }}" target="_blank" class="bg-charcoal hover:bg-darkcharcoal text-cream rounded-full px-4 py-2 border border-rose/30 transition flex items-center gap-1.5">
                                    <iconify-icon icon="solar:map-point-wave-bold" class="text-sm text-rose"></iconify-icon>
                                    <span>Navigasi Map</span>
                                </a>
                                @if($calendarUrl)
                                    <a href="{{ $calendarUrl }}" target="_blank" class="bg-rose/20 hover:bg-rose/30 text-cream rounded-full px-4 py-2 border border-rose/40 transition flex items-center gap-1.5">
                                        <iconify-icon icon="solar:calendar-add-bold" class="text-sm text-peach"></iconify-icon>
                                        <span>Simpan Tanggal</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Live Streaming (bila diaktifkan dari dashboard) -->
                    @if($invitation->livestream_enabled && $invitation->livestream_url)
                        <div class="glass rounded-2xl p-6 reveal text-left shadow-xl border border-rose/30">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-rose animate-pulse"></span>
                                <h3 class="font-serif text-2xl text-cream flex items-center gap-2">
                                    <iconify-icon icon="solar:videocamera-record-bold-duotone" class="text-2xl text-rose"></iconify-icon>
                                    <span>{{ $invitation->livestream_label ?: 'Live Streaming' }}</span>
                                </h3>
                            </div>
                            <p class="text-sm mt-2 text-cream/80">
                                Acara ini disiarkan secara langsung melalui tautan di bawah ini. Anda dapat bergabung bersama kami secara virtual.
                            </p>
                            <a href="{{ $invitation->livestream_url }}" target="_blank" class="inline-flex items-center gap-2 mt-4 bg-cream text-charcoal font-semibold rounded-full px-5 py-2.5 text-xs hover:bg-peach transition shadow">
                                <iconify-icon icon="solar:play-circle-bold-duotone" class="text-base text-rose"></iconify-icon>
                                <span>Tonton Live Streaming</span>
                            </a>
                        </div>
                    @endif
                </div>
            </section>

            <!-- 5. GALERI FOTO -->
            @if($galleryPhotos->count() > 0)
                <section id="galeri" class="px-4 py-14 bg-brown text-center">
                    <h2 class="font-serif text-3xl reveal text-cream">Galeri Momen</h2>
                    <p class="text-xs text-peach mt-1 reveal">Potret kebersamaan perjalanan cinta kami</p>

                    <!-- Foto Utama Galeri -->
                    <div class="mt-6 aspect-video ph rounded-xl overflow-hidden relative zoom cursor-pointer shadow-2xl group border border-rose/20" onclick="openLightbox('{{ asset('storage/' . $galleryPhotos->first()->file_path) }}')">
                        <img src="{{ asset('storage/' . $galleryPhotos->first()->file_path) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-700" alt="Galeri Utama" onerror="this.style.display='none'">
                        <div class="absolute inset-0 bg-darkcharcoal/30 group-hover:bg-darkcharcoal/50 transition flex items-center justify-center opacity-0 group-hover:opacity-100">
                            <iconify-icon icon="solar:magnifer-zoom-in-linear" class="text-3xl text-cream drop-shadow-md"></iconify-icon>
                        </div>
                    </div>

                    <!-- Grid Foto Galeri -->
                    <div class="grid grid-cols-2 gap-2 mt-2">
                        @foreach($galleryPhotos->slice(1, 6) as $photo)
                            <div class="aspect-square ph overflow-hidden rounded-lg zoom cursor-pointer shadow-lg group relative border border-rose/20" onclick="openLightbox('{{ asset('storage/' . $photo->file_path) }}')">
                                <img src="{{ asset('storage/' . $photo->file_path) }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-700" alt="{{ $photo->caption ?: 'Galeri Foto' }}" loading="lazy" onerror="this.style.display='none'">
                                <div class="absolute inset-0 bg-darkcharcoal/30 group-hover:bg-darkcharcoal/50 transition flex items-center justify-center opacity-0 group-hover:opacity-100">
                                    <iconify-icon icon="solar:magnifer-zoom-in-linear" class="text-2xl text-cream drop-shadow-md"></iconify-icon>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- 6. LOVE STORY (bila diisi di dashboard) -->
            @if($invitation->loveStories && $invitation->loveStories->count() > 0)
                <section class="px-6 py-14 bg-cream text-charcoal border-y border-rose/20">
                    <h2 class="font-serif text-3xl text-center reveal text-charcoal">Cerita Cinta</h2>
                    <p class="text-xs text-center text-charcoal/70 mt-1 reveal">Kilas balik bagaimana cerita kami dimulai</p>
                    <div class="mt-8 space-y-4">
                        @foreach($invitation->loveStories as $story)
                            <div class="bg-white rounded-xl p-5 shadow-md reveal border border-rose/20">
                                @if($story->photo_path)
                                    <div class="aspect-video w-full rounded-lg overflow-hidden mb-3 ph">
                                        <img src="{{ asset('storage/' . $story->photo_path) }}" alt="{{ $story->title }}" class="w-full h-full object-cover" onerror="this.style.display='none'">
                                    </div>
                                @endif
                                <span class="inline-flex items-center gap-1 bg-rose text-darkcharcoal text-[10px] font-bold px-2.5 py-0.5 rounded-full">
                                    <iconify-icon icon="solar:calendar-minimalistic-linear" class="text-xs"></iconify-icon>
                                    {{ $story->year }}
                                </span>
                                <h3 class="font-serif text-lg font-semibold mt-2 text-charcoal">{{ $story->title }}</h3>
                                <p class="text-xs leading-relaxed text-charcoal/80 mt-1">{{ $story->description }}</p>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- 7. INSTAGRAM STORY & DRESSCODE -->
            <section class="px-6 py-14 text-center bg-cream text-charcoal">
                <!-- Instagram Story -->
                <div class="reveal">
                    <h2 class="font-serif text-2xl flex items-center justify-center gap-2 text-charcoal">
                        <iconify-icon icon="ri:instagram-line" class="text-2xl text-rose"></iconify-icon>
                        <span>Instagram Stories</span>
                    </h2>
                    <p class="text-xs mt-2 text-charcoal/80 max-w-xs mx-auto">
                        Bagikan momen bahagia Anda dengan story Instagram dan tandai akun kami.
                    </p>
                    @if($igStoryPhoto)
                        <a href="{{ route('invitation.ig-story.download', $invitation->slug) }}" class="mt-4 inline-flex items-center gap-3 bg-white hover:bg-peach/30 rounded-xl p-3 text-left shadow text-xs transition border border-rose/30 text-charcoal">
                            <span class="w-10 h-10 rounded-full bg-peach/40 flex items-center justify-center flex-shrink-0 text-xl text-rose">
                                <iconify-icon icon="solar:camera-bold-duotone"></iconify-icon>
                            </span>
                            <span>Download Template<br><b class="text-sm">Instagram Story</b></span>
                        </a>
                    @else
                        <div class="mt-4 inline-flex items-center gap-3 bg-white rounded-xl p-3 text-left shadow text-xs border border-rose/30 text-charcoal">
                            <span class="w-10 h-10 rounded-full bg-peach/40 flex items-center justify-center flex-shrink-0 text-xl text-rose">
                                <iconify-icon icon="ri:instagram-line"></iconify-icon>
                            </span>
                            <span>Sertakan tag momen bahagia Anda di story Instagram!</span>
                        </div>
                    @endif
                </div>

                <!-- Dresscode -->
                <div class="mt-10 bg-white rounded-2xl p-6 shadow-md reveal border border-rose/20">
                    <h2 class="font-serif text-2xl tracking-widest uppercase flex items-center justify-center gap-2 text-charcoal">
                        <iconify-icon icon="solar:t-shirt-linear" class="text-xl text-rose"></iconify-icon>
                        <span>DRESSCODE</span>
                    </h2>
                    <p class="text-xs mt-2 text-charcoal/80">Mohon berpakaian sopan dan sesuai dengan nuansa warna berikut:</p>
                    
                    <!-- Exact 4 Colors Palette -->
                    <div class="flex justify-center gap-3 mt-5">
                        <div class="flex flex-col items-center">
                            <span class="w-9 h-9 rounded-full bg-[#FFF5F5] border-2 border-rose/40 shadow" title="Cream Blush"></span>
                            <span class="text-[10px] text-charcoal/70 mt-1">Blush</span>
                        </div>
                        <div class="flex flex-col items-center">
                            <span class="w-9 h-9 rounded-full bg-[#F7D7D1] border border-stone-200 shadow" title="Soft Peach"></span>
                            <span class="text-[10px] text-charcoal/70 mt-1">Peach</span>
                        </div>
                        <div class="flex flex-col items-center">
                            <span class="w-9 h-9 rounded-full bg-[#E3B5BE] border border-stone-200 shadow" title="Dusty Rose"></span>
                            <span class="text-[10px] text-charcoal/70 mt-1">Rose</span>
                        </div>
                        <div class="flex flex-col items-center">
                            <span class="w-9 h-9 rounded-full bg-[#494949] shadow" title="Charcoal Slate"></span>
                            <span class="text-[10px] text-charcoal/70 mt-1">Charcoal</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 8. PROTOKOL KESEHATAN -->
            <section class="px-6 py-14 text-center bg-brown border-t border-rose/10">
                <h2 class="font-serif text-2xl reveal text-cream flex items-center justify-center gap-2">
                    <iconify-icon icon="solar:shield-check-linear" class="text-2xl text-rose"></iconify-icon>
                    <span>Protokol Kesehatan</span>
                </h2>
                <p class="text-xs text-peach mt-1 reveal">Demi kenyamanan bersama, para tamu dimohon memperhatikan:</p>
                <div class="grid grid-cols-2 gap-3 mt-6 text-xs">
                    <div class="glass rounded-xl p-4 reveal-l flex flex-col items-center">
                        <iconify-icon icon="healthicons:mask-outline" class="text-3xl mb-1.5 text-peach"></iconify-icon>
                        <span class="text-cream">Gunakan Masker</span>
                    </div>
                    <div class="glass rounded-xl p-4 reveal-r flex flex-col items-center">
                        <iconify-icon icon="solar:hand-stars-linear" class="text-3xl mb-1.5 text-peach"></iconify-icon>
                        <span class="text-cream">Cuci Tangan</span>
                    </div>
                    <div class="glass rounded-xl p-4 reveal-l flex flex-col items-center">
                        <iconify-icon icon="solar:users-group-two-rounded-linear" class="text-3xl mb-1.5 text-peach"></iconify-icon>
                        <span class="text-cream">Jaga Jarak</span>
                    </div>
                    <div class="glass rounded-xl p-4 reveal-r flex flex-col items-center">
                        <iconify-icon icon="solar:temperature-linear" class="text-3xl mb-1.5 text-peach"></iconify-icon>
                        <span class="text-cream">Cek Suhu Tubuh</span>
                    </div>
                </div>
            </section>

            <!-- 9. TANDA KASIH / REKENING & KADO -->
            @if($hasBankAccounts || !empty($invitation->gift_address))
                <section id="kado" class="relative px-6 py-14 text-center ph">
                    <div class="relative">
                        <h2 class="font-serif text-3xl reveal text-cream flex items-center justify-center gap-2">
                            <iconify-icon icon="solar:gift-linear" class="text-2xl text-rose"></iconify-icon>
                            <span>Tanda Kasih</span>
                        </h2>
                        <p class="text-xs mt-2 reveal text-peach max-w-sm mx-auto">
                            Doa restu Anda merupakan karunia yang sangat berarti bagi kami. Namun apabila Anda berkenan memberikan tanda kasih, dapat disalurkan melalui:
                        </p>

                        <!-- Daftar Rekening Bank -->
                        @if($invitation->bankAccounts && $invitation->bankAccounts->count() > 0)
                            @foreach($invitation->bankAccounts as $acc)
                                <div class="glass rounded-2xl p-5 mt-5 reveal text-center shadow-xl border border-rose/25">
                                    <div class="flex items-center justify-center gap-2 mb-2">
                                        <img src="{{ \App\Support\BankLogo::assetUrl($acc->bank_name) }}" alt="{{ $acc->bank_name }}" class="h-6 w-auto bg-cream px-1.5 py-0.5 rounded object-contain">
                                        <p class="font-serif text-lg text-cream">{{ $acc->bank_name }}</p>
                                    </div>
                                    <p class="text-2xl font-mono tracking-widest mt-1 text-peach select-all">{{ $acc->account_number }}</p>
                                    <p class="text-xs text-cream/80 mt-1">a.n. {{ $acc->account_name }}</p>
                                    <button type="button" onclick="copyText('{{ $acc->account_number }}', 'Nomor rekening {{ $acc->bank_name }} disalin!')" class="mt-3 bg-cream hover:bg-peach text-charcoal rounded-full px-5 py-2 text-xs font-semibold transition active:scale-95 shadow inline-flex items-center gap-1.5 border border-rose/30">
                                        <iconify-icon icon="solar:copy-linear" class="text-sm text-rose"></iconify-icon>
                                        <span>Salin Rekening</span>
                                    </button>
                                </div>
                            @endforeach
                        @elseif(!empty($invitation->bank_name) && !empty($invitation->bank_account_number))
                            <div class="glass rounded-2xl p-5 mt-5 reveal text-center shadow-xl border border-rose/25">
                                <div class="flex items-center justify-center gap-2 mb-2">
                                    <img src="{{ \App\Support\BankLogo::assetUrl($invitation->bank_name) }}" alt="{{ $invitation->bank_name }}" class="h-6 w-auto bg-cream px-1.5 py-0.5 rounded object-contain">
                                    <p class="font-serif text-lg text-cream">{{ $invitation->bank_name }}</p>
                                </div>
                                <p class="text-2xl font-mono tracking-widest mt-1 text-peach select-all">{{ $invitation->bank_account_number }}</p>
                                <p class="text-xs text-cream/80 mt-1">a.n. {{ $invitation->bank_account_name ?: $coupleName }}</p>
                                <button type="button" onclick="copyText('{{ $invitation->bank_account_number }}', 'Nomor rekening disalin!')" class="mt-3 bg-cream hover:bg-peach text-charcoal rounded-full px-5 py-2 text-xs font-semibold transition active:scale-95 shadow inline-flex items-center gap-1.5 border border-rose/30">
                                    <iconify-icon icon="solar:copy-linear" class="text-sm text-rose"></iconify-icon>
                                    <span>Salin Rekening</span>
                                </button>
                            </div>
                        @endif

                        <!-- Kirim Hadiah Fisik -->
                        @if(!empty($invitation->gift_address))
                            <div class="glass rounded-2xl p-5 mt-4 reveal text-center shadow-xl border border-rose/25">
                                <p class="font-serif text-xl text-cream flex items-center justify-center gap-1.5">
                                    <iconify-icon icon="solar:box-linear" class="text-lg text-rose"></iconify-icon>
                                    <span>Kirim Kado Fisik</span>
                                </p>
                                <p class="text-xs text-cream/90 mt-2 leading-relaxed max-w-xs mx-auto">
                                    {!! nl2br(e($invitation->gift_address)) !!}
                                </p>
                                <button type="button" onclick="copyText('{{ addslashes($invitation->gift_address) }}', 'Alamat kado disalin!')" class="mt-3 bg-rose/20 hover:bg-rose/30 text-cream rounded-full px-5 py-2 text-xs font-semibold transition active:scale-95 border border-rose/40 inline-flex items-center gap-1.5">
                                    <iconify-icon icon="solar:clipboard-text-linear" class="text-sm text-peach"></iconify-icon>
                                    <span>Salin Alamat</span>
                                </button>
                            </div>
                        @endif
                    </div>
                </section>
            @endif

            <!-- 10. UCAPAN & DOA + RSVP -->
            <section id="ucapan" class="px-6 py-14 bg-brown border-t border-rose/10">
                <h2 class="font-serif text-3xl text-center reveal text-cream flex items-center justify-center gap-2">
                    <iconify-icon icon="solar:chat-round-dots-linear" class="text-2xl text-rose"></iconify-icon>
                    <span>Ucapan &amp; Doa</span>
                </h2>
                <p class="text-xs text-center text-peach mt-1 reveal">Kirimkan doa dan konfirmasi kehadiran Anda</p>
                
                <!-- Form Ucapan & RSVP Terhubung -->
                <form id="wishForm" class="mt-6 space-y-3 reveal" onsubmit="event.preventDefault(); submitWishAndRsvp();">
                    <div>
                        <input id="wNama" type="text" value="{{ $guestName !== 'Tamu Undangan' ? $guestName : '' }}" placeholder="Nama Anda" required class="w-full bg-darkcharcoal/60 border border-rose/30 rounded-lg px-3 py-2 text-sm text-cream placeholder-cream/40 outline-none focus:border-rose focus:bg-darkcharcoal/90 transition">
                    </div>
                    <div>
                        <textarea id="wPesan" rows="3" placeholder="Tuliskan ucapan dan doa terbaik Anda..." required class="w-full bg-darkcharcoal/60 border border-rose/30 rounded-lg px-3 py-2 text-sm text-cream placeholder-cream/40 outline-none focus:border-rose focus:bg-darkcharcoal/90 transition"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <select id="wHadir" class="w-full bg-darkcharcoal/60 border border-rose/30 rounded-lg px-3 py-2 text-xs text-cream outline-none focus:border-rose">
                            <option value="attending" class="text-charcoal bg-cream">Hadir</option>
                            <option value="not_attending" class="text-charcoal bg-cream">Tidak Hadir</option>
                            <option value="maybe" class="text-charcoal bg-cream">Masih Ragu</option>
                        </select>
                        <select id="wPax" class="w-full bg-darkcharcoal/60 border border-rose/30 rounded-lg px-3 py-2 text-xs text-cream outline-none focus:border-rose">
                            <option value="1" class="text-charcoal bg-cream">1 Orang</option>
                            <option value="2" class="text-charcoal bg-cream">2 Orang</option>
                            <option value="3" class="text-charcoal bg-cream">3 Orang</option>
                            <option value="4" class="text-charcoal bg-cream">4 Orang</option>
                        </select>
                    </div>
                    <button id="wSubmitBtn" type="submit" class="w-full bg-cream hover:bg-peach active:scale-98 text-charcoal font-semibold rounded-lg py-2.5 text-sm transition shadow-lg flex items-center justify-center gap-2 border border-rose/40">
                        <iconify-icon icon="solar:plain-bold-duotone" class="text-base text-rose"></iconify-icon>
                        <span>Kirim Ucapan &amp; Konfirmasi</span>
                    </button>
                </form>

                <!-- Daftar Ucapan dari Database -->
                <div id="wList" class="mt-6 space-y-3 max-h-80 overflow-y-auto pr-1">
                    @forelse($invitation->wishes as $wish)
                        <div class="bg-cream text-charcoal rounded-lg p-3 text-xs reveal show shadow-md border-l-4 border-rose">
                            <div class="flex items-center justify-between">
                                <b class="flex items-center gap-1.5 text-charcoal">
                                    <iconify-icon icon="solar:user-circle-linear" class="text-sm text-rose"></iconify-icon>
                                    {{ $wish->name }}
                                </b>
                                <span class="text-[10px] text-charcoal/60 flex items-center gap-0.5">
                                    <iconify-icon icon="solar:clock-circle-linear" class="text-[10px]"></iconify-icon>
                                    {{ $wish->created_at ? $wish->created_at->diffForHumans() : '' }}
                                </span>
                            </div>
                            <p class="mt-1 leading-relaxed text-charcoal/90">{{ $wish->message }}</p>
                        </div>
                    @empty
                        <p id="emptyWish" class="text-center text-xs text-peach/70 py-4 flex flex-col items-center gap-1">
                            <iconify-icon icon="solar:chat-dots-linear" class="text-2xl text-rose/50"></iconify-icon>
                            <span>Belum ada ucapan. Jadilah yang pertama mengirimkan doa!</span>
                        </p>
                    @endforelse
                </div>
            </section>

            <!-- 11. PENUTUP -->
            <section class="relative px-6 py-20 text-center ph overflow-hidden">
                @if($heroPhoto ?: $coverPhoto)
                    <img src="{{ $heroPhoto ?: $coverPhoto }}" class="absolute inset-0 w-full h-full object-cover opacity-25" alt="" onerror="this.style.display='none'">
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-darkcharcoal/95 via-darkcharcoal/70 to-transparent"></div>
                <div class="relative">
                    <p class="text-xs reveal leading-relaxed text-peach max-w-xs mx-auto">
                        {!! nl2br(e($closingText)) !!}
                    </p>
                    <h2 class="font-serif text-4xl mt-6 zoom text-cream">Terima Kasih</h2>
                    <p class="font-serif text-xl mt-3 text-peach">{{ $coupleName }}</p>
                    <p class="text-[10px] mt-16 opacity-50 tracking-wider uppercase text-cream">{{ $footerText }}</p>
                </div>
            </section>

            <div class="h-24 bg-darkcharcoal"></div>
        </div>

        <!-- ================= FLOATING BOTTOM NAV (ESTETIK ICONIFY) ================= -->
        <nav id="nav" class="absolute bottom-4 left-1/2 -translate-x-1/2 z-30 bg-cream/95 backdrop-blur-md text-charcoal rounded-full p-1.5 flex gap-1 shadow-2xl translate-y-24 transition-transform duration-700 border border-rose/30">
            <a data-t="home" title="Cover / Beranda" class="nv w-10 h-10 rounded-full flex items-center justify-center cursor-pointer transition hover:bg-peach/40">
                <iconify-icon icon="solar:home-2-linear" class="text-xl"></iconify-icon>
            </a>
            <a data-t="mempelai" title="Mempelai" class="nv w-10 h-10 rounded-full flex items-center justify-center cursor-pointer transition hover:bg-peach/40">
                <iconify-icon icon="solar:heart-linear" class="text-xl"></iconify-icon>
            </a>
            <a data-t="acara" title="Acara" class="nv w-10 h-10 rounded-full flex items-center justify-center cursor-pointer transition hover:bg-peach/40">
                <iconify-icon icon="solar:calendar-date-linear" class="text-xl"></iconify-icon>
            </a>
            @if($galleryPhotos->count() > 0)
                <a data-t="galeri" title="Galeri" class="nv w-10 h-10 rounded-full flex items-center justify-center cursor-pointer transition hover:bg-peach/40">
                    <iconify-icon icon="solar:gallery-wide-linear" class="text-xl"></iconify-icon>
                </a>
            @endif
            @if($hasBankAccounts || !empty($invitation->gift_address))
                <a data-t="kado" title="Tanda Kasih" class="nv w-10 h-10 rounded-full flex items-center justify-center cursor-pointer transition hover:bg-peach/40">
                    <iconify-icon icon="solar:gift-linear" class="text-xl"></iconify-icon>
                </a>
            @endif
            <a data-t="ucapan" title="Ucapan" class="nv w-10 h-10 rounded-full flex items-center justify-center cursor-pointer transition hover:bg-peach/40">
                <iconify-icon icon="solar:chat-round-dots-linear" class="text-xl"></iconify-icon>
            </a>
        </nav>

        <!-- ================= TOMBOL QR CODE (TAB SAMPING) ================= -->
        <button type="button" onclick="document.getElementById('qrModal').classList.remove('hidden')" class="absolute right-0 top-1/2 -translate-y-1/2 z-30 bg-cream text-charcoal text-[10px] font-bold rounded-l-xl px-2 py-3 flex flex-col items-center gap-1 shadow-lg hover:bg-peach transition border-y border-l border-rose/30">
            <iconify-icon icon="solar:qr-code-linear" class="text-base text-rose"></iconify-icon>
            <span class="[writing-mode:vertical-rl] tracking-widest">QR CODE</span>
        </button>

        <!-- ================= TOMBOL MUSIK ================= -->
        <button id="musicBtn" type="button" class="absolute top-4 right-4 z-30 w-10 h-10 rounded-full glass hidden flex items-center justify-center text-sm shadow-lg hover:bg-rose/20 transition border border-rose/30">
            <iconify-icon icon="solar:music-note-2-bold" class="text-lg text-rose"></iconify-icon>
        </button>
        @if($musicUrl)
            <audio id="bgm" src="{{ $musicUrl }}" loop preload="auto"></audio>
        @endif
    </main>

    <!-- ================= MODAL QR CODE ================= -->
    <div id="qrModal" class="hidden fixed inset-0 z-[90] bg-darkcharcoal/80 flex items-center justify-center p-6" onclick="this.classList.add('hidden')">
        <div class="bg-cream text-charcoal rounded-2xl p-6 text-center w-80 zoom show shadow-2xl relative border border-rose/30" onclick="event.stopPropagation()">
            <button type="button" onclick="document.getElementById('qrModal').classList.add('hidden')" class="absolute top-4 right-4 text-charcoal/50 hover:text-charcoal transition">
                <iconify-icon icon="solar:close-circle-linear" class="text-2xl"></iconify-icon>
            </button>
            <div class="w-12 h-12 mx-auto rounded-full bg-peach/40 flex items-center justify-center text-rose mb-2">
                <iconify-icon icon="solar:qr-code-bold-duotone" class="text-2xl"></iconify-icon>
            </div>
            <p class="font-serif text-xl font-bold">QR Code Tamu</p>
            <p class="text-xs text-charcoal/80 mt-1 font-semibold flex items-center justify-center gap-1">
                <iconify-icon icon="solar:user-linear" class="text-xs text-rose"></iconify-icon>
                {{ $guestName }}
            </p>
            
            <!-- QR Image Generated Secara Real-time -->
            <div class="w-48 h-48 mx-auto my-4 bg-white p-2 rounded-xl flex items-center justify-center border border-rose/30 shadow-inner">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data={{ urlencode($guestInvitationUrl) }}" class="w-full h-full object-contain" alt="QR Code">
            </div>

            @if(!empty($guest?->token))
                <p class="font-mono text-[11px] text-charcoal/70 font-bold tracking-wider">TOKEN: {{ $guest->getPublicToken() }}</p>
            @endif
            <p class="text-xs text-charcoal/70 mt-1">Tunjukkan QR code ini kepada penerima tamu saat tiba di lokasi acara.</p>
            <button type="button" onclick="document.getElementById('qrModal').classList.add('hidden')" class="mt-5 bg-charcoal hover:bg-darkcharcoal text-cream rounded-full px-6 py-2 text-xs font-semibold transition inline-flex items-center gap-1.5 border border-rose/30">
                <iconify-icon icon="solar:check-circle-linear" class="text-sm text-rose"></iconify-icon>
                <span>Selesai</span>
            </button>
        </div>
    </div>

    <!-- ================= MODAL LIGHTBOX FOTO ================= -->
    <div id="lightbox" class="hidden fixed inset-0 z-[99] bg-darkcharcoal/90 flex items-center justify-center p-4" onclick="closeLightbox()">
        <button type="button" class="absolute top-5 right-5 text-cream/80 hover:text-cream transition" onclick="closeLightbox()">
            <iconify-icon icon="solar:close-circle-linear" class="text-4xl"></iconify-icon>
        </button>
        <img id="lightboxImg" src="" alt="Preview" class="max-w-full max-h-[90vh] object-contain rounded-lg shadow-2xl border border-rose/20" onclick="event.stopPropagation()">
    </div>

    <!-- ================= JAVASCRIPT ================= -->
    <script>
        const $ = s => document.querySelector(s);
        const panel = $('#panel');
        const tokenMeta = $('meta[name="csrf-token"]')?.getAttribute('content');

        // Loader
        window.addEventListener('load', () => {
            setTimeout(() => {
                const loader = $('#loader');
                if (loader) {
                    loader.style.opacity = '0';
                    setTimeout(() => loader.remove(), 700);
                }
            }, 600);
        });

        // Countdown Timer
        const target = new Date("{{ $targetIso }}");
        function cd() {
            const now = new Date();
            let d = Math.max(0, target - now);
            let s = Math.floor(d / 1000);
            let v = [
                Math.floor(s / 86400),
                Math.floor((s % 86400) / 3600),
                Math.floor((s % 3600) / 60),
                s % 60
            ];
            let l = ['Hari', 'Jam', 'Menit', 'Detik'];
            const h = v.map((n, i) => `
                <div class="text-center">
                    <div class="w-12 h-12 rounded-full bg-darkcharcoal/80 border border-rose/30 flex items-center justify-center text-sm font-serif text-peach font-bold shadow">
                        ${String(n).padStart(2, '0')}
                    </div>
                    <p class="text-[10px] mt-1 text-peach/90 font-medium">${l[i]}</p>
                </div>
            `).join('');
            
            const cCover = $('#cdCover');
            const cHero = $('#cdHero');
            if (cCover) cCover.innerHTML = h;
            if (cHero) cHero.innerHTML = h;
        }
        cd();
        setInterval(cd, 1000);

        // Buka Undangan & Autoplay Musik
        const bgm = $('#bgm');
        let playing = false;

        $('#openBtn').onclick = () => {
            $('#cover').classList.add('hide');
            document.body.classList.remove('overflow-hidden');
            $('#nav').classList.remove('translate-y-24');
            
            if (bgm) {
                $('#musicBtn').classList.remove('hidden');
                bgm.play().then(() => {
                    playing = true;
                    $('#musicBtn').innerHTML = '<iconify-icon icon="solar:music-note-2-bold" class="text-lg text-rose animate-pulse"></iconify-icon>';
                }).catch(() => {
                    playing = false;
                    $('#musicBtn').innerHTML = '<iconify-icon icon="solar:volume-cross-bold" class="text-lg text-rose/60"></iconify-icon>';
                });
            }
        };

        // Toggle Musik
        $('#musicBtn').onclick = () => {
            if (!bgm) return;
            if (playing) {
                bgm.pause();
                $('#musicBtn').innerHTML = '<iconify-icon icon="solar:volume-cross-bold" class="text-lg text-rose/60"></iconify-icon>';
            } else {
                bgm.play().catch(() => {});
                $('#musicBtn').innerHTML = '<iconify-icon icon="solar:music-note-2-bold" class="text-lg text-rose animate-pulse"></iconify-icon>';
            }
            playing = !playing;
        };

        // Salin Teks / Rekening dengan Toast
        function copyText(text, message = 'Berhasil disalin!') {
            navigator.clipboard.writeText(text).then(() => {
                showToast(message);
            }).catch(() => {
                const ta = document.createElement('textarea');
                ta.value = text;
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                document.body.removeChild(ta);
                showToast(message);
            });
        }

        function showToast(msg) {
            const toast = $('#toast');
            const toastText = $('#toastText');
            if (!toast) return;
            if (toastText) toastText.textContent = msg;
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 2500);
        }

        // Lightbox
        function openLightbox(src) {
            const lb = $('#lightbox');
            const img = $('#lightboxImg');
            if (lb && img) {
                img.src = src;
                lb.classList.remove('hidden');
            }
        }
        function closeLightbox() {
            const lb = $('#lightbox');
            if (lb) lb.classList.add('hidden');
        }

        // Animasi Scroll (IntersectionObserver)
        const io = new IntersectionObserver(es => {
            es.forEach(e => {
                if (e.isIntersecting) {
                    e.target.classList.add('show');
                }
            });
        }, { root: panel, threshold: 0.15 });

        const watch = () => document.querySelectorAll('.reveal, .reveal-l, .reveal-r, .zoom').forEach(el => io.observe(el));
        watch();

        // Navigasi Bawah
        document.querySelectorAll('.nv').forEach(a => {
            a.onclick = () => {
                const targetEl = document.getElementById(a.dataset.t);
                if (targetEl) {
                    targetEl.scrollIntoView({ behavior: 'smooth' });
                }
            };
        });

        const activeSecs = ['home', 'mempelai', 'acara', 'galeri', 'kado', 'ucapan']
            .map(id => document.getElementById(id))
            .filter(Boolean);

        panel.addEventListener('scroll', () => {
            let cur = 0;
            activeSecs.forEach((s, i) => {
                if (s.offsetTop - panel.clientHeight / 2 <= panel.scrollTop) {
                    cur = i;
                }
            });
            document.querySelectorAll('.nv').forEach((btn, i) => {
                const match = activeSecs[cur] && btn.dataset.t === activeSecs[cur].id;
                btn.classList.toggle('bg-charcoal', match);
                btn.classList.toggle('text-cream', match);
                btn.classList.toggle('shadow-md', match);
            });
        });

        // Submit Ucapan & RSVP Langsung ke Backend
        async function submitWishAndRsvp() {
            const name = $('#wNama').value.trim();
            const message = $('#wPesan').value.trim();
            const status = $('#wHadir').value;
            const pax = $('#wPax').value;
            const submitBtn = $('#wSubmitBtn');

            if (!name || !message) {
                showToast('Mohon lengkapi nama dan ucapan Anda');
                return;
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<iconify-icon icon="solar:restart-bold" class="text-base animate-spin text-rose"></iconify-icon> <span>Mengirimkan...</span>';

            try {
                // 1. Kirim Wish
                const wishRes = await fetch("{{ route('invitation.wish', $invitation->slug) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': tokenMeta,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ name, message })
                });

                // 2. Kirim RSVP
                await fetch("{{ route('invitation.rsvp', $invitation->slug) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': tokenMeta,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        name,
                        status,
                        pax: parseInt(pax) || 1,
                        message,
                        @if(!empty($guest?->id))
                            guest_id: {{ $guest->id }},
                        @endif
                    })
                }).catch(() => {});

                // Render ucapan baru di tampilan secara instan
                const esc = t => t.replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
                const newWishCard = `
                    <div class="bg-cream text-charcoal rounded-lg p-3 text-xs reveal show shadow-md border-l-4 border-rose transition">
                        <div class="flex items-center justify-between">
                            <b class="flex items-center gap-1.5 text-charcoal">
                                <iconify-icon icon="solar:user-circle-linear" class="text-sm text-rose"></iconify-icon>
                                ${esc(name)}
                            </b>
                            <span class="text-[10px] text-charcoal/60 flex items-center gap-0.5">
                                <iconify-icon icon="solar:clock-circle-linear" class="text-[10px]"></iconify-icon>
                                Baru saja
                            </span>
                        </div>
                        <p class="mt-1 leading-relaxed text-charcoal/90">${esc(message)}</p>
                    </div>
                `;

                const emptyWish = $('#emptyWish');
                if (emptyWish) emptyWish.remove();

                const wList = $('#wList');
                wList.insertAdjacentHTML('afterbegin', newWishCard);
                $('#wPesan').value = '';

                showToast('Terima kasih atas doa dan ucapan Anda!');
            } catch (err) {
                showToast('Ucapan berhasil dicatat!');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<iconify-icon icon="solar:plain-bold-duotone" class="text-base text-rose"></iconify-icon> <span>Kirim Ucapan &amp; Konfirmasi</span>';
            }
        }

        // Efek Kelopak Bunga Jatuh
        for (let i = 0; i < 14; i++) {
            const p = document.createElement('i');
            p.className = 'petal';
            p.style.left = Math.random() * 100 + 'vw';
            p.style.animationDuration = (8 + Math.random() * 8) + 's';
            p.style.animationDelay = (Math.random() * 8) + 's';
            document.body.appendChild(p);
        }
    </script>
</body>
</html>
