@extends('layouts.admin')
@section('title', isset($package) ? 'Edit Paket' : 'Tambah Paket')
@section('page-title', isset($package) ? 'Edit Paket' : 'Tambah Paket')

@section('content')
<div class="max-w-2xl">
    <div class="card p-6">
        <form method="POST" action="{{ isset($package) ? route('admin.packages.update', $package) : route('admin.packages.store') }}">
            @csrf
            @if(isset($package)) @method('PUT') @endif

            <div class="mb-5">
                <label class="form-label">Nama Paket</label>
                <input type="text" name="name" value="{{ old('name', $package->name ?? '') }}" class="form-input" required>
            </div>
            <div class="mb-5">
                <label class="form-label">Tier Paket</label>
                <select name="tier" class="form-input" required>
                    @php $tier = old('tier', $package->tier ?? 'starter'); @endphp
                    <option value="starter" {{ $tier === 'starter' ? 'selected' : '' }}>Starter</option>
                    <option value="growth" {{ $tier === 'growth' ? 'selected' : '' }}>Growth</option>
                    <option value="pro" {{ $tier === 'pro' ? 'selected' : '' }}>Pro</option>
                    <option value="enterprise" {{ $tier === 'enterprise' ? 'selected' : '' }}>Enterprise</option>
                </select>
            </div>
            <div class="mb-5">
                <label class="form-label">Deskripsi</label>
                <textarea name="description" class="form-input" rows="3">{{ old('description', $package->description ?? '') }}</textarea>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-5">
                <div>
                    <label class="form-label">Badge Teks</label>
                    <input type="text" name="badge_text" value="{{ old('badge_text', $package->badge_text ?? '') }}" class="form-input" placeholder="Best Value">
                </div>
                <div>
                    <label class="form-label">Support Level</label>
                    <input type="text" name="support_level" value="{{ old('support_level', $package->support_level ?? '') }}" class="form-input" placeholder="Priority WhatsApp">
                </div>
                <div>
                    <label class="form-label">SLA (Jam)</label>
                    <input type="number" name="sla_hours" value="{{ old('sla_hours', $package->sla_hours ?? '') }}" class="form-input" min="1" max="168" placeholder="24">
                </div>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-5 mb-5">
                <div>
                    <label class="form-label">Harga (Rp)</label>
                    <input type="number" name="price" value="{{ old('price', $package->price ?? 0) }}" class="form-input" min="0" step="1000" required>
                </div>
                <div>
                    <label class="form-label">Komisi Affiliate (%)</label>
                    <input type="number" name="affiliate_commission_rate" value="{{ old('affiliate_commission_rate', $package->affiliate_commission_rate ?? 5) }}" class="form-input" min="0" max="100" step="0.01">
                    <p class="text-xs mt-1 text-slate-400">Atur persen komisi affiliate per paket di sini.</p>
                </div>
                <div>
                    <label class="form-label">Billing Type</label>
                    <select name="billing_type" class="form-input" required>
                        @php $bt = old('billing_type', $package->billing_type ?? 'one_time'); @endphp
                        <option value="one_time" {{ $bt === 'one_time' ? 'selected' : '' }}>One-time</option>
                        <option value="subscription" {{ $bt === 'subscription' ? 'selected' : '' }}>Subscription</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Billing Cycle</label>
                    <select name="billing_cycle" class="form-input">
                        @php $bc = old('billing_cycle', $package->billing_cycle ?? 'monthly'); @endphp
                        <option value="monthly" {{ $bc === 'monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="yearly" {{ $bc === 'yearly' ? 'selected' : '' }}>Yearly</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Max Tamu</label>
                    <input type="number" name="max_guests" value="{{ old('max_guests', $package->max_guests ?? 100) }}" class="form-input" min="1" required>
                </div>
                <div>
                    <label class="form-label">Max Foto</label>
                    <input type="number" name="max_photos" value="{{ old('max_photos', $package->max_photos ?? 10) }}" class="form-input" min="1" required>
                </div>
                <div>
                    <label class="form-label">Max Undangan</label>
                    <input type="number" name="max_invitations" value="{{ old('max_invitations', $package->max_invitations ?? 1) }}" class="form-input" min="1" required>
                </div>
            </div>
            <div class="mb-5">
                <label class="form-label">Fitur Paket (1 baris = 1 fitur)</label>
                <textarea name="features_input" class="form-input" rows="4" placeholder="Contoh:&#10;RSVP tanpa batas&#10;Galeri 30 foto&#10;Love Story">{{ old('features_input', isset($package) ? implode("\n", $package->features ?? []) : '') }}</textarea>
            </div>
            <div class="mb-5">
                <label class="form-label">Add-on Bernilai (1 baris = 1 item)</label>
                <textarea name="addons_input" class="form-input" rows="3" placeholder="Contoh:&#10;Custom domain gratis 1 tahun&#10;Template premium eksklusif">{{ old('addons_input', isset($package) ? implode("\n", $package->addons ?? []) : '') }}</textarea>
            </div>
            <div class="mb-5">
                <label class="form-label">Template Yang Diizinkan</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @foreach($templates as $template)
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" name="allowed_template_ids[]" value="{{ $template->id }}"
                                {{ in_array($template->id, old('allowed_template_ids', $package->allowed_template_ids ?? [])) ? 'checked' : '' }}>
                            <span>{{ $template->name }}</span>
                        </label>
                    @endforeach
                </div>
                <p class="text-xs mt-1 text-slate-400">Kosongkan jika paket boleh semua template.</p>
            </div>
            <div class="mb-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $package->is_active ?? true) ? 'checked' : '' }} class="w-4 h-4 rounded bg-slate-800 border-slate-600 text-indigo-500">
                    <span class="text-sm text-slate-400">Aktif</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer mt-2">
                    <input type="checkbox" name="is_recommended" value="1" {{ old('is_recommended', $package->is_recommended ?? false) ? 'checked' : '' }} class="w-4 h-4 rounded bg-slate-800 border-slate-600 text-indigo-500">
                    <span class="text-sm text-slate-400">Tandai sebagai rekomendasi utama</span>
                </label>
            </div>
            <div class="flex items-center gap-3">
                <button type="submit" class="btn-primary text-sm"><i class="fas fa-save mr-2"></i> Simpan</button>
                <a href="{{ route('admin.packages.index') }}" class="btn-outline text-sm">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
