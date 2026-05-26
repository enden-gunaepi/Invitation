@extends('layouts.admin')
@section('title', 'Pengaturan')
@section('page-title', 'Pengaturan Sistem')
@section('page-subtitle', 'Konfigurasi sistem undangan digital')

@section('content')
@php
    $user = auth()->user();
    $companyGroup = $settings->get('company', collect());
    $generalGroup = $settings->get('general', collect());
@endphp
<div class="w-full max-w-[88rem]" x-data="{ tab: 'profile' }">
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
    </div>

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

    <div x-show="tab === 'company'" style="display: none;" x-transition class="card p-6 mb-6">
        <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <h3 class="font-bold text-base mb-4 text-indigo-400"><i class="fas fa-building mr-2"></i>Identitas Brand Perusahaan</h3>
            <p class="text-xs text-gray-500 mb-6">Nama dan logo di sini menjadi sumber utama brand aplikasi di landing page, semua halaman auth, serta sidebar admin dan client.</p>

            <div class="flex items-center gap-4 mb-4">
                <div class="w-16 h-16 rounded-xl bg-gray-200 overflow-hidden shadow-sm shrink-0 border border-gray-300">
                    @if($brandLogoUrl)
                        <img src="{{ $brandLogoUrl }}" alt="Logo Perusahaan" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-2xl font-bold bg-gradient-to-br from-gray-700 to-black text-white">
                            <i class="fas fa-building"></i>
                        </div>
                    @endif
                </div>
                <div class="flex-1">
                    <label class="form-label mb-1">Upload Logo Perusahaan</label>
                    <input type="file" name="company_logo" class="form-input text-xs max-w-sm pb-2 pt-2 h-auto" accept="image/*">
                    @error('company_logo') <p class="text-xs mt-1 text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-6">
                <div>
                    <label class="form-label">Nama Perusahaan / Brand Vendor</label>
                    <input type="text" name="company_name" value="{{ old('company_name', $companyGroup->firstWhere('key', 'company_name')->value ?? '') }}" class="form-input" placeholder="Contoh: Digital Invitation Vendor">
                </div>
                <div>
                    <label class="form-label">Nama Aplikasi</label>
                    <input type="text" name="app_name" value="{{ old('app_name', $generalGroup->firstWhere('key', 'app_name')->value ?? config('app.name')) }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">Domain</label>
                    <input type="text" name="app_domain" value="{{ old('app_domain', $generalGroup->firstWhere('key', 'app_domain')->value ?? '') }}" class="form-input" placeholder="undangan.com">
                </div>
                <div>
                    <label class="form-label">Telepon Customer Service</label>
                    <input type="text" name="company_phone" value="{{ old('company_phone', $companyGroup->firstWhere('key', 'company_phone')->value ?? '') }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">Email Customer Service</label>
                    <input type="email" name="company_email" value="{{ old('company_email', $companyGroup->firstWhere('key', 'company_email')->value ?? '') }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">Instagram</label>
                    <input type="text" name="company_instagram" value="{{ old('company_instagram', $companyGroup->firstWhere('key', 'company_instagram')->value ?? '') }}" class="form-input">
                </div>
                <div class="sm:col-span-2">
                    <label class="form-label">Facebook</label>
                    <input type="text" name="company_facebook" value="{{ old('company_facebook', $companyGroup->firstWhere('key', 'company_facebook')->value ?? '') }}" class="form-input">
                </div>
                <div class="sm:col-span-2">
                    <label class="form-label">Alamat Kantor</label>
                    <textarea name="company_address" class="form-input" rows="2">{{ old('company_address', $companyGroup->firstWhere('key', 'company_address')->value ?? '') }}</textarea>
                </div>
            </div>

            <div class="mt-2 rounded-2xl border border-indigo-100 bg-indigo-50/70 px-4 py-4 text-sm text-indigo-700">
                Konfigurasi email gateway dan WhatsApp API sekarang dikelola terpusat dari menu <strong>Integrasi</strong> agar pengaturan operasional tidak tercampur dengan data perusahaan.
            </div>

            <button type="submit" class="btn-primary mt-6 text-sm">
                <i class="fas fa-save mr-2"></i>Simpan Data Perusahaan
            </button>
        </form>
    </div>
</div>
@endsection
