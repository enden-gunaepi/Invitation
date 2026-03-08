<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'user_id', 'invitation_id', 'package_id', 'amount', 'payment_method',
        'payment_gateway', 'payment_channel', 'payment_status', 'transaction_id',
        'gateway_reference', 'callback_token', 'paid_at', 'expired_at',
        'payment_url', 'gateway_response',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'expired_at' => 'datetime',
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

    public function markAsPaid(string $transactionId = null): void
    {
        $this->update([
            'payment_status' => 'paid',
            'paid_at' => now(),
            'transaction_id' => $transactionId ?? $this->transaction_id,
        ]);
    }

    public function markAsFailed(): void
    {
        $this->update(['payment_status' => 'failed']);
    }
}
