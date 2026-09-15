<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BurgundyExpense extends Model
{
    public const STATUSES = ['pending', 'approved', 'rejected'];

    protected $fillable = [
        'description', 'amount', 'expense_date', 'status', 'notes',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'expense_date' => 'date',
    ];
}
