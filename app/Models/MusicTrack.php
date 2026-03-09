<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MusicTrack extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'file_path',
        'mime_type',
        'file_size',
        'is_public',
        'usage_count',
    ];

    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

