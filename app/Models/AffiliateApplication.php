<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliateApplication extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'payout_method', 'payout_details', 'status',
        'agreement_version', 'accepted_at', 'accepted_ip', 'affiliate_id',
    ];

    protected $casts = ['accepted_at' => 'datetime'];

    public const METHODS = ['zelle' => 'Zelle', 'paypal' => 'PayPal', 'cashapp' => 'Cash App', 'check' => 'Check (mailed)'];

    public function methodLabel(): string
    {
        return self::METHODS[$this->payout_method] ?? $this->payout_method;
    }
}
