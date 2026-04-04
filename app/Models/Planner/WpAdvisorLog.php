<?php

namespace App\Models\Planner;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WpAdvisorLog extends Model
{
    protected $fillable = [
        'wp_profile_id', 'question', 'answer', 'category',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(WpProfile::class, 'wp_profile_id');
    }
}
