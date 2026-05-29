@extends('layouts.admin')
@section('title', 'Manajemen Saldo')
@section('page-title', 'Manajemen Saldo')
@section('page-subtitle', 'Pantau dan kelola saldo pengguna')

@section('content')
<div x-data="{
    adjustModalOpen: false,
    confirmModalOpen: false,
    adjustUserName: '',
    adjustUserBalance: '',
    confirmActionType: 'add',
    confirmAmount: '',
    confirmNote: '',
    confirmForm: null,
    adjustFormAction: '',
    openAdjustModal(userId, userName, userBalance) {
        this.adjustUserName = userName;
        this.adjustUserBalance = userBalance;
        this.adjustFormAction = '{{ route('admin.balance.adjust', ['user' => '__USER_ID__']) }}'.replace('__USER_ID__', userId);
        this.adjustModalOpen = true;
    },
    formatRupiah(value) {
        const amount = Number(value || 0);
        return 'Rp' + amount.toLocaleString('id-ID');
    },
    openConfirmModal(form) {
        this.confirmForm = form;
        this.confirmActionType = form.action_type.value;
        this.confirmAmount = form.amount.value;
        this.confirmNote = form.admin_note.value;
        this.confirmModalOpen = true;
    },
    submitConfirmedForm() {
        if (!this.confirmForm) return;
        this.confirmModalOpen = false;
        this.adjustModalOpen = false;
        this.confirmForm.submit();
    }
}"
@open-balance-confirm="
    confirmForm = $event.detail.form;
    confirmActionType = $event.detail.actionType;
    confirmAmount = $event.detail.amount;
    confirmNote = $event.detail.note;
    confirmModalOpen = true;
