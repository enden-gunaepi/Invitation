<?php

namespace App\Http\Controllers;

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

        $update = $request->all();
        $message = $update['message'] ?? null;

        if (!$message || empty($message['text'])) {
            return response()->json(['ok' => true]);
        }

        $text = trim($message['text']);
        $chatId = (string) $message['chat']['id'];
        $allowedChatId = Setting::get('telegram_chat_id', '');

        // Only process commands from the configured chat
        if ($allowedChatId && $chatId !== $allowedChatId) {
            Log::warning('Telegram webhook: unauthorized chat_id', ['chat_id' => $chatId]);
            return response()->json(['ok' => true]);
        }

        // Parse command
        if (str_starts_with($text, '/')) {
            $this->handleCommand($text, $chatId, $message);
        }

        return response()->json(['ok' => true]);
    }

    protected function handleCommand(string $text, string $chatId, array $message): void
    {
        $parts = preg_split('/\s+/', $text);
        $command = strtolower($parts[0]);

        // Strip @botname from command (e.g., /topup@mybot)
        $command = preg_replace('/@.*$/', '', $command);

        match ($command) {
            '/topup' => $this->commandTopup($parts, $chatId),
            '/balance', '/saldo' => $this->commandBalance($parts, $chatId),
            '/chatid' => $this->commandChatId($chatId, $message),
            '/help' => $this->commandHelp($chatId),
            default => $this->commandUnknown($chatId),
        };
    }

    protected function commandTopup(array $parts, string $chatId): void
    {
        // Format: /topup <email> <amount>
        if (count($parts) < 3) {
            $this->reply($chatId, "⚠️ <b>Format salah</b>\n\nGunakan: <code>/topup email@example.com 50000</code>");
            return;
        }

        $email = $parts[1];
        $amount = (int) preg_replace('/[^0-9]/', '', $parts[2]);

        if ($amount <= 0) {
            $this->reply($chatId, "⚠️ Jumlah topup harus lebih dari 0.");
            return;
        }

        $user = User::where('email', $email)->first();
        if (!$user) {
            $this->reply($chatId, "❌ User dengan email <code>{$this->escape($email)}</code> tidak ditemukan.");
            return;
        }

        DB::transaction(function () use ($user, $amount) {
            $user->increment('balance', $amount);
        });

        $user->refresh();
        $formattedAmount = number_format($amount, 0, ',', '.');
        $formattedBalance = number_format($user->balance, 0, ',', '.');

        $this->reply($chatId, "✅ <b>Topup Berhasil!</b>\n\n👤 {$this->escape($user->name)}\n📧 {$this->escape($user->email)}\n💰 Topup: Rp{$formattedAmount}\n💳 Saldo: Rp{$formattedBalance}");
    }

    protected function commandBalance(array $parts, string $chatId): void
    {
        // Format: /balance <email> or /saldo <email>
        if (count($parts) < 2) {
            $this->reply($chatId, "⚠️ <b>Format salah</b>\n\nGunakan: <code>/saldo email@example.com</code>");
            return;
        }

        $email = $parts[1];
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->reply($chatId, "❌ User dengan email <code>{$this->escape($email)}</code> tidak ditemukan.");
            return;
        }

        $formattedBalance = number_format($user->balance, 0, ',', '.');
        $this->reply($chatId, "💳 <b>Info Saldo</b>\n\n👤 {$this->escape($user->name)}\n📧 {$this->escape($user->email)}\n💰 Saldo: Rp{$formattedBalance}");
    }

    protected function commandChatId(string $chatId, array $message): void
    {
        $chat = $message['chat'] ?? [];
        $from = $message['from'] ?? [];

        $lines = ["📋 <b>Info Chat</b>\n"];

        // Chat info
        $lines[] = "<b>Chat:</b>";
        $lines[] = "  ID: <code>{$chatId}</code>";
        $lines[] = "  Type: " . $this->escape($chat['type'] ?? '-');
        if (!empty($chat['title'])) {
            $lines[] = "  Title: " . $this->escape($chat['title']);
        }
        if (!empty($chat['username'])) {
            $lines[] = "  Username: @" . $this->escape($chat['username']);
        }

        // Sender info
        $lines[] = "\n<b>Pengirim:</b>";
        $lines[] = "  ID: <code>" . ($from['id'] ?? '-') . "</code>";
        $lines[] = "  Nama: " . $this->escape(trim(($from['first_name'] ?? '') . ' ' . ($from['last_name'] ?? '')));
        if (!empty($from['username'])) {
            $lines[] = "  Username: @" . $this->escape($from['username']);
        }
        $lines[] = "  Bot: " . (($from['is_bot'] ?? false) ? 'Ya' : 'Tidak');
        if (!empty($from['language_code'])) {
            $lines[] = "  Bahasa: " . $this->escape($from['language_code']);
        }

        $this->reply($chatId, implode("\n", $lines));
    }

    protected function commandHelp(string $chatId): void
    {
        $this->reply($chatId, "📖 <b>Daftar Command</b>\n\n"
            . "<code>/topup email jumlah</code> — Topup saldo user\n"
            . "<code>/saldo email</code> — Cek saldo user\n"
            . "<code>/chatid</code> — Info detail chat & pengirim\n"
            . "<code>/help</code> — Tampilkan bantuan");
    }

    protected function commandUnknown(string $chatId): void
    {
        $this->reply($chatId, "❓ Command tidak dikenal. Ketik <code>/help</code> untuk bantuan.");
    }

    protected function reply(string $chatId, string $text): void
    {
        $service = new TelegramService();
        $service->sendMessage($text, $chatId);
    }

    protected function escape(string $text): string
    {
        return htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}
