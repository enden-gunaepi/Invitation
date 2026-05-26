@extends('layouts.admin')
@section('title', 'Log Mutasi Saldo Global')
@section('page-title', 'Log Mutasi Saldo Global')
@section('page-subtitle', 'Semua riwayat transaksi saldo pengguna')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.balance.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Manajemen Saldo
    </a>
</div>

{{-- Filters Card --}}
<div class="card p-4 mb-6">
    <form method="GET" class="flex flex-col md:flex-row items-end gap-3">
        <div class="flex-1 w-full">
            <label for="search" class="form-label">Cari Pengguna</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" id="search" name="search" class="form-input pl-9 w-full" value="{{ $search }}" placeholder="Nama atau email user...">
            </div>
        </div>
        
        <div class="w-full md:w-48">
            <label for="type" class="form-label">Tipe Transaksi</label>
            <select id="type" name="type" class="form-input w-full">
                <option value="">Semua Tipe</option>
                <option value="topup" {{ $type === 'topup' ? 'selected' : '' }}>Top-up</option>
                <option value="purchase" {{ $type === 'purchase' ? 'selected' : '' }}>Pembelian</option>
                <option value="refund" {{ $type === 'refund' ? 'selected' : '' }}>Refund</option>
                <option value="adjustment" {{ $type === 'adjustment' ? 'selected' : '' }}>Adjustment</option>
            </select>
        </div>

        <div class="flex gap-2 w-full md:w-auto">
            <button type="submit" class="btn btn-primary flex-1 md:flex-initial justify-center">
                <i class="fas fa-filter mr-1"></i> Filter
            </button>
            @if($search || $type)
                <a href="{{ route('admin.balance.transactions') }}" class="btn btn-secondary flex-1 md:flex-initial justify-center">Reset</a>
            @endif
        </div>
    </form>
</div>

{{-- Table Card --}}
<div class="card overflow-hidden">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Waktu / ID</th>
                    <th>User</th>
                    <th>Tipe</th>
                    <th>Jumlah</th>
                    <th>Sebelum / Sesudah</th>
                    <th>Deskripsi / Info Tambahan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $t)
                    <tr>
                        <td>
                            <div class="text-xs font-semibold text-slate-800 dark:text-slate-200">
                                {{ $t->created_at->format('d/m/Y H:i') }}
                            </div>
                            <div class="text-[10px] text-slate-400 font-mono">
                                #{{ $t->id }}
                            </div>
                        </td>
                        <td>
                            @if($t->user)
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-[10px] font-bold text-slate-600 dark:text-slate-300">
                                        {{ substr($t->user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.balance.show', $t->user) }}" class="text-xs font-semibold text-pink-500 hover:text-pink-600 dark:hover:text-pink-400 block">
                                            {{ $t->user->name }}
                                        </a>
                                        <span class="text-[10px] text-slate-400">{{ $t->user->email }}</span>
                                    </div>
                                </div>
                            @else
                                <span class="text-xs text-slate-400">User telah dihapus</span>
                            @endif
                        </td>
                        <td>
                            @if($t->type === \App\Models\BalanceTransaction::TYPE_TOPUP)
                                <span class="badge bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">Top-up</span>
                            @elseif($t->type === \App\Models\BalanceTransaction::TYPE_PURCHASE)
                                <span class="badge bg-red-500/10 text-red-600 dark:text-red-400">Pembelian</span>
                            @elseif($t->type === \App\Models\BalanceTransaction::TYPE_REFUND)
                                <span class="badge bg-blue-500/10 text-blue-600 dark:text-blue-400">Refund</span>
                            @else
                                <span class="badge bg-amber-500/10 text-amber-600 dark:text-amber-400">Adjustment</span>
                            @endif
                        </td>
                        <td>
                            <span class="font-bold text-sm {{ $t->amount > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                {{ $t->amount > 0 ? '+' : '' }}Rp{{ number_format($t->amount, 0, ',', '.') }}
                            </span>
                        </td>
                        <td>
                            <div class="text-[11px] text-slate-400">
                                Sebelum: Rp{{ number_format($t->balance_before, 0, ',', '.') }}
                            </div>
                            <div class="text-[11px] text-slate-600 dark:text-slate-300 font-medium">
                                Sesudah: Rp{{ number_format($t->balance_after, 0, ',', '.') }}
                            </div>
                        </td>
                        <td>
                            <div class="text-sm font-medium text-slate-800 dark:text-slate-200">{{ $t->description }}</div>
                            @if($t->admin_note)
                                <div class="text-xs text-amber-600 dark:text-amber-400 mt-1 italic flex items-start gap-1">
                                    <i class="fas fa-comment-dots mt-0.5 shrink-0"></i>
                                    <span>Catatan: {{ $t->admin_note }}</span>
                                </div>
                            @endif
                            @if($t->performedBy)
                                <div class="text-[10px] text-slate-400 mt-1">
                                    Diproses oleh: {{ $t->performedBy->name }}
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-8 text-slate-500">Tidak ada riwayat mutasi saldo</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">{{ $transactions->links() }}</div>
@endsection
