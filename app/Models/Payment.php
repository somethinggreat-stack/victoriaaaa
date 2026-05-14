<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'subscription_id',
        'transaction_id',
        'invoice_number',
        'amount',
        'type',
        'status',
        'event_type_raw',
        'charged_at',
        'raw_payload',
    ];

    protected $casts = [
        'amount'      => 'decimal:2',
        'charged_at'  => 'datetime',
        'raw_payload' => 'array',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function signedAmount(): float
    {
        $a = (float) $this->amount;
        return in_array($this->type, ['refund', 'void'], true) ? -$a : $a;
    }
}
