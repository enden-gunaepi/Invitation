@extends('layouts.admin')
@section('title', 'Pengaturan')
@section('page-title', 'Pengaturan Sistem')
@section('page-subtitle', 'Konfigurasi sistem undangan digital')

@section('content')
@php
$user = auth()->user();
@endphp
<div class="max-w-4xl" x-data="{ tab: 'profile' }">
    {{-- Tabs Navigation --}}
    <div class="flex gap-2 p-1 bg-gray-100/50 rounded-xl mb-6 backdrop-blur-sm border border-gray-200/50 w-full overflow-x-auto">
        <button @click="tab = 'profile'"
            class="flex-1 min-w-[120px] py-2 px-4 rounded-lg text-sm font-semibold transition-all flex items-center justify-center whitespace-nowrap"
            :class="tab === 'profile' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'">
            <i class="fas fa-user mr-2"></i>Profile Admin
        </button>
        <button @click="tab = 'company'"
             class="flex-1 min-w-[120px] py-2 px-4 rounded-lg text-sm font-semibold transition-all flex items-center justify-center whitespace-nowrap"
            :class="tab === 'company' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'">
            <i class="fas fa-building mr-2"></i>Data Perusahaan
        </button>
        <button @click="tab = 'system'"
             class="flex-1 min-w-[120px] py-2 px-4 rounded-lg text-sm font-semibold transition-all flex items-center justify-center whitespace-nowrap"
            :class="tab === 'system' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'">
            <i class="fas fa-globe mr-2"></i>Sistem Global
        </button>
    </div>

    {{-- TAB: PROFILE PRIBADI --}}
    <div x-show="tab === 'profile'" x-transition class="card p-6 mb-6">
        <h3 class="font-bold text-base mb-4 text-indigo-400"><i class="fas fa-user mr-2"></i>Informasi Akun Admin</h3>
        <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('patch')

            <div class="flex items-center gap-4 mb-4">
                <div class="w-16 h-16 rounded-full bg-gray-200 overflow-hidden shadow-sm shrink-0 border border-gray-300">
                    @if($user->avatar)
                        <img src="{{ Storage::url($user->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-xl font-bold bg-gradient-to-br from-blue-100 to-indigo-100 text-blue-800">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                    @endif
                </div>
                <div class="flex-1">
                    <label class="form-label mb-1">Foto Profil Admin</label>
                    <input type="file" name="avatar" class="form-input text-xs w-full pb-2 pt-2 h-auto" accept="image/*">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-input" required>
                </div>
            </div>
            
            <button type="submit" class="btn-primary mt-4 text-sm"><i class="fas fa-save mr-2"></i>Simpan Profil Pribadi</button>
        </form>

        <hr class="border-[var(--border-color)] my-6">

        <h3 class="font-bold text-base mb-4 text-indigo-400"><i class="fas fa-lock mr-2"></i>Keamanan (Ubah Password)</h3>
        <form method="post" action="{{ route('password.update') }}" class="space-y-4">
            @csrf
            @method('put')

            <div class="grid grid-cols-1 gap-5">
                <div>
                    <label class="form-label">Password Lama</label>
                    <input type="password" name="current_password" class="form-input max-w-sm">
                </div>
                <div>
                    <label class="form-label">Password Baru</label>
                    <input type="password" name="password" class="form-input max-w-sm">
                </div>
                <div>
                    <label class="form-label">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" class="form-input max-w-sm">
                </div>
            </div>
            <button type="submit" class="btn-primary mt-4 text-sm"><i class="fas fa-key mr-2"></i>Update Password</button>
        </form>
    </div>

    {{-- TAB: PERUSAHAAN --}}
    <div x-show="tab === 'company'" style="display: none;" x-transition class="card p-6 mb-6">
        <h3 class="font-bold text-base mb-4 text-indigo-400"><i class="fas fa-building mr-2"></i>Identitas Brand Perusahaan</h3>
        <p class="text-xs text-gray-500 mb-6">Logo dan nama yang diatur di sini akan menjadi identitas Anda yang akan muncul di Sidebar atas dan laporan-laporan invoice.</p>
        
        <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('patch')
            <input type="hidden" name="name" value="{{ $user->name }}">
            <input type="hidden" name="email" value="{{ $user->email }}">

            <div class="flex items-center gap-4 mb-4">
                <div class="w-16 h-16 rounded-xl bg-gray-200 overflow-hidden shadow-sm shrink-0 border border-gray-300">
                    @if($user->company_logo)
                        <img src="{{ Storage::url($user->company_logo) }}" alt="Logo Perusahaan" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-2xl font-bold bg-gradient-to-br from-gray-700 to-black text-white">
                            <i class="fas fa-building"></i>
                        </div>
                    @endif
                </div>
                <div class="flex-1">
                    <label class="form-label mb-1">Upload Logo Perusahaan</label>
                    <input type="file" name="company_logo" class="form-input text-xs max-w-sm pb-2 pt-2 h-auto" accept="image/*">
                </div>
            </div>

            <div>
                <label class="form-label">Nama Perusahaan / Brand Vendor</label>
                <input type="text" name="company_name" value="{{ old('company_name', $user->company_name) }}" class="form-input max-w-sm" placeholder="Contoh: Digital Invitation Vendor">
            </div>

            <button type="submit" class="btn-primary mt-4 text-sm"><i class="fas fa-save mr-2"></i>Simpan Identitas Perusahaan</button>
        </form>
    </div>

    {{-- TAB: SISTEM GLOBAL --}}
    <div x-show="tab === 'system'" style="display: none;" x-transition class="card p-6">
        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf @method('PUT')

            <h3 class="font-bold text-base mb-4 text-indigo-400"><i class="fas fa-globe mr-2"></i> Umum / Aplikasi</h3>
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

            <h3 class="font-bold text-base mb-4 text-indigo-400"><i class="fas fa-building mr-2"></i> Kontak Platform (Global)</h3>
            <p class="text-xs text-gray-500 mb-4">Informasi ini adalah master kontak dari sistem secara menyeluruh, berbeda dari identitas brand personal admin.</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-6">
                <div>
                    <label class="form-label">Nama Platform</label>
                    <input type="text" name="company_name" value="{{ $settings->get('company', collect())->firstWhere('key', 'company_name')->value ?? '' }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">Telepon Customer Service</label>
                    <input type="text" name="company_phone" value="{{ $settings->get('company', collect())->firstWhere('key', 'company_phone')->value ?? '' }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">Email Customer Service</label>
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
                    <label class="form-label">Alamat Kantor</label>
                    <textarea name="company_address" class="form-input" rows="2">{{ $settings->get('company', collect())->firstWhere('key', 'company_address')->value ?? '' }}</textarea>
                </div>
            </div>

            <hr class="border-[var(--border-color)] my-6">

            <h3 class="font-bold text-base mb-4 text-indigo-400"><i class="fas fa-envelope mr-2"></i> Gateway Email (SMTP Fallback)</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-6">
                <div>
                    <label class="form-label">Email Pengirim Asal</label>
                    <input type="email" name="mail_from" value="{{ $settings->get('email', collect())->firstWhere('key', 'mail_from')->value ?? '' }}" class="form-input" placeholder="noreply@undangan.com">
                </div>
                <div>
                    <label class="form-label">Nama Pengirim Asal</label>
                    <input type="text" name="mail_from_name" value="{{ $settings->get('email', collect())->firstWhere('key', 'mail_from_name')->value ?? '' }}" class="form-input">
                </div>
            </div>

            <hr class="border-[var(--border-color)] my-6">

            <h3 class="font-bold text-base mb-4 text-indigo-400"><i class="fab fa-whatsapp mr-2"></i> Integrasi WhatsApp API Gateway</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-6">
                <div>
                    <label class="form-label">Mode Notifikasi</label>
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
                    <label class="form-label">Meta Session API Token</label>
                    <input type="text" name="whatsapp_api_token" value="{{ $settings->get('integration', collect())->firstWhere('key', 'whatsapp_api_token')->value ?? '' }}" class="form-input" placeholder="EAAG...">
                </div>
                <div class="sm:col-span-2">
                    <label class="form-label">Base URL (Graph API)</label>
                    <input type="text" name="whatsapp_base_url" value="{{ $settings->get('integration', collect())->firstWhere('key', 'whatsapp_base_url')->value ?? 'https://graph.facebook.com' }}" class="form-input">
                </div>
            </div>

            <button type="submit" class="btn-primary text-sm">
                <i class="fas fa-save mr-2"></i> Simpan Pengaturan Global
            </button>
        </form>
    </div>
</div>
@endsection
