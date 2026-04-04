<?php

namespace App\Models\Planner;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WpVendor extends Model
{
    protected $table = 'wp_vendors';

    protected $fillable = [
        'wp_profile_id', 'category', 'name', 'contact_person', 'phone',
        'email', 'instagram', 'website', 'price', 'dp_amount', 'dp_paid_at',
        'remaining_amount', 'remaining_paid_at', 'status', 'notes',
        'contract_file', 'payment_deadline',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'dp_amount' => 'decimal:2',
            'remaining_amount' => 'decimal:2',
            'dp_paid_at' => 'date',
            'remaining_paid_at' => 'date',
            'payment_deadline' => 'date',
        ];
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(WpProfile::class, 'wp_profile_id');
    }

    public function isPaymentDueSoon(): bool
    {
        return $this->payment_deadline
            && $this->payment_deadline->diffInDays(now()) <= 7
            && $this->payment_deadline->isFuture()
            && !in_array($this->status, ['lunas', 'cancelled']);
    }

    public function getWhatsappUrlAttribute(): ?string
    {
        if (empty($this->phone)) return null;
        $phone = preg_replace('/[^0-9]/', '', $this->phone);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }
        return "https://wa.me/{$phone}";
    }

    public function getInstagramUrlAttribute(): ?string
    {
        if (empty($this->instagram)) return null;
        $handle = ltrim($this->instagram, '@');
        return "https://instagram.com/{$handle}";
    }

    public static function categoryOptions(): array
    {
        return [
            'venue' => 'Venue / Gedung',
            'catering' => 'Catering',
            'dekor' => 'Dekorasi',
            'foto' => 'Fotografer',
            'video' => 'Videografer',
            'mc' => 'MC / Pembawa Acara',
            'entertainment' => 'Entertainment / Musik',
            'makeup' => 'MUA / Makeup',
            'busana' => 'Busana / Bridal',
            'souvenir' => 'Souvenir',
            'undangan' => 'Undangan',
            'lainnya' => 'Lainnya',
        ];
    }

    public static function statusOptions(): array
    {
        return [
            'prospek' => 'Prospek',
            'deal' => 'Deal',
            'dp_paid' => 'DP Lunas',
            'lunas' => 'Lunas',
            'cancelled' => 'Cancelled',
        ];
    }
}
