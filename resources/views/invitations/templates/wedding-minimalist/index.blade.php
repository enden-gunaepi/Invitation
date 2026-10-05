<!DOCTYPE html>
<html lang="id">
@php
    $guest = $guest ?? null;
    $guestName = $guest->name ?? 'Tamu Undangan';

    $coverPhoto = isset($invitation->cover_photo) && $invitation->cover_photo
        ? asset('storage/' . $invitation->cover_photo) 
        : asset('assets_invitation/model/fotomodel1.png');
    $bridePhoto = isset($invitation->bride_photo) && $invitation->bride_photo
        ? asset('storage/' . $invitation->bride_photo) 
        : asset('assets_invitation/model/fotomodel2.png');
    $groomPhoto = isset($invitation->groom_photo) && $invitation->groom_photo
        ? asset('storage/' . $invitation->groom_photo) 
        : asset('assets_invitation/model/fotomodel3.png');
    
    $brideName = $invitation->bride_name ?? 'Mempelai Wanita';
    $groomName = $invitation->groom_name ?? 'Mempelai Pria';
    
    $brideFirstNames = trim(($invitation->bride_name ?? 'Mempelai Wanita') . ' & ' . ($invitation->groom_name ?? 'Mempelai Pria'), ' &');
    
    $eventDate = $invitation->event_date ? \Illuminate\Support\Carbon::parse($invitation->event_date) : null;
    $eventDateStr = $eventDate ? $eventDate->format('d.m.Y') : '12.12.2025';
    
    $venueName = $invitation->venue_name ?? 'Gedung Pernikahan';
    $venueAddress = $invitation->venue_address ?? 'Jl. Contoh Alamat Pernikahan No. 123, Kota, Provinsi';
    
    $brideParentName = $invitation->bride_parent_name ?? 'Putri dari Bpk. Fulan & Ibu Fulanah';
    $groomParentName = $invitation->groom_parent_name ?? 'Putra dari Bpk. Fulan & Ibu Fulanah';
    
    $brideInstagram = $invitation->bride_instagram ?? '#';
    $groomInstagram = $invitation->groom_instagram ?? '#';
    
    $musicUrl = $invitation->music_url ? ($invitation->music_signed_url ?? asset('storage/' . $invitation->music_url)) : null;

    $invitationEvents = isset($invitation->invitationEvents) && $invitation->invitationEvents->count() > 0 
        ? $invitation->invitationEvents 
        : collect([
            (object)[
                'name' => 'Resepsi Pernikahan',
                'date' => $invitation->event_date ?? '2025-12-12',
                'time' => $invitation->event_time ?? '09:00',
                'venue_name' => $venueName,
                'venue_address' => $venueAddress,
                'google_maps_url' => 'https://maps.google.com'
            ]
        ]);

    $photos = isset($invitation->photos) && $invitation->photos->count() > 0 
        ? $invitation->photos->map(fn($p) => asset('storage/' . $p->file_path)) 
        : collect([
            asset('assets_invitation/model/fotomodel4.png'),
            asset('assets_invitation/model/fotomodel5.png'),
            asset('assets_invitation/model/fotomodel6.png'),
            asset('assets_invitation/model/fotomodel7.png')
        ]);
        
    $bankAccounts = isset($invitation->bankAccounts) && $invitation->bankAccounts->count() > 0 
        ? $invitation->bankAccounts 
        : collect([
            (object)[
                'bank_name' => 'Bank BCA',
                'account_number' => '1234567890',
                'account_name' => 'Nama Mempelai'
            ]
        ]);
    
    $guestsList = isset($invitation->guests) && $invitation->guests->whereNotNull('message')->count() > 0 
        ? $invitation->guests()->whereNotNull('message')->latest()->get() 
        : collect([
            (object)[
                'name' => 'Tamu Contoh',
                'status' => 'Hadir',
                'created_at' => now(),
                'message' => 'Selamat menempuh hidup baru!'
            ]
        ]);
    
    $googleMapsUrl = $invitation->google_maps_url ?? 'https://maps.google.com';
    $eventTime = $invitation->event_time ?? '09:00';
    $invitationTitle = $invitation->title ?? 'Pernikahan Mempelai';

    $igStoryDate = $eventDate ? $eventDate->format('d . m . Y') : '12 . 12 . 2025';
    $igBrideName = $brideName;
    $igGroomName = $groomName;
    $igCoupleName = trim($igBrideName . ' & ' . $igGroomName, ' &');
    $igStoryPhoto = isset($invitation->ig_story_photo) && $invitation->ig_story_photo 
        ? asset('storage/' . $invitation->ig_story_photo)
        : $coverPhoto;

    $loveStoryItems = (isset($invitation->loveStories) && $invitation->loveStories->count() > 0)
        ? $invitation->loveStories->values()->map(function ($story) use ($coverPhoto) {
            return (object) [
                'year'        => $story->year,
                'title'       => $story->title,
                'description' => $story->description,
                'background'  => $story->photo_path ? asset('storage/' . $story->photo_path) : $coverPhoto,
            ];
          })
        : collect([
            (object) [
                'year'        => '2020',
                'title'       => 'Pertemuan Pertama',
                'description' => 'Sebuah pertemuan sederhana yang mengubah segalanya. Di sinilah kisah cinta kami dimulai.',
                'background'  => $coverPhoto,
            ],
            (object) [
                'year'        => '2022',
                'title'       => 'Jatuh Cinta',
                'description' => 'Hari demi hari kami saling mengenal lebih dalam, dan perasaan itu tumbuh begitu indah.',
                'background'  => $coverPhoto,
            ],
            (object) [
                'year'        => '2025',
                'title'       => 'Lamaran',
                'description' => 'Dengan segenap hati, ia mengucap janji dan kami memutuskan untuk melangkah bersama.',
                'background'  => $coverPhoto,
            ],
          ]);
