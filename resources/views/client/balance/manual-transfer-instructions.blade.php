@extends('layouts.client')
@section('title', 'Instruksi Transfer Top Up')
@section('page-title', 'Transfer Manual')
@section('page-subtitle', 'Selesaikan pembayaran top up Anda')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="text-center space-y-2 mb-8">
        <div class="w-16 h-16 bg-[var(--accent-bg)] rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-university text-2xl" style="color: var(--accent);"></i>
        </div>
        <h2 class="text-xl font-bold">Menunggu Pembayaran Top Up</h2>
        <p class="text-sm" style="color: var(--text-secondary);">Silakan transfer tepat sesuai nominal di bawah ini ke salah satu rekening yang tersedia.</p>
    </div>

    {{-- Info Tagihan --}}
    <div class="card p-6 text-center" style="border: 2px dashed var(--accent);">
        <p class="text-sm font-semibold mb-1" style="color: var(--text-secondary);">Total Tagihan</p>
        <div class="text-3xl font-bold mb-2 flex items-center justify-center gap-2" style="color: var(--accent);">
            Rp <span id="payment-amount">{{ number_format($payment->amount, 0, '', '') }}</span>
            <button type="button" onclick="copyAmount()" class="text-sm bg-gray-100 hover:bg-gray-200 dark:bg-slate-700 dark:hover:bg-slate-600 rounded-lg px-2 py-1 transition-colors" title="Salin Nominal">
                <i class="fas fa-copy"></i>
            </button>
        </div>
        <p class="text-xs" style="color: var(--text-tertiary);">Invoice: <code>{{ $payment->invoice_number }}</code></p>
    </div>

    {{-- Rekening Tujuan --}}
    <div class="card p-6">
        <h3 class="font-bold text-base mb-4 flex items-center gap-2">
            <i class="fas fa-list-ul" style="color: var(--accent);"></i> Pilihan Rekening Tujuan
        </h3>

        @if($bankAccounts->isEmpty())
            <div class="p-4 rounded-xl text-center" style="background: rgba(255,59,48,0.1); color: var(--danger);">
                Belum ada rekening bank yang dikonfigurasi oleh admin.
            </div>
        @else
            <div class="space-y-4">
                @foreach($bankAccounts as $account)
                <div class="p-4 rounded-xl border flex items-center justify-between transition-all hover:border-[var(--accent)]" style="background: var(--surface-container-low); border-color: var(--border-color);">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white dark:bg-slate-800 rounded-xl shadow-sm flex items-center justify-center font-bold text-[var(--accent)] text-xs border border-gray-100 dark:border-slate-700">
                            {{ strtoupper($account->bank_name) }}
                        </div>
                        <div>
                            <p class="text-sm font-bold">{{ $account->bank_name }}</p>
                            <p class="text-lg font-mono tracking-wider font-semibold my-0.5" id="account-{{ $account->id }}">{{ $account->account_number }}</p>
                            <p class="text-xs" style="color: var(--text-secondary);">a/n {{ $account->account_holder_name }}</p>
                        </div>
                    </div>
                    <button type="button" onclick="copyText('account-{{ $account->id }}')"
                            class="btn btn-secondary btn-sm flex-shrink-0"
                            style="background: var(--surface-container); border: none;">
                        <i class="fas fa-copy"></i> Salin
                    </button>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Form Upload Bukti --}}
    <div class="card p-6" id="upload-section">
        <h3 class="font-bold text-base mb-4 flex items-center gap-2">
            <i class="fas fa-cloud-upload-alt" style="color: var(--success);"></i> Upload Bukti Transfer
        </h3>

        <form method="POST" action="{{ route('client.balance.topup.manual-transfer.proof') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <input type="hidden" name="payment_id" value="{{ $payment->id }}">

            <div x-data="{ previewUrl: null }" class="space-y-3">
                <label class="block">
                    <span class="sr-only">Pilih foto bukti transfer</span>
                    <input type="file" name="transfer_proof" accept="image/jpeg,image/png,image/webp" required
                           @change="previewUrl = URL.createObjectURL($event.target.files[0])"
                           class="block w-full text-sm text-gray-500 dark:text-gray-400
                                  file:mr-4 file:py-3 file:px-4
                                  file:rounded-full file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-[var(--accent-bg)] file:text-[var(--accent)]
                                  hover:file:bg-[var(--accent)] hover:file:text-white transition-all cursor-pointer"/>
                </label>
                <p class="text-xs" style="color: var(--text-secondary);">Maksimal 5MB. Format: JPG, PNG, WEBP.</p>

                <!-- Image Preview -->
                <div x-show="previewUrl" style="display: none;" class="mt-4 p-2 border rounded-xl" style="border-color: var(--border-color);">
                    <img :src="previewUrl" class="max-h-64 mx-auto rounded-lg object-contain" alt="Preview Bukti Transfer">
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-full py-3.5 mt-4 text-center font-bold text-base rounded-xl">
                <i class="fas fa-paper-plane mr-2"></i> Kirim Bukti Transfer
            </button>
        </form>
    </div>

</div>

{{-- Script Copy to Clipboard --}}
<script>
function copyAmount() {
    const amount = document.getElementById('payment-amount').innerText.replace(/\./g, '');
    navigator.clipboard.writeText(amount).then(() => {
        alert('Nominal berhasil disalin!');
    }).catch(err => {
        console.error('Gagal menyalin:', err);
    });
}

function copyText(elementId) {
    const text = document.getElementById(elementId).innerText;
    navigator.clipboard.writeText(text).then(() => {
        alert('Nomor rekening berhasil disalin!');
    }).catch(err => {
        console.error('Gagal menyalin:', err);
    });
}
</script>
@endsection
