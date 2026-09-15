<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BurgundyPayment extends Model
{
    protected $fillable = [
        'burgundy_client_id', 'amount', 'status', 'method',
        'payment_link_id', 'counts_toward_profit', 'paid_at', 'note',
    ];

    protected $casts = [
        'amount'               => 'decimal:2',
        'counts_toward_profit' => 'boolean',
        'paid_at'              => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(BurgundyClient::class, 'burgundy_client_id');
    }

    public function paymentLink(): BelongsTo
    {
        return $this->belongsTo(PaymentLink::class);
    }
}
