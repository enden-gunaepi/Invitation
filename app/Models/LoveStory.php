<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoveStory extends Model
{
    protected $fillable = ['invitation_id', 'year', 'title', 'description', 'photo_path', 'sort_order'];

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }
}
