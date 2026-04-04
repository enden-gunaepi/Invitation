@extends('layouts.client')
@section('title', 'Kelola Tamu')
@section('page-title', 'Kelola Tamu')
@section('page-subtitle', $invitation->title)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="card overflow-hidden">
            <div class="px-6 py-4 border-b" style="border-color: var(--border);">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-bold text-base">Daftar Tamu</h3>
                    <span class="text-xs font-semibold" style="color: var(--text-secondary);">{{ $currentGuests }}/{{ $maxGuests }}</span>
                </div>
                <div class="flex items-center gap-3 text-xs mb-2" style="color: var(--text-secondary);">
                    <span><i class="fas fa-check-circle mr-1"></i>Check-in: {{ $checkedInGuests ?? 0 }}</span>
                    <span><i class="fas fa-chair mr-1"></i>Seat assigned: {{ $seatAssignedGuests ?? 0 }}</span>
                </div>
                @php $percent = $maxGuests > 0 ? min(100, round(($currentGuests / $maxGuests) * 100)) : 0; @endphp
                <div style="background: var(--bg-tertiary); height: 4px; border-radius: 2px; overflow: hidden;">
                    <div style="width: {{ $percent }}%; height: 100%; border-radius: 2px; transition: width 0.3s;
                        background: {{ $percent >= 90 ? 'var(--danger)' : ($percent >= 70 ? 'var(--warning)' : 'var(--accent)') }};"></div>
                </div>
                @if($percent >= 90)
                <p class="text-xs mt-1" style="color: var(--danger);">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Kuota tamu hampir penuh! {{ $percent >= 100 ? 'Upgrade paket untuk menambah tamu.' : '' }}
                </p>
                @endif
            </div>
            <div class="p-4">
                @forelse($guests as $guest)
                <div class="flex items-center gap-4 p-3 rounded-lg transition mb-1" style="border-radius: var(--radius-sm);"
                     onmouseover="this.style.background='var(--hover-bg)'" onmouseout="this.style.background='transparent'">
                    <div class="user-avatar" style="width:36px;height:36px;font-size:12px;">{{ substr($guest->name, 0, 1) }}</div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold">{{ $guest->name }}</p>
                        <p class="text-xs" style="color: var(--text-secondary);">
                            {{ $guest->category ?? 'Umum' }} - {{ $guest->pax }} orang
                            @if($guest->phone) - {{ $guest->phone }} @endif
                        </p>
                        <p class="text-xs mt-1" style="color: var(--text-secondary);">
                            Meja: <strong>{{ $guest->table_number ? 'T' . $guest->table_number : '-' }}</strong>
                            | Kursi: <strong>{{ $guest->seat_label ?? '-' }}</strong>
                            | Check-in:
                            <strong style="color: {{ $guest->checked_in_at ? 'var(--success)' : 'var(--text-secondary)' }};">
                                {{ $guest->checked_in_at ? $guest->checked_in_at->format('H:i') : 'Belum' }}
                            </strong>
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($invitation->isActive())
                        @php
                            $guestUrl = $guest->getInvitationUrl();
                            $signature = trim((string) (($invitation->groom_name ?? 'Mempelai Pria') . ' dan ' . ($invitation->bride_name ?? 'Mempelai Wanita')));
                            $waMessage = "Kepada Yth.\n"
                                . "Bapak/Ibu/Saudara/i\n"
                                . "{$guest->name}\n"
                                . "_______\n\n"
                                . "Assalamualaikum Warahmatullahi Wabarakatuh\n\n"
                                . "Tanpa mengurangi rasa hormat, perkenankan kami mengundang Bapak/Ibu/Saudara/i untuk menghadiri acara pernikahan kami.\n\n"
                                . "Untuk informasi detail Acara, Lokasi, dan Waktu lebih lengkap bisa akses link undangan berikut:\n"
                                . "{$guestUrl}\n\n"
                                . "Merupakan suatu kehormatan dan kebahagiaan bagi kami apabila Bapak/Ibu/Saudara/i berkenan hadir dan memberikan doa restu di acara pernikahan kami.\n"
                                . "Karena keterbatasan jarak dan waktu tidak dapat mengirimkan undangan ini secara langsung, maka melalui e-invitation ini dapat menjadi pengganti undangan resmi sehingga tujuan kami tersampaikan.\n\n"
                                . "Wassalamualaikum Warahmatullahi Wabarakatuh\n\n"
                                . "Hormat kami,\n"
                                . "{$signature}\n"
                                . "________";
                            $shareText = urlencode($waMessage);
                        @endphp
                        <a href="https://wa.me/?text={{ $shareText }}" target="_blank"
                           class="topbar-btn" style="width:32px;height:32px;color:#25D366;" title="Share WhatsApp">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        <a href="https://t.me/share/url?url={{ urlencode($guestUrl) }}&text={{ $shareText }}" target="_blank"
                           class="topbar-btn" style="width:32px;height:32px;color:#229ED9;" title="Share Telegram">
                            <i class="fab fa-telegram-plane"></i>
                        </a>
                        <button onclick="navigator.clipboard.writeText('{{ $guest->getInvitationUrl() }}'); this.innerHTML='<i class=\'fas fa-check\' style=\'color:var(--success)\'></i>'; setTimeout(() => this.innerHTML='<i class=\'fas fa-link\'></i>', 2000);"
                                class="topbar-btn" style="width:32px;height:32px;" title="Copy link">
                            <i class="fas fa-link"></i>
                        </button>
                        @endif
                        <form method="POST" action="{{ route('client.invitations.guests.destroy', [$invitation, $guest]) }}" onsubmit="return confirm('Hapus tamu ini?')">
                            @csrf @method('DELETE')
                            <button class="topbar-btn" style="width:32px;height:32px;color:var(--danger);" title="Hapus">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="text-center py-8" style="color: var(--text-secondary);">
                    <i class="fas fa-users text-3xl mb-3 opacity-40"></i>
                    <p class="text-sm">Belum ada tamu. Tambahkan dari form di samping.</p>
                </div>
                @endforelse
            </div>
        </div>
        <div class="mt-4">{{ $guests->links() }}</div>
    </div>

    <div>
        <div class="card p-4 mb-4">
            <div class="flex items-center gap-3">
                <div class="stat-icon" style="background: var(--accent-bg); color: var(--accent); width:32px; height:32px; font-size:13px;">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold" style="color: var(--text-secondary);">Paket {{ $invitation->package->name ?? '-' }}</p>
                    <p class="text-sm font-bold">{{ $currentGuests }} / {{ $maxGuests }} tamu</p>
                </div>
            </div>
            <a href="{{ route('client.invitations.checkin', $invitation) }}" class="btn btn-secondary w-full text-center block text-sm mt-3">
                <i class="fas fa-qrcode mr-2"></i> Scanner Check-in Hari H
            </a>
        </div>

        @if($currentGuests < $maxGuests)
        <div class="card p-6">
            <h3 class="font-bold text-base mb-4">Tambah Tamu</h3>
            <form method="POST" action="{{ route('client.invitations.guests.store', $invitation) }}">
                @csrf
                <div class="mb-4">
                    <label class="form-label">Nama</label>
                    <input type="text" name="name" class="form-input" required placeholder="Nama tamu">
                    @error('name') <p class="text-xs mt-1" style="color: var(--danger);">{{ $message }}</p> @enderror
                </div>
                <div class="mb-4">
                    <label class="form-label">No. HP</label>
                    <input type="text" name="phone" class="form-input" placeholder="08xxx">
                </div>
                <div class="mb-4">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input" placeholder="email@contoh.com">
                </div>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="form-label">Kategori</label>
                        <input type="text" name="category" class="form-input" placeholder="Keluarga">
                    </div>
                    <div>
                        <label class="form-label">Jumlah Kursi</label>
                        <input type="number" name="pax" class="form-input" value="1" min="1" max="10" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-full text-sm">
                    <i class="fas fa-plus mr-2"></i> Tambah Tamu
                </button>
            </form>
        </div>

        <div class="card p-6 mt-4">
            <h3 class="font-bold text-base mb-2">Import Tamu dari Excel</h3>
            <p class="text-xs mb-4" style="color: var(--text-secondary);">
                Upload file <strong>.xlsx/.xls/.csv</strong>. Kolom yang didukung: <code>name</code>, <code>phone</code>, <code>email</code>, <code>category</code>, <code>pax</code>, <code>notes</code>.
            </p>
            <form method="POST" action="{{ route('client.invitations.guests.import', $invitation) }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="form-label">File Excel / CSV</label>
                    <input type="file" name="guest_file" class="form-input" accept=".xlsx,.xls,.csv,.txt" required>
                    @error('guest_file') <p class="text-xs mt-1" style="color: var(--danger);">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="btn btn-primary w-full text-sm">
                    <i class="fas fa-file-import mr-2"></i> Import Tamu
                </button>
            </form>
            <a href="{{ asset('templates/guest-import-template.csv') }}" class="text-xs font-semibold inline-block mt-3" style="color: var(--accent);" download>
                <i class="fas fa-download mr-1"></i> Download template CSV
            </a>
        </div>

        <div class="card p-6 mt-4">
            <h3 class="font-bold text-base mb-2">Auto Seating Plan</h3>
            <p class="text-xs mb-4" style="color: var(--text-secondary);">Buat pembagian meja dan kursi otomatis untuk seluruh tamu.</p>
            <form method="POST" action="{{ route('client.invitations.guests.auto-seat', $invitation) }}">
                @csrf
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div>
                        <label class="form-label">Kursi per Meja</label>
                        <input type="number" name="seats_per_table" class="form-input" value="8" min="2" max="20" required>
                    </div>
                    <div>
                        <label class="form-label">Meja Awal</label>
                        <input type="number" name="start_table" class="form-input" value="1" min="1" max="999">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-full text-sm">
                    <i class="fas fa-chair mr-2"></i> Generate Seating Plan
                </button>
            </form>
        </div>
        @else
        <div class="card p-6 text-center">
            <i class="fas fa-lock text-2xl mb-3" style="color: var(--text-tertiary);"></i>
            <p class="text-sm font-semibold mb-1">Batas Tamu Tercapai</p>
            <p class="text-xs" style="color: var(--text-secondary);">Upgrade paket untuk menambah lebih banyak tamu.</p>
        </div>
        @endif
    </div>
</div>

<div class="mt-4">
    <a href="{{ route('client.invitations.show', $invitation) }}" class="text-sm font-semibold" style="color: var(--accent);">
        <i class="fas fa-arrow-left mr-1"></i> Kembali ke undangan
    </a>
</div>
@endsection
