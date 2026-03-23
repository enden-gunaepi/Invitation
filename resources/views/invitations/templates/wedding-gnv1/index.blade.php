<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $invitation->title }} - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;700&family=Nunito:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Nunito', sans-serif; background: #0b1120; color: #f9fafb; }
        .font-title { font-family: 'Cormorant Garamond', serif; }
        .glass-card {
            background: linear-gradient(135deg, rgba(255,255,255,0.16), rgba(255,255,255,0.06));
            border: 1px solid rgba(255,255,255,0.22);
            backdrop-filter: blur(10px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.35);
        }
    </style>
</head>
<body>
@php
    $coverPhoto = $invitation->cover_photo ? asset('storage/' . $invitation->cover_photo) : null;
    $groomPhoto = $invitation->groom_photo ? asset('storage/' . $invitation->groom_photo) : $coverPhoto;
    $bridePhoto = $invitation->bride_photo ? asset('storage/' . $invitation->bride_photo) : $coverPhoto;
    $guestName = $guest->name ?? 'Tamu Undangan';
    $coupleName = trim(($invitation->groom_name ?? 'Mempelai Pria') . ' & ' . ($invitation->bride_name ?? 'Mempelai Wanita'));
    $eventDateText = $invitation->event_date ? $invitation->event_date->translatedFormat('d F Y') : '-';
    $eventDateIso = $invitation->event_date ? $invitation->event_date->format('Y-m-d') : null;
    $eventTimeIso = $invitation->event_time ? \Carbon\Carbon::parse($invitation->event_time)->format('H:i:s') : '00:00:00';
    $mapsEmbed = $invitation->google_maps_url
        ? str_replace('/maps/', '/maps/embed/', $invitation->google_maps_url)
        : (($invitation->venue_lat && $invitation->venue_lng)
            ? 'https://www.google.com/maps?q=' . $invitation->venue_lat . ',' . $invitation->venue_lng . '&z=15&output=embed'
            : null);
    $mapsUrl = $invitation->google_maps_url
        ?: (($invitation->venue_lat && $invitation->venue_lng)
            ? 'https://www.google.com/maps?q=' . $invitation->venue_lat . ',' . $invitation->venue_lng
            : null);
    $slideshowImages = $invitation->photos->map(fn($photo) => asset('storage/' . $photo->file_path))->values()->all();
    if (count($slideshowImages) === 0 && $coverPhoto) {
        $slideshowImages[] = $coverPhoto;
    }
    if (count($slideshowImages) === 0 && $bridePhoto) {
        $slideshowImages[] = $bridePhoto;
    }
    if (count($slideshowImages) === 0 && $groomPhoto) {
        $slideshowImages[] = $groomPhoto;
    }
@endphp

<section id="cover" class="relative min-h-screen flex items-center justify-center text-center px-6 py-20 transition-opacity duration-700">
    <div class="absolute inset-0">
        @if($coverPhoto)
            <img src="{{ $coverPhoto }}" alt="Cover" class="w-full h-full object-cover">
        @else
            <div class="w-full h-full bg-gradient-to-br from-slate-950 via-slate-800 to-rose-900"></div>
        @endif
        <div class="absolute inset-0 bg-black/65"></div>
    </div>
    <div class="relative z-10 max-w-2xl">
        <p class="tracking-[0.25em] text-xs md:text-sm mb-4 opacity-90">THE WEDDING OF</p>
        <h1 class="font-title text-4xl md:text-6xl leading-tight mb-4">{{ $coupleName }}</h1>
        <p class="text-sm md:text-lg mb-8">{{ $eventDateText }}</p>
        <p class="text-sm opacity-80">Kepada Yth.</p>
        <p class="text-xl font-semibold mt-1">{{ $guestName }}</p>
        <button type="button" id="openInvite" class="mt-8 bg-rose-700 hover:bg-rose-600 px-6 py-3 rounded-full text-sm font-semibold">
            Buka Undangan
        </button>
    </div>
</section>

