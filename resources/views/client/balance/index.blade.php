@extends('layouts.client')

@section('title', 'Dompet & Saldo')
@section('page-title', 'Dompet & Saldo')
@section('page-subtitle', 'Kelola saldo Anda untuk pembayaran instan')

@section('content')
<style>
    .balance-card {
        background: linear-gradient(135deg, var(--accent) 0%, var(--accent-hover) 100%);
        color: var(--on-primary);
        border-radius: 20px;
        padding: 32px;
        box-shadow: 0 10px 30px rgba(219, 39, 119, 0.2);
        position: relative;
        overflow: hidden;
    }
    .balance-card::before {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 180px;
        height: 180px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
    }
    .balance-amount {
        font-size: 36px;
        font-weight: 800;
        letter-spacing: -0.02em;
    }
    .balance-label {
        font-size: 13px;
        font-weight: 500;
        opacity: 0.85;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .transaction-card {
        background-color: var(--surface-lowest);
        border-radius: 16px;
        border: 1px solid var(--outline-variant);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
    }
    .badge-topup {
        background: rgba(21, 128, 61, 0.10);
        color: var(--success-clr, #15803d);
    }
    .badge-purchase {
        background: rgba(186, 26, 26, 0.10);
        color: var(--danger, #ba1a1a);
    }
    .badge-refund {
        background: rgba(161, 98, 7, 0.10);
        color: var(--warning-clr, #a16207);
    }
    .badge-adjustment {
        background: var(--accent-bg);
        color: var(--accent);
    }
</style>

<div class="space-y-6">
    <!-- Balance Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 balance-card flex flex-col justify-between min-h-[180px]">
            <div>
                <div class="balance-label flex items-center gap-2">
                    <span class="material-symbols-outlined" style="font-size: 18px;">account_balance_wallet</span>
                    Saldo Tersedia
                </div>
                <div class="balance-amount mt-3">
                    Rp {{ number_format($user->balance, 0, ',', '.') }}
                </div>
            </div>
            
            <div class="flex items-center gap-3 mt-6">
                <a href="{{ route('client.balance.topup') }}" class="btn bg-white text-[var(--accent)] hover:bg-pink-50 transition-all font-bold px-6 py-2.5 rounded-xl flex items-center gap-2 shadow-sm">
                    <span class="material-symbols-outlined" style="font-size: 18px;">add_circle</span>
                    Top Up Saldo
                </a>
                <a href="{{ route('client.packages.select') }}" class="btn btn-secondary border-white/20 text-white hover:bg-white/10 transition-all font-semibold px-5 py-2.5 rounded-xl flex items-center gap-2">
                    <span class="material-symbols-outlined" style="font-size: 18px;">shopping_bag</span>
                    Beli Paket
                </a>
            </div>
        </div>

        <div class="card flex flex-col justify-between stat-card">
            <div>
                <div class="stat-icon bg-pink-100 dark:bg-pink-900/30 text-[var(--accent)]">
                    <span class="material-symbols-outlined">payments</span>
                </div>
                <div class="stat-value text-xl mt-2">Instan & Aman</div>
                <div class="stat-label text-sm">Gunakan saldo untuk memotong tagihan secara instan tanpa perlu transfer manual per transaksi.</div>
            </div>
        </div>
    </div>

    <!-- Transaction History -->
    <div class="transaction-card overflow-hidden">
        <div class="px-6 py-5 border-b border-[var(--outline-variant)] flex items-center justify-between">
            <h3 class="font-bold text-base text-[var(--on-surface)] flex items-center gap-2">
                <span class="material-symbols-outlined" style="font-size: 20px;">history</span>
                Riwayat Transaksi Saldo
            </h3>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Tipe</th>
                        <th>Jumlah</th>
                        <th>Sebelumnya</th>
                        <th>Sesudahnya</th>
                        <th>Deskripsi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $tx)
                        <tr>
                            <td class="whitespace-nowrap text-gray-500 dark:text-gray-400">
                                {{ $tx->created_at->format('d M Y, H:i') }}
                            </td>
                            <td>
                                @if($tx->type === 'topup')
                                    <span class="badge badge-topup">Top Up</span>
                                @elseif($tx->type === 'purchase')
                                    <span class="badge badge-purchase">Pembelian</span>
                                @elseif($tx->type === 'refund')
                                    <span class="badge badge-refund">Refund</span>
                                @else
                                    <span class="badge badge-adjustment">Penyesuaian</span>
                                @endif
                            </td>
                            <td class="font-semibold whitespace-nowrap {{ $tx->amount >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                {{ $tx->amount >= 0 ? '+' : '' }}Rp {{ number_format($tx->amount, 0, ',', '.') }}
                            </td>
                            <td class="text-gray-500 dark:text-gray-400">
                                Rp {{ number_format($tx->balance_before, 0, ',', '.') }}
                            </td>
                            <td class="font-medium text-gray-700 dark:text-gray-300">
                                Rp {{ number_format($tx->balance_after, 0, ',', '.') }}
                            </td>
                            <td>
                                <div class="max-w-[300px] truncate text-gray-700 dark:text-gray-300" title="{{ $tx->description }}">
                                    {{ $tx->description }}
                                </div>
                                @if($tx->admin_note)
                                    <span class="block text-[11px] text-gray-400 italic">Catatan: {{ $tx->admin_note }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-gray-500">
                                <span class="material-symbols-outlined text-4xl mb-2 block text-gray-300">account_balance_wallet</span>
                                Belum ada riwayat transaksi saldo.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($transactions->hasPages())
            <div class="px-6 py-4 border-t border-[var(--outline-variant)]">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
