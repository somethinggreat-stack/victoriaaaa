<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentAgreement extends Model
{
    protected $fillable = [
        'plan_key',
        'plan_label',
        'deposit_amount',
        'installment_amount',
        'installment_count',
        'total_amount',
        'full_name',
        'signature_data',
        'contract_text',
        'terms_version',
        'email',
        'invoice_number',
        'subscription_id',
        'ip_address',
        'user_agent',
        'signed_at',
    ];

    protected $casts = [
        'deposit_amount'     => 'decimal:2',
        'installment_amount' => 'decimal:2',
        'installment_count'  => 'integer',
        'total_amount'       => 'decimal:2',
        'signed_at'          => 'datetime',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}
