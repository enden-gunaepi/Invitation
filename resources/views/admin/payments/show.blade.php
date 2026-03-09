@extends('layouts.admin')
@section('title', 'Detail Pembayaran')
@section('page-title', 'Detail Pembayaran')
@section('page-subtitle', '#' . $payment->id . ' — ' . ucfirst($payment->payment_status))

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        {{-- Payment Info --}}
        <div class="card p-6">
            <h3 class="font-bold text-base mb-4">Informasi Pembayaran</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div><span style="color: var(--text-secondary);">Transaction ID</span><p class="font-mono font-semibold mt-1">{{ $payment->transaction_id }}</p></div>
                <div><span style="color: var(--text-secondary);">Invoice</span><p class="font-mono font-semibold mt-1">{{ $payment->invoice_number ?? '-' }}</p></div>
                <div><span style="color: var(--text-secondary);">Gateway</span><p class="font-semibold mt-1">{{ ucfirst($payment->payment_gateway) }}</p></div>
                <div><span style="color: var(--text-secondary);">Metode/Channel</span><p class="font-semibold mt-1">{{ strtoupper($payment->payment_method ?? '-') }} / {{ $payment->payment_channel ?? '-' }}</p></div>
                <div><span style="color: var(--text-secondary);">Amount</span><p class="font-bold text-lg mt-1" style="color: var(--accent);">Rp{{ number_format($payment->amount, 0, ',', '.') }}</p></div>
                <div><span style="color: var(--text-secondary);">Status</span>
                    <p class="mt-1"><span class="badge badge-{{ $payment->payment_status === 'paid' ? 'success' : ($payment->payment_status === 'pending' ? 'warning' : 'danger') }}">{{ ucfirst($payment->payment_status) }}</span></p>
                </div>
                <div><span style="color: var(--text-secondary);">Dibuat</span><p class="font-semibold mt-1">{{ $payment->created_at->format('d M Y, H:i') }}</p></div>
                @if($payment->paid_at)
                <div><span style="color: var(--text-secondary);">Dibayar</span><p class="font-semibold mt-1" style="color: var(--success);">{{ $payment->paid_at->format('d M Y, H:i') }}</p></div>
                @endif
                @if($payment->expired_at)
                <div><span style="color: var(--text-secondary);">Expired</span><p class="font-semibold mt-1" style="color: {{ $payment->expired_at->isPast() ? 'var(--danger)' : 'var(--text)' }};">{{ $payment->expired_at->format('d M Y, H:i') }}</p></div>
                @endif
            </div>

            <div class="mt-4 p-3 rounded-lg text-sm" style="background: var(--bg-tertiary);">
                <div class="flex items-center justify-between mb-1">
                    <span style="color: var(--text-secondary);">Subtotal</span>
                    <span>Rp{{ number_format($payment->base_amount ?? $payment->amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between mb-1">
                    <span style="color: var(--text-secondary);">Diskon</span>
                    <span>-Rp{{ number_format($payment->discount_amount ?? 0, 0, ',', '.') }}</span>
                </div>
                @if($payment->coupon_code)
                <div class="flex items-center justify-between mb-1">
                    <span style="color: var(--text-secondary);">Coupon ({{ $payment->coupon_code }})</span>
                    <span>-Rp{{ number_format($payment->coupon_discount_amount ?? 0, 0, ',', '.') }}</span>
                </div>
                @endif
                <div class="flex items-center justify-between mb-1">
                    <span style="color: var(--text-secondary);">PPN</span>
                    <span>Rp{{ number_format($payment->tax_amount ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between pt-2" style="border-top:1px solid var(--border);">
                    <strong>Total Tagihan</strong>
                    <strong style="color: var(--accent);">Rp{{ number_format($payment->amount, 0, ',', '.') }}</strong>
                </div>
                @if(($payment->affiliate_commission_amount ?? 0) > 0)
                <div class="flex items-center justify-between mt-2">
                    <span style="color: var(--text-secondary);">Affiliate Komisi</span>
                    <strong>Rp{{ number_format($payment->affiliate_commission_amount, 0, ',', '.') }}</strong>
                </div>
                @endif
            </div>

            @if($payment->gateway_reference)
            <div class="mt-4 p-3 rounded-lg text-xs" style="background: var(--bg-tertiary);">
                <strong>Gateway Reference:</strong> <code style="color: var(--accent);">{{ $payment->gateway_reference }}</code>
            </div>
            @endif

            @if($payment->isPending())
            <div class="mt-4 flex gap-2">
                <form method="POST" action="{{ route('admin.payments.mark-paid', $payment) }}" onsubmit="return confirm('Tandai sebagai lunas manual?')">
                    @csrf @method('PATCH')
                    <button class="btn btn-primary btn-sm"><i class="fas fa-check mr-1"></i> Mark as Paid</button>
                </form>
            </div>
            @endif
        </div>

        {{-- Gateway Response --}}
        @if($payment->gateway_response)
        <div class="card p-6">
            <h3 class="font-bold text-base mb-4">Gateway Response</h3>
            <pre class="p-4 rounded-lg text-xs overflow-auto" style="background: var(--bg-tertiary); color: var(--text-secondary); max-height: 300px; font-family: monospace;">{{ json_encode($payment->gateway_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
        @endif
    </div>

    {{-- Sidebar --}}
    <div class="space-y-6">
        <div class="card p-6">
            <h3 class="font-bold text-base mb-4">Client</h3>
            <div class="flex items-center gap-3">
                <div class="user-avatar">{{ substr($payment->user->name ?? '?', 0, 1) }}</div>
                <div>
                    <p class="font-semibold text-sm">{{ $payment->user->name ?? '-' }}</p>
                    <p class="text-xs" style="color: var(--text-secondary);">{{ $payment->user->email ?? '-' }}</p>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <h3 class="font-bold text-base mb-4">Undangan</h3>
            <p class="font-semibold text-sm">{{ $payment->invitation->title ?? '-' }}</p>
            <p class="text-xs mt-1" style="color: var(--text-secondary);">{{ ucfirst($payment->invitation->event_type ?? '-') }}</p>
            @if($payment->invitation)
            <a href="{{ route('admin.invitations.show', $payment->invitation) }}" class="btn btn-secondary btn-sm mt-3 w-full text-center">
                <i class="fas fa-eye mr-1"></i> Lihat Undangan
            </a>
            @endif
        </div>

        <div class="card p-6">
            <h3 class="font-bold text-base mb-4">Paket</h3>
            <p class="font-semibold text-sm">{{ $payment->package->name ?? '-' }}</p>
            <p class="text-xs mt-1" style="color: var(--accent);">Rp{{ number_format($payment->package->price ?? 0, 0, ',', '.') }}</p>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="{{ route('admin.payments.index') }}" class="text-sm font-semibold" style="color: var(--accent);">
        <i class="fas fa-arrow-left mr-1"></i> Kembali
    </a>
</div>
@endsection
