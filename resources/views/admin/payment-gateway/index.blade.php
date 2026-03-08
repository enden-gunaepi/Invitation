@extends('layouts.admin')
@section('title', 'Payment Gateway')
@section('page-title', 'Payment Gateway')
@section('page-subtitle', 'Konfigurasi integrasi Xendit & Tripay')

@section('content')
<form method="POST" action="{{ route('admin.payment-gateway.update') }}">
    @csrf @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Xendit --}}
        <div class="card p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="stat-icon" style="background: rgba(0,113,227,0.1); color: #0071e3;"><i class="fas fa-bolt"></i></div>
                    <div>
                        <h3 class="font-bold text-base">Xendit</h3>
                        <p class="text-xs" style="color: var(--text-secondary);">Payment gateway universal</p>
                    </div>
                </div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="hidden" name="xendit_enabled" value="0">
                    <input type="checkbox" name="xendit_enabled" value="1" {{ $config['xendit_enabled'] === '1' ? 'checked' : '' }}
                        style="width:18px;height:18px;accent-color:var(--accent);">
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
                        <option value="sandbox" {{ $config['xendit_mode'] === 'sandbox' ? 'selected' : '' }}>🧪 Sandbox</option>
                        <option value="production" {{ $config['xendit_mode'] === 'production' ? 'selected' : '' }}>🚀 Production</option>
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

        {{-- Tripay --}}
        <div class="card p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="stat-icon" style="background: rgba(52,199,89,0.1); color: var(--success);"><i class="fas fa-credit-card"></i></div>
                    <div>
                        <h3 class="font-bold text-base">Tripay</h3>
                        <p class="text-xs" style="color: var(--text-secondary);">Payment aggregator Indonesia</p>
                    </div>
                </div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="hidden" name="tripay_enabled" value="0">
                    <input type="checkbox" name="tripay_enabled" value="1" {{ $config['tripay_enabled'] === '1' ? 'checked' : '' }}
                        style="width:18px;height:18px;accent-color:var(--success);">
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
                        <option value="sandbox" {{ $config['tripay_mode'] === 'sandbox' ? 'selected' : '' }}>🧪 Sandbox</option>
                        <option value="production" {{ $config['tripay_mode'] === 'production' ? 'selected' : '' }}>🚀 Production</option>
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

    <div class="mt-6">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-2"></i> Simpan Konfigurasi</button>
    </div>
</form>

{{-- Test Gateway Form (hidden) --}}
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