@endphp

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>The Wedding of {{ $brideFirstNames }}</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link
        href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600&family=Poppins:wght@300;400;500;600&display=swap"
        rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
        }

        .font-wedding {
            font-family: 'Cinzel', serif;
        }

        #cover-section {
            transition: opacity 0.8s ease;
        }

        .floating-control {
            position: fixed;
            right: 14px;
            top: 50%;
            z-index: 60;
            transform: translateY(-50%);
            display: flex;
            flex-direction: column;
            gap: 8px;
            padding: 8px 6px;
            border-radius: 9999px;
            background: rgba(46, 27, 27, 0.34);
            backdrop-filter: blur(10px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.18);
        }

        .floating-control button {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border: 0;
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.16);
            color: #fff;
            font-size: 14px;
            transition: background 0.2s ease, transform 0.2s ease;
        }

        .floating-control button:hover {
            background: rgba(255, 255, 255, 0.28);
            transform: scale(1.04);
        }

        .floating-control button.is-active {
            background: #7b0f0f;
        }

        /* ========== LOVE STORY SECTION ========== */
        .love-story-item {
            position: relative;
            overflow: hidden;
        }

        .love-story-item__bg {
            position: absolute;
            inset: 0;
            background-size: cover;
            background-position: center;
            transition: transform 0.8s ease;
        }

        .love-story-item:hover .love-story-item__bg {
            transform: scale(1.04);
        }

        .love-story-item__overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(
                to bottom,
                rgba(248, 245, 242, 0.05) 0%,
                rgba(248, 245, 242, 0.82) 50%,
                rgba(248, 245, 242, 1) 100%
            );
        }

        .love-story-connector {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0;
        }

        .love-story-connector__dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #7b0f0f;
            border: 2px solid #fbf8f6;
            box-shadow: 0 0 0 3px rgba(123, 15, 15, 0.2);
            flex-shrink: 0;
        }

        .love-story-connector__line {
            width: 1px;
            flex: 1;
            min-height: 60px;
            background: linear-gradient(to bottom, #7b0f0f, rgba(123, 15, 15, 0.15));
        }

        .love-story-connector__line.is-last {
            background: linear-gradient(to bottom, rgba(123, 15, 15, 0.15), transparent);
        }

        .love-story-year {
            font-family: 'Cinzel', serif;
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.2em;
            color: #7b0f0f;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .love-story-title {
            font-family: 'Cinzel', serif;
            font-size: 1.25rem;
            font-weight: 500;
            color: #2e1b1b;
            margin-bottom: 10px;
            line-height: 1.4;
        }

        .love-story-desc {
            font-size: 0.825rem;
            line-height: 1.8;
            color: #5e4d4d;
            font-weight: 300;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .love-story-reveal {
            opacity: 0;
            transition: opacity 0.7s ease, transform 0.7s ease;
            transform: translateY(20px);
        }

        .love-story-reveal.is-visible {
            opacity: 1;
            transform: translateY(0);
        }
        /* ======================================== */
    </style>
</head>

<body class="bg-[#f8f5f2]">
    <section id="cover-section" class="fixed inset-0 z-50 h-screen w-full overflow-hidden">
        <div class="grid h-full grid-cols-1 lg:grid-cols-2">
            <div class="relative hidden items-center justify-center bg-[#f8f5f2] px-16 lg:flex">
                <div class="absolute left-0 top-10 opacity-[0.03]">
                    <h1 class="font-wedding text-[120px] leading-none text-black">
                        {{ $brideName }}
                    </h1>
                    <h1 class="font-wedding mt-5 text-[120px] leading-none text-black">
                        {{ $groomName }}
                    </h1>
                </div>

                <div class="relative z-10 max-w-xl">
                    <p class="text-sm uppercase tracking-[0.4em] text-[#8a7a7a]">Wedding Invitation</p>

                    <h1 class="font-wedding mt-6 text-6xl leading-tight text-[#2e1b1b]">
                        {{ $brideName }}
                        <span class="mx-2 text-[#7c1111]">&</span>
                        {{ $groomName }}
                    </h1>

                    <div class="mt-10 h-[1px] w-32 bg-[#c8baba]"></div>

                    <p class="mt-10 text-base leading-8 text-[#5e4d4d]">
                        Dengan penuh rasa syukur dan bahagia, kami mengundang Anda untuk hadir dalam acara
                        pernikahan kami.
                    </p>

                    <div class="mt-10">
                        <p class="text-sm text-[#7a6a6a]">Lokasi Acara</p>
                        <h2 class="mt-2 text-2xl font-semibold text-[#2e1b1b]">{{ $venueName }}</h2>
                        <p class="mt-3 max-w-md text-sm leading-7 text-[#6f5f5f]">{{ $venueAddress }}</p>
                    </div>
                </div>
            </div>

            <div class="relative flex h-screen items-center justify-center overflow-hidden">
                <img src="{{ $coverPhoto }}" alt="Wedding Cover"
                    class="absolute inset-0 h-full w-full object-cover">
                <div class="absolute inset-0 bg-black/45"></div>
                <div class="absolute inset-0 bg-gradient-to-b from-black/20 via-black/10 to-black/50"></div>

                <div class="relative z-10 flex w-full items-center justify-center px-8">
                    <div class="w-full max-w-sm text-center text-white">
                        <p class="text-xs font-medium uppercase tracking-[0.3em]">The Wedding Of</p>

                        <h1 class="font-wedding mt-8 text-4xl leading-tight md:text-5xl">
                            {{ $brideName }}
                            <span class="block py-2 text-2xl">&</span>
                            {{ $groomName }}
                        </h1>

                        <p class="mt-8 text-xl font-semibold tracking-wide">
                            {{ $eventDateStr }}
                        </p>

                        <div class="mt-12">
                            <p class="text-sm text-white/80">Yth Bapak/Ibu/Saudara/i</p>
                            <h2 class="mt-3 text-2xl font-semibold">{{ $guestName ?? 'Tamu Undangan' }}</h2>

                            <button type="button" onclick="openInvitation()"
                                class="mt-8 inline-flex items-center gap-2 rounded-full bg-[#7b0f0f] px-8 py-3 text-sm font-medium text-white shadow-2xl transition duration-300 hover:scale-105 hover:bg-[#5f0b0b]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path d="M2.94 6.34A2 2 0 014.5 5h11a2 2 0 011.56.75l-7.06 4.7-7.06-4.11z" />
                                    <path
                                        d="M18 8.11l-7.43 4.95a1 1 0 01-1.14 0L2 8.76V14a2 2 0 002 2h12a2 2 0 002-2V8.11z" />
                                </svg>
                                Buka Undangan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="floating-control">
        <button type="button" id="music-toggle" aria-label="Toggle music" title="Musik">♪</button>
        <button type="button" id="scroll-toggle" aria-label="Toggle auto scroll" title="Auto scroll">↕</button>
    </div>

    @if ($musicUrl)
        <audio id="bgMusic" loop preload="auto" playsinline style="display:none">
            <source src="{{ $musicUrl }}" type="audio/mpeg">
        </audio>
    @endif

    <main class="relative z-0 min-h-screen bg-white">
        <section id="opening-section" class="relative min-h-screen w-full overflow-hidden bg-[#f8f5f2]">
            <div class="grid min-h-screen grid-cols-1 lg:grid-cols-2">
                <div class="relative hidden items-start justify-start bg-[#f8f5f2] px-16 py-20 lg:flex">
                    <div class="absolute left-12 top-16 opacity-[0.035]">
                        <h2 class="font-wedding text-4xl leading-[2.5] text-[#2e1b1b]">{{ $brideName }} &
                        </h2>
                        <h2 class="font-wedding text-4xl leading-[2.5] text-[#2e1b1b]">{{ $groomName }}
                        </h2>
                    </div>

                    <a href="javascript:history.back()"
                        class="relative z-10 inline-flex items-center gap-2 rounded-full bg-[#7b0f0f] px-4 py-2 text-sm font-semibold text-white shadow-md transition hover:bg-[#5f0b0b]">
                        <span class="flex h-5 w-5 items-center justify-center rounded-full bg-white text-[#7b0f0f]">
                            ‹
                        </span>
                        Demo
                    </a>
                </div>

                <div class="relative flex min-h-screen items-end justify-center overflow-hidden px-6 pb-28 pt-32">
                    <img src="{{ $coverPhoto }}"
                        alt="Opening Wedding {{ $brideFirstNames }}"
                        class="absolute inset-0 h-full w-full object-cover">

                    <div class="absolute inset-0 bg-gradient-to-t from-[#ffffff] via-[#ffffff]/60 to-transparent"></div>

                    <div class="relative z-10 w-full max-w-xl text-center text-[#2e1b1b]">
                        <p class="mb-10 text-xs font-semibold uppercase tracking-[0.3em] text-[#8a7a7a]">
                            Surat Ar-Rum Ayat 21
                        </p>

                        <p class="font-serif text-2xl leading-[2.2] md:text-3xl md:leading-[2.4]" dir="rtl">
                            وَمِنْ آيَاتِهِ أَنْ خَلَقَ لَكُمْ مِنْ أَنْفُسِكُمْ أَزْوَاجًا لِتَسْكُنُوا إِلَيْهَا
                            وَجَعَلَ بَيْنَكُمْ مَوَدَّةً وَرَحْمَةً ۚ إِنَّ فِي ذَٰلِكَ لَآيَاتٍ لِقَوْمٍ
                            يَتَفَكَّرُونَ
                        </p>

                        <p class="mt-10 px-2 text-sm italic leading-relaxed text-[#5e4d4d] md:text-base md:leading-8">
                            "Dan di antara tanda-tanda kebesaran-Nya ialah Dia menciptakan pasangan-pasangan untukmu
                            dari jenismu sendiri, agar kamu cenderung dan merasa tenteram kepadanya, dan Dia menjadikan
                            di antaramu rasa kasih dan sayang."
                        </p>

                        <p class="mt-8 text-sm font-bold md:text-base">QS. Ar-Rum: 21</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="couple-section" class="relative min-h-screen w-full overflow-hidden bg-[#f8f5f2]">
            <div class="grid min-h-screen grid-cols-1 lg:grid-cols-2">
                <div class="relative hidden items-center justify-center bg-[#f8f5f2] px-16 lg:flex">
                    <div class="absolute left-12 top-16 opacity-[0.035]">
                        <h2 class="font-wedding text-4xl leading-[2.5] text-[#2e1b1b]">{{ $brideName }} &
                        </h2>
                        <h2 class="font-wedding text-4xl leading-[2.5] text-[#2e1b1b]">{{ $groomName }}
                        </h2>
                    </div>
                </div>

                <div
                    class="relative flex min-h-screen items-center justify-center overflow-hidden bg-[#fbf8f6] px-6 py-16">
                    <div class="absolute inset-0 opacity-[0.08]">
                        <img src="{{ $coverPhoto }}" alt="Background Couple"
                            class="h-full w-full object-cover blur-sm">
                    </div>
                    <div class="absolute inset-0 bg-white/85"></div>

                    <div class="relative z-10 w-full max-w-md text-center text-[#2e1b1b]">
                        <p class="mb-12 text-sm leading-7 text-[#4d3c3c]">
                            Kami mohon doa & restunya atas pernikahan kami
                        </p>

                        <div class="flex flex-col items-center">
                            <div class="h-32 w-32 rounded-full border-[7px] border-[#7b0f0f] p-1 shadow-2xl">
                                <img src="{{ $bridePhoto }}"
                                    alt="{{ $brideName }}"
                                    class="h-full w-full rounded-full object-cover">
                            </div>

                            <h2 class="font-wedding mt-8 text-4xl leading-tight text-[#2e1b1b]">
                                {{ $brideName }}
                            </h2>

                            <p class="mt-3 text-sm leading-6 text-[#4d3c3c]">{{ $brideParentName }}</p>

                            @if ($invitation->bride_instagram)
                                <a href="{{ $brideInstagram }}" target="_blank"
                                    class="mt-4 inline-flex items-center gap-2 rounded-md bg-[#7b0f0f] px-4 py-2 text-xs font-semibold text-white shadow-md transition hover:bg-[#5f0b0b]">
                                    Instagram
                                </a>
                            @endif
                        </div>

                        <div class="my-12">
                            <span class="font-wedding text-6xl text-[#2e1b1b]">&</span>
                        </div>

                        <div class="flex flex-col items-center">
                            <div class="h-32 w-32 rounded-full border-[7px] border-[#7b0f0f] p-1 shadow-2xl">
                                <img src="{{ $groomPhoto }}"
                                    alt="{{ $groomName }}"
                                    class="h-full w-full rounded-full object-cover">
                            </div>

                            <h2 class="font-wedding mt-8 text-4xl leading-tight text-[#2e1b1b]">
                                {{ $groomName }}
                            </h2>

                            <p class="mt-3 text-sm leading-6 text-[#4d3c3c]">{{ $groomParentName }}</p>

                            @if ($invitation->groom_instagram)
                                <a href="{{ $groomInstagram }}" target="_blank"
                                    class="mt-4 inline-flex items-center gap-2 rounded-md bg-[#7b0f0f] px-4 py-2 text-xs font-semibold text-white shadow-md transition hover:bg-[#5f0b0b]">
                                    Instagram
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="event-section" class="relative min-h-screen w-full overflow-hidden bg-[#f8f5f2]">
            <div class="grid min-h-screen grid-cols-1 lg:grid-cols-2">
                {{-- Left side (desktop only) --}}
                <div class="relative hidden items-start justify-start bg-[#f8f5f2] px-16 py-20 lg:flex">
                    <div class="absolute left-12 top-16 opacity-[0.035]">
                        <h2 class="font-wedding text-4xl leading-[2.5] text-[#2e1b1b]">Our</h2>
                        <h2 class="font-wedding text-4xl leading-[2.5] text-[#2e1b1b]">Events</h2>
                    </div>
                </div>

                {{-- Right side (main content) --}}
                <div
                    class="relative flex min-h-screen items-center justify-center overflow-hidden bg-[#fbf8f6] px-6 py-16">
                    <div class="absolute inset-0 opacity-[0.08]">
                        {{-- Use a subtle background image or pattern, or the cover photo blurred --}}
                        <img src="{{ $coverPhoto }}" alt="Background Events"
                            class="h-full w-full object-cover blur-sm">
                    </div>
                    <div class="absolute inset-0 bg-white/85"></div>

                    <div class="relative z-10 w-full max-w-xl text-center text-[#2e1b1b]">
                        <p class="mb-12 text-sm leading-7 text-[#4d3c3c]">
                            Dengan hormat, kami mengundang Bapak/Ibu/Saudara/i untuk hadir dalam rangkaian acara kami.
                        </p>

                        {{-- Countdown Timer --}}
                        @if ($eventDateStr)
                            <div class="mb-12">
                                <h3 class="font-wedding text-3xl text-[#2e1b1b] mb-6">Menuju Hari Bahagia</h3>
                                <div id="countdown" class="flex justify-center gap-4 text-center">
                                    <div>
                                        <span id="days"
                                            class="block font-wedding text-4xl text-[#7b0f0f]">00</span>
                                        <span class="text-sm text-[#5e4d4d]">Hari</span>
                                    </div>
                                    <div>
                                        <span id="hours"
                                            class="block font-wedding text-4xl text-[#7b0f0f]">00</span>
                                        <span class="text-sm text-[#5e4d4d]">Jam</span>
                                    </div>
                                    <div>
                                        <span id="minutes"
                                            class="block font-wedding text-4xl text-[#7b0f0f]">00</span>
                                        <span class="text-sm text-[#5e4d4d]">Menit</span>
                                    </div>
                                    <div>
                                        <span id="seconds"
                                            class="block font-wedding text-4xl text-[#7b0f0f]">00</span>
                                        <span class="text-sm text-[#5e4d4d]">Detik</span>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Event List --}}
                        @forelse ($invitationEvents as $event)
                            <div class="mb-10 last:mb-0 p-6 border border-[#c8baba] rounded-lg shadow-sm bg-white">
                                <h3 class="font-wedding text-3xl text-[#2e1b1b] mb-4">{{ $event->name }}</h3>
                                <p class="text-lg font-semibold text-[#7b0f0f] mb-2">
                                    {{ \Carbon\Carbon::parse($event->date)->isoFormat('dddd, D MMMM YYYY') }}
                                </p>
                                <p class="text-base text-[#5e4d4d] mb-4">Pukul {{ $event->time }} WIB</p>
                                <p class="text-sm leading-6 text-[#4d3c3c]">{{ $event->venue_name }}</p>
                                <p class="text-sm leading-6 text-[#4d3c3c]">{{ $event->venue_address }}</p>

                                @if ($event->google_maps_url)
                                    <a href="{{ $event->google_maps_url }}" target="_blank"
                                        class="mt-6 inline-flex items-center gap-2 rounded-md bg-[#7b0f0f] px-4 py-2 text-xs font-semibold text-white shadow-md transition hover:bg-[#5f0b0b]">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        Lihat di Google Maps
                                    </a>
                                @endif
                            </div>
                        @empty
                            <p class="text-sm text-[#5e4d4d]">Detail acara akan segera diumumkan.</p>
                        @endforelse

                        {{-- Google Maps button for main event --}}
                        @if ($googleMapsUrl)
                            <div class="mt-12">
                                <a href="{{ $googleMapsUrl }}" target="_blank"
                                    class="inline-flex items-center gap-2 rounded-full bg-[#7b0f0f] px-6 py-3 text-sm font-medium text-white shadow-lg transition duration-300 hover:scale-105 hover:bg-[#5f0b0b]">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    Lihat Lokasi Utama
                                </a>
                            </div>
                        @endif

                        {{-- Save to Calendar (for main event date) --}}
                        @if ($eventDateStr && $eventTime)
                            <div class="{{ $googleMapsUrl ? 'mt-6' : 'mt-12' }}">
                                @php
                                    $calDate = $eventDate ? $eventDate->format('Ymd') : '20251212';
                                    $calTimeStr = $invitation->event_time ? \Carbon\Carbon::parse($invitation->event_time)->format('His') : '090000';
                                    $calTimeEndStr = $invitation->event_time ? \Carbon\Carbon::parse($invitation->event_time)->addHours(2)->format('His') : '110000';
                                @endphp
                                <a href="#"
                                    onclick="addToCalendar('{{ $invitationTitle }}', '{{ $venueName }}', '{{ $calDate }}T{{ $calTimeStr }}', '{{ $calDate }}T{{ $calTimeEndStr }}')"
                                    class="inline-flex items-center gap-2 rounded-full bg-[#2e1b1b] px-6 py-3 text-sm font-medium text-white shadow-lg transition duration-300 hover:scale-105 hover:bg-[#1b1010]">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Simpan ke Kalender
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        {{-- ======= LOVE STORY SECTION ======= --}}
        @if ($loveStoryItems->count() > 0)
        <section id="love-story-section" class="relative w-full overflow-hidden bg-[#f8f5f2]">
            <div class="grid grid-cols-1 lg:grid-cols-2 min-h-screen">

                {{-- LEFT: Dekoratif --}}
                <div class="relative hidden lg:flex items-start justify-start bg-[#f8f5f2] px-16 py-20">
                    <div class="absolute left-12 top-16 opacity-[0.035]">
                        <h2 class="font-wedding text-4xl leading-[2.5] text-[#2e1b1b]">Love</h2>
                        <h2 class="font-wedding text-4xl leading-[2.5] text-[#2e1b1b]">Story</h2>
                    </div>
                    {{-- Timeline vertikal dekoratif --}}
                    <div class="absolute left-1/2 top-24 bottom-24 -translate-x-1/2 flex flex-col items-center" style="width:1px;">
                        <div style="width:1px;flex:1;background:linear-gradient(to bottom, transparent, rgba(123,15,15,0.18), transparent);"></div>
                    </div>
                </div>

                {{-- RIGHT: Konten --}}
                <div class="relative flex flex-col justify-center px-6 py-16 lg:px-14">
                    <div class="w-full max-w-lg mx-auto">

                        {{-- Judul Section --}}
                        <div class="text-center mb-14 love-story-reveal">
                            <p class="text-[0.65rem] tracking-[0.3em] text-[#7b0f0f] uppercase font-semibold mb-3">Our Journey</p>
                            <h3 class="font-wedding text-3xl text-[#2e1b1b]">Love Story</h3>
                            <div class="mx-auto mt-4" style="width:40px;height:1px;background:#c8baba;"></div>
                        </div>

                        {{-- Timeline Items --}}
                        <div class="relative flex flex-col gap-0">

                            @foreach ($loveStoryItems as $story)
                            <div class="love-story-item flex gap-5 mb-2" style="min-height:220px;">

                                {{-- Konektor Timeline --}}
                                <div class="love-story-connector pt-1" style="width:24px;flex-shrink:0;">
                                    <div class="love-story-connector__dot"></div>
                                    @if (!$loop->last)
                                        <div class="love-story-connector__line"></div>
                                    @else
                                        <div class="love-story-connector__line is-last"></div>
                                    @endif
                                </div>

                                {{-- Card Konten --}}
                                <div class="love-story-reveal flex-1 relative rounded-xl overflow-hidden shadow-sm border border-[#e8dede] mb-8" style="min-height:180px;">
                                    {{-- Background foto --}}
                                    <div class="love-story-item__bg" style="background-image: url('{{ $story->background }}');"></div>
                                    <div class="love-story-item__overlay"></div>

                                    {{-- Teks --}}
                                    <div class="relative z-10 p-6 pt-8 flex flex-col justify-end h-full" style="min-height:180px;">
                                        @if ($story->year)
                                            <div class="love-story-year">{{ $story->year }}</div>
                                        @endif
                                        <h4 class="love-story-title">{{ $story->title ?: 'Our Journey' }}</h4>
                                        @if ($story->description)
                                            <p class="love-story-desc">{{ $story->description }}</p>
                                        @endif
                                    </div>
                                </div>

                            </div>
                            @endforeach

                        </div>{{-- .flex.flex-col --}}
                    </div>
                </div>
            </div>
        </section>
        @endif
        {{-- ===================================== --}}

        <section id="gallery-section" class="relative min-h-screen w-full overflow-hidden bg-[#f8f5f2]">
            <div class="grid min-h-screen grid-cols-1 lg:grid-cols-2">
                <div class="relative hidden items-start justify-start bg-[#f8f5f2] px-16 py-20 lg:flex">
                    <div class="absolute left-12 top-16 opacity-[0.035]">
                        <h2 class="font-wedding text-4xl leading-[2.5] text-[#2e1b1b]">Our</h2>
                        <h2 class="font-wedding text-4xl leading-[2.5] text-[#2e1b1b]">Gallery</h2>
                    </div>
                </div>

                <div class="relative flex min-h-screen items-center justify-center overflow-hidden bg-[#fbf8f6] px-6 py-16">
                    <div class="relative z-10 w-full max-w-xl text-center text-[#2e1b1b]">
                        <h3 class="font-wedding text-3xl text-[#2e1b1b] mb-12">Gallery</h3>
                        
                        <div class="grid grid-cols-2 gap-4">
                            @forelse ($photos as $photo)
                                <div class="overflow-hidden rounded-lg shadow-md aspect-square">
                                    <img src="{{ $photo }}" alt="Gallery Photo" class="h-full w-full object-cover transition-transform duration-500 hover:scale-110">
                                </div>
                            @empty
                                <p class="col-span-2 text-sm text-[#5e4d4d]">Belum ada foto galeri.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="gift-section" class="relative min-h-screen w-full overflow-hidden bg-[#f8f5f2]">
            <div class="grid min-h-screen grid-cols-1 lg:grid-cols-2">
                <div class="relative hidden items-start justify-start bg-[#f8f5f2] px-16 py-20 lg:flex">
                    <div class="absolute left-12 top-16 opacity-[0.035]">
                        <h2 class="font-wedding text-4xl leading-[2.5] text-[#2e1b1b]">Wedding</h2>
                        <h2 class="font-wedding text-4xl leading-[2.5] text-[#2e1b1b]">Gift</h2>
                    </div>
                </div>

                <div class="relative flex min-h-screen items-center justify-center overflow-hidden px-6 py-16">
                    <img src="{{ $coverPhoto }}" alt="Background Gift" class="absolute inset-0 h-full w-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-[#ffffff] via-[#ffffff]/80 to-transparent"></div>

                    <div class="relative z-10 w-full max-w-xl text-center text-[#2e1b1b]">
                        <h3 class="font-wedding text-3xl text-[#2e1b1b] mb-6">Wedding Gift</h3>
                        <p class="mb-12 text-sm leading-7 text-[#4d3c3c]">
                            Doa restu Anda merupakan karunia yang sangat berarti bagi kami. Dan jika memberi adalah ungkapan tanda kasih Anda, Anda dapat memberi kado secara cashless.
                        </p>
                        
                        <div class="space-y-6">
                            @forelse ($bankAccounts as $account)
                                <div class="p-6 border border-[#c8baba] rounded-lg shadow-sm bg-white">
                                    <h4 class="text-lg font-bold text-[#2e1b1b] uppercase mb-2">{{ $account->bank_name }}</h4>
                                    <p class="text-xl tracking-widest text-[#7b0f0f] font-mono mb-2" id="account-{{ $loop->index }}">{{ $account->account_number }}</p>
                                    <p class="text-sm text-[#5e4d4d] mb-4">a.n. {{ $account->account_name }}</p>
                                    <button type="button" onclick="copyToClipboard('{{ $account->account_number }}', 'account-{{ $loop->index }}')" class="inline-flex items-center gap-2 rounded-md bg-[#2e1b1b] px-4 py-2 text-xs font-semibold text-white shadow-md transition hover:bg-[#1b1010]">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                        Salin Nomor Rekening
                                    </button>
                                </div>
                            @empty
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="rsvp-section" class="relative min-h-screen w-full overflow-hidden bg-[#f8f5f2]">
            <div class="grid min-h-screen grid-cols-1 lg:grid-cols-2">
                <div class="relative hidden items-start justify-start bg-[#f8f5f2] px-16 py-20 lg:flex">
                    <div class="absolute left-12 top-16 opacity-[0.035]">
                        <h2 class="font-wedding text-4xl leading-[2.5] text-[#2e1b1b]">RSVP &</h2>
                        <h2 class="font-wedding text-4xl leading-[2.5] text-[#2e1b1b]">Wishes</h2>
                    </div>
                </div>

                <div class="relative flex min-h-screen items-center justify-center overflow-hidden bg-[#fbf8f6] px-6 py-16">
                    <div class="relative z-10 w-full max-w-xl text-center text-[#2e1b1b]">
                        <h3 class="font-wedding text-3xl text-[#2e1b1b] mb-6">RSVP & Wishes</h3>
                        <p class="mb-10 text-sm leading-7 text-[#4d3c3c]">
                            Berikan doa dan harapan terbaik Anda untuk kami.
                        </p>
                        
                        <div class="bg-white p-6 md:p-8 border border-[#c8baba] rounded-lg shadow-sm text-left">
                            <form action="{{ isset($invitation->slug) ? route('invitation.rsvp', $invitation->slug) : '#' }}" method="POST" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-sm font-medium text-[#2e1b1b] mb-1">Nama</label>
                                    <input type="text" name="name" required class="w-full px-4 py-2 border border-[#c8baba] rounded-md focus:outline-none focus:ring-2 focus:ring-[#7b0f0f] bg-transparent text-[#2e1b1b]" placeholder="Nama Anda">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-[#2e1b1b] mb-1">Kehadiran</label>
                                    <select name="status" required class="w-full px-4 py-2 border border-[#c8baba] rounded-md focus:outline-none focus:ring-2 focus:ring-[#7b0f0f] bg-transparent text-[#2e1b1b]">
                                        <option value="Hadir">Hadir</option>
                                        <option value="Tidak Hadir">Tidak Hadir</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-[#2e1b1b] mb-1">Ucapan & Doa</label>
                                    <textarea name="message" rows="3" required class="w-full px-4 py-2 border border-[#c8baba] rounded-md focus:outline-none focus:ring-2 focus:ring-[#7b0f0f] bg-transparent text-[#2e1b1b]" placeholder="Tuliskan ucapan & doa"></textarea>
                                </div>
                                <button type="submit" class="w-full rounded-md bg-[#7b0f0f] px-4 py-3 text-sm font-semibold text-white shadow-md transition hover:bg-[#5f0b0b]">
                                    Kirim Ucapan
                                </button>
                            </form>
                        </div>
                        
                        <div class="mt-10 max-h-80 overflow-y-auto bg-white p-6 border border-[#c8baba] rounded-lg shadow-sm text-left space-y-4">
                            @forelse ($guestsList as $guestMsg)
                                <div class="border-b border-[#e8e8e8] pb-4 last:border-0 last:pb-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="font-bold text-[#2e1b1b] text-sm">{{ $guestMsg->name }}</span>
                                        @if ($guestMsg->status == 'Hadir')
                                            <span class="bg-green-100 text-green-800 text-[10px] px-2 py-0.5 rounded-full">Hadir</span>
                                        @else
                                            <span class="bg-red-100 text-red-800 text-[10px] px-2 py-0.5 rounded-full">Tidak Hadir</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-[#5e4d4d]">{{ $guestMsg->created_at->diffForHumans() }}</p>
                                    <p class="mt-2 text-sm text-[#4d3c3c]">{{ $guestMsg->message }}</p>
                                </div>
                            @empty
                                <p class="text-sm text-center text-[#5e4d4d]">Belum ada ucapan.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="ig-story-section" class="relative min-h-screen w-full overflow-hidden bg-[#f8f5f2]">
            <div class="grid min-h-screen grid-cols-1 lg:grid-cols-2">
                <div class="relative hidden items-start justify-start bg-[#f8f5f2] px-16 py-20 lg:flex">
                    <div class="absolute left-12 top-16 opacity-[0.035]">
                        <h2 class="font-wedding text-4xl leading-[2.5] text-[#2e1b1b]">Instagram</h2>
                        <h2 class="font-wedding text-4xl leading-[2.5] text-[#2e1b1b]">Story</h2>
                    </div>
                </div>

                <div class="relative flex min-h-screen items-center justify-center overflow-hidden bg-[#fbf8f6] px-6 py-16">
                    <div class="relative z-10 w-full max-w-md text-center text-[#2e1b1b]">
                        <h3 class="font-wedding text-3xl text-[#2e1b1b] mb-2">Instagram Story</h3>
                        <p class="mb-8 text-sm leading-7 text-[#4d3c3c]">
                            Bagikan momen bahagia kami di Instagram Story Anda.
                        </p>
                        
                        <div class="bg-white p-3 border border-[#c8baba] rounded-xl shadow-sm mb-6">
                            <canvas id="igStoryCanvas" class="w-full h-auto rounded-lg shadow-sm" style="aspect-ratio: 9/16;"></canvas>
                        </div>
                        
                        <button type="button" id="downloadIgStory"
                            class="inline-flex items-center gap-2 rounded-md bg-[#7b0f0f] px-6 py-3 text-sm font-semibold text-white shadow-md transition hover:bg-[#5f0b0b]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                <polyline points="7 10 12 15 17 10" />
                                <line x1="12" y1="15" x2="12" y2="3" />
                            </svg>
                            Download Template
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <section id="closing-section" class="relative min-h-screen w-full overflow-hidden bg-[#f8f5f2]">
            <div class="grid min-h-screen grid-cols-1 lg:grid-cols-2">
                <div class="relative hidden items-start justify-start bg-[#f8f5f2] px-16 py-20 lg:flex">
                </div>

                <div class="relative flex min-h-screen items-center justify-center overflow-hidden bg-[#fbf8f6] px-6 py-16">
                    <img src="{{ $coverPhoto }}" alt="Closing" class="absolute inset-0 h-full w-full object-cover opacity-[0.15]">
                    
                    <div class="relative z-10 w-full max-w-xl text-center text-[#2e1b1b]">
                        <p class="mb-8 text-sm leading-7 text-[#4d3c3c]">
                            Merupakan suatu kehormatan dan kebahagiaan bagi kami apabila Bapak/Ibu/Saudara/i berkenan hadir untuk memberikan doa restu.
                        </p>
                        <p class="mb-12 text-sm leading-7 text-[#4d3c3c]">
                            Atas kehadiran dan doa restunya, kami ucapkan terima kasih.
                        </p>
                        
                        <h3 class="font-wedding text-4xl text-[#2e1b1b] mb-4">Wassalamu'alaikum Wr. Wb.</h3>
                        
                        <div class="mt-12">
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-[#8a7a7a] mb-4">Kami yang berbahagia</p>
                            <h2 class="font-wedding text-5xl leading-tight text-[#7b0f0f]">
                                {{ $brideName }} & {{ $groomName }}
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script>
        function openInvitation() {
            const cover = document.getElementById('cover-section');

            cover.classList.add('opacity-0');
            cover.classList.add('pointer-events-none');

            setTimeout(() => {
                cover.style.display = 'none';
                document.body.style.overflow = 'auto';
            }, 800);

            const bgMusic = document.getElementById('bgMusic');
            if (bgMusic) {
                bgMusic.play().catch(console.error);
                if (musicToggle) {
                    musicToggle.classList.add('is-active');
                }
            }
        }

        document.body.style.overflow = 'hidden';

        const musicToggle = document.getElementById('music-toggle');
        const scrollToggle = document.getElementById('scroll-toggle');
        let autoScrollTimer = null;

        if (musicToggle) {
            musicToggle.addEventListener('click', function() {
                const isActive = this.classList.toggle('is-active');
                const bgMusic = document.getElementById('bgMusic');
                if (bgMusic) {
                    if (isActive) {
                        bgMusic.play().catch(console.error);
                    } else {
                        bgMusic.pause();
                    }
                }
            });
        }

        if (scrollToggle) {
            scrollToggle.addEventListener('click', function() {
                const isActive = this.classList.toggle('is-active');

                if (isActive) {
                    autoScrollTimer = window.setInterval(() => {
                        window.scrollBy({
                            top: 1,
                            behavior: 'smooth'
                        });
                    }, 60);
                } else if (autoScrollTimer) {
                    window.clearInterval(autoScrollTimer);
                    autoScrollTimer = null;
                }
            });
        }

        // Countdown Logic
        const eventDateStr = "{{ $invitation->event_date ?? '2025-12-12' }}";
        const eventTimeStr = "{{ $eventTime }}";

        if (eventDateStr && eventTimeStr) {
            // Combine date and time, assuming local timezone for simplicity
            const targetDate = new Date(eventDateStr + 'T' + eventTimeStr + ':00');
            const countdownInterval = setInterval(() => {
                const now = new Date().getTime();
                const distance = targetDate - now;

                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                // Update the elements
                document.getElementById("days").innerText = String(days).padStart(2, '0');
                document.getElementById("hours").innerText = String(hours).padStart(2, '0');
                document.getElementById("minutes").innerText = String(minutes).padStart(2, '0');
                document.getElementById("seconds").innerText = String(seconds).padStart(2, '0');

                // If the countdown is over, write some text
                if (distance < 0) {
                    clearInterval(countdownInterval);
                    document.getElementById("countdown").innerHTML =
                        "<span class='text-xl text-[#7b0f0f]'>Acara Telah Dimulai!</span>";
                }
            }, 1000);
        }

        // Add to Calendar Logic
        function addToCalendar(title, location, startDateTime, endDateTime) {
            // Google Calendar URL format: https://calendar.google.com/calendar/render?action=TEMPLATE&text=EventTitle&details=EventDetails&location=EventLocation&dates=YYYYMMDDTHHMMSS/YYYYMMDDTHHMMSS
            const googleCalendarUrl =
                `https://calendar.google.com/calendar/render?action=TEMPLATE&text=${encodeURIComponent(title)}&details=${encodeURIComponent('Pernikahan ' + title)}&location=${encodeURIComponent(location)}&dates=${startDateTime}/${endDateTime}`;
            window.open(googleCalendarUrl, '_blank');
            return false; // Prevent default link behavior
        }
        
        function copyToClipboard(text, elementId) {
            navigator.clipboard.writeText(text).then(function() {
                const el = document.getElementById(elementId);
                if(el) {
                    const originalText = el.innerText;
                    el.innerText = 'Tersalin!';
                    setTimeout(() => {
                        el.innerText = originalText;
                    }, 2000);
                }
            }).catch(function(err) {
                console.error('Could not copy text: ', err);
            });
        }

        // Love Story Scroll Reveal
        (function() {
            const revealEls = document.querySelectorAll('.love-story-reveal');
            if (!revealEls.length) return;

            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry, i) {
                    if (entry.isIntersecting) {
                        const delay = entry.target.dataset.delay || 0;
                        setTimeout(function() {
                            entry.target.classList.add('is-visible');
                        }, delay);
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.15 });

            revealEls.forEach(function(el, i) {
                el.dataset.delay = i * 120;
                observer.observe(el);
            });
        })();

        // IG Story Canvas script
        (function() {
            const CANVAS_W = 1080;
            const CANVAS_H = 1920;
            const coupleNameText = @json($igCoupleName);
            const brideNameText = @json($igBrideName);
            const groomNameText = @json($igGroomName);
            const dateText = @json($igStoryDate);
            const websiteUrl = 'janjisucikita.com';
            const igHandle = '-';
            const photoSrc = @json($igStoryPhoto);

            const scriptFont = new FontFace('Cinzel', 'url(https://fonts.gstatic.com/s/cinzel/v19/8vIX7Otw3tsBNzwf-g.woff2)');
            const sansFont = new FontFace('Poppins', 'url(https://fonts.gstatic.com/s/poppins/v20/pxiEyp8kv8JHgFVrJJfecg.woff2)');

            Promise.all([scriptFont.load(), sansFont.load()]).then(function(fonts) {
                fonts.forEach(function(f) { document.fonts.add(f); });
                renderIgStory();
            }).catch(function() {
                renderIgStory();
            });

            function fitScriptFont(ctx, text, maxWidth, initialSize, minSize) {
                let fontSize = initialSize;
                while (fontSize > minSize) {
                    ctx.font = `600 ${fontSize}px Cinzel, serif`;
                    if (ctx.measureText(text).width <= maxWidth) {
                        return fontSize;
                    }
                    fontSize -= 2;
                }
                return minSize;
            }

            function renderIgStory() {
                const canvas = document.getElementById('igStoryCanvas');
                if (!canvas) return;
                canvas.width = CANVAS_W;
                canvas.height = CANVAS_H;
                const ctx = canvas.getContext('2d');

                const img = new Image();
                img.crossOrigin = 'anonymous';
                img.onload = function() {
                    const imgRatio = img.width / img.height;
                    const canvasRatio = CANVAS_W / CANVAS_H;
                    let sx = 0, sy = 0, sw = img.width, sh = img.height;
                    if (imgRatio > canvasRatio) {
                        sw = img.height * canvasRatio;
                        sx = (img.width - sw) / 2;
                    } else {
                        sh = img.width / canvasRatio;
                        sy = (img.height - sh) / 2;
                    }
                    ctx.drawImage(img, sx, sy, sw, sh, 0, 0, CANVAS_W, CANVAS_H);

                    const gradStart = CANVAS_H * 0.45;
                    const grad = ctx.createLinearGradient(0, gradStart, 0, CANVAS_H);
                    grad.addColorStop(0, 'rgba(255, 255, 255, 0)');
                    grad.addColorStop(0.35, 'rgba(255, 255, 255, 0.75)');
                    grad.addColorStop(0.6, 'rgba(255, 255, 255, 0.9)');
                    grad.addColorStop(1, 'rgba(255, 255, 255, 1)');
                    ctx.fillStyle = grad;
                    ctx.fillRect(0, gradStart, CANVAS_W, CANVAS_H - gradStart);

                    const nameCenterY = CANVAS_H * 0.655;
                    const lineGap = 84;
                    const nameMaxWidth = CANVAS_W - 140;
                    const brideFontSize = fitScriptFont(ctx, brideNameText, nameMaxWidth, 64, 40);
                    const groomLineText = `& ${groomNameText}`;
                    const groomFontSize = fitScriptFont(ctx, groomLineText, nameMaxWidth, 64, 40);

                    ctx.textAlign = 'center';
                    ctx.fillStyle = '#2e1b1b';

                    ctx.font = `600 ${brideFontSize}px Cinzel, serif`;
                    ctx.fillText(brideNameText, CANVAS_W / 2, nameCenterY - (lineGap / 2));

                    ctx.font = `600 ${groomFontSize}px Cinzel, serif`;
                    ctx.fillText(groomLineText, CANVAS_W / 2, nameCenterY + (lineGap / 2));

                    const dateY = nameCenterY + (lineGap / 2) + 76;
                    ctx.font = '500 32px Poppins, sans-serif';
                    if ('letterSpacing' in ctx) ctx.letterSpacing = '4px';
                    ctx.fillStyle = '#4d3c3c';
                    ctx.fillText(dateText, CANVAS_W / 2, dateY);

                    const wishLabelY = dateY + 66;
                    ctx.font = '400 28px Poppins, sans-serif';
                    ctx.fillStyle = '#5e4d4d';
                    ctx.fillText('Wish', CANVAS_W / 2, wishLabelY);

                    const boxMargin = 60;
                    const boxTop = wishLabelY + 24;
                    const boxWidth = CANVAS_W - (boxMargin * 2);
                    const boxHeight = 360;
                    const boxRadius = 16;

                    ctx.fillStyle = 'rgba(251, 248, 246, 0.9)';
                    ctx.strokeStyle = '#c8baba';
                    ctx.lineWidth = 2;
                    ctx.beginPath();
                    ctx.moveTo(boxMargin + boxRadius, boxTop);
                    ctx.lineTo(boxMargin + boxWidth - boxRadius, boxTop);
                    ctx.quadraticCurveTo(boxMargin + boxWidth, boxTop, boxMargin + boxWidth, boxTop + boxRadius);
                    ctx.lineTo(boxMargin + boxWidth, boxTop + boxHeight - boxRadius);
                    ctx.quadraticCurveTo(boxMargin + boxWidth, boxTop + boxHeight, boxMargin + boxWidth - boxRadius, boxTop + boxHeight);
                    ctx.lineTo(boxMargin + boxRadius, boxTop + boxHeight);
                    ctx.quadraticCurveTo(boxMargin, boxTop + boxHeight, boxMargin, boxTop + boxHeight - boxRadius);
                    ctx.lineTo(boxMargin, boxTop + boxRadius);
                    ctx.quadraticCurveTo(boxMargin, boxTop, boxMargin + boxRadius, boxTop);
                    ctx.closePath();
                    ctx.fill();
                    ctx.stroke();

                    const footerY = CANVAS_H - 60;
                    ctx.font = '400 24px Poppins, sans-serif';
                    ctx.fillStyle = '#7a6a6a';
                    ctx.textAlign = 'left';
                    ctx.fillText(websiteUrl, boxMargin, footerY);
                    ctx.textAlign = 'right';
                    ctx.fillText(igHandle, CANVAS_W - boxMargin, footerY);
                    ctx.textAlign = 'center';
                };
                img.onerror = function() {
                    ctx.fillStyle = '#f8f5f2';
                    ctx.fillRect(0, 0, CANVAS_W, CANVAS_H);
                    ctx.fillStyle = '#2e1b1b';
                    ctx.font = '400 32px Poppins, sans-serif';
                    ctx.fillText('Gagal memuat foto', CANVAS_W/2, CANVAS_H/2);
                };
                img.src = photoSrc;
            }

            const dlBtn = document.getElementById('downloadIgStory');
            if(dlBtn) {
                dlBtn.addEventListener('click', function() {
                    const canvas = document.getElementById('igStoryCanvas');
                    if(!canvas) return;
                    const dataUrl = canvas.toDataURL('image/jpeg', 0.9);
                    const link = document.createElement('a');
                    link.download = 'IG-Story-' + coupleNameText.replace(/\s/g, '-') + '.jpg';
                    link.href = dataUrl;
                    link.click();
                });
            }
        })();
    </script>
</body>

</html>
