<?php

namespace App\Models\Planner;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WpBudgetCategory extends Model
{
    protected $fillable = [
        'wp_profile_id', 'name', 'estimated_amount', 'actual_amount',
        'sort_order', 'icon', 'color',
    ];

    protected function casts(): array
    {
        return [
            'estimated_amount' => 'decimal:2',
            'actual_amount' => 'decimal:2',
            'sort_order' => 'integer',
        ];
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(WpProfile::class, 'wp_profile_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(WpBudgetItem::class);
    }

    public function recalculate(): void
    {
        $this->update([
            'actual_amount' => $this->items()->sum('actual_amount'),
        ]);
    }

    public function getProgressPercentAttribute(): int
    {
        if ($this->estimated_amount <= 0) return 0;
        return (int) round(((float) $this->actual_amount / (float) $this->estimated_amount) * 100);
    }

    public function isOverBudget(): bool
    {
        return (float) $this->actual_amount > (float) $this->estimated_amount && (float) $this->estimated_amount > 0;
    }
}
