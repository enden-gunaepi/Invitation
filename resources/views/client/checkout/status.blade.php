@extends('layouts.client')
@section('title', 'Status Pembayaran')
@section('page-title', 'Status Pembayaran')
@section('page-subtitle', $invitation->title)

@section('content')
<div class="max-w-lg mx-auto">
    <div class="card p-8 text-center">
        @if($payment)
            @if($payment->isPaid())
                <div class="w-16 h-16 rounded-full mx-auto mb-4 flex items-center justify-center" style="background: rgba(52,199,89,0.12);">
                    <i class="fas fa-check-circle text-3xl" style="color: var(--success);"></i>
                </div>
                <h2 class="font-bold text-xl mb-2">Pembayaran Berhasil!</h2>
                <p class="text-sm text-gray-500">Terima kasih! Undangan Anda telah aktif.</p>
                
                <div class="mt-6 p-4 rounded-xl bg-gray-50 dark:bg-slate-800/50 border border-[var(--outline-variant)]">
                    <div class="flex items-center justify-between text-sm mb-2">
                        <span class="text-gray-500">Jumlah Terbayar</span>
                        <span class="font-bold text-[var(--accent)]">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm mb-2">
                        <span class="text-gray-500">Invoice</span>
                        <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $payment->invoice_number }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm mb-2">
                        <span class="text-gray-500">Metode</span>
                        <span class="font-semibold text-gray-700 dark:text-gray-300">Potong Saldo (Instan)</span>
                    </div>
                    <div class="flex items-center justify-between text-sm mb-2">
                        <span class="text-gray-500">Waktu Bayar</span>
                        <span class="font-semibold text-green-600 dark:text-green-400">{{ $payment->paid_at?->format('d M Y, H:i') }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm pt-2 mt-2 border-t border-[var(--outline-variant)]">
                        <span class="text-gray-500">Sisa Saldo Anda</span>
                        <span class="font-bold text-gray-800 dark:text-gray-200">Rp {{ number_format(auth()->user()->balance, 0, ',', '.') }}</span>
                    </div>
                </div>

                <a href="{{ route('client.invitations.show', $invitation) }}" class="btn btn-primary w-full mt-6 py-3 font-bold rounded-xl justify-center">
                    <i class="fas fa-eye mr-2"></i> Lihat Undangan
                </a>
            @else
                <div class="w-16 h-16 rounded-full mx-auto mb-4 flex items-center justify-center" style="background: rgba(255,59,48,0.12);">
                    <i class="fas fa-times-circle text-3xl" style="color: var(--danger);"></i>
                </div>
                <h2 class="font-bold text-xl mb-2">Pembayaran Gagal</h2>
                <p class="text-sm text-gray-500">Pembayaran saldo tidak berhasil. Silakan hubungi admin.</p>
                <a href="{{ route('client.checkout.show', $invitation) }}" class="btn btn-primary w-full mt-6 py-3 font-bold rounded-xl justify-center">
                    <i class="fas fa-redo mr-2"></i> Coba Lagi
                </a>
            @endif
        @else
            <div class="w-16 h-16 rounded-full mx-auto mb-4 flex items-center justify-center" style="background: var(--bg-tertiary);">
                <i class="fas fa-question text-3xl" style="color: var(--text-tertiary);"></i>
            </div>
            <h2 class="font-bold text-xl mb-2">Tidak Ada Pembayaran</h2>
            <p class="text-sm text-gray-500">Belum ada transaksi pembayaran untuk undangan ini.</p>
            <a href="{{ route('client.checkout.show', $invitation) }}" class="btn btn-primary w-full mt-6 py-3 font-bold rounded-xl justify-center">
                <i class="fas fa-credit-card mr-2"></i> Bayar Sekarang
            </a>
        @endif
    </div>
</div>

<div class="mt-4 text-center">
    <a href="{{ route('client.invitations.show', $invitation) }}" class="text-sm font-semibold text-[var(--accent)] hover:underline">
        <i class="fas fa-arrow-left mr-1"></i> Kembali ke undangan
    </a>
</div>
@endsection