<main id="content" class="opacity-0 pointer-events-none transition-opacity duration-700">
    <div id="galleryBg" class="fixed inset-0 -z-10">
        <img id="bgA" src="{{ $slideshowImages[0] ?? '' }}" class="absolute inset-0 w-full h-full object-cover opacity-100 transition-opacity duration-1000" alt="Background">
        <img id="bgB" src="{{ $slideshowImages[1] ?? ($slideshowImages[0] ?? '') }}" class="absolute inset-0 w-full h-full object-cover opacity-0 transition-opacity duration-1000" alt="Background">
        <div class="absolute inset-0 bg-slate-950/55"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-slate-950/75 via-slate-900/45 to-slate-950/90"></div>
    </div>

    <section class="max-w-6xl mx-auto px-6 py-20">
        <article class="glass-card rounded-3xl p-8 md:p-10 text-center">
            <p class="tracking-[0.2em] text-xs mb-3 opacity-85">WE INVITE YOU TO CELEBRATE</p>
            <h2 class="font-title text-4xl md:text-5xl mb-3">{{ $coupleName }}</h2>
            <p class="text-sm opacity-90">{{ $eventDateText }}</p>
        </article>
    </section>

    <section class="max-w-6xl mx-auto px-6 pb-16">
        <div class="grid md:grid-cols-2 gap-6">
            <article class="glass-card rounded-3xl p-7 text-center">
                @if($bridePhoto)
                    <img src="{{ $bridePhoto }}" alt="Foto mempelai wanita" class="w-36 h-36 mx-auto rounded-full object-cover border-2 border-white/70 mb-4">
                @endif
                <h3 class="font-title text-3xl">{{ $invitation->bride_name ?? '-' }}</h3>
                <p class="text-sm opacity-85 mt-2">{{ $invitation->bride_parent_name ?? '-' }}</p>
                @if($invitation->bride_instagram)
                    <a href="{{ $invitation->bride_instagram }}" target="_blank" class="inline-block mt-5 text-sm border border-white/40 px-4 py-2 rounded-full hover:bg-white/15 transition">@Instagram</a>
                @endif
            </article>
            <article class="glass-card rounded-3xl p-7 text-center">
                @if($groomPhoto)
                    <img src="{{ $groomPhoto }}" alt="Foto mempelai pria" class="w-36 h-36 mx-auto rounded-full object-cover border-2 border-white/70 mb-4">
                @endif
                <h3 class="font-title text-3xl">{{ $invitation->groom_name ?? '-' }}</h3>
                <p class="text-sm opacity-85 mt-2">{{ $invitation->groom_parent_name ?? '-' }}</p>
                @if($invitation->groom_instagram)
                    <a href="{{ $invitation->groom_instagram }}" target="_blank" class="inline-block mt-5 text-sm border border-white/40 px-4 py-2 rounded-full hover:bg-white/15 transition">@Instagram</a>
                @endif
            </article>
        </div>
    </section>

    <section class="max-w-5xl mx-auto px-6 pb-16">
        <article class="glass-card rounded-3xl p-8 text-center">
            <h3 class="font-title text-3xl mb-2">Countdown</h3>
            <p class="text-sm opacity-85 mb-6">Menuju hari spesial kami</p>
            <div class="grid grid-cols-4 gap-3 mb-4">
                <div class="bg-white/10 rounded-xl p-4"><p id="days" class="text-2xl font-bold">0</p><p class="text-xs">Hari</p></div>
                <div class="bg-white/10 rounded-xl p-4"><p id="hours" class="text-2xl font-bold">00</p><p class="text-xs">Jam</p></div>
                <div class="bg-white/10 rounded-xl p-4"><p id="minutes" class="text-2xl font-bold">00</p><p class="text-xs">Menit</p></div>
                <div class="bg-white/10 rounded-xl p-4"><p id="seconds" class="text-2xl font-bold">00</p><p class="text-xs">Detik</p></div>
            </div>
            <a href="#rsvp" class="inline-block mt-3 bg-rose-700 hover:bg-rose-600 px-5 py-2 rounded-full text-sm font-semibold">Konfirmasi Kehadiran</a>
        </article>
    </section>

    <section class="max-w-6xl mx-auto px-6 pb-16">
        <article class="glass-card rounded-3xl p-8">
            <h3 class="font-title text-3xl text-center mb-7">Event</h3>
            <div class="grid md:grid-cols-2 gap-5">
                @forelse($invitation->events as $event)
                    <div class="bg-white/10 rounded-2xl p-5">
                        <h4 class="font-semibold text-xl">{{ $event->event_name }}</h4>
                        <p class="text-sm opacity-85 mt-1">
                            {{ $event->event_date ? \Carbon\Carbon::parse($event->event_date)->translatedFormat('l, d F Y') : $eventDateText }}
                        </p>
                        <p class="text-sm opacity-85">
                            {{ $event->event_time ? \Carbon\Carbon::parse($event->event_time)->format('H:i') : '-' }} WIB
                        </p>
                        @if($event->event_description)
                            <p class="text-sm mt-3 opacity-90">{{ $event->event_description }}</p>
                        @endif
                    </div>
                @empty
                    <p class="text-center text-sm opacity-80 md:col-span-2">Belum ada rundown event.</p>
                @endforelse
            </div>
        </article>
    </section>

    @if($mapsEmbed)
    <section class="max-w-6xl mx-auto px-6 pb-16">
        <article class="glass-card rounded-3xl p-8 text-center">
            <h3 class="font-title text-3xl mb-3">Lokasi Acara</h3>
            <p class="text-sm opacity-90 mb-6">{{ $invitation->venue_name }}<br>{{ $invitation->venue_address }}</p>
            <div class="rounded-2xl overflow-hidden border border-white/25">
                <iframe src="{{ $mapsEmbed }}" width="100%" height="320" style="border:0;" loading="lazy"></iframe>
            </div>
            @if($mapsUrl)
                <a href="{{ $mapsUrl }}" target="_blank" class="inline-block mt-5 bg-rose-700 hover:bg-rose-600 px-5 py-3 rounded-full text-sm font-semibold">Gunakan Google Maps</a>
            @endif
        </article>
    </section>
    @endif

    <section class="max-w-6xl mx-auto px-6 pb-16">
        <article class="glass-card rounded-3xl p-8">
            <h3 class="font-title text-3xl text-center mb-7">Susunan Acara</h3>
            <div class="relative max-w-4xl mx-auto">
                <div class="absolute left-1/2 top-0 bottom-0 w-[2px] bg-white/30 -translate-x-1/2"></div>
                @foreach($invitation->events as $event)
                    <div class="mb-10 flex flex-col md:flex-row items-center">
                        <div class="md:w-1/2 md:pr-8 text-center md:text-right {{ $loop->even ? 'order-2 md:order-1' : '' }}">
                            @if($loop->odd)
                                <p class="text-sm font-semibold">{{ $event->event_time ? \Carbon\Carbon::parse($event->event_time)->format('H:i') : '-' }} WIB</p>
                            @else
                                <h4 class="font-semibold">{{ $event->event_name }}</h4>
                                <p class="text-sm opacity-80">{{ $event->event_description }}</p>
                            @endif
                        </div>
                        <div class="w-4 h-4 bg-rose-400 rounded-full border-2 border-white z-10"></div>
                        <div class="md:w-1/2 md:pl-8 text-center md:text-left {{ $loop->odd ? 'mt-4 md:mt-0' : 'order-1 md:order-2 mb-4 md:mb-0' }}">
                            @if($loop->odd)
                                <h4 class="font-semibold">{{ $event->event_name }}</h4>
                                <p class="text-sm opacity-80">{{ $event->event_description }}</p>
                            @else
                                <p class="text-sm font-semibold">{{ $event->event_time ? \Carbon\Carbon::parse($event->event_time)->format('H:i') : '-' }} WIB</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </article>
    </section>

    @if($invitation->photos->count())
    <section class="max-w-6xl mx-auto px-6 pb-16">
        <article class="glass-card rounded-3xl p-8">
            <h3 class="font-title text-3xl text-center mb-7">Galeri</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($invitation->photos as $photo)
                    <img src="{{ asset('storage/' . $photo->file_path) }}" alt="Galeri foto" class="w-full h-44 object-cover rounded-xl">
                @endforeach
            </div>
        </article>
    </section>
    @endif

    @if($invitation->loveStories->count())
    <section class="max-w-6xl mx-auto px-6 pb-16">
        <article class="glass-card rounded-3xl p-8">
            <h3 class="font-title text-3xl text-center mb-7">Love Story</h3>
            <div class="grid md:grid-cols-3 gap-5">
                @foreach($invitation->loveStories as $story)
                    <article class="bg-white/90 text-slate-900 rounded-2xl p-4">
                        @if($story->photo_path)
                            <img src="{{ asset('storage/' . $story->photo_path) }}" alt="{{ $story->title }}" class="w-full h-40 object-cover rounded-xl mb-4">
                        @endif
                        <h4 class="font-semibold">{{ $story->title }}</h4>
                        <p class="text-sm mt-2 opacity-80">{{ $story->description }}</p>
                        <p class="text-xs mt-3 font-semibold">{{ $story->year }}</p>
                    </article>
                @endforeach
            </div>
        </article>
    </section>
    @endif

    <section id="rsvp" class="max-w-6xl mx-auto px-6 pb-16">
        <div class="grid md:grid-cols-2 gap-6">
            <article class="glass-card rounded-3xl p-7">
                <h3 class="font-title text-3xl mb-5">RSVP</h3>
                <form method="POST" action="{{ route('invitation.rsvp', $invitation->slug) }}" class="space-y-3">
                    @csrf
                    <input type="text" name="name" value="{{ $guest->name ?? '' }}" placeholder="Nama" class="w-full px-4 py-3 rounded-lg text-slate-900" required>
                    <input type="text" name="phone" placeholder="Nomor HP" class="w-full px-4 py-3 rounded-lg text-slate-900">
                    <select name="status" class="w-full px-4 py-3 rounded-lg text-slate-900" required>
                        <option value="attending">Hadir</option>
                        <option value="not_attending">Tidak Hadir</option>
                        <option value="maybe">Masih Ragu</option>
                    </select>
                    <input type="number" min="1" max="10" name="pax" value="1" class="w-full px-4 py-3 rounded-lg text-slate-900" required>
                    <textarea name="message" rows="3" placeholder="Pesan RSVP" class="w-full px-4 py-3 rounded-lg text-slate-900"></textarea>
                    @if(!empty($guest?->id))
                        <input type="hidden" name="guest_id" value="{{ $guest->id }}">
                    @endif
                    <button type="submit" class="w-full bg-rose-700 hover:bg-rose-600 px-4 py-3 rounded-lg font-semibold">Kirim RSVP</button>
                </form>
                <div class="mt-4 space-y-2 max-h-64 overflow-y-auto">
                    @foreach($invitation->rsvps as $rsvp)
                        <article class="bg-white/10 rounded-lg p-3">
                            <p class="font-semibold text-sm">{{ $rsvp->name }} ({{ $rsvp->pax }} pax)</p>
                            <p class="text-xs opacity-80 mt-1">{{ ucfirst(str_replace('_', ' ', $rsvp->status)) }}</p>
                            @if($rsvp->message)
                                <p class="text-sm mt-1">{{ $rsvp->message }}</p>
                            @endif
                        </article>
                    @endforeach
                </div>
            </article>

            <article class="glass-card rounded-3xl p-7">
                <h3 class="font-title text-3xl mb-5">Doa & Ucapan</h3>
                <form method="POST" action="{{ route('invitation.wish', $invitation->slug) }}" class="space-y-3">
                    @csrf
                    <input type="text" name="name" value="{{ $guest->name ?? '' }}" placeholder="Nama" class="w-full px-4 py-3 rounded-lg text-slate-900" required>
                    <textarea name="message" rows="4" placeholder="Ucapan untuk mempelai" class="w-full px-4 py-3 rounded-lg text-slate-900" required></textarea>
                    <button type="submit" class="w-full bg-rose-700 hover:bg-rose-600 px-4 py-3 rounded-lg font-semibold">Kirim Ucapan</button>
                </form>
                <div class="mt-4 space-y-2 max-h-64 overflow-y-auto">
                    @foreach($invitation->wishes as $wish)
                        <article class="bg-white/10 rounded-lg p-3">
                            <p class="font-semibold text-sm">{{ $wish->name }}</p>
                            <p class="text-sm mt-1">{{ $wish->message }}</p>
                        </article>
                    @endforeach
                </div>
            </article>
        </div>
    </section>

    <section class="max-w-6xl mx-auto px-6 pb-16">
        <article class="glass-card rounded-3xl p-8">
            <h3 class="font-title text-3xl text-center mb-7">Tanda Kasih</h3>
            <div class="grid md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    @foreach($invitation->bankAccounts as $acc)
                        <div class="bg-white/10 rounded-2xl p-5">
                            <div class="flex items-center justify-between gap-3 mb-2">
                                <p class="text-sm opacity-90 font-semibold">{{ $acc->account_name }}</p>
                                <div class="flex items-center gap-2">
                                    <img src="{{ \App\Support\BankLogo::assetUrl($acc->bank_name) }}"
                                        alt="Logo {{ $acc->bank_name }}"
                                        class="w-12 h-8 rounded-md object-contain bg-white/90 p-1">
                                    <p class="text-sm opacity-90 font-semibold">{{ $acc->bank_name }}</p>
                                </div>
                            </div>
                            <p class="text-lg font-bold mt-1">{{ $acc->account_number }}</p>
                            <button type="button" onclick="copyText('{{ $acc->account_number }}')" class="mt-3 text-sm bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg">Salin Rekening</button>
                        </div>
                    @endforeach
                </div>
                <div class="bg-white/10 rounded-2xl p-5">
                    <h4 class="font-semibold mb-3">Kirim Kado</h4>
                    <p class="text-sm opacity-90 leading-relaxed">{{ $invitation->gift_address ?: 'Alamat pengiriman hadiah belum diisi.' }}</p>
                    @if($invitation->gift_address)
                        <button type="button" onclick="copyText('{{ addslashes($invitation->gift_address) }}')" class="mt-4 text-sm bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg">Salin Alamat</button>
                    @endif
                </div>
            </div>
        </article>
    </section>

    <section class="max-w-4xl mx-auto px-6 pb-20 text-center">
        <article class="glass-card rounded-3xl p-8">
            <h3 class="font-title text-3xl mb-4">Terima Kasih</h3>
            <p class="opacity-90">{{ $invitation->closing_text ?: 'Merupakan suatu kehormatan bagi kami jika Anda berkenan hadir dan memberikan doa restu.' }}</p>
            <p class="font-title text-2xl mt-6">{{ $coupleName }}</p>
            @if(!empty($guest))
                <p class="text-xs mt-4 break-all opacity-80">{{ $guest->getInvitationUrl() }}</p>
            @endif
        </article>
    </section>
