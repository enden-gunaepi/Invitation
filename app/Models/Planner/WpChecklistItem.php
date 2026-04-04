<?php

namespace App\Models\Planner;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WpChecklistItem extends Model
{
    protected $fillable = [
        'wp_profile_id', 'title', 'description', 'category', 'deadline',
        'status', 'sort_order', 'is_auto_generated', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'date',
            'completed_at' => 'datetime',
            'is_auto_generated' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(WpProfile::class, 'wp_profile_id');
    }

    public function isOverdue(): bool
    {
        return $this->deadline && $this->deadline->isPast() && $this->status !== 'done';
    }

    public function isUrgent(): bool
    {
        return $this->deadline
            && $this->deadline->diffInDays(now()) <= 7
            && $this->deadline->isFuture()
            && $this->status !== 'done';
    }
}
