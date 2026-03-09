@extends('layouts.client')
@section('title', 'Checkout')
@section('page-title', 'Pembayaran')
@section('page-subtitle', $invitation->title)

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    @if($devMode ?? false)
    <div class="card p-4" style="border-color: var(--warning); background: rgba(255,149,0,0.06);">
        <p class="text-sm font-semibold"><i class="fas fa-flask mr-1"></i> Mode Development Aktif</p>
        <p class="text-xs mt-1" style="color: var(--text-secondary);">
            Transaksi QRIS/E-Wallet akan disimulasikan tanpa koneksi API real.
        </p>
    </div>
    @endif

    <div class="card p-6">
        <h3 class="font-bold text-base mb-4"><i class="fas fa-receipt mr-2" style="color: var(--accent);"></i> Ringkasan Order</h3>
        <div class="flex items-center justify-between p-4 rounded-lg" style="background: var(--bg-tertiary);">
            <div>
                <p class="text-sm font-semibold">{{ $invitation->title }}</p>
                <p class="text-xs mt-1" style="color: var(--text-secondary);">Paket {{ $invitation->package->name }}</p>
                <p class="text-xs mt-1" style="color: var(--text-secondary);">
                    Billing: {{ ($invitation->package->billing_type ?? 'one_time') === 'subscription' ? 'Subscription ' . strtoupper($invitation->package->billing_cycle ?? 'monthly') : 'One-time' }}
                </p>
            </div>
            <div class="text-right">
                <p class="text-sm" style="color: var(--text-secondary);">Subtotal</p>
                <p class="text-lg font-bold" style="color: var(--accent);">Rp{{ number_format($billing['base'], 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="mt-4 p-4 rounded-lg" style="background: var(--bg-tertiary);">
            <div class="flex items-center justify-between text-sm mb-2">
                <span style="color: var(--text-secondary);">Harga Paket</span>
                <span>Rp{{ number_format($billing['base'], 0, ',', '.') }}</span>
            </div>
            <div class="flex items-center justify-between text-sm mb-2">
                <span style="color: var(--text-secondary);">Diskon</span>
                <span style="color: {{ $billing['discount'] > 0 ? 'var(--success)' : 'var(--text-secondary)' }};">
                    {{ $billing['discount'] > 0 ? '-' : '' }}Rp{{ number_format($billing['discount'], 0, ',', '.') }}
                </span>
            </div>
            <div class="flex items-center justify-between text-sm mb-2">
                <span style="color: var(--text-secondary);">PPN</span>
                <span>Rp{{ number_format($billing['tax'], 0, ',', '.') }}</span>
            </div>
            <div class="flex items-center justify-between text-sm pt-2" style="border-top:1px solid var(--border);">
                <span class="font-semibold">Total Tagihan</span>
                <span class="font-bold" style="color: var(--accent); font-size: 1rem;">Rp{{ number_format($billing['total'], 0, ',', '.') }}</span>
            </div>
        </div>

        @if($invitation->package->features)
        <div class="mt-4 flex flex-wrap gap-1">
            @foreach($invitation->package->features as $f)
            <span class="text-xs px-2 py-1 rounded" style="background: var(--accent-bg); color: var(--accent);">{{ $f }}</span>
            @endforeach
        </div>
        @endif
    </div>

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
            <div class="flex items-center justify-between mb-2">
                <span style="color: var(--text-secondary);">Gateway</span>
                <span class="font-semibold">{{ ucfirst($pendingPayment->payment_gateway) }}</span>
            </div>
            <div class="flex items-center justify-between mb-2">
                <span style="color: var(--text-secondary);">Metode</span>
                <span class="font-semibold">{{ strtoupper($pendingPayment->payment_method ?? '-') }} / {{ $pendingPayment->payment_channel ?? '-' }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span style="color: var(--text-secondary);">Total</span>
                <span class="font-bold">Rp{{ number_format($pendingPayment->amount, 0, ',', '.') }}</span>
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

    @if(!$pendingPayment)
        @if(count($gateways) > 0)
        <div class="card p-6">
            <h3 class="font-bold text-base mb-4"><i class="fas fa-credit-card mr-2" style="color: var(--accent);"></i> Pilih Metode Pembayaran</h3>

            <form method="POST" action="{{ route('client.checkout.process', $invitation) }}" id="checkoutForm">
                @csrf
                <div class="space-y-3 mb-4">
                    @foreach($gateways as $gw)
                    <label class="flex items-center gap-4 p-4 rounded-lg cursor-pointer transition" style="border: 2px solid var(--border);">
                        <input type="radio" name="gateway" value="{{ $gw['code'] }}" required style="accent-color: var(--accent); width: 18px; height: 18px;"
                               {{ $loop->first ? 'checked' : '' }} onchange="refreshChannelOptions()">
                        <div class="flex items-center gap-3 flex-1">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: var(--accent-bg);">
                                <i class="{{ $gw['icon'] }}" style="color: var(--accent);"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold">{{ $gw['name'] }}</p>
                                <p class="text-xs" style="color: var(--text-secondary);">QRIS & E-Wallet</p>
                            </div>
                        </div>
                    </label>
                    @endforeach
                </div>

                <div class="grid grid-cols-2 gap-3 mb-4">
                    <label class="flex items-center gap-2 p-3 rounded-lg" style="border:1px solid var(--border);">
                        <input type="radio" name="payment_type" value="qris" checked style="accent-color: var(--accent);" onchange="refreshChannelOptions()">
                        <span class="text-sm font-semibold"><i class="fas fa-qrcode mr-1"></i> QRIS</span>
                    </label>
                    <label class="flex items-center gap-2 p-3 rounded-lg" style="border:1px solid var(--border);">
                        <input type="radio" name="payment_type" value="ewallet" style="accent-color: var(--accent);" onchange="refreshChannelOptions()">
                        <span class="text-sm font-semibold"><i class="fas fa-wallet mr-1"></i> E-Wallet</span>
                    </label>
                </div>

                <div class="mb-4">
                    <label class="form-label">Channel Pembayaran</label>
                    <select class="form-input" id="channelSelect" name="channel" required></select>
                    <p class="text-xs mt-1" style="color: var(--text-secondary);">Channel akan otomatis menyesuaikan gateway + metode.</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-2">
                    <div>
                        <label class="form-label">Kupon (opsional)</label>
                        <input type="text" name="coupon_code" class="form-input" value="{{ old('coupon_code') }}" placeholder="PROMO10">
                    </div>
                    <div>
                        <label class="form-label">Referral (opsional)</label>
                        <input type="text" name="referral_code" class="form-input" value="{{ old('referral_code') }}" placeholder="REF12345">
                    </div>
                </div>
                <p class="text-xs mb-2" style="color: var(--text-secondary);">Kupon dan referral akan diverifikasi saat proses pembayaran.</p>

                <button type="submit" class="btn btn-primary w-full mt-2 py-3 text-center" style="font-size: 14px;">
                    <i class="fas fa-lock mr-2"></i> Bayar Rp{{ number_format($billing['total'], 0, ',', '.') }}
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

@if(!$pendingPayment && count($gateways) > 0)
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
            opt.textContent = 'Tidak ada channel tersedia';
            select.appendChild(opt);
            select.disabled = true;
            return;
        }

        select.disabled = false;
        options.forEach((item) => {
            const opt = document.createElement('option');
            opt.value = item.code;
            opt.textContent = item.name + ' (' + item.code + ')';
            select.appendChild(opt);
        });
    }

    document.addEventListener('DOMContentLoaded', refreshChannelOptions);
</script>
@endif
@endsection
