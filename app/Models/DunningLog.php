<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DunningLog extends Model
{
    protected $fillable = [
        'payment_id',
        'channel',
        'status',
        'message',
    ];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
}

