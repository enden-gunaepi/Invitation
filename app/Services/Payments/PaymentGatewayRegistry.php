<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Setting;
use InvalidArgumentException;

class PaymentGatewayRegistry
{
    public function forCode(string $code): PaymentGatewayInterface
    {
        return match ($code) {
            'xendit' => app(XenditGateway::class),
            'tripay' => app(TripayGateway::class),
            default => throw new InvalidArgumentException("Unsupported payment gateway [{$code}]"),
        };
    }

    public function primaryGatewayCode(): string
    {
        return Setting::get('payment_primary_gateway', 'xendit') ?: 'xendit';
    }

    public function productionGatewayCodes(): array
    {
        $enabled = [];

        if (Setting::get('xendit_enabled', '0') === '1') {
            $enabled[] = 'xendit';
        }

        if (Setting::get('tripay_enabled', '0') === '1') {
            $enabled[] = 'tripay';
        }

        return $enabled;
    }
}
