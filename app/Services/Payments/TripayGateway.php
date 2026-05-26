<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TripayGateway implements PaymentGatewayInterface
{
    private string $apiKey;
    private string $privateKey;
    private string $merchantCode;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = (string) (Setting::get('tripay_api_key', '') ?? '');
        $this->privateKey = (string) (Setting::get('tripay_private_key', '') ?? '');
        $this->merchantCode = (string) (Setting::get('tripay_merchant_code', '') ?? '');
        $mode = (string) (Setting::get('tripay_mode', 'sandbox') ?? 'sandbox');
        $this->baseUrl = $mode === 'production'
            ? 'https://tripay.co.id/api'
            : 'https://tripay.co.id/api-sandbox';
    }

    public function code(): string
    {
        return 'tripay';
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey) && !empty($this->privateKey) && !empty($this->merchantCode);
    }

    public function supportedChannels(): array
    {
        $allowQris = Setting::get('payment_allow_qris', '1') === '1';
        $allowEwallet = Setting::get('payment_allow_ewallet', '1') === '1';
        $channels = collect($this->getPaymentChannels()['channels'] ?? []);

        return [
            'qris' => $allowQris
                ? $channels->filter(fn ($c) => str_contains(strtoupper((string) ($c['code'] ?? '')), 'QRIS'))
                    ->map(fn ($c) => ['code' => $c['code'], 'name' => $c['name'] ?? $c['code']])
                    ->values()
                    ->all()
                : [],
            'ewallet' => $allowEwallet
                ? $channels->filter(function ($c) {
                    $code = strtoupper((string) ($c['code'] ?? ''));
                    $group = strtoupper((string) ($c['group'] ?? ''));
                    return !str_contains($code, 'QRIS')
                        && (str_contains($group, 'EWALLET') || str_contains($group, 'E-WALLET') || in_array($code, ['OVO', 'DANA', 'SHOPEEPAY', 'LINKAJA'], true));
                })
                    ->map(fn ($c) => ['code' => $c['code'], 'name' => $c['name'] ?? $c['code']])
                    ->values()
                    ->all()
                : [],
        ];
    }

    public function createPaymentIntent(array $payload): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'error' => 'Tripay belum dikonfigurasi.'];
        }

        try {
            $merchantRef = $payload['order_id'];
            $amount = (int) $payload['amount'];
            $signature = hash_hmac('sha256', $this->merchantCode . $merchantRef . $amount, $this->privateKey);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->post("{$this->baseUrl}/transaction/create", [
                'method' => $payload['channel'],
                'merchant_ref' => $merchantRef,
                'amount' => $amount,
                'customer_name' => $payload['name'] ?? 'Customer',
                'customer_email' => $payload['email'] ?? '',
                'customer_phone' => $payload['phone'] ?? '',
                'order_items' => [[
                    'name' => $payload['item_name'] ?? 'Paket Undangan',
                    'price' => $amount,
                    'quantity' => 1,
                ]],
                'callback_url' => $payload['callback_url'] ?? null,
                'return_url' => $payload['return_url'] ?? null,
                'expired_time' => now()->addSeconds((int) ($payload['expiry_seconds'] ?? 86400))->timestamp,
                'signature' => $signature,
            ]);

            if (!$response->successful() || !$response->json('success')) {
                Log::error('Tripay createPaymentIntent failed', ['response' => $response->json()]);
                return ['success' => false, 'error' => $response->json('message') ?? 'Request failed'];
            }

            $data = $response->json('data');

            return [
                'success' => true,
                'data' => $data,
                'payment_url' => $data['checkout_url'] ?? null,
                'gateway_reference' => $data['reference'] ?? null,
                'gateway_status' => $data['status'] ?? null,
                'expired_at' => isset($data['expired_time']) ? date('Y-m-d H:i:s', (int) $data['expired_time']) : null,
            ];
        } catch (\Throwable $e) {
            Log::error('Tripay createPaymentIntent exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function verifyWebhook(string $rawBody, array $headers = [], array $payload = []): bool
    {
        $signature = $headers['x-callback-signature'] ?? $headers['X-CALLBACK-SIGNATURE'] ?? '';
        if (is_array($signature)) {
            $signature = $signature[0] ?? '';
        }
        $signature = (string) $signature;
        if ($signature === '') {
            return false;
        }

        return hash_equals(hash_hmac('sha256', $rawBody, $this->privateKey), $signature);
    }

    public function parseWebhook(array $payload): array
    {
        return [
            'event_id' => (string) ($payload['reference'] ?? ''),
            'order_id' => (string) ($payload['merchant_ref'] ?? ''),
            'gateway_reference' => (string) ($payload['reference'] ?? ''),
            'status' => strtoupper((string) ($payload['status'] ?? '')),
            'amount' => (int) ($payload['total_amount'] ?? $payload['amount'] ?? 0),
            'raw' => $payload,
        ];
    }

    public function queryPaymentStatus(string $gatewayReference): array
    {
        return ['success' => false, 'error' => 'Tripay status sync belum diaktifkan untuk production v1.'];
    }

    public function getPaymentChannels(): array
    {
        if (!$this->isConfigured()) {
            return ['success' => true, 'channels' => []];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get("{$this->baseUrl}/merchant/payment-channel");

            if ($response->successful() && $response->json('success')) {
                return ['success' => true, 'channels' => $response->json('data')];
            }
        } catch (\Throwable $e) {
            Log::warning('Tripay getPaymentChannels failed', ['error' => $e->getMessage()]);
        }

        return ['success' => true, 'channels' => []];
    }
}
