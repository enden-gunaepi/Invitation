<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    protected $fillable = [
        'name', 'slug', 'tier', 'description', 'badge_text', 'support_level', 'sla_hours',
        'price', 'affiliate_commission_rate', 'billing_type', 'billing_cycle', 'max_guests', 'max_photos', 'max_invitations',
        'features', 'addons', 'allowed_template_ids', 'is_active', 'is_recommended',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'affiliate_commission_rate' => 'decimal:2',
            'features' => 'array',
            'addons' => 'array',
            'allowed_template_ids' => 'array',
            'is_active' => 'boolean',
            'is_recommended' => 'boolean',
        ];
    }

    public function allowsTemplate(int $templateId): bool
    {
        if (empty($this->allowed_template_ids)) {
            return true;
        }
        $allowed = collect($this->allowed_template_ids)
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->values()
            ->all();

        return in_array($templateId, $allowed, true);
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }
}
