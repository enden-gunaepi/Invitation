@extends('layouts.admin')
@section('title', 'Integrasi Telegram')
@section('page-title', 'Integrasi')
@section('page-subtitle', 'Konfigurasi bot Telegram untuk notifikasi & command')

@section('content')
<div class="flex flex-col lg:flex-row gap-6">
    {{-- Sidebar Tab --}}
    <div class="lg:w-56 shrink-0">
        <div class="card p-3">
            <div class="flex flex-row lg:flex-col gap-1">
                <a href="{{ route('admin.integration.telegram') }}"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.integration.telegram*') ? 'bg-blue-500/10' : 'hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                    style="color: {{ request()->routeIs('admin.integration.telegram*') ? 'var(--accent)' : 'var(--text-secondary)' }};">
                    <i class="fab fa-telegram text-lg w-5 text-center"></i>
                    <span>Telegram</span>
                </a>
                <a href="{{ route('admin.integration.whatsapp') }}"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.integration.whatsapp*') ? 'bg-green-500/10' : 'hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                    style="color: {{ request()->routeIs('admin.integration.whatsapp*') ? 'var(--success)' : 'var(--text-secondary)' }};">
                    <i class="fab fa-whatsapp text-lg w-5 text-center"></i>
                    <span>WhatsApp</span>
                </a>
                <a href="{{ route('admin.integration.email') }}"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.integration.email*') ? 'bg-purple-500/10' : 'hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                    style="color: {{ request()->routeIs('admin.integration.email*') ? '#8b5cf6' : 'var(--text-secondary)' }};">
                    <i class="fas fa-envelope text-lg w-5 text-center"></i>
                    <span>Email</span>
                </a>
                <a href="{{ route('admin.integration.payment-gateway') }}"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('admin.integration.payment-gateway*') ? 'bg-amber-500/10' : 'hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                    style="color: {{ request()->routeIs('admin.integration.payment-gateway*') ? '#d97706' : 'var(--text-secondary)' }};">
                    <i class="fas fa-credit-card text-lg w-5 text-center"></i>
                    <span>Payment</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="flex-1 space-y-6">
        {{-- Config Form --}}
        <form method="POST" action="{{ route('admin.integration.telegram.update') }}">
            @csrf @method('PUT')
            <div class="card p-6">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: rgba(0,136,204,0.1); color: #0088cc;">
                            <i class="fab fa-telegram text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-base">Telegram Bot</h3>
                            <p class="text-xs" style="color: var(--text-secondary);">Konfigurasi bot token dan chat ID</p>
                        </div>
                    </div>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="hidden" name="telegram_enabled" value="0">
                        <input type="checkbox" name="telegram_enabled" value="1" {{ $config['telegram_enabled'] === '1' ? 'checked' : '' }}
                            style="width:18px;height:18px;accent-color:#0088cc;">
                        <span class="text-xs font-semibold">Aktif</span>
                    </label>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="form-label">Bot Token</label>
                        <input type="password" name="telegram_bot_token" class="form-input" value="{{ $config['telegram_bot_token'] }}" placeholder="123456789:ABCdefGhIJKlmNoPQRsTUVwxyZ">
                        <p class="text-[11px] mt-1" style="color: var(--text-tertiary);">Dapatkan dari <a href="https://t.me/BotFather" target="_blank" class="underline" style="color: var(--accent);">@BotFather</a> di Telegram</p>
                    </div>
                    <div>
                        <label class="form-label">Command Chat ID</label>
                        <input type="text" name="telegram_chat_id" class="form-input" value="{{ $config['telegram_chat_id'] }}" placeholder="-1001234567890, 123456789">
                        <p class="text-[11px] mt-1" style="color: var(--text-tertiary);">
                            Chat ID yang <strong>diizinkan mengirim command</strong> (/topup, /saldo, /user, /stats). Pisahkan dengan koma jika lebih dari satu.<br>
                            Command <code>/chatid</code> bisa digunakan oleh siapa saja (tanpa perlu terdaftar di sini).
                        </p>
                    </div>
                    <div>
                        <label class="form-label">Notify Chat ID <span class="text-[10px] font-normal" style="color: var(--text-tertiary);">(opsional)</span></label>
                        <input type="text" name="telegram_notify_chat_id" class="form-input" value="{{ $config['telegram_notify_chat_id'] }}" placeholder="-1009876543210">
                        <p class="text-[11px] mt-1" style="color: var(--text-tertiary);">
                            Chat ID tujuan <strong>notifikasi otomatis</strong> (user register, undangan, pembayaran, dll). Biasanya ID group admin.<br>
                            Jika kosong, notifikasi akan dikirim ke Command Chat ID. Pisahkan dengan koma jika lebih dari satu.
                        </p>
                    </div>
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
            <div class="card p-6">
                <h4 class="font-bold text-sm mb-2">Test Koneksi</h4>
                <p class="text-xs mb-4" style="color: var(--text-secondary);">Pastikan bot token valid dan bot bisa dihubungi.</p>
                <form method="POST" action="{{ route('admin.integration.telegram.test') }}">
                    @csrf
                    <button type="submit" class="btn btn-secondary" {{ empty($config['telegram_bot_token']) ? 'disabled' : '' }}>
                        <i class="fas fa-vial mr-1"></i> Test Bot Token
                    </button>
                </form>
            </div>

            {{-- Test Message --}}
            <div class="card p-6">
                <h4 class="font-bold text-sm mb-2">Kirim Pesan Test</h4>
                <p class="text-xs mb-4" style="color: var(--text-secondary);">Kirim pesan percobaan ke chat ID yang dikonfigurasi.</p>
                <form method="POST" action="{{ route('admin.integration.telegram.test-message') }}">
                    @csrf
                    <button type="submit" class="btn btn-secondary" {{ empty($config['telegram_bot_token']) || empty($config['telegram_chat_id']) ? 'disabled' : '' }}>
                        <i class="fas fa-paper-plane mr-1"></i> Kirim Test
                    </button>
                </form>
            </div>
        </div>

        {{-- Webhook --}}
        <div class="card p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: rgba(52,199,89,0.1); color: var(--success);">
                    <i class="fas fa-satellite-dish text-lg"></i>
                </div>
                <div>
                    <h3 class="font-bold text-base">Webhook</h3>
                    <p class="text-xs" style="color: var(--text-secondary);">Terima command dari Telegram (topup, cek saldo, dll)</p>
                </div>
            </div>

            <div class="p-3 rounded-lg text-xs mb-4" style="background: var(--hover-bg); color: var(--text-secondary);">
                <strong>Webhook URL:</strong><br>
                <code style="color: var(--accent);">{{ $webhookUrl }}</code>
            </div>

            @if($webhookInfo)
                <div class="p-3 rounded-lg text-xs mb-4" style="background: var(--hover-bg);">
                    <strong style="color: var(--text);">Status Webhook:</strong><br>
                    @if(!empty($webhookInfo['url']))
                        <span style="color: var(--success);">● Aktif</span> — <code style="color: var(--text-secondary);">{{ $webhookInfo['url'] }}</code>
                        @if(isset($webhookInfo['last_error_date']))
                            <br><span style="color: var(--danger);">Last error:</span> <span style="color: var(--text-secondary);">{{ $webhookInfo['last_error_message'] ?? '-' }}</span>
                        @endif
                        @if(isset($webhookInfo['pending_update_count']))
                            <br><span style="color: var(--text-secondary);">Pending updates: {{ $webhookInfo['pending_update_count'] }}</span>
                        @endif
                    @else
                        <span style="color: var(--text-tertiary);">● Tidak aktif</span>
                    @endif
                </div>
            @endif

            <div class="flex items-center gap-3">
                <form method="POST" action="{{ route('admin.integration.telegram.set-webhook') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary" {{ empty($config['telegram_bot_token']) ? 'disabled' : '' }}>
                        <i class="fas fa-link mr-1"></i> Set Webhook
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.integration.telegram.delete-webhook') }}">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger" {{ empty($config['telegram_bot_token']) ? 'disabled' : '' }}>
                        <i class="fas fa-unlink mr-1"></i> Hapus Webhook
                    </button>
                </form>
            </div>

            {{-- Available Commands --}}
            <div class="mt-6 pt-4" style="border-top: 1px solid var(--border);">
                <h4 class="font-bold text-sm mb-3">Command yang Tersedia</h4>
                <div class="space-y-2">
                    @foreach([
                        ['/topup',   'var(--accent)',   '/topup email@example.com 50000',  'Tambah saldo user'],
                        ['/saldo',   'var(--accent)',   '/saldo email@example.com',        'Cek saldo user'],
                        ['/user',    'var(--accent)',   '/user email@example.com',         'Info lengkap user (nama, role, undangan, pembayaran)'],
                        ['/stats',   '#d97706',        '/stats',                           'Statistik sistem: users, undangan, revenue hari ini & bulan ini'],
                        ['/chatid',  'var(--accent)',   '/chatid',                         'Info detail chat & pengirim (untuk mengetahui Chat ID)'],
                        ['/help',    'var(--accent)',   '/help',                           'Tampilkan daftar command'],
                    ] as [$cmd, $color, $example, $desc])
                    <div class="p-2.5 rounded-lg" style="background: var(--hover-bg);">
                        <div class="flex items-center gap-2 mb-1">
                            <code class="text-xs font-bold px-2 py-0.5 rounded" style="background: var(--accent-bg); color: {{ $color }};">{{ $cmd }}</code>
                        </div>
                        <div class="text-xs" style="color: var(--text-secondary);">
                            <code>{{ $example }}</code><br>
                            <span class="mt-0.5 block" style="color: var(--text-tertiary);">{{ $desc }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
