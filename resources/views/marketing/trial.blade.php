<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trial 3 Menit</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-100">
    <div class="max-w-3xl mx-auto px-4 py-8">
        <a href="{{ route('marketing.home') }}" class="text-sm text-slate-400 hover:text-slate-200">← Kembali</a>

        <div class="mt-4 p-6 rounded-2xl border border-slate-800 bg-slate-900">
            <h1 class="text-2xl font-bold mb-1">Trial Instan 3 Menit</h1>
            <p class="text-sm text-slate-400 mb-6">Tanpa login. Isi data singkat lalu lihat preview halaman undangan.</p>

            <form method="POST" action="{{ route('marketing.trial.preview') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="text-xs text-slate-400">Niche</label>
                    <select name="niche" class="w-full mt-1 rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm" required>
                        @foreach($niches as $key => $niche)
                            <option value="{{ $key }}" {{ request('niche') === $key ? 'selected' : '' }}>{{ $niche['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs text-slate-400">Judul Acara</label>
                    <input name="title" class="w-full mt-1 rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm" placeholder="Contoh: The Wedding of Andi & Sari" required>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs text-slate-400">Nama 1</label>
                        <input name="host_1" class="w-full mt-1 rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm" placeholder="Andi">
                    </div>
                    <div>
                        <label class="text-xs text-slate-400">Nama 2</label>
                        <input name="host_2" class="w-full mt-1 rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm" placeholder="Sari">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs text-slate-400">Tanggal</label>
                        <input type="date" name="event_date" class="w-full mt-1 rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm" required>
                    </div>
                    <div>
                        <label class="text-xs text-slate-400">Kota</label>
                        <input name="city" class="w-full mt-1 rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm" placeholder="Jakarta">
                    </div>
                </div>
                <div>
                    <label class="text-xs text-slate-400">Lokasi</label>
                    <input name="venue_name" class="w-full mt-1 rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm" placeholder="Gedung Serbaguna" required>
                </div>
                <button class="w-full rounded-lg bg-emerald-500 text-slate-950 font-semibold py-2">Lihat Preview Instan</button>
            </form>
        </div>
    </div>
</body>
</html>

