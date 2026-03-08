<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TripayService
{
    private string $apiKey;
    private string $privateKey;
    private string $merchantCode;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = Setting::get('tripay_api_key', '');
        $this->privateKey = Setting::get('tripay_private_key', '');
        $this->merchantCode = Setting::get('tripay_merchant_code', '');
        $mode = Setting::get('tripay_mode', 'sandbox');
        $this->baseUrl = $mode === 'production'
            ? 'https://tripay.co.id/api'
            : 'https://tripay.co.id/api-sandbox';
    }

    /**
     * Create a transaction
     */
    public function createTransaction(array $data): array
    {
        try {
            $merchantRef = $data['merchant_ref'];
            $amount = (int) $data['amount'];

            $signature = hash_hmac('sha256', $this->merchantCode . $merchantRef . $amount, $this->privateKey);

            $payload = [
                'method' => $data['method'],
                'merchant_ref' => $merchantRef,
                'amount' => $amount,
                'customer_name' => $data['name'] ?? 'Customer',
                'customer_email' => $data['email'] ?? '',
                'customer_phone' => $data['phone'] ?? '',
                'order_items' => [
                    [
                        'name' => $data['item_name'] ?? 'Paket Undangan',
                        'price' => $amount,
                        'quantity' => 1,
                    ],
                ],
                'callback_url' => $data['callback_url'] ?? null,
                'return_url' => $data['return_url'] ?? null,
                'expired_time' => (int) (now()->addHours(24)->timestamp),
                'signature' => $signature,
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->post("{$this->baseUrl}/transaction/create", $payload);

            if ($response->successful() && $response->json('success')) {
                $resData = $response->json('data');
                return [
                    'success' => true,
                    'data' => $resData,
                    'payment_url' => $resData['checkout_url'] ?? null,
                    'reference' => $resData['reference'] ?? null,
                    'expired_at' => isset($resData['expired_time']) ? date('Y-m-d H:i:s', $resData['expired_time']) : null,
                ];
            }

            Log::error('Tripay createTransaction failed', ['response' => $response->json()]);
            return ['success' => false, 'error' => $response->json('message') ?? 'Request failed'];
        } catch (\Exception $e) {
            Log::error('Tripay exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Get available payment channels
     */
    public function getPaymentChannels(): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get("{$this->baseUrl}/merchant/payment-channel");

            if ($response->successful() && $response->json('success')) {
                return ['success' => true, 'channels' => $response->json('data')];
            }

            // Fallback static channels
            return ['success' => true, 'channels' => $this->staticChannels()];
        } catch (\Exception $e) {
            return ['success' => true, 'channels' => $this->staticChannels()];
        }
    }

    private function staticChannels(): array
    {
        return [
            ['code' => 'BRIVA', 'name' => 'BRI Virtual Account', 'group' => 'Virtual Account', 'icon_url' => null],
            ['code' => 'BNIVA', 'name' => 'BNI Virtual Account', 'group' => 'Virtual Account', 'icon_url' => null],
            ['code' => 'MANDIRIVA', 'name' => 'Mandiri Virtual Account', 'group' => 'Virtual Account', 'icon_url' => null],
            ['code' => 'BCAVA', 'name' => 'BCA Virtual Account', 'group' => 'Virtual Account', 'icon_url' => null],
            ['code' => 'QRIS', 'name' => 'QRIS (All E-Wallet)', 'group' => 'E-Wallet', 'icon_url' => null],
            ['code' => 'QRISC', 'name' => 'QRIS (Customizable)', 'group' => 'E-Wallet', 'icon_url' => null],
        ];
    }

    /**
     * Verify callback signature from Tripay
     */
    public function verifyCallback(string $jsonBody): bool
    {
        $callbackSignature = request()->header('X-Callback-Signature');
        if (!$callbackSignature) {
            return false;
        }

        $signature = hash_hmac('sha256', $jsonBody, $this->privateKey);
        return hash_equals($signature, $callbackSignature);
    }

    /**
     * Test API connection
     */
    public function testConnection(): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get("{$this->baseUrl}/merchant/payment-channel");

            if ($response->successful()) {
                $count = count($response->json('data', []));
                return ['success' => true, 'channels_count' => $count];
            }

            return ['success' => false, 'error' => $response->json('message') ?? 'Connection failed'];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey) && !empty($this->privateKey) && !empty($this->merchantCode);
    }
}
