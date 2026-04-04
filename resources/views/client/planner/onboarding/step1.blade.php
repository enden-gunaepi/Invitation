@extends('layouts.client')

@section('title', 'Wedding Planner — Mulai')
@section('page-title', 'Wedding Planner')
@section('page-subtitle', 'Langkah 1 dari 3 — Data Pasangan & Acara')

@section('content')
<div class="max-w-2xl mx-auto">
    {{-- Progress Bar --}}
    <div class="flex items-center gap-2 mb-8">
        <div class="flex-1 h-2 rounded-full bg-gradient-to-r from-rose-500 to-pink-500"></div>
        <div class="flex-1 h-2 rounded-full bg-gray-200"></div>
        <div class="flex-1 h-2 rounded-full bg-gray-200"></div>
    </div>

    <div class="card p-8">
        <div class="text-center mb-8">
            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-rose-500 to-pink-500 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-heart text-white text-2xl"></i>
            </div>
            <h2 class="text-2xl font-bold mb-2">Ceritakan Tentang Pernikahan Kamu 💍</h2>
            <p class="text-sm" style="color: var(--text-secondary);">Kami akan menyiapkan semua perencanaan secara otomatis berdasarkan data ini</p>
        </div>

        <form action="{{ route('client.planner.onboarding.step1.process') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-2">Nama Mempelai Pria</label>
                    <input type="text" name="partner_1_name" value="{{ old('partner_1_name', $profile->partner_1_name) }}"
                        class="form-input w-full" placeholder="Nama lengkap" required>
                    @error('partner_1_name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-2">Nama Mempelai Wanita</label>
                    <input type="text" name="partner_2_name" value="{{ old('partner_2_name', $profile->partner_2_name) }}"
                        class="form-input w-full" placeholder="Nama lengkap" required>
                    @error('partner_2_name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-2"><i class="fas fa-calendar-heart mr-1 text-rose-500"></i> Tanggal Pernikahan</label>
                <input type="date" name="wedding_date" value="{{ old('wedding_date', $profile->wedding_date?->format('Y-m-d')) }}"
                    class="form-input w-full" min="{{ now()->addDay()->format('Y-m-d') }}" required>
                @error('wedding_date') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold mb-2"><i class="fas fa-map-marker-alt mr-1 text-rose-500"></i> Kota Acara</label>
                <input type="text" name="city" value="{{ old('city', $profile->city) }}"
                    class="form-input w-full" placeholder="Contoh: Jakarta, Bandung, Surabaya" required>
                @error('city') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="w-full py-3.5 rounded-xl font-bold text-white text-sm
                bg-gradient-to-r from-rose-600 to-pink-600 hover:from-rose-700 hover:to-pink-700
                transition-all duration-200 shadow-lg shadow-rose-500/25 hover:shadow-rose-500/40">
                Lanjut — Pilih Konsep <i class="fas fa-arrow-right ml-2"></i>
            </button>
        </form>
    </div>
</div>
@endsection
