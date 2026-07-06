@extends('layouts.admin')
@section('title', 'Rekening Bank Transfer Manual')
@section('page-title', 'Rekening Bank')
@section('page-subtitle', 'Kelola rekening tujuan transfer manual untuk client')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Back --}}
    <a href="{{ route('admin.manual-transfer.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold hover:underline" style="color: var(--accent);">
        <i class="fas fa-arrow-left"></i> Kembali ke Daftar Transfer
    </a>

    {{-- Daftar Rekening --}}
    <div class="card p-6">
        <h3 class="font-bold text-base mb-4 flex items-center gap-2" style="color: var(--accent);">
            <i class="fas fa-university"></i> Daftar Rekening Aktif
        </h3>

        @if($accounts->isEmpty())
        <div class="text-center py-12" style="color: var(--text-secondary);">
            <i class="fas fa-university text-4xl mb-3 block opacity-30"></i>
            <p class="text-sm">Belum ada rekening. Tambahkan rekening di bawah.</p>
        </div>
        @else
        <div class="space-y-3 mb-6">
            @foreach($accounts as $account)
            <div class="flex items-center gap-4 p-4 rounded-xl border transition-all hover:shadow-sm"
                 style="border-color: var(--border-color); background: {{ $account->is_active ? 'var(--surface-container-low)' : 'var(--bg-tertiary)' }};">
                {{-- Bank Icon --}}
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background: var(--accent-bg); color: var(--accent);">
                    <i class="fas fa-university"></i>
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-sm">{{ $account->bank_name }}</span>
                        @if(!$account->is_active)
                        <span class="text-xs px-2 py-0.5 rounded" style="background: rgba(255,59,48,0.1); color: var(--danger);">Nonaktif</span>
                        @endif
                    </div>
                    <div class="text-sm font-mono mt-0.5">{{ $account->account_number }}</div>
                    <div class="text-xs mt-0.5" style="color: var(--text-secondary);">a/n {{ $account->account_holder_name }}</div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-2 flex-shrink-0" x-data="{ editing: false }">
                    <button @click="editing = !editing" type="button"
                            class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all"
                            style="background: var(--surface-container); color: var(--text-secondary);">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <form method="POST" action="{{ route('admin.manual-transfer.bank-accounts.destroy', $account) }}"
                          onsubmit="return confirm('Hapus rekening {{ $account->bank_name }} - {{ $account->account_number }}?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all"
                                style="background: rgba(255,59,48,0.1); color: var(--danger);">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>

                    {{-- Inline Edit Form --}}
                    <div x-show="editing" x-transition
                         class="absolute left-0 right-0 z-10 mt-2 card p-4 shadow-xl"
                         style="top: 100%;" @click.outside="editing = false">
                    </div>
                </div>
            </div>

            {{-- Edit Form (per rekening) --}}
            <div x-data="{ editing_{{ $account->id }}: false }">
                <script>document.addEventListener('DOMContentLoaded', () => {})</script>
                <form method="POST" action="{{ route('admin.manual-transfer.bank-accounts.update', $account) }}"
                      id="edit-form-{{ $account->id }}" class="hidden p-4 rounded-xl border mt-1 space-y-3"
                      style="border-color: var(--border-color); background: var(--surface-container-low);">
                    @csrf @method('PUT')
                    <h4 class="text-sm font-bold" style="color: var(--accent);">Edit Rekening</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="form-label text-xs">Nama Bank</label>
                            <input type="text" name="bank_name" value="{{ $account->bank_name }}" class="form-input text-sm" required>
                        </div>
                        <div>
                            <label class="form-label text-xs">Nomor Rekening</label>
                            <input type="text" name="account_number" value="{{ $account->account_number }}" class="form-input text-sm" required>
                        </div>
                        <div>
                            <label class="form-label text-xs">Nama Pemilik</label>
                            <input type="text" name="account_holder_name" value="{{ $account->account_holder_name }}" class="form-input text-sm" required>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <label class="flex items-center gap-2 cursor-pointer text-sm">
                            <input type="checkbox" name="is_active" value="1" {{ $account->is_active ? 'checked' : '' }}
                                   class="rounded">
                            Aktif (ditampilkan ke client)
                        </label>
                        <input type="number" name="sort_order" value="{{ $account->sort_order }}"
                               class="form-input text-sm w-24" placeholder="Urutan" min="0">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="btn-primary text-sm py-2">
                            <i class="fas fa-save mr-1"></i> Simpan
                        </button>
                        <button type="button" onclick="document.getElementById('edit-form-{{ $account->id }}').classList.add('hidden')"
                                class="px-4 py-2 rounded-lg text-sm" style="background: var(--surface-container); color: var(--text-secondary);">
                            Batal
                        </button>
                    </div>
                </form>
                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        document.querySelectorAll('[data-edit-toggle="{{ $account->id }}"]').forEach(btn => {
                            btn.addEventListener('click', () => {
                                document.getElementById('edit-form-{{ $account->id }}').classList.toggle('hidden');
                            });
                        });
                    });
                </script>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Form Tambah Rekening --}}
    <div class="card p-6">
        <h3 class="font-bold text-base mb-4 flex items-center gap-2" style="color: var(--accent);">
            <i class="fas fa-plus-circle"></i> Tambah Rekening Baru
        </h3>
        <form method="POST" action="{{ route('admin.manual-transfer.bank-accounts.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="form-label">Nama Bank <span class="text-red-500">*</span></label>
                    <input type="text" name="bank_name" value="{{ old('bank_name') }}" class="form-input"
                           placeholder="BCA, BRI, Mandiri, BNI..." required>
                    @error('bank_name') <p class="text-xs mt-1 text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Nomor Rekening <span class="text-red-500">*</span></label>
                    <input type="text" name="account_number" value="{{ old('account_number') }}" class="form-input font-mono"
                           placeholder="1234567890" required>
                    @error('account_number') <p class="text-xs mt-1 text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Nama Pemilik Rekening <span class="text-red-500">*</span></label>
                    <input type="text" name="account_holder_name" value="{{ old('account_holder_name') }}" class="form-input"
                           placeholder="PT. Nama Perusahaan" required>
                    @error('account_holder_name') <p class="text-xs mt-1 text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="flex items-center gap-4">
                <label class="flex items-center gap-2 cursor-pointer text-sm">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded">
                    Aktif (langsung ditampilkan ke client)
                </label>
                <div class="flex items-center gap-2">
                    <label class="text-sm" style="color: var(--text-secondary);">Urutan:</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}"
                           class="form-input text-sm w-24" min="0">
                </div>
            </div>
            <button type="submit" class="btn-primary">
                <i class="fas fa-plus mr-2"></i> Tambah Rekening
            </button>
        </form>
    </div>

</div>

{{-- Inline Edit Script --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[id^="edit-form-"]').forEach(form => {
        const id = form.id.replace('edit-form-', '');
        const toggleBtns = document.querySelectorAll(`[data-edit-toggle="${id}"]`);
        toggleBtns.forEach(btn => btn.addEventListener('click', () => form.classList.toggle('hidden')));
    });
});
</script>
@endsection
