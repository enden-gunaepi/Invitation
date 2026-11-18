<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $isDemoMode = (bool) ($demoMode ?? false);
        $personalization = $personalization ?? ['greeting' => 'Tamu Undangan', 'cta' => 'Konfirmasi Kehadiran', 'rsvp_hint' => 'Mohon konfirmasi kehadiran Anda.'];

        $groomName = $invitation->groom_name ?: 'Andra';
        $brideName = $invitation->bride_name ?: 'Via';
        $coupleName = trim(($invitation->bride_name ?? 'Via') . ' & ' . ($invitation->groom_name ?? 'Andra'));

        $guestName = $guest->name ?? (request('to') ?: 'Tamu Undangan');
        $guestInvitationUrl = !empty($guest?->token) ? $guest->getInvitationUrl() : $invitation->getPublicUrl();

        $coverPhoto = $invitation->cover_photo ? asset('storage/' . $invitation->cover_photo) : null;
        $groomPhoto = $invitation->groom_photo ? asset('storage/' . $invitation->groom_photo) : null;
        $bridePhoto = $invitation->bride_photo ? asset('storage/' . $invitation->bride_photo) : null;
        $igStoryPhoto = $invitation->ig_story_photo ? asset('storage/' . $invitation->ig_story_photo) : null;
        $musicUrl = $invitation->music_url ? asset('storage/' . $invitation->music_url) : null;

        $galleryPhotos = $invitation->photos;
        $heroPhoto = $coverPhoto ?: ($galleryPhotos->first() ? asset('storage/' . $galleryPhotos->first()->file_path) : null);

        $eventDateText = $invitation->event_date ? $invitation->event_date->translatedFormat('l, d F Y') : 'Sabtu, 28 Maret 2026';
        $eventDayName = $invitation->event_date ? $invitation->event_date->translatedFormat('l') : 'Sabtu';
        $eventDayNumber = $invitation->event_date ? $invitation->event_date->format('d') : '28';
        $eventMonthName = $invitation->event_date ? $invitation->event_date->translatedFormat('F') : 'Maret';
        $eventYearNumber = $invitation->event_date ? $invitation->event_date->format('Y') : '2026';

        $eventDateIso = $invitation->event_date ? $invitation->event_date->format('Y-m-d') : '2026-03-28';
        $eventTimeIso = $invitation->event_time ? \Carbon\Carbon::parse($invitation->event_time)->format('H:i:s') : '16:00:00';
        $targetIso = $eventDateIso . 'T' . $eventTimeIso . '+07:00';

        $openingText = $invitation->opening_text 
            ?: 'Dan di antara tanda-tanda kekuasaan-Nya ialah Dia menciptakan untukmu isteri-isteri dari jenismu sendiri, supaya kamu merasa tenang dan tenteram kepadanya, dan dijadikan-Nya di antaramu rasa kasih dan sayang.';
        $closingText = $invitation->closing_text 
            ?: 'Atas kehadiran Bapak/Ibu/Saudara/i serta doa restu yang diberikan, kami ucapkan terima kasih yang sebesar-besarnya.';
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

    <title>{{ $invitation->title ?: ('Undangan Pernikahan ' . $coupleName) }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Alex+Brush&family=Marcellus&family=Lato:wght@400;700&family=Amiri&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        burgundy: '#721C2C',
                        darkburgundy: '#4A0E17',
                        roseburgundy: '#9E3B50',
                        wine: '#581422',
                        blush: '#FAF0F2',
                        mist: '#F5DDE2',
                        softcream: '#FFF9FA',
                    }
                }
            }
        };
    </script>
    <style>
        :root {
            --f-judul: 'Great Vibes', cursive;
            --f-nama: 'Alex Brush', cursive;
            --f-teks: 'Marcellus', serif;
            --f-tombol: 'Lato', sans-serif;
            --c-burgundy: #721C2C;
            --c-dark: #4A0E17;
            --c-mist: #F5DDE2;
        }
        html { scroll-behavior: smooth; }
        body { font-family: var(--f-teks); background: #FAF0F2; color: #3A2025; }
        .judul { font-family: var(--f-judul); color: var(--c-burgundy); font-size: 2.6rem; line-height: 1.2; }
        .nama { font-family: var(--f-nama); color: var(--c-dark); font-size: 2.3rem; }
        .btn {
            font-family: var(--f-tombol); font-size: .78rem; font-weight: 700;
            background: var(--c-burgundy); color: #fff; border-radius: .5rem;
            padding: .65rem 1.15rem; display: inline-flex; gap: .45rem; align-items: center;
            justify-content: center; transition: all .3s cubic-bezier(.34, 1.56, .64, 1);
            box-shadow: 0 4px 14px rgba(114, 28, 44, 0.28);
        }
        .btn:hover { background: var(--c-dark); transform: translateY(-2px); box-shadow: 0 6px 20px rgba(114, 28, 44, 0.35); }
        .btn:active { transform: translateY(0); }
        .card {
            background: rgba(255, 255, 255, 0.94); border-radius: 1.6rem;
            box-shadow: 0 10px 35px rgba(114, 28, 44, 0.08); position: relative;
            overflow: hidden; padding: 2.5rem 1.5rem; text-align: center;
            border: 1px solid rgba(227, 169, 181, 0.25);
        }
        .ph { background: linear-gradient(135deg, #ECCCD3, #D4A2AC); }
        .foto-bulat {
            width: 110px; height: 110px; border-radius: 50%;
            border: 4px solid var(--c-burgundy); overflow: hidden; margin: 0 auto;
            box-shadow: 0 6px 18px rgba(114, 28, 44, 0.2);
        }
        .reveal { opacity: 0; transform: translateY(45px); transition: all 1s cubic-bezier(.2, .7, .2, 1); }
        .reveal.show { opacity: 1; transform: none; }
        .fade-l { opacity: 0; transform: translateX(-40px); transition: all 1s; }
        .fade-l.show { opacity: 1; transform: none; }
        .fade-r { opacity: 0; transform: translateX(40px); transition: all 1s; }
        .fade-r.show { opacity: 1; transform: none; }
        .zoom { opacity: 0; transform: scale(.8); transition: all 1s; }
        .zoom.show { opacity: 1; transform: none; }
        @keyframes sway { 0%, 100% { transform: rotate(-2deg); } 50% { transform: rotate(2deg); } }
        .sway { animation: sway 7s ease-in-out infinite; transform-origin: bottom center; }
        @keyframes spin { to { transform: rotate(360deg); } }
        @keyframes pulse2 { 0%, 100% { box-shadow: 0 0 0 0 rgba(114, 28, 44, 0.5); } 50% { box-shadow: 0 0 0 12px rgba(114, 28, 44, 0); } }
        .pulse2 { animation: pulse2 2s infinite; }
        @keyframes drift {
            0% { transform: translate3d(0, -10vh, 0) rotate(0); opacity: 0; }
            15% { opacity: .85; }
            100% { transform: translate3d(80px, 110vh, 0) rotate(320deg); opacity: 0; }
        }
        .petal {
            position: fixed; top: 0; width: 9px; height: 9px; border-radius: 50% 0 50% 0;
            background: #B85B6F; pointer-events: none; z-index: 3; animation: drift linear infinite;
        }
        #cover { transition: opacity 1s, transform 1.2s; }
        #cover.hide { opacity: 0; transform: scale(1.08); pointer-events: none; }
        .tl { border-left: 2px dotted #B85B6F; }
        .nv {
            width: 2.5rem; height: 2.5rem; border-radius: 9999px; display: flex;
            align-items: center; justify-content: center; cursor: pointer;
            transition: all .25s ease; color: #4A0E17;
        }
        .nv.on { background: var(--c-burgundy); color: #fff; box-shadow: 0 4px 12px rgba(114, 28, 44, 0.35); }
        .toast-msg {
            position: fixed; bottom: 85px; left: 50%; transform: translateX(-50%);
            background: #4A0E17; color: #FFF5F5; padding: 10px 22px; border-radius: 9999px;
            font-size: 13px; font-weight: 600; z-index: 100; box-shadow: 0 10px 30px rgba(74, 14, 23, 0.4);
            border: 1px solid #B85B6F; opacity: 0; pointer-events: none; transition: opacity 0.3s ease;
        }
        .toast-msg.show { opacity: 1; }
        iconify-icon { display: inline-flex; vertical-align: middle; }
    </style>
</head>
<body class="relative min-h-screen overflow-x-hidden">

    <!-- DEMO BADGE -->
    @if($isDemoMode)
        <div class="fixed left-3 top-3 z-[95] bg-burgundy text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg flex items-center gap-1.5 border border-pink-200/40">
            <iconify-icon icon="solar:info-circle-bold" class="text-sm"></iconify-icon>
            Demo Mode
        </div>
    @endif

    <!-- TOAST NOTIFICATION -->
    <div id="toast" class="toast-msg flex items-center gap-1.5">
        <iconify-icon icon="solar:check-circle-bold" class="text-base text-pink-300"></iconify-icon>
        <span id="toastText">Tersalin ke clipboard</span>
    </div>

    <!-- LOADING SPINNER -->
    <div id="loader" class="fixed inset-0 z-[100] bg-white flex items-center justify-center transition-opacity duration-700">
        <div class="w-9 h-9 border-2 border-burgundy/30 border-t-burgundy rounded-full" style="animation: spin 1s linear infinite"></div>
    </div>

    <!-- LATAR BERSAMA: Watercolor Burgundy & Floral Accent -->
    <div class="fixed inset-0 -z-0 pointer-events-none" style="background: radial-gradient(circle at 20% 10%, #F5DDE2 0, transparent 40%), radial-gradient(circle at 85% 70%, #ECCCD3 0, transparent 45%), #FAF0F2;"></div>

    <!-- Hiasan Floral Burgundy Pojok Kiri Atas -->
    <div class="fixed top-0 left-0 w-44 md:w-72 sway pointer-events-none z-[1] opacity-75">
        <svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto text-burgundy">
            <path d="M0 0C40 20 70 60 75 100C80 140 100 170 140 185" stroke="#721C2C" stroke-width="1.5" stroke-dasharray="3 3"/>
            <circle cx="75" cy="100" r="18" fill="#F5DDE2" stroke="#721C2C" stroke-width="1.5"/>
            <circle cx="50" cy="50" r="26" fill="#ECCCD3" fill-opacity="0.6"/>
            <path d="M35 30C45 10 75 25 65 50C55 75 25 50 35 30Z" fill="#721C2C" fill-opacity="0.25"/>
            <path d="M70 90C85 75 110 95 95 115C80 135 55 105 70 90Z" fill="#721C2C" fill-opacity="0.35"/>
        </svg>
    </div>

    <!-- Hiasan Floral Burgundy Pojok Kanan Bawah -->
    <div class="fixed bottom-0 right-0 w-44 md:w-80 sway pointer-events-none z-[1] opacity-75">
        <svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto text-burgundy">
            <path d="M200 200C160 180 130 140 125 100C120 60 100 30 60 15" stroke="#721C2C" stroke-width="1.5" stroke-dasharray="3 3"/>
            <circle cx="125" cy="100" r="18" fill="#F5DDE2" stroke="#721C2C" stroke-width="1.5"/>
            <circle cx="150" cy="150" r="28" fill="#ECCCD3" fill-opacity="0.6"/>
            <path d="M165 170C155 190 125 175 135 150C145 125 175 150 165 170Z" fill="#721C2C" fill-opacity="0.25"/>
            <path d="M130 110C115 125 90 105 105 85C120 65 145 95 130 110Z" fill="#721C2C" fill-opacity="0.35"/>
        </svg>
    </div>

    <!-- ================= COVER ================= -->
    <section id="cover" class="fixed inset-0 z-50 ph overflow-hidden flex items-center justify-center text-center">
        @if($coverPhoto ?: $heroPhoto)
            <img src="{{ $coverPhoto ?: $heroPhoto }}" class="absolute inset-0 w-full h-full object-cover" alt="Cover" onerror="this.style.display='none'">
        @endif
        <div class="absolute inset-0 bg-white/75 backdrop-blur-[2px]"></div>
        <div class="relative px-6 zoom show max-w-sm mx-auto">
            <p class="text-xs uppercase tracking-widest text-burgundy font-bold">Undangan Pernikahan</p>
            <h1 class="text-6xl text-darkburgundy mt-2" style="font-family: var(--f-judul)">{{ $coupleName }}</h1>
            
            <div class="flex items-center justify-center gap-4 my-8 text-sm text-darkburgundy">
                <span class="font-semibold">{{ $eventDayName }}</span>
                <div class="border-x-2 border-burgundy/30 px-5 leading-tight">
                    <b class="text-3xl font-serif text-burgundy">{{ $eventDayNumber }}</b><br>
                    <span class="text-xs tracking-wider opacity-80">{{ $eventYearNumber }}</span>
                </div>
                <span class="font-semibold">{{ $eventMonthName }}</span>
            </div>

            <p class="text-xs uppercase tracking-wider text-darkburgundy/70">{{ $personalization['greeting'] ?? 'Kepada Yth:' }}</p>
            <p id="guest" class="font-bold text-lg text-darkburgundy mt-0.5" style="font-family: var(--f-tombol)">{{ $guestName }}</p>

            <button id="openBtn" type="button" class="btn pulse2 mt-6 text-sm">
                <iconify-icon icon="solar:letter-opened-linear" class="text-base"></iconify-icon>
                <span>Buka Undangan</span>
            </button>
        </div>
    </section>

    <!-- ================= ISI (KOLOM TENGAH) ================= -->
    <main class="relative z-10 max-w-md mx-auto px-4 pt-6 pb-28 space-y-6">

        <!-- 1. HERO SECTION -->
        <section id="home" class="card !p-0 reveal overflow-hidden shadow-xl">
            <div class="aspect-[3/4] ph relative overflow-hidden">
                @if($heroPhoto ?: $coverPhoto)
                    <img src="{{ $heroPhoto ?: $coverPhoto }}" class="w-full h-full object-cover" alt="{{ $coupleName }}" onerror="this.style.display='none'">
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
            </div>
            <div class="py-6 px-4">
                <p class="text-xs tracking-widest text-burgundy uppercase font-semibold">Undangan Pernikahan</p>
                <h2 class="judul !text-5xl mt-1">{{ $coupleName }}</h2>
                <p class="text-xs text-darkburgundy/80 mt-1">{{ $eventDateText }}</p>
                
                <!-- Countdown Timer -->
                <div id="cd" class="flex justify-center gap-2.5 mt-5"></div>

                <div class="flex justify-center gap-2 mt-5">
                    @if($calendarUrl)
                        <a href="{{ $calendarUrl }}" target="_blank" class="btn !bg-white !text-burgundy !border !border-burgundy/30 hover:!bg-mist text-xs">
                            <iconify-icon icon="solar:calendar-add-bold" class="text-sm text-burgundy"></iconify-icon>
                            <span>Simpan Tanggal</span>
                        </a>
                    @endif
                    <a href="#acara" class="btn text-xs">
                        <iconify-icon icon="solar:map-point-wave-bold" class="text-sm"></iconify-icon>
                        <span>Lihat Lokasi</span>
                    </a>
                </div>
            </div>
        </section>

        <!-- 2. QUOTE -->
        <section class="card reveal">
            <p class="text-2xl leading-loose font-serif text-burgundy" style="font-family:'Amiri',serif;" dir="rtl">
                وَمِنْ آيَاتِهِ أَنْ خَلَقَ لَكُم مِّنْ أَنفُسِكُمْ أَزْوَاجًا لِّتَسْكُنُوا إِلَيْهَا
            </p>
            <p class="italic text-xs leading-relaxed mt-4 text-darkburgundy/90">
                {!! nl2br(e($openingText)) !!}
            </p>
            <p class="text-xs font-bold tracking-wider mt-4 text-burgundy">— QS. Ar-Rum: 21</p>
        </section>

        <!-- 3. MEMPELAI -->
        <section id="mempelai" class="card">
            <h2 class="judul !text-4xl">Mempelai</h2>
            <p class="text-xs mt-1 text-darkburgundy/80 reveal">Kami memohon doa &amp; restu atas pernikahan kami:</p>

            <!-- Mempelai Wanita -->
            <div class="mt-8 fade-l">
                <div class="foto-bulat ph">
                    @if($bridePhoto)
                        <img src="{{ $bridePhoto }}" class="w-full h-full object-cover" alt="{{ $brideName }}" onerror="this.style.display='none'">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-burgundy bg-mist">
                            <iconify-icon icon="solar:user-heart-rounded-linear" class="text-5xl"></iconify-icon>
                        </div>
                    @endif
                </div>
                <h3 class="nama mt-4">{{ $invitation->bride_name ?: 'Mempelai Wanita' }}</h3>
                @if($invitation->bride_parent_name)
                    <p class="text-xs text-darkburgundy/80 mt-1 max-w-xs mx-auto">{{ $invitation->bride_parent_name }}</p>
                @endif
                <div class="flex justify-center gap-2 mt-3 text-xs">
                    @if($invitation->bride_instagram)
                        <a href="{{ Str::startsWith($invitation->bride_instagram, 'http') ? $invitation->bride_instagram : 'https://instagram.com/' . ltrim($invitation->bride_instagram, '@') }}" target="_blank" class="btn !py-1.5 !px-3 !text-xs">
                            <iconify-icon icon="ri:instagram-line" class="text-sm"></iconify-icon>
                            <span>{{ Str::startsWith($invitation->bride_instagram, '@') ? $invitation->bride_instagram : '@Instagram' }}</span>
                        </a>
                    @endif
                    @if($invitation->bride_facebook)
                        <a href="{{ Str::startsWith($invitation->bride_facebook, 'http') ? $invitation->bride_facebook : 'https://facebook.com/' . $invitation->bride_facebook }}" target="_blank" class="btn !bg-white !text-burgundy !border !border-burgundy/30 !py-1.5 !px-3 !text-xs">
                            <iconify-icon icon="ri:facebook-circle-line" class="text-sm"></iconify-icon>
                            <span>Facebook</span>
                        </a>
                    @endif
                </div>
            </div>

            <p class="text-5xl my-4 zoom text-burgundy" style="font-family: var(--f-nama)">&amp;</p>

            <!-- Mempelai Pria -->
            <div class="fade-r">
                <div class="foto-bulat ph">
                    @if($groomPhoto)
                        <img src="{{ $groomPhoto }}" class="w-full h-full object-cover" alt="{{ $groomName }}" onerror="this.style.display='none'">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-burgundy bg-mist">
                            <iconify-icon icon="solar:user-rounded-linear" class="text-5xl"></iconify-icon>
                        </div>
                    @endif
                </div>
                <h3 class="nama mt-4">{{ $invitation->groom_name ?: 'Mempelai Pria' }}</h3>
                @if($invitation->groom_parent_name)
                    <p class="text-xs text-darkburgundy/80 mt-1 max-w-xs mx-auto">{{ $invitation->groom_parent_name }}</p>
                @endif
                <div class="flex justify-center gap-2 mt-3 text-xs">
                    @if($invitation->groom_instagram)
                        <a href="{{ Str::startsWith($invitation->groom_instagram, 'http') ? $invitation->groom_instagram : 'https://instagram.com/' . ltrim($invitation->groom_instagram, '@') }}" target="_blank" class="btn !py-1.5 !px-3 !text-xs">
                            <iconify-icon icon="ri:instagram-line" class="text-sm"></iconify-icon>
                            <span>{{ Str::startsWith($invitation->groom_instagram, '@') ? $invitation->groom_instagram : '@Instagram' }}</span>
                        </a>
                    @endif
                    @if($invitation->groom_facebook)
                        <a href="{{ Str::startsWith($invitation->groom_facebook, 'http') ? $invitation->groom_facebook : 'https://facebook.com/' . $invitation->groom_facebook }}" target="_blank" class="btn !bg-white !text-burgundy !border !border-burgundy/30 !py-1.5 !px-3 !text-xs">
                            <iconify-icon icon="ri:facebook-circle-line" class="text-sm"></iconify-icon>
                            <span>Facebook</span>
                        </a>
                    @endif
                </div>
            </div>
        </section>

        <!-- 4. ACARA -->
        <section id="acara" class="card">
            <h2 class="judul reveal">Rangkaian Acara</h2>
            <p class="text-xs mt-1 text-darkburgundy/80 reveal">Kami bermaksud untuk mengundang Saudara/(i) dalam acara kami pada:</p>

            @if($hasEvents)
                @foreach($invitation->events as $index => $event)
                    @php
                        $eventDateFormatted = $event->event_date ? \Carbon\Carbon::parse($event->event_date)->translatedFormat('l, d F Y') : $eventDateText;
                        $eventTimeStart = $event->event_time ? \Carbon\Carbon::parse($event->event_time)->format('H:i') : null;
                        $eventTimeEnd = $event->event_end_time ? \Carbon\Carbon::parse($event->event_end_time)->format('H:i') : 'Selesai';
                        $eventTimeFormatted = $eventTimeStart ? ($eventTimeStart . ' - ' . $eventTimeEnd . ' WIB') : 'Waktu menyusul';
                        $eventVenue = $event->venue_name ?: $invitation->venue_name;
                        $eventAddress = $event->venue_address ?: $invitation->venue_address;
                        $eventMap = $event->venue_maps_url ?: $mapsTrackedUrl;
                    @endphp
                    @if($index > 0)
                        <hr class="border-dashed border-burgundy/20 my-6">
                    @else
                        <div class="mt-6"></div>
                    @endif
                    <div class="reveal">
                        <h3 class="text-2xl font-bold text-darkburgundy" style="font-family: var(--f-teks)">{{ $event->event_name }}</h3>
                        <p class="text-xs text-burgundy font-semibold mt-1">
                            {{ $eventDateFormatted }}<br>{{ $eventTimeFormatted }}
                        </p>
                        @if($eventVenue)
                            <p class="text-sm font-bold mt-2 text-darkburgundy flex items-center justify-center gap-1.5">
                                <iconify-icon icon="solar:buildings-2-linear" class="text-sm text-burgundy"></iconify-icon>
                                {{ $eventVenue }}
                            </p>
                        @endif
                        @if($eventAddress)
                            <p class="text-xs text-darkburgundy/75 mt-0.5">{{ $eventAddress }}</p>
                        @endif
                        @if($event->event_description)
                            <p class="text-xs italic text-burgundy/80 mt-1.5">{{ $event->event_description }}</p>
                        @endif
                        <div class="flex justify-center gap-2 mt-4 text-xs">
                            @if($calendarUrl)
                                <a href="{{ $calendarUrl }}" target="_blank" class="btn !bg-white !text-burgundy !border !border-burgundy/30 hover:!bg-mist">
                                    <iconify-icon icon="solar:calendar-add-bold" class="text-sm text-burgundy"></iconify-icon>
                                    <span>Simpan Tanggal</span>
                                </a>
                            @endif
                            @if($eventMap)
                                <a href="{{ $eventMap }}" target="_blank" class="btn">
                                    <iconify-icon icon="solar:map-point-wave-bold" class="text-sm"></iconify-icon>
                                    <span>Navigasi Map</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <!-- Default Rundown Fallback -->
                <div class="mt-6 reveal">
                    <h3 class="text-2xl font-bold text-darkburgundy" style="font-family: var(--f-teks)">Akad Nikah</h3>
                    <p class="text-xs text-burgundy font-semibold mt-1">
                        {{ $eventDateText }}<br>
                        {{ $invitation->event_time ? \Carbon\Carbon::parse($invitation->event_time)->format('H:i') : '16:00' }} WIB – Selesai
                    </p>
                    @if($invitation->venue_name)
                        <p class="text-sm font-bold mt-2 text-darkburgundy flex items-center justify-center gap-1.5">
                            <iconify-icon icon="solar:buildings-2-linear" class="text-sm text-burgundy"></iconify-icon>
                            {{ $invitation->venue_name }}
                        </p>
                    @endif
                    @if($invitation->venue_address)
                        <p class="text-xs text-darkburgundy/75 mt-0.5">{{ $invitation->venue_address }}</p>
                    @endif
                    <div class="flex justify-center gap-2 mt-4 text-xs">
                        @if($calendarUrl)
                            <a href="{{ $calendarUrl }}" target="_blank" class="btn !bg-white !text-burgundy !border !border-burgundy/30 hover:!bg-mist">
                                <iconify-icon icon="solar:calendar-add-bold" class="text-sm text-burgundy"></iconify-icon>
                                <span>Simpan Tanggal</span>
                            </a>
                        @endif
                        <a href="{{ $mapsTrackedUrl }}" target="_blank" class="btn">
                            <iconify-icon icon="solar:map-point-wave-bold" class="text-sm"></iconify-icon>
                            <span>Navigasi Map</span>
                        </a>
                    </div>
                </div>
                <hr class="border-dashed border-burgundy/20 my-6">
                <div class="reveal">
                    <h3 class="text-2xl font-bold text-darkburgundy" style="font-family: var(--f-teks)">Resepsi</h3>
                    <p class="text-xs text-burgundy font-semibold mt-1">
                        {{ $eventDateText }}<br>
                        {{ $invitation->event_time ? \Carbon\Carbon::parse($invitation->event_time)->addHours(2)->format('H:i') : '18:30' }} WIB – Selesai
                    </p>
                    @if($invitation->venue_name)
                        <p class="text-sm font-bold mt-2 text-darkburgundy flex items-center justify-center gap-1.5">
                            <iconify-icon icon="solar:buildings-2-linear" class="text-sm text-burgundy"></iconify-icon>
                            {{ $invitation->venue_name }}
                        </p>
                    @endif
                    @if($invitation->venue_address)
                        <p class="text-xs text-darkburgundy/75 mt-0.5">{{ $invitation->venue_address }}</p>
                    @endif
                    <div class="flex justify-center gap-2 mt-4 text-xs">
                        @if($calendarUrl)
                            <a href="{{ $calendarUrl }}" target="_blank" class="btn !bg-white !text-burgundy !border !border-burgundy/30 hover:!bg-mist">
                                <iconify-icon icon="solar:calendar-add-bold" class="text-sm text-burgundy"></iconify-icon>
                                <span>Simpan Tanggal</span>
                            </a>
                        @endif
                        <a href="{{ $mapsTrackedUrl }}" target="_blank" class="btn">
                            <iconify-icon icon="solar:map-point-wave-bold" class="text-sm"></iconify-icon>
                            <span>Navigasi Map</span>
                        </a>
                    </div>
                </div>
            @endif

            <!-- Live Streaming (bila aktif) -->
            @if($invitation->livestream_enabled && $invitation->livestream_url)
                <div class="mt-8 pt-6 border-t border-burgundy/20 reveal">
                    <h3 class="judul !text-3xl flex items-center justify-center gap-2">
                        <iconify-icon icon="solar:videocamera-record-bold-duotone" class="text-burgundy animate-pulse"></iconify-icon>
                        <span>{{ $invitation->livestream_label ?: 'Live Streaming' }}</span>
                    </h3>
                    <p class="text-xs text-darkburgundy/80 mt-1 max-w-xs mx-auto">
                        Acara ini disiarkan langsung melalui media internet. Silahkan klik tombol di bawah untuk menyaksikan.
                    </p>
                    <a href="{{ $invitation->livestream_url }}" target="_blank" class="btn mt-4 !text-xs inline-flex items-center gap-2">
                        <iconify-icon icon="solar:play-circle-bold-duotone" class="text-base"></iconify-icon>
                        <span>Tonton Live Streaming</span>
                    </a>
                </div>
            @endif
        </section>

        <!-- 5. KISAH CINTA (LOVE STORIES) -->
        @if($invitation->loveStories && $invitation->loveStories->count() > 0)
            <section id="kisah" class="card">
                <h2 class="judul reveal">Kisah Cinta</h2>
                <div class="tl ml-3 mt-6 space-y-6 text-left">
                    @foreach($invitation->loveStories as $index => $story)
                        <div class="relative pl-6 {{ $index % 2 == 0 ? 'fade-l' : 'fade-r' }}">
                            <span class="absolute -left-[9px] top-1 w-4 h-4 rounded-full bg-burgundy text-white text-[9px] flex items-center justify-center shadow">
                                <iconify-icon icon="solar:heart-bold"></iconify-icon>
                            </span>
                            @if($story->photo_path)
                                <div class="rounded-lg overflow-hidden ph h-32 shadow-sm">
                                    <img src="{{ asset('storage/' . $story->photo_path) }}" class="w-full h-full object-cover" alt="{{ $story->title }}" onerror="this.style.display='none'">
                                </div>
                            @endif
                            <div class="flex items-center gap-2 mt-2">
                                <span class="bg-mist text-burgundy font-bold text-[10px] px-2 py-0.5 rounded-full">{{ $story->year }}</span>
                                <h4 class="text-sm font-bold text-darkburgundy">{{ $story->title }}</h4>
                            </div>
                            <p class="text-xs text-darkburgundy/80 mt-1 leading-relaxed">{{ $story->description }}</p>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- 6. GALERI FOTO -->
        @if($galleryPhotos->count() > 0)
            <section id="galeri" class="card">
                <h2 class="judul reveal">Galeri</h2>
                <p class="text-xs text-darkburgundy/70 mt-1 reveal">Momen indah yang kami abadikan</p>
                <div class="grid grid-cols-2 gap-2.5 mt-5">
                    @foreach($galleryPhotos->take(6) as $photo)
                        <div class="rounded-xl overflow-hidden ph aspect-[3/4] zoom cursor-pointer group relative shadow-sm border border-burgundy/15" onclick="openLightbox('{{ asset('storage/' . $photo->file_path) }}')">
                            <img src="{{ asset('storage/' . $photo->file_path) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-700" alt="{{ $photo->caption ?: 'Galeri' }}" loading="lazy" onerror="this.style.display='none'">
                            <div class="absolute inset-0 bg-darkburgundy/20 group-hover:bg-darkburgundy/40 transition flex items-center justify-center opacity-0 group-hover:opacity-100">
                                <iconify-icon icon="solar:magnifer-zoom-in-linear" class="text-2xl text-white drop-shadow"></iconify-icon>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- 7. RSVP -->
        <section id="rsvp" class="card">
            <h2 class="judul reveal">RSVP</h2>
            <p class="text-xs text-darkburgundy/80 mt-1 reveal">{{ $personalization['rsvp_hint'] }}</p>
            <button onclick="document.getElementById('rsvpModal').classList.remove('hidden')" class="btn !w-full !justify-center flex-col !py-4 mt-4 !text-xs reveal shadow-lg">
                <iconify-icon icon="solar:user-check-bold" class="text-2xl mb-1"></iconify-icon>
                <span>{{ $personalization['cta'] }}</span>
            </button>
        </section>

        <!-- 8. KADO / TANDA KASIH -->
        @if($hasBankAccounts || !empty($invitation->gift_address))
            <section id="kado" class="card">
                <h2 class="judul reveal">Tanda Kasih</h2>
                <p class="text-xs text-darkburgundy/80 mt-1 reveal">
                    Doa restu Anda merupakan karunia yang sangat berarti bagi kedua mempelai. Namun jika memberi adalah ungkapan tanda kasih, Anda dapat melalui:
                </p>

                <!-- Daftar Rekening Bank -->
                @if($invitation->bankAccounts && $invitation->bankAccounts->count() > 0)
                    @foreach($invitation->bankAccounts as $acc)
                        <div class="mt-5 p-4 rounded-xl bg-mist/60 border border-burgundy/20 text-center reveal">
                            <div class="flex items-center justify-center gap-2 mb-1.5">
                                <img src="{{ \App\Support\BankLogo::assetUrl($acc->bank_name) }}" alt="{{ $acc->bank_name }}" class="h-5 w-auto bg-white px-1.5 py-0.5 rounded object-contain shadow-xs">
                                <p class="font-bold text-darkburgundy text-base" style="font-family:var(--f-tombol)">{{ $acc->bank_name }}</p>
                            </div>
                            <p class="text-xs text-darkburgundy/70">Nama Penerima</p>
                            <p class="text-xs font-semibold text-darkburgundy">{{ $acc->account_name }}</p>
                            <p class="text-xs text-darkburgundy/70 mt-2">Nomor Rekening</p>
                            <div class="flex items-center justify-center gap-2 mt-0.5">
                                <span class="font-mono text-base font-bold text-burgundy">{{ $acc->account_number }}</span>
                                <button type="button" onclick="copyText('{{ $acc->account_number }}', 'Nomor rekening {{ $acc->bank_name }} disalin!')" class="p-1 rounded bg-white text-burgundy hover:bg-burgundy hover:text-white transition shadow-xs" title="Salin Rekening">
                                    <iconify-icon icon="solar:copy-linear" class="text-sm"></iconify-icon>
                                </button>
                            </div>
                        </div>
                    @endforeach
                @elseif(!empty($invitation->bank_name) && !empty($invitation->bank_account_number))
                    <div class="mt-5 p-4 rounded-xl bg-mist/60 border border-burgundy/20 text-center reveal">
                        <div class="flex items-center justify-center gap-2 mb-1.5">
                            <img src="{{ \App\Support\BankLogo::assetUrl($invitation->bank_name) }}" alt="{{ $invitation->bank_name }}" class="h-5 w-auto bg-white px-1.5 py-0.5 rounded object-contain shadow-xs">
                            <p class="font-bold text-darkburgundy text-base" style="font-family:var(--f-tombol)">{{ $invitation->bank_name }}</p>
                        </div>
                        <p class="text-xs text-darkburgundy/70">Nama Penerima</p>
                        <p class="text-xs font-semibold text-darkburgundy">{{ $invitation->bank_account_name ?: $coupleName }}</p>
                        <p class="text-xs text-darkburgundy/70 mt-2">Nomor Rekening</p>
                        <div class="flex items-center justify-center gap-2 mt-0.5">
                            <span class="font-mono text-base font-bold text-burgundy">{{ $invitation->bank_account_number }}</span>
                            <button type="button" onclick="copyText('{{ $invitation->bank_account_number }}', 'Nomor rekening disalin!')" class="p-1 rounded bg-white text-burgundy hover:bg-burgundy hover:text-white transition shadow-xs" title="Salin Rekening">
                                <iconify-icon icon="solar:copy-linear" class="text-sm"></iconify-icon>
                            </button>
                        </div>
                    </div>
                @endif

                <!-- Kirim Kado Fisik -->
                @if(!empty($invitation->gift_address))
                    <div class="mt-4 p-4 rounded-xl bg-white border border-burgundy/20 text-center reveal shadow-xs">
                        <p class="font-serif text-lg text-darkburgundy flex items-center justify-center gap-1.5">
                            <iconify-icon icon="solar:box-linear" class="text-burgundy"></iconify-icon>
                            <span>Kirim Hadiah Fisik</span>
                        </p>
                        <p class="text-xs text-darkburgundy/80 mt-1 leading-relaxed">
                            {!! nl2br(e($invitation->gift_address)) !!}
                        </p>
                        <button type="button" onclick="copyText('{{ addslashes($invitation->gift_address) }}', 'Alamat kado disalin!')" class="btn !bg-mist !text-burgundy !border !border-burgundy/30 hover:!bg-burgundy hover:!text-white !py-1.5 !px-3 !text-xs mt-3">
                            <iconify-icon icon="solar:clipboard-text-linear" class="text-sm"></iconify-icon>
                            <span>Salin Alamat</span>
                        </button>
                    </div>
                @endif
            </section>
        @endif

        <!-- 9. UCAPAN & DOA -->
        <section id="ucapan" class="card">
            <h2 class="judul reveal">Ucapan &amp; Doa</h2>
            <p class="text-xs text-darkburgundy/80 mt-1 reveal">Tuliskan doa dan ucapan selamat untuk mempelai</p>
            
            <form id="wishForm" class="mt-5 space-y-3 text-left reveal" onsubmit="event.preventDefault(); submitWish();">
                <div>
                    <input id="wNama" type="text" value="{{ $guestName !== 'Tamu Undangan' ? $guestName : '' }}" placeholder="Nama Anda" required class="w-full border border-mist bg-mist/40 rounded-lg px-3 py-2 text-sm outline-none focus:border-burgundy focus:bg-white transition">
                </div>
                <div>
                    <textarea id="wPesan" rows="3" placeholder="Tuliskan ucapan & doa terbaik..." required class="w-full border border-mist bg-mist/40 rounded-lg px-3 py-2 text-sm outline-none focus:border-burgundy focus:bg-white transition"></textarea>
                </div>
                <button id="wSubmitBtn" type="submit" class="btn !w-full !justify-center !py-2.5">
                    <iconify-icon icon="solar:plain-bold-duotone" class="text-base"></iconify-icon>
                    <span>Kirim Ucapan</span>
                </button>
            </form>

            <div id="wList" class="mt-5 space-y-2.5 text-left max-h-72 overflow-y-auto pr-1">
                @forelse($invitation->wishes as $wish)
                    <div class="bg-mist/70 border-l-4 border-burgundy rounded-lg p-3 text-xs shadow-xs">
                        <div class="flex items-center justify-between">
                            <b class="text-darkburgundy flex items-center gap-1">
                                <iconify-icon icon="solar:user-circle-linear" class="text-burgundy"></iconify-icon>
                                {{ $wish->name }}
                            </b>
                            <span class="text-[10px] text-darkburgundy/60 flex items-center gap-0.5">
                                <iconify-icon icon="solar:clock-circle-linear" class="text-[10px]"></iconify-icon>
                                {{ $wish->created_at ? $wish->created_at->diffForHumans() : '' }}
                            </span>
                        </div>
                        <p class="mt-1 text-darkburgundy/90 leading-relaxed">{{ $wish->message }}</p>
                    </div>
                @empty
                    <p id="emptyWish" class="text-xs text-center text-darkburgundy/60 py-3">Belum ada ucapan. Jadilah yang pertama mengirimkan doa!</p>
                @endforelse
            </div>
        </section>

        <!-- 10. IG STORY TEMPLATE -->
        @if($igStoryPhoto)
            <section class="card reveal">
                <h2 class="judul">Instagram Stories</h2>
                <p class="text-xs mt-1 text-darkburgundy/80">
                    Ayo bagikan momen bahagia ini ke Instagram Story kamu. Download template IG Story dan mention kami!
                </p>
                <a href="{{ route('invitation.ig-story.download', $invitation->slug) }}" class="btn !w-full !justify-start gap-3.5 !py-4 mt-4 !text-left">
                    <iconify-icon icon="ri:instagram-line" class="text-3xl text-pink-200"></iconify-icon>
                    <span>
                        <small class="block font-normal text-xs text-pink-200">Download Template</small>
                        <span class="text-sm font-bold">Instagram Story</span>
                    </span>
                </a>
            </section>
        @endif

        <!-- 11. PENUTUP -->
        <section class="card reveal">
            <p class="text-xs text-darkburgundy/90 leading-relaxed max-w-xs mx-auto">
                {!! nl2br(e($closingText)) !!}
            </p>
            <p class="text-xs mt-6 text-darkburgundy/70 uppercase tracking-widest font-semibold">Hormat Kami,</p>
            <p class="text-3xl mt-1 text-burgundy" style="font-family: var(--f-judul)">{{ $coupleName }}</p>
            <p class="text-[10px] mt-8 text-darkburgundy/50 tracking-wider uppercase">{{ $footerText }}</p>
        </section>
    </main>

    <!-- ================= FLOATING BOTTOM NAV ================= -->
    <nav id="nav" class="fixed bottom-4 left-1/2 -translate-x-1/2 z-40 bg-white/95 backdrop-blur-md rounded-full shadow-2xl px-2 py-1.5 flex gap-1 translate-y-24 transition-transform duration-700 border border-burgundy/20">
        <a data-t="home" title="Beranda" class="nv">
            <iconify-icon icon="solar:home-2-linear" class="text-lg"></iconify-icon>
        </a>
        <a data-t="mempelai" title="Mempelai" class="nv">
            <iconify-icon icon="solar:heart-linear" class="text-lg"></iconify-icon>
        </a>
        <a data-t="acara" title="Acara" class="nv">
            <iconify-icon icon="solar:calendar-date-linear" class="text-lg"></iconify-icon>
        </a>
        @if($galleryPhotos->count() > 0)
            <a data-t="galeri" title="Galeri" class="nv">
                <iconify-icon icon="solar:gallery-wide-linear" class="text-lg"></iconify-icon>
            </a>
        @endif
        @if($hasBankAccounts || !empty($invitation->gift_address))
            <a data-t="kado" title="Hadiah" class="nv">
                <iconify-icon icon="solar:gift-linear" class="text-lg"></iconify-icon>
            </a>
        @endif
        <a data-t="ucapan" title="Ucapan" class="nv">
            <iconify-icon icon="solar:chat-round-dots-linear" class="text-lg"></iconify-icon>
        </a>
    </nav>

    <!-- ================= TOMBOL QR CODE (TAB SAMPING) ================= -->
    <button type="button" onclick="document.getElementById('qrModal').classList.remove('hidden')" class="fixed right-0 top-1/2 -translate-y-1/2 z-40 bg-white text-darkburgundy text-[10px] font-bold rounded-l-xl px-2 py-3 flex flex-col items-center gap-1 shadow-lg hover:bg-mist transition border-y border-l border-burgundy/20">
        <iconify-icon icon="solar:qr-code-linear" class="text-base text-burgundy"></iconify-icon>
        <span class="[writing-mode:vertical-rl] tracking-widest">QR CODE</span>
    </button>

    <!-- ================= TOMBOL MUSIK ================= -->
    <button id="musicBtn" type="button" class="fixed top-4 right-4 z-40 w-10 h-10 rounded-full bg-white/90 text-burgundy shadow-lg hidden flex items-center justify-center border border-burgundy/20 hover:bg-white transition">
        <iconify-icon icon="solar:music-note-2-bold" class="text-lg text-burgundy"></iconify-icon>
    </button>
    @if($musicUrl)
        <audio id="bgm" src="{{ $musicUrl }}" loop preload="auto"></audio>
    @endif

    <!-- ================= MODAL RSVP ================= -->
    <div id="rsvpModal" class="hidden fixed inset-0 z-[90] bg-darkburgundy/70 backdrop-blur-xs flex items-center justify-center p-5" onclick="this.classList.add('hidden')">
        <div class="card w-full max-w-sm zoom show relative border border-burgundy/30 shadow-2xl" onclick="event.stopPropagation()">
            <button type="button" onclick="document.getElementById('rsvpModal').classList.add('hidden')" class="absolute top-4 right-4 text-darkburgundy/60 hover:text-darkburgundy transition">
                <iconify-icon icon="solar:close-circle-linear" class="text-2xl"></iconify-icon>
            </button>
            <h3 class="judul !text-3xl">Konfirmasi Kehadiran</h3>
            <p class="text-xs text-darkburgundy/75 mt-1">{{ $guestName }}</p>

            <form id="rsvpForm" class="mt-4 space-y-3 text-left" onsubmit="event.preventDefault(); submitRsvp();">
                <div>
                    <label class="text-[11px] font-bold text-darkburgundy uppercase">Status Kehadiran</label>
                    <select id="rsvpStatus" class="w-full mt-1 border border-mist bg-mist/40 rounded-lg px-3 py-2 text-sm text-darkburgundy outline-none focus:border-burgundy focus:bg-white">
                        <option value="attending">Hadir</option>
                        <option value="not_attending">Tidak Hadir</option>
                        <option value="maybe">Masih Ragu</option>
                    </select>
                </div>
                <div>
                    <label class="text-[11px] font-bold text-darkburgundy uppercase">Jumlah Tamu</label>
                    <select id="rsvpPax" class="w-full mt-1 border border-mist bg-mist/40 rounded-lg px-3 py-2 text-sm text-darkburgundy outline-none focus:border-burgundy focus:bg-white">
                        <option value="1">1 Orang</option>
                        <option value="2">2 Orang</option>
                        <option value="3">3 Orang</option>
                        <option value="4">4 Orang</option>
                    </select>
                </div>
                <div>
                    <label class="text-[11px] font-bold text-darkburgundy uppercase">Pesan Tambahan (Opsional)</label>
                    <textarea id="rsvpMessage" rows="2" placeholder="Pesan atau catatan..." class="w-full mt-1 border border-mist bg-mist/40 rounded-lg px-3 py-2 text-sm text-darkburgundy outline-none focus:border-burgundy focus:bg-white"></textarea>
                </div>
                <button id="rsvpSubmitBtn" type="submit" class="btn !w-full !justify-center !py-2.5 mt-2">
                    <iconify-icon icon="solar:check-circle-linear" class="text-base"></iconify-icon>
                    <span>Kirim Konfirmasi</span>
                </button>
            </form>
        </div>
    </div>

    <!-- ================= MODAL QR CODE TAMU ================= -->
    <div id="qrModal" class="hidden fixed inset-0 z-[90] bg-darkburgundy/75 flex items-center justify-center p-6" onclick="this.classList.add('hidden')">
        <div class="card w-full max-w-sm zoom show relative shadow-2xl border border-burgundy/30" onclick="event.stopPropagation()">
            <button type="button" onclick="document.getElementById('qrModal').classList.add('hidden')" class="absolute top-4 right-4 text-darkburgundy/60 hover:text-darkburgundy transition">
                <iconify-icon icon="solar:close-circle-linear" class="text-2xl"></iconify-icon>
            </button>
            <div class="w-12 h-12 mx-auto rounded-full bg-mist flex items-center justify-center text-burgundy mb-2">
                <iconify-icon icon="solar:qr-code-bold-duotone" class="text-2xl"></iconify-icon>
            </div>
            <h3 class="judul !text-3xl">QR Code Tamu</h3>
            <p class="text-xs text-darkburgundy font-semibold mt-1">{{ $guestName }}</p>

            <div class="w-48 h-48 mx-auto my-4 bg-white p-2 rounded-xl flex items-center justify-center border border-burgundy/20 shadow-inner">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data={{ urlencode($guestInvitationUrl) }}" class="w-full h-full object-contain" alt="QR Code">
            </div>

            @if(!empty($guest?->token))
                <p class="font-mono text-[11px] text-darkburgundy/70 font-bold tracking-wider">TOKEN: {{ $guest->getPublicToken() }}</p>
            @endif
            <p class="text-xs text-darkburgundy/70 mt-1">Tunjukkan QR code ini kepada panitia saat tiba di lokasi acara.</p>
            <button type="button" onclick="document.getElementById('qrModal').classList.add('hidden')" class="btn !py-2 !px-6 !text-xs mt-4">
                <span>Tutup</span>
            </button>
        </div>
    </div>

    <!-- ================= MODAL LIGHTBOX FOTO ================= -->
    <div id="lightbox" class="hidden fixed inset-0 z-[99] bg-darkburgundy/95 flex items-center justify-center p-4" onclick="closeLightbox()">
        <button type="button" class="absolute top-5 right-5 text-white/80 hover:text-white transition" onclick="closeLightbox()">
            <iconify-icon icon="solar:close-circle-linear" class="text-4xl"></iconify-icon>
        </button>
        <img id="lightboxImg" src="" alt="Preview" class="max-w-full max-h-[90vh] object-contain rounded-lg shadow-2xl border border-white/20" onclick="event.stopPropagation()">
    </div>

    <!-- ================= JAVASCRIPT ================= -->
    <script>
        const $ = s => document.querySelector(s);
        const tokenMeta = $('meta[name="csrf-token"]')?.getAttribute('content');

        // Loader
        addEventListener('load', () => setTimeout(() => {
            const loader = $('#loader');
            if (loader) {
                loader.style.opacity = '0';
                setTimeout(() => loader.remove(), 700);
            }
        }, 800));

        // Countdown Timer
        const target = new Date("{{ $targetIso }}");
        function cd() {
            const now = new Date();
            const s = Math.max(0, Math.floor((target - now) / 1000));
            const v = [
                Math.floor(s / 86400),
                Math.floor((s % 86400) / 3600),
                Math.floor((s % 3600) / 60),
                s % 60
            ];
            const l = ['Hari', 'Jam', 'Menit', 'Detik'];
            const cdEl = $('#cd');
            if (cdEl) {
                cdEl.innerHTML = v.map((n, i) => `
                    <div class="text-center">
                        <div class="w-13 h-13 px-2 py-2 rounded-xl bg-burgundy text-white flex items-center justify-center text-lg font-bold shadow-md">
                            ${String(n).padStart(2, '0')}
                        </div>
                        <p class="text-[10px] mt-1 text-darkburgundy font-semibold">${l[i]}</p>
                    </div>
                `).join('');
            }
        }
        cd();
        setInterval(cd, 1000);

        // Buka Undangan & Autoplay Musik
        const bgm = $('#bgm');
        let playing = false;

        $('#openBtn').onclick = () => {
            const cover = $('#cover');
            cover.classList.add('hide');
            setTimeout(() => cover.remove(), 1200);

            $('#nav').classList.remove('translate-y-24');
            
            if (bgm) {
                $('#musicBtn').classList.remove('hidden');
                bgm.play().then(() => {
                    playing = true;
                    $('#musicBtn').innerHTML = '<iconify-icon icon="solar:music-note-2-bold" class="text-lg text-burgundy animate-pulse"></iconify-icon>';
                }).catch(() => {
                    playing = false;
                    $('#musicBtn').innerHTML = '<iconify-icon icon="solar:volume-cross-bold" class="text-lg text-burgundy/60"></iconify-icon>';
                });
            }
        };

        // Toggle Musik
        $('#musicBtn').onclick = () => {
            if (!bgm) return;
            if (playing) {
                bgm.pause();
                $('#musicBtn').innerHTML = '<iconify-icon icon="solar:volume-cross-bold" class="text-lg text-burgundy/60"></iconify-icon>';
            } else {
                bgm.play().catch(() => {});
                $('#musicBtn').innerHTML = '<iconify-icon icon="solar:music-note-2-bold" class="text-lg text-burgundy animate-pulse"></iconify-icon>';
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
        }, { threshold: 0.12 });

        const watch = () => document.querySelectorAll('.reveal, .fade-l, .fade-r, .zoom').forEach(el => io.observe(el));
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

        window.addEventListener('scroll', () => {
            let cur = 0;
            activeSecs.forEach((s, i) => {
                if (s.getBoundingClientRect().top < window.innerHeight / 2) {
                    cur = i;
                }
            });
            document.querySelectorAll('.nv').forEach((btn, i) => {
                const match = activeSecs[cur] && btn.dataset.t === activeSecs[cur].id;
                btn.classList.toggle('on', match);
            });
        });

        // Submit Ucapan Langsung ke Backend
        async function submitWish() {
            const name = $('#wNama').value.trim();
            const message = $('#wPesan').value.trim();
            const submitBtn = $('#wSubmitBtn');

            if (!name || !message) {
                showToast('Mohon lengkapi nama dan ucapan Anda');
                return;
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<iconify-icon icon="solar:restart-bold" class="text-base animate-spin"></iconify-icon> <span>Mengirimkan...</span>';

            try {
                await fetch("{{ route('invitation.wish', $invitation->slug) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': tokenMeta,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ name, message })
                });

                const esc = t => t.replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
                const newWishCard = `
                    <div class="bg-mist/70 border-l-4 border-burgundy rounded-lg p-3 text-xs shadow-xs transition">
                        <div class="flex items-center justify-between">
                            <b class="text-darkburgundy flex items-center gap-1">
                                <iconify-icon icon="solar:user-circle-linear" class="text-burgundy"></iconify-icon>
                                ${esc(name)}
                            </b>
                            <span class="text-[10px] text-darkburgundy/60 flex items-center gap-0.5">
                                <iconify-icon icon="solar:clock-circle-linear" class="text-[10px]"></iconify-icon>
                                Baru saja
                            </span>
                        </div>
                        <p class="mt-1 text-darkburgundy/90 leading-relaxed">${esc(message)}</p>
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
                submitBtn.innerHTML = '<iconify-icon icon="solar:plain-bold-duotone" class="text-base"></iconify-icon> <span>Kirim Ucapan</span>';
            }
        }

        // Submit RSVP Modal Langsung ke Backend
        async function submitRsvp() {
            const status = $('#rsvpStatus').value;
            const pax = $('#rsvpPax').value;
            const message = $('#rsvpMessage').value.trim();
            const submitBtn = $('#rsvpSubmitBtn');

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<iconify-icon icon="solar:restart-bold" class="text-base animate-spin"></iconify-icon> <span>Mengirim...</span>';

            try {
                await fetch("{{ route('invitation.rsvp', $invitation->slug) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': tokenMeta,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        name: "{{ $guestName }}",
                        status,
                        pax: parseInt(pax) || 1,
                        message,
                        @if(!empty($guest?->id))
                            guest_id: {{ $guest->id }},
                        @endif
                    })
                });

                showToast('Konfirmasi kehadiran berhasil dikirim!');
                document.getElementById('rsvpModal').classList.add('hidden');
            } catch (err) {
                showToast('Konfirmasi berhasil dicatat!');
                document.getElementById('rsvpModal').classList.add('hidden');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<iconify-icon icon="solar:check-circle-linear" class="text-base"></iconify-icon> <span>Kirim Konfirmasi</span>';
            }
        }

        // Efek Kelopak Bunga Burgundy Jatuh
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

