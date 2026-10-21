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
    $googleMapsUrl = $invitation->google_maps_url ?? 'https://maps.google.com';

    // Format event time safely to HH:mm string (e.g. "09:00")
    $rawEventTime = $invitation->event_time;
    $eventTime = '09:00';
    if (!empty($rawEventTime)) {
        $eventTime = $rawEventTime instanceof \Carbon\CarbonInterface
            ? $rawEventTime->format('H:i')
            : \Illuminate\Support\Carbon::parse($rawEventTime)->format('H:i');
    }
    
    $brideParentName = $invitation->bride_parent_name ?? 'Putri dari Bpk. Fulan & Ibu Fulanah';
    $groomParentName = $invitation->groom_parent_name ?? 'Putra dari Bpk. Fulan & Ibu Fulanah';
    
    $brideInstagram = $invitation->bride_instagram ?? '#';
    $groomInstagram = $invitation->groom_instagram ?? '#';
    
    $musicUrl = $invitation->music_url ? ($invitation->music_signed_url ?? asset('storage/' . $invitation->music_url)) : null;

    $rawEvents = (isset($invitation->events) && $invitation->events->count() > 0)
        ? $invitation->events
        : ((isset($invitation->invitationEvents) && $invitation->invitationEvents->count() > 0)
            ? $invitation->invitationEvents
            : collect());

    $invitationEvents = $rawEvents->isNotEmpty()
        ? $rawEvents->map(function ($event) use ($venueName, $venueAddress, $eventTime) {
            $eDate = $event->event_date ?? $event->date ?? null;
            $eTime = $event->event_time ?? $event->time ?? null;
            $formattedTime = $eTime
                ? ($eTime instanceof \Carbon\CarbonInterface ? $eTime->format('H:i') : \Illuminate\Support\Carbon::parse($eTime)->format('H:i'))
                : $eventTime;

            return (object) [
                'name' => $event->event_name ?? $event->name ?? 'Acara Pernikahan',
                'date' => $eDate ? ($eDate instanceof \Carbon\CarbonInterface ? $eDate : \Illuminate\Support\Carbon::parse($eDate)) : null,
                'time' => $formattedTime,
                'venue_name' => $event->venue_name ?? $venueName,
                'venue_address' => $event->venue_address ?? $venueAddress,
                'google_maps_url' => $event->venue_maps_url ?? $event->google_maps_url ?? null,
            ];
        })
        : collect([
            (object) [
                'name' => 'Resepsi Pernikahan',
                'date' => $eventDate,
                'time' => $eventTime,
                'venue_name' => $venueName,
                'venue_address' => $venueAddress,
                'google_maps_url' => $googleMapsUrl,
            ],
        ]);

    // Primary target date & time for countdown
    $firstEvent = $invitationEvents->first();
    $targetDateObj = $eventDate ?? ($firstEvent?->date ? \Illuminate\Support\Carbon::parse($firstEvent->date) : \Illuminate\Support\Carbon::parse('2026-12-12'));
    $targetTimeStr = ($firstEvent?->time ?? $eventTime ?? '09:00') . ':00';
    $targetCountdownIso = $targetDateObj->format('Y-m-d') . 'T' . $targetTimeStr;

    $photos = isset($invitation->photos) && $invitation->photos->count() > 0 
        ? $invitation->photos->map(fn($p) => asset('storage/' . $p->file_path)) 
        : collect([
            asset('assets_invitation/model/fotomodel4.png'),
            asset('assets_invitation/model/fotomodel5.png'),
            asset('assets_invitation/model/fotomodel6.png'),
            asset('assets_invitation/model/fotomodel7.png')
        ]);

    // Background photos per section taken sequentially from gallery photos
    $coupleBgPhoto  = $photos->get(0) ?? $coverPhoto;
    $eventsBgPhoto  = $photos->get(1) ?? $coupleBgPhoto;
    $giftBgPhoto    = $photos->get(2) ?? $photos->get(0) ?? $coverPhoto;
    $closingBgPhoto = $photos->get(3) ?? $photos->get(1) ?? $photos->get(0) ?? $coverPhoto;
        
    $bankAccounts = isset($invitation->bankAccounts) && $invitation->bankAccounts->count() > 0 
        ? $invitation->bankAccounts 
        : collect([
            (object)[
                'bank_name' => 'Bank BCA',
                'account_number' => '1234567890',
                'account_name' => 'Nama Mempelai'
            ]
        ]);
    
    // Prepare RSVP & wishes list from database
    $rawRsvps = (isset($invitation->rsvps) && $invitation->rsvps->count() > 0)
        ? $invitation->rsvps
        : collect();
    $rawWishes = (isset($invitation->wishes) && $invitation->wishes->count() > 0)
        ? $invitation->wishes
        : collect();

    $mergedWishes = $rawRsvps
        ->filter(fn($r) => !empty($r->message) || !empty($r->name))
        ->map(fn($r) => (object)[
            'name' => $r->name ?? 'Tamu',
            'status' => $r->status, // attending, not_attending, maybe
            'pax' => $r->pax ?? 1,
            'message' => $r->message,
            'created_at' => $r->created_at ? \Illuminate\Support\Carbon::parse($r->created_at) : now(),
        ])
        ->concat(
            $rawWishes->map(fn($w) => (object)[
                'name' => $w->name ?? 'Tamu',
                'status' => null,
                'pax' => 1,
                'message' => $w->message,
                'created_at' => $w->created_at ? \Illuminate\Support\Carbon::parse($w->created_at) : now(),
            ])
        )
        ->sortByDesc('created_at')
        ->values();

    $guestsList = $mergedWishes->isNotEmpty() ? $mergedWishes : collect([
        (object)[
            'name' => 'Budi & Keluarga',
            'status' => 'attending',
            'pax' => 2,
            'created_at' => now()->subHours(2),
            'message' => 'Selamat menempuh hidup baru, semoga menjadi keluarga sakinah mawaddah warahmah!'
        ]
    ]);
    
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
                        <img src="{{ $coupleBgPhoto }}" alt="Background Couple"
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
                        {{-- Use a subtle background image or pattern from gallery --}}
                        <img src="{{ $eventsBgPhoto }}" alt="Background Events"
                            class="h-full w-full object-cover blur-sm">
                    </div>
                    <div class="absolute inset-0 bg-white/85"></div>

                    <div class="relative z-10 w-full max-w-xl text-center text-[#2e1b1b]">
                        <p class="mb-12 text-sm leading-7 text-[#4d3c3c]">
                            Dengan hormat, kami mengundang Bapak/Ibu/Saudara/i untuk hadir dalam rangkaian acara kami.
                        </p>

                        {{-- Countdown Timer --}}
                        @if ($eventDateStr || $targetCountdownIso)
                            <div class="mb-12">
                                <h3 class="font-wedding text-3xl text-[#2e1b1b] mb-6">Menuju Hari Bahagia</h3>
                                <div id="countdown" class="grid grid-cols-4 gap-3 sm:gap-4 max-w-md mx-auto text-center" data-target="{{ $targetCountdownIso }}">
                                    <div class="bg-white/90 backdrop-blur-sm border border-[#e8dede] rounded-xl py-3 px-2 shadow-xs transition hover:border-[#7b0f0f]/30">
                                        <span id="days"
                                            class="block font-wedding text-3xl sm:text-4xl font-semibold text-[#7b0f0f]">00</span>
                                        <span class="text-[11px] sm:text-xs uppercase tracking-wider text-[#5e4d4d] font-medium mt-1 block">Hari</span>
                                    </div>
                                    <div class="bg-white/90 backdrop-blur-sm border border-[#e8dede] rounded-xl py-3 px-2 shadow-xs transition hover:border-[#7b0f0f]/30">
                                        <span id="hours"
                                            class="block font-wedding text-3xl sm:text-4xl font-semibold text-[#7b0f0f]">00</span>
                                        <span class="text-[11px] sm:text-xs uppercase tracking-wider text-[#5e4d4d] font-medium mt-1 block">Jam</span>
                                    </div>
                                    <div class="bg-white/90 backdrop-blur-sm border border-[#e8dede] rounded-xl py-3 px-2 shadow-xs transition hover:border-[#7b0f0f]/30">
                                        <span id="minutes"
                                            class="block font-wedding text-3xl sm:text-4xl font-semibold text-[#7b0f0f]">00</span>
                                        <span class="text-[11px] sm:text-xs uppercase tracking-wider text-[#5e4d4d] font-medium mt-1 block">Menit</span>
                                    </div>
                                    <div class="bg-white/90 backdrop-blur-sm border border-[#e8dede] rounded-xl py-3 px-2 shadow-xs transition hover:border-[#7b0f0f]/30">
                                        <span id="seconds"
                                            class="block font-wedding text-3xl sm:text-4xl font-semibold text-[#7b0f0f]">00</span>
                                        <span class="text-[11px] sm:text-xs uppercase tracking-wider text-[#5e4d4d] font-medium mt-1 block">Detik</span>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Event List --}}
                        @forelse ($invitationEvents as $event)
                            <div class="mb-10 last:mb-0 p-6 border border-[#c8baba] rounded-lg shadow-sm bg-white">
                                <h3 class="font-wedding text-3xl text-[#2e1b1b] mb-4">{{ $event->name }}</h3>
                                @if ($event->date)
                                    <p class="text-lg font-semibold text-[#7b0f0f] mb-2">
                                        {{ \Carbon\Carbon::parse($event->date)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
                                    </p>
                                @endif
                                @if ($event->time)
                                    <p class="text-base text-[#5e4d4d] mb-4">Pukul {{ $event->time }} WIB</p>
                                @endif
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
                                    $calDate = $targetDateObj->format('Ymd');
                                    $calStartTime = \Carbon\Carbon::parse($targetTimeStr);
                                    $calTimeStr = $calStartTime->format('His');
                                    $calTimeEndStr = $calStartTime->copy()->addHours(2)->format('His');
                                @endphp
                                <a href="#"
                                    onclick="addToCalendar('{{ $invitationTitle }}', '{{ $venueName }}', '{{ $calDate }}T{{ $calTimeStr }}', '{{ $calDate }}T{{ $calTimeEndStr }}'); return false;"
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
                    <img src="{{ $giftBgPhoto }}" alt="Background Gift" class="absolute inset-0 h-full w-full object-cover">
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
                            {{-- Success Alert jika redirect biasa dari backend --}}
                            @if (session('success'))
                                <div id="rsvp-session-alert" class="mb-5 p-4 rounded-md bg-green-50 border border-green-200 text-green-800 text-sm flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>{{ session('success') }}</span>
                                </div>
                            @endif

                            {{-- Alert Sukses Pengiriman AJAX --}}
                            <div id="rsvp-ajax-alert" class="hidden mb-5 p-4 rounded-md bg-green-50 border border-green-200 text-green-800 text-sm flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span id="rsvp-ajax-message">Terima kasih, konfirmasi kehadiran dan doa restu Anda telah tersimpan!</span>
                            </div>

                            <form id="rsvp-form" action="{{ isset($invitation->slug) ? route('invitation.rsvp', $invitation->slug) : '#' }}" method="POST" class="space-y-4">
                                @csrf
                                <input type="hidden" name="redirect_anchor" value="rsvp-section">
                                @if (!empty($guest?->id))
                                    <input type="hidden" name="guest_id" value="{{ $guest->id }}">
                                @endif
                                @if (!empty($guest?->phone))
                                    <input type="hidden" name="phone" value="{{ $guest->phone }}">
                                @endif

                                <div>
                                    <label class="block text-sm font-medium text-[#2e1b1b] mb-1">Nama</label>
                                    <input type="text" name="name" id="rsvp_name" value="{{ $guest?->name ?? old('name') }}" required class="w-full px-4 py-2.5 border border-[#c8baba] rounded-md focus:outline-none focus:ring-2 focus:ring-[#7b0f0f] bg-white text-[#2e1b1b]" placeholder="Nama Anda">
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-[#2e1b1b] mb-1">Konfirmasi Kehadiran</label>
                                        <select name="status" id="rsvp_status" required class="w-full px-4 py-2.5 border border-[#c8baba] rounded-md focus:outline-none focus:ring-2 focus:ring-[#7b0f0f] bg-white text-[#2e1b1b]">
                                            <option value="attending" {{ old('status') === 'attending' ? 'selected' : '' }}>Hadir</option>
                                            <option value="not_attending" {{ old('status') === 'not_attending' ? 'selected' : '' }}>Tidak Hadir</option>
                                            <option value="maybe" {{ old('status') === 'maybe' ? 'selected' : '' }}>Masih Ragu</option>
                                        </select>
                                    </div>

                                    <div id="rsvp_pax_container">
                                        <label class="block text-sm font-medium text-[#2e1b1b] mb-1">Jumlah Tamu</label>
                                        <select name="pax" id="rsvp_pax" required class="w-full px-4 py-2.5 border border-[#c8baba] rounded-md focus:outline-none focus:ring-2 focus:ring-[#7b0f0f] bg-white text-[#2e1b1b]">
                                            <option value="1">1 Orang</option>
                                            <option value="2">2 Orang</option>
                                            <option value="3">3 Orang</option>
                                            <option value="4">4 Orang</option>
                                            <option value="5">5 Orang</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-[#2e1b1b] mb-1">Ucapan & Doa</label>
                                    <textarea name="message" id="rsvp_message" rows="3" required class="w-full px-4 py-2.5 border border-[#c8baba] rounded-md focus:outline-none focus:ring-2 focus:ring-[#7b0f0f] bg-white text-[#2e1b1b]" placeholder="Tuliskan ucapan & doa restu untuk kedua mempelai"></textarea>
                                </div>

                                <button type="submit" id="rsvp_submit_btn" class="w-full rounded-md bg-[#7b0f0f] px-4 py-3 text-sm font-semibold text-white shadow-md transition hover:bg-[#5f0b0b] flex items-center justify-center gap-2">
                                    <span id="rsvp_btn_text">Kirim Ucapan & Konfirmasi</span>
                                </button>
                            </form>
                        </div>
                        
                        <div id="rsvp-list-container" class="mt-10 max-h-80 overflow-y-auto bg-white p-6 border border-[#c8baba] rounded-lg shadow-sm text-left space-y-4">
                            @forelse ($guestsList as $guestMsg)
                                <div class="rsvp-item border-b border-[#e8e8e8] pb-4 last:border-0 last:pb-0">
                                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                                        <span class="font-bold text-[#2e1b1b] text-sm">{{ $guestMsg->name }}</span>
                                        @if (in_array(strtolower($guestMsg->status ?? ''), ['attending', 'hadir']))
                                            <span class="bg-green-100 text-green-800 text-[10px] px-2 py-0.5 rounded-full font-medium">Hadir</span>
                                        @elseif (in_array(strtolower($guestMsg->status ?? ''), ['not_attending', 'tidak hadir']))
                                            <span class="bg-red-100 text-red-800 text-[10px] px-2 py-0.5 rounded-full font-medium">Tidak Hadir</span>
                                        @elseif (in_array(strtolower($guestMsg->status ?? ''), ['maybe', 'masih ragu', 'ragu-ragu']))
                                            <span class="bg-amber-100 text-amber-800 text-[10px] px-2 py-0.5 rounded-full font-medium">Masih Ragu</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-[#5e4d4d]">{{ $guestMsg->created_at ? \Carbon\Carbon::parse($guestMsg->created_at)->diffForHumans() : 'Baru saja' }}</p>
                                    @if (!empty($guestMsg->message))
                                        <p class="mt-2 text-sm text-[#4d3c3c] leading-relaxed">{{ $guestMsg->message }}</p>
                                    @endif
                                </div>
                            @empty
                                <p id="rsvp-empty-state" class="text-sm text-center text-[#5e4d4d]">Belum ada ucapan. Jadilah yang pertama memberikan ucapan!</p>
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
                    <img src="{{ $closingBgPhoto }}" alt="Closing" class="absolute inset-0 h-full w-full object-cover opacity-[0.15]">
                    
                    <div class="relative z-10 w-full max-w-xl text-center text-[#2e1b1b]">
                        <p class="mb-8 text-sm leading-7 text-[#4d3c3c]">
                            Merupakan suatu kehormatan dan kebahagiaan bagi kami apabila Bapak/Ibu/Saudara/i berkenan hadir untuk memberikan doa restu.
                        </p>
                        <p class="mb-12 text-sm leading-7 text-[#4d3c3c]">
                            Atas kehadiran dan doa restunya, kami ucapkan terima kasih.
                        </p>
                        
                        <h3 class="font-wedding text-2xl sm:text-3xl md:text-4xl text-[#2e1b1b] mb-4">Wassalamu'alaikum Wr. Wb.</h3>
                        
                        <div class="mt-12">
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-[#8a7a7a] mb-4">Kami yang berbahagia</p>
                            <h2 class="font-wedding text-3xl sm:text-4xl md:text-5xl leading-tight text-[#7b0f0f]">
                                <span class="block">{{ $brideName }}</span>
                                <span class="block text-xl sm:text-2xl py-1 text-[#5e4d4d]">&</span>
                                <span class="block">{{ $groomName }}</span>
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script>
        const invitationSlug = "{{ $invitation->slug ?? 'default' }}";
        const sessionKey = 'invitation_opened_' + invitationSlug;
        const hasSessionSuccess = @json(session()->has('success'));
        const hasHash = window.location.hash.length > 1;
        const wasOpenedInSession = sessionStorage.getItem(sessionKey) === 'true';

        // Check if invitation should be open already (e.g. after RSVP reload, anchor, or session memory)
        const shouldBypassCover = hasSessionSuccess || hasHash || wasOpenedInSession;

        function openInvitation(silent = false) {
            const cover = document.getElementById('cover-section');
            if (!cover) return;

            sessionStorage.setItem(sessionKey, 'true');

            if (silent) {
                cover.style.display = 'none';
                cover.classList.add('opacity-0', 'pointer-events-none');
                document.body.style.overflow = 'auto';
            } else {
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
        }

        if (shouldBypassCover) {
            openInvitation(true);
            if (hasHash) {
                setTimeout(() => {
                    const el = document.querySelector(window.location.hash);
                    if (el) el.scrollIntoView({ behavior: 'smooth' });
                }, 150);
            } else if (hasSessionSuccess) {
                setTimeout(() => {
                    const el = document.getElementById('rsvp-section');
                    if (el) el.scrollIntoView({ behavior: 'smooth' });
                }, 150);
            }
        } else {
            document.body.style.overflow = 'hidden';
        }

        // AJAX RSVP Submission Handler
        (function() {
            const form = document.getElementById('rsvp-form');
            if (!form) return;

            const submitBtn = document.getElementById('rsvp_submit_btn');
            const btnText = document.getElementById('rsvp_btn_text');
            const ajaxAlert = document.getElementById('rsvp-ajax-alert');
            const ajaxMessage = document.getElementById('rsvp-ajax-message');
            const listContainer = document.getElementById('rsvp-list-container');
            const emptyState = document.getElementById('rsvp-empty-state');
            const statusSelect = document.getElementById('rsvp_status');
            const paxContainer = document.getElementById('rsvp_pax_container');
            const paxSelect = document.getElementById('rsvp_pax');

            if (statusSelect && paxContainer && paxSelect) {
                statusSelect.addEventListener('change', function() {
                    if (this.value === 'not_attending') {
                        paxContainer.style.opacity = '0.4';
                        paxContainer.style.pointerEvents = 'none';
                        paxSelect.value = '1';
                    } else {
                        paxContainer.style.opacity = '1';
                        paxContainer.style.pointerEvents = 'auto';
                    }
                });
            }

            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                if (submitBtn) {
                    submitBtn.disabled = true;
                    if (btnText) btnText.textContent = 'Mengirim...';
                }

                const formData = new FormData(form);
                const actionUrl = form.getAttribute('action');

                // Mark session storage that invitation is opened
                sessionStorage.setItem(sessionKey, 'true');

                try {
                    const response = await fetch(actionUrl, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });

                    const data = await response.json().catch(() => null);

                    if (response.ok && data && data.success) {
                        if (ajaxAlert) {
                            if (ajaxMessage) ajaxMessage.textContent = data.message || 'Terima kasih, konfirmasi kehadiran dan doa restu Anda telah tersimpan!';
                            ajaxAlert.classList.remove('hidden');
                        }

                        // Prepend new RSVP to the list
                        const nameVal = formData.get('name') || 'Tamu';
                        const statusVal = formData.get('status') || 'attending';
                        const messageVal = formData.get('message') || '';

                        let badgeHtml = '';
                        if (statusVal === 'attending') {
                            badgeHtml = '<span class="bg-green-100 text-green-800 text-[10px] px-2 py-0.5 rounded-full font-medium">Hadir</span>';
                        } else if (statusVal === 'not_attending') {
                            badgeHtml = '<span class="bg-red-100 text-red-800 text-[10px] px-2 py-0.5 rounded-full font-medium">Tidak Hadir</span>';
                        } else if (statusVal === 'maybe') {
                            badgeHtml = '<span class="bg-amber-100 text-amber-800 text-[10px] px-2 py-0.5 rounded-full font-medium">Masih Ragu</span>';
                        }

                        const newItem = document.createElement('div');
                        newItem.className = 'rsvp-item border-b border-[#e8e8e8] pb-4 last:border-0 last:pb-0 bg-[#fbf8f6]/70 p-3 rounded-lg transition-all duration-300';
                        newItem.innerHTML = `
                            <div class="flex items-center gap-2 mb-1 flex-wrap">
                                <span class="font-bold text-[#2e1b1b] text-sm">${escapeHtml(nameVal)}</span>
                                ${badgeHtml}
                            </div>
                            <p class="text-xs text-[#5e4d4d]">Baru saja</p>
                            ${messageVal ? `<p class="mt-2 text-sm text-[#4d3c3c] leading-relaxed">${escapeHtml(messageVal)}</p>` : ''}
                        `;

                        if (emptyState) emptyState.remove();
                        if (listContainer) {
                            listContainer.insertBefore(newItem, listContainer.firstChild);
                        }

                        // Reset message input
                        const messageInput = document.getElementById('rsvp_message');
                        if (messageInput) messageInput.value = '';

                    } else if (!response.ok && response.status === 404 && actionUrl.includes('demo')) {
                        // Demo mode simulation
                        if (ajaxAlert) {
                            if (ajaxMessage) ajaxMessage.textContent = 'Simulasi Demo: Ucapan & RSVP Anda berhasil disimpan!';
                            ajaxAlert.classList.remove('hidden');
                        }
                        const nameVal = formData.get('name') || 'Tamu Demo';
                        const statusVal = formData.get('status') || 'attending';
                        const messageVal = formData.get('message') || '';

                        let badgeHtml = '<span class="bg-green-100 text-green-800 text-[10px] px-2 py-0.5 rounded-full font-medium">Hadir</span>';
                        if (statusVal === 'not_attending') {
                            badgeHtml = '<span class="bg-red-100 text-red-800 text-[10px] px-2 py-0.5 rounded-full font-medium">Tidak Hadir</span>';
                        } else if (statusVal === 'maybe') {
                            badgeHtml = '<span class="bg-amber-100 text-amber-800 text-[10px] px-2 py-0.5 rounded-full font-medium">Masih Ragu</span>';
                        }

                        const newItem = document.createElement('div');
                        newItem.className = 'rsvp-item border-b border-[#e8e8e8] pb-4 last:border-0 last:pb-0 bg-[#fbf8f6]/70 p-3 rounded-lg';
                        newItem.innerHTML = `
                            <div class="flex items-center gap-2 mb-1 flex-wrap">
                                <span class="font-bold text-[#2e1b1b] text-sm">${escapeHtml(nameVal)}</span>
                                ${badgeHtml}
                            </div>
                            <p class="text-xs text-[#5e4d4d]">Baru saja</p>
                            ${messageVal ? `<p class="mt-2 text-sm text-[#4d3c3c] leading-relaxed">${escapeHtml(messageVal)}</p>` : ''}
                        `;
                        if (emptyState) emptyState.remove();
                        if (listContainer) listContainer.insertBefore(newItem, listContainer.firstChild);

                        const messageInput = document.getElementById('rsvp_message');
                        if (messageInput) messageInput.value = '';

                    } else {
                        // If validation error or other error, fallback to normal submission
                        form.submit();
                        return;
                    }
                } catch (err) {
                    console.error('RSVP submit error:', err);
                    form.submit();
                    return;
                } finally {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        if (btnText) btnText.textContent = 'Kirim Ucapan & Konfirmasi';
                    }
                }
            });

            function escapeHtml(text) {
                const map = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                };
                return text.replace(/[&<>"']/g, function(m) { return map[m]; });
            }
        })();

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
        (function() {
            const countdownEl = document.getElementById("countdown");
            if (!countdownEl) return;

            const targetIso = countdownEl.dataset.target || @json($targetCountdownIso);
            if (!targetIso) return;

            function parseCountdownDate(str) {
                if (!str) return null;
                const match = str.match(/^(\d{4})-(\d{2})-(\d{2})[T ](\d{2}):(\d{2})(?::(\d{2}))?/);
                if (match) {
                    return new Date(
                        parseInt(match[1], 10),
                        parseInt(match[2], 10) - 1,
                        parseInt(match[3], 10),
                        parseInt(match[4], 10),
                        parseInt(match[5], 10),
                        match[6] ? parseInt(match[6], 10) : 0
                    ).getTime();
                }
                const fallback = new Date(str).getTime();
                return isNaN(fallback) ? null : fallback;
            }

            const targetTimestamp = parseCountdownDate(targetIso);
            if (!targetTimestamp) return;

            const daysEl = document.getElementById("days");
            const hoursEl = document.getElementById("hours");
            const minutesEl = document.getElementById("minutes");
            const secondsEl = document.getElementById("seconds");

            function updateCountdown() {
                const now = Date.now();
                const distance = targetTimestamp - now;

                if (distance <= 0) {
                    if (daysEl) daysEl.innerText = "00";
                    if (hoursEl) hoursEl.innerText = "00";
                    if (minutesEl) minutesEl.innerText = "00";
                    if (secondsEl) secondsEl.innerText = "00";

                    countdownEl.innerHTML = `
                        <div class="col-span-4 py-4 px-6 bg-white/90 border border-[#e8dede] rounded-xl shadow-xs text-center w-full">
                            <span class="font-wedding text-2xl text-[#7b0f0f] block mb-1">Acara Telah Dimulai!</span>
                            <span class="text-xs text-[#5e4d4d]">Momen bahagia kami telah tiba.</span>
                        </div>
                    `;
                    return true;
                }

                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                if (daysEl) daysEl.innerText = String(days).padStart(2, '0');
                if (hoursEl) hoursEl.innerText = String(hours).padStart(2, '0');
                if (minutesEl) minutesEl.innerText = String(minutes).padStart(2, '0');
                if (secondsEl) secondsEl.innerText = String(seconds).padStart(2, '0');

                return false;
            }

            const isFinished = updateCountdown();
            if (!isFinished) {
                const timer = setInterval(() => {
                    const finished = updateCountdown();
                    if (finished) {
                        clearInterval(timer);
                    }
                }, 1000);
            }
        })();

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
