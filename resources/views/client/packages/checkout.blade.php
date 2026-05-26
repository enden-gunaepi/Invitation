@extends('layouts.client')
@section('title', 'Checkout Paket')
@section('page-title', 'Checkout Paket')
@section('page-subtitle', $subscription->package->name)

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="card p-6">
        <h3 class="font-bold text-base mb-4"><i class="fas fa-receipt mr-2" style="color: var(--accent);"></i> Ringkasan Paket</h3>
        <div class="p-4 rounded-lg" style="background: var(--surface-container-low);">
            <p class="text-sm font-semibold">{{ $subscription->package->name }}</p>
            <p class="text-xs mt-1" style="color: var(--text-secondary);">
                Kuota undangan {{ $subscription->package->max_invitations ?? 1 }}, tamu {{ $subscription->package->max_guests ?? 100 }}, foto {{ $subscription->package->max_photos ?? 10 }}
            </p>
            
            <div class="mt-4 p-3 rounded-lg bg-white dark:bg-slate-800 border border-[var(--outline-variant)]">
                <div class="flex justify-between text-sm mb-1 text-gray-500">
                    <span>Harga Paket</span>
                    <span>Rp {{ number_format($billing['base'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm mb-1 text-gray-500">
                    <span>Diskon</span>
                    <span class="text-green-600 dark:text-green-400">-Rp {{ number_format($billing['discount'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm mb-1 text-gray-500">
                    <span>PPN ({{ $billing['ppn_percent'] }}%)</span>
                    <span>Rp {{ number_format($billing['tax'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-base font-bold mt-2 pt-2 border-t border-[var(--outline-variant)] text-gray-800 dark:text-gray-200">
                    <span>Total Bayar</span>
                    <span class="text-[var(--accent)]">Rp {{ number_format($billing['total'], 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
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

        <form method="POST" action="{{ route('client.packages.checkout.process', $subscription) }}" id="checkoutForm">
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
                    <p class="text-xs text-red-600 dark:text-red-400 mt-1">Silakan isi ulang saldo Anda terlebih dahulu untuk melanjutkan pembelian paket ini.</p>
                </div>
                <a href="{{ route('client.balance.topup', ['amount' => $billing['total'] - $user->balance]) }}" 
                   class="btn btn-primary w-full py-3.5 text-center font-bold text-base rounded-xl justify-center">
                    <i class="fas fa-plus-circle mr-2"></i> Top Up Saldo Sekarang
                </a>
            @endif
        </form>
    </div>

    <div class="text-center">
        <a href="{{ route('client.packages.select') }}" class="text-sm font-semibold text-[var(--accent)] hover:underline flex items-center justify-center gap-1">
            <span class="material-symbols-outlined" style="font-size: 16px;">arrow_back</span>
            Kembali ke Pilih Paket
        </a>
    </div>
</div>
@endsection
