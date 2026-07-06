@extends('layouts.admin')
@section('title', 'Transfer Manual')
@section('page-title', 'Transfer Manual')
@section('page-subtitle', 'Konfirmasi pembayaran transfer bank manual dari client')

@section('content')
<div class="space-y-6">

    {{-- Stats & Filter --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold"
                  style="background: rgba(255,149,0,0.12); color: #f97316;">
                <i class="fas fa-clock"></i>
                {{ $pendingCount }} menunggu konfirmasi
            </span>
        </div>
        <form method="GET" class="flex items-center gap-2">
            <select name="status" class="form-input text-sm py-2 pr-8" onchange="this.form.submit()">
                <option value="pending_verification" {{ request('status','pending_verification') === 'pending_verification' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
                <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Dikonfirmasi</option>
                <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Ditolak</option>
                <option value="" {{ request('status') === '' ? 'selected' : '' }}>Semua Status</option>
            </select>
        </form>
    </div>

    {{-- Table --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b" style="border-color: var(--border-color); background: var(--surface-container-low);">
                        <th class="px-4 py-3 text-left font-semibold text-xs uppercase tracking-wider" style="color: var(--text-secondary);">Client</th>
                        <th class="px-4 py-3 text-left font-semibold text-xs uppercase tracking-wider" style="color: var(--text-secondary);">Invoice</th>
                        <th class="px-4 py-3 text-right font-semibold text-xs uppercase tracking-wider" style="color: var(--text-secondary);">Nominal</th>
                        <th class="px-4 py-3 text-center font-semibold text-xs uppercase tracking-wider" style="color: var(--text-secondary);">Status</th>
                        <th class="px-4 py-3 text-left font-semibold text-xs uppercase tracking-wider" style="color: var(--text-secondary);">Waktu</th>
                        <th class="px-4 py-3 text-center font-semibold text-xs uppercase tracking-wider" style="color: var(--text-secondary);">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="divide-color: var(--border-color);">
                    @forelse($payments as $payment)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-800/30 transition-colors">
                        <td class="px-4 py-3">
                            <div class="font-semibold text-sm">{{ $payment->user?->name ?? '-' }}</div>
                            <div class="text-xs mt-0.5" style="color: var(--text-secondary);">{{ $payment->user?->email ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <code class="text-xs px-2 py-1 rounded" style="background: var(--surface-container-low);">{{ $payment->invoice_number }}</code>
                            @if($payment->invitation)
                            <div class="text-xs mt-1" style="color: var(--text-secondary);">{{ Str::limit($payment->invitation->title, 30) }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <span class="font-bold" style="color: var(--accent);">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($payment->payment_status === 'pending_verification')
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-semibold" style="background: rgba(255,149,0,0.12); color: #f97316;">
                                    <i class="fas fa-hourglass-half" style="font-size: 10px;"></i> Menunggu
                                </span>
                            @elseif($payment->payment_status === 'paid')
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-semibold" style="background: rgba(52,199,89,0.12); color: var(--success);">
                                    <i class="fas fa-check" style="font-size: 10px;"></i> Dikonfirmasi
                                </span>
                            @elseif($payment->payment_status === 'failed')
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-semibold" style="background: rgba(255,59,48,0.12); color: var(--danger);">
                                    <i class="fas fa-times" style="font-size: 10px;"></i> Ditolak
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs" style="color: var(--text-secondary);">
                            {{ $payment->created_at->format('d M Y, H:i') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('admin.manual-transfer.show', $payment) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all hover:scale-105"
                               style="background: var(--accent-bg); color: var(--accent);">
                                <i class="fas fa-eye" style="font-size: 11px;"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-16 text-center" style="color: var(--text-secondary);">
                            <i class="fas fa-inbox text-3xl mb-3 block opacity-40"></i>
                            <p class="text-sm">Tidak ada pembayaran transfer manual</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if($payments->hasPages())
    <div class="flex justify-center">
        {{ $payments->links() }}
    </div>
    @endif

</div>
@endsection
