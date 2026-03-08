@extends('layouts.admin')
@section('title', 'Pembayaran')
@section('page-title', 'Pembayaran')
@section('page-subtitle', 'Kelola transaksi pembayaran')

@section('content')
{{-- Stats --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <div class="card stat-card">
        <div class="stat-icon" style="background: var(--accent-bg); color: var(--accent);"><i class="fas fa-receipt"></i></div>
        <div class="stat-value">{{ $stats['total'] }}</div>
        <div class="stat-label">Total Transaksi</div>
    </div>
    <div class="card stat-card">
        <div class="stat-icon" style="background: rgba(52,199,89,0.1); color: var(--success);"><i class="fas fa-check-circle"></i></div>
        <div class="stat-value">{{ $stats['paid'] }}</div>
        <div class="stat-label">Lunas</div>
    </div>
    <div class="card stat-card">
        <div class="stat-icon" style="background: rgba(255,149,0,0.1); color: var(--warning);"><i class="fas fa-clock"></i></div>
        <div class="stat-value">{{ $stats['pending'] }}</div>
        <div class="stat-label">Pending</div>
    </div>
    <div class="card stat-card">
        <div class="stat-icon" style="background: rgba(52,199,89,0.1); color: var(--success);"><i class="fas fa-wallet"></i></div>
        <div class="stat-value">Rp{{ number_format($stats['revenue'], 0, ',', '.') }}</div>
        <div class="stat-label">Revenue</div>
    </div>
</div>

{{-- Filters --}}
<div class="card p-4 mb-6">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div class="search-bar flex-1" style="min-width:200px;">
            <i class="fas fa-search"></i>
            <input type="text" name="search" class="form-input" value="{{ request('search') }}" placeholder="Cari transaksi...">
        </div>
        <div>
            <select name="status" class="form-input">
                <option value="">Semua Status</option>
                <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Lunas</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Gagal</option>
            </select>
        </div>
        <div>
            <select name="gateway" class="form-input">
                <option value="">Semua Gateway</option>
                <option value="xendit" {{ request('gateway') === 'xendit' ? 'selected' : '' }}>Xendit</option>
                <option value="tripay" {{ request('gateway') === 'tripay' ? 'selected' : '' }}>Tripay</option>
                <option value="manual" {{ request('gateway') === 'manual' ? 'selected' : '' }}>Manual</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter"></i></button>
    </form>
</div>

{{-- Table --}}
<div class="card overflow-hidden">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Client</th>
                    <th>Undangan</th>
                    <th>Paket</th>
                    <th>Amount</th>
                    <th>Gateway</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $p)
                <tr>
                    <td class="font-mono text-xs" style="color: var(--text-secondary);">#{{ $p->id }}</td>
                    <td>
                        <div class="flex items-center gap-2">
                            <div class="user-avatar" style="width:28px;height:28px;font-size:10px;">{{ substr($p->user->name ?? '?', 0, 1) }}</div>
                            <span class="text-sm font-semibold">{{ $p->user->name ?? '-' }}</span>
                        </div>
                    </td>
                    <td class="text-sm">{{ Str::limit($p->invitation->title ?? '-', 25) }}</td>
                    <td><span class="badge badge-info">{{ $p->package->name ?? '-' }}</span></td>
                    <td class="font-semibold">Rp{{ number_format($p->amount, 0, ',', '.') }}</td>
                    <td>
                        <span class="badge {{ $p->payment_gateway === 'xendit' ? 'badge-info' : ($p->payment_gateway === 'tripay' ? 'badge-success' : 'badge-default') }}">
                            {{ ucfirst($p->payment_gateway) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-{{ $p->payment_status === 'paid' ? 'success' : ($p->payment_status === 'pending' ? 'warning' : 'danger') }}">
                            {{ ucfirst($p->payment_status) }}
                        </span>
                    </td>
                    <td class="text-xs" style="color: var(--text-secondary);">{{ $p->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <div class="flex items-center gap-1">
                            <a href="{{ route('admin.payments.show', $p) }}" class="btn btn-secondary btn-icon btn-sm" title="Detail">
                                <i class="fas fa-eye text-xs"></i>
                            </a>
                            @if($p->isPending())
                            <form method="POST" action="{{ route('admin.payments.mark-paid', $p) }}" onsubmit="return confirm('Tandai sebagai lunas?')">
                                @csrf @method('PATCH')
                                <button class="btn btn-icon btn-sm" style="background:rgba(52,199,89,0.1);color:var(--success);" title="Mark as Paid">
                                    <i class="fas fa-check text-xs"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center py-8" style="color: var(--text-secondary);">Belum ada transaksi</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">{{ $payments->links() }}</div>
@endsection
