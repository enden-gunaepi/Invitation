<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'referral_code', 'referred_by_user_id', 'affiliate_rate',
        'password', 'role', 'phone', 'avatar', 'is_active',
        'signup_ip', 'signup_ua_hash', 'referral_clicked_at',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'affiliate_rate' => 'decimal:2',
            'referral_clicked_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $user): void {
            if (empty($user->referral_code)) {
                $user->referral_code = strtoupper(Str::random(8));
            }
        });
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }

    public function referredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_by_user_id');
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(User::class, 'referred_by_user_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function affiliateCommissionsAsReferrer(): HasMany
    {
        return $this->hasMany(AffiliateCommission::class, 'referrer_user_id');
    }

    public function payoutRequests(): HasMany
    {
        return $this->hasMany(PayoutRequest::class);
    }

    public function affiliateClicks(): HasMany
    {
        return $this->hasMany(AffiliateClick::class, 'referrer_user_id');
    }

    public function reminderCampaigns(): HasMany
    {
        return $this->hasMany(ReminderCampaign::class, 'created_by_user_id');
    }

    public function vendorLeads(): HasMany
    {
        return $this->hasMany(VendorLead::class);
    }
}
