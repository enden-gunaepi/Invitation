@extends('layouts.client')
@section('title', 'Affiliate Dashboard')
@section('page-title', 'Affiliate Dashboard')
@section('page-subtitle', 'Komisi referral, histori, dan payout request')

@section('content')
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="card stat-card">
        <div class="stat-value" style="color: var(--warning);">Rp{{ number_format($stats['pending'], 0, ',', '.') }}</div>
        <div class="stat-label">Pending</div>
    </div>
    <div class="card stat-card">
        <div class="stat-value" style="color: var(--info);">Rp{{ number_format($stats['approved'], 0, ',', '.') }}</div>
        <div class="stat-label">Approved</div>
    </div>
    <div class="card stat-card">
        <div class="stat-value" style="color: var(--success);">Rp{{ number_format($stats['paid'], 0, ',', '.') }}</div>
        <div class="stat-label">Paid</div>
    </div>
    <div class="card stat-card">
        <div class="stat-value" style="color: var(--accent);">Rp{{ number_format($stats['available'], 0, ',', '.') }}</div>
        <div class="stat-label">Available Payout</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="card overflow-hidden">
            <div class="px-6 py-4 border-b" style="border-color: var(--border);">
                <h3 class="font-bold text-base">Histori Komisi</h3>
            </div>
            <div class="p-4">
                @forelse($commissions as $c)
                    <div class="p-3 rounded-lg mb-2" style="background: var(--bg-tertiary);">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold">Dari user: {{ $c->referred->name ?? '-' }}</p>
                                <p class="text-xs" style="color: var(--text-secondary);">
                                    Invoice: {{ $c->payment->invoice_number ?? ('PAY-' . $c->payment_id) }} | {{ $c->created_at->format('d M Y H:i') }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold">Rp{{ number_format($c->commission_amount, 0, ',', '.') }}</p>
                                <span class="badge badge-{{ $c->status === 'paid' ? 'success' : ($c->status === 'approved' ? 'info' : 'warning') }}">{{ ucfirst($c->status) }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm py-6 text-center" style="color: var(--text-secondary);">Belum ada komisi affiliate.</p>
                @endforelse
            </div>
        </div>
        <div>{{ $commissions->links() }}</div>

        <div class="card overflow-hidden">
            <div class="px-6 py-4 border-b" style="border-color: var(--border);">
                <h3 class="font-bold text-base">Histori Payout</h3>
            </div>
            <div class="p-4">
                @forelse($payouts as $p)
                    <div class="p-3 rounded-lg mb-2" style="background: var(--bg-tertiary);">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold">{{ strtoupper($p->method) }} - {{ $p->account_name }}</p>
                                <p class="text-xs" style="color: var(--text-secondary);">{{ $p->account_number }} | {{ $p->requested_at?->format('d M Y H:i') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold">Rp{{ number_format($p->amount, 0, ',', '.') }}</p>
                                <span class="badge badge-{{ $p->status === 'paid' ? 'success' : ($p->status === 'approved' ? 'info' : ($p->status === 'rejected' ? 'danger' : 'warning')) }}">{{ ucfirst($p->status) }}</span>
                            </div>
                        </div>
                        @if($p->admin_notes)
                            <p class="text-xs mt-2" style="color: var(--text-secondary);">Catatan admin: {{ $p->admin_notes }}</p>
                        @endif
                    </div>
                @empty
                    <p class="text-sm py-6 text-center" style="color: var(--text-secondary);">Belum ada payout request.</p>
                @endforelse
            </div>
        </div>
        <div>{{ $payouts->links('pagination::tailwind') }}</div>
    </div>

    <div class="space-y-6">
        <div class="card p-6">
            <h3 class="font-bold text-base mb-3">Request Payout</h3>
            <p class="text-xs mb-4" style="color: var(--text-secondary);">Saldo tersedia: <strong>Rp{{ number_format($stats['available'], 0, ',', '.') }}</strong></p>
            <form method="POST" action="{{ route('client.affiliate.payout-request') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="form-label">Jumlah Payout</label>
                    <input type="number" name="amount" min="10000" max="{{ (int) floor($stats['available']) }}" step="1000" value="{{ old('amount', (int) floor($stats['available'])) }}" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Metode</label>
                    <select name="method" class="form-input" required>
                        <option value="bank_transfer" {{ old('method') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="ewallet" {{ old('method') === 'ewallet' ? 'selected' : '' }}>E-Wallet</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Nama Akun</label>
                    <input type="text" name="account_name" value="{{ old('account_name') }}" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">No Rekening / No Wallet</label>
                    <input type="text" name="account_number" value="{{ old('account_number') }}" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Catatan (opsional)</label>
                    <textarea name="notes" class="form-input" rows="2">{{ old('notes') }}</textarea>
                </div>
                <button class="btn btn-primary w-full text-sm" {{ $stats['available'] < 10000 ? 'disabled' : '' }}>
                    <i class="fas fa-paper-plane mr-2"></i> Kirim Request
                </button>
            </form>
            @if($stats['available'] < 10000)
                <p class="text-xs mt-3" style="color: var(--warning);">Minimum payout Rp10.000. Komisi available belum mencukupi.</p>
            @endif
        </div>
    </div>
</div>
@endsection
