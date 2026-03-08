<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Invitation extends Model
{
    protected $fillable = [
        'user_id', 'template_id', 'package_id', 'slug', 'event_type', 'title',
        'groom_name', 'bride_name', 'host_name', 'event_date', 'event_time',
        'event_end_time', 'venue_name', 'venue_address', 'venue_lat', 'venue_lng',
        'google_maps_url', 'cover_photo', 'opening_text', 'closing_text',
        'bank_name', 'bank_account_number', 'bank_account_name', 'gift_address', 'footer_text',
        'music_url', 'status', 'is_password_protected', 'invitation_password',
        'rsvp_deadline', 'custom_colors', 'custom_fonts', 'view_count',
        'published_at', 'expires_at', 'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'event_time' => 'datetime:H:i',
            'event_end_time' => 'datetime:H:i',
            'rsvp_deadline' => 'date',
            'published_at' => 'datetime',
            'expires_at' => 'datetime',
            'custom_colors' => 'array',
            'custom_fonts' => 'array',
            'is_password_protected' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($invitation) {
            if (empty($invitation->slug)) {
                $invitation->slug = Str::slug($invitation->title) . '-' . Str::random(6);
            }
        });
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(InvitationPhoto::class)->orderBy('sort_order');
    }

    public function events(): HasMany
    {
        return $this->hasMany(InvitationEvent::class)->orderBy('sort_order');
    }

    public function guests(): HasMany
    {
        return $this->hasMany(Guest::class);
    }

    public function rsvps(): HasMany
    {
        return $this->hasMany(Rsvp::class);
    }

    public function wishes(): HasMany
    {
        return $this->hasMany(Wish::class);
    }

    public function loveStories(): HasMany
    {
        return $this->hasMany(LoveStory::class)->orderBy('sort_order');
    }

    public function views(): HasMany
    {
        return $this->hasMany(InvitationView::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Helpers
    public function getPublicUrl(): string
    {
        return url("/inv/{$this->slug}");
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if more guests can be added based on the package limit.
     */
    public function canAddGuest(): bool
    {
        $max = $this->package->max_guests ?? 100;
        return $this->guests()->count() < $max;
    }

    /**
     * Check if more photos can be added based on the package limit.
     */
    public function canAddPhoto(): bool
    {
        $max = $this->package->max_photos ?? 10;
        return $this->photos()->count() < $max;
    }

    /**
     * Get guest limit info: [current, max].
     */
    public function guestLimitInfo(): array
    {
        return [
            'current' => $this->guests()->count(),
            'max' => $this->package->max_guests ?? 100,
        ];
    }

    /**
     * Get photo limit info: [current, max].
     */
    public function photoLimitInfo(): array
    {
        return [
            'current' => $this->photos()->count(),
            'max' => $this->package->max_photos ?? 10,
        ];
    }
}
