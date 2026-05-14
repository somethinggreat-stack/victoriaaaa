<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MentorshipLead extends Model
{
    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone',
        'situation', 'timeline', 'hours', 'investment',
        'status',
        'ip', 'user_agent',
    ];

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
}
