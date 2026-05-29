<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $invitation->title }} - {{ $invitation->venue_name }}">
    <title>{{ $invitation->title }} - {{ config('app.name') }}</title>

    @vite(['resources/css/app.css'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Inter:wght@300;400;500;600&family=Noto+Naskh+Arabic:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @php
        $coverPhoto = $invitation->cover_photo
            ? asset('storage/' . $invitation->cover_photo)
            : 'https://images.unsplash.com/photo-1511285560929-80b456fea0bc?auto=format&fit=crop&w=1200&q=80';
        $displayTitle =
            $invitation->event_type === 'wedding'
                ? trim(
                    collect([$invitation->bride_name, $invitation->groom_name])
                        ->filter()
                        ->implode(' & '),
                )
                : $invitation->title;
        $displayTitle = $displayTitle !== '' ? $displayTitle : $invitation->title;
        $targetDate =
            optional($invitation->event_date)?->format('Y-m-d') .
            'T' .
            ($invitation->event_time ? \Carbon\Carbon::parse($invitation->event_time)->format('H:i:s') : '00:00:00');
        $guestName = $guest->name ?? null;
        $openingPhoto = $invitation->photos->first()?->file_path
            ? asset('storage/' . $invitation->photos->first()->file_path)
            : $coverPhoto;
        $blessingPhoto = $invitation->photos->skip(1)->first()?->file_path
            ? asset('storage/' . $invitation->photos->skip(1)->first()->file_path)
            : $coverPhoto;
        $eventPhoto = $invitation->photos->skip(2)->first()?->file_path
            ? asset('storage/' . $invitation->photos->skip(2)->first()->file_path)
            : $coverPhoto;
        $bridePhoto = $invitation->bride_photo ? asset('storage/' . $invitation->bride_photo) : $coverPhoto;
        $groomPhoto = $invitation->groom_photo ? asset('storage/' . $invitation->groom_photo) : $coverPhoto;
        $musicDirectUrl = $invitation->music_url
            ? \Illuminate\Support\Facades\Storage::url($invitation->music_url)
            : null;
        $eventItems = $invitation->events->count()
            ? $invitation->events
            : collect([
                (object) [
                    'event_name' => $invitation->title ?: 'Wedding Event',
                    'event_date' => $invitation->event_date,
                    'event_time' => $invitation->event_time,
                    'event_end_time' => $invitation->event_end_time,
                    'venue_name' => $invitation->venue_name,
                    'venue_address' => $invitation->venue_address,
                    'venue_maps_url' => $invitation->google_maps_url,
                ],
            ]);
        $loveStoryItems = $invitation->loveStories->values()->map(function ($story) use ($coverPhoto) {
            return (object) [
                'year' => $story->year,
                'title' => $story->title,
                'description' => $story->description,
                'background' => $story->photo_path ? asset('storage/' . $story->photo_path) : $coverPhoto,
            ];
        });
        $galleryItems = $invitation->photos->values()->map(function ($photo) {
            return (object) [
                'url' => asset('storage/' . $photo->file_path),
                'caption' => $photo->caption,
            ];
        });
        $giftPhoto = $invitation->photos->skip(3)->first()?->file_path
            ? asset('storage/' . $invitation->photos->skip(3)->first()->file_path)
            : $coverPhoto;
        $closingPhoto = $invitation->photos->skip(4)->first()?->file_path
            ? asset('storage/' . $invitation->photos->skip(4)->first()->file_path)
            : $coverPhoto;
        $poweredLogo = asset('logo/logoputih.png');
        $igStoryPhoto = $invitation->ig_story_photo ? asset('storage/' . $invitation->ig_story_photo) : null;
        $giftAccounts = $invitation->bankAccounts->count()
            ? $invitation->bankAccounts
            : collect(
                array_filter([
                    $invitation->bank_name || $invitation->bank_account_number || $invitation->bank_account_name
                        ? (object) [
                            'bank_name' => $invitation->bank_name,
                            'account_number' => $invitation->bank_account_number,
                            'account_name' => $invitation->bank_account_name,
                        ]
                        : null,
                ]),
            );
        $giftAccounts = $giftAccounts->map(function ($account) {
            $bankName = strtolower(trim((string) ($account->bank_name ?? '')));
            $assetMap = [
                'bank bca' => 'bca.svg',
                'bca' => 'bca.svg',
                'bank bni' => 'bni.svg',
                'bni' => 'bni.svg',
                'bank bri' => 'bri.svg',
                'bri' => 'bri.svg',
                'bank mandiri' => 'mandiri.svg',
                'mandiri' => 'mandiri.svg',
                'bsi' => 'bsi.svg',
                'bank syariah indonesia' => 'bsi.svg',
                'btn' => 'btn.svg',
                'bank btn' => 'btn.svg',
                'bjb' => 'bjb.svg',
                'bank bjb' => 'bjb.svg',
                'cimb' => 'cimb.svg',
                'cimb niaga' => 'cimb.svg',
                'danamon' => 'danamon.svg',
                'permata' => 'permata.svg',
                'seabank' => 'SeaBank.svg',
                'sea bank' => 'SeaBank.svg',
                'ovo' => 'ovo.svg',
                'gopay' => 'Gopay.svg',
                'go pay' => 'Gopay.svg',
                'dana' => 'dana.png',
                'linkaja' => 'LinkAja.svg',
                'link aja' => 'LinkAja.svg',
            ];

            $account->logo = asset('assets/banks/' . ($assetMap[$bankName] ?? 'default.svg'));
            return $account;
        });
    @endphp

    <style>
        :root {
            --bg: #050608;
            --panel: #0c0f14;
            --panel-soft: rgba(255, 255, 255, 0.03);
            --gold: #c6a76a;
            --gold-soft: rgba(198, 167, 106, 0.24);
            --text: rgba(255, 255, 255, 0.92);
            --muted: rgba(255, 255, 255, 0.66);
            --muted-soft: rgba(255, 255, 255, 0.46);
            --screen-h: 100dvh;
        }

        body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            scroll-snap-type: y mandatory;
            background:
                radial-gradient(circle at top, rgba(198, 167, 106, 0.08), transparent 28%),
                linear-gradient(180deg, #040507 0%, #07090d 52%, #050608 100%);
            color: var(--text);
            font-family: 'Inter', sans-serif;
        }

        body.is-locked {
            overflow: hidden;
            height: 100svh;
        }

        .hero-wrapper {
            width: 100%;
            min-height: var(--screen-h);
            display: flex;
            position: relative;
            align-items: flex-start;
            background: #050608;
            scroll-snap-align: start;
            scroll-snap-stop: always;
        }

        .hero-left {
            width: 68%;
            min-height: var(--screen-h);
            background: #050608;
            position: sticky;
            top: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            box-sizing: border-box;
            overflow: hidden;
        }

        .hero-left::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at center, rgba(255, 255, 255, 0.08), transparent 60%);
        }

        .hero-text-left {
            position: relative;
            z-index: 2;
            text-align: center;
            color: white;
            max-width: 640px;
        }

        .reveal {
            opacity: 0;
            transform: translate3d(0, 28px, 0) scale(.985);
            transition:
                opacity .9s cubic-bezier(.22, 1, .36, 1),
                transform .9s cubic-bezier(.22, 1, .36, 1);
            will-change: opacity, transform;
        }

        .reveal.is-visible {
            opacity: 1;
            transform: translate3d(0, 0, 0) scale(1);
        }

        .reveal-delay-1 {
            transition-delay: .12s;
        }

        .reveal-delay-2 {
            transition-delay: .22s;
        }

        .reveal-delay-3 {
            transition-delay: .34s;
        }

        .eyebrow {
            margin-bottom: 15px;
            color: var(--muted);
            letter-spacing: .24em;
            text-transform: uppercase;
            font-size: 11px;
            font-weight: 500;
        }

        .hero-text-left h1,
        .hero-content h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(1.45rem, 2.3vw, 2rem);
            margin: 0;
            font-weight: 500;
            line-height: 1.08;
            letter-spacing: .03em;
        }

        .hero-date,
        .guest-line {
            margin-top: 1rem;
            font-size: .74rem;
            color: var(--muted);
            letter-spacing: .08em;
        }

        .hero-right {
            width: 32%;
            position: relative;
            background:
                linear-gradient(180deg, rgba(10, 13, 19, 0.96), rgba(7, 9, 13, 0.98));
            border-left: 1px solid rgba(198, 167, 106, 0.12);
        }

        .hero-visual {
            position: relative;
            min-height: var(--screen-h);
            overflow: hidden;
        }

        .hero-visual::after {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 160px;
            z-index: 2;
            pointer-events: none;
            background: linear-gradient(180deg, rgba(5, 6, 8, 0) 0%, rgba(5, 6, 8, 0.22) 34%, rgba(5, 6, 8, 0.76) 72%, rgba(5, 6, 8, 0.98) 100%);
        }

        .hero-right img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .hero-overlay {
            position: absolute;
            inset: 0;
            background:
                linear-gradient(to top, rgba(4, 5, 7, .94), rgba(4, 5, 7, .28)),
                linear-gradient(to left, rgba(4, 5, 7, .25), rgba(4, 5, 7, .02));
        }

        .hero-content {
            position: absolute;
            bottom: 56px;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            text-align: center;
            color: white;
            z-index: 3;
            padding: 0 20px;
            box-sizing: border-box;
        }

        .hero-content-inner {
            max-width: 360px;
            margin: 0 auto;
        }

        .right-scroll {
            position: relative;
            z-index: 2;
            background: linear-gradient(180deg, rgba(5, 6, 8, 0.12) 0%, rgba(5, 6, 8, 0.84) 12%, rgba(5, 6, 8, 0.98) 100%);
        }

        .right-scroll::before {
            content: '';
            position: absolute;
            top: -90px;
            left: 0;
            right: 0;
            height: 150px;
            z-index: 3;
            pointer-events: none;
            background: linear-gradient(180deg, rgba(5, 6, 8, 0.96) 0%, rgba(5, 6, 8, 0.72) 24%, rgba(5, 6, 8, 0.34) 58%, rgba(5, 6, 8, 0.08) 82%, rgba(5, 6, 8, 0) 100%);
        }

        .right-section {
            min-height: var(--screen-h);
            padding: 2rem 1.2rem;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            scroll-snap-align: start;
            scroll-snap-stop: always;
            position: relative;
        }

        .right-section:first-child {
            margin-top: 0;
        }

        .right-card {
            width: 100%;
            max-width: 360px;
            margin: 0 auto;
            padding: 0;
            border: 0;
            background: transparent;
            backdrop-filter: none;
            box-shadow: none;
        }

        .right-card .verse-arabic {
            text-align: center;
            font-size: 1.18rem;
            line-height: 1.9;
            margin-bottom: .65rem;
            color: rgba(255, 255, 255, 0.94);
        }

        .right-card .verse-translation,
        .right-card .verse-ref,
        .right-card .verse-label {
            text-align: center;
        }

        .right-card .verse-label {
            margin-bottom: .45rem;
            font-size: 9px;
            letter-spacing: .22em;
            color: rgba(198, 167, 106, 0.84);
        }

        .right-card .verse-translation {
            font-size: .72rem;
            line-height: 1.75;
            color: rgba(255, 255, 255, 0.72);
        }

        .right-card .verse-ref {
            margin-top: .55rem;
            font-size: .68rem;
            letter-spacing: .08em;
            color: rgba(255, 255, 255, 0.82);
        }

        .opening-section {
            overflow: hidden;
            background-image:
                linear-gradient(180deg, rgba(5, 6, 8, 0.22) 0%, rgba(5, 6, 8, 0.46) 28%, rgba(5, 6, 8, 0.88) 100%),
                url('{{ $openingPhoto }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .opening-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                linear-gradient(180deg, rgba(5, 6, 8, 0.82) 0%, rgba(5, 6, 8, 0.28) 18%, rgba(5, 6, 8, 0) 34%),
                linear-gradient(180deg, rgba(5, 6, 8, 0) 0%, rgba(5, 6, 8, 0.16) 68%, rgba(5, 6, 8, 0.52) 100%);
            pointer-events: none;
            z-index: 0;
        }

        .opening-section .right-card {
            position: relative;
            z-index: 1;
        }

        .blessing-section {
            overflow: hidden;
            background-image:
                linear-gradient(180deg, rgba(5, 6, 8, 0.12) 0%, rgba(5, 6, 8, 0.34) 34%, rgba(5, 6, 8, 0.88) 100%),
                url('{{ $blessingPhoto }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .blessing-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                linear-gradient(90deg, rgba(5, 6, 8, 0.8) 0%, rgba(5, 6, 8, 0.36) 36%, rgba(5, 6, 8, 0.08) 100%),
                linear-gradient(180deg, rgba(5, 6, 8, 0.22) 0%, rgba(5, 6, 8, 0) 28%, rgba(5, 6, 8, 0.44) 100%);
            pointer-events: none;
        }

        .blessing-card {
            position: relative;
            z-index: 1;
            max-width: 340px;
            margin: 0 auto;
            text-align: center;
            padding: 0 0 5.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-end;
        }

        .ornament-dots {
            display: flex;
            justify-content: center;
            gap: .35rem;
            margin-bottom: .9rem;
        }

        .ornament-dots span {
            width: 10px;
            height: 10px;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.7);
            background: transparent;
            box-shadow: 0 0 18px rgba(255, 255, 255, 0.12);
        }

        .blessing-text {
            font-size: .8rem;
            line-height: 1.75;
            color: rgba(255, 255, 255, 0.84);
            margin: 0;
            text-align: center;
            max-width: 320px;
        }

        .blessing-line {
            width: 142px;
            height: 1px;
            margin: 1rem auto 0;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.72), transparent);
        }

        .person-section {
            overflow: hidden;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .person-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                linear-gradient(180deg, rgba(5, 6, 8, 0.16) 0%, rgba(5, 6, 8, 0.02) 28%, rgba(5, 6, 8, 0.72) 100%),
                linear-gradient(90deg, rgba(5, 6, 8, 0.22) 0%, rgba(5, 6, 8, 0.04) 42%, rgba(5, 6, 8, 0.56) 100%);
            pointer-events: none;
        }

        .bride-section {
            background-image:
                linear-gradient(180deg, rgba(5, 6, 8, 0.08) 0%, rgba(5, 6, 8, 0.18) 40%, rgba(5, 6, 8, 0.78) 100%),
                url('{{ $bridePhoto }}');
        }

        .groom-section {
            background-image:
                linear-gradient(180deg, rgba(5, 6, 8, 0.08) 0%, rgba(5, 6, 8, 0.18) 40%, rgba(5, 6, 8, 0.78) 100%),
                url('{{ $groomPhoto }}');
        }

        .person-card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 360px;
            margin: 0 auto;
            text-align: center;
            padding-bottom: 3.8rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-end;
        }

        .person-name {
            margin: 0;
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(2rem, 4vw, 3rem);
            line-height: 1;
            font-weight: 600;
            color: #fff;
        }

        .person-role {
            margin-top: .55rem;
            font-size: .82rem;
            color: rgba(255, 255, 255, 0.88);
        }

        .person-parent {
            margin: .7rem 0 0;
            max-width: 320px;
            font-size: .76rem;
            line-height: 1.7;
            color: rgba(255, 255, 255, 0.72);
            text-align: center;
        }

        .social-pill {
            margin-top: 1rem;
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            padding: .78rem 1.18rem;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.78);
            color: #fff;
            text-decoration: none;
            font-size: .78rem;
            background: rgba(5, 6, 8, 0.22);
            backdrop-filter: blur(8px);
        }

        .event-section {
            overflow: hidden;
            background-image:
                linear-gradient(180deg, rgba(5, 6, 8, 0.14) 0%, rgba(5, 6, 8, 0.22) 34%, rgba(5, 6, 8, 0.86) 100%),
                url('{{ $eventPhoto }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            align-items: center;
            justify-content: center;
        }

        .event-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                linear-gradient(90deg, rgba(5, 6, 8, 0.82) 0%, rgba(5, 6, 8, 0.24) 44%, rgba(5, 6, 8, 0.74) 100%),
                linear-gradient(180deg, rgba(5, 6, 8, 0.14) 0%, rgba(5, 6, 8, 0) 20%, rgba(5, 6, 8, 0.48) 100%);
            pointer-events: none;
        }

        .event-stack {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 460px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 1.7rem;
            padding: 3rem 1.25rem;
        }

        .event-header {
            text-align: center;
        }

        .event-header h2 {
            margin: 0 0 .4rem;
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(1.85rem, 3.2vw, 2.6rem);
            line-height: 1;
            color: #fff;
            font-weight: 600;
        }

        .event-header p {
            margin: 0;
            font-size: .72rem;
            line-height: 1.7;
            color: rgba(255, 255, 255, 0.78);
        }

        .event-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            width: 100%;
        }

        .event-item {
            text-align: center;
            width: 100%;
            padding: 0 .8rem;
        }

        .event-item+.event-item {
            border-top: 1px dashed rgba(255, 255, 255, 0.28);
            padding-top: 1.15rem;
        }

        .event-name {
            margin: 0 0 .9rem;
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(1.5rem, 2.2vw, 2rem);
            line-height: 1.08;
            font-weight: 600;
            letter-spacing: .01em;
            color: #fff;
        }

        .event-date-grid {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: center;
            gap: .55rem;
            max-width: 180px;
            margin: 0 auto .8rem;
        }

        .event-date-grid .small {
            font-size: .66rem;
            color: rgba(255, 255, 255, 0.86);
        }

        .event-date-grid .day {
            font-size: 1.95rem;
            font-weight: 600;
            color: #fff;
            line-height: 1;
            padding: 0 .65rem;
            border-left: 1px solid rgba(255, 255, 255, 0.46);
            border-right: 1px solid rgba(255, 255, 255, 0.46);
        }

        .event-time,
        .event-venue,
        .event-address {
            margin: .22rem 0 0;
            font-size: .72rem;
            color: rgba(255, 255, 255, 0.84);
        }

        .event-venue {
            font-weight: 600;
            color: #fff;
        }

        .event-actions {
            margin-top: .8rem;
            display: flex;
            justify-content: center;
            gap: .5rem;
            flex-wrap: wrap;
        }

        .love-story-section {
            overflow: hidden;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .love-story-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                linear-gradient(180deg, rgba(5, 6, 8, 0.8) 0%, rgba(5, 6, 8, 0.38) 38%, rgba(5, 6, 8, 0.9) 100%),
                linear-gradient(90deg, rgba(5, 6, 8, 0.3) 0%, rgba(5, 6, 8, 0.55) 100%);
            pointer-events: none;
        }

        .story-stage {
            position: relative;
            z-index: 1;
            width: 100%;
            min-height: var(--screen-h);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            text-align: center;
            padding: 2.6rem 1.6rem 3rem;
            box-sizing: border-box;
        }

        .story-head {
            margin-bottom: .8rem;
        }

        .story-head.is-continuation {
            height: 18px;
            margin-bottom: .25rem;
        }

        .story-head h2 {
            margin: 0;
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(1.9rem, 3vw, 2.6rem);
            line-height: 1;
            font-weight: 600;
            color: #fff;
        }

        .story-line {
            position: relative;
            width: 1px;
            flex: 1 1 auto;
            min-height: 180px;
            margin: .2rem 0 1rem;
            background: repeating-linear-gradient(to bottom,
                    rgba(255, 255, 255, 0.42) 0,
                    rgba(255, 255, 255, 0.42) 3px,
                    transparent 3px,
                    transparent 7px);
        }

        .story-line::before,
        .story-line::after {
            content: '';
            position: absolute;
            left: 50%;
            width: 12px;
            height: 12px;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.78);
            background: rgba(10, 12, 16, 0.72);
            transform: translateX(-50%);
        }

        .story-line::before {
            top: -6px;
        }

        .story-line::after {
            bottom: -6px;
        }

        .story-line.is-last::after {
            display: none;
        }

        .story-line.is-continuation::before {
            top: -12px;
        }

        .story-copy {
            width: min(100%, 430px);
            display: grid;
            gap: .75rem;
        }

        .story-year {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .24em;
            color: rgba(255, 255, 255, 0.74);
        }

        .story-title {
            margin: 0;
            font-size: 1rem;
            line-height: 1.35;
            font-weight: 600;
            color: #fff;
        }

        .story-description {
            margin: 0;
            font-size: .86rem;
            line-height: 1.9;
            color: rgba(255, 255, 255, 0.9);
        }

        .gallery-section {
            overflow: hidden;
            background:
                radial-gradient(circle at top, rgba(198, 167, 106, 0.18), transparent 34%),
                linear-gradient(180deg, rgba(14, 24, 36, 0.96) 0%, rgba(41, 63, 88, 0.74) 48%, rgba(10, 14, 22, 0.98) 100%);
        }

        .gallery-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                linear-gradient(180deg, rgba(5, 6, 8, 0.22) 0%, rgba(5, 6, 8, 0.08) 20%, rgba(5, 6, 8, 0.46) 100%),
                radial-gradient(circle at center, rgba(255, 255, 255, 0.05), transparent 58%);
            pointer-events: none;
        }

        .gallery-stage {
            position: relative;
            z-index: 1;
            width: min(100%, 430px);
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-end;
            gap: 1rem;
            padding: 2.4rem 0 2.6rem;
        }

        .gallery-heading {
            text-align: center;
            max-width: 320px;
        }

        .gallery-heading h2 {
            margin: 0;
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(2rem, 3.2vw, 2.9rem);
            line-height: .95;
            font-weight: 600;
            color: #fff;
        }

        .gallery-heading p {
            margin: .45rem 0 0;
            font-size: .82rem;
            line-height: 1.6;
            color: rgba(255, 255, 255, 0.82);
            font-style: italic;
        }

        .gallery-thumb-row {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .7rem;
        }

        .gallery-thumb-glow {
            width: 28px;
            height: 28px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.12);
            box-shadow: 0 0 24px rgba(255, 255, 255, 0.08);
            flex: 0 0 auto;
        }

        .gallery-thumbs {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: .65rem;
            flex-wrap: nowrap;
            overflow-x: auto;
            max-width: 100%;
            padding: .1rem .15rem .35rem;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .gallery-thumbs::-webkit-scrollbar {
            display: none;
        }

        .gallery-thumb {
            width: 72px;
            height: 58px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
            background: rgba(255, 255, 255, 0.04);
            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.2);
            opacity: 0.7;
            transform: translateY(0);
            transition: opacity .3s ease, transform .3s ease, border-color .3s ease;
            cursor: pointer;
        }

        .gallery-thumb.is-active {
            opacity: 1;
            transform: translateY(-2px);
            border-color: rgba(198, 167, 106, 0.75);
        }

        .gallery-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .gallery-feature {
            width: 100%;
            border-radius: 24px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.14);
            background: rgba(255, 255, 255, 0.04);
            box-shadow: 0 20px 54px rgba(0, 0, 0, 0.32);
        }

        .gallery-feature img {
            width: 100%;
            aspect-ratio: 4 / 5.4;
            object-fit: cover;
            display: block;
        }

        .gallery-caption {
            margin: -.15rem 0 0;
            min-height: 18px;
            font-size: .72rem;
            line-height: 1.5;
            letter-spacing: .04em;
            color: rgba(255, 255, 255, 0.66);
            text-align: center;
        }

        .rsvp-section {
            overflow: hidden;
            background:
                radial-gradient(circle at top, rgba(198, 167, 106, 0.12), transparent 30%),
                linear-gradient(180deg, rgba(18, 20, 24, 0.98) 0%, rgba(44, 46, 44, 0.92) 100%);
        }

        .rsvp-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                linear-gradient(180deg, rgba(5, 6, 8, 0.26) 0%, rgba(5, 6, 8, 0.1) 22%, rgba(5, 6, 8, 0.42) 100%);
            pointer-events: none;
        }

        .rsvp-stage {
            position: relative;
            z-index: 1;
            width: min(100%, 430px);
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-end;
            gap: 1.2rem;
            padding: 2.4rem 0 2.8rem;
            text-align: center;
        }

        .rsvp-title,
        .gift-title,
        .wishes-title {
            margin: 0;
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(2rem, 3.2vw, 2.8rem);
            line-height: .95;
            font-weight: 600;
            color: #fff;
        }

        .rsvp-action-card,
        .gift-action-card {
            width: 100%;
            border: 0;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.96);
            color: #1a1b1f;
            padding: 1rem 1rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: .45rem;
            cursor: pointer;
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.2);
            transition: transform .28s ease, box-shadow .28s ease;
        }

        .rsvp-action-card:hover,
        .gift-action-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 22px 46px rgba(0, 0, 0, 0.24);
        }

        .rsvp-action-card i,
        .gift-action-card i {
            font-size: 1.45rem;
            color: #2f3138;
        }

        .rsvp-action-card span,
        .gift-action-card span {
            font-size: .82rem;
            font-weight: 600;
        }

        .wishes-form {
            width: 100%;
            display: grid;
            gap: .55rem;
        }

        .wishes-input,
        .wishes-textarea {
            width: 100%;
            border: 0;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.12);
            color: #fff;
            box-sizing: border-box;
            font: inherit;
        }

        .wishes-input {
            padding: .95rem 1rem;
        }

        .wishes-textarea {
            min-height: 150px;
            padding: 1rem;
            resize: vertical;
        }

        .wishes-input::placeholder,
        .wishes-textarea::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .wishes-submit {
            width: 100%;
            border: 0;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.96);
            color: #111;
            padding: .95rem 1rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .55rem;
            font-size: .9rem;
            font-weight: 500;
            cursor: pointer;
        }

        .wishes-list {
            width: 100%;
            max-height: 250px;
            overflow-y: auto;
            padding: .35rem;
            border-radius: 14px;
            background: rgba(0, 0, 0, 0.22);
        }

        .wish-item {
            padding: .8rem .85rem;
            border-radius: 12px;
            background: rgba(0, 0, 0, 0.46);
            text-align: left;
        }

        .wish-item+.wish-item {
            margin-top: .45rem;
        }

        .wish-name {
            font-size: .92rem;
            font-weight: 600;
            color: #fff;
        }

        .wish-date {
            margin-top: .25rem;
            font-size: .67rem;
            color: rgba(255, 255, 255, 0.62);
        }

        .wish-message {
            margin-top: .4rem;
            font-size: .8rem;
            line-height: 1.7;
            color: rgba(255, 255, 255, 0.9);
        }

        .gift-copy {
            max-width: 360px;
        }

        .gift-copy p {
            margin: .45rem 0 0;
            font-size: .82rem;
            line-height: 1.7;
            color: rgba(255, 255, 255, 0.84);
        }

        .gift-address-wrap {
            display: grid;
            gap: .35rem;
        }

        .gift-address-wrap h4 {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.94);
        }

        .gift-address-wrap p {
            margin: 0;
            font-size: .8rem;
            line-height: 1.65;
            color: rgba(255, 255, 255, 0.76);
        }

        .gift-address-box {
            width: 100%;
            padding: 1rem 1.1rem;
            border: 1px dashed rgba(255, 255, 255, 0.38);
            border-radius: 16px;
            font-size: .84rem;
            line-height: 1.7;
            color: rgba(255, 255, 255, 0.92);
            background: rgba(255, 255, 255, 0.04);
        }

        .gift-section {
            overflow: hidden;
            background-image:
                linear-gradient(180deg, rgba(5, 6, 8, 0.24) 0%, rgba(5, 6, 8, 0.46) 32%, rgba(5, 6, 8, 0.92) 100%),
                url('{{ $giftPhoto }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .gift-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                linear-gradient(180deg, rgba(5, 6, 8, 0.7) 0%, rgba(5, 6, 8, 0.22) 20%, rgba(5, 6, 8, 0.54) 100%),
                radial-gradient(circle at top, rgba(255, 255, 255, 0.06), transparent 42%);
            pointer-events: none;
        }

        .gift-stage {
            position: relative;
            z-index: 1;
            width: min(100%, 500px);
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-end;
            gap: 1rem;
            padding: 2.4rem 0 2.8rem;
            text-align: center;
        }

        .gift-intro {
            max-width: 380px;
        }

        .gift-intro p {
            margin: .5rem 0 0;
            font-size: .82rem;
            line-height: 1.7;
            color: rgba(255, 255, 255, 0.84);
        }

        .gift-trigger {
            width: 100%;
            max-width: 420px;
            border: 0;
            border-radius: 18px;
            background: rgba(238, 226, 213, 0.96);
            color: #242830;
            padding: 1rem 1rem .95rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: .45rem;
            cursor: pointer;
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.2);
            transition: transform .28s ease, box-shadow .28s ease;
        }

        .gift-trigger:hover {
            transform: translateY(-2px);
            box-shadow: 0 22px 46px rgba(0, 0, 0, 0.24);
        }

        .gift-trigger i {
            font-size: 1.55rem;
            color: #2b313a;
        }

        .gift-trigger span {
            font-size: .86rem;
            font-weight: 600;
        }

        .gift-card-list {
            width: 100%;
            display: grid;
            gap: .7rem;
        }

        .gift-card {
            width: 100%;
            border-radius: 20px;
            padding: 1.25rem 1.2rem 1.15rem;
            background: rgba(255, 255, 255, 0.96);
            color: #17191e;
            box-sizing: border-box;
            text-align: center;
            box-shadow: 0 22px 46px rgba(0, 0, 0, 0.22);
        }

        .gift-card-bank {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 28px;
        }

        .gift-card-bank img {
            max-width: 82px;
            max-height: 26px;
            object-fit: contain;
            display: block;
        }

        .gift-card-title {
            margin-top: .2rem;
            font-size: 1.05rem;
            font-weight: 700;
            color: #0f1115;
        }

        .gift-card-label {
            margin-top: .6rem;
            font-size: .72rem;
            color: rgba(23, 25, 30, 0.68);
        }

        .gift-card-value {
            margin-top: .18rem;
            font-size: .88rem;
            color: #0f1115;
        }

        .gift-card-number-row {
            margin-top: .18rem;
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            justify-content: center;
        }

        .gift-copy-btn {
            border: 0;
            background: transparent;
            color: #394150;
            cursor: pointer;
            font-size: .9rem;
            padding: 0;
        }

        .ig-story-section {
            overflow: hidden;
            background:
                radial-gradient(circle at top, rgba(198, 167, 106, 0.12), transparent 32%),
                linear-gradient(180deg, rgba(10, 12, 18, 0.98) 0%, rgba(20, 34, 52, 0.92) 52%, rgba(6, 8, 12, 0.98) 100%);
        }

        .ig-story-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                linear-gradient(180deg, rgba(5, 6, 8, 0.28) 0%, rgba(5, 6, 8, 0.08) 18%, rgba(5, 6, 8, 0.48) 100%);
            pointer-events: none;
        }

        .ig-story-stage {
            position: relative;
            z-index: 1;
            width: min(100%, 430px);
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-end;
            gap: 1rem;
            padding: 2.4rem 0 2.7rem;
            text-align: center;
        }

        .ig-story-ornament {
            position: absolute;
            width: 110px;
            height: 110px;
            opacity: .16;
            pointer-events: none;
        }

        .ig-story-ornament::before,
        .ig-story-ornament::after {
            content: '';
            position: absolute;
            border: 1px solid rgba(255, 255, 255, 0.7);
            border-radius: 999px;
        }

        .ig-story-ornament::before {
            inset: 8px;
        }

        .ig-story-ornament::after {
            inset: 28px;
        }

        .ig-story-ornament.top-left {
            top: 8px;
            left: -12px;
        }

        .ig-story-ornament.top-right {
            top: 8px;
            right: -12px;
            transform: rotate(180deg);
        }

        .ig-story-ornament.bottom-left {
            bottom: 96px;
            left: -12px;
        }

        .ig-story-ornament.bottom-right {
            bottom: 96px;
            right: -12px;
            transform: rotate(180deg);
        }

        .ig-story-copy {
            max-width: 360px;
        }

        .ig-story-copy h2 {
            margin: 0;
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(2rem, 3.2vw, 2.8rem);
            line-height: .95;
            font-weight: 600;
            color: #fff;
        }

        .ig-story-copy p {
            margin: .45rem 0 0;
            font-size: .82rem;
            line-height: 1.7;
            color: rgba(255, 255, 255, 0.8);
        }

        .ig-story-preview {
            width: min(100%, 330px);
            padding: .7rem;
            border-radius: 28px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(14px);
        }

        .ig-story-frame {
            position: relative;
            overflow: hidden;
            border-radius: 22px;
            background: #0d1118;
        }

        .ig-story-frame::after {
            content: '';
            position: absolute;
            inset: 0;
            background:
                linear-gradient(180deg, rgba(7, 9, 14, 0.08) 0%, rgba(7, 9, 14, 0.02) 18%, rgba(7, 9, 14, 0.28) 100%),
                linear-gradient(180deg, rgba(198, 167, 106, 0.08) 0%, rgba(198, 167, 106, 0) 36%);
            pointer-events: none;
        }

        .ig-story-frame canvas {
            width: 100%;
            aspect-ratio: 9 / 16;
            display: block;
        }

        .ig-story-download {
            border: 0;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.96);
            color: #111;
            padding: .88rem 1.35rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .55rem;
            font-size: .8rem;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
            text-decoration: none;
            box-shadow: 0 18px 36px rgba(0, 0, 0, 0.22);
        }

        .closing-section {
            overflow: hidden;
            background-image:
                linear-gradient(180deg, rgba(8, 10, 14, 0.22) 0%, rgba(18, 24, 32, 0.38) 32%, rgba(5, 6, 8, 0.9) 100%),
                url('{{ $closingPhoto }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .closing-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                linear-gradient(180deg, rgba(5, 6, 8, 0.22) 0%, rgba(5, 6, 8, 0.08) 22%, rgba(5, 6, 8, 0.42) 100%);
            pointer-events: none;
        }

        .closing-stage {
            position: relative;
            z-index: 1;
            width: min(100%, 420px);
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            text-align: center;
            gap: 1rem;
            padding: 2.4rem 0 2rem;
            min-height: calc(var(--screen-h) - 4rem);
        }

        .closing-top {
            display: grid;
            gap: 1rem;
            justify-items: center;
            padding-top: .6rem;
        }

        .closing-copy {
            max-width: 360px;
            display: grid;
            gap: .8rem;
        }

        .closing-copy p {
            margin: 0;
            font-size: .88rem;
            line-height: 1.9;
            color: rgba(255, 255, 255, 0.86);
        }

        .closing-signoff {
            display: grid;
            gap: .25rem;
            color: rgba(255, 255, 255, 0.94);
        }

        .closing-signoff span {
            font-size: .78rem;
            letter-spacing: .05em;
        }

        .closing-signoff strong {
            font-size: 1.05rem;
            font-weight: 700;
        }

        .closing-footer {
            display: grid;
            gap: .45rem;
            justify-items: center;
        }

        .closing-made {
            font-size: .72rem;
            letter-spacing: .06em;
            color: rgba(255, 255, 255, 0.74);
        }

        .closing-powered {
            font-size: .68rem;
            letter-spacing: .18em;
            text-transform: uppercase;
            color: rgba(198, 167, 106, 0.72);
        }

        .closing-brand {
            display: inline-flex;
            align-items: center;
            gap: .65rem;
        }

        .closing-brand img {
            width: 28px;
            height: 28px;
            object-fit: contain;
            display: block;
        }

        .closing-brand span {
            font-size: .72rem;
            letter-spacing: .16em;
            text-transform: uppercase;
            color: rgba(198, 167, 106, 0.8);
        }

        .modal-overlay {
            position: fixed;
            inset: 0;
            z-index: 60;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.25rem;
            background: rgba(5, 6, 8, 0.74);
            backdrop-filter: blur(10px);
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: opacity .25s ease, visibility .25s ease;
        }

        .modal-overlay.is-open {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }

        .modal-panel {
            width: min(100%, 420px);
            max-height: min(86vh, 760px);
            overflow-y: auto;
            border-radius: 24px;
            padding: 1.3rem 1.2rem 1.2rem;
            background: linear-gradient(180deg, rgba(12, 15, 20, 0.98) 0%, rgba(17, 21, 28, 0.98) 100%);
            border: 1px solid rgba(198, 167, 106, 0.18);
            box-shadow: 0 24px 70px rgba(0, 0, 0, 0.42);
        }

        .modal-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .modal-head h3 {
            margin: 0;
            font-family: 'Cormorant Garamond', serif;
            font-size: 2rem;
            font-weight: 600;
            color: #fff;
        }

        .modal-head p {
            margin: .35rem 0 0;
            font-size: .76rem;
            line-height: 1.65;
            color: rgba(255, 255, 255, 0.68);
        }

        .modal-close {
            width: 38px;
            height: 38px;
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.05);
            color: rgba(255, 255, 255, 0.82);
            cursor: pointer;
        }

        .modal-form {
            display: grid;
            gap: .85rem;
        }

        .modal-field {
            display: grid;
            gap: .35rem;
            text-align: left;
        }

        .modal-field label {
            font-size: .72rem;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.62);
        }

        .modal-field input,
        .modal-field select,
        .modal-field textarea {
            width: 100%;
            border-radius: 14px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            background: rgba(255, 255, 255, 0.05);
            color: #fff;
            padding: .82rem .95rem;
            font: inherit;
            box-sizing: border-box;
        }

        .modal-field textarea {
            min-height: 108px;
            resize: vertical;
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            margin-top: .2rem;
        }

        .modal-submit {
            border: 0;
            border-radius: 999px;
            background: linear-gradient(135deg, rgba(198, 167, 106, 0.96), rgba(221, 192, 129, 0.94));
            color: #111;
            padding: .82rem 1.3rem;
            font-size: .78rem;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
            cursor: pointer;
        }

        .gift-account-list {
            display: grid;
            gap: .8rem;
        }

        .gift-account-item {
            padding: 1rem 1rem .95rem;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            text-align: left;
        }

        .gift-account-bank {
            font-size: .72rem;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: rgba(198, 167, 106, 0.88);
        }

        .gift-account-number {
            margin-top: .35rem;
            font-size: 1rem;
            font-weight: 700;
            color: #fff;
            word-break: break-word;
        }

        .gift-account-name {
            margin-top: .2rem;
            font-size: .78rem;
            color: rgba(255, 255, 255, 0.72);
        }

        .modal-empty {
            margin: 0;
            padding: 1rem;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.04);
            font-size: .82rem;
            line-height: 1.7;
            color: rgba(255, 255, 255, 0.7);
        }

        .rsvp-note-card {
            padding: 1rem 1rem .95rem;
            border-radius: 18px;
            background: linear-gradient(180deg, rgba(191, 239, 215, 0.94) 0%, rgba(178, 231, 205, 0.92) 100%);
            color: #173d2e;
            text-align: left;
        }

        .rsvp-note-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #166b4e;
            color: #f4fff9;
            margin-bottom: .8rem;
        }

        .rsvp-note-card h4 {
            margin: 0 0 .4rem;
            font-size: 1rem;
            line-height: 1.35;
            font-weight: 700;
        }

        .rsvp-note-card p {
            margin: 0;
            font-size: .82rem;
            line-height: 1.7;
            color: rgba(23, 61, 46, 0.9);
        }

        .rsvp-note-card small {
            display: block;
            margin-top: .8rem;
            font-size: .72rem;
            color: rgba(23, 61, 46, 0.52);
        }

        .rsvp-status-actions {
            display: grid;
            grid-template-columns: 1fr 1fr auto;
            gap: .55rem;
            margin-top: .2rem;
        }

        .rsvp-pax-control {
            display: grid;
            grid-template-columns: 1fr 48px;
            gap: .5rem;
        }

        .rsvp-pax-input {
            text-align: center;
            font-size: 1rem;
            font-weight: 600;
        }

        .rsvp-pax-buttons {
            display: grid;
            gap: .45rem;
        }

        .rsvp-pax-step {
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.06);
            color: rgba(255, 255, 255, 0.84);
            cursor: pointer;
            font-size: .85rem;
        }

        .rsvp-status-btn,
        .rsvp-close-btn {
            border: 0;
            border-radius: 12px;
            padding: .88rem .9rem;
            font-size: .82rem;
            font-weight: 700;
            cursor: pointer;
        }

        .rsvp-status-btn.attend {
            background: #31352f;
            color: #fff;
        }

        .rsvp-status-btn.decline {
            background: #ff2941;
            color: #fff;
        }

        .rsvp-close-btn {
            background: rgba(255, 255, 255, 0.92);
            color: #111;
        }

        .event-btn {
            display: inline-flex;
            align-items: center;
            gap: .42rem;
            padding: .62rem .88rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.96);
            color: #111;
            text-decoration: none;
            font-size: .68rem;
            font-weight: 600;
        }

        .countdown {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 26px;
            flex-wrap: wrap;
        }

        .cover-recipient {
            margin-top: 1rem;
            font-size: .72rem;
            color: rgba(255, 255, 255, 0.78);
            letter-spacing: .08em;
        }

        .cover-recipient strong {
            display: block;
            margin-top: .35rem;
            font-weight: 500;
            color: #f4ecde;
            letter-spacing: .04em;
        }

        .open-button {
            margin-top: 1.2rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .55rem;
            padding: .9rem 1.2rem;
            border-radius: 999px;
            border: 1px solid rgba(198, 167, 106, .34);
            background: rgba(8, 10, 14, 0.4);
            color: #f4ecde;
            font-size: .72rem;
            letter-spacing: .16em;
            text-transform: uppercase;
            cursor: pointer;
            transition: .25s ease;
            backdrop-filter: blur(14px);
        }

        .open-button:hover {
            background: rgba(198, 167, 106, 0.14);
            border-color: rgba(198, 167, 106, .52);
        }

        .count-box {
            width: 54px;
            height: 54px;
            border: 1px solid rgba(198, 167, 106, .3);
            border-radius: 14px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(14px);
            background: rgba(12, 15, 20, 0.34);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.05);
        }

        .count-box span {
            font-size: 16px;
            font-weight: 600;
            color: #f7f1e7;
        }

        .count-box small {
            font-size: 9px;
            color: var(--muted-soft);
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .particle {
            position: absolute;
            background: rgba(198, 167, 106, 0.9);
            border-radius: 50%;
            opacity: .42;
            animation: float 10s linear infinite;
            box-shadow: 0 0 12px rgba(198, 167, 106, 0.4);
        }

        .intro-section {
            position: relative;
            padding: 7rem 1.5rem 6rem;
            background:
                radial-gradient(circle at 20% 20%, rgba(198, 167, 106, 0.06), transparent 25%),
                linear-gradient(180deg, #06080c 0%, #090c12 100%);
            overflow: hidden;
        }

        .intro-grid {
            max-width: 1180px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: minmax(0, 1.05fr) minmax(340px, .95fr);
            gap: 2rem;
            align-items: center;
        }

        .intro-copy {
            position: relative;
            z-index: 1;
            padding: 1.5rem 0;
        }

        .intro-copy::before {
            content: '';
            position: absolute;
            left: -10px;
            top: 0;
            width: 1px;
            height: 100%;
            background: linear-gradient(180deg, transparent 0%, var(--gold-soft) 20%, transparent 100%);
        }

        .intro-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(1.8rem, 3vw, 2.5rem);
            font-weight: 600;
            line-height: 1.1;
            margin: 0 0 1rem;
            color: #f4ecde;
        }

        .intro-desc {
            max-width: 530px;
            margin: 0 0 1.6rem;
            color: var(--muted);
            font-size: .92rem;
            line-height: 1.9;
        }

        .verse-card {
            position: relative;
            border: 1px solid rgba(198, 167, 106, 0.18);
            border-radius: 28px;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.03), rgba(255, 255, 255, 0.015)),
                rgba(9, 12, 18, 0.86);
            padding: 2rem 2rem 2.2rem;
            box-shadow: 0 24px 80px rgba(0, 0, 0, 0.26);
            overflow: hidden;
        }

        .verse-card::before,
        .verse-card::after {
            content: '';
            position: absolute;
            border-radius: 999px;
            background: rgba(198, 167, 106, 0.12);
            filter: blur(24px);
        }

        .verse-card::before {
            width: 140px;
            height: 140px;
            top: -40px;
            right: -30px;
        }

        .verse-card::after {
            width: 110px;
            height: 110px;
            bottom: -30px;
            left: -20px;
        }

        .verse-label {
            position: relative;
            z-index: 1;
            margin-bottom: .8rem;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .28em;
            color: var(--gold);
        }

        .verse-arabic {
            position: relative;
            z-index: 1;
            font-family: 'Noto Naskh Arabic', serif;
            font-size: clamp(1.55rem, 2.3vw, 2rem);
            line-height: 2.05;
            direction: rtl;
            text-align: right;
            color: #f7f2e8;
            margin: 0 0 1.3rem;
        }

        .verse-translation {
            position: relative;
            z-index: 1;
            margin: 0;
            color: var(--muted);
            font-size: .9rem;
            line-height: 1.95;
            font-style: italic;
        }

        .verse-ref {
            position: relative;
            z-index: 1;
            margin-top: 1rem;
            font-size: .8rem;
            color: #f0dfb6;
            letter-spacing: .08em;
        }

        .ornament {
            display: inline-flex;
            align-items: center;
            gap: .8rem;
            margin-top: 1.1rem;
            color: var(--gold);
            font-size: .82rem;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .ornament::before,
        .ornament::after {
            content: '';
            width: 46px;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(198, 167, 106, 0.7), transparent);
        }

        @keyframes float {
            from {
                transform: translateY(100vh);
            }

            to {
                transform: translateY(-100vh);
            }
        }

        @media (max-width: 768px) {
            .hero-wrapper {
                display: block;
            }

            .hero-left {
                display: none;
            }

            .hero-right {
                width: 100%;
            }

            .hero-visual {
                min-height: var(--screen-h);
            }

            .hero-content {
                bottom: 40px;
            }

            .right-scroll {
                background: linear-gradient(180deg, rgba(5, 6, 8, 0.72) 0%, rgba(5, 6, 8, 1) 100%);
            }

            .right-scroll::before {
                top: -58px;
                height: 96px;
                background: linear-gradient(180deg, rgba(5, 6, 8, 0.98) 0%, rgba(5, 6, 8, 0.76) 28%, rgba(5, 6, 8, 0.26) 72%, rgba(5, 6, 8, 0) 100%);
            }

            .right-section {
                min-height: var(--screen-h);
                padding: 2rem 1.25rem;
                align-items: flex-end;
            }

            .right-section:first-child {
                margin-top: 0;
            }

            .right-card {
                max-width: none;
            }

            .blessing-card {
                margin: 0 auto;
                padding-bottom: 4rem;
                max-width: 340px;
            }

            .event-stack {
                max-width: 100%;
                margin: 0 auto;
                padding: 2.4rem 0;
            }

            .story-stage {
                padding: 2.25rem 1.2rem 2.7rem;
            }

            .story-line {
                min-height: 140px;
            }

            .gallery-stage {
                width: 100%;
                max-width: 340px;
                padding: 2.2rem 0 2.2rem;
                gap: .9rem;
            }

            .gallery-heading h2 {
                font-size: 2.3rem;
            }

            .gallery-heading p {
                font-size: .8rem;
            }

            .gallery-thumb {
                width: 62px;
                height: 50px;
                border-radius: 10px;
            }

            .gallery-thumb-glow {
                width: 22px;
                height: 22px;
            }

            .gallery-feature {
                border-radius: 22px;
            }

            .rsvp-stage {
                width: 100%;
                max-width: 340px;
                padding: 2.2rem 0 2.4rem;
                gap: 1rem;
            }

            .rsvp-title,
            .gift-title {
                font-size: 2.3rem;
            }

            .rsvp-action-card,
            .gift-action-card {
                padding: 1.2rem 1rem;
                border-radius: 16px;
            }

            .wishes-title {
                font-size: 2.3rem;
            }

            .wishes-textarea {
                min-height: 132px;
            }

            .wishes-list {
                max-height: 230px;
            }

            .rsvp-pax-control {
                grid-template-columns: 1fr 44px;
            }

            .gift-stage {
                width: 100%;
                max-width: 340px;
                padding: 2.2rem 0 2.4rem;
            }

            .gift-trigger {
                border-radius: 16px;
                padding: .95rem .95rem .9rem;
            }

            .gift-card {
                border-radius: 18px;
                padding: 1.1rem 1rem 1rem;
            }

            .ig-story-stage {
                width: 100%;
                max-width: 340px;
                padding: 2.2rem 0 2.4rem;
            }

            .ig-story-preview {
                width: min(100%, 300px);
                padding: .65rem;
                border-radius: 24px;
            }

            .ig-story-frame {
                border-radius: 18px;
            }

            .closing-stage {
                width: 100%;
                max-width: 340px;
                padding: 2.1rem 0 1.8rem;
                min-height: calc(var(--screen-h) - 4rem);
            }

            .modal-panel {
                border-radius: 22px;
                padding: 1.15rem 1rem 1rem;
            }

            .intro-section {
                padding: 4.5rem 1.25rem 4.5rem;
            }

            .intro-grid {
                grid-template-columns: 1fr;
                gap: 1.4rem;
            }

            .intro-copy::before {
                display: none;
            }

            .verse-card {
                padding: 1.5rem 1.25rem 1.7rem;
                border-radius: 22px;
            }
        }
    </style>
