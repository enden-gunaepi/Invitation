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

            <button type="submit" class="btn-primary text-sm">
                <i class="fas fa-save mr-2"></i> Simpan Pengaturan
            </button>
        </form>
    </div>
</div>
@endsection
