@extends('layouts.client')
@section('title', 'Pilih Paket')
@section('page-title', 'Pilih Paket Langganan')
@section('page-subtitle', 'Aktifkan paket dulu sebelum membuat undangan')

@section('content')
<div class="w-full max-w-[88rem] mx-auto space-y-6">
    <!-- User Balance Info Banner -->
    <div class="card p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-gradient-to-r from-pink-50 to-white dark:from-slate-800 dark:to-slate-900 border-l-4 border-l-[var(--accent)]">
        <div>
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Saldo Dompet Anda</span>
            <span class="text-xl font-bold text-gray-800 dark:text-gray-200">Rp {{ number_format(auth()->user()->balance, 0, ',', '.') }}</span>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('client.balance.topup') }}" class="btn btn-primary px-5 py-2.5 rounded-xl font-bold">
                <span class="material-symbols-outlined mr-1" style="font-size: 16px;">add_circle</span>
                Top Up Saldo
            </a>
            <a href="{{ route('client.balance.index') }}" class="btn btn-secondary px-5 py-2.5 rounded-xl">
                <span class="material-symbols-outlined mr-1" style="font-size: 16px;">history</span>
                Riwayat
            </a>
        </div>
    </div>

    @if($subscriptionCards->isNotEmpty())
    <div class="card p-5 space-y-4" style="border-color: rgba(52,199,89,.35); background: rgba(52,199,89,0.02);">
        <div>
            <p class="text-sm font-semibold text-green-700 dark:text-green-400">
                <i class="fas fa-check-circle mr-1"></i> Paket aktif Anda
            </p>
            <p class="text-xs mt-1 text-gray-500">Setiap pembelian paket tetap aktif secara terpisah dan bisa dipakai membuat undangan sendiri.</p>
        </div>

        <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-3">
            @foreach($subscriptionCards as $card)
                @php
                    $subscription = $card['subscription'];
                    $usage = $card['usage'];
                @endphp
                <div class="rounded-2xl border px-4 py-4 bg-white/70 dark:bg-slate-900/40">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-bold text-sm text-gray-900 dark:text-gray-100">{{ $subscription->package->name }}</p>
                            <p class="text-xs mt-1 text-gray-500">SUB-{{ str_pad((string) $subscription->id, 4, '0', STR_PAD_LEFT) }}</p>
                        </div>
                        <span class="badge badge-success text-[10px] px-2 py-0.5">Aktif</span>
                    </div>
                    <p class="text-xs mt-3 text-gray-500">
                        Terpakai {{ $usage['used'] }}/{{ $usage['max'] }} · Sisa {{ $usage['remaining'] }}
                    </p>
                    <p class="text-xs mt-1 text-gray-500">
                        Berlaku sampai {{ $subscription->expires_at?->format('d M Y H:i') ?? 'tanpa batas waktu' }}
                    </p>
                    <a href="{{ route('client.invitations.create', ['subscription_id' => $subscription->id]) }}" class="btn btn-primary mt-3 px-4 py-2 rounded-xl text-sm font-bold">
                        Buat Undangan
                    </a>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($packages as $package)
        <div class="card p-6 flex flex-col justify-between hover:shadow-md transition duration-300">
            <div>
                <div class="flex items-start justify-between">
                    <h3 class="font-bold text-lg text-gray-800 dark:text-gray-200">{{ $package->name }}</h3>
                    @if(auth()->user()->balance >= $package->price)
                        <span class="badge badge-success text-[10px] px-2 py-0.5">Saldo Cukup</span>
                    @else
                        <span class="badge badge-default text-[10px] px-2 py-0.5" style="background: rgba(186, 26, 26, 0.05); color: #ba1a1a;">Saldo Kurang</span>
                    @endif
                </div>
                <p class="text-xs mt-1 text-gray-500 leading-relaxed">{{ $package->description }}</p>
                
                <div class="my-4">
                    <span class="text-2xl font-extrabold text-[var(--accent)]">Rp {{ number_format((float) $package->price, 0, ',', '.') }}</span>
                    <span class="text-xs text-gray-400 block mt-0.5">
                        {{ ($package->billing_type ?? 'one_time') === 'subscription' ? 'Langganan ' . strtoupper($package->billing_cycle ?? 'monthly') : 'Sekali Bayar' }}
                    </span>
                </div>

                <div class="mt-4 pt-4 border-t border-[var(--outline-variant)] text-xs space-y-2 text-gray-600 dark:text-gray-400">
                    <div class="flex justify-between">
                        <span>Max Undangan</span>
                        <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $package->max_invitations ?? 1 }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Max Tamu/Undangan</span>
                        <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $package->max_guests ?? 100 }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Max Foto/Undangan</span>
                        <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $package->max_photos ?? 10 }}</span>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('client.packages.select.store') }}" class="mt-6">
                @csrf
                <input type="hidden" name="package_id" value="{{ $package->id }}">
                <button type="submit" class="btn btn-primary w-full py-2.5 text-center font-bold text-sm rounded-xl justify-center">
                    Pilih Paket Ini
                </button>
            </form>
        </div>
        @endforeach
    </div>
</div>
@endsection
