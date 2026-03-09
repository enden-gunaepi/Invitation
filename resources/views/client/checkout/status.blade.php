@extends('layouts.client')
@section('title', 'Status Pembayaran')
@section('page-title', 'Status Pembayaran')
@section('page-subtitle', $invitation->title)

@section('content')
<div class="max-w-lg mx-auto">
    @if($devMode ?? false)
    <div class="card p-4 mb-4" style="border-color: var(--warning); background: rgba(255,149,0,0.06);">
        <p class="text-sm font-semibold"><i class="fas fa-flask mr-1"></i> Mode Development Aktif</p>
        <p class="text-xs mt-1" style="color: var(--text-secondary);">
            Anda bisa gunakan tombol simulasi lunas untuk test alur pembayaran.
        </p>
    </div>
    @endif

    <div class="card p-8 text-center">
        @if($payment)
            @if($payment->isPaid())
                <div class="w-16 h-16 rounded-full mx-auto mb-4 flex items-center justify-center" style="background: rgba(52,199,89,0.12);">
                    <i class="fas fa-check-circle text-3xl" style="color: var(--success);"></i>
                </div>
                <h2 class="font-bold text-xl mb-2">Pembayaran Berhasil!</h2>
                <p class="text-sm" style="color: var(--text-secondary);">Terima kasih! Undangan Anda telah aktif.</p>
                <div class="mt-6 p-4 rounded-lg" style="background: var(--bg-tertiary);">
                    <div class="flex items-center justify-between text-sm mb-2">
                        <span style="color: var(--text-secondary);">Subtotal</span>
                        <span class="font-semibold">Rp{{ number_format($payment->base_amount ?? $payment->amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm mb-2">
                        <span style="color: var(--text-secondary);">Diskon</span>
                        <span class="font-semibold" style="color: {{ ($payment->discount_amount ?? 0) > 0 ? 'var(--success)' : 'var(--text-secondary)' }};">
                            {{ ($payment->discount_amount ?? 0) > 0 ? '-' : '' }}Rp{{ number_format($payment->discount_amount ?? 0, 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-sm mb-2">
                        <span style="color: var(--text-secondary);">PPN</span>
                        <span class="font-semibold">Rp{{ number_format($payment->tax_amount ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm mb-2" style="border-top:1px solid var(--border); padding-top:8px;">
                        <span style="color: var(--text-secondary);">Total</span>
                        <span class="font-bold">Rp{{ number_format($payment->amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm mb-2">
                        <span style="color: var(--text-secondary);">Gateway</span>
                        <span class="font-semibold">{{ ucfirst($payment->payment_gateway) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm mb-2">
                        <span style="color: var(--text-secondary);">Invoice</span>
                        <span class="font-semibold">{{ $payment->invoice_number ?? '-' }}</span>
                    </div>
                    @if($payment->coupon_code)
                    <div class="flex items-center justify-between text-sm mb-2">
                        <span style="color: var(--text-secondary);">Kupon</span>
                        <span class="font-semibold">{{ $payment->coupon_code }} (-Rp{{ number_format($payment->coupon_discount_amount ?? 0, 0, ',', '.') }})</span>
                    </div>
                    @endif
                    @if($payment->referral_code)
                    <div class="flex items-center justify-between text-sm mb-2">
                        <span style="color: var(--text-secondary);">Referral</span>
                        <span class="font-semibold">{{ $payment->referral_code }}</span>
                    </div>
                    @endif
                    <div class="flex items-center justify-between text-sm mb-2">
                        <span style="color: var(--text-secondary);">Metode</span>
                        <span class="font-semibold">{{ strtoupper($payment->payment_method ?? '-') }} / {{ $payment->payment_channel ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span style="color: var(--text-secondary);">Dibayar</span>
                        <span class="font-semibold" style="color: var(--success);">{{ $payment->paid_at?->format('d M Y, H:i') }}</span>
                    </div>
                </div>
                <a href="{{ route('client.invitations.show', $invitation) }}" class="btn btn-primary w-full mt-6">
                    <i class="fas fa-eye mr-2"></i> Lihat Undangan
                </a>

            @elseif($payment->isPending())
                <div class="w-16 h-16 rounded-full mx-auto mb-4 flex items-center justify-center" style="background: rgba(255,149,0,0.12);">
                    <i class="fas fa-clock text-3xl" style="color: var(--warning);"></i>
                </div>
                <h2 class="font-bold text-xl mb-2">Menunggu Pembayaran</h2>
                <p class="text-sm" style="color: var(--text-secondary);">Silahkan selesaikan pembayaran Anda. Status akan diperbarui otomatis.</p>
                <div class="mt-6 p-4 rounded-lg" style="background: var(--bg-tertiary);">
                    <div class="flex items-center justify-between text-sm mb-2">
                        <span style="color: var(--text-secondary);">Total</span>
                        <span class="font-bold">Rp{{ number_format($payment->amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm mb-2">
                        <span style="color: var(--text-secondary);">Via</span>
                        <span class="font-semibold">{{ ucfirst($payment->payment_gateway) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm mb-2">
                        <span style="color: var(--text-secondary);">Invoice</span>
                        <span class="font-semibold">{{ $payment->invoice_number ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm mb-2">
                        <span style="color: var(--text-secondary);">Metode</span>
                        <span class="font-semibold">{{ strtoupper($payment->payment_method ?? '-') }} / {{ $payment->payment_channel ?? '-' }}</span>
                    </div>
                    @if($payment->expired_at)
                    <div class="flex items-center justify-between text-sm">
                        <span style="color: var(--text-secondary);">Batas Waktu</span>
                        <span class="font-semibold" style="color: {{ $payment->expired_at->isPast() ? 'var(--danger)' : 'var(--warning)' }};">
                            {{ $payment->expired_at->format('d M Y, H:i') }}
                        </span>
                    </div>
                    @endif
                </div>
                @if($payment->payment_url)
                <a href="{{ $payment->payment_url }}" target="_blank" class="btn btn-primary w-full mt-6">
                    <i class="fas fa-external-link-alt mr-2"></i> Lanjutkan Pembayaran
                </a>
                @endif
                @if($devMode ?? false)
                <form method="POST" action="{{ route('client.checkout.simulate-paid', $invitation) }}" class="mt-2">
                    @csrf
                    <button type="submit" class="btn w-full" style="background: rgba(52,199,89,.12); color: var(--success); border:1px solid rgba(52,199,89,.25);">
                        <i class="fas fa-check-circle mr-2"></i> Simulasi Bayar Berhasil
                    </button>
                </form>
                @endif
                <a href="{{ route('client.checkout.status', $invitation) }}" class="btn btn-secondary w-full mt-2">
                    <i class="fas fa-sync-alt mr-2"></i> Cek Status
                </a>

            @else
                <div class="w-16 h-16 rounded-full mx-auto mb-4 flex items-center justify-center" style="background: rgba(255,59,48,0.12);">
                    <i class="fas fa-times-circle text-3xl" style="color: var(--danger);"></i>
                </div>
                <h2 class="font-bold text-xl mb-2">Pembayaran Gagal</h2>
                <p class="text-sm" style="color: var(--text-secondary);">Pembayaran tidak berhasil. Silahkan coba lagi.</p>
                <a href="{{ route('client.checkout.show', $invitation) }}" class="btn btn-primary w-full mt-6">
                    <i class="fas fa-redo mr-2"></i> Coba Lagi
                </a>
            @endif
        @else
            <div class="w-16 h-16 rounded-full mx-auto mb-4 flex items-center justify-center" style="background: var(--bg-tertiary);">
                <i class="fas fa-question text-3xl" style="color: var(--text-tertiary);"></i>
            </div>
            <h2 class="font-bold text-xl mb-2">Tidak Ada Pembayaran</h2>
            <p class="text-sm" style="color: var(--text-secondary);">Belum ada transaksi untuk undangan ini.</p>
            <a href="{{ route('client.checkout.show', $invitation) }}" class="btn btn-primary w-full mt-6">
                <i class="fas fa-credit-card mr-2"></i> Bayar Sekarang
            </a>
        @endif
    </div>
</div>

<div class="mt-4 text-center">
    <a href="{{ route('client.invitations.show', $invitation) }}" class="text-sm font-semibold" style="color: var(--accent);">
        <i class="fas fa-arrow-left mr-1"></i> Kembali ke undangan
    </a>
</div>
@endsection
