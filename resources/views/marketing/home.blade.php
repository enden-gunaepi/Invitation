<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invitation Platform</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-100">
    <div class="max-w-6xl mx-auto px-4 py-8">
        <header class="flex items-center justify-between mb-10">
            <div>
                <h1 class="text-2xl font-bold">Invitation Platform</h1>
                <p class="text-sm text-slate-400">Landing per niche + trial instan 3 menit</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('marketing.trial') }}" class="px-4 py-2 rounded-lg bg-emerald-500 text-slate-950 font-semibold text-sm">Coba Trial</a>
                <a href="{{ route('login') }}" class="px-4 py-2 rounded-lg border border-slate-700 text-sm">Login</a>
            </div>
        </header>

        <section class="mb-8 p-6 rounded-2xl border border-slate-800 bg-gradient-to-br from-slate-900 to-slate-950">
            <h2 class="text-3xl font-bold mb-2">Buat undangan digital sesuai jenis acara Anda</h2>
            <p class="text-slate-400 max-w-3xl">Pilih niche yang paling relevan untuk melihat contoh value proposition, fitur utama, dan alur conversion yang terarah.</p>
        </section>

        <section class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($niches as $key => $niche)
                <a href="{{ route('marketing.niche', $key) }}" class="p-5 rounded-2xl border border-slate-800 bg-slate-900 hover:border-emerald-400/50 transition">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-lg font-semibold">{{ $niche['name'] }}</h3>
                        <i class="fas {{ $niche['icon'] }}" style="color: {{ $niche['color'] }}"></i>
                    </div>
                    <p class="text-sm text-slate-300 mb-2">{{ $niche['headline'] }}</p>
                    <p class="text-xs text-slate-500">{{ $niche['description'] }}</p>
                </a>
            @endforeach
        </section>
    </div>
</body>
</html>

