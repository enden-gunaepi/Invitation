<?php

namespace App\Contracts;

interface PaymentGatewayInterface
{
    public function code(): string;

    public function isConfigured(): bool;

    public function supportedChannels(): array;

    public function createPaymentIntent(array $payload): array;

    public function verifyWebhook(string $rawBody, array $headers = [], array $payload = []): bool;

    public function parseWebhook(array $payload): array;

    public function queryPaymentStatus(string $gatewayReference): array;
}
