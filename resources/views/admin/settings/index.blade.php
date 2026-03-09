@extends('layouts.admin')
@section('title', 'Pengaturan')
@section('page-title', 'Pengaturan Sistem')
@section('page-subtitle', 'Konfigurasi sistem undangan digital')

@section('content')
<div class="max-w-3xl">
    <div class="card p-6">
        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf @method('PUT')

            <h3 class="font-bold text-base mb-4 text-indigo-400"><i class="fas fa-globe mr-2"></i> Umum</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-6">
                <div>
                    <label class="form-label">Nama Aplikasi</label>
                    <input type="text" name="app_name" value="{{ $settings->get('general', collect())->firstWhere('key', 'app_name')->value ?? config('app.name') }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">Domain</label>
                    <input type="text" name="app_domain" value="{{ $settings->get('general', collect())->firstWhere('key', 'app_domain')->value ?? '' }}" class="form-input" placeholder="undangan.com">
                </div>
            </div>

            <hr class="border-[var(--border-color)] my-6">

            <h3 class="font-bold text-base mb-4 text-indigo-400"><i class="fas fa-building mr-2"></i> Perusahaan</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-6">
                <div>
                    <label class="form-label">Nama Perusahaan</label>
                    <input type="text" name="company_name" value="{{ $settings->get('company', collect())->firstWhere('key', 'company_name')->value ?? '' }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">Telepon</label>
                    <input type="text" name="company_phone" value="{{ $settings->get('company', collect())->firstWhere('key', 'company_phone')->value ?? '' }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">Email Perusahaan</label>
                    <input type="email" name="company_email" value="{{ $settings->get('company', collect())->firstWhere('key', 'company_email')->value ?? '' }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">Instagram</label>
                    <input type="text" name="company_instagram" value="{{ $settings->get('company', collect())->firstWhere('key', 'company_instagram')->value ?? '' }}" class="form-input">
                </div>
                <div class="sm:col-span-2">
                    <label class="form-label">Facebook</label>
                    <input type="text" name="company_facebook" value="{{ $settings->get('company', collect())->firstWhere('key', 'company_facebook')->value ?? '' }}" class="form-input">
                </div>
                <div class="sm:col-span-2">
                    <label class="form-label">Alamat Perusahaan</label>
                    <textarea name="company_address" class="form-input" rows="2">{{ $settings->get('company', collect())->firstWhere('key', 'company_address')->value ?? '' }}</textarea>
                </div>
            </div>

            <hr class="border-[var(--border-color)] my-6">

            <h3 class="font-bold text-base mb-4 text-indigo-400"><i class="fas fa-envelope mr-2"></i> Email</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-6">
                <div>
                    <label class="form-label">Email Pengirim</label>
                    <input type="email" name="mail_from" value="{{ $settings->get('email', collect())->firstWhere('key', 'mail_from')->value ?? '' }}" class="form-input" placeholder="noreply@undangan.com">
                </div>
                <div>
                    <label class="form-label">Nama Pengirim</label>
                    <input type="text" name="mail_from_name" value="{{ $settings->get('email', collect())->firstWhere('key', 'mail_from_name')->value ?? '' }}" class="form-input">
                </div>
            </div>

            <hr class="border-[var(--border-color)] my-6">

            <h3 class="font-bold text-base mb-4 text-indigo-400"><i class="fab fa-whatsapp mr-2"></i> Integrasi WhatsApp</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-6">
                <div>
                    <label class="form-label">Mode</label>
                    <select name="whatsapp_mode" class="form-input">
                        @php $waMode = $settings->get('integration', collect())->firstWhere('key', 'whatsapp_mode')->value ?? 'mock'; @endphp
                        <option value="mock" {{ $waMode === 'mock' ? 'selected' : '' }}>Mock (Development)</option>
                        <option value="live" {{ $waMode === 'live' ? 'selected' : '' }}>Live (Meta Official API)</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">API Version</label>
                    <input type="text" name="whatsapp_api_version" value="{{ $settings->get('integration', collect())->firstWhere('key', 'whatsapp_api_version')->value ?? 'v20.0' }}" class="form-input">
                </div>
                <div class="sm:col-span-2">
                    <label class="form-label">Phone Number ID</label>
                    <input type="text" name="whatsapp_phone_number_id" value="{{ $settings->get('integration', collect())->firstWhere('key', 'whatsapp_phone_number_id')->value ?? '' }}" class="form-input" placeholder="contoh: 123456789012345">
                </div>
                <div class="sm:col-span-2">
                    <label class="form-label">API Token</label>
                    <input type="text" name="whatsapp_api_token" value="{{ $settings->get('integration', collect())->firstWhere('key', 'whatsapp_api_token')->value ?? '' }}" class="form-input" placeholder="EAAG...">
                </div>
                <div class="sm:col-span-2">
                    <label class="form-label">Base URL</label>
                    <input type="text" name="whatsapp_base_url" value="{{ $settings->get('integration', collect())->firstWhere('key', 'whatsapp_base_url')->value ?? 'https://graph.facebook.com' }}" class="form-input">
                </div>
                <div class="sm:col-span-2">
                    <p class="text-xs" style="color: var(--text-secondary);">Gunakan mode <strong>mock</strong> untuk testing lokal tanpa request ke Meta API.</p>
                </div>
            </div>

            <button type="submit" class="btn-primary text-sm">
                <i class="fas fa-save mr-2"></i> Simpan Pengaturan
            </button>
        </form>
    </div>
</div>
@endsection
