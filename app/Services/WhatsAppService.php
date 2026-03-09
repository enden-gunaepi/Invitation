<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    public function sendText(string $toPhone, string $message): array
    {
        $mode = strtolower((string) Setting::get('whatsapp_mode', 'mock'));
        $normalizedPhone = $this->normalizePhone($toPhone);

        if (empty($normalizedPhone)) {
            return ['success' => false, 'error' => 'Nomor WhatsApp tidak valid.'];
        }

        if ($mode !== 'live') {
            return [
                'success' => true,
                'message_id' => 'MOCK-WA-' . time(),
                'response' => ['mode' => 'mock', 'to' => $normalizedPhone],
            ];
        }

        $phoneNumberId = (string) Setting::get('whatsapp_phone_number_id', '');
        $apiToken = (string) Setting::get('whatsapp_api_token', '');
        $apiVersion = (string) Setting::get('whatsapp_api_version', 'v20.0');
        $baseUrl = rtrim((string) Setting::get('whatsapp_base_url', 'https://graph.facebook.com'), '/');

        if ($phoneNumberId === '' || $apiToken === '') {
            return ['success' => false, 'error' => 'WhatsApp API belum dikonfigurasi lengkap.'];
        }

        $endpoint = "{$baseUrl}/{$apiVersion}/{$phoneNumberId}/messages";
        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $normalizedPhone,
            'type' => 'text',
            'text' => ['body' => $message],
        ];

        $response = Http::withToken($apiToken)->timeout(20)->post($endpoint, $payload);
        $json = $response->json() ?? [];

        if (!$response->successful()) {
            return [
                'success' => false,
                'error' => (string) data_get($json, 'error.message', 'Request WhatsApp API gagal.'),
                'response' => $json,
            ];
        }

        return [
            'success' => true,
            'message_id' => (string) data_get($json, 'messages.0.id', ''),
            'response' => $json,
        ];
    }

    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9+]/', '', trim($phone)) ?? '';
        if ($phone === '') {
            return '';
        }

        if (str_starts_with($phone, '+')) {
            return ltrim($phone, '+');
        }

        if (str_starts_with($phone, '0')) {
            return '62' . substr($phone, 1);
        }

        if (!str_starts_with($phone, '62')) {
            return '62' . $phone;
        }

        return $phone;
    }
}