</main>

@if($invitation->music_url)
<audio id="bgMusic" loop>
    <source src="{{ asset('storage/' . $invitation->music_url) }}" type="audio/mpeg">
</audio>
@endif

<script>
    const openButton = document.getElementById('openInvite');
    const content = document.getElementById('content');
    const cover = document.getElementById('cover');
    const bgMusic = document.getElementById('bgMusic');
    const slideshowImages = @json($slideshowImages);

    if (openButton) {
        openButton.addEventListener('click', function () {
            cover.classList.add('opacity-0');
            setTimeout(function () {
                cover.classList.add('hidden');
                content.classList.remove('opacity-0', 'pointer-events-none');
                if (bgMusic) {
                    bgMusic.play().catch(function () {});
                }
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }, 700);
        });
    }

    function copyText(text) {
        navigator.clipboard.writeText(text);
    }

    function initSlideshow() {
        if (!Array.isArray(slideshowImages) || slideshowImages.length < 2) return;
        const bgA = document.getElementById('bgA');
        const bgB = document.getElementById('bgB');
        if (!bgA || !bgB) return;

        let index = 1;
        let showingA = true;
        setInterval(function () {
            if (showingA) {
                bgB.src = slideshowImages[index];
                bgB.classList.remove('opacity-0');
                bgA.classList.add('opacity-0');
            } else {
                bgA.src = slideshowImages[index];
                bgA.classList.remove('opacity-0');
                bgB.classList.add('opacity-0');
            }
            showingA = !showingA;
            index = (index + 1) % slideshowImages.length;
        }, 5000);
    }
    initSlideshow();

    @if($eventDateIso)
    const targetDate = new Date('{{ $eventDateIso }}T{{ $eventTimeIso }}').getTime();
    function updateCountdown() {
        const now = new Date().getTime();
        const gap = Math.max(0, targetDate - now);
        const days = Math.floor(gap / (1000 * 60 * 60 * 24));
        const hours = Math.floor((gap / (1000 * 60 * 60)) % 24);
        const minutes = Math.floor((gap / (1000 * 60)) % 60);
        const seconds = Math.floor((gap / 1000) % 60);
        const daysEl = document.getElementById('days');
        const hoursEl = document.getElementById('hours');
        const minutesEl = document.getElementById('minutes');
        const secondsEl = document.getElementById('seconds');
        if (daysEl) daysEl.textContent = String(days);
        if (hoursEl) hoursEl.textContent = String(hours).padStart(2, '0');
        if (minutesEl) minutesEl.textContent = String(minutes).padStart(2, '0');
        if (secondsEl) secondsEl.textContent = String(seconds).padStart(2, '0');
    }
    updateCountdown();
    setInterval(updateCountdown, 1000);
    @endif
</script>
</body>
</html>
