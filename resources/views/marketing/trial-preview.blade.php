<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview Trial</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen text-white" style="background: radial-gradient(circle at 15% 20%, {{ $data['theme_color'] }}44, #020617 38%), #020617;">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-6">
            <a href="{{ route('marketing.trial') }}" class="text-sm text-slate-300 hover:text-white">← Ulangi Trial</a>
            <a href="{{ route('register') }}" class="px-4 py-2 rounded-lg text-slate-950 font-semibold" style="background: {{ $data['theme_color'] }};">Aktifkan Akun</a>
        </div>

        <section class="rounded-3xl border border-white/20 bg-white/5 backdrop-blur-sm p-8 text-center">
            <p class="text-xs uppercase tracking-[0.25em] text-slate-300 mb-3">{{ $item['name'] }} Demo</p>
            <h1 class="text-4xl font-bold mb-2">{{ $data['title'] }}</h1>
            <p class="text-slate-300 mb-6">{{ $data['headline'] }}</p>

            <div class="inline-flex items-center gap-3 rounded-full bg-white/10 border border-white/20 px-4 py-2 text-sm">
                <span>{{ $data['host_1'] ?: 'Host 1' }}</span>
                <span>&</span>
                <span>{{ $data['host_2'] ?: 'Host 2' }}</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-8 text-left">
                <div class="rounded-xl border border-white/15 bg-black/20 p-4">
                    <p class="text-xs text-slate-300 mb-1">Tanggal</p>
                    <p class="font-semibold">{{ \Carbon\Carbon::parse($data['event_date'])->translatedFormat('d F Y') }}</p>
                </div>
                <div class="rounded-xl border border-white/15 bg-black/20 p-4">
                    <p class="text-xs text-slate-300 mb-1">Lokasi</p>
                    <p class="font-semibold">{{ $data['venue_name'] }}{{ !empty($data['city']) ? ', ' . $data['city'] : '' }}</p>
                </div>
            </div>

            <div class="mt-8 rounded-xl border border-emerald-400/35 bg-emerald-400/10 p-4 text-left">
                <p class="text-sm font-semibold mb-1">Trial berhasil dibuat dalam hitungan detik.</p>
                <p class="text-xs text-slate-300">Untuk publish link, custom domain, RSVP real-time, dan analytics, lanjutkan dengan akun client.</p>
            </div>
        </section>
    </div>
</body>
</html>

