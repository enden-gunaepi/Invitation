@extends('layouts.admin')
@section('title', 'Affiliate')
@section('page-title', 'Affiliate')
@section('page-subtitle', 'Kelola komisi referral/affiliate')

@section('content')
<div class="mb-4 flex justify-end">
    <a href="{{ route('admin.affiliate.payouts') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-wallet"></i> Kelola Payout
    </a>
</div>

<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <div class="card stat-card">
        <div class="stat-value">{{ $stats['total'] }}</div>
        <div class="stat-label">Total Komisi</div>
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
        <div class="stat-label">Sudah Dibayar</div>
    </div>
</div>

<div class="card p-4 mb-6">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div class="search-bar flex-1" style="min-width:200px;">
            <i class="fas fa-search"></i>
            <input type="text" name="search" class="form-input" value="{{ request('search') }}" placeholder="Cari referrer/referred/invoice...">
        </div>
        <div>
            <select name="status" class="form-input">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
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
                    <th>Referrer</th>
                    <th>Referred User</th>
                    <th>Invoice</th>
                    <th>Komisi</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @forelse($commissions as $c)
                <tr>
                    <td class="font-mono text-xs">{{ $c->id }}</td>
                    <td>{{ $c->referrer->name ?? '-' }}</td>
                    <td>{{ $c->referred->name ?? '-' }}</td>
                    <td class="text-xs">{{ $c->payment->invoice_number ?? ('PAY-' . $c->payment_id) }}</td>
                    <td class="font-semibold">Rp{{ number_format($c->commission_amount, 0, ',', '.') }}</td>
                    <td>
                        <span class="badge badge-{{ $c->status === 'paid' ? 'success' : ($c->status === 'approved' ? 'info' : 'warning') }}">
                            {{ ucfirst($c->status) }}
                        </span>
                        @if($c->risk_flag)
                            <div class="text-[10px] mt-1 px-2 py-1 rounded-full inline-block" style="background: rgba(239,68,68,.12); color: #ef4444;">
                                Risk: {{ $c->risk_reason ?? 'flagged' }}
                            </div>
                        @endif
                    </td>
                    <td class="text-xs">{{ $c->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <div class="flex gap-1">
                            @if($c->status === 'pending')
                            <form method="POST" action="{{ route('admin.affiliate.approve', $c) }}">
                                @csrf @method('PATCH')
                                <button class="btn btn-secondary btn-sm" title="Approve"><i class="fas fa-check"></i></button>
                            </form>
                            @endif
                            @if($c->status !== 'paid')
                            <form method="POST" action="{{ route('admin.affiliate.mark-paid', $c) }}" onsubmit="return confirm('Tandai komisi ini sudah dibayar?')">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm" style="background: rgba(52,199,89,0.12); color: var(--success);" title="Mark Paid">
                                    <i class="fas fa-money-check-dollar"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center py-8" style="color: var(--text-secondary);">Belum ada data affiliate.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">{{ $commissions->links() }}</div>
@endsection
