@extends('layouts.admin')
@section('title', 'Payment Gateway')
@section('page-title', 'Payment Gateway')
@section('page-subtitle', 'Konfigurasi integrasi pembayaran production')

@section('content')
<form method="POST" action="{{ route('admin.payment-gateway.update') }}">
    @csrf @method('PUT')

    <div class="card p-6 mb-6">
        <h3 class="font-bold text-base mb-4">Mode Production</h3>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div>
                    <label class="form-label">Gateway Utama</label>
                    <select name="payment_primary_gateway" class="form-input">
                        <option value="xendit" {{ $config['payment_primary_gateway'] === 'xendit' ? 'selected' : '' }}>Xendit</option>
                        <option value="tripay" {{ $config['payment_primary_gateway'] === 'tripay' ? 'selected' : '' }}>Tripay</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Durasi Expiry Checkout (detik)</label>
                    <input type="number" min="1800" max="172800" name="payment_expiry_seconds" class="form-input" value="{{ $config['payment_expiry_seconds'] }}">
                </div>
            </div>
            <div class="p-4 rounded-lg text-sm" style="background: var(--bg-tertiary); color: var(--text-secondary);">
                Gunakan satu gateway utama untuk launch awal. Rekomendasi saat ini adalah Xendit dengan QRIS + E-Wallet agar rollout production lebih stabil.
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="card p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="stat-icon" style="background: rgba(0,113,227,0.1); color: #0071e3;"><i class="fas fa-bolt"></i></div>
                    <div>
                        <h3 class="font-bold text-base">Xendit</h3>
                        <p class="text-xs" style="color: var(--text-secondary);">Primary gateway production v1</p>
                    </div>
                </div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="hidden" name="xendit_enabled" value="0">
                    <input type="checkbox" name="xendit_enabled" value="1" {{ $config['xendit_enabled'] === '1' ? 'checked' : '' }} style="width:18px;height:18px;accent-color:var(--accent);">
                    <span class="text-xs font-semibold">Aktif</span>
                </label>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="form-label">Secret API Key</label>
                    <input type="password" name="xendit_secret_key" class="form-input" value="{{ $config['xendit_secret_key'] }}" placeholder="xnd_development_xxxx">
                </div>
                <div>
                    <label class="form-label">Callback Verification Token</label>
                    <input type="text" name="xendit_callback_token" class="form-input" value="{{ $config['xendit_callback_token'] }}" placeholder="Token dari dashboard Xendit">
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
                    <code style="color: var(--accent);">{{ route('callback.xendit') }}</code>
                </div>
            </div>

            <div class="mt-4 pt-4" style="border-top: 1px solid var(--border);">
                <button type="button" class="btn btn-secondary btn-sm" onclick="testGateway('xendit')">
                    <i class="fas fa-vial mr-1"></i> Test Koneksi
                </button>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="stat-icon" style="background: rgba(52,199,89,0.1); color: var(--success);"><i class="fas fa-credit-card"></i></div>
                    <div>
                        <h3 class="font-bold text-base">Tripay</h3>
                        <p class="text-xs" style="color: var(--text-secondary);">Fallback / phase 2 gateway</p>
                    </div>
                </div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="hidden" name="tripay_enabled" value="0">
                    <input type="checkbox" name="tripay_enabled" value="1" {{ $config['tripay_enabled'] === '1' ? 'checked' : '' }} style="width:18px;height:18px;accent-color:var(--success);">
                    <span class="text-xs font-semibold">Aktif</span>
                </label>
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
    </div>

    <div class="card p-6 mt-6">
        <h3 class="font-bold text-base mb-4">
            <i class="fas fa-calculator mr-2" style="color: var(--accent);"></i> Aturan Tagihan
        </h3>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="space-y-4">
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
            Formula: <strong>Total = Harga Paket - Diskon + PPN</strong>. Nilai total otomatis dipakai saat checkout QRIS/E-Wallet.
        </div>
        <div class="p-3 rounded-lg text-xs mt-4" style="background: rgba(245,158,11,.08); color: var(--text-secondary); border: 1px solid rgba(245,158,11,.18);">
            Checklist production: set callback URL, isi callback token, aktifkan credential gateway, uji transaksi sandbox, lalu uji webhook paid dan expired sebelum switch live.
        </div>
    </div>

    <div class="mt-6">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-2"></i> Simpan Konfigurasi</button>
    </div>
</form>

<form id="testForm" method="POST" action="{{ route('admin.payment-gateway.test') }}" style="display:none;">
    @csrf
    <input type="hidden" name="gateway" id="testGateway">
</form>
<script>
function testGateway(name) {
    document.getElementById('testGateway').value = name;
    document.getElementById('testForm').submit();
}
</script>
@endsection
