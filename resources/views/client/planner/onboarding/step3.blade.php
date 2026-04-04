@extends('layouts.client')

@section('title', 'Wedding Planner — Budget')
@section('page-title', 'Wedding Planner')
@section('page-subtitle', 'Langkah 3 dari 3 — Total Budget')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-2 mb-8">
        <div class="flex-1 h-2 rounded-full bg-gradient-to-r from-rose-500 to-pink-500"></div>
        <div class="flex-1 h-2 rounded-full bg-gradient-to-r from-violet-500 to-purple-500"></div>
        <div class="flex-1 h-2 rounded-full bg-gradient-to-r from-amber-500 to-yellow-500"></div>
    </div>

    <div class="card p-8">
        <div class="text-center mb-8">
            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-amber-500 to-yellow-500 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-wallet text-white text-2xl"></i>
            </div>
            <h2 class="text-2xl font-bold mb-2">Berapa Total Budget? 💰</h2>
            <p class="text-sm" style="color: var(--text-secondary);">Kami akan membagi budget otomatis ke setiap kategori berdasarkan konsep <strong>{{ ucfirst($profile->concept) }}</strong></p>
        </div>

        <form action="{{ route('client.planner.onboarding.step3.process') }}" method="POST" class="space-y-6" x-data="{ budget: {{ old('total_budget', 50000000) }} }">
            @csrf

            <div>
                <label class="block text-sm font-semibold mb-2"><i class="fas fa-money-bill-wave mr-1 text-amber-500"></i> Total Budget Pernikahan</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-bold" style="color: var(--text-secondary);">Rp</span>
                    <input type="number" name="total_budget" x-model="budget"
                        class="form-input w-full pl-12 text-lg font-bold" min="1000000" step="1000000" required>
                </div>
                <p class="text-sm font-semibold mt-2 text-amber-600" x-text="'Rp' + Number(budget).toLocaleString('id-ID')"></p>
                @error('total_budget') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Quick Presets --}}
            <div>
                <p class="text-xs font-semibold mb-2" style="color: var(--text-secondary);">Pilihan cepat:</p>
                <div class="flex flex-wrap gap-2">
                    @foreach([
                        ['amount' => 30000000, 'label' => '30 Juta'],
                        ['amount' => 50000000, 'label' => '50 Juta'],
                        ['amount' => 75000000, 'label' => '75 Juta'],
                        ['amount' => 100000000, 'label' => '100 Juta'],
                        ['amount' => 150000000, 'label' => '150 Juta'],
                        ['amount' => 200000000, 'label' => '200 Juta'],
                        ['amount' => 300000000, 'label' => '300 Juta'],
                        ['amount' => 500000000, 'label' => '500 Juta+'],
                    ] as $preset)
                    <button type="button" @click="budget = {{ $preset['amount'] }}"
                        class="px-3 py-1.5 rounded-lg text-xs font-semibold border transition-all duration-150"
                        :class="budget == {{ $preset['amount'] }}
                            ? 'bg-amber-500 text-white border-amber-500 shadow-md'
                            : 'border-gray-200 hover:border-amber-300 hover:bg-amber-50'"
                        style="color: var(--text);">
                        {{ $preset['label'] }}
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- Budget Preview --}}
            <div class="rounded-2xl p-4 border" style="background: var(--bg-tertiary); border-color: var(--border);">
                <p class="text-xs font-bold mb-3" style="color: var(--text-secondary);"><i class="fas fa-magic mr-1"></i> Preview alokasi otomatis ({{ ucfirst($profile->concept) }}):</p>
                @php
                    $previewCategories = match($profile->concept) {
                        'mewah' => ['Venue 25%', 'Catering 22%', 'Dekorasi 18%', 'Foto/Video 12%', 'Busana 8%', 'Entertainment 5%'],
                        'intimate' => ['Venue 22%', 'Catering 35%', 'Dekorasi 13%', 'Foto/Video 15%', 'Busana 7%', 'Lainnya 8%'],
                        'outdoor' => ['Venue 20%', 'Catering 30%', 'Dekorasi+Tenda 18%', 'Foto/Video 12%', 'Entertainment 4%', 'Lainnya 16%'],
                        default => ['Venue 30%', 'Catering 35%', 'Dekorasi 8%', 'Foto/Video 12%', 'Busana 5%', 'Lainnya 10%'],
                    };
                @endphp
                <div class="flex flex-wrap gap-2">
                    @foreach($previewCategories as $cat)
                    <span class="px-2 py-1 rounded-md text-xs font-medium" style="background: var(--bg-secondary); color: var(--text-secondary);">{{ $cat }}</span>
                    @endforeach
                </div>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('client.planner.onboarding.step2') }}"
                    class="flex-1 py-3 rounded-xl font-semibold text-sm text-center border transition hover:bg-gray-50"
                    style="border-color: var(--border); color: var(--text-secondary);">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
                <button type="submit" class="flex-[2] py-3.5 rounded-xl font-bold text-white text-sm
                    bg-gradient-to-r from-amber-500 to-yellow-500 hover:from-amber-600 hover:to-yellow-600
                    transition-all duration-200 shadow-lg shadow-amber-500/25">
                    🎉 Generate Wedding Planner!
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
