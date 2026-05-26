<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class XenditGateway implements PaymentGatewayInterface
{
    private string $apiKey;
    private string $callbackToken;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = (string) (Setting::get('xendit_secret_key', '') ?? '');
        $this->callbackToken = (string) (Setting::get('xendit_callback_token', '') ?? '');
        $this->baseUrl = 'https://api.xendit.co';
    }

    public function code(): string
    {
        return 'xendit';
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    public function supportedChannels(): array
    {
        $channels = [];

        if (Setting::get('payment_allow_qris', '1') === '1') {
            $channels['qris'] = [
                ['code' => 'QRIS', 'name' => 'QRIS'],
            ];
        }

        if (Setting::get('payment_allow_ewallet', '1') === '1') {
            $channels['ewallet'] = [
                ['code' => 'OVO', 'name' => 'OVO'],
                ['code' => 'DANA', 'name' => 'DANA'],
                ['code' => 'SHOPEEPAY', 'name' => 'ShopeePay'],
                ['code' => 'LINKAJA', 'name' => 'LinkAja'],
            ];
        }

        return $channels;
    }

    public function createPaymentIntent(array $payload): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'error' => 'Xendit belum dikonfigurasi.'];
        }

        try {
            $response = Http::withBasicAuth($this->apiKey, '')
                ->post("{$this->baseUrl}/v2/invoices", [
                    'external_id' => $payload['order_id'],
                    'amount' => $payload['amount'],
                    'description' => $payload['description'],
                    'payer_email' => $payload['email'] ?? null,
                    'customer' => [
                        'given_names' => $payload['name'] ?? 'Customer',
                        'email' => $payload['email'] ?? null,
                    ],
                    'success_redirect_url' => $payload['success_url'] ?? null,
                    'failure_redirect_url' => $payload['failure_url'] ?? null,
                    'invoice_duration' => $payload['expiry_seconds'] ?? 86400,
                    'currency' => 'IDR',
                    'payment_methods' => [$payload['channel']],
                ]);

            if (!$response->successful()) {
                Log::error('Xendit createPaymentIntent failed', ['response' => $response->json()]);
                return ['success' => false, 'error' => $response->json('message') ?? 'Request failed'];
            }

            $data = $response->json();

            return [
                'success' => true,
                'data' => $data,
                'payment_url' => $data['invoice_url'] ?? null,
                'gateway_reference' => $data['id'] ?? null,
                'gateway_status' => $data['status'] ?? null,
                'expired_at' => $data['expiry_date'] ?? null,
            ];
        } catch (\Throwable $e) {
            Log::error('Xendit createPaymentIntent exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function verifyWebhook(string $rawBody, array $headers = [], array $payload = []): bool
    {
        $token = $headers['x-callback-token'] ?? $headers['X-CALLBACK-TOKEN'] ?? '';
        if (is_array($token)) {
            $token = $token[0] ?? '';
        }
        $token = (string) $token;
        return !empty($this->callbackToken) && hash_equals($this->callbackToken, $token);
    }

    public function parseWebhook(array $payload): array
    {
        return [
            'event_id' => (string) ($payload['id'] ?? ''),
            'order_id' => (string) ($payload['external_id'] ?? ''),
            'gateway_reference' => (string) ($payload['id'] ?? ''),
            'status' => strtoupper((string) ($payload['status'] ?? '')),
            'amount' => (int) ($payload['paid_amount'] ?? $payload['amount'] ?? 0),
            'raw' => $payload,
        ];
    }

    public function queryPaymentStatus(string $gatewayReference): array
    {
        if (!$this->isConfigured() || $gatewayReference === '') {
            return ['success' => false, 'error' => 'Gateway reference tidak valid.'];
        }

        try {
            $response = Http::withBasicAuth($this->apiKey, '')
                ->get("{$this->baseUrl}/v2/invoices/{$gatewayReference}");

            if (!$response->successful()) {
                return ['success' => false, 'error' => $response->json('message') ?? 'Query failed'];
            }

            $data = $response->json();

            return [
                'success' => true,
                'status' => strtoupper((string) ($data['status'] ?? '')),
                'amount' => (int) ($data['paid_amount'] ?? $data['amount'] ?? 0),
                'data' => $data,
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
