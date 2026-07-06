@extends('layouts.admin')
@section('title', 'Detail Transfer Manual')
@section('page-title', 'Detail Transfer Manual')
@section('page-subtitle', 'Review & konfirmasi bukti transfer dari client')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- Back --}}
    <a href="{{ route('admin.manual-transfer.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold hover:underline" style="color: var(--accent);">
        <i class="fas fa-arrow-left"></i> Kembali ke Daftar
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Info Pembayaran --}}
        <div class="card p-6 space-y-4">
            <h3 class="font-bold text-base flex items-center gap-2" style="color: var(--accent);">
                <i class="fas fa-receipt"></i> Informasi Pembayaran
            </h3>

            <div class="space-y-3 text-sm">
                <div class="flex justify-between items-center py-2 border-b" style="border-color: var(--border-color);">
                    <span style="color: var(--text-secondary);">Status</span>
                    @if($payment->payment_status === 'pending_verification')
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-semibold" style="background: rgba(255,149,0,0.15); color: #f97316;">
                            <i class="fas fa-hourglass-half"></i> Menunggu Konfirmasi
                        </span>
                    @elseif($payment->isPaid())
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-semibold" style="background: rgba(52,199,89,0.12); color: var(--success);">
                            <i class="fas fa-check-circle"></i> Dikonfirmasi
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-semibold" style="background: rgba(255,59,48,0.12); color: var(--danger);">
                            <i class="fas fa-times-circle"></i> Ditolak
                        </span>
                    @endif
                </div>
                <div class="flex justify-between py-2 border-b" style="border-color: var(--border-color);">
                    <span style="color: var(--text-secondary);">Client</span>
                    <div class="text-right">
                        <div class="font-semibold">{{ $payment->user?->name }}</div>
                        <div class="text-xs" style="color: var(--text-secondary);">{{ $payment->user?->email }}</div>
                    </div>
                </div>
                <div class="flex justify-between py-2 border-b" style="border-color: var(--border-color);">
                    <span style="color: var(--text-secondary);">Invoice</span>
                    <code class="text-xs px-2 py-1 rounded" style="background: var(--surface-container-low);">{{ $payment->invoice_number }}</code>
                </div>
                @if($payment->invitation)
                <div class="flex justify-between py-2 border-b" style="border-color: var(--border-color);">
                    <span style="color: var(--text-secondary);">Undangan</span>
                    <span class="font-medium text-right max-w-[180px]">{{ $payment->invitation->title }}</span>
                </div>
                @endif
                <div class="flex justify-between py-2 border-b" style="border-color: var(--border-color);">
                    <span style="color: var(--text-secondary);">Nominal</span>
                    <span class="font-bold text-lg" style="color: var(--accent);">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-2 border-b" style="border-color: var(--border-color);">
                    <span style="color: var(--text-secondary);">Batas Waktu</span>
                    <span class="{{ $payment->invoice_due_at?->isPast() ? 'text-red-500' : '' }}">
                        {{ $payment->invoice_due_at?->format('d M Y, H:i') ?? '-' }}
                    </span>
                </div>
                <div class="flex justify-between py-2">
                    <span style="color: var(--text-secondary);">Dikirim</span>
                    <span>{{ $payment->created_at->format('d M Y, H:i') }}</span>
                </div>
                @if($payment->isPaid() && $payment->verifiedBy)
                <div class="flex justify-between py-2 border-t" style="border-color: var(--border-color);">
                    <span style="color: var(--text-secondary);">Dikonfirmasi oleh</span>
                    <span class="font-semibold" style="color: var(--success);">{{ $payment->verifiedBy->name }}</span>
                </div>
                @endif
                @if($payment->payment_status === 'failed' && $payment->transfer_rejection_reason)
                <div class="p-3 rounded-xl mt-2" style="background: rgba(255,59,48,0.08); border: 1px solid rgba(255,59,48,0.2);">
                    <p class="text-xs font-semibold mb-1" style="color: var(--danger);">Alasan Penolakan:</p>
                    <p class="text-sm">{{ $payment->transfer_rejection_reason }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Bukti Transfer --}}
        <div class="card p-6 space-y-4">
            <h3 class="font-bold text-base flex items-center gap-2" style="color: var(--accent);">
                <i class="fas fa-image"></i> Bukti Transfer
            </h3>

            @if($payment->transfer_proof_path)
                <div class="rounded-xl overflow-hidden border" style="border-color: var(--border-color);">
                    <a href="{{ Storage::url($payment->transfer_proof_path) }}" target="_blank" title="Klik untuk lihat ukuran penuh">
                        <img src="{{ Storage::url($payment->transfer_proof_path) }}"
                             alt="Bukti Transfer"
                             class="w-full object-contain max-h-80 bg-gray-100 dark:bg-slate-800 hover:opacity-90 transition-opacity cursor-zoom-in">
                    </a>
                </div>
                <p class="text-xs text-center" style="color: var(--text-secondary);">
                    <i class="fas fa-search-plus mr-1"></i> Klik gambar untuk melihat ukuran penuh
                </p>
            @else
                <div class="flex flex-col items-center justify-center py-12 rounded-xl" style="background: var(--surface-container-low);">
                    <i class="fas fa-image text-4xl mb-3 opacity-30"></i>
                    <p class="text-sm" style="color: var(--text-secondary);">Bukti transfer belum diunggah</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Aksi (hanya tampil jika masih pending_verification) --}}
    @if($payment->isPendingVerification())
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        {{-- Konfirmasi --}}
        <div class="card p-6">
            <h4 class="font-bold text-sm mb-3 flex items-center gap-2" style="color: var(--success);">
                <i class="fas fa-check-circle"></i> Konfirmasi Pembayaran
            </h4>
            <p class="text-sm mb-4" style="color: var(--text-secondary);">
                Pastikan nominal transfer sesuai sebelum mengkonfirmasi. Setelah dikonfirmasi, undangan client akan otomatis diaktifkan.
            </p>
            <form method="POST" action="{{ route('admin.manual-transfer.confirm', $payment) }}"
                  onsubmit="return confirm('Konfirmasi pembayaran Rp {{ number_format($payment->amount, 0, ',', '.') }} dari {{ $payment->user?->name }}?')">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn w-full py-3 font-bold rounded-xl justify-center flex items-center gap-2"
                        style="background: var(--success); color: white;">
                    <i class="fas fa-check"></i> Konfirmasi & Aktifkan Undangan
                </button>
            </form>
        </div>

        {{-- Tolak --}}
        <div class="card p-6" x-data="{ showForm: false }">
            <h4 class="font-bold text-sm mb-3 flex items-center gap-2" style="color: var(--danger);">
                <i class="fas fa-times-circle"></i> Tolak Pembayaran
            </h4>
            <p class="text-sm mb-4" style="color: var(--text-secondary);">
                Tolak jika bukti tidak valid, nominal tidak sesuai, atau ada indikasi fraud.
            </p>
            <button @click="showForm = !showForm" type="button"
                    class="btn w-full py-3 font-bold rounded-xl justify-center flex items-center gap-2"
                    style="background: rgba(255,59,48,0.1); color: var(--danger); border: 1px solid rgba(255,59,48,0.3);">
                <i class="fas fa-times"></i> Tolak Pembayaran
            </button>

            <form x-show="showForm" x-transition method="POST"
                  action="{{ route('admin.manual-transfer.reject', $payment) }}"
                  class="mt-4 space-y-3">
                @csrf
                @method('PATCH')
                <div>
                    <label class="form-label text-xs">Alasan penolakan <span class="text-red-500">*</span></label>
                    <textarea name="rejection_reason" rows="3" required
                              class="form-input text-sm"
                              placeholder="Contoh: Bukti transfer tidak terbaca, nominal tidak sesuai, dll."></textarea>
                </div>
                <button type="submit" class="btn w-full py-2.5 text-sm font-bold rounded-xl"
                        style="background: var(--danger); color: white;">
                    <i class="fas fa-ban mr-2"></i> Kirim Penolakan
                </button>
            </form>
        </div>
    </div>
    @endif

</div>
@endsection