</head>

<body class="is-locked">

    <section class="hero-wrapper">
        <div class="hero-left">
            <div class="hero-text-left">
                <p class="eyebrow">{{ $invitation->title }}</p>
                <h1>{{ $displayTitle }}</h1>
                @if ($invitation->event_date)
                    <p class="hero-date">{{ $invitation->event_date->translatedFormat('l, d F Y') }}</p>
                @endif
                @if ($guestName)
                    <p class="guest-line">Kepada Yth. {{ $guestName }}</p>
                @endif
            </div>

            @for ($i = 0; $i < 25; $i++)
                <div class="particle"
                    style="
                        width:{{ rand(2, 6) }}px;
                        height:{{ rand(2, 6) }}px;
                        left:{{ rand(0, 100) }}%;
                        animation-duration:{{ rand(8, 20) }}s;
                        animation-delay:-{{ rand(0, 20) }}s;
                    ">
                </div>
            @endfor
        </div>

        <div class="hero-right">
            <div class="hero-visual">
                <img src="{{ $coverPhoto }}" alt="{{ $displayTitle }}">

                <div class="hero-overlay"></div>

                <div class="hero-content">
                    <div class="hero-content-inner">
                        <p class="eyebrow reveal is-visible">Wedding Invitation</p>
                        <h1 class="reveal is-visible reveal-delay-1">{{ $displayTitle }}</h1>

                        @if ($guestName)
                            <div class="cover-recipient reveal is-visible reveal-delay-2">
                                Kepada Yth.
                                <strong>{{ $guestName }}</strong>
                            </div>
                        @endif

                        <div class="countdown reveal is-visible reveal-delay-2">
                            <div class="count-box">
                                <span id="days">00</span>
                                <small>Days</small>
                            </div>
                            <div class="count-box">
                                <span id="hours">00</span>
                                <small>Hr</small>
                            </div>
                            <div class="count-box">
                                <span id="minutes">00</span>
                                <small>Min</small>
                            </div>
                            <div class="count-box">
                                <span id="seconds">00</span>
                                <small>Sec</small>
                            </div>
                        </div>

                        <button type="button" class="open-button reveal is-visible reveal-delay-3"
                            id="openInvitationButton" onclick="openInvitation()">
                            <i class="fa-regular fa-envelope-open"></i>
                            <span>Open Invitation</span>
                        </button>

                    </div>
                </div>
            </div>

            <div class="right-scroll">
                <section class="right-section opening-section">
                    <div class="right-card reveal">
                        <div class="verse-label reveal">Surat Ar-Rum Ayat 21</div>
                        <p class="verse-arabic reveal reveal-delay-1">
                            وَمِنْ آيَاتِهِ أَنْ خَلَقَ لَكُمْ مِنْ أَنْفُسِكُمْ أَزْوَاجًا لِتَسْكُنُوا
                            إِلَيْهَا وَجَعَلَ بَيْنَكُمْ مَوَدَّةً وَرَحْمَةً ۚ إِنَّ فِي ذَٰلِكَ لَآيَاتٍ
                            لِقَوْمٍ يَتَفَكَّرُونَ
                        </p>
                        <p class="verse-translation reveal reveal-delay-2">
                            “Dan di antara tanda-tanda kebesaran-Nya ialah Dia menciptakan pasangan-pasangan untukmu
                            dari jenismu sendiri, agar kamu cenderung dan merasa tenteram kepadanya, dan Dia
                            menjadikan di antaramu rasa kasih dan sayang.”
                        </p>
                        <div class="verse-ref reveal reveal-delay-3">QS. Ar-Rum: 21</div>
                    </div>
                </section>
                <section class="right-section blessing-section">
                    <div class="blessing-card">
                        <div class="ornament-dots reveal">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                        <p class="blessing-text reveal reveal-delay-1">
                            We ask for your prayers and blessings for our marriage
                        </p>
                        <div class="blessing-line reveal reveal-delay-2"></div>
                    </div>
                </section>

                <section class="right-section person-section bride-section">
                    <div class="person-card">
                        <h2 class="person-name reveal">{{ $invitation->bride_name ?: 'Mempelai Wanita' }}</h2>
                        <div class="person-role reveal reveal-delay-1">The Bride</div>

                        @if ($invitation->bride_parent_name)
                            <p class="person-parent reveal reveal-delay-2">
                                Putri dari {{ $invitation->bride_parent_name }}
                            </p>
                        @endif

                        @if ($invitation->bride_instagram)
                            <a href="{{ str_starts_with($invitation->bride_instagram, 'http') ? $invitation->bride_instagram : 'https://instagram.com/' . ltrim($invitation->bride_instagram, '@') }}"
                                target="_blank" rel="noopener noreferrer" class="social-pill reveal reveal-delay-3">
                                <i class="fa-brands fa-instagram"></i>
                                <span>Instagram</span>
                            </a>
                        @endif
                    </div>
                </section>

                <section class="right-section person-section groom-section">
                    <div class="person-card">
                        <h2 class="person-name reveal">{{ $invitation->groom_name ?: 'Mempelai Pria' }}</h2>
                        <div class="person-role reveal reveal-delay-1">The Groom</div>

                        @if ($invitation->groom_parent_name)
                            <p class="person-parent reveal reveal-delay-2">
                                Putra dari {{ $invitation->groom_parent_name }}
                            </p>
                        @endif

                        @if ($invitation->groom_instagram)
                            <a href="{{ str_starts_with($invitation->groom_instagram, 'http') ? $invitation->groom_instagram : 'https://instagram.com/' . ltrim($invitation->groom_instagram, '@') }}"
                                target="_blank" rel="noopener noreferrer" class="social-pill reveal reveal-delay-3">
                                <i class="fa-brands fa-instagram"></i>
                                <span>Instagram</span>
                            </a>
                        @endif
                    </div>
                </section>

                <section class="right-section event-section">
                    <div class="event-stack">
                        <div class="event-header">
                            <h2 class="reveal">Event</h2>
                            <p class="reveal reveal-delay-1">Kami mengundang Anda untuk hadir
                                dalam acara pernikahan kami yang akan diselenggarakan pada:</p>
                        </div>

                        <div class="event-list">
                            @foreach ($eventItems as $eventItem)
                                @php
                                    $eventDate = $eventItem->event_date
                                        ? \Carbon\Carbon::parse($eventItem->event_date)
                                        : optional($invitation->event_date);
                                    $startTime = $eventItem->event_time
                                        ? \Carbon\Carbon::parse($eventItem->event_time)->format('H:i')
                                        : null;
                                    $endTime = $eventItem->event_end_time
                                        ? \Carbon\Carbon::parse($eventItem->event_end_time)->format('H:i')
                                        : null;
                                    $eventMapsUrl =
                                        $eventItem->venue_maps_url ?:
                                        $invitation->google_maps_url ?:
                                        $invitation->maps_deep_link;
                                    $calendarUrl =
                                        $eventDate && $startTime
                                            ? 'https://calendar.google.com/calendar/render?action=TEMPLATE' .
                                                '&text=' .
                                                urlencode($eventItem->event_name ?: $invitation->title) .
                                                '&dates=' .
                                                $eventDate->format('Ymd') .
                                                'T' .
                                                \Carbon\Carbon::parse($startTime)->format('His') .
                                                '/' .
                                                $eventDate->format('Ymd') .
                                                'T' .
                                                ($endTime
                                                    ? \Carbon\Carbon::parse($endTime)->format('His')
                                                    : \Carbon\Carbon::parse($startTime)->addHours(2)->format('His')) .
                                                '&location=' .
                                                urlencode(
                                                    trim(
                                                        ($eventItem->venue_name ?: '') .
                                                            ' ' .
                                                            ($eventItem->venue_address ?: ''),
                                                    ),
                                                )
                                            : '#';
                                @endphp
                                <div class="event-item">
                                    <h3 class="event-name reveal">{{ $eventItem->event_name ?: 'Wedding Event' }}</h3>

                                    @if ($eventDate)
                                        <div class="event-date-grid reveal reveal-delay-1">
                                            <div class="small">
                                                <div>{{ $eventDate->translatedFormat('M') }}</div>
                                            </div>
                                            <div class="day">{{ $eventDate->format('d') }}</div>
                                            <div class="small">
                                                <div>{{ $eventDate->format('Y') }}</div>
                                            </div>
                                        </div>
                                    @endif

                                    @if ($startTime)
                                        <p class="event-time reveal reveal-delay-1">
                                            {{ $startTime }}{{ $endTime ? ' - ' . $endTime : '' }} WIB
                                        </p>
                                    @endif

                                    @if ($eventItem->venue_name)
                                        <p class="event-venue reveal reveal-delay-2">{{ $eventItem->venue_name }}</p>
                                    @endif

                                    @if ($eventItem->venue_address)
                                        <p class="event-address reveal reveal-delay-2">{{ $eventItem->venue_address }}
                                        </p>
                                    @endif

                                    <div class="event-actions reveal reveal-delay-3">
                                        @if ($calendarUrl !== '#')
                                            <a href="{{ $calendarUrl }}" target="_blank" rel="noopener noreferrer"
                                                class="event-btn">
                                                <i class="fa-regular fa-calendar-plus"></i>
                                                <span>Save The Date</span>
                                            </a>
                                        @endif
                                        @if ($eventMapsUrl)
                                            <a href="{{ $eventMapsUrl }}" target="_blank" rel="noopener noreferrer"
                                                class="event-btn">
                                                <i class="fa-solid fa-link"></i>
                                                <span>Map Navigation</span>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>

                @foreach ($loveStoryItems as $story)
                    <section class="right-section love-story-section"
                        style="background-image: url('{{ $story->background }}');">
                        <div class="story-stage">
                            <div class="story-head {{ $loop->first ? '' : 'is-continuation' }}">
                                @if ($loop->first)
                                    <h2 class="reveal">Love Story</h2>
                                @endif
                            </div>

                            <div
                                class="story-line reveal reveal-delay-1 {{ $loop->last ? 'is-last' : '' }} {{ $loop->first ? '' : 'is-continuation' }}">
                            </div>

                            <div class="story-copy">
                                @if ($story->year)
                                    <div class="story-year reveal reveal-delay-1">{{ $story->year }}</div>
                                @endif

                                <h3 class="story-title reveal reveal-delay-2">
                                    {{ $story->title ?: 'Our Journey' }}
                                </h3>

                                @if ($story->description)
                                    <p class="story-description reveal reveal-delay-3">
                                        {{ $story->description }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </section>
                @endforeach

                @if ($galleryItems->count())
                    <section class="right-section gallery-section">
                        <div class="gallery-stage">
                            <div class="gallery-heading">
                                <h2 class="reveal">Galeri</h2>
                                <p class="reveal reveal-delay-1">Setiap potret menceritakan kisah cinta kami yang abadi
                                </p>
                            </div>

                            <div class="gallery-thumb-row reveal reveal-delay-2">
                                <div class="gallery-thumb-glow"></div>
                                <div class="gallery-thumbs" id="galleryThumbs">
                                    @foreach ($galleryItems as $photo)
                                        <button type="button"
                                            class="gallery-thumb {{ $loop->first ? 'is-active' : '' }}"
                                            data-gallery-target="{{ $photo->url }}"
                                            data-gallery-caption="{{ $photo->caption ?: 'Momen istimewa kami' }}"
                                            aria-label="Pilih foto galeri {{ $loop->iteration }}">
                                            <img src="{{ $photo->url }}"
                                                alt="{{ $photo->caption ?: $displayTitle }}">
                                        </button>
                                    @endforeach
                                </div>
                                <div class="gallery-thumb-glow"></div>
                            </div>

                            <div class="gallery-feature reveal reveal-delay-3">
                                <img id="galleryFeaturedImage" src="{{ $galleryItems->first()->url }}"
                                    alt="{{ $galleryItems->first()->caption ?: $displayTitle }}">
                            </div>

                            <p class="gallery-caption reveal reveal-delay-3" id="galleryCaption">
                                {{ $galleryItems->first()->caption ?: 'Momen istimewa kami' }}
                            </p>
                        </div>
                    </section>
                @endif

                <section class="right-section rsvp-section" id="wishes-section">
                    <div class="rsvp-stage">
                        <h2 class="wishes-title reveal">Wishes</h2>

                        <button type="button" class="rsvp-action-card reveal reveal-delay-1"
                            data-open-modal="rsvpModal">
                            <i class="fa-solid fa-user-check"></i>
                            <span>Konfirmasi Kehadiran</span>
                        </button>

                        <form method="POST" action="{{ route('invitation.wish', $invitation->slug) }}"
                            class="wishes-form reveal reveal-delay-2">
                            @csrf
                            <input type="hidden" name="redirect_anchor" value="wishes-section">
                            <input type="text" name="name" class="wishes-input" placeholder="Nama Anda"
                                value="{{ old('name', $guestName) }}" required>
                            <textarea name="message" class="wishes-textarea" placeholder="Write your wishes" required>{{ old('message') }}</textarea>
                            <button type="submit" class="wishes-submit">
                                <i class="fa-regular fa-comment-dots"></i>
                                <span>Send</span>
                            </button>
                        </form>

                        <div class="wishes-list reveal reveal-delay-3">
                            @forelse ($invitation->wishes as $wish)
                                <div class="wish-item">
                                    <div class="wish-name">{{ $wish->name }}</div>
                                    <div class="wish-date">
                                        <i class="fa-regular fa-clock"></i>
                                        {{ optional($wish->created_at)->translatedFormat('l, d F Y H:i') }}
                                    </div>
                                    <div class="wish-message">{{ $wish->message }}</div>
                                </div>
                            @empty
                                <div class="wish-item">
                                    <div class="wish-message">Belum ada ucapan. Jadilah yang pertama mengirimkan doa
                                        terbaik untuk kedua mempelai.</div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </section>

                <section class="right-section gift-section" id="gift-section">
                    <div class="gift-stage">
                        <div class="gift-intro">
                            <h2 class="gift-title reveal">Gift</h2>
                            <p class="reveal reveal-delay-1">Doa restu dan kehadiran Anda sudah menjadi kebahagiaan
                                tersendiri bagi kami. Namun, apabila Anda ingin memberikan tanda kasih, kami telah
                                menyediakan fitur berikut.</p>
                        </div>

                        <button type="button" class="gift-trigger reveal reveal-delay-2"
                            data-open-modal="giftModal">
                            <i class="fa-solid fa-gift"></i>
                            <span>Kirim Kado</span>
                        </button>

                        @if ($invitation->gift_address)
                            <div class="gift-address-wrap reveal reveal-delay-3">
                                <h4>Kirim Kado Ke Alamat</h4>
                                <p>Anda juga dapat mengirimkan melalui alamat berikut</p>
                                <div class="gift-address-box">{{ $invitation->gift_address }}</div>
                            </div>
                        @endif
                    </div>
                </section>

                @if ($igStoryPhoto)
                    <section class="right-section ig-story-section" id="ig-story-section">
                        <div class="ig-story-stage">
                            <div class="ig-story-ornament top-left"></div>
                            <div class="ig-story-ornament top-right"></div>
                            <div class="ig-story-ornament bottom-left"></div>
                            <div class="ig-story-ornament bottom-right"></div>

                            <div class="ig-story-copy">
                                <h2 class="reveal">Instagram Story</h2>
                                <p class="reveal reveal-delay-1">Bagikan momen bahagia ini melalui template story yang
                                    telah kami siapkan.</p>
                            </div>

                            <div class="ig-story-preview reveal reveal-delay-2">
                                <div class="ig-story-frame">
                                    <canvas id="igStoryCanvas" aria-label="Preview Instagram Story"></canvas>
                                </div>
                            </div>

                            <button type="button" id="downloadIgStory"
                                class="ig-story-download reveal reveal-delay-3">
                                <i class="fa-solid fa-download"></i>
                                <span>Download Story</span>
                            </button>
                        </div>
                    </section>
                @endif

                <section class="right-section closing-section" id="closing-section">
                    <div class="closing-stage">
                        <div class="closing-top">
                            <div class="closing-copy">
                                <p class="reveal">{{ $invitation->closing_text }}</p>
                            </div>

                            <div class="closing-signoff reveal reveal-delay-1">
                                <span>Best Regards</span>
                                <strong>{{ $displayTitle }}</strong>
                            </div>
                        </div>

                        <div class="closing-footer reveal reveal-delay-2">
                            <div class="closing-made">Made with ❤ somewhere in the world</div>
                            <div class="closing-powered">Powered by</div>
                            <div class="closing-brand">
                                <img src="{{ $poweredLogo }}" alt="Janji Suci Kita">
                                <span>janjisucikita.com</span>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </section>

    <div class="modal-overlay" id="rsvpModal" aria-hidden="true">
        <div class="modal-panel">
            <div class="modal-head">
                <div>
                    <h3>RSVP</h3>
                    <p>Konfirmasikan kehadiran Anda untuk membantu kami mempersiapkan momen terbaik dengan lebih hangat
                        dan tertata.</p>
                </div>
                <button type="button" class="modal-close" data-close-modal="rsvpModal" aria-label="Tutup RSVP">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('invitation.rsvp', $invitation->slug) }}" class="modal-form">
                @csrf
                @if (!empty($guest?->id))
                    <input type="hidden" name="guest_id" value="{{ $guest->id }}">
                @endif
                <input type="hidden" name="name" value="{{ old('name', $guestName ?: 'Tamu Undangan') }}">
                <input type="hidden" name="status" id="rsvp_status_input"
                    value="{{ old('status', 'attending') }}">

                <div class="rsvp-note-card">
                    <div class="rsvp-note-icon">
                        <i class="fa-solid fa-bookmark"></i>
                    </div>
                    <h4>Terima kasih banyak telah memberikan konfirmasi kehadiran</h4>
                    <p>Kami sangat senang dan menantikan kehadiran Anda di acara kami. Semoga acara ini menjadi momen
                        yang berkesan untuk kita semua.</p>
                    <small>Anda akan hadir: {{ (int) old('pax', 1) }} orang</small>
                </div>

                <div class="modal-field">
                    <label for="rsvp_pax">Jumlah Pax</label>
                    <div class="rsvp-pax-control">
                        <input id="rsvp_pax" type="number" name="pax" min="1" max="10"
                            step="1" value="{{ (int) old('pax', 1) }}" class="rsvp-pax-input" required>
                        <div class="rsvp-pax-buttons">
                            <button type="button" class="rsvp-pax-step" onclick="adjustRsvpPax(1)"
                                aria-label="Tambah Pax">
                                <i class="fa-solid fa-chevron-up"></i>
                            </button>
                            <button type="button" class="rsvp-pax-step" onclick="adjustRsvpPax(-1)"
                                aria-label="Kurangi Pax">
                                <i class="fa-solid fa-chevron-down"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="rsvp-status-actions">
                    <button type="submit" class="rsvp-status-btn attend"
                        onclick="setRsvpStatus('attending')">Hadir</button>
                    <button type="submit" class="rsvp-status-btn decline"
                        onclick="setRsvpStatus('not_attending')">Tidak Hadir</button>
                    <button type="button" class="rsvp-close-btn" data-close-modal="rsvpModal">Tutup</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-overlay" id="giftModal" aria-hidden="true">
        <div class="modal-panel">
            <div class="modal-head">
                <div>
                    <h3>Kirim Kado</h3>
                    <p>Jika berkenan, Anda dapat mengirim tanda kasih melalui rekening berikut dengan tetap menjaga
                        kehangatan dan kesederhanaan momen istimewa ini.</p>
                </div>
                <button type="button" class="modal-close" data-close-modal="giftModal" aria-label="Tutup Gift">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            @if ($giftAccounts->count())
                <div class="gift-card-list">
                    @foreach ($giftAccounts as $account)
                        <div class="gift-card">
                            <div class="gift-card-bank">
                                <img src="{{ $account->logo }}" alt="{{ $account->bank_name ?: 'Bank' }}">
                            </div>
                            <div class="gift-card-title">{{ $account->bank_name ?: 'Bank Account' }}</div>

                            <div class="gift-card-label">Account Name</div>
                            <div class="gift-card-value">{{ $account->account_name ?: $displayTitle }}</div>

                            <div class="gift-card-label">Account Number</div>
                            <div class="gift-card-number-row">
                                <div class="gift-card-value">{{ $account->account_number ?: '-' }}</div>
                                @if (!empty($account->account_number))
                                    <button type="button" class="gift-copy-btn"
                                        onclick="copyGiftAccount('{{ $account->account_number }}')"
                                        aria-label="Salin nomor rekening">
                                        <i class="fa-regular fa-copy"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="modal-empty">Informasi rekening hadiah belum tersedia saat ini.</p>
            @endif
        </div>
    </div>

    @if ($invitation->music_url)
        <audio id="bgMusic" loop preload="auto" playsinline style="display:none"></audio>
    @endif

    <script>
        const targetDate = new Date(@json($targetDate)).getTime();
        let invitationOpened = false;
        const musicUrl = @json($invitation->music_signed_url ?? null);
        const musicFallbackUrl = @json($musicDirectUrl);
        let galleryInterval = null;
        const igStoryPhotoSrc = @json($igStoryPhoto);
        const igStoryCoupleName = @json($displayTitle);
        const igStoryDate = @json(optional($invitation->event_date)?->format('d . m . Y') ?: '');

        function updateCountdown() {
            const now = new Date().getTime();
            const distance = targetDate - now;

            if (Number.isNaN(targetDate) || distance <= 0) {
                document.getElementById('days').textContent = '00';
                document.getElementById('hours').textContent = '00';
                document.getElementById('minutes').textContent = '00';
                document.getElementById('seconds').textContent = '00';
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            document.getElementById('days').textContent = String(days).padStart(2, '0');
            document.getElementById('hours').textContent = String(hours).padStart(2, '0');
            document.getElementById('minutes').textContent = String(minutes).padStart(2, '0');
            document.getElementById('seconds').textContent = String(seconds).padStart(2, '0');
        }

        updateCountdown();
        setInterval(updateCountdown, 1000);

        function initRevealAnimations() {
            const items = document.querySelectorAll('.reveal');
            if (!items.length) {
                return;
            }

            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                    }
                });
            }, {
                threshold: 0.22,
                rootMargin: '0px 0px -8% 0px'
            });

            items.forEach((item) => {
                if (!item.classList.contains('is-visible')) {
                    observer.observe(item);
                }
            });
        }

        function initGallerySection() {
            const featuredImage = document.getElementById('galleryFeaturedImage');
            const caption = document.getElementById('galleryCaption');
            const thumbs = Array.from(document.querySelectorAll('#galleryThumbs .gallery-thumb'));

            if (!featuredImage || !thumbs.length) {
                return;
            }

            let activeIndex = thumbs.findIndex((thumb) => thumb.classList.contains('is-active'));
            activeIndex = activeIndex >= 0 ? activeIndex : 0;

            const setGalleryItem = (index) => {
                const thumb = thumbs[index];
                if (!thumb) {
                    return;
                }

                const target = thumb.dataset.galleryTarget;
                const text = thumb.dataset.galleryCaption || 'Momen istimewa kami';

                featuredImage.src = target;
                featuredImage.alt = text;

                if (caption) {
                    caption.textContent = text;
                }

                thumbs.forEach((item) => item.classList.remove('is-active'));
                thumb.classList.add('is-active');
                activeIndex = index;
            };

            const restartGalleryAutoPlay = () => {
                if (galleryInterval) {
                    window.clearInterval(galleryInterval);
                }

                if (thumbs.length < 2) {
                    return;
                }

                galleryInterval = window.setInterval(() => {
                    const nextIndex = (activeIndex + 1) % thumbs.length;
                    setGalleryItem(nextIndex);
                }, 4200);
            };

            thumbs.forEach((thumb, index) => {
                thumb.addEventListener('click', () => {
                    setGalleryItem(index);
                    restartGalleryAutoPlay();
                });
            });

            setGalleryItem(activeIndex);
            restartGalleryAutoPlay();
        }

        function loadBrowserFont(name, url) {
            if (typeof FontFace === 'undefined') {
                return Promise.resolve(null);
            }

            const font = new FontFace(name, `url(${url})`);
            return font.load().then((loaded) => {
                document.fonts.add(loaded);
                return loaded;
            }).catch(() => null);
        }

        function roundedRectPath(ctx, x, y, width, height, radius) {
            ctx.beginPath();
            ctx.moveTo(x + radius, y);
            ctx.lineTo(x + width - radius, y);
            ctx.quadraticCurveTo(x + width, y, x + width, y + radius);
            ctx.lineTo(x + width, y + height - radius);
            ctx.quadraticCurveTo(x + width, y + height, x + width - radius, y + height);
            ctx.lineTo(x + radius, y + height);
            ctx.quadraticCurveTo(x, y + height, x, y + height - radius);
            ctx.lineTo(x, y + radius);
            ctx.quadraticCurveTo(x, y, x + radius, y);
            ctx.closePath();
        }

        function renderIgStoryCanvas() {
            const canvas = document.getElementById('igStoryCanvas');
            if (!canvas || !igStoryPhotoSrc) {
                return;
            }

            const CANVAS_W = 1080;
            const CANVAS_H = 1920;
            const websiteUrl = 'janjisucikita.com';

            canvas.width = CANVAS_W;
            canvas.height = CANVAS_H;
            const ctx = canvas.getContext('2d');
            if (!ctx) {
                return;
            }

            Promise.all([
                loadBrowserFont('GreatVibesCodex',
                    'https://fonts.gstatic.com/s/greatvibes/v18/RWmMoKWR9v4ksMfaWd_JN9XFiaQ.woff2'),
                loadBrowserFont('InterCodex',
                    'https://fonts.gstatic.com/s/inter/v18/UcCO3FwrK3iLTeHuS_nVMrMxCp50SjIw2boKoduKmMEVuLyfAZ9hiA.woff2'
                ),
            ]).finally(() => {
                const img = new Image();
                img.crossOrigin = 'anonymous';
                img.onload = function() {
                    const imgRatio = img.width / img.height;
                    const canvasRatio = CANVAS_W / CANVAS_H;
                    let sx = 0;
                    let sy = 0;
                    let sw = img.width;
                    let sh = img.height;

                    if (imgRatio > canvasRatio) {
                        sw = img.height * canvasRatio;
                        sx = (img.width - sw) / 2;
                    } else {
                        sh = img.width / canvasRatio;
                        sy = (img.height - sh) / 2;
                    }

                    ctx.clearRect(0, 0, CANVAS_W, CANVAS_H);
                    ctx.drawImage(img, sx, sy, sw, sh, 0, 0, CANVAS_W, CANVAS_H);

                    const gradStart = CANVAS_H * 0.45;
                    const grad = ctx.createLinearGradient(0, gradStart, 0, CANVAS_H);
                    grad.addColorStop(0, 'rgba(55, 94, 124, 0)');
                    grad.addColorStop(0.34, 'rgba(47, 78, 104, 0.50)');
                    grad.addColorStop(0.62, 'rgba(28, 52, 76, 0.82)');
                    grad.addColorStop(1, 'rgba(13, 26, 40, 0.94)');
                    ctx.fillStyle = grad;
                    ctx.fillRect(0, gradStart, CANVAS_W, CANVAS_H - gradStart);

                    const glow = ctx.createRadialGradient(CANVAS_W / 2, CANVAS_H * 0.78, 40, CANVAS_W / 2,
                        CANVAS_H * 0.78, 520);
                    glow.addColorStop(0, 'rgba(198, 167, 106, 0.15)');
                    glow.addColorStop(1, 'rgba(198, 167, 106, 0)');
                    ctx.fillStyle = glow;
                    ctx.fillRect(0, CANVAS_H * 0.5, CANVAS_W, CANVAS_H * 0.5);

                    ctx.textAlign = 'center';
                    ctx.fillStyle = '#ffffff';
                    ctx.shadowColor = 'rgba(0,0,0,0.35)';
                    ctx.shadowBlur = 10;
                    ctx.font = '700 74px GreatVibesCodex, "Times New Roman", serif';
                    ctx.fillText(igStoryCoupleName, CANVAS_W / 2, CANVAS_H * 0.665);
                    ctx.shadowBlur = 0;

                    ctx.font = '300 34px InterCodex, Arial, sans-serif';
                    ctx.fillStyle = 'rgba(255,255,255,0.9)';
                    ctx.fillText(igStoryDate, CANVAS_W / 2, CANVAS_H * 0.71);

                    ctx.font = '400 28px InterCodex, Arial, sans-serif';
                    ctx.fillStyle = 'rgba(255,255,255,0.82)';
                    ctx.fillText('Wish', CANVAS_W / 2, CANVAS_H * 0.745);

                    const boxMargin = 60;
                    const boxTop = CANVAS_H * 0.765;
                    const boxWidth = CANVAS_W - (boxMargin * 2);
                    const boxHeight = 300;
                    roundedRectPath(ctx, boxMargin, boxTop, boxWidth, boxHeight, 22);
                    ctx.fillStyle = 'rgba(248, 244, 236, 0.94)';
                    ctx.fill();

                    const footerY = CANVAS_H - 66;
                    const iconX = boxMargin;
                    const iconY = footerY - 7;
                    ctx.strokeStyle = 'rgba(255,255,255,0.72)';
                    ctx.lineWidth = 2;
                    ctx.beginPath();
                    ctx.arc(iconX, iconY, 11, 0, Math.PI * 2);
                    ctx.stroke();
                    ctx.beginPath();
                    ctx.moveTo(iconX - 9, iconY);
                    ctx.lineTo(iconX + 9, iconY);
                    ctx.moveTo(iconX, iconY - 9);
                    ctx.lineTo(iconX, iconY + 9);
                    ctx.stroke();

                    ctx.textAlign = 'left';
                    ctx.font = '400 24px InterCodex, Arial, sans-serif';
                    ctx.fillStyle = 'rgba(255,255,255,0.78)';
                    ctx.fillText(websiteUrl, boxMargin + 24, footerY);
                };

                img.onerror = function() {
                    ctx.fillStyle = '#0d1118';
                    ctx.fillRect(0, 0, CANVAS_W, CANVAS_H);
                    ctx.fillStyle = '#ffffff';
                    ctx.font = '400 32px Arial';
                    ctx.textAlign = 'center';
                    ctx.fillText('Gagal memuat template story', CANVAS_W / 2, CANVAS_H / 2);
                };

                img.src = igStoryPhotoSrc;
            });
        }

        function initIgStoryDownload() {
            const button = document.getElementById('downloadIgStory');
            const canvas = document.getElementById('igStoryCanvas');
            if (!button || !canvas) {
                return;
            }

            button.addEventListener('click', () => {
                const link = document.createElement('a');
                const safeName = (igStoryCoupleName || 'instagram-story')
                    .toLowerCase()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-|-$/g, '');

                link.download = `${safeName || 'instagram-story'}-story-hd.jpg`;
                link.href = canvas.toDataURL('image/jpeg', 0.94);
                link.click();
            });
        }

        function initModalControls() {
            const openButtons = document.querySelectorAll('[data-open-modal]');
            const closeButtons = document.querySelectorAll('[data-close-modal]');

            const openModal = (id) => {
                const modal = document.getElementById(id);
                if (!modal) {
                    return;
                }

                modal.classList.add('is-open');
                modal.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
            };

            const closeModal = (id) => {
                const modal = document.getElementById(id);
                if (!modal) {
                    return;
                }

                modal.classList.remove('is-open');
                modal.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = invitationOpened ? '' : 'hidden';
            };

            openButtons.forEach((button) => {
                button.addEventListener('click', () => openModal(button.dataset.openModal));
            });

            closeButtons.forEach((button) => {
                button.addEventListener('click', () => closeModal(button.dataset.closeModal));
            });

            document.querySelectorAll('.modal-overlay').forEach((modal) => {
                modal.addEventListener('click', (event) => {
                    if (event.target === modal) {
                        closeModal(modal.id);
                    }
                });
            });

            document.addEventListener('keydown', (event) => {
                if (event.key !== 'Escape') {
                    return;
                }

                document.querySelectorAll('.modal-overlay.is-open').forEach((modal) => {
                    closeModal(modal.id);
                });
            });
        }

        function setRsvpStatus(status) {
            const statusInput = document.getElementById('rsvp_status_input');
            if (statusInput) {
                statusInput.value = status;
            }
        }

        function adjustRsvpPax(delta) {
            const paxInput = document.getElementById('rsvp_pax');
            if (!paxInput) {
                return;
            }

            const current = parseInt(paxInput.value || '1', 10);
            const next = Math.min(10, Math.max(1, current + delta));
            paxInput.value = String(next);
            paxInput.dispatchEvent(new Event('input', {
                bubbles: true
            }));
        }

        async function copyGiftAccount(accountNumber) {
            if (!accountNumber) {
                return;
            }

            try {
                await navigator.clipboard.writeText(accountNumber);
            } catch (error) {
                console.warn('Failed to copy account number', error);
            }
        }

        function initRsvpModal() {
            const paxSelect = document.getElementById('rsvp_pax');
            const noteCount = document.querySelector('.rsvp-note-card small');

            if (!paxSelect || !noteCount) {
                return;
            }

            const updatePaxNote = () => {
                const normalized = Math.min(10, Math.max(1, parseInt(paxSelect.value || '1', 10) || 1));
                paxSelect.value = String(normalized);
                noteCount.textContent = `Anda akan hadir: ${normalized} orang`;
            };

            paxSelect.addEventListener('input', updatePaxNote);
            paxSelect.addEventListener('change', updatePaxNote);
            updatePaxNote();
        }

        async function tryPlayAudio(audio, url) {
            if (!audio || !url) {
                return false;
            }

            try {
                audio.pause();
                audio.src = url;
                audio.load();
                audio.currentTime = 0;
                audio.volume = 1;
                audio.muted = false;
                const playPromise = audio.play();
                if (playPromise && typeof playPromise.then === 'function') {
                    await playPromise;
                }
                return true;
            } catch (error) {
                console.warn('Audio play failed for', url, error);
                return false;
            }
        }

        function ensureBackgroundAudio() {
            const audio = document.getElementById('bgMusic');
            if (!audio) {
                return null;
            }

            audio.loop = true;
            audio.preload = 'auto';
            audio.setAttribute('playsinline', '');
            return audio;
        }

        async function openInvitation() {
            if (invitationOpened) {
                return;
            }

            invitationOpened = true;
            document.body.classList.remove('is-locked');

            const audio = ensureBackgroundAudio();
            if (audio) {
                let played = false;

                if (musicUrl) {
                    played = await tryPlayAudio(audio, musicUrl);
                }

                if (!played && musicFallbackUrl) {
                    played = await tryPlayAudio(audio, musicFallbackUrl);
                }
            }

            const nextSection = document.querySelector('.right-scroll');
            if (nextSection) {
                nextSection.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start',
                });
            }
        }

        initRevealAnimations();
        initGallerySection();
        initModalControls();
        initRsvpModal();
        renderIgStoryCanvas();
        initIgStoryDownload();
    </script>

</body>

</html>
