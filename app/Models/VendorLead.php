<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorLead extends Model
{
    protected $fillable = [
        'invitation_id',
        'user_id',
        'category',
        'vendor_name',
        'contact_name',
        'phone',
        'instagram',
        'status',
        'offered_price',
        'follow_up_date',
        'notes',
        'last_contact_at',
    ];

    protected function casts(): array
    {
        return [
            'offered_price' => 'decimal:2',
            'follow_up_date' => 'date',
            'last_contact_at' => 'datetime',
        ];
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
