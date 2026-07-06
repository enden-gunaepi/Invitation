@extends('layouts.client')

@section('title', 'Top Up Saldo')
@section('page-title', 'Top Up Saldo')
@section('page-subtitle', 'Isi ulang saldo Anda menggunakan payment gateway')

@section('content')
<style>
    .preset-btn {
        background: var(--surface-container-low);
        border: 2px solid var(--border);
        color: var(--text);
        border-radius: 12px;
        padding: 12px 16px;
        font-weight: 700;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: center;
    }
    .preset-btn:hover {
        background: var(--hover-bg);
        border-color: var(--accent);
        color: var(--accent);
        transform: translateY(-2px);
    }
    .preset-btn.active {
        background: var(--accent-bg);
        border-color: var(--accent);
        color: var(--accent);
    }
</style>

<div class="max-w-2xl mx-auto space-y-6">
    @if($devMode ?? false)
    <div class="card p-4" style="border-color: var(--warning); background: rgba(255,149,0,0.06);">
        <p class="text-sm font-semibold"><i class="fas fa-flask mr-1"></i> Mode Development Aktif</p>
        <p class="text-xs mt-1" style="color: var(--text-secondary);">
            Transaksi QRIS/E-Wallet akan disimulasikan tanpa koneksi API real. Anda dapat mensimulasikan pembayaran sukses setelah submit.
        </p>
    </div>
    @endif

    <div class="card p-6" x-data="{ method: '{{ count($gateways) > 0 ? 'gateway' : 'manual' }}' }">
        <h3 class="font-bold text-base mb-4"><i class="fas fa-wallet mr-2" style="color: var(--accent);"></i> Nominal Top Up</h3>
        
        <!-- Preset Amounts -->
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-4">
            <button type="button" class="preset-btn" data-amount="50000">Rp 50.000</button>
            <button type="button" class="preset-btn" data-amount="100000">Rp 100.000</button>
            <button type="button" class="preset-btn" data-amount="150000">Rp 150.000</button>
            <button type="button" class="preset-btn" data-amount="200000">Rp 200.000</button>
            <button type="button" class="preset-btn" data-amount="500000">Rp 500.000</button>
            <button type="button" class="preset-btn" data-amount="1000000">Rp 1.000.000</button>
        </div>

        <!-- Custom Amount Input -->
        <div class="mb-6">
            <label class="form-label">Nominal Custom (Rp)</label>
            <input type="number" id="globalAmount" class="form-input px-4 py-3 text-lg font-bold" 
                   placeholder="Masukkan nominal lainnya" min="{{ $minTopup }}" required value="{{ old('amount', $prefillAmount ?? 50000) }}"
                   x-on:input="document.getElementById('gatewayAmount').value = $event.target.value; document.getElementById('manualAmount').value = $event.target.value">
            <p class="text-xs mt-1 text-gray-500">Minimum top up: Rp {{ number_format($minTopup, 0, ',', '.') }}</p>
        </div>

        <h3 class="font-bold text-base mb-4"><i class="fas fa-credit-card mr-2" style="color: var(--accent);"></i> Pilih Metode Pembayaran</h3>

        <!-- Pilihan Metode Utama -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            @if(count($gateways) > 0)
            <label class="flex items-start gap-3 p-4 rounded-xl border cursor-pointer transition-all"
                   :class="method === 'gateway' ? 'ring-2 ring-[var(--accent)] bg-[var(--accent-bg)] border-transparent' : 'border-[var(--border-color)] hover:bg-gray-50 dark:hover:bg-slate-800/50'"
                   @click="method = 'gateway'">
                <input type="radio" name="main_method" value="gateway" x-model="method" class="hidden">
                <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0"
                     :style="method === 'gateway' ? 'background: var(--accent); color: white;' : 'background: var(--surface-container); color: var(--text-secondary);'">
                    <i class="fas fa-bolt"></i>
                </div>
                <div>
                    <div class="font-bold text-sm">Payment Gateway</div>
                    <div class="text-xs mt-1" style="color: var(--text-secondary);">Otomatis (QRIS, E-Wallet, dll)</div>
                </div>
            </label>
            @endif

            @if($manualTransferActive)
            <label class="flex items-start gap-3 p-4 rounded-xl border cursor-pointer transition-all"
                   :class="method === 'manual' ? 'ring-2 ring-[var(--accent)] bg-[var(--accent-bg)] border-transparent' : 'border-[var(--border-color)] hover:bg-gray-50 dark:hover:bg-slate-800/50'"
                   @click="method = 'manual'">
                <input type="radio" name="main_method" value="manual" x-model="method" class="hidden">
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

        <!-- Form Payment Gateway -->
        <div x-show="method === 'gateway'" x-transition style="display: none;">
            @if(count($gateways) > 0)
            <form method="POST" action="{{ route('client.balance.topup.process') }}" id="gatewayForm">
                @csrf
                <input type="hidden" name="amount" id="gatewayAmount" :value="document.getElementById('globalAmount').value">

                <!-- Gateway Selection -->
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

                <!-- Payment Type Selection -->
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <label class="flex items-center gap-2 p-3 rounded-lg cursor-pointer" style="border:1px solid var(--border);">
                        <input type="radio" name="payment_type" value="qris" checked style="accent-color: var(--accent);" onchange="refreshChannelOptions()">
                        <span class="text-sm font-semibold"><i class="fas fa-qrcode mr-1"></i> QRIS</span>
                    </label>
                    <label class="flex items-center gap-2 p-3 rounded-lg cursor-pointer" style="border:1px solid var(--border);">
                        <input type="radio" name="payment_type" value="ewallet" style="accent-color: var(--accent);" onchange="refreshChannelOptions()">
                        <span class="text-sm font-semibold"><i class="fas fa-wallet mr-1"></i> E-Wallet</span>
                    </label>
                </div>

                <!-- Channel Selection -->
                <div class="mb-6">
                    <label class="form-label">Channel Pembayaran</label>
                    <select class="form-input" id="channelSelect" name="channel" required></select>
                </div>

                <button type="submit" class="btn btn-primary w-full py-3.5 text-center font-bold text-base rounded-xl">
                    <i class="fas fa-lock mr-2"></i> Lanjutkan Pembayaran
                </button>
            </form>
            @else
                <div class="text-center p-6 bg-yellow-50 dark:bg-yellow-900/10 rounded-xl border border-yellow-200/50">
                    <i class="fas fa-exclamation-triangle text-2xl mb-2 text-yellow-600"></i>
                    <p class="text-sm font-semibold text-yellow-700">Payment Gateway Belum Aktif</p>
                    <p class="text-xs text-yellow-600 mt-1">Silakan pilih metode Transfer Manual jika tersedia, atau hubungi administrator.</p>
                </div>
            @endif
        </div>

        <!-- Form Manual Transfer -->
        @if($manualTransferActive)
        <div x-show="method === 'manual'" x-transition style="display: none;">
            <form method="POST" action="{{ route('client.balance.topup.manual-transfer.process') }}" id="manualTransferForm">
                @csrf
                <input type="hidden" name="amount" id="manualAmount" :value="document.getElementById('globalAmount').value">
                <button type="submit" class="btn btn-primary w-full py-3.5 text-center font-bold text-base rounded-xl">
                    Lanjut ke Instruksi Transfer <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </form>
        </div>
        @endif

        <p class="text-xs text-center mt-4 text-gray-400">
            <i class="fas fa-shield-alt mr-1"></i> Pembayaran aman & terenkripsi
        </p>
    </div>

    <div class="text-center">
        <a href="{{ route('client.balance.index') }}" class="text-sm font-semibold text-[var(--accent)] hover:underline flex items-center justify-center gap-1">
            <span class="material-symbols-outlined" style="font-size: 16px;">arrow_back</span>
            Kembali ke Dompet
        </a>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const presetBtns = document.querySelectorAll('.preset-btn');
        const customInput = document.getElementById('globalAmount');

        function syncPresetState() {
            const normalizedValue = String(parseInt(customInput.value || '0', 10));
            presetBtns.forEach((btn) => {
                btn.classList.toggle('active', btn.dataset.amount === normalizedValue);
            });
        }

        presetBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                customInput.value = this.dataset.amount;
                customInput.dispatchEvent(new Event('input', { bubbles: true }));
                customInput.focus();
            });
        });

        customInput.addEventListener('input', function() {
            syncPresetState();
        });

        syncPresetState();
    });

    @if(count($gateways) > 0)
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

    document.addEventListener('DOMContentLoaded', function() {
        refreshChannelOptions();
    });
    @endif
</script>
@endsection
