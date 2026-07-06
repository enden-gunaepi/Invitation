<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManualTransferBankAccount extends Model
{
    protected $fillable = [
        'bank_name',
        'account_number',
        'account_holder_name',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('id');
    }
}
