<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Affiliate extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'code', 'payout_amount', 'active', 'notes'];

    protected $casts = [
        'payout_amount' => 'decimal:2',
        'active'        => 'boolean',
    ];

    public function referrals(): HasMany
    {
        return $this->hasMany(AffiliateReferral::class);
    }

    /** The link this affiliate shares. */
    public function getLinkAttribute(): string
    {
        return url('/r/' . $this->code);
    }
}
