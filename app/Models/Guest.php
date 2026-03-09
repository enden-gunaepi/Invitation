<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Guest extends Model
{
    protected $fillable = [
        'invitation_id', 'name', 'phone', 'email', 'token', 'category', 'pax', 'notes',
        'table_number', 'seat_label', 'checked_in_at', 'checkin_method', 'checked_in_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'checked_in_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($guest) {
            if (empty($guest->token)) {
                $guest->token = Str::random(64);
            }
        });
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    public function rsvps(): HasMany
    {
        return $this->hasMany(Rsvp::class);
    }

    public function getInvitationUrl(): string
    {
        return url("/inv/{$this->invitation->slug}/{$this->token}");
    }
}
