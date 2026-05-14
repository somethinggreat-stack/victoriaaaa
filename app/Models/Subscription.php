<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'zip',
        'plan_key',
        'plan_label',
        'amount',
        'recurring_amount',
        'invoice_number',
        'transaction_id',
        'auth_code',
        'arb_subscription_id',
        'customer_profile_id',
        'customer_payment_profile_id',
        'referral_code',
        'status',
        'failed_payment_count',
        'first_failed_at',
        'grace_period_ends_at',
        'subscribed_at',
        'next_billing_date',
        'terminated_at',
    ];

    protected $casts = [
        'amount'               => 'decimal:2',
        'recurring_amount'     => 'decimal:2',
        'failed_payment_count' => 'integer',
        'first_failed_at'      => 'datetime',
        'grace_period_ends_at' => 'datetime',
        'subscribed_at'        => 'datetime',
        'next_billing_date'    => 'datetime',
        'terminated_at'        => 'datetime',
    ];

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(SubscriptionEvent::class);
    }

    public function lifetimeRevenue(): float
    {
        return (float) $this->payments->sum(fn ($p) => $p->signedAmount());
    }

    public function fullName(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }
}
