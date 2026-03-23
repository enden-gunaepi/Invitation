<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliateCommission extends Model
{
    protected $fillable = [
        'referrer_user_id',
        'referred_user_id',
        'payment_id',
        'payout_request_id',
        'commission_amount',
        'status',
        'risk_flag',
        'risk_reason',
        'approved_at',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'commission_amount' => 'decimal:2',
            'risk_flag' => 'boolean',
            'approved_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    public function canApprove(): bool
    {
        return $this->status === 'pending';
    }

    public function canMarkPaid(): bool
    {
        return in_array($this->status, ['pending', 'approved'], true);
    }

    public function approve(): bool
    {
        if (!$this->canApprove()) {
            return false;
        }

        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        return true;
    }

    public function markPaid(): bool
    {
        if (!$this->canMarkPaid()) {
            return false;
        }

        $payload = [
            'status' => 'paid',
            'paid_at' => now(),
        ];

        if ($this->status === 'pending' && is_null($this->approved_at)) {
            $payload['approved_at'] = now();
        }

        $this->update($payload);

        return true;
    }

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referrer_user_id');
    }

    public function referred(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_user_id');
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function payoutRequest(): BelongsTo
    {
        return $this->belongsTo(PayoutRequest::class);
    }
}
