<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentAgreement extends Model
{
    protected $fillable = [
        'source', 'source_id', 'partner', 'status',
        'service_description', 'next_url', 'client_name', 'client_phone',
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

    public function isSigned(): bool
    {
        return $this->status === 'signed';
    }

    /** Who signed, or who we expected to. */
    public function signerName(): string
    {
        return $this->full_name ?: ($this->client_name ?: '—');
    }

    /** "$149.00 today, then $149.00/mo" — for admin lists. */
    public function priceSummary(): string
    {
        $today = '$' . number_format((float) $this->deposit_amount, 2);

        if ($this->installment_amount && (float) $this->installment_amount > 0) {
            $each = '$' . number_format((float) $this->installment_amount, 2);

            return $this->installment_count
                ? "{$today} today, then {$each} x {$this->installment_count}"
                : "{$today} today, then {$each}/mo";
        }

        return $today . ' one-time';
    }
}
