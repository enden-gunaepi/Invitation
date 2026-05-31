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

    <main class="relative z-0 min-h-screen bg-white">
        <section id="opening-section" class="relative min-h-screen w-full overflow-hidden bg-[#f8f5f2]">
            <div class="grid min-h-screen grid-cols-1 lg:grid-cols-2">
                <div class="relative hidden items-start justify-start bg-[#f8f5f2] px-16 py-20 lg:flex">
                    <div class="absolute left-12 top-16 opacity-[0.035]">
                        <h2 class="font-wedding text-4xl leading-[2.5] text-[#2e1b1b]">{{ $invitation->bride_name }} &</h2>
                        <h2 class="font-wedding text-4xl leading-[2.5] text-[#2e1b1b]">{{ $invitation->groom_name }}</h2>
                    </div>

                    <a href="javascript:history.back()"
                        class="relative z-10 inline-flex items-center gap-2 rounded-full bg-[#7b0f0f] px-4 py-2 text-sm font-semibold text-white shadow-md transition hover:bg-[#5f0b0b]">
                        <span class="flex h-5 w-5 items-center justify-center rounded-full bg-white text-[#7b0f0f]">
                            ‹
                        </span>
                        Demo
                    </a>
                </div>

                <div class="relative flex min-h-screen items-center justify-center overflow-hidden">
                    <img src="{{ asset('storage/' . $invitation->cover_photo) }}"
                        alt="Opening Wedding {{ $brideFirstNames }}" class="absolute inset-0 h-full w-full object-cover">
                    <div class="absolute inset-0 bg-black/50"></div>
                    <div class="absolute inset-0 bg-gradient-to-b from-black/25 via-black/10 to-black/60"></div>

                    <div class="relative z-10 w-full px-8 text-center text-white">
                        <div class="mx-auto max-w-sm">
                            <p class="font-wedding text-sm font-semibold uppercase tracking-wide">We Invite You</p>
                            <p class="font-wedding mt-4 text-sm font-semibold uppercase tracking-wide">
                                To Celebrate Our Wedding
                            </p>

                            <h1 class="font-wedding mt-10 text-4xl leading-tight">{{ $brideFirstNames }}</h1>

                            <div
                                class="mt-10 flex items-center justify-center gap-4 font-wedding text-sm font-semibold uppercase">
                                <span>{{ $eventDate?->translatedFormat('l') ?? '-' }}</span>
                                <span class="h-5 w-[1px] bg-[#8b1111]"></span>
                                <span>{{ $eventDate?->translatedFormat('d F Y') ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="quote-section" class="relative min-h-screen w-full overflow-hidden bg-[#f8f5f2]">
            <div class="grid min-h-screen grid-cols-1 lg:grid-cols-2">
                <div class="relative hidden items-center justify-center bg-[#f8f5f2] px-16 lg:flex">
                    <div class="absolute left-12 top-16 opacity-[0.035]">
                        <h2 class="font-wedding text-4xl leading-[2.5] text-[#2e1b1b]">{{ $invitation->bride_name }} &</h2>
                        <h2 class="font-wedding text-4xl leading-[2.5] text-[#2e1b1b]">{{ $invitation->groom_name }}</h2>
                    </div>
                </div>

                <div class="relative flex min-h-screen items-center justify-center overflow-hidden px-6 py-16">
                    <img src="{{ asset('storage/' . $invitation->cover_photo) }}" alt="Quote Wedding"
                        class="absolute inset-0 h-full w-full object-cover">
                    <div class="absolute inset-0 bg-black/45"></div>

                    <div
                        class="relative z-10 w-full max-w-xl rounded-2xl bg-black/45 px-7 py-10 text-center text-white shadow-2xl backdrop-blur-md">
                        <div
                            class="mx-auto mb-7 flex h-20 w-20 items-center justify-center rounded-full border-4 border-white">
                            <div class="flex h-16 w-16 items-center justify-center rounded-full border border-white/70">
                                <span class="font-wedding text-2xl">
                                    {{ strtoupper(substr((string) $invitation->bride_name, 0, 1)) }}{{ strtoupper(substr((string) $invitation->groom_name, 0, 1)) }}
                                </span>
                            </div>
                        </div>

                        <p class="text-sm font-semibold italic leading-8 md:text-base md:leading-9">
                            “Dan diantara tanda-tanda kebesaran-Nya ialah diciptakan-Nya untukmu pasangan hidup dari
                            jenismu sendiri supaya kamu mendapatkan ketenangan hati dan dijadikan-Nya kasih sayang
                            diantara kamu sesungguhnya yang demikian menjadi tanda-tanda kebesaran-Nya bagi orang-orang
                            yang berpikir”
                        </p>

                        <p class="mt-6 text-sm font-bold italic md:text-base">Surah Ar - Ruum : 21</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="couple-section" class="relative min-h-screen w-full overflow-hidden bg-[#f8f5f2]">
            <div class="grid min-h-screen grid-cols-1 lg:grid-cols-2">
                <div class="relative hidden items-center justify-center bg-[#f8f5f2] px-16 lg:flex">
                    <div class="absolute left-12 top-16 opacity-[0.035]">
                        <h2 class="font-wedding text-4xl leading-[2.5] text-[#2e1b1b]">{{ $invitation->bride_name }} &</h2>
                        <h2 class="font-wedding text-4xl leading-[2.5] text-[#2e1b1b]">{{ $invitation->groom_name }}</h2>
                    </div>
                </div>

                <div class="relative flex min-h-screen items-center justify-center overflow-hidden bg-[#fbf8f6] px-6 py-16">
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
                                    alt="{{ $invitation->bride_name }}" class="h-full w-full rounded-full object-cover">
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
                                    alt="{{ $invitation->groom_name }}" class="h-full w-full rounded-full object-cover">
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
        }

        document.body.style.overflow = 'hidden';

        const musicToggle = document.getElementById('music-toggle');
        const scrollToggle = document.getElementById('scroll-toggle');
        let autoScrollTimer = null;

        if (musicToggle) {
            musicToggle.addEventListener('click', function() {
                this.classList.toggle('is-active');
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
    </script>
</body>

</html>
