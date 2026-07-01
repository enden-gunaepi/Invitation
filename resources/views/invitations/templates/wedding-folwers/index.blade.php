<!DOCTYPE html>
<html lang="id">
@php
    $brideName = trim((string) ($invitation->bride_name ?? 'Yuli'));
    $groomName = trim((string) ($invitation->groom_name ?? 'Enden'));
    $coupleNames = trim($brideName . ' & ' . $groomName, ' &');
    $guestName = $guest->name ?? ($guestName ?? 'Tamu Undangan');
    $eventDate = $invitation->event_date ? \Illuminate\Support\Carbon::parse($invitation->event_date) : null;
    $eventTime = $invitation->event_time
        ? \Illuminate\Support\Carbon::parse($invitation->event_time)->format('H:i')
        : null;
    $eventDateTimeIso = $eventDate
        ? $eventDate->format('Y-m-d') .
            'T' .
            ($invitation->event_time
                ? \Illuminate\Support\Carbon::parse($invitation->event_time)->format('H:i:s')
                : '00:00:00')
        : null;
    $assetBase = asset('assets_invitation/flowertheme');
    $coverPhoto = $invitation->cover_photo ? asset('storage/' . $invitation->cover_photo) : null;
    $bridePhoto = $invitation->bride_photo ? asset('storage/' . $invitation->bride_photo) : $coverPhoto;
    $groomPhoto = $invitation->groom_photo ? asset('storage/' . $invitation->groom_photo) : $coverPhoto;
    $events = $invitation->events ?? collect();
    $wishes = $invitation->wishes ?? collect();
    $rsvps = $invitation->rsvps ?? collect();
    $rsvpWishItems = $wishes
        ->map(
            fn($wish) => [
                'name' => $wish->name,
                'message' => $wish->message,
                'created_at' => $wish->created_at,
            ],
        )
        ->concat(
            $rsvps->filter(fn($rsvp) => trim((string) ($rsvp->message ?? '')) !== '')->map(
                fn($rsvp) => [
                    'name' => $rsvp->name,
                    'message' => $rsvp->message,
                    'created_at' => $rsvp->created_at,
                ],
            ),
        )
        ->sortByDesc('created_at')
        ->values();
    $galleryItems = ($invitation->photos ?? collect())
        ->map(
            fn($photo) => [
                'url' => asset('storage/' . $photo->file_path),
                'caption' => $photo->caption ?: 'momen bahagia kami',
            ],
        )
        ->values();

    if ($galleryItems->isEmpty() && $coverPhoto) {
        $galleryItems->push([
            'url' => $coverPhoto,
            'caption' => 'momen bahagia kami',
        ]);
    }

    $giftAccounts = $invitation->bankAccounts ?? collect();

    if (
        $giftAccounts->isEmpty() &&
        ($invitation->bank_name || $invitation->bank_account_number || $invitation->bank_account_name)
    ) {
        $giftAccounts = collect([
            (object) [
                'bank_name' => $invitation->bank_name,
                'account_number' => $invitation->bank_account_number,
                'account_name' => $invitation->bank_account_name,
            ],
        ]);
    }

    $giftAccounts = $giftAccounts
        ->filter(fn($account) => $account->bank_name || $account->account_number || $account->account_name)
        ->values();
    $giftAddress = trim((string) ($invitation->gift_address ?? ''));
