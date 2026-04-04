<?php

namespace App\Models\Planner;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WpBudgetItem extends Model
{
    protected $fillable = [
        'wp_budget_category_id', 'name', 'vendor_name',
        'estimated_amount', 'actual_amount', 'notes', 'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'estimated_amount' => 'decimal:2',
            'actual_amount' => 'decimal:2',
            'paid_at' => 'date',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(WpBudgetCategory::class, 'wp_budget_category_id');
    }
}
