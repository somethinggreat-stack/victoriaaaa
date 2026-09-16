<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'subscription_id',
        'customer_name',
        'customer_email',
        'source',
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

    /** Who paid — the subscription's owner, or whoever we identified. */
    public function payerName(): ?string
    {
        if ($this->subscription) {
            return trim($this->subscription->first_name . ' ' . $this->subscription->last_name) ?: null;
        }

        return $this->customer_name;
    }

    public function payerEmail(): ?string
    {
        return $this->subscription?->email ?? $this->customer_email;
    }

    /** Where the charge came from, in plain words. */
    public function sourceLabel(): string
    {
        if ($this->subscription) {
            return $this->subscription->plan_label ?: 'Subscription';
        }

        return match ($this->source) {
            'payment_link' => 'Payment link',
            'ebook'        => 'eBook sale',
            'gateway'      => 'Charged in Authorize.Net',
            default        => '—',
        };
    }

    public function signedAmount(): float
    {
        $a = (float) $this->amount;
        return in_array($this->type, ['refund', 'void'], true) ? -$a : $a;
    }
}
