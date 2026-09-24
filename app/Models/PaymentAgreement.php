<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentAgreement extends Model
{
    protected $fillable = [
        'source', 'source_id', 'partner', 'status',
        'service_description', 'next_url', 'client_name', 'client_phone',
        'requires_cosigner', 'cosigner_name', 'cosigner_email',
        'cosigner_full_name', 'cosigner_signature_data',
        'cosigner_ip_address', 'cosigner_user_agent', 'cosigner_signed_at',
        'plan_key',
        'plan_label',
        'deposit_amount',
        'installment_amount',
        'installment_count',
        'recurring_interval',
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
        'requires_cosigner'  => 'boolean',
        'cosigner_signed_at' => 'datetime',
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

    public function primarySigned(): bool
    {
        return ! empty($this->signature_data);
    }

    public function cosignerSigned(): bool
    {
        return ! empty($this->cosigner_signature_data);
    }

    /** Everyone who has to sign has signed. */
    public function fullySigned(): bool
    {
        return $this->primarySigned()
            && (! $this->requires_cosigner || $this->cosignerSigned());
    }

    /** Signed by one of two people, still waiting on the other. */
    public function isPartiallySigned(): bool
    {
        return $this->requires_cosigner
            && ($this->primarySigned() xor $this->cosignerSigned());
    }

    /** Who still owes a signature, for the admin and the client page. */
    public function awaitingSignatureFrom(): ?string
    {
        if (! $this->primarySigned()) {
            return $this->client_name ?: 'the client';
        }

        if ($this->requires_cosigner && ! $this->cosignerSigned()) {
            return $this->cosigner_name ?: 'the second client';
        }

        return null;
    }

    /** Both names for a joint agreement, one for everything else. */
    public function partiesLabel(): string
    {
        $first = $this->full_name ?: $this->client_name ?: '—';

        if (! $this->requires_cosigner) {
            return $first;
        }

        $second = $this->cosigner_full_name ?: $this->cosigner_name ?: 'second client';

        return $first . ' & ' . $second;
    }

    /** Who signed, or who we expected to. */
    public function signerName(): string
    {
        return $this->requires_cosigner
            ? $this->partiesLabel()
            : ($this->full_name ?: ($this->client_name ?: '—'));
    }

    /** "$149.00 today, then $149.00/mo" — for admin lists. */
    public function priceSummary(): string
    {
        $today = '$' . number_format((float) $this->deposit_amount, 2);

        if ($this->installment_amount && (float) $this->installment_amount > 0) {
            $each = '$' . number_format((float) $this->installment_amount, 2);

            $every = $this->recurring_interval === 'week' ? 'wk' : 'mo';

            return $this->installment_count
                ? "{$today} today, then {$each}/{$every} x {$this->installment_count}"
                : "{$today} today, then {$each}/{$every}";
        }

        return $today . ' one-time';
    }
}
