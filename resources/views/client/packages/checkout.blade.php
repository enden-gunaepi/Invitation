@extends('layouts.client')
@section('title', 'Checkout Paket')
@section('page-title', 'Checkout Paket')
@section('page-subtitle', $subscription->package->name)

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="card p-6">
        <h3 class="font-bold text-base mb-4">Ringkasan Paket</h3>
        <div class="p-4 rounded-lg" style="background: var(--bg-tertiary);">
            <p class="text-sm font-semibold">{{ $subscription->package->name }}</p>
            <p class="text-xs mt-1" style="color: var(--text-secondary);">
                Kuota undangan {{ $subscription->package->max_invitations ?? 1 }}, tamu {{ $subscription->package->max_guests ?? 100 }}, foto {{ $subscription->package->max_photos ?? 10 }}
            </p>
            <p class="text-lg font-bold mt-3" style="color: var(--accent);">Rp{{ number_format($billing['total'], 0, ',', '.') }}</p>
        </div>
    </div>

    @if($pendingPayment)
    <div class="card p-6">
        <p class="text-sm font-semibold mb-2">Pembayaran pending tersedia</p>
        @if($pendingPayment->payment_url)
        <a href="{{ $pendingPayment->payment_url }}" target="_blank" class="btn btn-primary w-full text-center">
            Lanjutkan Pembayaran
        </a>
        @endif
    </div>
    @else
    <div class="card p-6">
        <form method="POST" action="{{ route('client.packages.checkout.process', $subscription) }}">
            @csrf
            <div class="space-y-3 mb-4">
                @foreach($gateways as $gw)
                <label class="flex items-center gap-3 p-3 rounded-lg cursor-pointer" style="border:1px solid var(--border);">
                    <input type="radio" name="gateway" value="{{ $gw['code'] }}" required {{ $loop->first ? 'checked' : '' }} onchange="refreshChannelOptions()" style="accent-color: var(--accent);">
                    <span class="text-sm font-semibold">{{ $gw['name'] }}</span>
                </label>
                @endforeach
            </div>

            <div class="grid grid-cols-2 gap-3 mb-4">
                <label class="flex items-center gap-2 p-3 rounded-lg" style="border:1px solid var(--border);">
                    <input type="radio" name="payment_type" value="qris" checked onchange="refreshChannelOptions()" style="accent-color: var(--accent);">
                    <span class="text-sm font-semibold">QRIS</span>
                </label>
                <label class="flex items-center gap-2 p-3 rounded-lg" style="border:1px solid var(--border);">
                    <input type="radio" name="payment_type" value="ewallet" onchange="refreshChannelOptions()" style="accent-color: var(--accent);">
                    <span class="text-sm font-semibold">E-Wallet</span>
                </label>
            </div>

            <div class="mb-3">
                <label class="form-label">Channel</label>
                <select name="channel" id="channelSelect" class="form-input" required></select>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                <div>
                    <label class="form-label">Kupon</label>
                    <input type="text" name="coupon_code" class="form-input" value="{{ old('coupon_code') }}">
                </div>
                <div>
                    <label class="form-label">Referral</label>
                    <input type="text" name="referral_code" class="form-input" value="{{ old('referral_code', $lockedReferralCode ?? '') }}" {{ !empty($lockedReferralCode) ? 'readonly' : '' }}>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-full">Bayar Paket</button>
        </form>
    </div>
    @endif

    <a href="{{ route('client.packages.select') }}" class="text-sm font-semibold" style="color: var(--accent);">
        <i class="fas fa-arrow-left mr-1"></i> Kembali ke pilih paket
    </a>
</div>

<script>
const channelMap = @json($channelMap);
function refreshChannelOptions() {
    const gateway = document.querySelector('input[name="gateway"]:checked')?.value;
    const paymentType = document.querySelector('input[name="payment_type"]:checked')?.value;
    const select = document.getElementById('channelSelect');
    if (!gateway || !paymentType || !select) return;
    const options = (channelMap[gateway] && channelMap[gateway][paymentType]) ? channelMap[gateway][paymentType] : [];
    select.innerHTML = '';
    if (!options.length) {
        const opt = document.createElement('option');
        opt.value = '';
        opt.textContent = 'Tidak ada channel';
        select.appendChild(opt);
        return;
    }
    options.forEach((item) => {
        const opt = document.createElement('option');
        opt.value = item.code;
        opt.textContent = item.name + ' (' + item.code + ')';
        select.appendChild(opt);
    });
}
document.addEventListener('DOMContentLoaded', refreshChannelOptions);
</script>
@endsection
