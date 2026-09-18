<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BurgundyClient extends Model
{
    public const CONTACT_STATUSES = [
        'not_contacted'     => 'Not Contacted',
        'contacted'         => 'Contacted',
        'interested'        => 'Interested',
        'not_interested'    => 'Not Interested',
        'payment_link_sent' => 'Payment Link Sent',
    ];

    protected $fillable = [
        'first_name', 'middle_name', 'last_name', 'email', 'phone', 'zip',
        'birth_date', 'ssn_last4',
        'source', 'match_status', 'matched_on',
        'contact_status', 'onboarding_status', 'client_status',
        'contacted_at', 'interested_at', 'link_sent_at', 'notes',
        'subscription_id', 'onboarding_submission_id', 'payment_agreement_id',
        'apex_status', 'apex_id', 'possible_duplicate_of',
    ];

    protected $casts = [
        'birth_date'    => 'date',
        'contacted_at'  => 'datetime',
        'interested_at' => 'datetime',
        'link_sent_at'  => 'datetime',
    ];

    // ── Relations ────────────────────────────────────────────────────────────

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function onboardingSubmission(): BelongsTo
    {
        return $this->belongsTo(OnboardingSubmission::class, 'onboarding_submission_id');
    }

    public function agreement(): BelongsTo
    {
        return $this->belongsTo(PaymentAgreement::class, 'payment_agreement_id');
    }

    public function identifiers(): HasMany
    {
        return $this->hasMany(BurgundyClientIdentifier::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(BurgundyClientEvent::class)->latest();
    }

    /** Every charge for this client — read through the subscription, never duplicated. */
    public function payments()
    {
        if (! $this->subscription_id) {
            return Payment::whereRaw('1 = 0');
        }

        return Payment::where('subscription_id', $this->subscription_id)
            ->orderByDesc('charged_at');
    }

    // ── Derived state ────────────────────────────────────────────────────────

    public function getFullNameAttribute(): string
    {
        return trim(implode(' ', array_filter([$this->first_name, $this->middle_name, $this->last_name])));
    }

    /** True once this client has been attached to a paid subscription. */
    public function hasPaid(): bool
    {
        return $this->subscription_id !== null;
    }

    /**
     * Payment state, read live from the linked subscription. Deliberately not
     * stored on this table — the Authorize.Net webhook owns these values, and a
     * second copy would drift the moment a rebill or refund lands.
     */
    public function paymentStatus(): string
    {
        if (! $this->subscription) {
            return 'unpaid';
        }

        return match ($this->subscription->status) {
            'active'     => 'paid',
            'past_due'   => 'failed',
            'terminated' => 'cancelled',
            default      => (string) $this->subscription->status,
        };
    }

    public function subscriptionStatus(): string
    {
        return $this->subscription?->status ?? '—';
    }

    public function nextBillingDate()
    {
        return $this->subscription?->next_billing_date;
    }

    /**
     * The client's single position in the ladder:
     *   Not Contacted → Contacted → Interested → Payment Link Sent → Paid
     *   → Onboarding Pending → Onboarding Complete → Active
     *
     * Checked most-advanced first: a paid client is past the contact stages
     * regardless of what the contact column still says.
     */
    public function stage(): string
    {
        if ($this->client_status === 'cancelled')          return 'Cancelled';
        if ($this->client_status === 'active')             return 'Active';
        if ($this->onboarding_status === 'complete')       return 'Onboarding Complete';
        if ($this->hasPaid())                              return 'Onboarding Pending';
        if ($this->contact_status === 'payment_link_sent') return 'Payment Link Sent';
        if ($this->contact_status === 'interested')        return 'Interested';
        if ($this->contact_status === 'not_interested')    return 'Not Interested';
        if ($this->contact_status === 'contacted')         return 'Contacted';

        return 'Not Contacted';
    }

    public function needsReview(): bool
    {
        return $this->match_status === 'needs_review';
    }

    // ── Normalizers (the matcher and the seeder share these) ─────────────────

    public static function normalizeEmail(?string $email): ?string
    {
        $e = strtolower(trim((string) $email));

        return $e === '' ? null : $e;
    }

    /** US phone → bare 10 digits, so (817) 917-2091 and +1 817-917-2091 match. */
    public static function normalizePhone(?string $phone): ?string
    {
        $d = preg_replace('/\D+/', '', (string) $phone);

        if (strlen($d) === 11 && str_starts_with($d, '1')) {
            $d = substr($d, 1);
        }

        return strlen($d) === 10 ? $d : null;
    }

    /** Name reduced to comparable form: lowercase, letters only, first|last. */
    public static function normalizeName(?string $first, ?string $last): ?string
    {
        $n = strtolower(trim((string) $first)) . '|' . strtolower(trim((string) $last));
        $n = preg_replace('/[^a-z|]/', '', $n);

        return trim($n, '|') === '' ? null : $n;
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    /** Record an event on this client's timeline. */
    public function logEvent(string $type, ?string $note = null, array $meta = []): void
    {
        $this->events()->create([
            'event_type' => $type,
            'note'       => $note,
            'meta'       => $meta ?: null,
        ]);
    }

    /** Teach the matcher an email/phone this client should match on from now on. */
    public function rememberIdentifier(string $type, ?string $value): void
    {
        $normalized = $type === 'email'
            ? self::normalizeEmail($value)
            : self::normalizePhone($value);

        if ($normalized === null) {
            return;
        }

        // Unique on (type, value). If another client already owns this identifier,
        // leave it where it is rather than stealing it — that ambiguity is exactly
        // what the review queue is for.
        BurgundyClientIdentifier::firstOrCreate(
            ['type' => $type, 'value' => $normalized],
            ['burgundy_client_id' => $this->id],
        );
    }
}
