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

    <!-- Pilihan Metode Pembayaran -->
    <div x-data="{ method: 'balance' }" class="space-y-6">
        <div class="card p-6">
            <h3 class="font-bold text-base mb-4"><i class="fas fa-credit-card mr-2" style="color: var(--accent);"></i> Pilih Metode Pembayaran</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                <!-- Option: Saldo -->
                <label class="flex items-start gap-3 p-4 rounded-xl border cursor-pointer transition-all"
                       :class="method === 'balance' ? 'ring-2 ring-[var(--accent)] bg-[var(--accent-bg)] border-transparent' : 'border-[var(--border-color)] hover:bg-gray-50 dark:hover:bg-slate-800/50'"
                       @click="method = 'balance'">
                    <input type="radio" name="payment_method_selector" value="balance" x-model="method" class="hidden">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0"
                         :style="method === 'balance' ? 'background: var(--accent); color: white;' : 'background: var(--surface-container); color: var(--text-secondary);'">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div>
                        <div class="font-bold text-sm">Potong Saldo</div>
                        <div class="text-xs mt-1" style="color: var(--text-secondary);">Instan, langsung aktif</div>
                        <div class="text-xs font-semibold mt-1" style="color: var(--accent);">
                            Saldo Anda: Rp {{ number_format($user->balance, 0, ',', '.') }}
                        </div>
                    </div>
                </label>

                <!-- Option: Transfer Manual -->
                @if($manualTransferActive)
                <label class="flex items-start gap-3 p-4 rounded-xl border cursor-pointer transition-all"
                       :class="method === 'manual' ? 'ring-2 ring-[var(--accent)] bg-[var(--accent-bg)] border-transparent' : 'border-[var(--border-color)] hover:bg-gray-50 dark:hover:bg-slate-800/50'"
                       @click="method = 'manual'">
                    <input type="radio" name="payment_method_selector" value="manual" x-model="method" class="hidden">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0"
                         :style="method === 'manual' ? 'background: var(--accent); color: white;' : 'background: var(--surface-container); color: var(--text-secondary);'">
                        <i class="fas fa-university"></i>
                    </div>
                    <div>
                        <div class="font-bold text-sm">Transfer Manual</div>
                        <div class="text-xs mt-1" style="color: var(--text-secondary);">Verifikasi admin 1x24 jam</div>
                    </div>
                </label>
                @endif
            </div>

            <!-- Formulir Kupon & Referral (Berlaku untuk semua metode) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-6 p-4 rounded-xl" style="background: var(--surface-container-low);">
                <div>
                    <label class="form-label">Kupon (opsional)</label>
                    <input type="text" form="checkoutForm" id="global_coupon_code" class="form-input" value="{{ old('coupon_code') }}" placeholder="PROMO10"
                           x-on:input="document.getElementById('manual_coupon_code').value = $event.target.value">
                </div>
                <div>
                    <label class="form-label">Referral (opsional)</label>
                    <input type="text" form="checkoutForm" id="global_referral_code" class="form-input" value="{{ old('referral_code', $lockedReferralCode ?? '') }}" placeholder="REF12345" {{ !empty($lockedReferralCode) ? 'readonly' : '' }}
                           x-on:input="document.getElementById('manual_referral_code').value = $event.target.value">
                    @if(!empty($currentReferrer))
                    <p class="text-xs mt-1" style="color: var(--text-secondary);">
                        Dirujuk oleh <strong>{{ $currentReferrer->name }}</strong>
                    </p>
                    @endif
                </div>
            </div>

            <!-- Content: Saldo -->
            <div x-show="method === 'balance'" x-transition>
                <form method="POST" action="{{ route('client.checkout.process', $invitation) }}" id="checkoutForm">
                    @csrf
                    <!-- inputs form are bound to inputs above via id and script/alpine -->
                    <input type="hidden" name="coupon_code" id="balance_coupon_code" :value="document.getElementById('global_coupon_code').value">
                    <input type="hidden" name="referral_code" id="balance_referral_code" :value="document.getElementById('global_referral_code').value">

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

            <!-- Content: Manual Transfer -->
            @if($manualTransferActive)
            <div x-show="method === 'manual'" x-transition style="display: none;">
                <form method="POST" action="{{ route('client.checkout.manual-transfer.process', $invitation) }}" id="manualTransferForm">
                    @csrf
                    <input type="hidden" name="coupon_code" id="manual_coupon_code" :value="document.getElementById('global_coupon_code').value">
                    <input type="hidden" name="referral_code" id="manual_referral_code" :value="document.getElementById('global_referral_code').value">

                    <button type="submit" class="btn btn-primary w-full py-3.5 text-center font-bold text-base rounded-xl">
                        Lanjut ke Instruksi Transfer <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>

<div class="mt-4 text-center">
    <a href="{{ route('client.invitations.show', $invitation) }}" class="text-sm font-semibold text-[var(--accent)] hover:underline">
        <i class="fas fa-arrow-left mr-1"></i> Kembali ke undangan
    </a>
</div>
@endsection
