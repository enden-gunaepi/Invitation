@extends('layouts.admin')
@section('title', 'Integrasi WhatsApp')
@section('page-title', 'Integrasi')
@section('page-subtitle', 'Konfigurasi WhatsApp via WeaGate API')

@section('content')
<div class="flex flex-col lg:flex-row gap-6">
    {{-- Sidebar Tab --}}
    <div class="lg:w-56 shrink-0">
        <div class="card p-3">
            <div class="flex flex-row lg:flex-col gap-1">
                <a href="{{ route('admin.integration.telegram') }}"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all hover:bg-slate-100 dark:hover:bg-slate-800" style="color: var(--text-secondary);">
                    <i class="fab fa-telegram text-lg w-5 text-center"></i>
                    <span>Telegram</span>
                </a>
                <a href="{{ route('admin.integration.whatsapp') }}"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all bg-green-500/10" style="color: var(--success);">
                    <i class="fab fa-whatsapp text-lg w-5 text-center"></i>
                    <span>WhatsApp</span>
                </a>
                <a href="{{ route('admin.integration.email') }}"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all hover:bg-slate-100 dark:hover:bg-slate-800" style="color: var(--text-secondary);">
                    <i class="fas fa-envelope text-lg w-5 text-center"></i>
                    <span>Email</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="flex-1 space-y-6">
        {{-- Config Form --}}
        <form method="POST" action="{{ route('admin.integration.whatsapp.update') }}">
            @csrf @method('PUT')
            <div class="card p-6">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: rgba(37,211,102,0.1); color: #25D366;">
                            <i class="fab fa-whatsapp text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-base">WhatsApp API</h3>
                            <p class="text-xs" style="color: var(--text-secondary);">Kirim pesan WhatsApp melalui vendor API</p>
                        </div>
                    </div>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="hidden" name="whatsapp_enabled" value="0">
                        <input type="checkbox" name="whatsapp_enabled" value="1" {{ $config['whatsapp_enabled'] === '1' ? 'checked' : '' }}
                            style="width:18px;height:18px;accent-color:#25D366;">
                        <span class="text-xs font-semibold">Aktif</span>
                    </label>
                </div>

                <div class="space-y-4">
                    {{-- Vendor --}}
                    <div>
                        <label class="form-label">Vendor</label>
                        <select name="whatsapp_vendor" class="form-input">
                            <option value="weagate" {{ $config['whatsapp_vendor'] === 'weagate' ? 'selected' : '' }}>WeaGate</option>
                        </select>
                        <p class="text-[11px] mt-1" style="color: var(--text-tertiary);">Pilih vendor penyedia API WhatsApp</p>
                    </div>

                    {{-- Domain API --}}
                    <div>
                        <label class="form-label">Domain API</label>
                        <input type="url" name="whatsapp_weagate_domain_api" class="form-input" value="{{ $config['whatsapp_weagate_domain_api'] }}" placeholder="https://mywifi.weagate.com">
                        <p class="text-[11px] mt-1" style="color: var(--text-tertiary);">Base URL API WeaGate (tanpa trailing slash)</p>
                    </div>

                    {{-- Token --}}
                    <div>
                        <label class="form-label">Device Token</label>
                        <input type="password" name="whatsapp_weagate_token" class="form-input" value="{{ $config['whatsapp_weagate_token'] }}" placeholder="YOUR_DEVICE_TOKEN">
                        <p class="text-[11px] mt-1" style="color: var(--text-tertiary);">Token device dari dashboard WeaGate</p>
                    </div>

                    {{-- Kirim Instan --}}
                    <div>
                        <label class="form-label">Mode Pengiriman</label>
                        <div class="flex items-center gap-4 mt-1">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="hidden" name="whatsapp_weagate_instan" value="0">
                                <input type="checkbox" name="whatsapp_weagate_instan" value="1" {{ $config['whatsapp_weagate_instan'] === '1' ? 'checked' : '' }}
                                    style="width:18px;height:18px;accent-color:#25D366;">
                                <span class="text-sm font-medium">Kirim Instan</span>
                            </label>
                        </div>
                        <p class="text-[11px] mt-1" style="color: var(--text-tertiary);">Jika aktif, pesan langsung dikirim. Jika tidak, pesan masuk antrian (delay) di server WeaGate.</p>
                    </div>
                </div>

                {{-- API Info --}}
                <div class="p-3 rounded-lg text-xs mt-4" style="background: var(--hover-bg); color: var(--text-secondary);">
                    <strong>Endpoint:</strong><br>
                    <code style="color: var(--accent);">{{ $config['whatsapp_weagate_domain_api'] }}/api/send-message</code>
                </div>

                <div class="flex items-center gap-3 mt-6 pt-4" style="border-top: 1px solid var(--border);">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Simpan
                    </button>
                </div>
            </div>
        </form>

        {{-- Actions --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Test Connection --}}
            <div class="card p-6" x-data="waTestConnection()">
                <h4 class="font-bold text-sm mb-2">Test Koneksi</h4>
                <p class="text-xs mb-4" style="color: var(--text-secondary);">Verifikasi token dan domain API valid.</p>
                <button type="button" @click="test()" class="btn btn-secondary" :disabled="loading || !hasToken" :class="loading ? 'opacity-60 pointer-events-none' : ''">
                    <template x-if="loading">
                        <svg class="animate-spin h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    </template>
                    <template x-if="!loading">
                        <i class="fas fa-vial mr-1"></i>
                    </template>
                    <span x-text="loading ? 'Menghubungkan...' : 'Test Koneksi'"></span>
                </button>

                {{-- Result Card --}}
                <div x-show="result !== null" x-transition class="mt-4 p-4 rounded-xl text-xs" :class="result?.success ? 'bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20' : 'bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20'" x-cloak>
                    <template x-if="result?.success">
                        <div class="space-y-2">
                            <div class="flex items-center gap-2 font-bold text-sm" style="color: var(--success);">
                                <i class="fas fa-check-circle"></i> Koneksi Berhasil
                            </div>
                            <div class="grid grid-cols-2 gap-2 mt-2">
                                <div>
                                    <span style="color: var(--text-tertiary);">Device</span>
                                    <p class="font-semibold" style="color: var(--text);" x-text="result.device_name"></p>
                                </div>
                                <div>
                                    <span style="color: var(--text-tertiary);">Paket</span>
                                    <p class="font-semibold" style="color: var(--text);" x-text="result.package"></p>
                                </div>
                                <div>
                                    <span style="color: var(--text-tertiary);">Status</span>
                                    <p class="font-semibold">
                                        <span :class="result.device_status === 'connected' ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400'" x-text="result.device_status === 'connected' ? '● Connected' : '● Disconnected'"></span>
                                    </p>
                                </div>
                                <div>
                                    <span style="color: var(--text-tertiary);">Expired</span>
                                    <p class="font-semibold" style="color: var(--text);" x-text="result.expired_at ? new Date(result.expired_at).toLocaleDateString('id-ID', {day:'numeric',month:'short',year:'numeric'}) : '-'"></p>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template x-if="result && !result.success">
                        <div class="flex items-start gap-2" style="color: var(--danger);">
                            <i class="fas fa-times-circle mt-0.5"></i>
                            <span>Koneksi gagal: <span x-text="result.error"></span></span>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Test Message --}}
            <div class="card p-6" x-data="waTestMessage()">
                <h4 class="font-bold text-sm mb-2">Kirim Pesan Test</h4>
                <p class="text-xs mb-4" style="color: var(--text-secondary);">Kirim pesan percobaan ke nomor tertentu.</p>
                <div class="space-y-3">
                    <div>
                        <input type="text" x-model="phone" class="form-input" placeholder="6281234567890" required>
                        <p class="text-[11px] mt-1" style="color: var(--text-tertiary);">Format: 62xxx (tanpa + atau 0)</p>
                    </div>
                    <div>
                        <textarea x-model="message" class="form-input" rows="3" placeholder="Tulis pesan custom..."></textarea>
                    </div>
                    <button type="button" @click="send()" class="btn btn-secondary" :disabled="loading || !hasToken || !phone" :class="loading ? 'opacity-60 pointer-events-none' : ''">
                        <template x-if="loading">
                            <svg class="animate-spin h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        </template>
                        <template x-if="!loading">
                            <i class="fas fa-paper-plane mr-1"></i>
                        </template>
                        <span x-text="loading ? 'Mengirim...' : 'Kirim Test'"></span>
                    </button>

                    {{-- Result Card --}}
                    <div x-show="result !== null" x-transition class="p-3 rounded-xl text-xs" :class="result?.success ? 'bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20' : 'bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20'" x-cloak>
                        <template x-if="result?.success">
                            <div class="flex items-center gap-2 font-semibold" style="color: var(--success);">
                                <i class="fas fa-check-circle"></i> Pesan berhasil dikirim ke <span x-text="result.phone"></span>
                            </div>
                        </template>
                        <template x-if="result && !result.success">
                            <div class="flex items-start gap-2" style="color: var(--danger);">
                                <i class="fas fa-times-circle mt-0.5"></i>
                                <span>Gagal: <span x-text="result.error"></span></span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('head')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('waTestConnection', () => ({
            loading: false,
            result: null,
            hasToken: {{ !empty($config['whatsapp_weagate_token']) ? 'true' : 'false' }},
            async test() {
                this.loading = true;
                this.result = null;
                try {
                    const res = await fetch('{{ route("admin.integration.whatsapp.test") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' },
                    });
                    this.result = await res.json();
                } catch (e) {
                    this.result = { success: false, error: e.message };
                } finally {
                    this.loading = false;
                }
            }
        }));

        Alpine.data('waTestMessage', () => ({
            loading: false,
            result: null,
            phone: '',
            message: 'Halo, ini pesan test dari panel admin!',
            hasToken: {{ !empty($config['whatsapp_weagate_token']) ? 'true' : 'false' }},
            async send() {
                if (!this.phone) return;
                this.loading = true;
                this.result = null;
                try {
                    const res = await fetch('{{ route("admin.integration.whatsapp.test-message") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' },
                        body: JSON.stringify({ test_phone: this.phone, test_message: this.message }),
                    });
                    this.result = await res.json();
                } catch (e) {
                    this.result = { success: false, error: e.message };
                } finally {
                    this.loading = false;
                }
            }
        }));
    });
</script>
@endpush
@endsection
