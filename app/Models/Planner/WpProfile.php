<?php

namespace App\Models\Planner;

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WpProfile extends Model
{
    protected $fillable = [
        'user_id', 'invitation_id', 'partner_1_name', 'partner_2_name',
        'wedding_date', 'city', 'target_guests', 'concept', 'total_budget',
        'onboarding_completed',
    ];

    protected function casts(): array
    {
        return [
            'wedding_date' => 'date',
            'total_budget' => 'decimal:2',
            'target_guests' => 'integer',
            'onboarding_completed' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    public function checklistItems(): HasMany
    {
        return $this->hasMany(WpChecklistItem::class)->orderBy('sort_order');
    }

    public function budgetCategories(): HasMany
    {
        return $this->hasMany(WpBudgetCategory::class)->orderBy('sort_order');
    }

    public function vendors(): HasMany
    {
        return $this->hasMany(WpVendor::class);
    }

    public function timelineEvents(): HasMany
    {
        return $this->hasMany(WpTimelineEvent::class)->orderBy('sort_order');
    }

    public function advisorLogs(): HasMany
    {
        return $this->hasMany(WpAdvisorLog::class)->latest();
    }

    public function getDaysRemainingAttribute(): ?int
    {
        if (!$this->wedding_date) {
            return null;
        }
        return (int) now()->startOfDay()->diffInDays($this->wedding_date, false);
    }

    public function getChecklistProgressAttribute(): int
    {
        $total = $this->checklistItems()->count();
        if ($total === 0) return 0;
        $done = $this->checklistItems()->where('status', 'done')->count();
        return (int) round(($done / $total) * 100);
    }

    public function getBudgetUsedAttribute(): float
    {
        return (float) $this->budgetCategories()->sum('actual_amount');
    }

    public function getBudgetRemainingAttribute(): float
    {
        return max(0, (float) $this->total_budget - $this->budget_used);
    }

    public function getBudgetPercentAttribute(): int
    {
        if ($this->total_budget <= 0) return 0;
        return (int) round(($this->budget_used / (float) $this->total_budget) * 100);
    }
}
