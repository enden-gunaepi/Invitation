@extends('layouts.client')
@section('title', 'Checkout')
@section('page-title', 'Pembayaran')
@section('page-subtitle', $invitation->title)

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    {{-- Order Summary --}}
    <div class="card p-6">
        <h3 class="font-bold text-base mb-4"><i class="fas fa-receipt mr-2" style="color: var(--accent);"></i> Ringkasan Order</h3>
        <div class="flex items-center justify-between p-4 rounded-lg" style="background: var(--bg-tertiary);">
            <div>
                <p class="text-sm font-semibold">{{ $invitation->title }}</p>
                <p class="text-xs mt-1" style="color: var(--text-secondary);">Paket {{ $invitation->package->name }}</p>
            </div>
            <div class="text-right">
                <p class="text-lg font-bold" style="color: var(--accent);">Rp{{ number_format($invitation->package->price, 0, ',', '.') }}</p>
            </div>
        </div>

        {{-- Package features --}}
        @if($invitation->package->features)
        <div class="mt-4 flex flex-wrap gap-1">
            @foreach($invitation->package->features as $f)
            <span class="text-xs px-2 py-1 rounded" style="background: var(--accent-bg); color: var(--accent);">{{ $f }}</span>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Pending Payment --}}
    @if($pendingPayment)
    <div class="card p-6" style="border-color: var(--warning);">
        <div class="flex items-center gap-3 mb-3">
            <i class="fas fa-clock text-lg" style="color: var(--warning);"></i>
            <div>
                <h3 class="font-bold text-sm">Pembayaran Tertunda</h3>
                <p class="text-xs" style="color: var(--text-secondary);">Anda memiliki pembayaran yang belum selesai</p>
            </div>
        </div>
        <div class="p-3 rounded-lg text-sm" style="background: var(--bg-tertiary);">
            <div class="flex items-center justify-between">
                <span style="color: var(--text-secondary);">Via {{ ucfirst($pendingPayment->payment_gateway) }}</span>
                <span class="badge badge-warning">Pending</span>
            </div>
            @if($pendingPayment->expired_at)
            <p class="text-xs mt-2" style="color: var(--text-secondary);">
                Expired: {{ $pendingPayment->expired_at->format('d M Y, H:i') }}
            </p>
            @endif
        </div>
        @if($pendingPayment->payment_url)
        <a href="{{ $pendingPayment->payment_url }}" target="_blank" class="btn btn-primary w-full text-center mt-4">
            <i class="fas fa-external-link-alt mr-2"></i> Lanjutkan Pembayaran
        </a>
        @endif
    </div>
    @endif

    {{-- Payment Gateway Selection --}}
    @if(!$pendingPayment)
        @if(count($gateways) > 0)
        <div class="card p-6">
            <h3 class="font-bold text-base mb-4"><i class="fas fa-credit-card mr-2" style="color: var(--accent);"></i> Pilih Metode Pembayaran</h3>

            <form method="POST" action="{{ route('client.checkout.process', $invitation) }}">
                @csrf
                <div class="space-y-3">
                    @foreach($gateways as $gw)
                    <label class="flex items-center gap-4 p-4 rounded-lg cursor-pointer transition" style="border: 2px solid var(--border);"
                           onmouseover="this.style.borderColor='var(--accent)'" onmouseout="if(!this.querySelector('input').checked) this.style.borderColor='var(--border)'"
                           onclick="this.style.borderColor='var(--accent)'">
                        <input type="radio" name="gateway" value="{{ $gw['code'] }}" required style="accent-color: var(--accent); width: 18px; height: 18px;"
                               {{ $loop->first ? 'checked' : '' }}>
                        <div class="flex items-center gap-3 flex-1">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: var(--accent-bg);">
                                <i class="{{ $gw['icon'] }}" style="color: var(--accent);"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold">{{ $gw['name'] }}</p>
                                <p class="text-xs" style="color: var(--text-secondary);">
                                    {{ $gw['code'] === 'xendit' ? 'VA, QRIS, E-Wallet, Credit Card' : 'VA, QRIS, Convenience Store' }}
                                </p>
                            </div>
                        </div>
                    </label>
                    @endforeach
                </div>

                <button type="submit" class="btn btn-primary w-full mt-6 py-3 text-center" style="font-size: 14px;">
                    <i class="fas fa-lock mr-2"></i> Bayar Rp{{ number_format($invitation->package->price, 0, ',', '.') }}
                </button>
            </form>

            <p class="text-xs text-center mt-4" style="color: var(--text-tertiary);">
                <i class="fas fa-shield-alt mr-1"></i> Pembayaran aman & terenkripsi
            </p>
        </div>
        @else
        <div class="card p-6 text-center">
            <i class="fas fa-exclamation-triangle text-2xl mb-3" style="color: var(--warning);"></i>
            <p class="text-sm font-semibold">Payment Gateway Belum Dikonfigurasi</p>
            <p class="text-xs mt-1" style="color: var(--text-secondary);">Hubungi admin untuk mengaktifkan metode pembayaran.</p>
        </div>
        @endif
    @endif
</div>

<div class="mt-4 text-center">
    <a href="{{ route('client.invitations.show', $invitation) }}" class="text-sm font-semibold" style="color: var(--accent);">
        <i class="fas fa-arrow-left mr-1"></i> Kembali ke undangan
    </a>
</div>
@endsection