@endphp

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="description" content="The Wedding of {{ $coupleNames }}">
    <title>The Wedding of {{ $coupleNames }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Poppins:wght@400;500;600;700&family=Nunito+Sans:wght@300;400;600;700&display=swap"
        rel="stylesheet">

    <style>
        html {
            scroll-behavior: smooth;
        }

        body.cover-lock {
            height: 100vh;
            overflow: hidden;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .flower-layer {
            transform:
                translate3d(calc(var(--start-x, 0px) + var(--move-x, 0px)), calc(var(--start-y, 0px) + var(--move-y, 0px)), 0) scale(var(--scale, 1)) rotate(var(--r, 0deg));
            animation:
                flowerEnter 1.15s cubic-bezier(.19, 1, .22, 1) var(--delay, 0s) forwards,
                flowerFloat var(--float, 6s) ease-in-out calc(var(--delay, 0s) + 1.05s) infinite alternate;
            will-change: transform, opacity;
        }

        @keyframes flowerEnter {
            from {
                opacity: 0;
                transform:
                    translate3d(calc(var(--start-x, 0px) + var(--move-x, 0px)), calc(var(--start-y, 0px) + 34px + var(--move-y, 0px)), 0) scale(calc(var(--scale, 1) * .96)) rotate(var(--r, 0deg));
            }

            to {
                opacity: 1;
                transform:
                    translate3d(calc(var(--start-x, 0px) + var(--move-x, 0px)), calc(var(--start-y, 0px) + var(--move-y, 0px)), 0) scale(var(--scale, 1)) rotate(var(--r, 0deg));
            }
        }

        @keyframes flowerFloat {
            from {
                transform:
                    translate3d(calc(var(--start-x, 0px) + var(--move-x, 0px)), calc(var(--start-y, 0px) + var(--move-y, 0px)), 0) scale(var(--scale, 1)) rotate(var(--r, 0deg));
            }

            to {
                transform:
                    translate3d(calc(var(--start-x, 0px) + var(--move-x, 0px)), calc(var(--start-y, 0px) - 12px + var(--move-y, 0px)), 0) scale(var(--scale, 1)) rotate(calc(var(--r, 0deg) + 1.2deg));
            }
        }

        @keyframes softDrift {
            from {
                transform: translate3d(0, 0, 0) rotate(-2deg);
            }

            to {
                transform: translate3d(10px, -14px, 0) rotate(1deg);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            html {
                scroll-behavior: auto;
            }

            .flower-layer {
                animation: none;
                opacity: 1;
                transform: translate3d(var(--start-x, 0px), var(--start-y, 0px), 0) scale(var(--scale, 1)) rotate(var(--r, 0deg));
            }
        }
    </style>
</head>

<body
    class="cover-lock min-h-screen overflow-x-hidden bg-[#dfe6dc] font-['Nunito_Sans',Arial,sans-serif] text-[#24452f]"
    style="background-image: radial-gradient(circle at 18% 15%, rgba(195, 212, 198, 0.28), transparent 28%), radial-gradient(circle at 82% 88%, rgba(132, 167, 151, 0.22), transparent 30%);">
    <section
        class="fixed inset-0 z-50 flex justify-center bg-[rgba(218,226,216,.72)] transition-[opacity,visibility] duration-[850ms] ease-in-out motion-reduce:transition-none"
        id="coverSection" aria-label="Cover undangan">
        <div class="relative isolate min-h-dvh w-full max-w-[440px] overflow-hidden bg-[#f8faf5] min-[720px]:border-x min-[720px]:border-white/50"
            id="coverCanvas">
            <div class="absolute inset-0 z-0 scale-[1.035] bg-cover bg-center"
                style="background-image: url('{{ $assetBase }}/bg1.png');" aria-hidden="true"></div>

            <img class="flower-layer absolute left-1/2 top-7 z-[2] ml-[calc(var(--w)/-2)] w-[var(--w)] max-w-none pointer-events-none opacity-0 drop-shadow-[0_14px_22px_rgba(29,61,45,.08)] [--depth:10px] [--float:7.2s] [--start-y:-42px] [--w:min(100vw,430px)] [@media(max-height:760px)]:top-2.5 [@media(max-height:760px)]:[--w:min(92vw,395px)]"
                src="{{ $assetBase }}/bunga_atas.png" alt="" data-depth="10">
            <img class="flower-layer absolute bottom-[170px] right-[-82px] z-[2] w-[var(--w)] max-w-none pointer-events-none opacity-0 drop-shadow-[0_14px_22px_rgba(29,61,45,.08)] [--delay:.28s] [--depth:24px] [--float:5.9s] [--start-x:54px] [--start-y:18px] [--w:min(56vw,240px)] [@media(max-height:760px)]:bottom-[138px]"
                src="{{ $assetBase }}/bunga_setangkai.png" alt="" data-depth="24">
            <img class="flower-layer absolute bottom-[-32px] left-[-98px] z-[3] w-[var(--w)] max-w-none pointer-events-none opacity-0 drop-shadow-[0_14px_22px_rgba(29,61,45,.08)] [--delay:.4s] [--depth:14px] [--float:7.4s] [--start-x:-62px] [--start-y:56px] [--w:min(70vw,300px)] [@media(max-height:760px)]:bottom-[-54px]"
                src="{{ $assetBase }}/bung_pojok.png" alt="" data-depth="14">
            <img class="flower-layer absolute bottom-[170px] left-[-72px] z-[2] w-[var(--w)] max-w-none pointer-events-none opacity-0 drop-shadow-[0_14px_22px_rgba(29,61,45,.08)] [--delay:.16s] [--depth:18px] [--float:6.4s] [--start-x:-48px] [--start-y:14px] [--w:min(54vw,230px)] [@media(max-height:760px)]:bottom-[138px]"
                src="{{ $assetBase }}/bungaaksen1.png" alt="" data-depth="18">
            <img class="flower-layer absolute bottom-[-30px] right-[-104px] z-[5] w-[var(--w)] max-w-none pointer-events-none opacity-0 drop-shadow-[0_14px_22px_rgba(29,61,45,.08)] [--delay:.62s] [--depth:28px] [--float:5.7s] [--start-x:72px] [--start-y:58px] [--w:min(72vw,310px)] [@media(max-height:760px)]:bottom-[-54px]"
                src="{{ $assetBase }}/bung_pojok2.png" alt="" data-depth="28">
            <img class="flower-layer absolute bottom-1.5 left-[-52px] z-[6] w-[var(--w)] max-w-none pointer-events-none opacity-0 drop-shadow-[0_14px_22px_rgba(29,61,45,.08)] [--delay:.72s] [--depth:9px] [--float:7.8s] [--start-x:-26px] [--start-y:18px] [--w:min(45vw,192px)] [@media(max-height:760px)]:bottom-[-4px] [@media(max-height:760px)]:[--w:min(42vw,176px)]"
                src="{{ $assetBase }}/bungaaksen1.png" alt="" data-depth="9">
            <img class="flower-layer absolute bottom-[-42px] left-1/2 z-[4] ml-[calc(var(--w)/-2)] w-[var(--w)] max-w-none pointer-events-none opacity-0 drop-shadow-[0_14px_22px_rgba(29,61,45,.08)] [--delay:.5s] [--depth:20px] [--float:6.2s] [--start-y:64px] [--w:min(64vw,275px)] [@media(max-height:760px)]:bottom-[-54px]"
                src="{{ $assetBase }}/bunga_tengah.png" alt="" data-depth="20">
            <img class="flower-layer absolute bottom-1 right-[-58px] z-[6] w-[var(--w)] max-w-none pointer-events-none opacity-0 drop-shadow-[0_14px_22px_rgba(29,61,45,.08)] [--delay:.78s] [--depth:11px] [--float:7.1s] [--start-x:28px] [--start-y:16px] [--w:min(47vw,202px)] [@media(max-height:760px)]:bottom-[-4px] [@media(max-height:760px)]:[--w:min(42vw,176px)]"
                src="{{ $assetBase }}/bunga_setangkai.png" alt="" data-depth="11">

            <div
                class="relative z-[7] flex min-h-dvh flex-col items-center justify-center px-[34px] pb-[max(112px,env(safe-area-inset-bottom))] pt-[max(76px,env(safe-area-inset-top))] text-center">
                <p
                    class="mb-[50px] mt-[17vh] font-['Poppins',sans-serif] text-[clamp(13px,3.5vw,16px)] font-bold uppercase tracking-[.02em] text-[#587276] [@media(max-height:760px)]:mb-[34px] [@media(max-height:760px)]:mt-[14vh]">
                    The Wedding of
                </p>
                <h1
                    class="m-0 w-full font-['Great_Vibes',cursive] text-[clamp(46px,15vw,72px)] font-normal leading-[.92] text-[#257046] [text-shadow:0_2px_0_rgba(255,255,255,.62)] [@media(max-height:760px)]:text-[clamp(42px,13vw,64px)]">
                    <span class="block w-full max-w-full whitespace-nowrap"
                        data-fit-name>{{ $brideName ?: 'Yuli' }}</span>
                    <span
                        class="mb-[22px] mt-5 block font-['Great_Vibes',cursive] text-[clamp(28px,8vw,42px)] leading-none text-[#2d7a4e]">and</span>
                    <span class="block w-full max-w-full whitespace-nowrap"
                        data-fit-name>{{ $groomName ?: 'Enden' }}</span>
                </h1>

                <div class="mx-auto mt-6 max-w-[280px] text-[13px] leading-normal text-[#24452f]/85">
                    Kepada Yth.
                    <strong
                        class="block font-['Poppins',sans-serif] text-[15px] text-[#24452f]">{{ $guestName }}</strong>
                </div>

                <button type="button"
                    class="relative z-[8] mt-[22px] inline-flex min-h-[46px] min-w-[154px] cursor-pointer items-center justify-center rounded-[14px] border-0 bg-[#577477] font-['Poppins',sans-serif] text-sm font-bold text-white shadow-[0_14px_30px_rgba(61,93,96,.24)] transition duration-200 hover:-translate-y-0.5 hover:bg-[#405f62] hover:shadow-[0_16px_34px_rgba(61,93,96,.30)] focus-visible:-translate-y-0.5 focus-visible:bg-[#405f62] focus-visible:outline-none active:translate-y-px motion-reduce:transition-none [@media(max-height:760px)]:min-h-[42px] [@media(max-height:760px)]:min-w-[144px] [@media(max-height:760px)]:rounded-[13px] [@media(max-height:760px)]:text-[13px]"
                    id="openInvitationButton">
                    Open Invitation
                </button>
            </div>
        </div>
    </section>

    <main
        class="relative z-[1] mx-auto min-h-screen w-full max-w-[440px] overflow-hidden bg-[#f8faf5] bg-cover bg-fixed bg-center shadow-[0_0_42px_rgba(28,62,46,.18)] min-[720px]:border-x min-[720px]:border-white/50"
        id="mainContent" style="background-image: url('{{ $assetBase }}/bg1.png');">
        <section
            class="relative flex min-h-[100svh] items-center justify-center overflow-hidden px-[clamp(16px,5vw,28px)] py-[clamp(36px,7vh,72px)] text-center"
            id="opening">
            <div class="pointer-events-none absolute inset-[18px] border border-[rgba(67,105,78,.13)]"></div>
            <img class="absolute right-[-58px] top-[-74px] w-[clamp(170px,58vw,250px)] pointer-events-none opacity-[.82] animate-[softDrift_7s_ease-in-out_infinite_alternate] motion-reduce:animate-none"
                src="{{ $assetBase }}/bung_pojok2.png" alt="">
            <img class="absolute bottom-[-10px] left-[-18px] w-[clamp(170px,58vw,250px)] pointer-events-none opacity-[.82] animate-[softDrift_8.5s_ease-in-out_infinite_alternate] motion-reduce:animate-none"
                src="{{ $assetBase }}/bunga_pojokkiribawah.png" alt="">
            <div class="relative z-[2] mx-auto w-full max-w-[360px]">
                <p
                    class="mb-[clamp(10px,3vh,18px)] font-['Poppins',sans-serif] text-[11px] font-bold uppercase tracking-[.16em] text-[#587276] min-[390px]:text-xs min-[390px]:tracking-[.18em]">
                    Wedding Invitation
                </p>
                <h2
                    class="mx-auto w-full max-w-full text-center font-['Great_Vibes',cursive] font-normal leading-none text-[#287047]">
                    <span
                        class="block w-full max-w-full whitespace-nowrap text-[64px] min-[390px]:text-[68px] min-[720px]:text-[68px]"
                        data-fit-name data-min-size="24">{{ $brideName ?: 'Yuli' }}</span>
                    <span
                        class="my-[clamp(4px,1.8vh,8px)] block font-['Great_Vibes',cursive] text-[clamp(22px,7vw,34px)] leading-none text-[#2d7a4e]">and</span>
                    <span
                        class="block w-full max-w-full whitespace-nowrap text-[64px] min-[390px]:text-[68px] min-[720px]:text-[68px]"
                        data-fit-name data-min-size="24">{{ $groomName ?: 'Enden' }}</span>
                </h2>
                <p
                    class="mt-[clamp(16px,4vh,26px)] text-[clamp(13px,3.6vw,15px)] leading-[1.75] text-[#49665c] min-[390px]:leading-[1.9]">
                    {{ $invitation->opening_text ?: 'Dengan penuh rasa syukur dan bahagia, kami mengundang Bapak/Ibu/Saudara/i untuk hadir dan memberikan doa restu pada hari pernikahan kami.' }}
                </p>

                <div
                    class="mx-auto mt-[clamp(18px,4vh,28px)] w-full max-w-[320px] overflow-hidden rounded-[18px] border border-[rgba(76,108,87,.22)] bg-white/60 px-3 py-4 shadow-[0_18px_46px_rgba(61,88,68,.11)] backdrop-blur-[10px] min-[390px]:max-w-[340px] min-[390px]:px-4 min-[390px]:py-5">
                    <div class="font-['Great_Vibes',cursive] text-[clamp(32px,10vw,40px)] leading-none text-[#287047]">
                        {{ $eventDate?->translatedFormat('l') ?? 'Hari Bahagia' }}
                    </div>
                    <div
                        class="mt-2 font-['Poppins',sans-serif] text-[13px] font-bold text-[#24452f] min-[390px]:text-sm">
                        {{ $eventDate?->translatedFormat('d F Y') ?? '-' }}
                    </div>
                    @if ($eventTime)
                        <p class="mt-2 text-[13px] leading-relaxed text-[#526f64]">Pukul {{ $eventTime }} WIB</p>
                    @endif

                    <div class="mt-5 border-t border-[rgba(76,108,87,.16)] pt-4"
                        data-countdown="{{ $eventDateTimeIso }}">
                        <p
                            class="font-['Poppins',sans-serif] text-[10px] font-bold uppercase tracking-[.24em] text-[#6b8580]">
                            Menuju Hari Bahagia
                        </p>
                        <div
                            class="mx-auto mt-3 grid max-w-[250px] grid-cols-2 gap-2 min-[380px]:max-w-none min-[380px]:grid-cols-4">
                            <div
                                class="rounded-xl bg-[#eef4ec]/90 px-1.5 py-2.5 shadow-[inset_0_0_0_1px_rgba(76,108,87,.12)] min-[390px]:px-2 min-[390px]:py-3">
                                <strong
                                    class="block font-['Poppins',sans-serif] text-[clamp(16px,5vw,20px)] font-bold leading-none text-[#287047]"
                                    data-countdown-days>--</strong>
                                <span
                                    class="mt-1 block text-[9px] font-semibold uppercase tracking-[.06em] text-[#587276] min-[390px]:text-[10px] min-[390px]:tracking-[.08em]">Hari</span>
                            </div>
                            <div
                                class="rounded-xl bg-[#eef4ec]/90 px-1.5 py-2.5 shadow-[inset_0_0_0_1px_rgba(76,108,87,.12)] min-[390px]:px-2 min-[390px]:py-3">
                                <strong
                                    class="block font-['Poppins',sans-serif] text-[clamp(16px,5vw,20px)] font-bold leading-none text-[#287047]"
                                    data-countdown-hours>--</strong>
                                <span
                                    class="mt-1 block text-[9px] font-semibold uppercase tracking-[.06em] text-[#587276] min-[390px]:text-[10px] min-[390px]:tracking-[.08em]">Jam</span>
                            </div>
                            <div
                                class="rounded-xl bg-[#eef4ec]/90 px-1.5 py-2.5 shadow-[inset_0_0_0_1px_rgba(76,108,87,.12)] min-[390px]:px-2 min-[390px]:py-3">
                                <strong
                                    class="block font-['Poppins',sans-serif] text-[clamp(16px,5vw,20px)] font-bold leading-none text-[#287047]"
                                    data-countdown-minutes>--</strong>
                                <span
                                    class="mt-1 block text-[9px] font-semibold uppercase tracking-[.06em] text-[#587276] min-[390px]:text-[10px] min-[390px]:tracking-[.08em]">Menit</span>
                            </div>
                            <div
                                class="rounded-xl bg-[#eef4ec]/90 px-1.5 py-2.5 shadow-[inset_0_0_0_1px_rgba(76,108,87,.12)] min-[390px]:px-2 min-[390px]:py-3">
                                <strong
                                    class="block font-['Poppins',sans-serif] text-[clamp(16px,5vw,20px)] font-bold leading-none text-[#287047]"
                                    data-countdown-seconds>--</strong>
                                <span
                                    class="mt-1 block text-[9px] font-semibold uppercase tracking-[.06em] text-[#587276] min-[390px]:text-[10px] min-[390px]:tracking-[.08em]">Detik</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section
            class="relative flex min-h-screen items-center justify-center overflow-hidden px-4 pb-28 pt-20 text-center"
            id="bride">
            <div class="pointer-events-none absolute inset-[18px] border border-[rgba(67,105,78,.13)]"></div>
            <img class="absolute -left-24 top-8 w-52 pointer-events-none opacity-25 animate-[softDrift_8s_ease-in-out_infinite_alternate] motion-reduce:animate-none"
                src="{{ $assetBase }}/bungaaksen2.png" alt="">
            <img class="absolute -right-24 top-20 w-60 pointer-events-none opacity-30 animate-[softDrift_7s_ease-in-out_infinite_alternate] motion-reduce:animate-none"
                src="{{ $assetBase }}/bunga_lingkar.png" alt="">
            <img class="absolute bottom-[-48px] left-1/2 z-[1] w-[520px] max-w-none -translate-x-1/2 pointer-events-none opacity-95"
                src="{{ $assetBase }}/bunga_bawah.png" alt="">

            <div class="relative z-[2] w-full max-w-[410px]">
                <div class="relative mx-auto aspect-square w-[350px] max-w-[calc(100vw-48px)]">
                    <div
                        class="absolute left-1/2 top-[11.5%] z-[1] h-[77%] w-[48%] -translate-x-1/2 overflow-hidden rounded-full border border-white/75 bg-[#e6eee6] shadow-[0_22px_45px_rgba(57,86,69,.18)]">
                        @if ($bridePhoto)
                            <img src="{{ $bridePhoto }}" alt="{{ $brideName ?: 'Pengantin perempuan' }}"
                                class="h-full w-full object-cover">
                        @else
                            <div
                                class="flex h-full w-full items-center justify-center px-8 font-['Poppins',sans-serif] text-xs font-bold uppercase tracking-[.18em] text-[#6c8580]">
                                Bride Photo
                            </div>
                        @endif
                    </div>
                    <img class="absolute inset-0 z-[2] h-full w-full object-contain pointer-events-none"
                        src="{{ $assetBase }}/bunga_pengantin.png" alt="">
                </div>

                <div class="relative z-[3] -mt-4">
                    <p
                        class="font-['Poppins',sans-serif] text-[13px] font-bold uppercase tracking-[.08em] text-[#587276]">
                        Bride
                    </p>
                    <h2 class="mx-auto mt-3 block w-full max-w-full whitespace-nowrap font-['Great_Vibes',cursive] text-[88px] font-normal leading-none text-[#287047] min-[390px]:text-[96px] min-[720px]:text-[96px]"
                        data-fit-name data-min-size="42">
                        {{ $brideName ?: 'Mempelai Wanita' }}
                    </h2>
                    <p class="mx-auto mt-3 max-w-[310px] text-[15px] font-semibold leading-relaxed text-[#587276]">
                        {{ $invitation->bride_parent_name ?: 'Nama orang tua mempelai wanita' }}
                    </p>

                    @if ($invitation->bride_instagram)
                        <a href="{{ $invitation->bride_instagram }}" target="_blank" rel="noopener"
                            class="mx-auto mt-9 inline-flex min-h-[42px] min-w-[170px] items-center justify-center rounded-2xl bg-[#587477] px-8 font-['Poppins',sans-serif] text-base font-regular text-white shadow-[0_14px_30px_rgba(61,93,96,.22)] transition hover:-translate-y-0.5 hover:bg-[#405f62]">
                            Instagram
                        </a>
                    @endif
                </div>
            </div>
        </section>

        <section
            class="relative flex min-h-screen items-center justify-center overflow-hidden px-4 pb-28 pt-20 text-center"
            id="groom">
            <div class="pointer-events-none absolute inset-[18px] border border-[rgba(67,105,78,.13)]"></div>
            <img class="absolute -left-24 top-8 w-52 pointer-events-none opacity-25 animate-[softDrift_8s_ease-in-out_infinite_alternate] motion-reduce:animate-none"
                src="{{ $assetBase }}/bungaaksen2.png" alt="">
            <img class="absolute -right-24 top-20 w-60 pointer-events-none opacity-30 animate-[softDrift_7s_ease-in-out_infinite_alternate] motion-reduce:animate-none"
                src="{{ $assetBase }}/bunga_lingkar.png" alt="">
            <img class="absolute bottom-[-48px] left-1/2 z-[1] w-[520px] max-w-none -translate-x-1/2 pointer-events-none opacity-95"
                src="{{ $assetBase }}/bunga_bawah.png" alt="">

            <div class="relative z-[2] w-full max-w-[410px]">
                <div class="relative mx-auto aspect-square w-[350px] max-w-[calc(100vw-48px)]">
                    <div
                        class="absolute left-1/2 top-[11.5%] z-[1] h-[77%] w-[48%] -translate-x-1/2 overflow-hidden rounded-full border border-white/75 bg-[#e6eee6] shadow-[0_22px_45px_rgba(57,86,69,.18)]">
                        @if ($groomPhoto)
                            <img src="{{ $groomPhoto }}" alt="{{ $groomName ?: 'Pengantin laki-laki' }}"
                                class="h-full w-full object-cover">
                        @else
                            <div
                                class="flex h-full w-full items-center justify-center px-8 font-['Poppins',sans-serif] text-xs font-bold uppercase tracking-[.18em] text-[#6c8580]">
                                Groom Photo
                            </div>
                        @endif
                    </div>
                    <img class="absolute inset-0 z-[2] h-full w-full object-contain pointer-events-none"
                        src="{{ $assetBase }}/bunga_pengantin.png" alt="">
                </div>

                <div class="relative z-[3] -mt-4">
                    <p
                        class="font-['Poppins',sans-serif] text-[13px] font-bold uppercase tracking-[.08em] text-[#587276]">
                        Groom
                    </p>
                    <h2 class="mx-auto mt-3 block w-full max-w-full whitespace-nowrap font-['Great_Vibes',cursive] text-[88px] font-normal leading-none text-[#287047] min-[390px]:text-[96px] min-[720px]:text-[96px]"
                        data-fit-name data-min-size="42">
                        {{ $groomName ?: 'Mempelai Pria' }}
                    </h2>
                    <p class="mx-auto mt-3 max-w-[310px] text-[15px] font-semibold leading-relaxed text-[#587276]">
                        {{ $invitation->groom_parent_name ?: 'Nama orang tua mempelai pria' }}
                    </p>

                    @if ($invitation->groom_instagram)
                        <a href="{{ $invitation->groom_instagram }}" target="_blank" rel="noopener"
                            class="mx-auto mt-9 inline-flex min-h-[42px] min-w-[170px] items-center justify-center rounded-2xl bg-[#587477] px-8 font-['Poppins',sans-serif] text-base font-regular text-white shadow-[0_14px_30px_rgba(61,93,96,.22)] transition hover:-translate-y-0.5 hover:bg-[#405f62]">
                            Instagram
                        </a>
                    @endif
                </div>
            </div>
        </section>

        <section class="relative min-h-screen overflow-hidden px-5 py-16 text-center" id="event">
            <div class="pointer-events-none absolute inset-[18px] border border-[rgba(67,105,78,.13)]"></div>
            <img class="absolute right-[-80px] top-8 z-[1] w-[210px] pointer-events-none opacity-90 animate-[softDrift_7s_ease-in-out_infinite_alternate] motion-reduce:animate-none"
                src="{{ $assetBase }}/bungaaksen2.png" alt="">
            <img class="absolute bottom-[-70px] left-[-80px] z-[3] w-[245px] pointer-events-none opacity-95 animate-[softDrift_8.5s_ease-in-out_infinite_alternate] motion-reduce:animate-none"
                src="{{ $assetBase }}/bungaaksen1.png" alt="">

            @php
                $displayEvents = $events->count()
                    ? $events
                    : collect([
                        (object) [
                            'event_name' => 'Acara',
                            'event_date' => $invitation->event_date,
                            'event_time' => $invitation->event_time,
                            'venue_name' => $invitation->venue_name,
                            'venue_address' => $invitation->venue_address,
                            'venue_maps_url' => $invitation->google_maps_url,
                        ],
                    ]);
            @endphp

            <div class="relative z-[2] mx-auto w-full max-w-[380px]">
                <h2 class="font-['Great_Vibes',cursive] text-[54px] font-normal leading-none text-[#287047]">
                    Acara
                </h2>
                <p
                    class="mx-auto mt-4 max-w-[310px] font-['Poppins',sans-serif] text-[13px] font-semibold leading-relaxed text-[#587276]">
                    Kami mengundang Anda untuk hadir dalam acara pernikahan kami yang akan diselenggarakan pada:
                </p>

                <div
                    class="relative z-[2] mx-auto mt-10 w-full rounded-t-[180px] rounded-b-[150px] bg-white px-6 pb-24 pt-28 shadow-[0_22px_54px_rgba(57,86,69,.10)] min-[390px]:px-8 min-[390px]:pt-32">
                    <div class="grid gap-20">
                        @foreach ($displayEvents as $event)
                            @php
                                $eventDateItem = $event->event_date
                                    ? \Illuminate\Support\Carbon::parse($event->event_date)
                                    : $eventDate;
                                $eventTimeItem = $event->event_time
                                    ? \Illuminate\Support\Carbon::parse($event->event_time)->format('H:i')
                                    : $eventTime;
                                $eventVenue = $event->venue_name ?: $invitation->venue_name;
                                $eventAddress = $event->venue_address ?: $invitation->venue_address;
                                $eventMapsUrl =
                                    $event->venue_maps_url ?:
                                    ($invitation->google_maps_url ?:
                                    'https://www.google.com/maps/search/?api=1&query=' .
                                        urlencode(trim((string) ($eventVenue . ' ' . $eventAddress))));
                            @endphp

                            <article>
                                <h3
                                    class="font-['Great_Vibes',cursive] text-[44px] font-normal leading-none text-[#287047]">
                                    {{ $event->event_name ?: 'Acara' }}
                                </h3>

                                <div
                                    class="mx-auto mt-9 grid max-w-[270px] grid-cols-[1fr_auto_1.15fr_auto_1fr] items-center text-[#587276]">
                                    <div class="font-['Poppins',sans-serif] text-[16px] font-bold">
                                        {{ $eventDateItem?->translatedFormat('F') ?? '-' }}
                                    </div>
                                    <div class="h-[72px] w-px bg-[#587276]/90"></div>
                                    <div class="font-['Poppins',sans-serif] text-[58px] font-bold leading-none">
                                        {{ $eventDateItem?->format('d') ?? '--' }}
                                    </div>
                                    <div class="h-[72px] w-px bg-[#587276]/90"></div>
                                    <div class="font-['Poppins',sans-serif] text-[16px] font-bold">
                                        {{ $eventDateItem?->format('Y') ?? '-' }}
                                    </div>
                                </div>

                                <p
                                    class="mt-7 font-['Poppins',sans-serif] text-[16px] font-semibold leading-snug text-[#587276]">
                                    {{ $eventTimeItem ? $eventTimeItem . ' WIB' : '-' }}
                                </p>
                                <p
                                    class="mt-2 font-['Poppins',sans-serif] text-[18px] font-bold leading-tight text-[#587276]">
                                    {{ $eventVenue ?: 'Lokasi Acara' }}
                                </p>
                                <p
                                    class="mx-auto mt-2 max-w-[260px] font-['Poppins',sans-serif] text-[14px] font-semibold leading-relaxed text-[#587276]">
                                    {{ $eventAddress ?: '-' }}
                                </p>

                                @if ($eventMapsUrl)
                                    <a href="{{ $eventMapsUrl }}" target="_blank" rel="noopener"
                                        class="mx-auto mt-9 inline-flex min-h-[42px] min-w-[178px] items-center justify-center gap-2.5 rounded-2xl bg-[#587477] px-5 font-['Poppins',sans-serif] text-[13px] font-bold text-white shadow-[0_14px_30px_rgba(61,93,96,.18)] transition hover:-translate-y-0.5 hover:bg-[#405f62]">
                                        <span class="text-[20px] leading-none">↗</span>
                                        Maps Navigation
                                    </a>
                                @endif
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        @if ($galleryItems->count())
            <section class="relative min-h-screen overflow-hidden px-5 py-12 text-center" id="gallery">
                <div class="pointer-events-none absolute inset-[18px] border border-[rgba(67,105,78,.13)]"></div>

                <div class="relative z-[2] mx-auto w-full max-w-[390px]">
                    <h2 class="font-['Great_Vibes',cursive] text-[58px] font-normal leading-none text-[#287047]">
                        Galery
                    </h2>
                    <p
                        class="mx-auto mt-4 max-w-[330px] font-['Poppins',sans-serif] text-[14px] font-semibold leading-relaxed text-[#587276]">
                        Setiap potret menceritakan kisah cinta kami yang abadi
                    </p>

                    <div class="relative mx-auto mt-7 w-full px-1 pb-14 pt-8" data-gallery-root
                        data-gallery-count="{{ $galleryItems->count() }}">
                        <div class="flex items-center gap-3">
                            <button type="button"
                                class="grid h-10 w-10 shrink-0 place-items-center rounded-full text-4xl leading-none text-black transition hover:bg-[#eef4ec]"
                                data-gallery-prev aria-label="Foto sebelumnya">&#8249;</button>

                            <div class="flex min-w-0 flex-1 snap-x gap-4 overflow-hidden scroll-smooth"
                                data-gallery-thumbs>
                                @foreach ($galleryItems as $item)
                                    <button type="button"
                                        class="h-[68px] min-w-[88px] snap-center overflow-hidden rounded-[18px] bg-[#d9d9d9] opacity-55 ring-0 transition duration-300 data-[active=true]:opacity-100 data-[active=true]:ring-2 data-[active=true]:ring-[#587477]"
                                        data-gallery-thumb data-gallery-index="{{ $loop->index }}"
                                        data-active="{{ $loop->first ? 'true' : 'false' }}"
                                        aria-label="Tampilkan foto {{ $loop->iteration }}">
                                        <img src="{{ $item['url'] }}" alt="{{ $item['caption'] }}"
                                            class="h-full w-full object-cover" loading="lazy" decoding="async">
                                    </button>
                                @endforeach
                            </div>

                            <button type="button"
                                class="grid h-10 w-10 shrink-0 place-items-center rounded-full text-4xl leading-none text-black transition hover:bg-[#eef4ec]"
                                data-gallery-next aria-label="Foto berikutnya">&#8250;</button>
                        </div>

                        <div
                            class="mx-auto mt-7 aspect-[4/5] w-full max-w-[292px] overflow-hidden rounded-[18px] bg-[#eef4ec]">
                            <img src="{{ $galleryItems->first()['url'] }}"
                                alt="{{ $galleryItems->first()['caption'] }}"
                                class="h-full w-full object-cover opacity-100 transition-opacity duration-500"
                                data-gallery-feature loading="lazy" decoding="async">
                        </div>

                        <div class="mt-6 flex justify-center gap-3" data-gallery-dots>
                            @foreach ($galleryItems as $item)
                                <button type="button"
                                    class="h-3 w-3 rounded-full bg-[#d9d9d9] transition data-[active=true]:bg-[#587477]"
                                    data-gallery-dot data-gallery-index="{{ $loop->index }}"
                                    data-active="{{ $loop->first ? 'true' : 'false' }}"
                                    aria-label="Pilih foto {{ $loop->iteration }}"></button>
                            @endforeach
                        </div>

                        <p class="mx-auto mt-8 max-w-[290px] font-['Poppins',sans-serif] text-[14px] font-semibold leading-relaxed text-[#587276]"
                            data-gallery-caption>
                            {{ $galleryItems->first()['caption'] }}
                        </p>
                    </div>
                </div>

                <img class="absolute bottom-[-100px] left-1/2 z-[3] w-[250px] max-w-none -translate-x-1/2 pointer-events-none opacity-95 animate-[softDrift_5.5s_ease-in-out_infinite_alternate] motion-reduce:animate-none"
                    src="{{ $assetBase }}/bunga_tengah.png" alt="">
            </section>
        @endif

        <section class="relative min-h-screen overflow-hidden px-5 py-16 text-center" id="rsvp">
            <div class="pointer-events-none absolute inset-[18px] border border-[rgba(67,105,78,.13)]"></div>
            <img class="absolute right-[-110px] top-[-70px] z-[1] w-[260px] pointer-events-none opacity-80 animate-[softDrift_7s_ease-in-out_infinite_alternate] motion-reduce:animate-none"
                src="{{ $assetBase }}/bunga_atas.png" alt="">
            <img class="absolute bottom-[-90px] left-[-92px] z-[1] w-[250px] pointer-events-none opacity-80 animate-[softDrift_8.5s_ease-in-out_infinite_alternate] motion-reduce:animate-none"
                src="{{ $assetBase }}/bungaaksen1.png" alt="">

            <div class="relative z-[2] mx-auto w-full max-w-[360px]">
                <h2 class="font-['Great_Vibes',cursive] text-[52px] font-normal leading-none text-[#287047]">
                    Ucapan
                </h2>

                <form class="mt-8 space-y-3" method="POST"
                    action="{{ route('invitation.rsvp', $invitation->slug) }}" id="flowerRsvpForm">
                    @csrf
                    @if (isset($guest) && !empty($guest->id))
                        <input type="hidden" name="guest_id" value="{{ $guest->id }}">
                    @endif
                    <input type="hidden" name="status" id="flowerRsvpStatus" value="attending">
                    <input type="hidden" name="pax" id="flowerRsvpPax" value="1">
                    <input type="hidden" name="redirect_anchor" value="rsvp">

                    <input type="text" name="name" value="{{ old('name', $guestName) }}" required
                        class="w-full rounded-xl border border-[#d7e2d8] bg-white/85 px-4 py-3 font-['Poppins',sans-serif] text-sm font-semibold text-[#24452f] outline-none transition placeholder:text-[#587276]/70 focus:border-[#587477] focus:ring-2 focus:ring-[#587477]/15"
                        placeholder="Nama Anda">

                    <textarea name="message" rows="4"
                        class="w-full resize-none rounded-xl border border-[#d7e2d8] bg-white/85 px-4 py-3 font-['Poppins',sans-serif] text-sm font-semibold leading-relaxed text-[#24452f] outline-none transition placeholder:text-[#587276]/70 focus:border-[#587477] focus:ring-2 focus:ring-[#587477]/15"
                        placeholder="Tuliskan ucapan & doa Anda">{{ old('message') }}</textarea>

                    <button type="button"
                        class="flex min-h-[74px] w-full flex-col items-center justify-center rounded-2xl bg-white px-5 font-['Poppins',sans-serif] text-sm font-bold text-[#24452f] shadow-[0_14px_34px_rgba(57,86,69,.12)] ring-1 ring-[#d7e2d8] transition hover:-translate-y-0.5 hover:bg-[#f8faf5]"
                        id="openFlowerRsvpModal">
                        <span class="text-[26px] leading-none text-[#587477]">☑</span>
                        Lanjut Konfirmasi Kehadiran
                    </button>
                </form>

                <div
                    class="relative mx-auto mt-12 rounded-t-[150px] rounded-b-[34px] bg-white/82 px-4 pb-5 pt-20 text-left shadow-[0_22px_54px_rgba(57,86,69,.12)] ring-1 ring-white/70 backdrop-blur-[10px]">
                    <img class="absolute left-1/2 top-[-54px] z-[1] w-[172px] max-w-none -translate-x-1/2 pointer-events-none drop-shadow-[0_12px_20px_rgba(61,88,68,.08)]"
                        src="{{ $assetBase }}/bunga_tengah.png" alt="">

                    <div class="relative z-[2] text-center">
                        <p
                            class="font-['Poppins',sans-serif] text-[10px] font-semibold uppercase tracking-[.2em] text-[#6b8580]">
                            Doa & Ucapan
                        </p>
                        <p
                            class="mt-1 font-['Great_Vibes',cursive] text-[38px] font-normal leading-none text-[#287047]">
                            Ucapan Tamu
                        </p>
                    </div>

                    <div
                        class="no-scrollbar relative z-[2] mt-5 max-h-[240px] overflow-y-auto rounded-[24px] bg-[#24452f] px-5 py-5 text-left shadow-[0_18px_36px_rgba(36,69,47,.18)]">
                        <div class="pointer-events-none absolute inset-x-5 top-0 h-px bg-white/18"></div>
                        <div class="pointer-events-none absolute inset-x-5 bottom-0 h-px bg-white/12"></div>

                        <div class="space-y-5">
                            @forelse ($rsvpWishItems->take(6) as $wish)
                                <article class="relative border-b border-white/12 pb-5 pl-6 last:border-b-0 last:pb-0">
                                    <span
                                        class="absolute left-0 top-0 font-['Great_Vibes',cursive] text-[32px] leading-none text-white/34">"</span>
                                    <p
                                        class="font-['Poppins',sans-serif] text-sm font-semibold leading-snug text-white">
                                        {{ $wish['name'] }}</p>
                                    <p
                                        class="mt-2 font-['Poppins',sans-serif] text-[13px] font-medium leading-relaxed text-white/80">
                                        {{ $wish['message'] }}</p>
                                </article>
                            @empty
                                <p
                                    class="font-['Poppins',sans-serif] text-xs font-medium leading-relaxed text-white/82">
                                    Belum ada RSVP & ucapan. Jadilah yang pertama mengirimkan konfirmasi kehadiran dan
                                    doa terbaik untuk kedua mempelai.
                                </p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="fixed inset-0 z-[80] hidden items-center justify-center bg-[#24452f]/45 px-5 backdrop-blur-sm"
            id="flowerRsvpModal" aria-hidden="true">
            <div
                class="w-full max-w-[340px] rounded-2xl bg-white p-5 text-left shadow-[0_24px_60px_rgba(36,69,47,.28)]">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="font-['Great_Vibes',cursive] text-[38px] leading-none text-[#287047]">Konfirmasi</p>
                        <p
                            class="mt-1 font-['Poppins',sans-serif] text-xs font-semibold leading-relaxed text-[#587276]">
                            Pilih status kehadiran dan jumlah tamu.
                        </p>
                    </div>
                    <button type="button"
                        class="grid h-9 w-9 place-items-center rounded-full bg-[#eef4ec] text-xl text-[#24452f]"
                        id="closeFlowerRsvpModal" aria-label="Tutup modal">&times;</button>
                </div>

                <div class="mt-5 space-y-4">
                    <div>
                        <label
                            class="font-['Poppins',sans-serif] text-xs font-bold uppercase tracking-[.12em] text-[#587276]">Status</label>
                        <div class="mt-2 grid grid-cols-3 gap-2">
                            <button type="button" data-rsvp-status="attending"
                                class="rounded-xl bg-[#587477] px-3 py-2 font-['Poppins',sans-serif] text-xs font-bold text-white">Hadir</button>
                            <button type="button" data-rsvp-status="maybe"
                                class="rounded-xl bg-[#eef4ec] px-3 py-2 font-['Poppins',sans-serif] text-xs font-bold text-[#587276]">Mungkin</button>
                            <button type="button" data-rsvp-status="not_attending"
                                class="rounded-xl bg-[#eef4ec] px-3 py-2 font-['Poppins',sans-serif] text-xs font-bold text-[#587276]">Tidak</button>
                        </div>
                    </div>

                    <div>
                        <label for="flowerModalPax"
                            class="font-['Poppins',sans-serif] text-xs font-bold uppercase tracking-[.12em] text-[#587276]">Jumlah
                            Tamu</label>
                        <input type="number" min="1" max="10" value="1" id="flowerModalPax"
                            class="mt-2 w-full rounded-xl border border-[#d7e2d8] px-4 py-3 font-['Poppins',sans-serif] text-sm font-bold text-[#24452f] outline-none focus:border-[#587477] focus:ring-2 focus:ring-[#587477]/15">
                    </div>

                    <button type="submit" form="flowerRsvpForm"
                        class="min-h-[48px] w-full rounded-2xl bg-[#587477] px-5 font-['Poppins',sans-serif] text-sm font-bold text-white shadow-[0_14px_30px_rgba(61,93,96,.22)]">
                        Kirim Konfirmasi
                    </button>
                </div>
            </div>
        </div>

        <section class="relative min-h-screen overflow-hidden px-5 py-16 text-center" id="gift">
            <div class="pointer-events-none absolute inset-[18px] border border-[rgba(67,105,78,.13)]"></div>
            <img class="absolute right-[-96px] top-[-42px] z-[1] w-[250px] pointer-events-none opacity-80 animate-[softDrift_7s_ease-in-out_infinite_alternate] motion-reduce:animate-none"
                src="{{ $assetBase }}/bunga_atas.png" alt="">
            <img class="absolute bottom-[-76px] left-[-86px] z-[1] w-[238px] pointer-events-none opacity-80 animate-[softDrift_8.5s_ease-in-out_infinite_alternate] motion-reduce:animate-none"
                src="{{ $assetBase }}/bunga_pojokkiribawah.png" alt="">

            <div class="relative z-[2] mx-auto w-full max-w-[370px]">
                <p
                    class="font-['Poppins',sans-serif] text-[11px] font-semibold uppercase tracking-[.22em] text-[#6b8580]">
                    Wedding Gift
                </p>
                <h2 class="mt-2 font-['Great_Vibes',cursive] text-[58px] font-normal leading-none text-[#287047]">
                    Gift
                </h2>
                <p
                    class="mx-auto mt-4 max-w-[315px] font-['Poppins',sans-serif] text-[13px] font-medium leading-relaxed text-[#587276]">
                    Doa restu Anda adalah hadiah terindah. Bila berkenan, tanda kasih dapat dikirim melalui rekening
                    atau alamat berikut.
                </p>

                <div
                    class="relative mx-auto mt-9 rounded-t-[170px] rounded-b-[42px] bg-white/86 px-5 pb-7 pt-24 text-left shadow-[0_22px_54px_rgba(57,86,69,.12)] ring-1 ring-white/70 backdrop-blur-[10px]">
                    <img class="absolute left-1/2 top-[-58px] z-[1] w-[190px] max-w-none -translate-x-1/2 pointer-events-none drop-shadow-[0_12px_20px_rgba(61,88,68,.08)]"
                        src="{{ $assetBase }}/bunga_tengah.png" alt="">

                    <div class="relative z-[2] space-y-4">
                        @forelse ($giftAccounts as $account)
                            <article
                                class="rounded-[18px] border border-[#d7e2d8] bg-[#f8faf5]/92 p-4 shadow-[0_14px_30px_rgba(57,86,69,.08)]">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p
                                            class="font-['Poppins',sans-serif] text-[10px] font-semibold uppercase tracking-[.18em] text-[#6b8580]">
                                            Rekening
                                        </p>
                                        <h3
                                            class="mt-1 font-['Poppins',sans-serif] text-[17px] font-semibold leading-tight text-[#287047]">
                                            {{ $account->bank_name ?: 'Bank' }}
                                        </h3>
                                    </div>
                                    <div
                                        class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-[#587477] font-['Poppins',sans-serif] text-xs font-semibold text-white shadow-[0_10px_22px_rgba(61,93,96,.20)]">
                                        Rp
                                    </div>
                                </div>

                                <div class="mt-4 space-y-3">
                                    <div>
                                        <p
                                            class="font-['Poppins',sans-serif] text-[10px] font-semibold uppercase tracking-[.14em] text-[#6b8580]">
                                            Atas Nama
                                        </p>
                                        <p
                                            class="mt-1 font-['Poppins',sans-serif] text-sm font-semibold leading-relaxed text-[#24452f]">
                                            {{ $account->account_name ?: $coupleNames }}
                                        </p>
                                    </div>

                                    <div>
                                        <p
                                            class="font-['Poppins',sans-serif] text-[10px] font-semibold uppercase tracking-[.14em] text-[#6b8580]">
                                            Nomor Rekening
                                        </p>
                                        <div class="mt-1 flex items-center gap-2">
                                            <p
                                                class="min-w-0 flex-1 break-all font-['Poppins',sans-serif] text-[17px] font-semibold leading-snug text-[#24452f]">
                                                {{ $account->account_number ?: '-' }}
                                            </p>
                                            @if (!empty($account->account_number))
                                                <button type="button"
                                                    class="shrink-0 rounded-full bg-[#587477] px-3 py-2 font-['Poppins',sans-serif] text-[11px] font-semibold text-white shadow-[0_10px_22px_rgba(61,93,96,.18)] transition hover:-translate-y-0.5 hover:bg-[#405f62]"
                                                    data-gift-copy data-copy-value="{{ $account->account_number }}">
                                                    Salin
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div
                                class="rounded-[18px] border border-dashed border-[#b8cabb] bg-[#f8faf5]/80 p-5 text-center">
                                <p
                                    class="font-['Poppins',sans-serif] text-sm font-semibold leading-relaxed text-[#587276]">
                                    Informasi rekening hadiah belum tersedia.
                                </p>
                            </div>
                        @endforelse

                        <article
                            class="rounded-[18px] border border-[#d7e2d8] bg-[#24452f] p-4 text-white shadow-[0_16px_34px_rgba(36,69,47,.16)]">
                            <p
                                class="font-['Poppins',sans-serif] text-[10px] font-semibold uppercase tracking-[.18em] text-white/70">
                                Alamat Kado
                            </p>
                            <p
                                class="mt-3 font-['Poppins',sans-serif] text-sm font-medium leading-relaxed text-white/90">
                                {{ $giftAddress ?: 'Alamat pengiriman kado belum tersedia.' }}
                            </p>
                            @if ($giftAddress)
                                <button type="button"
                                    class="mt-4 inline-flex min-h-[38px] items-center rounded-full bg-white px-4 font-['Poppins',sans-serif] text-xs font-semibold text-[#24452f] shadow-[0_10px_22px_rgba(0,0,0,.12)] transition hover:-translate-y-0.5"
                                    data-gift-copy data-copy-value="{{ $giftAddress }}">
                                    Salin Alamat
                                </button>
                            @endif
                        </article>
                    </div>
                </div>

                <p class="mt-4 min-h-[20px] font-['Poppins',sans-serif] text-xs font-semibold text-[#587276]"
                    id="giftCopyStatus" aria-live="polite"></p>
            </div>
        </section>

        @if ($invitation->ig_story_photo)
            <section class="relative min-h-screen overflow-hidden px-5 py-16 text-center" id="ig-story">
                <div class="pointer-events-none absolute inset-[18px] border border-[rgba(67,105,78,.13)]"></div>
                <img class="absolute right-[-96px] top-[-42px] z-[1] w-[250px] pointer-events-none opacity-80 animate-[softDrift_7s_ease-in-out_infinite_alternate] motion-reduce:animate-none"
                    src="{{ $assetBase }}/bunga_atas.png" alt="">
                <img class="absolute bottom-[-76px] left-[-86px] z-[1] w-[238px] pointer-events-none opacity-80 animate-[softDrift_8.5s_ease-in-out_infinite_alternate] motion-reduce:animate-none"
                    src="{{ $assetBase }}/bunga_pojokkiribawah.png" alt="">

                <div class="relative z-[2] mx-auto w-full max-w-[370px]">
                    <p
                        class="font-['Poppins',sans-serif] text-[11px] font-semibold uppercase tracking-[.22em] text-[#6b8580]">
                        Instagram Story
                    </p>
                    <h2 class="mt-2 font-['Great_Vibes',cursive] text-[58px] font-normal leading-none text-[#287047]">
                        Download
                    </h2>
                    <p
                        class="mx-auto mt-4 max-w-[315px] font-['Poppins',sans-serif] text-[13px] font-medium leading-relaxed text-[#587276]">
                        Bagikan kabar bahagia ini melalui template story yang sudah disiapkan dari dashboard.
                    </p>

                    <div
                        class="relative mx-auto mt-9 rounded-t-[170px] rounded-b-[42px] bg-white/86 px-5 pb-7 pt-24 text-left shadow-[0_22px_54px_rgba(57,86,69,.12)] ring-1 ring-white/70 backdrop-blur-[10px]">
                        <img class="absolute left-1/2 top-[-58px] z-[1] w-[190px] max-w-none -translate-x-1/2 pointer-events-none drop-shadow-[0_12px_20px_rgba(61,88,68,.08)]"
                            src="{{ $assetBase }}/bunga_tengah.png" alt="">

                        <div
                            class="relative z-[2] mx-auto aspect-[9/16] w-full max-w-[250px] overflow-hidden rounded-[26px] bg-[#24452f] shadow-[0_18px_38px_rgba(36,69,47,.18)]">
                            <img src="{{ asset('storage/' . $invitation->ig_story_photo) }}" alt="Template Instagram Story"
                                class="absolute inset-0 h-full w-full object-cover">
                            <div
                                class="absolute inset-x-0 bottom-0 h-[58%] bg-gradient-to-b from-transparent via-[#24452f]/52 to-[#0d1f26]/92">
                            </div>
                            <div class="absolute inset-x-5 bottom-[88px] text-center text-white">
                                <div class="font-['Great_Vibes',cursive] font-normal leading-none drop-shadow-[0_2px_10px_rgba(0,0,0,.35)]">
                                    <span class="block w-full max-w-full whitespace-nowrap text-[34px]"
                                        data-fit-name data-min-size="18">{{ $brideName ?: 'Mempelai Wanita' }}</span>
                                    <span
                                        class="my-1 block font-['Great_Vibes',cursive] text-[20px] leading-none">and</span>
                                    <span class="block w-full max-w-full whitespace-nowrap text-[34px]"
                                        data-fit-name data-min-size="18">{{ $groomName ?: 'Mempelai Pria' }}</span>
                                </div>
                                <p class="mt-3 font-['Poppins',sans-serif] text-[13px] font-medium tracking-[.18em] text-white/90">
                                    {{ $eventDate?->format('d . m . Y') ?? now()->format('d . m . Y') }}
                                </p>
                                <p class="mt-3 font-['Poppins',sans-serif] text-xs font-medium text-white/82">Wish</p>
                                <div class="mx-auto mt-4 h-[56px] rounded-[14px] bg-[#f8f4ec]/94"></div>
                            </div>
                            <div class="absolute bottom-5 left-5 flex items-center gap-2 text-white">
                                <span class="grid h-5 w-5 place-items-center rounded-full border border-white/70 text-sm leading-none">+</span>
                                <span class="font-['Poppins',sans-serif] text-[11px] font-medium text-white">janjisucikita.com</span>
                            </div>
                        </div>

                        <a href="{{ route('invitation.ig-story.download', $invitation->slug) }}"
                            class="mx-auto mt-6 flex min-h-[46px] w-full max-w-[250px] items-center justify-center rounded-2xl bg-[#587477] px-5 font-['Poppins',sans-serif] text-sm font-semibold text-white shadow-[0_14px_30px_rgba(61,93,96,.22)] transition hover:-translate-y-0.5 hover:bg-[#405f62]"
                            download>
                            Download Template
                        </a>
                    </div>
                </div>
            </section>
        @endif

        <section
            class="relative flex min-h-screen flex-col justify-between overflow-hidden px-5 pb-[max(26px,env(safe-area-inset-bottom))] pt-16 text-center"
            id="closing">
            <div class="pointer-events-none absolute inset-[18px] border border-[rgba(67,105,78,.13)]"></div>
            <img class="absolute right-[-115px] top-[-110px] w-[250px] pointer-events-none opacity-[.82] animate-[softDrift_7s_ease-in-out_infinite_alternate] motion-reduce:animate-none"
                src="{{ $assetBase }}/bunga_atas.png" alt="">
            <img class="absolute bottom-[86px] left-[-92px] z-[1] w-[238px] pointer-events-none opacity-80 animate-[softDrift_8.5s_ease-in-out_infinite_alternate] motion-reduce:animate-none"
                src="{{ $assetBase }}/bunga_pojokkiribawah.png" alt="">
            <img class="absolute bottom-[52px] right-[-86px] z-[1] w-[215px] pointer-events-none opacity-70 animate-[softDrift_7.4s_ease-in-out_infinite_alternate] motion-reduce:animate-none"
                src="{{ $assetBase }}/bunga_setangkai.png" alt="">

            <div class="relative z-[2] mx-auto flex w-full max-w-[370px] flex-1 items-center py-12">
                <div
                    class="relative w-full rounded-t-[170px] rounded-b-[42px] bg-white/84 px-5 pb-8 pt-24 shadow-[0_22px_54px_rgba(57,86,69,.12)] ring-1 ring-white/70 backdrop-blur-[10px]">
                    <img class="absolute left-1/2 top-[-58px] z-[1] w-[190px] max-w-none -translate-x-1/2 pointer-events-none drop-shadow-[0_12px_20px_rgba(61,88,68,.08)]"
                        src="{{ $assetBase }}/bunga_tengah.png" alt="">

                    <p
                        class="font-['Poppins',sans-serif] text-[11px] font-semibold uppercase tracking-[.22em] text-[#6b8580]">
                        Terima Kasih
                    </p>
                    <h2
                        class="mx-auto mt-4 w-full max-w-full font-['Great_Vibes',cursive] font-normal leading-none text-[#287047]">
                        <span
                            class="block w-full max-w-full whitespace-nowrap text-[62px] min-[390px]:text-[70px] min-[720px]:text-[70px]"
                            data-fit-name data-min-size="30">{{ $brideName ?: 'Yuli' }}</span>
                        <span
                            class="my-1 block font-['Great_Vibes',cursive] text-[clamp(24px,7vw,34px)] leading-none text-[#2d7a4e]">and</span>
                        <span
                            class="block w-full max-w-full whitespace-nowrap text-[62px] min-[390px]:text-[70px] min-[720px]:text-[70px]"
                            data-fit-name data-min-size="30">{{ $groomName ?: 'Enden' }}</span>
                    </h2>

                    <div class="mx-auto mt-7 h-px w-24 bg-[#587477]/24"></div>

                    <p
                        class="mx-auto mt-6 max-w-[300px] font-['Poppins',sans-serif] text-[14px] font-medium leading-[1.85] text-[#49665c]">
                        {{ $invitation->closing_text ?: 'Merupakan suatu kehormatan dan kebahagiaan bagi kami apabila Bapak/Ibu/Saudara/i berkenan hadir dan memberikan doa restu.' }}
                    </p>
                </div>
            </div>

            <footer
                class="relative z-[2] mx-auto w-full max-w-[370px] rounded-[24px] bg-[#24452f] px-5 py-4 shadow-[0_18px_38px_rgba(36,69,47,.18)]">
                <div class="flex items-center justify-center gap-3">
                    <img src="{{ asset('logo/logoputih.png') }}" alt="Janji Suci Kita"
                        class="h-8 w-auto shrink-0 object-contain">
                    <p class="font-['Poppins',sans-serif] text-[11px] font-medium tracking-[.06em] text-white">
                        www.janjisucikita.com
                    </p>
                </div>
            </footer>
        </section>
    </main>

    @if ($invitation->music_url)
        <button type="button"
            class="fixed bottom-[18px] z-20 hidden h-[42px] w-[42px] cursor-pointer place-items-center rounded-full border-0 bg-[rgba(67,96,86,.78)] text-white shadow-[0_12px_26px_rgba(40,71,55,.22)] [right:max(16px,calc((100vw-440px)/2+16px))]"
            id="musicToggle" aria-label="Toggle music">&#9834;</button>
        <audio id="weddingMusic" loop preload="none">
            <source src="{{ $invitation->music_signed_url ?? asset('storage/' . $invitation->music_url) }}">
        </audio>
    @endif

    <script>
        (() => {
            const body = document.body;
            const cover = document.getElementById('coverSection');
            const canvas = document.getElementById('coverCanvas');
            const flowerLayers = Array.from(document.querySelectorAll('.flower-layer'));
            const nameLines = Array.from(document.querySelectorAll('[data-fit-name]'));
            const countdown = document.querySelector('[data-countdown]');
            const openButton = document.getElementById('openInvitationButton');
            const music = document.getElementById('weddingMusic');
            const musicToggle = document.getElementById('musicToggle');
            const galleryRoot = document.querySelector('[data-gallery-root]');

            const fitNameLines = () => {
                nameLines.forEach((line) => {
                    line.style.fontSize = '';
                    line.style.transform = '';
                    line.style.transformOrigin = 'center center';
                    const parentWidth = Math.max(0, line.parentElement.clientWidth - 4);
                    let size = parseFloat(window.getComputedStyle(line).fontSize);
                    const minSize = Number(line.dataset.minSize || 28);

                    while (line.scrollWidth > parentWidth && size > minSize) {
                        size -= 1;
                        line.style.fontSize = `${size}px`;
                    }

                    if (line.scrollWidth > parentWidth && parentWidth > 0) {
                        line.style.transform = `scaleX(${parentWidth / line.scrollWidth})`;
                    }
                });
            };

            const scheduleFitNameLines = () => {
                window.requestAnimationFrame(fitNameLines);
            };

            const updateCountdown = () => {
                if (!countdown?.dataset.countdown) {
                    return;
                }

                const target = new Date(countdown.dataset.countdown).getTime();

                if (Number.isNaN(target)) {
                    return;
                }

                const remaining = Math.max(0, target - Date.now());
                const days = Math.floor(remaining / 86400000);
                const hours = Math.floor((remaining % 86400000) / 3600000);
                const minutes = Math.floor((remaining % 3600000) / 60000);
                const seconds = Math.floor((remaining % 60000) / 1000);

                countdown.querySelector('[data-countdown-days]').textContent = String(days);
                countdown.querySelector('[data-countdown-hours]').textContent = String(hours).padStart(2, '0');
                countdown.querySelector('[data-countdown-minutes]').textContent = String(minutes).padStart(2, '0');
                countdown.querySelector('[data-countdown-seconds]').textContent = String(seconds).padStart(2, '0');
            };

            const setParallax = (x, y) => {
                if (!canvas || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                    return;
                }

                flowerLayers.forEach((layer) => {
                    const depth = Number(layer.dataset.depth || 0);
                    layer.style.setProperty('--move-x', `${(x * depth).toFixed(2)}px`);
                    layer.style.setProperty('--move-y', `${(y * depth).toFixed(2)}px`);
                });
            };

            const initGalleryCarousel = () => {
                if (!galleryRoot) {
                    return;
                }

                const feature = galleryRoot.querySelector('[data-gallery-feature]');
                const caption = galleryRoot.querySelector('[data-gallery-caption]');
                const thumbsWrap = galleryRoot.querySelector('[data-gallery-thumbs]');
                const thumbs = Array.from(galleryRoot.querySelectorAll('[data-gallery-thumb]'));
                const dots = Array.from(galleryRoot.querySelectorAll('[data-gallery-dot]'));
                const prev = galleryRoot.querySelector('[data-gallery-prev]');
                const next = galleryRoot.querySelector('[data-gallery-next]');

                if (!feature || thumbs.length === 0) {
                    return;
                }

                let activeIndex = 0;
                let timer = null;

                const setActive = (index, shouldResetTimer = false) => {
                    activeIndex = (index + thumbs.length) % thumbs.length;
                    const activeThumb = thumbs[activeIndex];
                    const image = activeThumb.querySelector('img');

                    if (!image) {
                        return;
                    }

                    feature.classList.add('opacity-0');

                    window.setTimeout(() => {
                        feature.src = image.src;
                        feature.alt = image.alt || 'Galeri foto';
                        caption.textContent = image.alt || 'momen bahagia kami';
                        feature.classList.remove('opacity-0');
                    }, 180);

                    thumbs.forEach((thumb, thumbIndex) => {
                        thumb.dataset.active = thumbIndex === activeIndex ? 'true' : 'false';
                    });

                    dots.forEach((dot, dotIndex) => {
                        dot.dataset.active = dotIndex === activeIndex ? 'true' : 'false';
                    });

                    thumbsWrap?.scrollTo({
                        left: activeThumb.offsetLeft - thumbsWrap.clientWidth / 2 + activeThumb
                            .clientWidth / 2,
                        behavior: 'smooth',
                    });

                    if (shouldResetTimer) {
                        restartAutoplay();
                    }
                };

                const startAutoplay = () => {
                    if (thumbs.length < 2 || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                        return;
                    }

                    if (timer) {
                        return;
                    }

                    timer = window.setInterval(() => {
                        setActive(activeIndex + 1);
                    }, 4200);
                };

                const stopAutoplay = () => {
                    if (timer) {
                        window.clearInterval(timer);
                        timer = null;
                    }
                };

                function restartAutoplay() {
                    stopAutoplay();
                    startAutoplay();
                }

                thumbs.forEach((thumb, index) => {
                    thumb.addEventListener('click', () => setActive(index, true));
                });

                dots.forEach((dot, index) => {
                    dot.addEventListener('click', () => setActive(index, true));
                });

                prev?.addEventListener('click', () => setActive(activeIndex - 1, true));
                next?.addEventListener('click', () => setActive(activeIndex + 1, true));

                thumbsWrap?.addEventListener('pointerenter', stopAutoplay);
                thumbsWrap?.addEventListener('pointerleave', startAutoplay);

                setActive(0);
                startAutoplay();
            };

            const initFlowerRsvpModal = () => {
                const modal = document.getElementById('flowerRsvpModal');
                const openModal = document.getElementById('openFlowerRsvpModal');
                const closeModal = document.getElementById('closeFlowerRsvpModal');
                const form = document.getElementById('flowerRsvpForm');
                const statusInput = document.getElementById('flowerRsvpStatus');
                const paxInput = document.getElementById('flowerRsvpPax');
                const modalPax = document.getElementById('flowerModalPax');
                const statusButtons = Array.from(document.querySelectorAll('[data-rsvp-status]'));

                if (!modal || !openModal || !form || !statusInput || !paxInput || !modalPax) {
                    return;
                }

                const showModal = () => {
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    modal.setAttribute('aria-hidden', 'false');
                    modalPax.focus();
                };

                const hideModal = () => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    modal.setAttribute('aria-hidden', 'true');
                };

                const setStatus = (status) => {
                    statusInput.value = status;
                    statusButtons.forEach((button) => {
                        const isActive = button.dataset.rsvpStatus === status;
                        button.classList.toggle('bg-[#587477]', isActive);
                        button.classList.toggle('text-white', isActive);
                        button.classList.toggle('bg-[#eef4ec]', !isActive);
                        button.classList.toggle('text-[#587276]', !isActive);
                    });
                };

                openModal.addEventListener('click', showModal);
                closeModal?.addEventListener('click', hideModal);

                modal.addEventListener('click', (event) => {
                    if (event.target === modal) {
                        hideModal();
                    }
                });

                window.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                        hideModal();
                    }
                });

                modalPax.addEventListener('input', () => {
                    const value = Math.max(1, Math.min(10, Number(modalPax.value || 1)));
                    modalPax.value = value;
                    paxInput.value = value;
                });

                statusButtons.forEach((button) => {
                    button.addEventListener('click', () => setStatus(button.dataset.rsvpStatus));
                });

                form.addEventListener('submit', () => {
                    const value = Math.max(1, Math.min(10, Number(modalPax.value || 1)));
                    paxInput.value = value;
                });

                setStatus(statusInput.value || 'attending');
            };

            const initGiftCopy = () => {
                const status = document.getElementById('giftCopyStatus');
                const buttons = Array.from(document.querySelectorAll('[data-gift-copy]'));
                let timer = null;

                const setStatus = (message) => {
                    if (!status) {
                        return;
                    }

                    status.textContent = message;

                    if (timer) {
                        window.clearTimeout(timer);
                    }

                    timer = window.setTimeout(() => {
                        status.textContent = '';
                    }, 2200);
                };

                const fallbackCopy = (value) => {
                    const field = document.createElement('textarea');
                    field.value = value;
                    field.setAttribute('readonly', '');
                    field.style.position = 'fixed';
                    field.style.top = '-999px';
                    document.body.appendChild(field);
                    field.select();
                    document.execCommand('copy');
                    field.remove();
                };

                buttons.forEach((button) => {
                    button.addEventListener('click', async () => {
                        const value = button.dataset.copyValue || '';

                        if (!value) {
                            return;
                        }

                        try {
                            if (navigator.clipboard?.writeText) {
                                await navigator.clipboard.writeText(value);
                            } else {
                                fallbackCopy(value);
                            }

                            setStatus('Berhasil disalin.');
                        } catch (error) {
                            setStatus('Gagal menyalin. Silakan salin manual.');
                        }
                    });
                });
            };

            canvas?.addEventListener('pointermove', (event) => {
                const rect = canvas.getBoundingClientRect();
                const x = ((event.clientX - rect.left) / rect.width) - 0.5;
                const y = ((event.clientY - rect.top) / rect.height) - 0.5;
                setParallax(x, y);
            }, {
                passive: true
            });

            window.addEventListener('deviceorientation', (event) => {
                const x = Math.max(-0.5, Math.min(0.5, (event.gamma || 0) / 60));
                const y = Math.max(-0.5, Math.min(0.5, (event.beta || 0) / 90));
                setParallax(x, y);
            }, {
                passive: true
            });

            window.addEventListener('resize', scheduleFitNameLines, {
                passive: true
            });
            window.addEventListener('load', scheduleFitNameLines);

            if (document.fonts?.ready) {
                document.fonts.ready.then(scheduleFitNameLines).catch(() => {});
            }

            if ('ResizeObserver' in window) {
                const nameObserver = new ResizeObserver(scheduleFitNameLines);
                nameLines.forEach((line) => nameObserver.observe(line.parentElement));
            }

            scheduleFitNameLines();
            updateCountdown();

            if (countdown?.dataset.countdown) {
                window.setInterval(updateCountdown, 1000);
            }

            initGalleryCarousel();
            initFlowerRsvpModal();
            initGiftCopy();

            const tryPlayMusic = () => {
                if (!music) {
                    return;
                }

                music.play()
                    .then(() => musicToggle?.classList.add('bg-[#405f62]'))
                    .catch(() => {});
            };

            openButton?.addEventListener('click', () => {
                cover?.classList.add('invisible', 'opacity-0', 'pointer-events-none');
                body.classList.remove('cover-lock');
                musicToggle?.classList.remove('hidden');
                musicToggle?.classList.add('grid');
                tryPlayMusic();

                window.setTimeout(() => {
                    document.getElementById('opening')?.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }, 120);
            });

            musicToggle?.addEventListener('click', () => {
                if (!music) {
                    return;
                }

                if (music.paused) {
                    tryPlayMusic();
                } else {
                    music.pause();
                    musicToggle.classList.remove('bg-[#405f62]');
                }
            });
        })();
    </script>
</body>

</html>
