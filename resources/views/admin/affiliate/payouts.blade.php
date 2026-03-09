@extends('layouts.admin')
@section('title', 'Affiliate Payouts')
@section('page-title', 'Affiliate Payouts')
@section('page-subtitle', 'Approval payout request affiliate client')

@section('content')
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <div class="card stat-card">
        <div class="stat-value">{{ $stats['total'] }}</div>
        <div class="stat-label">Total Request</div>
    </div>
    <div class="card stat-card">
        <div class="stat-value" style="color: var(--warning);">{{ $stats['pending'] }}</div>
        <div class="stat-label">Pending</div>
    </div>
    <div class="card stat-card">
        <div class="stat-value" style="color: var(--info);">{{ $stats['approved'] }}</div>
        <div class="stat-label">Approved</div>
    </div>
    <div class="card stat-card">
        <div class="stat-value" style="color: var(--success);">Rp{{ number_format($stats['amount_paid'], 0, ',', '.') }}</div>
        <div class="stat-label">Total Paid</div>
    </div>
</div>

<div class="card p-4 mb-6">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div class="search-bar flex-1" style="min-width:220px;">
            <i class="fas fa-search"></i>
            <input type="text" name="search" class="form-input" value="{{ request('search') }}" placeholder="Cari nama client / rekening...">
        </div>
        <div>
            <select name="status" class="form-input">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter"></i></button>
    </form>
</div>

<div class="card overflow-hidden">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Client</th>
                    <th>Metode</th>
                    <th>Akun</th>
                    <th>Nominal</th>
                    <th>Komisi</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @forelse($payouts as $p)
                <tr>
                    <td class="font-mono text-xs">{{ $p->id }}</td>
                    <td>{{ $p->user->name ?? '-' }}</td>
                    <td class="text-xs uppercase">{{ str_replace('_', ' ', $p->method) }}</td>
                    <td>
                        <div class="text-sm font-semibold">{{ $p->account_name }}</div>
                        <div class="text-xs" style="color: var(--text-secondary);">{{ $p->account_number }}</div>
                    </td>
                    <td class="font-semibold">Rp{{ number_format($p->amount, 0, ',', '.') }}</td>
                    <td class="text-xs">{{ $p->commissions->count() }} item</td>
                    <td>
                        <span class="badge badge-{{ $p->status === 'paid' ? 'success' : ($p->status === 'approved' ? 'info' : ($p->status === 'rejected' ? 'danger' : 'warning')) }}">
                            {{ ucfirst($p->status) }}
                        </span>
                    </td>
                    <td class="text-xs">{{ optional($p->requested_at)->format('d/m/Y H:i') }}</td>
                    <td>
                        <div class="flex gap-1">
                            @if($p->status === 'pending')
                            <form method="POST" action="{{ route('admin.affiliate.payouts.approve', $p) }}">
                                @csrf @method('PATCH')
                                <button class="btn btn-secondary btn-sm" title="Approve"><i class="fas fa-check"></i></button>
                            </form>
                            @endif
                            @if(in_array($p->status, ['pending', 'approved']))
                            <form method="POST" action="{{ route('admin.affiliate.payouts.mark-paid', $p) }}" onsubmit="return confirm('Tandai payout ini sudah dibayar?')">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm" style="background: rgba(52,199,89,0.12); color: var(--success);" title="Mark Paid">
                                    <i class="fas fa-money-check-dollar"></i>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.affiliate.payouts.reject', $p) }}" onsubmit="return confirm('Tolak payout ini? Komisi akan dilepas agar bisa diajukan ulang.')">
                                @csrf @method('PATCH')
                                <button class="btn btn-danger btn-sm" title="Reject"><i class="fas fa-xmark"></i></button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @if($p->notes || $p->admin_notes)
                    <tr>
                        <td colspan="9" style="background: var(--bg-tertiary);">
                            <div class="text-xs" style="color: var(--text-secondary);">
                                @if($p->notes)<strong>Catatan client:</strong> {{ $p->notes }} @endif
                                @if($p->admin_notes)<span class="ml-3"><strong>Catatan admin:</strong> {{ $p->admin_notes }}</span>@endif
                            </div>
                        </td>
                    </tr>
                @endif
            @empty
                <tr><td colspan="9" class="text-center py-8" style="color: var(--text-secondary);">Belum ada payout request affiliate.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">{{ $payouts->links() }}</div>
@endsection
