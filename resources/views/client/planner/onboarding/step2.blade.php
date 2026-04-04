@extends('layouts.client')

@section('title', 'Wedding Planner — Konsep')
@section('page-title', 'Wedding Planner')
@section('page-subtitle', 'Langkah 2 dari 3 — Tamu & Konsep')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-2 mb-8">
        <div class="flex-1 h-2 rounded-full bg-gradient-to-r from-rose-500 to-pink-500"></div>
        <div class="flex-1 h-2 rounded-full bg-gradient-to-r from-rose-500 to-pink-500"></div>
        <div class="flex-1 h-2 rounded-full bg-gray-200"></div>
    </div>

    <div class="card p-8">
        <div class="text-center mb-8">
            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-violet-500 to-purple-500 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-users text-white text-2xl"></i>
            </div>
            <h2 class="text-2xl font-bold mb-2">Berapa Tamu & Konsep Apa? ✨</h2>
            <p class="text-sm" style="color: var(--text-secondary);">Ini menentukan checklist dan estimasi budget otomatis</p>
        </div>

        <form action="{{ route('client.planner.onboarding.step2.process') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-semibold mb-2"><i class="fas fa-user-group mr-1 text-violet-500"></i> Target Jumlah Tamu</label>
                <div class="relative">
                    <input type="range" name="target_guests" id="guestSlider"
                        value="{{ old('target_guests', $profile->target_guests ?? 200) }}"
                        min="20" max="2000" step="10"
                        class="w-full h-2 rounded-full appearance-none cursor-pointer accent-violet-600"
                        oninput="document.getElementById('guestCount').textContent = this.value">
                    <div class="flex justify-between mt-2 text-xs" style="color: var(--text-secondary);">
                        <span>20</span>
                        <span class="text-lg font-bold text-violet-600" id="guestCount">{{ old('target_guests', $profile->target_guests ?? 200) }}</span>
                        <span>2000</span>
                    </div>
                </div>
                <p class="text-xs mt-1" style="color: var(--text-tertiary);">Jumlah orang yang akan diundang</p>
                @error('target_guests') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold mb-3"><i class="fas fa-sparkles mr-1 text-violet-500"></i> Konsep Pernikahan</label>
                <div class="grid grid-cols-2 gap-3" x-data="{ selected: '{{ old('concept', $profile->concept ?? 'simple') }}' }">
                    @foreach([
                        ['value' => 'simple', 'icon' => 'fa-leaf', 'label' => 'Simple & Elegan', 'desc' => 'Minimalis, elegan, budget-friendly', 'gradient' => 'from-emerald-500 to-teal-500'],
                        ['value' => 'mewah', 'icon' => 'fa-crown', 'label' => 'Mewah & Grand', 'desc' => 'Grand, dekorasi wah, entertainment lengkap', 'gradient' => 'from-amber-500 to-yellow-500'],
                        ['value' => 'intimate', 'icon' => 'fa-heart', 'label' => 'Intimate', 'desc' => 'Jumlah tamu terbatas, personal & hangat', 'gradient' => 'from-rose-500 to-pink-500'],
                        ['value' => 'outdoor', 'icon' => 'fa-sun', 'label' => 'Outdoor / Garden', 'desc' => 'Taman, pantai, atau venue terbuka', 'gradient' => 'from-sky-500 to-blue-500'],
                    ] as $opt)
                    <label class="cursor-pointer" @click="selected = '{{ $opt['value'] }}'">
                        <input type="radio" name="concept" value="{{ $opt['value'] }}" class="hidden"
                            :checked="selected === '{{ $opt['value'] }}'">
                        <div class="card p-4 text-center transition-all duration-200 border-2"
                            :class="selected === '{{ $opt['value'] }}'
                                ? 'border-violet-500 shadow-lg shadow-violet-500/10'
                                : 'border-transparent hover:border-gray-200'">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br {{ $opt['gradient'] }} flex items-center justify-center mx-auto mb-3">
                                <i class="fas {{ $opt['icon'] }} text-white"></i>
                            </div>
                            <p class="font-bold text-sm">{{ $opt['label'] }}</p>
                            <p class="text-xs mt-1" style="color: var(--text-secondary);">{{ $opt['desc'] }}</p>
                        </div>
                    </label>
                    @endforeach
                </div>
                @error('concept') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-3">
                <a href="{{ route('client.planner.onboarding.step1') }}"
                    class="flex-1 py-3 rounded-xl font-semibold text-sm text-center border transition-all hover:bg-gray-50"
                    style="border-color: var(--border); color: var(--text-secondary);">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
                <button type="submit" class="flex-[2] py-3.5 rounded-xl font-bold text-white text-sm
                    bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-700 hover:to-purple-700
                    transition-all duration-200 shadow-lg shadow-violet-500/25">
                    Lanjut — Atur Budget <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
