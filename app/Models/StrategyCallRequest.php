<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StrategyCallRequest extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'best_time',
        'situation', 'score', 'timeline', 'goal',
        'prior_repair', 'prior_repair_notes',
        'monitoring_service', 'monitoring_username', 'will_bring_login',
        'investment_range', 'showup_confirmed',
        'status', 'ip', 'user_agent',
    ];

    protected $casts = [
        'prior_repair'     => 'boolean',
        'will_bring_login' => 'boolean',
        'showup_confirmed' => 'boolean',
    ];
}
