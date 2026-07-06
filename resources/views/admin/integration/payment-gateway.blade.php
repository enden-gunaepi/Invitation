@extends('layouts.admin')
@section('title', 'Integrasi Payment Gateway')
@section('page-title', 'Integrasi')
@section('page-subtitle', 'Konfigurasi vendor pembayaran aktif')

@section('content')
<div class="flex flex-col lg:flex-row gap-6">
    {{-- Sidebar Tab --}}
    <div class="lg:w-56 shrink-0">
        <div class="card p-3">
            <div class="flex flex-row lg:flex-col gap-1">
                <a href="{{ route('admin.integration.telegram') }}"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.integration.telegram*') ? 'bg-blue-500/10' : 'hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                    style="color: {{ request()->routeIs('admin.integration.telegram*') ? 'var(--accent)' : 'var(--text-secondary)' }};">
                    <i class="fab fa-telegram text-lg w-5 text-center"></i>
                    <span>Telegram</span>
                </a>
                <a href="{{ route('admin.integration.whatsapp') }}"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.integration.whatsapp*') ? 'bg-green-500/10' : 'hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                    style="color: {{ request()->routeIs('admin.integration.whatsapp*') ? 'var(--success)' : 'var(--text-secondary)' }};">
                    <i class="fab fa-whatsapp text-lg w-5 text-center"></i>
                    <span>WhatsApp</span>
                </a>
                <a href="{{ route('admin.integration.email') }}"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.integration.email*') ? 'bg-purple-500/10' : 'hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                    style="color: {{ request()->routeIs('admin.integration.email*') ? '#8b5cf6' : 'var(--text-secondary)' }};">
                    <i class="fas fa-envelope text-lg w-5 text-center"></i>
                    <span>Email</span>
                </a>
                <a href="{{ route('admin.integration.payment-gateway') }}"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.integration.payment-gateway*') ? 'bg-amber-500/10' : 'hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                    style="color: {{ request()->routeIs('admin.integration.payment-gateway*') ? '#d97706' : 'var(--text-secondary)' }};">
                    <i class="fas fa-credit-card text-lg w-5 text-center"></i>
                    <span>Payment</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="flex-1 space-y-6">
        <form method="POST" action="{{ route('admin.integration.payment-gateway.update') }}" id="pgForm">
            @csrf @method('PUT')

            {{-- Metode Pembayaran Aktif --}}
            <div class="card p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="font-bold text-base flex items-center gap-2" style="color: var(--accent);">
                            <i class="fas fa-toggle-on"></i> Metode Pembayaran Aktif
                        </h3>
                        <p class="text-xs mt-1" style="color: var(--text-secondary);">
                            Pilih satu atau lebih metode pembayaran yang bisa digunakan oleh client. Minimal 1 metode harus aktif.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Payment Gateway Toggle --}}
                    <label class="flex items-start gap-3 p-4 rounded-xl border cursor-pointer transition-all hover:bg-gray-50 dark:hover:bg-slate-800/50"
                           style="border-color: var(--border-color);">
                        <input type="hidden" name="payment_method_gateway" value="0">
                        <input type="checkbox" name="payment_method_gateway" value="1"
                               {{ $config['payment_method_gateway'] === '1' ? 'checked' : '' }}
                               style="width:20px;height:20px;accent-color:var(--accent);margin-top:2px;">
                        <div>
                            <div class="font-bold text-sm">Payment Gateway</div>
                            <div class="text-xs mt-1" style="color: var(--text-secondary);">
                                Xendit / Tripay (Otomatis)
                            </div>
                        </div>
                    </label>

                    {{-- Manual Transfer Toggle --}}
                    <label class="flex items-start gap-3 p-4 rounded-xl border cursor-pointer transition-all hover:bg-gray-50 dark:hover:bg-slate-800/50"
                           style="border-color: var(--border-color);">
                        <input type="hidden" name="payment_method_transfer_manual" value="0">
                        <input type="checkbox" name="payment_method_transfer_manual" value="1"
                               {{ $config['payment_method_transfer_manual'] === '1' ? 'checked' : '' }}
                               style="width:20px;height:20px;accent-color:var(--accent);margin-top:2px;">
                        <div class="flex-1">
                            <div class="font-bold text-sm">Transfer Manual</div>
                            <div class="text-xs mt-1" style="color: var(--text-secondary);">
                                Client transfer ke rekening bank
                            </div>
                            <div class="mt-2">
                                <a href="{{ route('admin.manual-transfer.bank-accounts') }}"
                                   class="text-xs font-semibold hover:underline"
                                   style="color: var(--accent);" @click.stop>
                                    <i class="fas fa-university"></i> Kelola Rekening Bank
                                </a>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Pilih Vendor --}}
            <div class="card p-6">
                <h3 class="font-bold text-base mb-1">Pilih Vendor Gateway</h3>
                <p class="text-xs mb-5" style="color: var(--text-secondary);">Hanya satu vendor yang aktif pada satu waktu. Simpan untuk menerapkan pilihan.</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Xendit Card --}}
                    <label class="vendor-card cursor-pointer rounded-2xl border-2 p-5 transition-all"
                        style="border-color: {{ $config['payment_primary_gateway'] === 'xendit' ? '#0071e3' : 'var(--border)' }}; background: {{ $config['payment_primary_gateway'] === 'xendit' ? 'rgba(0,113,227,0.05)' : 'transparent' }};"
                        id="card-xendit">
                        <input type="radio" name="payment_primary_gateway" value="xendit"
                            {{ $config['payment_primary_gateway'] === 'xendit' ? 'checked' : '' }}
                            class="hidden" onchange="switchVendor('xendit')">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background: rgba(0,113,227,0.1); color: #0071e3;">
                                <i class="fas fa-bolt text-lg"></i>
                            </div>
                            <div>
                                <div class="font-bold text-sm">Xendit</div>
                                <div class="text-xs" style="color: var(--text-secondary);">QRIS, E-Wallet, VA</div>
                            </div>
                            <div class="ml-auto">
                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all"
                                    id="dot-xendit"
                                    style="border-color: {{ $config['payment_primary_gateway'] === 'xendit' ? '#0071e3' : 'var(--border)' }};">
                                    @if($config['payment_primary_gateway'] === 'xendit')
                                        <div class="w-2.5 h-2.5 rounded-full" style="background: #0071e3;"></div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if($config['payment_primary_gateway'] === 'xendit')
                            <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-0.5 rounded-full" style="background: rgba(0,113,227,0.1); color: #0071e3;">
                                <i class="fas fa-check-circle"></i> Aktif
                            </span>
                        @else
                            <span class="text-xs" style="color: var(--text-tertiary);">Tidak aktif</span>
                        @endif
                    </label>

                    {{-- Tripay Card --}}
                    <label class="vendor-card cursor-pointer rounded-2xl border-2 p-5 transition-all"
                        style="border-color: {{ $config['payment_primary_gateway'] === 'tripay' ? 'var(--success)' : 'var(--border)' }}; background: {{ $config['payment_primary_gateway'] === 'tripay' ? 'rgba(52,199,89,0.05)' : 'transparent' }};"
                        id="card-tripay">
                        <input type="radio" name="payment_primary_gateway" value="tripay"
                            {{ $config['payment_primary_gateway'] === 'tripay' ? 'checked' : '' }}
                            class="hidden" onchange="switchVendor('tripay')">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background: rgba(52,199,89,0.1); color: var(--success);">
                                <i class="fas fa-credit-card text-lg"></i>
                            </div>
                            <div>
                                <div class="font-bold text-sm">Tripay</div>
                                <div class="text-xs" style="color: var(--text-secondary);">QRIS, VA, Retail</div>
                            </div>
                            <div class="ml-auto">
                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all"
                                    id="dot-tripay"
                                    style="border-color: {{ $config['payment_primary_gateway'] === 'tripay' ? 'var(--success)' : 'var(--border)' }};">
                                    @if($config['payment_primary_gateway'] === 'tripay')
                                        <div class="w-2.5 h-2.5 rounded-full" style="background: var(--success);"></div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if($config['payment_primary_gateway'] === 'tripay')
                            <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-0.5 rounded-full" style="background: rgba(52,199,89,0.1); color: var(--success);">
                                <i class="fas fa-check-circle"></i> Aktif
                            </span>
                        @else
                            <span class="text-xs" style="color: var(--text-tertiary);">Tidak aktif</span>
                        @endif
                    </label>
                </div>
            </div>

            {{-- Konfigurasi Xendit --}}
            <div id="section-xendit" class="card p-6" style="{{ $config['payment_primary_gateway'] !== 'xendit' ? 'display:none;' : '' }}">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: rgba(0,113,227,0.1); color: #0071e3;">
                        <i class="fas fa-bolt text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-base">Konfigurasi Xendit</h3>
                        <p class="text-xs" style="color: var(--text-secondary);">API credential dan mode operasi</p>
                    </div>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="form-label">Secret API Key</label>
                        <input type="password" name="xendit_secret_key" class="form-input" value="{{ $config['xendit_secret_key'] }}" placeholder="xnd_development_xxxx">
                    </div>
                    <div>
                        <label class="form-label">Callback Verification Token</label>
                        <input type="text" name="xendit_callback_token" class="form-input" value="{{ $config['xendit_callback_token'] }}" placeholder="Token dari dashboard Xendit">
                        <p class="text-[11px] mt-1" style="color: var(--text-tertiary);">Wajib diisi saat mode Production.</p>
                    </div>
                    <div>
                        <label class="form-label">Mode</label>
                        <select name="xendit_mode" class="form-input">
                            <option value="sandbox" {{ $config['xendit_mode'] === 'sandbox' ? 'selected' : '' }}>Sandbox</option>
                            <option value="production" {{ $config['xendit_mode'] === 'production' ? 'selected' : '' }}>Production</option>
                        </select>
                    </div>
                    <div class="p-3 rounded-lg text-xs" style="background: var(--bg-tertiary); color: var(--text-secondary);">
                        <strong>Callback URL:</strong><br>
                        <code style="color: #0071e3;">{{ route('callback.xendit') }}</code>
                    </div>
                </div>
                <div class="mt-4 pt-4" style="border-top: 1px solid var(--border);">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="testGateway('xendit')">
                        <i class="fas fa-vial mr-1"></i> Test Koneksi
                    </button>
                </div>
            </div>

            {{-- Konfigurasi Tripay --}}
            <div id="section-tripay" class="card p-6" style="{{ $config['payment_primary_gateway'] !== 'tripay' ? 'display:none;' : '' }}">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: rgba(52,199,89,0.1); color: var(--success);">
                        <i class="fas fa-credit-card text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-base">Konfigurasi Tripay</h3>
                        <p class="text-xs" style="color: var(--text-secondary);">API credential dan mode operasi</p>
                    </div>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="form-label">API Key</label>
                        <input type="password" name="tripay_api_key" class="form-input" value="{{ $config['tripay_api_key'] }}" placeholder="DEV-xxxx">
                    </div>
                    <div>
                        <label class="form-label">Private Key</label>
                        <input type="password" name="tripay_private_key" class="form-input" value="{{ $config['tripay_private_key'] }}" placeholder="xxxx-xxxxx">
                    </div>
                    <div>
                        <label class="form-label">Merchant Code</label>
                        <input type="text" name="tripay_merchant_code" class="form-input" value="{{ $config['tripay_merchant_code'] }}" placeholder="T12345">
                    </div>
                    <div>
                        <label class="form-label">Mode</label>
                        <select name="tripay_mode" class="form-input">
                            <option value="sandbox" {{ $config['tripay_mode'] === 'sandbox' ? 'selected' : '' }}>Sandbox</option>
                            <option value="production" {{ $config['tripay_mode'] === 'production' ? 'selected' : '' }}>Production</option>
                        </select>
                    </div>
                    <div class="p-3 rounded-lg text-xs" style="background: var(--bg-tertiary); color: var(--text-secondary);">
                        <strong>Callback URL:</strong><br>
                        <code style="color: var(--success);">{{ route('callback.tripay') }}</code>
                    </div>
                </div>
                <div class="mt-4 pt-4" style="border-top: 1px solid var(--border);">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="testGateway('tripay')">
                        <i class="fas fa-vial mr-1"></i> Test Koneksi
                    </button>
                </div>
            </div>

            {{-- Pengaturan Pembayaran --}}
            <div class="card p-6">
                <h3 class="font-bold text-base mb-4">
                    <i class="fas fa-sliders-h mr-2" style="color: var(--accent);"></i> Pengaturan Pembayaran
                </h3>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="form-label">Durasi Expiry Checkout (detik)</label>
                            <input type="number" min="1800" max="172800" name="payment_expiry_seconds" class="form-input" value="{{ $config['payment_expiry_seconds'] }}">
                            <p class="text-[11px] mt-1" style="color: var(--text-tertiary);">Min 1800 (30 menit), maks 172800 (2 hari)</p>
                        </div>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="hidden" name="payment_dev_mode" value="0">
                            <input type="checkbox" name="payment_dev_mode" value="1" {{ $config['payment_dev_mode'] === '1' ? 'checked' : '' }} style="width:18px;height:18px;accent-color:var(--warning);">
                            <span class="text-sm font-semibold">Mode Development (Simulasi)</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="hidden" name="payment_allow_qris" value="0">
                            <input type="checkbox" name="payment_allow_qris" value="1" {{ $config['payment_allow_qris'] === '1' ? 'checked' : '' }} style="width:18px;height:18px;accent-color:var(--accent);">
                            <span class="text-sm font-semibold">Aktifkan QRIS</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="hidden" name="payment_allow_ewallet" value="0">
                            <input type="checkbox" name="payment_allow_ewallet" value="1" {{ $config['payment_allow_ewallet'] === '1' ? 'checked' : '' }} style="width:18px;height:18px;accent-color:var(--accent);">
                            <span class="text-sm font-semibold">Aktifkan E-Wallet</span>
                        </label>
                    </div>

                    <div class="space-y-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="hidden" name="payment_discount_enabled" value="0">
                            <input type="checkbox" name="payment_discount_enabled" value="1" {{ $config['payment_discount_enabled'] === '1' ? 'checked' : '' }} style="width:18px;height:18px;accent-color:var(--success);">
                            <span class="text-sm font-semibold">Aktifkan Diskon Global</span>
                        </label>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="form-label">Tipe Diskon</label>
                                <select name="payment_discount_type" class="form-input">
                                    <option value="percent" {{ $config['payment_discount_type'] === 'percent' ? 'selected' : '' }}>Persen (%)</option>
                                    <option value="fixed" {{ $config['payment_discount_type'] === 'fixed' ? 'selected' : '' }}>Nominal (Rp)</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Nilai Diskon</label>
                                <input type="number" min="0" step="0.01" name="payment_discount_value" class="form-input" value="{{ $config['payment_discount_value'] }}">
                            </div>
                        </div>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="hidden" name="payment_ppn_enabled" value="0">
                            <input type="checkbox" name="payment_ppn_enabled" value="1" {{ $config['payment_ppn_enabled'] === '1' ? 'checked' : '' }} style="width:18px;height:18px;accent-color:var(--warning);">
                            <span class="text-sm font-semibold">Aktifkan PPN</span>
                        </label>
                        <div>
                            <label class="form-label">PPN (%)</label>
                            <input type="number" min="0" step="0.01" name="payment_ppn_percent" class="form-input" value="{{ $config['payment_ppn_percent'] }}">
                        </div>
                    </div>
                </div>

                <div class="p-3 rounded-lg text-xs mt-4" style="background: var(--bg-tertiary); color: var(--text-secondary);">
                    Formula: <strong>Total = Harga Paket − Diskon + PPN</strong>
                </div>
                <div class="p-3 rounded-lg text-xs mt-3" style="background: rgba(245,158,11,.08); color: var(--text-secondary); border: 1px solid rgba(245,158,11,.18);">
                    Checklist production: set callback URL di dashboard vendor, isi credential, uji transaksi sandbox, lalu uji webhook paid dan expired sebelum switch live.
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-2"></i> Simpan Konfigurasi</button>
            </div>
        </form>
    </div>
