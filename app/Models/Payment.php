<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PENDING = 'pending';
    public const STATUS_PENDING_VERIFICATION = 'pending_verification';
    public const STATUS_PAID = 'paid';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_FAILED = 'failed';
    public const STATUS_CANCELLED = 'cancelled';

    public const METHOD_TRANSFER_MANUAL = 'transfer_manual';

    public const PURPOSE_TOPUP = 'topup';
    public const PURPOSE_INVITATION = 'invitation';
    public const PURPOSE_SUBSCRIPTION = 'subscription';

    protected $fillable = [
        'user_id', 'invitation_id', 'client_package_subscription_id', 'package_id', 'amount', 'payment_method',
        'payment_gateway', 'payment_channel', 'payment_status', 'payment_purpose', 'transaction_id',
        'gateway_reference', 'callback_token', 'paid_at', 'expired_at',
        'payment_url', 'gateway_response', 'base_amount', 'discount_amount',
        'tax_amount', 'total_amount', 'invoice_number', 'invoice_due_at',
        'coupon_code', 'coupon_discount_amount', 'referral_code',
        'affiliate_commission_amount', 'retry_count', 'last_retry_at', 'next_retry_at',
        // Transfer Manual
        'transfer_proof_path', 'transfer_verified_at', 'transfer_verified_by', 'transfer_rejection_reason',
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
            'transfer_verified_at' => 'datetime',
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

    public function clientPackageSubscription(): BelongsTo
    {
        return $this->belongsTo(ClientPackageSubscription::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function callbackReceipts(): HasMany
    {
        return $this->hasMany(PaymentCallbackReceipt::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'transfer_verified_by');
    }

    public function isPending(): bool
    {
        return $this->payment_status === self::STATUS_PENDING;
    }

    public function isPendingVerification(): bool
    {
        return $this->payment_status === self::STATUS_PENDING_VERIFICATION;
    }

    public function isManualTransfer(): bool
    {
        return $this->payment_method === self::METHOD_TRANSFER_MANUAL;
    }

    public function isPaid(): bool
    {
        return $this->payment_status === self::STATUS_PAID;
    }

    public function isFinal(): bool
    {
        return in_array($this->payment_status, [
            self::STATUS_PAID,
            self::STATUS_EXPIRED,
            self::STATUS_FAILED,
            self::STATUS_CANCELLED,
        ], true);
    }

    public function markAsPendingVerification(): void
    {
        $this->update(['payment_status' => self::STATUS_PENDING_VERIFICATION]);
    }

    public function markAsVerified(int $adminId): void
    {
        $this->update([
            'payment_status'       => self::STATUS_PAID,
            'paid_at'              => now(),
            'transfer_verified_at' => now(),
            'transfer_verified_by' => $adminId,
            'transaction_id'       => $this->transaction_id ?? ('MANUAL-VERIFIED-' . now()->timestamp),
        ]);
    }

    public function markAsRejected(int $adminId, string $reason): void
    {
        $this->update([
            'payment_status'            => self::STATUS_FAILED,
            'transfer_verified_at'      => now(),
            'transfer_verified_by'      => $adminId,
            'transfer_rejection_reason' => $reason,
        ]);
    }

    public function isExpired(): bool
    {
        return $this->payment_status === self::STATUS_EXPIRED
            || ($this->expired_at && $this->expired_at->isPast() && $this->isPending());
    }

    public function markAsPaid(?string $transactionId = null): void
    {
        if ($this->isFinal() && !$this->isPending()) {
            return;
        }

        $this->update([
            'payment_status' => self::STATUS_PAID,
            'paid_at' => now(),
            'transaction_id' => $transactionId ?? $this->transaction_id,
        ]);
    }

    public function markAsFailed(): void
    {
        if ($this->isFinal()) {
            return;
        }

        $this->update(['payment_status' => self::STATUS_FAILED]);
    }

    public function markAsExpired(): void
    {
        if ($this->isFinal()) {
            return;
        }

        $this->update(['payment_status' => self::STATUS_EXPIRED]);
    }

    public function markAsCancelled(): void
    {
        if ($this->isFinal()) {
            return;
        }

        $this->update(['payment_status' => self::STATUS_CANCELLED]);
    }
}
