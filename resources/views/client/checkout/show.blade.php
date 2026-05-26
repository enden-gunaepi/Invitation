@extends('layouts.client')
@section('title', 'Checkout')
@section('page-title', 'Pembayaran')
@section('page-subtitle', $invitation->title)

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="card p-6">
        <h3 class="font-bold text-base mb-4"><i class="fas fa-receipt mr-2" style="color: var(--accent);"></i> Ringkasan Order</h3>
        <div class="flex items-center justify-between p-4 rounded-lg" style="background: var(--surface-container-low);">
            <div>
                <p class="text-sm font-semibold">{{ $invitation->title }}</p>
                <p class="text-xs mt-1" style="color: var(--text-secondary);">Paket {{ $invitation->package->name }}</p>
                <p class="text-xs mt-1" style="color: var(--text-secondary);">
                    Billing: {{ ($invitation->package->billing_type ?? 'one_time') === 'subscription' ? 'Subscription ' . strtoupper($invitation->package->billing_cycle ?? 'monthly') : 'One-time' }}
                </p>
            </div>
            <div class="text-right">
                <p class="text-sm" style="color: var(--text-secondary);">Subtotal</p>
                <p class="text-lg font-bold" style="color: var(--accent);">Rp {{ number_format($billing['base'], 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="mt-4 p-4 rounded-lg" style="background: var(--surface-container-low);">
            <div class="flex items-center justify-between text-sm mb-2">
                <span style="color: var(--text-secondary);">Harga Paket</span>
                <span>Rp {{ number_format($billing['base'], 0, ',', '.') }}</span>
            </div>
            <div class="flex items-center justify-between text-sm mb-2">
                <span style="color: var(--text-secondary);">Diskon</span>
                <span style="color: {{ $billing['discount'] > 0 ? 'var(--success)' : 'var(--text-secondary)' }};">
                    {{ $billing['discount'] > 0 ? '-' : '' }}Rp {{ number_format($billing['discount'], 0, ',', '.') }}
                </span>
            </div>
            <div class="flex items-center justify-between text-sm mb-2">
                <span style="color: var(--text-secondary);">PPN</span>
                <span>Rp {{ number_format($billing['tax'], 0, ',', '.') }}</span>
            </div>
            <div class="flex items-center justify-between text-sm pt-2" style="border-top:1px solid var(--border);">
                <span class="font-semibold">Total Tagihan</span>
                <span class="font-bold" style="color: var(--accent); font-size: 1rem;">Rp {{ number_format($billing['total'], 0, ',', '.') }}</span>
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

    <!-- Balance Status Card -->
    <div class="card p-6">
        <h3 class="font-bold text-base mb-4"><i class="fas fa-wallet mr-2" style="color: var(--accent);"></i> Pembayaran Saldo</h3>
        
        <div class="flex justify-between items-center p-4 rounded-xl border mb-6" 
             style="background: var(--surface-container-low); border-color: var(--outline-variant);">
            <div>
                <span class="text-xs text-gray-500 block uppercase tracking-wider font-semibold">Saldo Dompet Anda</span>
                <span class="text-lg font-bold text-gray-800 dark:text-gray-200">Rp {{ number_format($user->balance, 0, ',', '.') }}</span>
            </div>
            <div>
                @if($user->hasSufficientBalance($billing['total']))
                    <span class="badge badge-success px-3 py-1 flex items-center gap-1">
                        <span class="material-symbols-outlined" style="font-size: 14px;">check_circle</span>
                        Saldo Cukup
                    </span>
                @else
                    <span class="badge badge-danger px-3 py-1 flex items-center gap-1 bg-red-100 text-red-700">
                        <span class="material-symbols-outlined" style="font-size: 14px;">cancel</span>
                        Saldo Kurang
                    </span>
                @endif
            </div>
        </div>

        <form method="POST" action="{{ route('client.checkout.process', $invitation) }}" id="checkoutForm">
            @csrf
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-6">
                <div>
                    <label class="form-label">Kupon (opsional)</label>
                    <input type="text" name="coupon_code" class="form-input" value="{{ old('coupon_code') }}" placeholder="PROMO10">
                </div>
                <div>
                    <label class="form-label">Referral (opsional)</label>
                    <input type="text" name="referral_code" class="form-input" value="{{ old('referral_code', $lockedReferralCode ?? '') }}" placeholder="REF12345" {{ !empty($lockedReferralCode) ? 'readonly' : '' }}>
                    @if(!empty($currentReferrer))
                    <p class="text-xs mt-1" style="color: var(--text-secondary);">
                        Dirujuk oleh <strong>{{ $currentReferrer->name }}</strong>
                    </p>
                    @endif
                </div>
            </div>

            @if($user->hasSufficientBalance($billing['total']))
                <button type="submit" class="btn btn-primary w-full py-3.5 text-center font-bold text-base rounded-xl">
                    <i class="fas fa-lock mr-2"></i> Bayar Sekarang (Potong Saldo)
                </button>
            @else
                <div class="p-4 rounded-xl bg-red-50 dark:bg-red-950/20 border border-red-200/50 text-center mb-4">
                    <p class="text-sm font-semibold text-red-800 dark:text-red-300">Saldo Anda kurang sebesar Rp {{ number_format($billing['total'] - $user->balance, 0, ',', '.') }}</p>
                    <p class="text-xs text-red-600 dark:text-red-400 mt-1">Silakan isi ulang saldo Anda terlebih dahulu untuk melanjutkan pembelian ini.</p>
                </div>
                <a href="{{ route('client.balance.topup', ['amount' => $billing['total'] - $user->balance]) }}" 
                   class="btn btn-primary w-full py-3.5 text-center font-bold text-base rounded-xl justify-center">
                    <i class="fas fa-plus-circle mr-2"></i> Top Up Saldo Sekarang
                </a>
            @endif
        </form>
    </div>
</div>

<div class="mt-4 text-center">
    <a href="{{ route('client.invitations.show', $invitation) }}" class="text-sm font-semibold text-[var(--accent)] hover:underline">
        <i class="fas fa-arrow-left mr-1"></i> Kembali ke undangan
    </a>
</div>
@endsection
