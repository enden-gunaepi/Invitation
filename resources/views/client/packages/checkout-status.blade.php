@extends('layouts.client')
@section('title', 'Status Pembayaran Paket')
@section('page-title', 'Status Pembayaran Paket')
@section('page-subtitle', $subscription->package->name)

@section('content')
<div class="max-w-lg mx-auto">
    <div class="card p-8 text-center">
        @if($payment)
            @if($payment->isPaid())
                <h2 class="font-bold text-xl mb-2">Pembayaran Berhasil</h2>
                <p class="text-sm" style="color: var(--text-secondary);">Paket akun Anda sudah aktif, silakan mulai buat undangan.</p>
                <a href="{{ route('client.invitations.create') }}" class="btn btn-primary w-full mt-6">Buat Undangan</a>
            @elseif($payment->isPending())
                <h2 class="font-bold text-xl mb-2">Menunggu Pembayaran</h2>
                <p class="text-sm" style="color: var(--text-secondary);">Selesaikan pembayaran untuk mengaktifkan paket.</p>
                @if($payment->payment_url)
                <a href="{{ $payment->payment_url }}" target="_blank" class="btn btn-primary w-full mt-6">Lanjutkan Pembayaran</a>
                @endif
                @if($devMode ?? false)
                <form method="POST" action="{{ route('client.packages.checkout.simulate-paid', $subscription) }}" class="mt-2">
                    @csrf
                    <button type="submit" class="btn w-full" style="background: rgba(52,199,89,.12); color: var(--success); border:1px solid rgba(52,199,89,.25);">
                        Simulasi Bayar Berhasil
                    </button>
                </form>
                @endif
            @else
                <h2 class="font-bold text-xl mb-2">Pembayaran Gagal</h2>
                <a href="{{ route('client.packages.checkout.show', $subscription) }}" class="btn btn-primary w-full mt-6">Coba Lagi</a>
            @endif
        @else
            <h2 class="font-bold text-xl mb-2">Belum Ada Transaksi</h2>
            <a href="{{ route('client.packages.checkout.show', $subscription) }}" class="btn btn-primary w-full mt-6">Bayar Sekarang</a>
        @endif
    </div>

    <div class="mt-4 text-center">
        <a href="{{ route('client.packages.select') }}" class="text-sm font-semibold" style="color: var(--accent);">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke pilih paket
        </a>
    </div>
</div>
@endsection
