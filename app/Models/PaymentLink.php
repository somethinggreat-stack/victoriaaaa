<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentLink extends Model
{
    protected $fillable = [
        'token', 'client_name', 'email', 'amount', 'note', 'status',
        'invoice_number', 'transaction_id', 'auth_code', 'payer_email', 'paid_at',
    ];

    protected $casts = [
        'amount'  => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    /** Full public pay URL for this link. */
    public function getUrlAttribute(): string
    {
        return route('pay.show', $this->token);
    }

    /** Can this link still be paid? */
    public function isPayable(): bool
    {
        return $this->status === 'unpaid';
    }
}
