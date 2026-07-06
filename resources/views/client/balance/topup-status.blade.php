@extends('layouts.client')

@section('title', 'Status Top Up')
@section('page-title', 'Status Top Up')
@section('page-subtitle', 'Detail transaksi isi ulang saldo Anda')

@section('content')
<div class="max-w-lg mx-auto">
    @if($devMode ?? false)
    <div class="card p-4 mb-4" style="border-color: var(--warning); background: rgba(255,149,0,0.06);">
        <p class="text-sm font-semibold"><i class="fas fa-flask mr-1"></i> Mode Development Aktif</p>
        <p class="text-xs mt-1" style="color: var(--text-secondary);">
            Anda bisa gunakan tombol simulasi lunas untuk menguji penambahan saldo tanpa harus membayar asli.
        </p>
    </div>
    @endif

    <div class="card p-8 text-center">
        @if($payment)
            @if($payment->isPaid())
                <div class="w-16 h-16 rounded-full mx-auto mb-4 flex items-center justify-center" style="background: rgba(52,199,89,0.12);">
                    <i class="fas fa-check-circle text-3xl" style="color: var(--success);"></i>
                </div>
                <h2 class="font-bold text-xl mb-2">Top Up Berhasil!</h2>
                <p class="text-sm text-gray-500">Saldo Anda telah ditambahkan sebesar <strong>Rp {{ number_format($payment->amount, 0, ',', '.') }}</strong>.</p>
                
                <div class="mt-6 p-4 rounded-xl bg-gray-50 dark:bg-slate-800/50 border border-[var(--outline-variant)]">
                    <div class="flex items-center justify-between text-sm mb-2">
                        <span class="text-gray-500">Total Nominal</span>
                        <span class="font-bold text-[var(--accent)]">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm mb-2">
                        <span class="text-gray-500">Invoice</span>
                        <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $payment->invoice_number }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm mb-2">
                        <span class="text-gray-500">Gateway</span>
                        <span class="font-semibold text-gray-700 dark:text-gray-300">{{ strtoupper($payment->payment_gateway) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm mb-2">
                        <span class="text-gray-500">Metode</span>
                        <span class="font-semibold text-gray-700 dark:text-gray-300">{{ strtoupper($payment->payment_method ?? '-') }} / {{ $payment->payment_channel ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Waktu Bayar</span>
                        <span class="font-semibold text-green-600 dark:text-green-400">{{ $payment->paid_at?->format('d M Y, H:i') }}</span>
                    </div>
                </div>

                <a href="{{ route('client.balance.index') }}" class="btn btn-primary w-full mt-6 py-3 font-bold rounded-xl justify-center">
                    <i class="fas fa-wallet mr-2"></i> Lihat Dompet & Saldo
                </a>

            @elseif($payment->isPendingVerification())
                <div class="w-16 h-16 rounded-full mx-auto mb-4 flex items-center justify-center" style="background: rgba(245,158,11,0.12);">
                    <i class="fas fa-clock text-3xl" style="color: #f59e0b;"></i>
                </div>
                <h2 class="font-bold text-xl mb-2">Menunggu Konfirmasi Admin</h2>
                <p class="text-sm text-gray-500">Bukti transfer Anda telah diterima dan sedang menunggu verifikasi oleh admin (1x24 jam).</p>
                
                <div class="mt-6 p-4 rounded-xl bg-gray-50 dark:bg-slate-800/50 border border-[var(--outline-variant)]">
                    <div class="flex items-center justify-between text-sm mb-2">
                        <span class="text-gray-500">Jumlah Top Up</span>
                        <span class="font-bold text-[var(--accent)]">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm mb-2">
                        <span class="text-gray-500">Invoice</span>
                        <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $payment->invoice_number }}</span>
                    </div>
                </div>

                <div class="mt-6">
                    <p class="text-xs text-gray-500 mb-2">Salah upload foto bukti transfer?</p>
                    <a href="{{ route('client.balance.topup.manual-transfer.instructions', ['payment_id' => $payment->id]) }}" class="btn btn-secondary w-full py-3 font-bold rounded-xl justify-center">
                        <i class="fas fa-upload mr-2"></i> Upload Ulang Bukti Transfer
                    </a>
                </div>

            @elseif($payment->isPending())
                <div class="w-16 h-16 rounded-full mx-auto mb-4 flex items-center justify-center" style="background: rgba(255,149,0,0.12);">
                    <i class="fas fa-clock text-3xl" style="color: var(--warning);"></i>
                </div>
                <h2 class="font-bold text-xl mb-2">Menunggu Pembayaran</h2>
                <p class="text-sm text-gray-500">Silakan selesaikan pembayaran Anda. Saldo akan otomatis bertambah setelah pembayaran lunas.</p>

                <div class="mt-6 p-4 rounded-xl bg-gray-50 dark:bg-slate-800/50 border border-[var(--outline-variant)]">
                    <div class="flex items-center justify-between text-sm mb-2">
                        <span class="text-gray-500">Jumlah Top Up</span>
                        <span class="font-bold text-gray-800 dark:text-gray-200">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm mb-2">
                        <span class="text-gray-500">Invoice</span>
                        <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $payment->invoice_number }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm mb-2">
                        <span class="text-gray-500">Gateway</span>
                        <span class="font-semibold text-gray-700 dark:text-gray-300">{{ strtoupper($payment->payment_gateway) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm mb-2">
                        <span class="text-gray-500">Metode</span>
                        <span class="font-semibold text-gray-700 dark:text-gray-300">{{ strtoupper($payment->payment_method ?? '-') }} / {{ $payment->payment_channel ?? '-' }}</span>
                    </div>
                    @if($payment->expired_at)
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Batas Waktu</span>
                        <span class="font-semibold {{ $payment->expired_at->isPast() ? 'text-red-500' : 'text-yellow-600' }}">
                            {{ $payment->expired_at->format('d M Y, H:i') }}
                        </span>
                    </div>
                    @endif
                </div>

                @if($payment->payment_url)
                <a href="{{ $payment->payment_url }}" target="_blank" class="btn btn-primary w-full mt-6 py-3 font-bold rounded-xl justify-center">
                    <i class="fas fa-external-link-alt mr-2"></i> Lanjutkan Pembayaran
                </a>
                @endif

                @if($devMode ?? false)
                <form method="POST" action="{{ route('client.balance.topup.simulate-paid', $payment) }}" class="mt-2">
                    @csrf
                    <button type="submit" class="btn w-full py-3 rounded-xl justify-center border" style="background: rgba(52,199,89,.12); color: var(--success); border-color: rgba(52,199,89,.25);">
                        <i class="fas fa-check-circle mr-2"></i> Simulasi Bayar Berhasil
                    </button>
                </form>
                @endif

                <a href="{{ route('client.balance.topup.status', ['payment_id' => $payment->id]) }}" class="btn btn-secondary w-full mt-2 py-3 rounded-xl justify-center">
                    <i class="fas fa-sync-alt mr-2"></i> Cek Status Pembayaran
                </a>

            @else
                <div class="w-16 h-16 rounded-full mx-auto mb-4 flex items-center justify-center" style="background: rgba(255,59,48,0.12);">
                    <i class="fas fa-times-circle text-3xl" style="color: var(--danger);"></i>
                </div>
                <h2 class="font-bold text-xl mb-2">Pembayaran Gagal / Kadaluarsa</h2>
                <p class="text-sm text-gray-500">Pembayaran tidak berhasil atau waktu pembayaran telah habis. Silakan coba lagi.</p>
                
                <a href="{{ route('client.balance.topup') }}" class="btn btn-primary w-full mt-6 py-3 font-bold rounded-xl justify-center">
                    <i class="fas fa-redo mr-2"></i> Coba Lagi
                </a>
            @endif
        @else
            <div class="w-16 h-16 rounded-full mx-auto mb-4 flex items-center justify-center" style="background: var(--bg-tertiary);">
                <i class="fas fa-question text-3xl" style="color: var(--text-tertiary);"></i>
            </div>
            <h2 class="font-bold text-xl mb-2">Transaksi Tidak Ditemukan</h2>
            <p class="text-sm text-gray-500">Detail transaksi top-up tidak ditemukan.</p>
            <a href="{{ route('client.balance.topup') }}" class="btn btn-primary w-full mt-6 py-3 font-bold rounded-xl justify-center">
                <i class="fas fa-wallet mr-2"></i> Top Up Sekarang
            </a>
        @endif
    </div>
</div>

<div class="mt-6 text-center">
    <a href="{{ route('client.balance.index') }}" class="text-sm font-semibold text-[var(--accent)] hover:underline flex items-center justify-center gap-1">
        <span class="material-symbols-outlined" style="font-size: 16px;">arrow_back</span>
        Kembali ke Dompet
    </a>
</div>
@endsection
