<!DOCTYPE html>
<html lang="id">
@php
    $brideFirstNames = trim(($invitation->bride_name ?? '') . ' & ' . ($invitation->groom_name ?? ''), ' &');
    $eventDate = $invitation->event_date ? \Illuminate\Support\Carbon::parse($invitation->event_date) : null;
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
    </style>
</head>

<body class="bg-[#f8f5f2]">
    <section id="cover-section" class="fixed inset-0 z-50 h-screen w-full overflow-hidden">
        <div class="grid h-full grid-cols-1 lg:grid-cols-2">
            <div class="relative hidden items-center justify-center bg-[#f8f5f2] px-16 lg:flex">
                <div class="absolute left-0 top-10 opacity-[0.03]">
                    <h1 class="font-wedding text-[120px] leading-none text-black">
                        {{ $invitation->bride_name }}
                    </h1>
                    <h1 class="font-wedding mt-5 text-[120px] leading-none text-black">
                        {{ $invitation->groom_name }}
                    </h1>
                </div>

                <div class="relative z-10 max-w-xl">
                    <p class="text-sm uppercase tracking-[0.4em] text-[#8a7a7a]">Wedding Invitation</p>

                    <h1 class="font-wedding mt-6 text-6xl leading-tight text-[#2e1b1b]">
                        {{ $invitation->bride_name }}
                        <span class="mx-2 text-[#7c1111]">&</span>
                        {{ $invitation->groom_name }}
                    </h1>

                    <div class="mt-10 h-[1px] w-32 bg-[#c8baba]"></div>

                    <p class="mt-10 text-base leading-8 text-[#5e4d4d]">
                        Dengan penuh rasa syukur dan bahagia, kami mengundang Anda untuk hadir dalam acara
                        pernikahan kami.
                    </p>

                    <div class="mt-10">
                        <p class="text-sm text-[#7a6a6a]">Lokasi Acara</p>
                        <h2 class="mt-2 text-2xl font-semibold text-[#2e1b1b]">{{ $invitation->venue_name }}</h2>
                        <p class="mt-3 max-w-md text-sm leading-7 text-[#6f5f5f]">{{ $invitation->venue_address }}</p>
                    </div>
                </div>
            </div>

            <div class="relative flex h-screen items-center justify-center overflow-hidden">
                <img src="{{ asset('storage/' . $invitation->cover_photo) }}" alt="Wedding Cover"
                    class="absolute inset-0 h-full w-full object-cover">
                <div class="absolute inset-0 bg-black/45"></div>
                <div class="absolute inset-0 bg-gradient-to-b from-black/20 via-black/10 to-black/50"></div>

                <div class="relative z-10 flex w-full items-center justify-center px-8">
                    <div class="w-full max-w-sm text-center text-white">
                        <p class="text-xs font-medium uppercase tracking-[0.3em]">The Wedding Of</p>

                        <h1 class="font-wedding mt-8 text-4xl leading-tight md:text-5xl">
                            {{ $invitation->bride_name }}
                            <span class="block py-2 text-2xl">&</span>
                            {{ $invitation->groom_name }}
                        </h1>

                        <p class="mt-8 text-xl font-semibold tracking-wide">
                            {{ $eventDate?->format('d.m.Y') ?? '-' }}
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

    @if ($invitation->music_url)
        <audio id="bgMusic" loop preload="auto" playsinline style="display:none">
            <source src="{{ $invitation->music_signed_url ?? asset('storage/' . $invitation->music_url) }}" type="audio/mpeg">
        </audio>
    @endif

    <main class="relative z-0 min-h-screen bg-white">
        <section id="opening-section" class="relative min-h-screen w-full overflow-hidden bg-[#f8f5f2]">
            <div class="grid min-h-screen grid-cols-1 lg:grid-cols-2">
                <div class="relative hidden items-start justify-start bg-[#f8f5f2] px-16 py-20 lg:flex">
                    <div class="absolute left-12 top-16 opacity-[0.035]">
                        <h2 class="font-wedding text-4xl leading-[2.5] text-[#2e1b1b]">{{ $invitation->bride_name }} &
                        </h2>
                        <h2 class="font-wedding text-4xl leading-[2.5] text-[#2e1b1b]">{{ $invitation->groom_name }}
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
                    <img src="{{ asset('storage/' . $invitation->cover_photo) }}"
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
                        <h2 class="font-wedding text-4xl leading-[2.5] text-[#2e1b1b]">{{ $invitation->bride_name }} &
                        </h2>
                        <h2 class="font-wedding text-4xl leading-[2.5] text-[#2e1b1b]">{{ $invitation->groom_name }}
                        </h2>
                    </div>
                </div>

                <div
                    class="relative flex min-h-screen items-center justify-center overflow-hidden bg-[#fbf8f6] px-6 py-16">
                    <div class="absolute inset-0 opacity-[0.08]">
                        <img src="{{ asset('storage/' . $invitation->cover_photo) }}" alt="Background Couple"
                            class="h-full w-full object-cover blur-sm">
                    </div>
                    <div class="absolute inset-0 bg-white/85"></div>

                    <div class="relative z-10 w-full max-w-md text-center text-[#2e1b1b]">
                        <p class="mb-12 text-sm leading-7 text-[#4d3c3c]">
                            Kami mohon doa & restunya atas pernikahan kami
                        </p>

                        <div class="flex flex-col items-center">
                            <div class="h-32 w-32 rounded-full border-[7px] border-[#7b0f0f] p-1 shadow-2xl">
                                <img src="{{ asset('storage/' . $invitation->bride_photo) }}"
                                    alt="{{ $invitation->bride_name }}"
                                    class="h-full w-full rounded-full object-cover">
                            </div>

                            <h2 class="font-wedding mt-8 text-4xl leading-tight text-[#2e1b1b]">
                                {{ $invitation->bride_name }}
                            </h2>

                            <p class="mt-3 text-sm leading-6 text-[#4d3c3c]">{{ $invitation->bride_parent_name }}</p>

                            @if ($invitation->bride_instagram)
                                <a href="{{ $invitation->bride_instagram }}" target="_blank"
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
                                <img src="{{ asset('storage/' . $invitation->groom_photo) }}"
                                    alt="{{ $invitation->groom_name }}"
                                    class="h-full w-full rounded-full object-cover">
                            </div>

                            <h2 class="font-wedding mt-8 text-4xl leading-tight text-[#2e1b1b]">
                                {{ $invitation->groom_name }}
                            </h2>

                            <p class="mt-3 text-sm leading-6 text-[#4d3c3c]">{{ $invitation->groom_parent_name }}</p>

                            @if ($invitation->groom_instagram)
                                <a href="{{ $invitation->groom_instagram }}" target="_blank"
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
                        <img src="{{ asset('storage/' . $invitation->cover_photo) }}" alt="Background Events"
                            class="h-full w-full object-cover blur-sm">
                    </div>
                    <div class="absolute inset-0 bg-white/85"></div>

                    <div class="relative z-10 w-full max-w-xl text-center text-[#2e1b1b]">
                        <p class="mb-12 text-sm leading-7 text-[#4d3c3c]">
                            Dengan hormat, kami mengundang Bapak/Ibu/Saudara/i untuk hadir dalam rangkaian acara kami.
                        </p>

                        {{-- Countdown Timer --}}
                        @if ($eventDate)
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
                        @forelse ($invitation->invitationEvents ?? [] as $event)
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
                        @if ($invitation->google_maps_url)
                            <div class="mt-12">
                                <a href="{{ $invitation->google_maps_url }}" target="_blank"
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
                        @if ($eventDate && $invitation->event_time)
                            <div class="{{ $invitation->google_maps_url ? 'mt-6' : 'mt-12' }}">
                                <a href="#"
                                    onclick="addToCalendar('{{ $invitation->title }}', '{{ $invitation->venue_name }}', '{{ $eventDate->format('Ymd') }}T{{ \Carbon\Carbon::parse($invitation->event_time)->format('His') }}', '{{ $eventDate->addHours(2)->format('Ymd') }}T{{ \Carbon\Carbon::parse($invitation->event_time)->addHours(2)->format('His') }}')"
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
        const eventDateStr = "{{ $invitation->event_date }}";
        const eventTimeStr = "{{ $invitation->event_time }}";

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
    </script>
</body>

</html>