">
{{-- Stats --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <div class="card stat-card">
        <div class="stat-icon bg-pink-500/10 text-pink-500" style="background: rgba(219, 39, 119, 0.1); color: var(--accent);"><i class="fas fa-wallet"></i></div>
        <div class="stat-value">Rp{{ number_format($stats['total_users_balance'], 0, ',', '.') }}</div>
        <div class="stat-label">Total Saldo Pengguna</div>
    </div>
    <div class="card stat-card">
        <div class="stat-icon bg-emerald-500/10 text-emerald-500" style="background: rgba(16, 185, 129, 0.1); color: var(--success);"><i class="fas fa-arrow-trend-up"></i></div>
        <div class="stat-value">Rp{{ number_format($stats['topup_this_month'], 0, ',', '.') }}</div>
        <div class="stat-label">Top-up Bulan Ini</div>
    </div>
    <div class="card stat-card">
        <div class="stat-icon bg-blue-500/10 text-blue-500" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;"><i class="fas fa-shopping-cart"></i></div>
        <div class="stat-value">Rp{{ number_format($stats['purchase_this_month'], 0, ',', '.') }}</div>
        <div class="stat-label">Pengeluaran Bulan Ini</div>
    </div>
    <div class="card stat-card">
        <div class="stat-icon bg-amber-500/10 text-amber-500" style="background: rgba(245, 158, 11, 0.1); color: var(--warning);"><i class="fas fa-sliders"></i></div>
        <div class="stat-value">Rp{{ number_format($stats['adjustment_this_month'], 0, ',', '.') }}</div>
        <div class="stat-label">Adjustment Bulan Ini</div>
    </div>
</div>

{{-- Toolbar / Filters --}}
<div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 mb-6">
    <form method="GET" class="flex flex-1 items-center gap-3">
        <div class="relative flex-1 max-w-md">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                <i class="fas fa-search"></i>
            </span>
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau email user..."
                class="form-input pl-9 w-full">
        </div>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-filter mr-1"></i> Cari
        </button>
        @if($search)
            <a href="{{ route('admin.balance.index') }}" class="btn btn-secondary">Reset</a>
        @endif
    </form>

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.balance.transactions') }}" class="btn btn-secondary">
            <i class="fas fa-history mr-2"></i> Log Mutasi Global
        </a>
    </div>
</div>

{{-- Users Balance Table --}}
<div class="card overflow-hidden">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>User</th>
                    <th>Email</th>
                    <th>
                        <a href="{{ route('admin.balance.index', array_merge(request()->query(), ['sort_by' => 'balance', 'sort_order' => $sortOrder === 'asc' && $sortBy === 'balance' ? 'desc' : 'asc'])) }}" class="flex items-center gap-1 hover:text-slate-200">
                            Saldo
                            @if($sortBy === 'balance')
                                <i class="fas fa-sort-{{ $sortOrder === 'asc' ? 'up' : 'down' }} text-[10px]"></i>
                            @else
                                <i class="fas fa-sort text-[10px] text-slate-500"></i>
                            @endif
                        </a>
                    </th>
                    <th>
                        <a href="{{ route('admin.balance.index', array_merge(request()->query(), ['sort_by' => 'created_at', 'sort_order' => $sortOrder === 'asc' && $sortBy === 'created_at' ? 'desc' : 'asc'])) }}" class="flex items-center gap-1 hover:text-slate-200">
                            Terdaftar
                            @if($sortBy === 'created_at')
                                <i class="fas fa-sort-{{ $sortOrder === 'asc' ? 'up' : 'down' }} text-[10px]"></i>
                            @else
                                <i class="fas fa-sort text-[10px] text-slate-500"></i>
                            @endif
                        </a>
                    </th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white text-sm font-bold shadow-sm">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div>
                                    <span class="font-semibold text-sm block">{{ $user->name }}</span>
                                    <span class="text-xs text-slate-400 lg:hidden block">{{ $user->email }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="text-slate-400 text-sm hidden lg:table-cell">{{ $user->email }}</td>
                        <td>
                            <span class="font-bold text-sm text-slate-800 dark:text-slate-200">
                                Rp{{ number_format($user->balance, 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="text-slate-400 text-sm">{{ $user->created_at->format('d M Y') }}</td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button type="button"
                                    @click="openAdjustModal('{{ $user->id }}', @js($user->name), @js('Rp' . number_format($user->balance, 0, ',', '.')))"
                                    class="btn btn-primary btn-sm flex items-center gap-1.5"
                                    title="Tambah atau kurangi saldo manual">
                                    <i class="fas fa-wallet text-xs"></i> <span class="hidden md:inline">Adjust Saldo</span>
                                </button>
                                <a href="{{ route('admin.balance.show', $user) }}" class="btn btn-secondary btn-sm flex items-center gap-1.5" title="Detail & Penyesuaian Saldo">
                                    <i class="fas fa-eye text-xs"></i> <span class="hidden md:inline">Detail & Kelola</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-8 text-slate-500">Tidak ada data pengguna</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">{{ $users->links() }}</div>

<div x-show="adjustModalOpen" x-cloak class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50" @click="adjustModalOpen = false"></div>

    <div x-show="adjustModalOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="relative w-full max-w-lg rounded-2xl border bg-white p-6 shadow-2xl dark:bg-slate-900"
        style="border-color: var(--outline-variant);">
        <div class="mb-5">
            <div class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Penyesuaian Manual</div>
            <h3 class="mt-2 text-xl font-bold text-slate-900 dark:text-slate-100" x-text="adjustUserName"></h3>
            <p class="mt-1 text-sm text-slate-500">Saldo saat ini: <span class="font-semibold" x-text="adjustUserBalance"></span></p>
        </div>

        <form method="POST" :action="adjustFormAction" class="space-y-4" x-data="{ actionType: 'add' }" @submit.prevent="$dispatch('open-balance-confirm', { form: $el, actionType, amount: $el.amount.value, note: $el.admin_note.value })">
            @csrf

            <div>
                <label class="form-label">Tindakan</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center justify-center gap-2 p-3 border rounded-xl cursor-pointer transition-all duration-200 select-none"
                        :class="actionType === 'add'
                            ? 'border-emerald-600 bg-emerald-600 text-white shadow-lg shadow-emerald-500/20'
                            : 'border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/50'">
                        <input type="radio" name="action_type" value="add" checked class="sr-only" x-model="actionType">
                        <i class="fas fa-plus-circle" :class="actionType === 'add' ? 'text-white' : 'text-emerald-600'"></i>
                        <span class="text-xs font-semibold">Tambah Saldo</span>
                    </label>
                    <label class="flex items-center justify-center gap-2 p-3 border rounded-xl cursor-pointer transition-all duration-200 select-none"
                        :class="actionType === 'subtract'
                            ? 'border-red-600 bg-red-600 text-white shadow-lg shadow-red-500/20'
                            : 'border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/50'">
                        <input type="radio" name="action_type" value="subtract" class="sr-only" x-model="actionType">
                        <i class="fas fa-minus-circle" :class="actionType === 'subtract' ? 'text-white' : 'text-red-600'"></i>
                        <span class="text-xs font-semibold">Kurangi Saldo</span>
                    </label>
                </div>
            </div>

            <div>
                <label for="modal_amount" class="form-label">Jumlah (Rupiah)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 font-semibold text-sm">Rp</span>
                    <input type="number" id="modal_amount" name="amount" required min="1" step="1" class="form-input pl-9" placeholder="Contoh: 100000">
                </div>
            </div>

            <div>
                <label for="modal_admin_note" class="form-label">Catatan Admin</label>
                <textarea id="modal_admin_note" name="admin_note" rows="3" required minlength="5" maxlength="255" class="form-input resize-none" placeholder="Jelaskan alasan penyesuaian saldo..."></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" class="btn btn-secondary" @click="adjustModalOpen = false">Batal</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check mr-2"></i> Simpan Adjustment
                </button>
            </div>
        </form>
    </div>
</div>

<div x-show="confirmModalOpen" x-cloak class="fixed inset-0 z-[10000] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-950/45 backdrop-blur-sm" @click="confirmModalOpen = false"></div>

    <div x-show="confirmModalOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-3 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-3 scale-95"
        class="relative w-full max-w-md overflow-hidden rounded-[28px] border shadow-[0_24px_70px_rgba(15,23,42,0.18)]"
        style="background: var(--surface-lowest); border-color: var(--outline-variant);"
        @click.stop>
        <div class="p-6">
            <div class="flex items-start gap-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl"
                    :style="confirmActionType === 'add'
                        ? 'background: rgba(16,185,129,0.12); color: #059669;'
                        : 'background: rgba(239,68,68,0.12); color: #dc2626;'">
                    <i class="fas" :class="confirmActionType === 'add' ? 'fa-plus' : 'fa-minus'"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="text-[11px] font-semibold uppercase tracking-[0.22em] text-slate-400">Konfirmasi</div>
                    <h3 class="mt-2 text-lg font-bold text-slate-900 dark:text-slate-100">
                        <span x-text="confirmActionType === 'add' ? 'Tambah saldo sekarang?' : 'Kurangi saldo sekarang?'"></span>
                    </h3>
                    <p class="mt-2 text-sm leading-6 text-slate-500">
                        Review singkat sebelum adjustment saldo disimpan ke riwayat transaksi.
                    </p>
                </div>
            </div>

            <div class="mt-5 rounded-2xl border p-4"
                style="background: var(--surface-container-low); border-color: var(--outline-variant);">
                <div class="flex items-center justify-between gap-4 text-sm">
                    <span class="text-slate-500">User</span>
                    <span class="font-semibold text-slate-800 dark:text-slate-100" x-text="adjustUserName"></span>
                </div>
                <div class="mt-3 flex items-center justify-between gap-4 text-sm">
                    <span class="text-slate-500">Tindakan</span>
                    <span class="font-semibold"
                        :class="confirmActionType === 'add' ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400'"
                        x-text="confirmActionType === 'add' ? 'Tambah saldo' : 'Kurangi saldo'"></span>
                </div>
                <div class="mt-3 flex items-center justify-between gap-4 text-sm">
                    <span class="text-slate-500">Nominal</span>
                    <span class="font-semibold text-slate-800 dark:text-slate-100" x-text="formatRupiah(confirmAmount)"></span>
                </div>
                <div class="mt-3 text-sm">
                    <div class="text-slate-500">Catatan</div>
                    <div class="mt-1 font-medium text-slate-700 dark:text-slate-200" x-text="confirmNote"></div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-3">
                <button type="button" class="btn btn-secondary" @click="confirmModalOpen = false">Kembali</button>
                <button type="button" class="btn btn-primary" @click="submitConfirmedForm()">
                    <i class="fas fa-check mr-2"></i> Ya, simpan
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .stat-card {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 1.25rem;
    }
    .stat-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 0.5rem;
        font-size: 1.25rem;
        margin-bottom: 0.75rem;
    }
    .stat-value {
        font-size: 1.25rem;
        font-weight: 700;
        line-height: 1.2;
        color: var(--on-surface);
    }
    .stat-label {
        font-size: 0.75rem;
        color: var(--on-surface-variant);
        margin-top: 0.25rem;
    }
    @media (min-width: 640px) {
        .stat-value {
            font-size: 1.5rem;
        }
    }
</style>
</div>
@endsection
