<?php

namespace App\Services\Payments;

use App\Models\Payment;
use App\Models\PaymentCallbackReceipt;
use App\Services\ClientPackageService;
use Illuminate\Support\Collection;

class PaymentStatusSyncService
{
    public function __construct(
        private readonly PaymentGatewayRegistry $gatewayRegistry,
        private readonly ClientPackageService $clientPackageService,
    ) {
    }

    public function syncPendingPayments(?Collection $payments = null): array
    {
        $payments ??= Payment::query()
            ->where('payment_status', 'pending')
            ->whereNotNull('gateway_reference')
            ->where('payment_gateway', 'xendit')
            ->get();

        $synced = 0;
        $expired = 0;
        $errors = 0;

        foreach ($payments as $payment) {
            $gateway = $this->gatewayRegistry->forCode((string) $payment->payment_gateway);
            $result = $gateway->queryPaymentStatus((string) $payment->gateway_reference);

            if (!$result['success']) {
                $errors++;
                continue;
            }

            $payment->update(['gateway_response' => $result['data'] ?? $payment->gateway_response]);
            $status = strtoupper((string) ($result['status'] ?? ''));

            if (in_array($status, ['PAID', 'SETTLED'], true)) {
                if (!$payment->isPaid()) {
                    $payment->markAsPaid((string) ($payment->gateway_reference ?: $payment->transaction_id));
                    if ($payment->client_package_subscription_id) {
                        $this->clientPackageService->activateFromPayment($payment->fresh(['clientPackageSubscription.package']));
                    }
                    $synced++;
                }
            } elseif (in_array($status, ['EXPIRED', 'FAILED'], true)) {
                if ($status === 'EXPIRED') {
                    $payment->markAsExpired();
                    $expired++;
                } else {
                    $payment->markAsFailed();
                    $errors++;
                }
            }
        }

        return [
            'synced_paid' => $synced,
            'marked_expired' => $expired,
            'errors' => $errors,
        ];
    }

    public function recentReceipts(int $limit = 20): Collection
    {
        return PaymentCallbackReceipt::query()
            ->with('payment.user')
            ->latest()
            ->limit($limit)
            ->get();
    }
}
