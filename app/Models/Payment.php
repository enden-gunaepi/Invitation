<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    protected $fillable = [
        'user_id', 'invitation_id', 'package_id', 'amount', 'payment_method',
        'payment_gateway', 'payment_channel', 'payment_status', 'transaction_id',
        'gateway_reference', 'callback_token', 'paid_at', 'expired_at',
        'payment_url', 'gateway_response', 'base_amount', 'discount_amount',
        'tax_amount', 'total_amount', 'invoice_number', 'invoice_due_at',
        'coupon_code', 'coupon_discount_amount', 'referral_code',
        'affiliate_commission_amount', 'retry_count', 'last_retry_at', 'next_retry_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'base_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'coupon_discount_amount' => 'decimal:2',
            'affiliate_commission_amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'expired_at' => 'datetime',
            'invoice_due_at' => 'datetime',
            'last_retry_at' => 'datetime',
            'next_retry_at' => 'datetime',
            'gateway_response' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function callbackReceipts(): HasMany
    {
        return $this->hasMany(PaymentCallbackReceipt::class);
    }

    public function isPending(): bool
    {
        return $this->payment_status === 'pending';
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function isExpired(): bool
    {
        return $this->expired_at && $this->expired_at->isPast() && $this->isPending();
    }

    public function markAsPaid(?string $transactionId = null): void
    {
        if (!in_array($this->payment_status, ['pending', 'failed'], true)) {
            return;
        }

        $this->update([
            'payment_status' => 'paid',
            'paid_at' => now(),
            'transaction_id' => $transactionId ?? $this->transaction_id,
        ]);
    }

    public function markAsFailed(): void
    {
        if ($this->payment_status !== 'pending') {
            return;
        }

        $this->update(['payment_status' => 'failed']);
    }
}
