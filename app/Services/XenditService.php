<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class XenditService
{
    private string $apiKey;
    private string $baseUrl;
    private string $callbackToken;

    public function __construct()
    {
        $this->apiKey = Setting::get('xendit_secret_key', '');
        $this->callbackToken = Setting::get('xendit_callback_token', '');
        $mode = Setting::get('xendit_mode', 'sandbox');
        $this->baseUrl = 'https://api.xendit.co';
    }

    /**
     * Create an invoice (payment request)
     */
    public function createInvoice(array $data): array
    {
        try {
            $response = Http::withBasicAuth($this->apiKey, '')
                ->post("{$this->baseUrl}/v2/invoices", [
                    'external_id' => $data['external_id'],
                    'amount' => $data['amount'],
                    'description' => $data['description'],
                    'payer_email' => $data['email'] ?? null,
                    'customer' => [
                        'given_names' => $data['name'] ?? 'Customer',
                        'email' => $data['email'] ?? null,
                    ],
                    'success_redirect_url' => $data['success_url'] ?? null,
                    'failure_redirect_url' => $data['failure_url'] ?? null,
                    'invoice_duration' => 86400, // 24 hours
                    'currency' => 'IDR',
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                    'payment_url' => $response->json('invoice_url'),
                    'reference' => $response->json('id'),
                    'expired_at' => $response->json('expiry_date'),
                ];
            }

            Log::error('Xendit createInvoice failed', ['response' => $response->json()]);
            return ['success' => false, 'error' => $response->json('message') ?? 'Request failed'];
        } catch (\Exception $e) {
            Log::error('Xendit exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Get available payment channels
     */
    public function getPaymentChannels(): array
    {
        return [
            ['code' => 'BCA', 'name' => 'BCA Virtual Account', 'group' => 'Virtual Account', 'icon' => 'fas fa-university'],
            ['code' => 'BNI', 'name' => 'BNI Virtual Account', 'group' => 'Virtual Account', 'icon' => 'fas fa-university'],
            ['code' => 'BRI', 'name' => 'BRI Virtual Account', 'group' => 'Virtual Account', 'icon' => 'fas fa-university'],
            ['code' => 'MANDIRI', 'name' => 'Mandiri Virtual Account', 'group' => 'Virtual Account', 'icon' => 'fas fa-university'],
            ['code' => 'QRIS', 'name' => 'QRIS', 'group' => 'E-Wallet', 'icon' => 'fas fa-qrcode'],
            ['code' => 'OVO', 'name' => 'OVO', 'group' => 'E-Wallet', 'icon' => 'fas fa-wallet'],
            ['code' => 'DANA', 'name' => 'DANA', 'group' => 'E-Wallet', 'icon' => 'fas fa-wallet'],
            ['code' => 'SHOPEEPAY', 'name' => 'ShopeePay', 'group' => 'E-Wallet', 'icon' => 'fas fa-wallet'],
        ];
    }

    /**
     * Verify callback signature
     */
    public function verifyCallback(string $callbackToken): bool
    {
        return $this->callbackToken && $this->callbackToken === $callbackToken;
    }

    /**
     * Test API connection
     */
    public function testConnection(): array
    {
        try {
            $response = Http::withBasicAuth($this->apiKey, '')
                ->get("{$this->baseUrl}/balance");

            if ($response->successful()) {
                return ['success' => true, 'balance' => $response->json('balance')];
            }

            return ['success' => false, 'error' => $response->json('message') ?? 'Connection failed'];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }
}
