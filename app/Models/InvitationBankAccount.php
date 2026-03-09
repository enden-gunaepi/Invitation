<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvitationBankAccount extends Model
{
    protected $fillable = [
        'invitation_id',
        'bank_name',
        'account_number',
        'account_name',
        'sort_order',
    ];

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }
}

