<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReminderCampaign extends Model
{
    protected $fillable = [
        'invitation_id',
        'created_by_user_id',
        'channel',
        'audience',
        'message_template',
        'scheduled_at',
        'processed_at',
        'status',
        'sent_count',
        'failed_count',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'processed_at' => 'datetime',
        ];
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(ReminderLog::class, 'campaign_id');
    }
}
