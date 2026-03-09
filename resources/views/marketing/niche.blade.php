<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $item['name'] }} Landing</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-100">
    <div class="max-w-5xl mx-auto px-4 py-8">
        <a href="{{ route('marketing.home') }}" class="text-sm text-slate-400 hover:text-slate-200">← Kembali ke semua niche</a>

        <section class="mt-4 p-6 rounded-2xl border border-slate-800" style="background: linear-gradient(135deg, {{ $item['color'] }}22, #0f172a);">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-2">{{ $item['name'] }}</h1>
                    <p class="text-slate-200 mb-2">{{ $item['headline'] }}</p>
                    <p class="text-sm text-slate-400">{{ $item['description'] }}</p>
                </div>
                <i class="fas {{ $item['icon'] }} text-4xl" style="color: {{ $item['color'] }}"></i>
            </div>
        </section>

        <section class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
            @foreach($item['highlights'] as $highlight)
                <div class="rounded-xl border border-slate-800 bg-slate-900 p-4 text-sm">{{ $highlight }}</div>
            @endforeach
        </section>

        <section class="mt-8 p-6 rounded-2xl border border-slate-800 bg-slate-900">
            <h2 class="font-bold text-lg mb-2">Mulai dalam 3 menit</h2>
            <p class="text-sm text-slate-400 mb-4">Tes pengalaman buat undangan tanpa login panjang. Masukkan data singkat dan lihat preview instan.</p>
            <div class="flex gap-2">
                <a href="{{ route('marketing.trial', ['niche' => $niche]) }}" class="px-4 py-2 rounded-lg text-slate-950 font-semibold" style="background: {{ $item['color'] }};">Coba Trial 3 Menit</a>
                <a href="{{ route('register') }}" class="px-4 py-2 rounded-lg border border-slate-700 text-sm">Daftar Akun</a>
            </div>
        </section>
    </div>
</body>
</html>

