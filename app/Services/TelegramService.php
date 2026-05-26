<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected string $botToken;
    protected string $chatIds;
    protected string $notifyChatIds;
    protected string $baseUrl = 'https://api.telegram.org';

    public function __construct()
    {
        $this->botToken      = (string) Setting::get('telegram_bot_token', '');
        $this->chatIds       = (string) Setting::get('telegram_chat_id', '');
        $this->notifyChatIds = (string) Setting::get('telegram_notify_chat_id', '');
    }

    public function isConfigured(): bool
    {
        return !empty($this->botToken) && !empty($this->chatIds);
    }

    /** Chat ID untuk command (authorize list). */
    public function getAllChatIds(): array
    {
        return array_filter(array_map('trim', explode(',', $this->chatIds)));
    }

    /** Chat ID pertama dari authorize list. */
    public function primaryChatId(): string
    {
        return $this->getAllChatIds()[0] ?? '';
    }

    public function sendMessage(string $text, ?string $chatId = null): array
    {
        if (empty($this->botToken)) {
            return ['success' => false, 'error' => 'Bot token belum dikonfigurasi.'];
        }

        $targetChatId = $chatId ?? $this->primaryChatId();
        if (empty($targetChatId)) {
            return ['success' => false, 'error' => 'Chat ID belum dikonfigurasi.'];
        }

        try {
            $response = Http::timeout(10)->post("{$this->baseUrl}/bot{$this->botToken}/sendMessage", [
                'chat_id'    => $targetChatId,
                'text'       => $text,
                'parse_mode' => 'HTML',
            ]);

            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json()];
            }

            return ['success' => false, 'error' => $response->json('description', 'Unknown error')];
        } catch (\Throwable $e) {
            Log::error('Telegram sendMessage failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /** Chat ID tujuan notifikasi (notify group). Fallback ke authorize list jika kosong. */
    public function getNotifyChatIds(): array
    {
        $ids = array_filter(array_map('trim', explode(',', $this->notifyChatIds)));
        return !empty($ids) ? $ids : $this->getAllChatIds();
    }

    /** Broadcast notifikasi ke semua notify chat ID. */
    public function broadcast(string $text): array
    {
        $ids = $this->getNotifyChatIds();
        if (empty($ids)) {
            return ['success' => false, 'error' => 'Notify Chat ID belum dikonfigurasi.'];
        }

        $errors = [];
        foreach ($ids as $id) {
            $result = $this->sendMessage($text, $id);
            if (!$result['success']) {
                $errors[] = "ID {$id}: " . ($result['error'] ?? 'error');
            }
        }

        return empty($errors)
            ? ['success' => true]
            : ['success' => false, 'error' => implode('; ', $errors)];
    }

    public function setWebhook(string $url): array
    {
        if (empty($this->botToken)) {
            return ['success' => false, 'error' => 'Bot token belum dikonfigurasi.'];
        }

        try {
            $response = Http::timeout(10)->post("{$this->baseUrl}/bot{$this->botToken}/setWebhook", [
                'url' => $url,
                'allowed_updates' => ['message'],
            ]);

            if ($response->successful() && $response->json('ok')) {
                return ['success' => true, 'data' => $response->json()];
            }

            return ['success' => false, 'error' => $response->json('description', 'Unknown error')];
        } catch (\Throwable $e) {
            Log::error('Telegram setWebhook failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function deleteWebhook(): array
    {
        if (empty($this->botToken)) {
            return ['success' => false, 'error' => 'Bot token belum dikonfigurasi.'];
        }

        try {
            $response = Http::timeout(10)->post("{$this->baseUrl}/bot{$this->botToken}/deleteWebhook");

            if ($response->successful() && $response->json('ok')) {
                return ['success' => true];
            }

            return ['success' => false, 'error' => $response->json('description', 'Unknown error')];
        } catch (\Throwable $e) {
            Log::error('Telegram deleteWebhook failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function getWebhookInfo(): array
    {
        if (empty($this->botToken)) {
            return ['success' => false, 'error' => 'Bot token belum dikonfigurasi.'];
        }

        try {
            $response = Http::timeout(10)->get("{$this->baseUrl}/bot{$this->botToken}/getWebhookInfo");

            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json('result')];
            }

            return ['success' => false, 'error' => $response->json('description', 'Unknown error')];
        } catch (\Throwable $e) {
            Log::error('Telegram getWebhookInfo failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function testConnection(): array
    {
        if (empty($this->botToken)) {
            return ['success' => false, 'error' => 'Bot token belum dikonfigurasi.'];
        }

        try {
            $response = Http::timeout(10)->get("{$this->baseUrl}/bot{$this->botToken}/getMe");

            if ($response->successful() && $response->json('ok')) {
                $bot = $response->json('result');
                return [
                    'success' => true,
                    'bot_name' => $bot['first_name'] ?? '',
                    'bot_username' => $bot['username'] ?? '',
                ];
            }

            return ['success' => false, 'error' => $response->json('description', 'Token tidak valid')];
        } catch (\Throwable $e) {
            Log::error('Telegram testConnection failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
