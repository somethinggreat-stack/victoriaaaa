<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'name', 'email', 'phone',
        'score', 'issue', 'goal',
        'source', 'status',
        'ip', 'user_agent',
    ];
}
