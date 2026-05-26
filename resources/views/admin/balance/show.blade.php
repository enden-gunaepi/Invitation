@extends('layouts.admin')
@section('title', 'Detail Saldo & Riwayat: ' . $user->name)
@section('page-title', 'Detail Saldo & Riwayat')
@section('page-subtitle', 'Manajemen detail saldo untuk ' . $user->name)

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.balance.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Saldo
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- User Info & Adjustment Form --}}
    <div class="space-y-6">
        {{-- User Info Card --}}
        <div class="card">
            <div class="flex flex-col items-center text-center p-4">
                <div class="w-20 h-20 rounded-full overflow-hidden shadow-md mb-4 border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                    @if($user->avatar)
                        <img src="{{ Storage::url($user->avatar) }}" alt="Avatar {{ $user->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-pink-500 to-rose-600 flex items-center justify-center text-white text-3xl font-bold">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                    @endif
                </div>
                <h3 class="font-bold text-lg text-slate-800 dark:text-slate-100">{{ $user->name }}</h3>
                <p class="text-sm text-slate-400 mb-4">{{ $user->email }}</p>
                
                <div class="w-full border-t border-slate-100 dark:border-slate-800 my-4"></div>
                
                <div class="text-xs uppercase font-semibold text-slate-400 mb-1">Saldo Saat Ini</div>
                <div class="text-3xl font-extrabold text-slate-800 dark:text-slate-100">
                    Rp{{ number_format($user->balance, 0, ',', '.') }}
                </div>
            </div>
        </div>

        {{-- Adjustment Form Card --}}
        <div class="card">
            <h4 class="font-bold text-sm mb-4 flex items-center gap-2 text-slate-800 dark:text-slate-200">
                <i class="fas fa-sliders text-pink-500"></i> Penyesuaian Saldo Manual
            </h4>

            <form method="POST" action="{{ route('admin.balance.adjust', $user) }}" class="space-y-4" x-data="{ actionType: 'add' }">
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
                    <label for="amount" class="form-label">Jumlah (Rupiah)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 font-semibold text-sm">
                            Rp
                        </span>
                        <input type="number" id="amount" name="amount" required min="1" step="1" placeholder="Contoh: 100000"
                            class="form-input pl-9">
                    </div>
                </div>

                <div>
                    <label for="admin_note" class="form-label">Catatan Admin (Min. 5 karakter)</label>
                    <textarea id="admin_note" name="admin_note" rows="3" required placeholder="Jelaskan alasan penyesuaian saldo..."
                        class="form-input resize-none" minlength="5"></textarea>
                </div>

                <button type="submit" class="btn btn-primary w-full justify-center" onclick="return confirm('Apakah Anda yakin ingin menyesuaikan saldo user ini?')">
                    <i class="fas fa-check mr-2"></i> Proses Penyesuaian
                </button>
            </form>
        </div>
    </div>

    {{-- User Transactions History --}}
    <div class="lg:col-span-2">
        <div class="card overflow-hidden">
            <h4 class="font-bold text-sm mb-4 flex items-center gap-2 text-slate-800 dark:text-slate-200">
                <i class="fas fa-history text-pink-500"></i> Riwayat Transaksi Saldo
            </h4>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Waktu &amp; ID</th>
                            <th>Tipe</th>
                            <th>Jumlah</th>
                            <th>Sebelum / Sesudah</th>
                            <th>Deskripsi / Catatan Admin</th>
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
                                <td colspan="5" class="text-center py-8 text-slate-500">Belum ada riwayat transaksi</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4 p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
