<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvitationEvent extends Model
{
    protected $fillable = [
        'invitation_id', 'event_name', 'event_date', 'event_time',
        'event_end_time', 'venue_name', 'venue_address', 'venue_maps_url', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
        ];
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }
}
