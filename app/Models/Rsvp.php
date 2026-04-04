<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rsvp extends Model
{
    protected $fillable = [
        'invitation_id', 'guest_id', 'name', 'phone', 'normalized_phone', 'status', 'pax', 'message', 'is_shown', 'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'is_shown' => 'boolean',
        ];
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }
}
