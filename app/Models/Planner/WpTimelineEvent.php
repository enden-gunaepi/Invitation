<?php

namespace App\Models\Planner;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WpTimelineEvent extends Model
{
    protected $fillable = [
        'wp_profile_id', 'title', 'description', 'target_date',
        'is_completed', 'category', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'target_date' => 'date',
            'is_completed' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(WpProfile::class, 'wp_profile_id');
    }
}
