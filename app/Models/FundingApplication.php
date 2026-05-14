<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FundingApplication extends Model
{
    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone',
        'amount', 'confirmed', 'limits', 'usage', 'fico',
        'situation', 'income', 'negatives',
        'status',
        'ip', 'user_agent',
    ];

    protected $casts = [
        'negatives' => 'array',
    ];

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
}
