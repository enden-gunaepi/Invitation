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
        'approved_at',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'commission_amount' => 'decimal:2',
            'approved_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
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
