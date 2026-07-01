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
            $whatsappPhone = preg_replace('/\D+/', '', (string) $guest->phone);

            if ($whatsappPhone && str_starts_with($whatsappPhone, '0')) {
                $whatsappPhone = '62' . substr($whatsappPhone, 1);
            }

            if ($whatsappPhone && str_starts_with($whatsappPhone, '8')) {
                $whatsappPhone = '62' . $whatsappPhone;
            }

            $whatsappUrl = $whatsappPhone
                ? "https://wa.me/{$whatsappPhone}?text={$shareText}"
                : "https://wa.me/?text={$shareText}";
        @endphp
        <a href="{{ $whatsappUrl }}" target="_blank"
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
    <p class="text-sm">
        {{ !empty($search) ? 'Tidak ada tamu yang cocok dengan pencarian.' : 'Belum ada tamu. Tambahkan dari form di samping.' }}
    </p>
</div>
@endforelse