</div>

<form id="testForm" method="POST" action="{{ route('admin.integration.payment-gateway.test') }}" style="display:none;">
    @csrf
    <input type="hidden" name="gateway" id="testGatewayInput">
</form>

<script>
const xenditColor = '#0071e3';
const tripayColor = 'var(--success)';

function switchVendor(vendor) {
    const isXendit = vendor === 'xendit';

    // Sections
    document.getElementById('section-xendit').style.display = isXendit ? '' : 'none';
    document.getElementById('section-tripay').style.display = isXendit ? 'none' : '';

    // Card styles
    const xenditCard = document.getElementById('card-xendit');
    const tripayCard = document.getElementById('card-tripay');

    xenditCard.style.borderColor = isXendit ? xenditColor : 'var(--border)';
    xenditCard.style.background = isXendit ? 'rgba(0,113,227,0.05)' : 'transparent';
    tripayCard.style.borderColor = isXendit ? 'var(--border)' : '#22c55e';
    tripayCard.style.background = isXendit ? 'transparent' : 'rgba(52,199,89,0.05)';

    // Dots
    const xenditDot = document.getElementById('dot-xendit');
    const tripayDot = document.getElementById('dot-tripay');

    xenditDot.style.borderColor = isXendit ? xenditColor : 'var(--border)';
    xenditDot.innerHTML = isXendit ? `<div class="w-2.5 h-2.5 rounded-full" style="background:${xenditColor};"></div>` : '';
    tripayDot.style.borderColor = isXendit ? 'var(--border)' : '#22c55e';
    tripayDot.innerHTML = isXendit ? '' : `<div class="w-2.5 h-2.5 rounded-full" style="background:#22c55e;"></div>`;
}

function testGateway(name) {
    document.getElementById('testGatewayInput').value = name;
    document.getElementById('testForm').submit();
}

// Attach click to labels for better UX
document.querySelectorAll('.vendor-card').forEach(card => {
    card.addEventListener('click', function () {
        const radio = this.querySelector('input[type=radio]');
        radio.checked = true;
        switchVendor(radio.value);
    });
});
</script>
@endsection
