<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeaGateService
{
    protected string $token;
    protected string $domainApi;
    protected bool $instan;

    public function __construct()
    {
        $this->token = Setting::get('whatsapp_weagate_token', '');
        $this->domainApi = rtrim(Setting::get('whatsapp_weagate_domain_api', 'https://mywifi.weagate.com'), '/');
        $this->instan = Setting::get('whatsapp_weagate_instan', '1') === '1';
    }

    public function isConfigured(): bool
    {
        return !empty($this->token) && !empty($this->domainApi);
    }

    public function sendMessage(string $to, string $message, ?bool $instan = null): array
    {
        if (empty($this->token)) {
            return ['success' => false, 'error' => 'Token belum dikonfigurasi.'];
        }

        try {
            $response = Http::acceptJson()->timeout(15)->post("{$this->domainApi}/api/send-message", [
                'token'   => $this->token,
                'to'      => $to,
                'type'    => 'text',
                'message' => $message,
                'instan'  => ($instan ?? $this->instan) ? 1 : 0,
            ]);

            if ($response->successful()) {
                $body = $response->json();
                return ['success' => true, 'data' => $body];
            }

            $errorBody = $response->json();
            $errorMessage = $errorBody['message'] ?? $errorBody['error'] ?? 'Unknown error';

            return ['success' => false, 'error' => $errorMessage, 'status' => $response->status()];
        } catch (\Throwable $e) {
            Log::error('WeaGate send-message gagal', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function testConnection(): array
    {
        if (empty($this->token)) {
            return ['success' => false, 'error' => 'Token belum dikonfigurasi.'];
        }

        try {
            $response = Http::acceptJson()->timeout(10)->post("{$this->domainApi}/api/device-status", [
                'token' => $this->token,
            ]);

            if (!$response->ok()) {
                return ['success' => false, 'error' => 'Token tidak valid atau akses ditolak. (HTTP ' . $response->status() . ')'];
            }

            $data = $response->json('data', []);

            return [
                'success' => true,
                'domain' => $this->domainApi,
                'device_name' => $data['name'] ?? '-',
                'package' => $data['package_name'] ?? '-',
                'device_status' => $data['device_status'] ?? 'unknown',
                'expired_at' => $data['expired_at'] ?? null,
            ];
        } catch (\Throwable $e) {
            Log::error('WeaGate testConnection gagal', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
