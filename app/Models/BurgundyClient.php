<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BurgundyClient extends Model
{
    public const STATUSES = [
        'invited'         => 'Invited',
        'signed_up'       => 'Signed Up',
        'payment_pending' => 'Payment Pending',
        'paid'            => 'Paid',
        'active'          => 'Active',
        'paused'          => 'Paused',
        'cancelled'       => 'Cancelled',
    ];

    /** Statuses that flip to "paid" automatically once money arrives. */
    public const AWAITING_PAYMENT = ['invited', 'signed_up', 'payment_pending'];

    public const SOURCES = [
        'transitioned' => 'Transitioned',
        'new'          => 'New',
    ];

    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone',
        'status', 'source', 'subscription_id',
        'monthly_fee', 'current_round',
        'invited_at', 'signed_up_at', 'paid_at', 'last_activity_at',
        'next_action', 'notes',
    ];

    protected $casts = [
        'monthly_fee'      => 'decimal:2',
        'current_round'    => 'integer',
        'invited_at'       => 'datetime',
        'signed_up_at'     => 'date',
        'paid_at'          => 'datetime',
        'last_activity_at' => 'datetime',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function rounds(): HasMany
    {
        return $this->hasMany(BurgundyRound::class)->orderByDesc('round_number');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(BurgundyPayment::class)->latest();
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst(str_replace('_', ' ', (string) $this->status));
    }

    public function getSignupDateAttribute()
    {
        return $this->signed_up_at ?? $this->created_at;
    }

    /** What to do next when Victoria hasn't typed a custom next action. */
    public function suggestedNextAction(): string
    {
        return match ($this->status) {
            'invited'         => 'Follow up on invite',
            'signed_up'       => 'Send payment link',
            'payment_pending' => 'Collect payment',
            'paid'            => 'Start round 1',
            'active'          => 'Process round ' . ($this->current_round + 1),
            'paused'          => 'Check in to resume',
            default           => '—',
        };
    }

    public function getNextActionLabelAttribute(): string
    {
        return $this->next_action ?: $this->suggestedNextAction();
    }

    /** Change status, stamping the milestone dates the first time they're reached. */
    public function setStatus(string $status): void
    {
        $this->status = $status;

        if ($status === 'invited' && ! $this->invited_at) {
            $this->invited_at = now();
        }
        if ($status === 'signed_up' && ! $this->signed_up_at) {
            $this->signed_up_at = today();
        }
        if (in_array($status, ['paid', 'active'], true) && ! $this->paid_at) {
            $this->paid_at = now();
        }

        $this->last_activity_at = now();
        $this->save();
    }

    /** Called when a payment lands — advances clients still waiting on payment. */
    public function markPaidIfWaiting($paidAt = null): void
    {
        if (! in_array($this->status, self::AWAITING_PAYMENT, true)) {
            return;
        }

        $this->status           = 'paid';
        $this->paid_at          = $this->paid_at ?? $paidAt ?? now();
        $this->signed_up_at     = $this->signed_up_at ?? today();
        $this->last_activity_at = now();
        $this->save();
    }

    public function touchActivity(): void
    {
        $this->forceFill(['last_activity_at' => now()])->save();
    }
}
