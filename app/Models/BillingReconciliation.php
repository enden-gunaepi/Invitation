<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillingReconciliation extends Model
{
    protected $fillable = [
        'run_date',
        'status',
        'issues_count',
        'summary',
    ];

    protected function casts(): array
    {
        return [
            'run_date' => 'date',
            'issues_count' => 'integer',
            'summary' => 'array',
        ];
    }
}
