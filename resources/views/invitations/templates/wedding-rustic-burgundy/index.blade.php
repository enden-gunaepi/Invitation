<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $isDemoMode = (bool) ($demoMode ?? false);
        $personalization = $personalization ?? ['greeting' => 'Tamu Undangan', 'cta' => 'Konfirmasi Kehadiran', 'rsvp_hint' => 'Mohon konfirmasi kehadiran Anda.'];

        $groomName = $invitation->groom_name ?: 'Dion';
        $brideName = $invitation->bride_name ?: 'Deva';
        $coupleName = trim(($invitation->bride_name ?? 'Deva') . ' & ' . ($invitation->groom_name ?? 'Dion'));

        $guestName = $guest->name ?? (request('to') ?: 'Tamu Undangan');
        $guestInvitationUrl = !empty($guest?->token) ? $guest->getInvitationUrl() : $invitation->getPublicUrl();

        $coverPhoto = $invitation->cover_photo ? asset('storage/' . $invitation->cover_photo) : null;
        $groomPhoto = $invitation->groom_photo ? asset('storage/' . $invitation->groom_photo) : null;
        $bridePhoto = $invitation->bride_photo ? asset('storage/' . $invitation->bride_photo) : null;
        $igStoryPhoto = $invitation->ig_story_photo ? asset('storage/' . $invitation->ig_story_photo) : null;
        $musicUrl = $invitation->music_signed_url ?? ($invitation->music_url ? asset('storage/' . $invitation->music_url) : null);

        $galleryPhotos = $invitation->photos;
        $heroPhoto = $coverPhoto ?: ($galleryPhotos->first() ? asset('storage/' . $galleryPhotos->first()->file_path) : null);

        $eventDateText = $invitation->event_date ? $invitation->event_date->translatedFormat('l, d F Y') : 'Kamis, 31 Desember 2026';
        $eventDayName = $invitation->event_date ? $invitation->event_date->translatedFormat('l') : 'Kamis';
        $eventDayNumber = $invitation->event_date ? $invitation->event_date->format('d') : '31';
        $eventMonthName = $invitation->event_date ? $invitation->event_date->translatedFormat('F') : 'Desember';
        $eventYearNumber = $invitation->event_date ? $invitation->event_date->format('Y') : '2026';

        $eventDateIso = $invitation->event_date ? $invitation->event_date->format('Y-m-d') : '2026-12-31';
        $eventTimeIso = $invitation->event_time ? \Carbon\Carbon::parse($invitation->event_time)->format('H:i:s') : '08:00:00';
        $targetIso = $eventDateIso . 'T' . $eventTimeIso . '+07:00';

        $openingText = $invitation->opening_text 
            ?: 'Dengan memohon rahmat dan ridho Allah SWT, kami mengundang Bapak/Ibu/Saudara/(i) untuk menghadiri hari bahagia pernikahan kami.';
        $closingText = $invitation->closing_text 
            ?: 'Atas kehadiran Saudara/(i) & doa restunya, kami ucapkan terima kasih.';
        $footerText = $invitation->footer_text 
            ?: 'Dibuat dengan cinta oleh ' . config('app.name', 'GNVisual');

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
    <link href="https://fonts.googleapis.com/css2?family=Almendra:ital,wght@0,400;0,700;1,400&family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400&family=Amiri&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        marun: '#6b0000',
                        marun2: '#3a0000',
                        rose: '#fcd5d9',
                        darkwine: '#1a0000',
                    }
                }
            }
        }
    </script>
    <style>
        :root {
            --f-utama: 'Almendra', serif;
            --f-italic: 'Cormorant Garamond', serif;
            --f-arabic: 'Amiri', serif;
            --color-marun: #6b0000;
            --color-marun2: #3a0000;
            --color-rose: #fcd5d9;
        }
        html { scroll-behavior: smooth; }
        body { font-family: var(--f-utama); background: #1a0000; color: #fff; }
        .h2 { font-size: 2.1rem; line-height: 1.2; font-weight: 700; }
        .btn {
            background: #6b0000;
            color: #fff;
            border-radius: 0.5rem;
            padding: 0.7rem 1.2rem;
            display: inline-flex;
            gap: 0.5rem;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            transition: all 0.3s;
            cursor: pointer;
        }
        .btn:hover { background: #3a0000; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(58,0,0,.4); }
        .btn-rose {
            background: #fcd5d9;
            color: #6b0000;
            border-radius: 9999px;
            padding: 0.65rem 1.4rem;
            font-size: 0.85rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.3s;
            cursor: pointer;
        }
        .btn-rose:hover { transform: scale(1.06); box-shadow: 0 4px 15px rgba(252,213,217,.4); }
        .ph { background: linear-gradient(135deg, #8b1a1a, #3a0000); }
        .ph-rose { background: linear-gradient(135deg, #f3b9bf, #e59aa3); }
        .reveal { opacity: 0; transform: translateY(40px); transition: all 1s cubic-bezier(.2,.7,.2,1); }
        .reveal.show { opacity: 1; transform: none; }
        .fade-l { opacity: 0; transform: translateX(-40px); transition: all 1s cubic-bezier(.2,.7,.2,1); }
        .fade-l.show { opacity: 1; transform: none; }
        .fade-r { opacity: 0; transform: translateX(40px); transition: all 1s cubic-bezier(.2,.7,.2,1); }
        .fade-r.show { opacity: 1; transform: none; }
        .zoom { opacity: 0; transform: scale(.85); transition: all 1s cubic-bezier(.2,.7,.2,1); }
        .zoom.show { opacity: 1; transform: none; }
        @keyframes kb { from { transform: scale(1); } to { transform: scale(1.1); } }
        .kb { animation: kb 16s ease-in-out infinite alternate; }
        @keyframes spin { to { transform: rotate(360deg); } }
        @keyframes beat { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.25); } }
        .beat { animation: beat 1.4s infinite; }
        @keyframes fall {
            0% { transform: translate3d(0, -10vh, 0) rotate(0); opacity: 0; }
            15% { opacity: .85; }
            100% { transform: translate3d(70px, 110vh, 0) rotate(320deg); opacity: 0; }
        }
        .petal {
            position: fixed;
            top: 0;
            width: 9px;
            height: 9px;
            border-radius: 50% 0 50% 0;
            background: #c2434f;
            pointer-events: none;
            z-index: 5;
            animation: fall linear infinite;
        }
        @keyframes draw { to { stroke-dashoffset: 0; } }
        .line { stroke-dasharray: 400; stroke-dashoffset: 400; }
        .show .line { animation: draw 3s forwards; }
        #cover { transition: transform 1.2s cubic-bezier(.7,0,.2,1); }
        #cover.hide { transform: translateY(-100%); pointer-events: none; }
        #panel::-webkit-scrollbar { width: 5px; }
        #panel::-webkit-scrollbar-thumb { background: #6b0000; border-radius: 4px; }
        .dot { border-left: 3px dotted #6b0000; }
        .nv {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 1.15rem;
            transition: all 0.3s;
            color: #6b0000;
        }
        .nv.on {
            background: #6b0000;
            color: #fff;
            box-shadow: 0 4px 12px rgba(107,0,0,.35);
        }
        .custom-toast {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%) translateY(-100px);
            background: rgba(58, 0, 0, 0.95);
            color: #fcd5d9;
            padding: 12px 24px;
            border-radius: 9999px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.4);
            border: 1px solid #fcd5d9;
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
        <iconify-icon icon="solar:check-circle-bold" class="text-xl text-rose"></iconify-icon>
        <span id="toast-message">Pesan notifikasi</span>
    </div>

    <!-- LOADER -->
    <div id="loader" class="fixed inset-0 z-[100] bg-rose flex items-center justify-center transition-opacity duration-700">
        <div class="w-10 h-10 border-2 border-marun/30 border-t-marun rounded-full" style="animation:spin 1s linear infinite"></div>
    </div>

    <!-- LATAR DESKTOP (kiri): Foto Pasangan + Overlay Merah Marun -->
    <div class="hidden lg:block fixed inset-0 ph overflow-hidden">
        @if($coverPhoto || $heroPhoto)
            <img src="{{ $coverPhoto ?: $heroPhoto }}" class="kb w-full h-full object-cover opacity-75" alt="{{ $coupleName }}">
        @else
            <img src="https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=1400&q=80" class="kb w-full h-full object-cover opacity-70" alt="{{ $coupleName }}">
        @endif
        <div class="absolute inset-0 bg-gradient-to-r from-marun2/90 via-marun2/70 to-marun2/50"></div>
        <div class="absolute left-[12%] top-1/2 -translate-y-1/2 text-left max-w-md">
            <p class="text-sm tracking-widest text-rose uppercase font-semibold">The Wedding Celebration</p>
            <h1 class="text-5xl font-bold mt-2 text-white">{{ $coupleName }}</h1>
            <p class="text-base text-rose/90 mt-4 font-italic" style="font-family:var(--f-italic)">{{ $eventDateText }}</p>
            <div class="w-16 h-0.5 bg-rose/60 mt-4"></div>
            <p class="text-xs text-white/80 mt-4 leading-relaxed">{{ $openingText }}</p>
        </div>
    </div>

    <!-- KOLOM UNDANGAN MOBILE / RIGHT FRAME -->
    <main class="fixed inset-y-0 right-0 w-full lg:w-[480px] bg-white shadow-2xl z-20">

        <!-- ============ 1. COVER ============ -->
        <section id="cover" class="absolute inset-0 z-40 ph overflow-hidden">
            @if($coverPhoto)
                <img src="{{ $coverPhoto }}" class="kb absolute inset-0 w-full h-full object-cover" alt="{{ $coupleName }}">
            @else
                <img src="https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=1080&q=80" class="kb absolute inset-0 w-full h-full object-cover" alt="{{ $coupleName }}">
            @endif
            <div class="absolute inset-0 bg-gradient-to-b from-marun2/75 via-marun2/35 to-marun2/95"></div>
            
            <div class="absolute top-10 right-6 text-right zoom show">
                <p class="text-xs tracking-widest uppercase text-rose font-semibold">Undangan Pernikahan</p>
                <h1 class="text-3xl font-bold mt-1 text-white">{{ $coupleName }}</h1>
            </div>

            <div class="absolute bottom-8 left-6 right-6 text-right reveal show">
                <p class="text-xs text-rose tracking-wider uppercase font-semibold">Kepada Yth:</p>
                <p id="guest" class="text-2xl font-bold text-white mt-0.5">{{ $guestName }}</p>
                <p class="text-xs text-rose/90 mt-5">{{ $eventDateText }}</p>
                <div class="h-px bg-white/70 my-3"></div>
                <button id="openBtn" class="btn-rose beat-hover mt-1">
                    <iconify-icon icon="solar:letter-opened-bold-duotone" class="text-xl"></iconify-icon>
                    <span>Buka Undangan</span>
                </button>
            </div>
        </section>

        <!-- ============ ISI UNDANGAN (SCROLLABLE PANEL) ============ -->
        <div id="panel" class="h-full overflow-y-auto text-marun relative">

            <!-- 2. HERO SECTION -->
            <section id="home" class="relative h-screen ph overflow-hidden text-white">
                @if($heroPhoto)
                    <img src="{{ $heroPhoto }}" class="kb absolute inset-0 w-full h-full object-cover" alt="{{ $coupleName }}">
                @else
                    <img src="https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=1080&q=80" class="kb absolute inset-0 w-full h-full object-cover" alt="{{ $coupleName }}">
                @endif
                <div class="absolute inset-0 bg-gradient-to-b from-transparent via-marun2/20 to-marun2/95"></div>
                
                <div class="absolute bottom-12 w-full px-8 text-center">
                    <p class="text-xs uppercase tracking-widest text-rose font-semibold">The Wedding Of</p>
                    <h2 class="text-4xl font-bold zoom mt-1">{{ $coupleName }}</h2>
                    <div class="h-px bg-white/70 my-4 max-w-[200px] mx-auto"></div>
                    <div id="cd" class="flex justify-center gap-2.5"></div>
                </div>
            </section>

            <!-- 3. QUOTE SECTION -->
            <section class="bg-marun text-white text-center px-8 py-10 relative overflow-hidden">
                <div class="absolute -right-8 -top-8 w-24 h-24 rounded-full bg-white/5 pointer-events-none"></div>
                <p class="reveal text-lg leading-relaxed text-rose/95" style="font-family:var(--f-italic);font-style:italic">
                    "Love does not consist in gazing at each other, but in looking outward together in the same direction."
                </p>
                <p class="reveal text-xs font-bold mt-3 tracking-widest text-white uppercase">— Antoine de Saint-Exupéry</p>
            </section>

            <!-- 4. MEMPELAI SECTION -->
            <section id="mempelai" class="bg-rose px-6 py-14 text-center relative overflow-hidden">
                <p class="text-xs leading-relaxed text-marun reveal max-w-sm mx-auto">
                    {{ $openingText }}
                </p>

                <!-- Mempelai Pria -->
                <div class="mt-10 fade-l">
                    <div class="w-44 h-44 mx-auto rounded-3xl overflow-hidden ph shadow-xl border-4 border-white/60">
                        @if($groomPhoto)
                            <img src="{{ $groomPhoto }}" class="w-full h-full object-cover" alt="{{ $groomName }}">
                        @else
                            <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=500&q=80" class="w-full h-full object-cover" alt="{{ $groomName }}">
                        @endif
                    </div>
                    <h3 class="h2 mt-4 text-marun">{{ $invitation->groom_name ?: 'Dion Putra Dewa' }}</h3>
                    @if($invitation->groom_parent_name)
                        <p class="text-xs text-marun/80 mt-1">Putra dari {{ $invitation->groom_parent_name }}</p>
                    @endif

                    <!-- Sosmed Groom -->
                    <div class="flex justify-center gap-2 mt-3">
                        @if($invitation->groom_instagram)
                            <a href="https://instagram.com/{{ ltrim($invitation->groom_instagram, '@') }}" target="_blank" class="w-9 h-9 rounded-full bg-marun text-white flex items-center justify-center text-sm hover:scale-110 transition shadow-sm" title="Instagram">
                                <iconify-icon icon="ri:instagram-line"></iconify-icon>
                            </a>
                        @endif
                        @if($invitation->groom_facebook)
                            <a href="{{ $invitation->groom_facebook }}" target="_blank" class="w-9 h-9 rounded-full bg-marun text-white flex items-center justify-center text-sm hover:scale-110 transition shadow-sm" title="Facebook">
                                <iconify-icon icon="ri:facebook-fill"></iconify-icon>
                            </a>
                        @endif
                        @if(!$invitation->groom_instagram && !$invitation->groom_facebook)
                            <a href="#" class="w-9 h-9 rounded-full bg-marun text-white flex items-center justify-center text-sm hover:scale-110 transition shadow-sm" title="Instagram">
                                <iconify-icon icon="ri:instagram-line"></iconify-icon>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Simbol & -->
                <svg class="w-24 mx-auto my-7 reveal" viewBox="0 0 100 100">
                    <text x="50" y="80" text-anchor="middle" font-size="90" fill="#6b0000" opacity=".25" font-family="'Almendra', serif">&amp;</text>
                </svg>

                <!-- Mempelai Wanita -->
                <div class="fade-r">
                    <div class="w-44 h-44 mx-auto rounded-3xl overflow-hidden ph shadow-xl border-4 border-white/60">
                        @if($bridePhoto)
                            <img src="{{ $bridePhoto }}" class="w-full h-full object-cover" alt="{{ $brideName }}">
                        @else
                            <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=500&q=80" class="w-full h-full object-cover" alt="{{ $brideName }}">
                        @endif
                    </div>
                    <h3 class="h2 mt-4 text-marun">{{ $invitation->bride_name ?: 'Deva Nada Putri' }}</h3>
                    @if($invitation->bride_parent_name)
                        <p class="text-xs text-marun/80 mt-1">Putri dari {{ $invitation->bride_parent_name }}</p>
                    @endif

                    <!-- Sosmed Bride -->
                    <div class="flex justify-center gap-2 mt-3">
                        @if($invitation->bride_instagram)
                            <a href="https://instagram.com/{{ ltrim($invitation->bride_instagram, '@') }}" target="_blank" class="w-9 h-9 rounded-full bg-marun text-white flex items-center justify-center text-sm hover:scale-110 transition shadow-sm" title="Instagram">
                                <iconify-icon icon="ri:instagram-line"></iconify-icon>
                            </a>
                        @endif
                        @if($invitation->bride_facebook)
                            <a href="{{ $invitation->bride_facebook }}" target="_blank" class="w-9 h-9 rounded-full bg-marun text-white flex items-center justify-center text-sm hover:scale-110 transition shadow-sm" title="Facebook">
                                <iconify-icon icon="ri:facebook-fill"></iconify-icon>
                            </a>
                        @endif
                        @if(!$invitation->bride_instagram && !$invitation->bride_facebook)
                            <a href="#" class="w-9 h-9 rounded-full bg-marun text-white flex items-center justify-center text-sm hover:scale-110 transition shadow-sm" title="Instagram">
                                <iconify-icon icon="ri:instagram-line"></iconify-icon>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Ornamen Garis Bunga Tergambar -->
                <svg class="w-44 mx-auto mt-10 reveal" viewBox="0 0 200 100" fill="none" stroke="#6b0000" stroke-width="1.3">
                    <path class="line" d="M10 80 C50 20 90 90 120 40 S180 30 190 60 M60 50 C70 30 90 30 95 50 M120 40 C130 20 150 25 150 45"/>
                </svg>
            </section>

            <!-- 5. ACARA SECTION -->
            <section id="acara" class="bg-white px-6 py-14 text-center">
                <h2 class="h2 reveal text-marun">Rangkaian Acara</h2>
                <p class="text-xs text-marun/80 mt-1 reveal">Dengan penuh rasa syukur, kami mengundang kehadiran Bapak/Ibu/Saudara/(i) pada:</p>

                @if($hasEvents)
                    @foreach($invitation->events as $index => $event)
                        <div class="mt-8 reveal">
                            <h3 class="text-xl font-bold text-marun">{{ $event->event_name }}</h3>
                            <p class="text-xs text-marun/80 mt-1">
                                {{ $event->event_date ? $event->event_date->translatedFormat('l, d F Y') : $eventDateText }}
                                <br>
                                @if($event->event_time)
                                    {{ \Carbon\Carbon::parse($event->event_time)->format('H:i') }}
                                    @if($event->event_end_time)
                                        - {{ \Carbon\Carbon::parse($event->event_end_time)->format('H:i') }}
                                    @endif
                                    WIB
                                @endif
                            </p>
                            @if($event->venue_name)
                                <p class="font-bold mt-2 text-sm text-marun">{{ $event->venue_name }}</p>
                            @endif
                            @if($event->venue_address)
                                <p class="text-xs text-marun/70 max-w-xs mx-auto">{{ $event->venue_address }}</p>
                            @endif

                            @php
                                $eventCalendarUrl = null;
                                if ($event->event_date && $event->event_time) {
                                    $startDT = \Carbon\Carbon::parse($event->event_date->format('Y-m-d') . ' ' . $event->event_time);
                                    $endDT = $event->event_end_time 
                                        ? \Carbon\Carbon::parse($event->event_date->format('Y-m-d') . ' ' . $event->event_end_time) 
                                        : (clone $startDT)->addHours(2);
                                    $eventCalendarUrl = 'https://calendar.google.com/calendar/render?action=TEMPLATE&text=' . urlencode($event->event_name . ' - ' . $coupleName) . '&dates=' . $startDT->utc()->format('Ymd\THis\Z') . '/' . $endDT->utc()->format('Ymd\THis\Z') . '&details=' . urlencode('Pernikahan ' . $coupleName) . '&location=' . urlencode($event->venue_name . ', ' . $event->venue_address);
                                }
                            @endphp

                            <div class="flex justify-center gap-2.5 mt-4">
                                @if($eventCalendarUrl || $calendarUrl)
                                    <a href="{{ $eventCalendarUrl ?: $calendarUrl }}" target="_blank" class="btn !py-2 !px-3.5 !text-xs">
                                        <iconify-icon icon="solar:calendar-add-bold-duotone" class="text-base"></iconify-icon>
                                        <span>Simpan Tanggal</span>
                                    </a>
                                @endif
                                <a href="{{ $event->venue_maps_url ?: $mapsTrackedUrl }}" target="_blank" class="btn !py-2 !px-3.5 !text-xs !bg-marun2">
                                    <iconify-icon icon="solar:map-point-wave-bold" class="text-base"></iconify-icon>
                                    <span>Petunjuk Arah</span>
                                </a>
                            </div>
                        </div>

                        @if(!$loop->last)
                            <hr class="border-dashed border-marun/30 my-8">
                        @endif
                    @endforeach
                @else
                    <!-- Fallback Akad Nikah -->
                    <div class="mt-8 reveal">
                        <h3 class="text-xl font-bold text-marun">Akad Nikah</h3>
                        <p class="text-xs text-marun/80 mt-1">
                            {{ $eventDateText }}<br>08.00 - 10.00 WIB
                        </p>
                        <p class="font-bold mt-2 text-sm text-marun">{{ $invitation->venue_name ?: 'Gedung Serbaguna Mawar' }}</p>
                        <p class="text-xs text-marun/70">{{ $invitation->venue_address ?: 'Jl. Contoh Alamat No. 12, Bandung' }}</p>
                        <div class="flex justify-center gap-2.5 mt-4">
                            @if($calendarUrl)
                                <a href="{{ $calendarUrl }}" target="_blank" class="btn !py-2 !px-3.5 !text-xs">
                                    <iconify-icon icon="solar:calendar-add-bold-duotone" class="text-base"></iconify-icon>
                                    <span>Simpan Tanggal</span>
                                </a>
                            @endif
                            <a href="{{ $mapsTrackedUrl }}" target="_blank" class="btn !py-2 !px-3.5 !text-xs !bg-marun2">
                                <iconify-icon icon="solar:map-point-wave-bold" class="text-base"></iconify-icon>
                                <span>Petunjuk Arah</span>
                            </a>
                        </div>
                    </div>

                    <hr class="border-dashed border-marun/30 my-8">

                    <!-- Fallback Resepsi -->
                    <div class="reveal">
                        <h3 class="text-xl font-bold text-marun">Resepsi Pernikahan</h3>
                        <p class="text-xs text-marun/80 mt-1">
                            {{ $eventDateText }}<br>11.00 - 14.00 WIB
                        </p>
                        <p class="font-bold mt-2 text-sm text-marun">{{ $invitation->venue_name ?: 'Gedung Serbaguna Mawar' }}</p>
                        <p class="text-xs text-marun/70">{{ $invitation->venue_address ?: 'Jl. Contoh Alamat No. 12, Bandung' }}</p>
                        <div class="flex justify-center gap-2.5 mt-4">
                            @if($calendarUrl)
                                <a href="{{ $calendarUrl }}" target="_blank" class="btn !py-2 !px-3.5 !text-xs">
                                    <iconify-icon icon="solar:calendar-add-bold-duotone" class="text-base"></iconify-icon>
                                    <span>Simpan Tanggal</span>
                                </a>
                            @endif
                            <a href="{{ $mapsTrackedUrl }}" target="_blank" class="btn !py-2 !px-3.5 !text-xs !bg-marun2">
                                <iconify-icon icon="solar:map-point-wave-bold" class="text-base"></iconify-icon>
                                <span>Petunjuk Arah</span>
                            </a>
                        </div>
                    </div>
                @endif
            </section>

            <!-- 6. LIVE STREAMING (Opsional) -->
            @if($invitation->livestream_enabled && !empty($invitation->livestream_url))
                <section class="bg-rose px-6 py-12 text-center reveal">
                    <h2 class="h2 text-marun">Live Streaming</h2>
                    <p class="text-xs mt-2 text-marun/80 max-w-xs mx-auto">
                        Acara ini akan disiarkan langsung melalui media internet. Silakan klik tombol di bawah untuk membuka saluran live streaming.
                    </p>
                    <a href="{{ $invitation->livestream_url }}" target="_blank" class="btn mt-5 !py-2.5 !px-5 shadow-md">
                        <iconify-icon icon="solar:videocamera-record-bold-duotone" class="text-xl text-rose"></iconify-icon>
                        <span>{{ $invitation->livestream_label ?: 'Buka Live Streaming' }}</span>
                    </a>
                </section>
            @endif

            <!-- 7. KISAH CINTA -->
            <section id="kisah" class="bg-white px-5 py-14 text-center">
                <h2 class="h2 reveal text-marun">Kisah Cinta</h2>
                <div class="text-marun text-xl my-2 beat flex justify-center">
                    <iconify-icon icon="solar:heart-bold"></iconify-icon>
                </div>
                <div id="storyWrap" class="space-y-0">
                    @if($invitation->loveStories && $invitation->loveStories->count() > 0)
                        @foreach($invitation->loveStories as $i => $story)
                            <div class="mx-auto w-0 h-8 dot"></div>
                            <div class="bg-rose/40 border border-rose rounded-3xl shadow-md overflow-hidden text-center {{ $i % 2 ? 'fade-r' : 'fade-l' }}">
                                @if($story->photo_path)
                                    <div class="h-36 ph relative">
                                        <img src="{{ asset('storage/' . $story->photo_path) }}" class="w-full h-full object-cover" alt="{{ $story->title }}">
                                        <div class="absolute inset-x-0 bottom-0 h-16 bg-gradient-to-t from-white to-transparent"></div>
                                    </div>
                                @endif
                                <div class="p-5">
                                    @if($story->year)
                                        <span class="inline-block px-3 py-0.5 rounded-full bg-marun text-white text-[10px] font-bold tracking-wider mb-1">{{ $story->year }}</span>
                                    @endif
                                    <h4 class="font-bold text-sm text-marun">{{ $story->title }}</h4>
                                    <p class="text-xs text-marun/80 mt-1 leading-relaxed">{{ $story->description }}</p>
                                </div>
                            </div>
                            <div class="mx-auto w-0 h-8 dot"></div>
                            <div class="text-center text-marun beat flex justify-center">
                                <iconify-icon icon="solar:heart-bold"></iconify-icon>
                            </div>
                        @endforeach
                    @else
                        <!-- Fallback Sample Stories -->
                        <div class="mx-auto w-0 h-8 dot"></div>
                        <div class="bg-rose/30 border border-rose rounded-3xl shadow-md overflow-hidden text-center fade-l">
                            <div class="p-5">
                                <span class="inline-block px-3 py-0.5 rounded-full bg-marun text-white text-[10px] font-bold tracking-wider mb-1">Awal Pertemuan</span>
                                <h4 class="font-bold text-sm text-marun">Awal Bersama</h4>
                                <p class="text-xs text-marun/80 mt-1 leading-relaxed">
                                    Langkah sederhana yang dipilih bersama, tumbuh keyakinan untuk saling menjaga dan melangkah ke masa depan. Hari ini menjadi awal baru yang dirayakan dengan hati penuh syukur.
                                </p>
                            </div>
                        </div>
                        <div class="mx-auto w-0 h-8 dot"></div>
                        <div class="text-center text-marun beat flex justify-center">
                            <iconify-icon icon="solar:heart-bold"></iconify-icon>
                        </div>

                        <div class="mx-auto w-0 h-8 dot"></div>
                        <div class="bg-rose/30 border border-rose rounded-3xl shadow-md overflow-hidden text-center fade-r">
                            <div class="p-5">
                                <span class="inline-block px-3 py-0.5 rounded-full bg-marun text-white text-[10px] font-bold tracking-wider mb-1">Komitmen</span>
                                <h4 class="font-bold text-sm text-marun">Langkah Bersama</h4>
                                <p class="text-xs text-marun/80 mt-1 leading-relaxed">
                                    Dua hati yang saling menemukan, perjalanan ini tumbuh menjadi komitmen yang kuat dan penuh harapan. Kini, dengan syukur dan cinta, kami melangkah menuju babak baru.
                                </p>
                            </div>
                        </div>
                        <div class="mx-auto w-0 h-8 dot"></div>
                        <div class="text-center text-marun beat flex justify-center">
                            <iconify-icon icon="solar:heart-bold"></iconify-icon>
                        </div>

                        <div class="mx-auto w-0 h-8 dot"></div>
                        <div class="bg-rose/30 border border-rose rounded-3xl shadow-md overflow-hidden text-center fade-l">
                            <div class="p-5">
                                <span class="inline-block px-3 py-0.5 rounded-full bg-marun text-white text-[10px] font-bold tracking-wider mb-1">Pernikahan</span>
                                <h4 class="font-bold text-sm text-marun">Janji Suci</h4>
                                <p class="text-xs text-marun/80 mt-1 leading-relaxed">
                                    Kami memutuskan untuk menjalani hidup bersama dalam ikatan suci pernikahan, dengan doa dan restu dari keluarga serta sahabat tercinta.
                                </p>
                            </div>
                        </div>
                        <div class="mx-auto w-0 h-8 dot"></div>
                        <div class="text-center text-marun beat flex justify-center">
                            <iconify-icon icon="solar:heart-bold"></iconify-icon>
                        </div>
                    @endif
                </div>
            </section>

            <!-- 8. GALERI SECTION -->
            <section id="galeri" class="bg-rose px-5 py-14 text-center">
                <h2 class="h2 reveal text-marun">Galeri Bahagia</h2>
                <p class="text-xs text-marun/80 mt-1 reveal">Momen indah yang telah kami abadikan bersama</p>

                <div id="gal" class="grid grid-cols-2 gap-2 mt-6">
                    @if($galleryPhotos && $galleryPhotos->count() > 0)
                        @foreach($galleryPhotos as $idx => $photo)
                            <div class="rounded-xl overflow-hidden ph aspect-[3/4] zoom cursor-pointer relative group {{ $idx == 0 ? 'col-span-2 !aspect-video' : '' }}" onclick="openLightbox('{{ asset('storage/' . $photo->file_path) }}')">
                                <img src="{{ asset('storage/' . $photo->file_path) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="{{ $photo->caption ?: 'Galeri ' . ($idx + 1) }}">
                                <div class="absolute inset-0 bg-marun/30 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white">
                                    <iconify-icon icon="solar:magnifer-zoom-in-bold-duotone" class="text-3xl"></iconify-icon>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <!-- Sample Gallery -->
                        <div class="rounded-xl overflow-hidden ph aspect-[3/4] zoom col-span-2 !aspect-video cursor-pointer relative group" onclick="openLightbox('https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=1200&q=80')">
                            <img src="https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=1200&q=80" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="Galeri 1">
                        </div>
                        <div class="rounded-xl overflow-hidden ph aspect-[3/4] zoom cursor-pointer relative group" onclick="openLightbox('https://images.unsplash.com/photo-1583939003579-730e3918a45a?auto=format&fit=crop&w=800&q=80')">
                            <img src="https://images.unsplash.com/photo-1583939003579-730e3918a45a?auto=format&fit=crop&w=800&q=80" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="Galeri 2">
                        </div>
                        <div class="rounded-xl overflow-hidden ph aspect-[3/4] zoom cursor-pointer relative group" onclick="openLightbox('https://images.unsplash.com/photo-1511285560929-80b456fea0bc?auto=format&fit=crop&w=800&q=80')">
                            <img src="https://images.unsplash.com/photo-1511285560929-80b456fea0bc?auto=format&fit=crop&w=800&q=80" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="Galeri 3">
                        </div>
                    @endif
                </div>
            </section>

            <!-- 9. RSVP SECTION -->
            <section id="rsvp" class="bg-white px-6 py-12 text-center">
                <h2 class="h2 reveal text-marun">RSVP</h2>
                <p class="text-xs text-marun/80 mt-1 reveal">{{ $personalization['rsvp_hint'] }}</p>
                <button onclick="rsvpModal.classList.remove('hidden')" class="btn w-full flex-col !py-4 mt-5 reveal shadow-lg">
                    <iconify-icon icon="solar:user-check-bold-duotone" class="text-3xl mb-1"></iconify-icon>
                    <span>{{ $personalization['cta'] }}</span>
                </button>
            </section>

            <!-- 10. KADO SECTION -->
            @if($hasBankAccounts || !empty($invitation->gift_address))
                <section id="kado" class="bg-white px-6 pb-14 text-center">
                    <h2 class="h2 reveal text-marun">Tanda Kasih</h2>
                    <p class="text-xs text-marun/80 mt-2 reveal max-w-xs mx-auto">
                        Doa restu Anda adalah karunia yang sangat berarti bagi kedua mempelai. Namun jika memberi adalah ungkapan tanda kasih Anda, dapat menggunakan fitur berikut:
                    </p>

                    <!-- Rekening Bank -->
                    @if($invitation->bankAccounts && $invitation->bankAccounts->count() > 0)
                        @foreach($invitation->bankAccounts as $acc)
                            <div class="mt-5 bg-rose rounded-2xl p-5 reveal text-center border border-marun/10">
                                <div class="flex items-center justify-center gap-2 mb-2">
                                    <img src="{{ \App\Support\BankLogo::assetUrl($acc->bank_name) }}" alt="{{ $acc->bank_name }}" class="h-6 w-auto bg-white px-2 py-0.5 rounded object-contain shadow-xs">
                                    <p class="font-bold text-lg text-marun">{{ $acc->bank_name }}</p>
                                </div>
                                <p class="text-xs text-marun/70 mt-1">Nama Pemilik Rekening</p>
                                <p class="font-bold text-sm text-marun">{{ $acc->account_name }}</p>
                                <p class="text-xs text-marun/70 mt-2">Nomor Rekening</p>
                                <div class="flex items-center justify-center gap-2 mt-0.5">
                                    <span class="font-mono font-bold text-lg text-marun">{{ $acc->account_number }}</span>
                                    <button onclick="copyText('{{ $acc->account_number }}', 'Nomor rekening disalin!')" class="p-1 rounded-full bg-marun text-white hover:bg-marun2 transition flex items-center justify-center" title="Salin">
                                        <iconify-icon icon="solar:copy-bold" class="text-sm"></iconify-icon>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    @elseif(!empty($invitation->bank_name) && !empty($invitation->bank_account_number))
                        <div class="mt-5 bg-rose rounded-2xl p-5 reveal text-center border border-marun/10">
                            <div class="flex items-center justify-center gap-2 mb-2">
                                <img src="{{ \App\Support\BankLogo::assetUrl($invitation->bank_name) }}" alt="{{ $invitation->bank_name }}" class="h-6 w-auto bg-white px-2 py-0.5 rounded object-contain shadow-xs">
                                <p class="font-bold text-lg text-marun">{{ $invitation->bank_name }}</p>
                            </div>
                            <p class="text-xs text-marun/70 mt-1">Nama Pemilik Rekening</p>
                            <p class="font-bold text-sm text-marun">{{ $invitation->bank_account_name ?: $coupleName }}</p>
                            <p class="text-xs text-marun/70 mt-2">Nomor Rekening</p>
                            <div class="flex items-center justify-center gap-2 mt-0.5">
                                <span class="font-mono font-bold text-lg text-marun">{{ $invitation->bank_account_number }}</span>
                                <button onclick="copyText('{{ $invitation->bank_account_number }}', 'Nomor rekening disalin!')" class="p-1 rounded-full bg-marun text-white hover:bg-marun2 transition flex items-center justify-center" title="Salin">
                                    <iconify-icon icon="solar:copy-bold" class="text-sm"></iconify-icon>
                                </button>
                            </div>
                        </div>
                    @endif

                    <!-- Kirim Hadiah Fisik -->
                    @if(!empty($invitation->gift_address))
                        <div class="mt-4 bg-rose rounded-2xl p-5 reveal text-center border border-marun/10">
                            <div class="flex items-center justify-center gap-1.5 text-marun font-bold mb-1">
                                <iconify-icon icon="solar:box-bold-duotone" class="text-xl"></iconify-icon>
                                <span>Kirim Kado Fisik</span>
                            </div>
                            <p class="text-xs text-marun/80 mt-1 leading-relaxed">{{ $invitation->gift_address }}</p>
                            <button onclick="copyText('{{ addslashes($invitation->gift_address) }}', 'Alamat kado disalin!')" class="btn-rose mt-3 !py-1.5 !px-3 !text-xs !bg-white">
                                <iconify-icon icon="solar:copy-bold" class="text-xs"></iconify-icon>
                                <span>Salin Alamat</span>
                            </button>
                        </div>
                    @endif
                </section>
            @endif

            <!-- 11. UCAPAN & DOA -->
            <section id="ucapan" class="bg-rose px-6 py-14">
                <h2 class="h2 text-center reveal text-marun">Ucapan &amp; Doa</h2>
                <p class="text-xs text-center text-marun/80 mt-1 reveal">Tinggalkan doa restu hangat untuk kami berdua</p>

                <form id="wishForm" action="{{ route('invitation.wish', $invitation->slug) }}" method="POST" class="mt-5 space-y-3 reveal">
                    @csrf
                    <input type="text" name="name" id="wNama" value="{{ $guestName !== 'Tamu Undangan' ? $guestName : '' }}" placeholder="Nama Anda" required class="w-full rounded-lg px-3.5 py-2.5 text-xs outline-none bg-white border border-marun/20 text-marun placeholder-marun/50 focus:border-marun">
                    <textarea name="message" id="wPesan" rows="4" placeholder="Tuliskan ucapan dan doa restu Anda di sini..." required class="w-full rounded-lg px-3.5 py-2.5 text-xs outline-none bg-white border border-marun/20 text-marun placeholder-marun/50 focus:border-marun"></textarea>
                    <button type="submit" id="wSubmitBtn" class="btn w-full !py-2.5 !text-xs shadow-md">
                        <iconify-icon icon="solar:chat-round-dots-bold-duotone" class="text-lg"></iconify-icon>
                        <span>Kirim Ucapan</span>
                    </button>
                </form>

                <div id="wList" class="mt-6 space-y-2.5 max-h-72 overflow-y-auto pr-1">
                    @if($invitation->wishes && $invitation->wishes->count() > 0)
                        @foreach($invitation->wishes as $wish)
                            <div class="bg-white rounded-lg p-3 text-xs shadow-xs border border-marun/10 text-marun">
                                <div class="flex items-center justify-between">
                                    <b class="text-marun font-bold">{{ $wish->name }}</b>
                                    <span class="opacity-60 text-[10px]">{{ $wish->created_at ? $wish->created_at->diffForHumans() : 'Baru saja' }}</span>
                                </div>
                                <p class="mt-1 text-marun/90 leading-relaxed">{{ $wish->message }}</p>
                            </div>
                        @endforeach
                    @else
                        <p id="emptyWishMsg" class="text-xs text-center opacity-60 text-marun py-3">Belum ada ucapan. Jadilah yang pertama memberikan doa restu!</p>
                    @endif
                </div>
            </section>

            <!-- 12. INSTAGRAM STORY -->
            <section class="bg-marun text-white px-6 py-12 text-center">
                <h2 class="h2 reveal">Instagram Stories</h2>
                <p class="text-xs mt-2 reveal text-rose/90 max-w-xs mx-auto">
                    Bagikan momen bahagia ini ke Instagram Story kamu. Download template IG Story di bawah ini dan posting di akun kamu!
                </p>
                <a href="{{ route('invitation.ig-story.download', $invitation->slug) }}" class="mt-5 flex items-center gap-4 bg-rose text-marun rounded-2xl p-4 text-left zoom shadow-lg hover:scale-102 transition">
                    <iconify-icon icon="solar:camera-square-bold-duotone" class="text-4xl text-marun"></iconify-icon>
                    <div>
                        <small class="block text-xs uppercase tracking-wider font-semibold opacity-70">Download</small>
                        <span class="text-base font-bold">Template IG Story</span>
                    </div>
                </a>
            </section>

            <!-- 13. PROTOKOL KESEHATAN -->
            <section class="bg-white px-6 py-14 text-center">
                <h2 class="h2 reveal text-marun">Protokol Kesehatan</h2>
                <p class="text-xs mt-2 reveal text-marun/80">Acara ini diselenggarakan dengan mematuhi protokol kesehatan demi keselamatan bersama.</p>
                <div class="grid grid-cols-2 gap-5 mt-6 text-xs text-marun">
                    <div class="zoom p-3 rounded-2xl bg-rose/40 border border-rose">
                        <div class="w-12 h-12 mx-auto rounded-full bg-marun text-white flex items-center justify-center text-2xl shadow-sm">
                            <iconify-icon icon="solar:mask-bold-duotone"></iconify-icon>
                        </div>
                        <p class="mt-2 font-semibold">Gunakan Masker</p>
                    </div>
                    <div class="zoom p-3 rounded-2xl bg-rose/40 border border-rose">
                        <div class="w-12 h-12 mx-auto rounded-full bg-marun text-white flex items-center justify-center text-2xl shadow-sm">
                            <iconify-icon icon="solar:hand-wash-bold-duotone"></iconify-icon>
                        </div>
                        <p class="mt-2 font-semibold">Cuci Tangan</p>
                    </div>
                    <div class="zoom p-3 rounded-2xl bg-rose/40 border border-rose">
                        <div class="w-12 h-12 mx-auto rounded-full bg-marun text-white flex items-center justify-center text-2xl shadow-sm">
                            <iconify-icon icon="solar:users-group-rounded-bold-duotone"></iconify-icon>
                        </div>
                        <p class="mt-2 font-semibold">Jaga Jarak</p>
                    </div>
                    <div class="zoom p-3 rounded-2xl bg-rose/40 border border-rose">
                        <div class="w-12 h-12 mx-auto rounded-full bg-marun text-white flex items-center justify-center text-2xl shadow-sm">
                            <iconify-icon icon="solar:thermometer-bold-duotone"></iconify-icon>
                        </div>
                        <p class="mt-2 font-semibold">Cek Suhu Tubuh</p>
                    </div>
                </div>
            </section>

            <!-- 14. PENUTUP -->
            <section class="bg-white px-6 pb-32 pt-4 text-center">
                <p class="text-xs reveal text-marun/80 leading-relaxed max-w-sm mx-auto">{{ $closingText }}</p>
                <p class="text-xs mt-6 uppercase tracking-wider text-marun font-semibold">Hormat Kami</p>
                <p class="font-bold text-xl text-marun mt-1">{{ $coupleName }}</p>
                
                <svg class="w-36 mx-auto mt-6 opacity-60" viewBox="0 0 200 60" fill="none" stroke="#6b0000" stroke-width="1.2">
                    <path d="M10 30 C50 10, 80 50, 100 30 S150 10, 190 30"/>
                    <circle cx="100" cy="30" r="4" fill="#6b0000"/>
                </svg>

                <p class="text-[10px] mt-8 opacity-60 text-marun">{{ $footerText }}</p>
            </section>
        </div>

        <!-- ============ 15. BOTTOM NAVBAR ============ -->
        <nav id="nav" class="absolute bottom-4 left-1/2 -translate-x-1/2 z-30 bg-white/95 backdrop-blur-md rounded-full shadow-2xl px-2 py-1.5 flex gap-1 translate-y-24 transition-transform duration-700 text-marun border border-rose">
            <a data-t="home" class="nv" title="Beranda"><iconify-icon icon="solar:home-smile-bold-duotone"></iconify-icon></a>
            <a data-t="mempelai" class="nv" title="Mempelai"><iconify-icon icon="solar:heart-bold-duotone"></iconify-icon></a>
            <a data-t="acara" class="nv" title="Acara"><iconify-icon icon="solar:calendar-bold-duotone"></iconify-icon></a>
            <a data-t="galeri" class="nv" title="Galeri"><iconify-icon icon="solar:gallery-bold-duotone"></iconify-icon></a>
            <a data-t="kado" class="nv" title="Kado"><iconify-icon icon="solar:gift-bold-duotone"></iconify-icon></a>
            <a data-t="ucapan" class="nv" title="Ucapan"><iconify-icon icon="solar:chat-round-dots-bold-duotone"></iconify-icon></a>
        </nav>

        <!-- ============ 16. TAB QR CODE FLOATING ============ -->
        <button onclick="document.getElementById('qrModal').classList.remove('hidden')" class="absolute right-0 top-[60%] z-30 bg-marun text-rose text-[10px] font-bold rounded-l-lg px-2 py-3 shadow-lg [writing-mode:vertical-rl] tracking-widest flex items-center gap-1.5 hover:bg-marun2 transition">
            <iconify-icon icon="solar:qr-code-bold-duotone" class="text-sm"></iconify-icon>
            <span>QR CODE</span>
        </button>

        <!-- ============ 17. MUSIK TOGGLE ============ -->
        <button id="musicBtn" class="absolute top-4 right-4 z-30 w-11 h-11 rounded-full bg-white/95 text-marun shadow-lg hidden flex items-center justify-center border border-rose hover:scale-105 transition" title="Musik">
            <iconify-icon id="musicIcon" icon="solar:music-note-slider-bold-duotone" class="text-xl"></iconify-icon>
        </button>
        <audio id="bgm" src="{{ $musicUrl ?: 'https://assets.mixkit.co/music/preview/mixkit-romantic-wedding-272.mp3' }}" loop preload="auto"></audio>
    </main>

    <!-- ============ MODAL QR CODE ============ -->
    <div id="qrModal" class="hidden fixed inset-0 z-[90] bg-black/75 backdrop-blur-xs flex items-center justify-center p-5" onclick="this.classList.add('hidden')">
        <div class="bg-white text-marun rounded-3xl p-6 text-center w-80 zoom show shadow-2xl border border-rose" onclick="event.stopPropagation()">
            <div class="flex justify-between items-center pb-2 border-b border-rose/50 mb-3">
                <p class="font-bold text-base text-marun">QR Check-in Tamu</p>
                <button onclick="document.getElementById('qrModal').classList.add('hidden')" class="text-marun/60 hover:text-marun">
                    <iconify-icon icon="solar:close-circle-bold" class="text-xl"></iconify-icon>
                </button>
            </div>
            <p class="text-xs text-marun/80">Tunjukkan QR ini saat tiba di lokasi acara</p>
            
            <div class="w-48 h-48 mx-auto my-3 bg-white p-2 rounded-xl border border-marun/20 shadow-inner flex items-center justify-center">
                @php
                    $qrData = !empty($guest?->token) ? $guestInvitationUrl : ($invitation->slug . '-' . urlencode($guestName));
                    $qrApiUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=' . urlencode($qrData);
                @endphp
                <img src="{{ $qrApiUrl }}" class="w-full h-full object-contain" alt="QR Tamu">
            </div>

            <p class="font-bold text-sm text-marun">{{ $guestName }}</p>
            @if(!empty($guest?->category))
                <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full bg-rose text-marun text-[10px] font-semibold">
                    {{ ucfirst($guest->category) }}
                </span>
            @endif

            <button onclick="document.getElementById('qrModal').classList.add('hidden')" class="btn-rose mt-5 !w-full">
                Tutup
            </button>
        </div>
    </div>

    <!-- ============ MODAL RSVP ============ -->
    <div id="rsvpModal" class="hidden fixed inset-0 z-[90] bg-black/65 backdrop-blur-xs flex items-center justify-center p-5" onclick="this.classList.add('hidden')">
        <div class="bg-white text-marun rounded-3xl p-6 w-full max-w-sm text-left zoom show shadow-2xl border border-rose" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between pb-2 border-b border-rose mb-3">
                <h3 class="h2 !text-xl text-marun">Konfirmasi Kehadiran</h3>
                <button onclick="document.getElementById('rsvpModal').classList.add('hidden')" class="text-marun/60 hover:text-marun">
                    <iconify-icon icon="solar:close-circle-bold" class="text-xl"></iconify-icon>
                </button>
            </div>
            
            <form id="rsvpForm" action="{{ route('invitation.rsvp', $invitation->slug) }}" method="POST" class="space-y-3">
                @csrf
                @if(!empty($guest?->id))
                    <input type="hidden" name="guest_id" value="{{ $guest->id }}">
                @endif

                <div>
                    <label class="block text-xs font-semibold text-marun mb-1">Nama Tamu</label>
                    <input type="text" name="name" id="rsvpName" value="{{ $guestName !== 'Tamu Undangan' ? $guestName : '' }}" placeholder="Nama Lengkap" required class="w-full bg-rose/40 border border-marun/20 rounded-lg px-3 py-2 text-xs outline-none focus:border-marun text-marun">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-marun mb-1">Nomor WhatsApp / HP</label>
                    <input type="text" name="phone" id="rsvpPhone" value="{{ $guest->phone ?? '' }}" placeholder="08xxxxxxxxxx" class="w-full bg-rose/40 border border-marun/20 rounded-lg px-3 py-2 text-xs outline-none focus:border-marun text-marun">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-marun mb-1">Status Kehadiran</label>
                    <select name="status" id="rsvpStatus" class="w-full bg-rose/40 border border-marun/20 rounded-lg px-3 py-2 text-xs outline-none focus:border-marun text-marun">
                        <option value="attending">Saya Akan Hadir</option>
                        <option value="not_attending">Maaf, Tidak Bisa Hadir</option>
                        <option value="maybe">Belum Pasti (Masih Ragu)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-marun mb-1">Jumlah Tamu (Pax)</label>
                    <select name="pax" id="rsvpPax" class="w-full bg-rose/40 border border-marun/20 rounded-lg px-3 py-2 text-xs outline-none focus:border-marun text-marun">
                        <option value="1">1 Orang</option>
                        <option value="2">2 Orang</option>
                        <option value="3">3 Orang</option>
                        <option value="4">4 Orang</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-marun mb-1">Pesan / Catatan (Opsional)</label>
                    <textarea name="message" id="rsvpMessage" rows="2" placeholder="Catatan untuk mempelai..." class="w-full bg-rose/40 border border-marun/20 rounded-lg px-3 py-2 text-xs outline-none focus:border-marun text-marun"></textarea>
                </div>

                <button type="submit" id="rsvpSubmitBtn" class="btn w-full mt-3 !py-2.5 !text-xs shadow-md">
                    <iconify-icon icon="solar:check-circle-bold" class="text-base"></iconify-icon>
                    <span>Kirim Konfirmasi</span>
                </button>
            </form>
        </div>
    </div>

    <!-- ============ LIGHTBOX MODAL ============ -->
    <div id="lightboxModal" class="hidden fixed inset-0 z-[100] bg-black/90 flex items-center justify-center p-4" onclick="this.classList.add('hidden')">
        <button class="absolute top-4 right-4 text-white text-3xl hover:opacity-80">
            <iconify-icon icon="solar:close-circle-bold"></iconify-icon>
        </button>
        <img id="lightboxImg" src="" class="max-w-full max-h-[90vh] object-contain rounded-xl shadow-2xl" onclick="event.stopPropagation()">
    </div>

    <script>
        const $ = s => document.querySelector(s);
        const panel = $('#panel');

        // Parameter tamu di URL fallback
        const urlParams = new URLSearchParams(location.search);
        const toParam = urlParams.get('to');
        if (toParam && $('#guest').textContent === 'Tamu Undangan') {
            $('#guest').textContent = toParam;
            if ($('#wNama')) $('#wNama').value = toParam;
            if ($('#rsvpName')) $('#rsvpName').value = toParam;
        }

        // Loader
        window.addEventListener('load', () => {
            setTimeout(() => {
                $('#loader').style.opacity = '0';
                setTimeout(() => $('#loader').remove(), 700);
            }, 800);
        });

        // Toast Helper
        function showToast(msg) {
            const toast = $('#toast');
            $('#toast-message').textContent = msg;
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 3500);
        }

        // Salin Teks Helper
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

        // Lightbox
        function openLightbox(src) {
            $('#lightboxImg').src = src;
            $('#lightboxModal').classList.remove('hidden');
        }

        // Hitung Mundur Otomatis
        const targetDate = new Date('{{ $targetIso }}');
        function updateCountdown() {
            const now = new Date();
            const s = Math.max(0, Math.floor((targetDate - now) / 1000));
            const v = [
                Math.floor(s / 86400),
                Math.floor((s % 86400) / 3600),
                Math.floor((s % 3600) / 60),
                s % 60
            ];
            const l = ['Hari', 'Jam', 'Menit', 'Detik'];
            $('#cd').innerHTML = v.map((n, i) => `
                <div>
                    <div class="w-13 h-13 rounded-2xl bg-marun/90 border border-white/20 text-white flex items-center justify-center text-base font-bold shadow-md">
                        ${String(n).padStart(2, '0')}
                    </div>
                    <p class="text-[10px] mt-1 text-rose font-medium uppercase tracking-wider">${l[i]}</p>
                </div>
            `).join('');
        }
        updateCountdown();
        setInterval(updateCountdown, 1000);

        // Buka Undangan & Play Musik
        let playing = false;
        const bgm = $('#bgm');
        $('#openBtn').onclick = () => {
            $('#cover').classList.add('hide');
            document.body.classList.remove('overflow-hidden');
            $('#nav').classList.remove('translate-y-24');
            $('#musicBtn').classList.remove('hidden');
            bgm.play().then(() => {
                playing = true;
                $('#musicIcon').setAttribute('icon', 'solar:music-note-slider-bold-duotone');
            }).catch(() => {});
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

        // Animasi Scroll (IntersectionObserver pada scrollable #panel)
        const io = new IntersectionObserver(entries => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    e.target.classList.add('show');
                }
            });
        }, { root: panel, threshold: 0.12 });

        document.querySelectorAll('.reveal, .fade-l, .fade-r, .zoom').forEach(el => io.observe(el));

        // Navigasi Bawah & Scrollspy
        document.querySelectorAll('.nv').forEach(a => {
            a.onclick = () => {
                const target = document.getElementById(a.dataset.t);
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            };
        });

        const navIds = ['home', 'mempelai', 'acara', 'galeri', 'kado', 'ucapan'];
        panel.addEventListener('scroll', () => {
            let activeIdx = 0;
            navIds.forEach((id, i) => {
                const el = document.getElementById(id);
                if (el && (el.offsetTop - panel.clientHeight / 2 <= panel.scrollTop)) {
                    activeIdx = i;
                }
            });
            document.querySelectorAll('.nv').forEach((btn, i) => btn.classList.toggle('on', i === activeIdx));
        });

        // AJAX RSVP Form
        $('#rsvpForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const btn = $('#rsvpSubmitBtn');
            btn.disabled = true;
            btn.innerHTML = `<iconify-icon icon="solar:spinner-bold" class="text-base animate-spin"></iconify-icon><span>Menyimpan...</span>`;

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
                $('#rsvpModal').classList.add('hidden');
                showToast(data.message || 'Konfirmasi kehadiran berhasil disimpan!');
            })
            .catch(() => {
                showToast('Terima kasih, konfirmasi Anda berhasil dikirim.');
                $('#rsvpModal').classList.add('hidden');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = `<iconify-icon icon="solar:check-circle-bold" class="text-base"></iconify-icon><span>Kirim Konfirmasi</span>`;
            });
        });

        // AJAX Ucapan Form
        $('#wishForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const btn = $('#wSubmitBtn');
            const nama = $('#wNama').value.trim();
            const pesan = $('#wPesan').value.trim();
            if (!nama || !pesan) return;

            btn.disabled = true;
            btn.innerHTML = `<iconify-icon icon="solar:spinner-bold" class="text-base animate-spin"></iconify-icon><span>Mengirim...</span>`;

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
                
                // Tambahkan langsung ke daftar ucapan
                const emptyMsg = $('#emptyWishMsg');
                if (emptyMsg) emptyMsg.remove();

                const newCard = document.createElement('div');
                newCard.className = 'bg-white rounded-lg p-3 text-xs shadow-xs border border-marun/10 text-marun animate-fadeIn';
                newCard.innerHTML = `
                    <div class="flex items-center justify-between">
                        <b class="text-marun font-bold">${nama.replace(/[&<>'"]/g, '')}</b>
                        <span class="opacity-60 text-[10px]">Baru saja</span>
                    </div>
                    <p class="mt-1 text-marun/90 leading-relaxed">${pesan.replace(/[&<>'"]/g, '')}</p>
                `;
                $('#wList').prepend(newCard);
                $('#wPesan').value = '';
            })
            .catch(() => {
                showToast('Ucapan Anda berhasil dikirim!');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = `<iconify-icon icon="solar:chat-round-dots-bold-duotone" class="text-lg"></iconify-icon><span>Kirim Ucapan</span>`;
            });
        });

        // Kelopak Burgundy Melayang
        for (let i = 0; i < 14; i++) {
            const p = document.createElement('i');
            p.className = 'petal';
            p.style.left = Math.random() * 100 + 'vw';
            p.style.animationDuration = 8 + Math.random() * 8 + 's';
            p.style.animationDelay = Math.random() * 7 + 's';
            document.body.appendChild(p);
        }
    </script>
</body>
</html>
