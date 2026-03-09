<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReminderLog extends Model
{
    protected $fillable = [
        'campaign_id',
        'guest_id',
        'phone',
        'status',
        'provider_message_id',
        'response_message',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(ReminderCampaign::class, 'campaign_id');
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }
}
