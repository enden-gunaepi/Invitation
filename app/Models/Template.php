<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Template extends Model
{
    protected $fillable = [
        'name', 'slug', 'category', 'thumbnail', 'preview_url', 'html_path',
        'color_schemes', 'is_premium', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'color_schemes' => 'array',
            'is_premium' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }
}
