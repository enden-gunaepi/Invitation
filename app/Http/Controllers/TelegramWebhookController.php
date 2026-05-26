<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TelegramWebhookController extends Controller
{
    public function handle(Request $request)
    {
        if (Setting::get('telegram_enabled', '0') !== '1') {
            return response()->json(['ok' => true]);
        }

        $update  = $request->all();
        $message = $update['message'] ?? null;

        if (!$message || empty($message['text'])) {
            return response()->json(['ok' => true]);
        }

        $text   = trim($message['text']);
        $chatId = (string) $message['chat']['id'];

        $allowedRaw = (string) (Setting::get('telegram_chat_id', '') ?? '');
        $allowedIds = array_filter(array_map('trim', explode(',', $allowedRaw)));

        if (!str_starts_with($text, '/')) {
            return response()->json(['ok' => true]);
        }

        // /chatid boleh diakses siapa saja (tidak perlu authorize)
        $parts   = preg_split('/\s+/', $text);
        $command = strtolower(preg_replace('/@.*$/', '', $parts[0]));

        if ($command === '/chatid') {
            $this->commandChatId($chatId, $message);
            return response()->json(['ok' => true]);
        }

        // Command lain hanya untuk chat ID yang terdaftar
        if (!empty($allowedIds) && !in_array($chatId, $allowedIds, true)) {
            Log::warning('Telegram webhook: unauthorized chat_id', ['chat_id' => $chatId]);
            $this->reply($chatId, "🚫 <b>Akses Dilarang</b>\n\nChat ID <code>{$chatId}</code> tidak terdaftar sebagai pengguna yang diizinkan.\n\nHubungi administrator untuk mendapatkan akses.");
            return response()->json(['ok' => true]);
        }

        $this->handleCommand($text, $chatId, $message);

        return response()->json(['ok' => true]);
    }

    protected function handleCommand(string $text, string $chatId, array $message): void
    {
        $parts   = preg_split('/\s+/', $text);
        $command = strtolower($parts[0]);
        $command = preg_replace('/@.*$/', '', $command);

        match ($command) {
            '/topup'           => $this->commandTopup($parts, $chatId),
            '/balance',
            '/saldo'           => $this->commandBalance($parts, $chatId),
            '/user'            => $this->commandUser($parts, $chatId),
            '/stats',
            '/statistik'       => $this->commandStats($chatId),
            '/chatid'          => $this->commandChatId($chatId, $message),
            '/help',
            '/bantuan'         => $this->commandHelp($chatId),
            default            => $this->commandUnknown($chatId),
        };
    }

    // ── /topup email jumlah ──────────────────────────────────────────────────

    protected function commandTopup(array $parts, string $chatId): void
    {
        if (count($parts) < 3) {
            $this->reply($chatId, "⚠️ <b>Format salah</b>\n\nGunakan: <code>/topup email@example.com 50000</code>");
            return;
        }

        $email  = $parts[1];
        $amount = (int) preg_replace('/[^0-9]/', '', $parts[2]);

        if ($amount <= 0) {
            $this->reply($chatId, "⚠️ Jumlah topup harus lebih dari 0.");
            return;
        }

        $user = User::where('email', $email)->first();
        if (!$user) {
            $this->reply($chatId, "❌ User <code>{$this->e($email)}</code> tidak ditemukan.");
            return;
        }

        DB::transaction(fn () => $user->increment('balance', $amount));
        $user->refresh();

        $this->reply($chatId,
            "✅ <b>Topup Berhasil!</b>\n\n"
            . "👤 {$this->e($user->name)}\n"
            . "📧 {$this->e($user->email)}\n"
            . "💰 Ditambah: Rp" . $this->rp($amount) . "\n"
            . "💳 Saldo kini: Rp" . $this->rp($user->balance)
        );
    }

    // ── /saldo email ─────────────────────────────────────────────────────────

    protected function commandBalance(array $parts, string $chatId): void
    {
        if (count($parts) < 2) {
            $this->reply($chatId, "⚠️ <b>Format salah</b>\n\nGunakan: <code>/saldo email@example.com</code>");
            return;
        }

        $user = User::where('email', $parts[1])->first();
        if (!$user) {
            $this->reply($chatId, "❌ User <code>{$this->e($parts[1])}</code> tidak ditemukan.");
            return;
        }

        $this->reply($chatId,
            "💳 <b>Info Saldo</b>\n\n"
            . "👤 {$this->e($user->name)}\n"
            . "📧 {$this->e($user->email)}\n"
            . "💰 Saldo: Rp" . $this->rp($user->balance)
        );
    }

    // ── /user email ──────────────────────────────────────────────────────────

    protected function commandUser(array $parts, string $chatId): void
    {
        if (count($parts) < 2) {
            $this->reply($chatId, "⚠️ <b>Format salah</b>\n\nGunakan: <code>/user email@example.com</code>");
            return;
        }

        $user = User::where('email', $parts[1])->first();
        if (!$user) {
            $this->reply($chatId, "❌ User <code>{$this->e($parts[1])}</code> tidak ditemukan.");
            return;
        }

        $invCount  = $user->invitations()->count();
        $paidCount = $user->payments()->where('payment_status', 'paid')->count();
        $status    = $user->is_active ? '✅ Aktif' : '🔴 Non-aktif';
        $role      = $user->role === 'admin' ? '🛡️ Admin' : '👤 Client';
        $joined    = $user->created_at->format('d M Y');

        $this->reply($chatId,
            "👤 <b>Info User</b>\n\n"
            . "Nama     : {$this->e($user->name)}\n"
            . "Email    : <code>{$this->e($user->email)}</code>\n"
            . "Role     : {$role}\n"
            . "Status   : {$status}\n"
            . "💰 Saldo : Rp" . $this->rp($user->balance) . "\n"
            . "📨 Undangan : {$invCount}\n"
            . "💳 Pembayaran lunas : {$paidCount}\n"
            . "📅 Bergabung : {$joined}"
        );
    }

    // ── /stats ───────────────────────────────────────────────────────────────

    protected function commandStats(string $chatId): void
    {
        $totalUsers       = User::where('role', 'client')->count();
        $activeUsers      = User::where('role', 'client')->where('is_active', true)->count();
        $totalInvitations = Invitation::count();
        $pendingPayments  = Payment::where('payment_status', 'pending')->count();
        $todayRevenue     = Payment::where('payment_status', 'paid')
            ->whereDate('updated_at', today())
            ->sum('amount');
        $monthRevenue     = Payment::where('payment_status', 'paid')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->sum('amount');
        $totalRevenue     = Payment::where('payment_status', 'paid')->sum('amount');

        $this->reply($chatId,
            "📊 <b>Statistik Sistem</b>\n"
            . "─────────────────────\n"
            . "👥 Users      : {$totalUsers} (<b>{$activeUsers}</b> aktif)\n"
            . "📨 Undangan   : {$totalInvitations}\n"
            . "⏳ Pembayaran pending : {$pendingPayments}\n\n"
            . "💰 <b>Pendapatan</b>\n"
            . "  Hari ini  : Rp" . $this->rp($todayRevenue) . "\n"
            . "  Bulan ini : Rp" . $this->rp($monthRevenue) . "\n"
            . "  Total     : Rp" . $this->rp($totalRevenue)
        );
    }

    // ── /chatid ──────────────────────────────────────────────────────────────

    protected function commandChatId(string $chatId, array $message): void
    {
        $chat = $message['chat'] ?? [];
        $from = $message['from'] ?? [];

        $lines = ["📋 <b>Info Chat</b>\n"];

        $lines[] = "<b>Chat:</b>";
        $lines[] = "  ID   : <code>{$chatId}</code>";
        $lines[] = "  Type : " . $this->e($chat['type'] ?? '-');
        if (!empty($chat['title']))    $lines[] = "  Title: " . $this->e($chat['title']);
        if (!empty($chat['username'])) $lines[] = "  User : @" . $this->e($chat['username']);

        $lines[] = "\n<b>Pengirim:</b>";
        $lines[] = "  ID   : <code>" . ($from['id'] ?? '-') . "</code>";
        $lines[] = "  Nama : " . $this->e(trim(($from['first_name'] ?? '') . ' ' . ($from['last_name'] ?? '')));
        if (!empty($from['username'])) $lines[] = "  User : @" . $this->e($from['username']);
        $lines[] = "  Bot  : " . (($from['is_bot'] ?? false) ? 'Ya' : 'Tidak');
        if (!empty($from['language_code'])) $lines[] = "  Lang : " . $this->e($from['language_code']);

        $this->reply($chatId, implode("\n", $lines));
    }

    // ── /help ────────────────────────────────────────────────────────────────

    protected function commandHelp(string $chatId): void
    {
        $this->reply($chatId,
            "📖 <b>Daftar Command</b>\n"
            . "─────────────────────\n"
            . "<code>/topup email jumlah</code>\n"
            . "  Tambah saldo user\n\n"
            . "<code>/saldo email</code>\n"
            . "  Cek saldo user\n\n"
            . "<code>/user email</code>\n"
            . "  Info lengkap user\n\n"
            . "<code>/stats</code>\n"
            . "  Statistik sistem (users, undangan, revenue)\n\n"
            . "<code>/chatid</code>\n"
            . "  Info chat & pengirim saat ini\n\n"
            . "<code>/help</code>\n"
            . "  Tampilkan bantuan ini"
        );
    }

    // ── Unknown ──────────────────────────────────────────────────────────────

    protected function commandUnknown(string $chatId): void
    {
        $this->reply($chatId, "❓ Command tidak dikenal. Ketik <code>/help</code> untuk bantuan.");
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    protected function reply(string $chatId, string $text): void
    {
        (new TelegramService())->sendMessage($text, $chatId);
    }

    protected function e(string $text): string
    {
        return htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    protected function rp(float|int $amount): string
    {
        return number_format($amount, 0, ',', '.');
    }
}
