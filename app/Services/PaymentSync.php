<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Subscription;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Attaches Authorize.Net charges to the right subscription.
 *
 * A recurring (ARB) charge arrives with its own transaction id and — unless the
 * subscription was created with an order invoice — no invoice number at all, so
 * it matches nothing we stored at checkout. Looking the transaction up gives us
 * the ARB subscription id / customer email, which does match.
 *
 * Shared by the live webhook and the backfill command so both link identically.
 */
class PaymentSync
{
    public function __construct(private AuthorizeNetApi $api) {}

    /** Transaction details from Authorize.Net, or null if unavailable. */
    public function lookupTransaction(?string $transactionId): ?array
    {
        if (! $transactionId || ! $this->api->isConfigured()) {
            return null;
        }

        return $this->api->transactionDetails($transactionId);
    }

    /**
     * Find the subscription a charge belongs to: ARB id first (exact), then the
     * invoice number, then the customer email as a last resort.
     */
    public function subscriptionFor(?string $arbId, ?string $invoiceNumber, ?string $email): ?Subscription
    {
        if ($arbId) {
            $match = Subscription::where('arb_subscription_id', (string) $arbId)->first();
            if ($match) {
                return $match;
            }
        }

        if ($invoiceNumber) {
            $match = Subscription::where('invoice_number', $invoiceNumber)->first();
            if ($match) {
                return $this->rememberArbId($match, $arbId);
            }
        }

        if ($email) {
            $match = Subscription::where('email', $email)->latest()->first();
            if ($match) {
                return $this->rememberArbId($match, $arbId);
            }
        }

        return null;
    }

    /** Store the ARB id on a subscription that didn't have one, so later charges match instantly. */
    public function rememberArbId(Subscription $subscription, ?string $arbId): Subscription
    {
        if ($arbId && ! $subscription->arb_subscription_id) {
            $subscription->update(['arb_subscription_id' => (string) $arbId]);
        }

        return $subscription;
    }

    /**
     * Roll the next billing date forward past the charge we just saw. Every
     * recurring plan on this site bills monthly.
     */
    public function advanceBillingDate(Subscription $subscription, CarbonInterface $chargedAt): void
    {
        if ($subscription->recurring_amount === null || $subscription->status === 'terminated') {
            return;
        }

        $next = Carbon::instance($chargedAt instanceof Carbon ? $chargedAt : Carbon::parse($chargedAt))->addMonth();

        if ($subscription->next_billing_date === null || $subscription->next_billing_date->lessThan($next)) {
            $subscription->update(['next_billing_date' => $next]);
        }
    }

    /** A rebill proves the card is good again. */
    public function clearPastDue(Subscription $subscription): void
    {
        if ($subscription->status === 'past_due') {
            $subscription->update([
                'status'               => 'active',
                'failed_payment_count' => 0,
                'first_failed_at'      => null,
                'grace_period_ends_at' => null,
            ]);
        }
    }

    /** payNum 1 is the signup charge; anything higher is a renewal. */
    public function typeForPayNum($payNum): string
    {
        return ((int) $payNum) > 1 ? 'recurring' : 'initial';
    }

    /**
     * Map an Authorize.Net transaction type onto our payment type + status.
     *
     * @return array{0: string, 1: string}|null  [type, status]
     */
    public function classify(?string $transactionType, ?string $transactionStatus, $payNum = null): ?array
    {
        if (in_array($transactionStatus, ['voided', 'expired', 'declined', 'FDSPendingReview', 'FDSAuthorizedPendingReview'], true)) {
            return $transactionStatus === 'voided' ? ['void', 'voided'] : null;
        }

        return match ($transactionType) {
            'authCaptureTransaction', 'authOnlyTransaction', 'priorAuthCaptureTransaction', 'captureOnlyTransaction'
                => [$this->typeForPayNum($payNum), 'captured'],
            'refundTransaction'  => ['refund', 'refunded'],
            'voidTransaction'    => ['void', 'voided'],
            default              => null,
        };
    }

    /** True when this transaction is already on file (under any type). */
    public function alreadyRecorded(?string $transactionId): bool
    {
        return $transactionId && Payment::where('transaction_id', $transactionId)->exists();
    }

    /**
     * Resolve + record one charge. Returns the payment, or null if it was
     * skipped. Safe to call twice for the same transaction.
     */
    public function record(array $attrs, ?Subscription $subscription, CarbonInterface $chargedAt): ?Payment
    {
        try {
            $attrs['subscription_id'] = $subscription?->id;
            $attrs['charged_at']      = $chargedAt;

            $payment = Payment::updateOrCreate(
                ['transaction_id' => $attrs['transaction_id'], 'type' => $attrs['type']],
                $attrs
            );

            if ($subscription && in_array($attrs['type'], ['initial', 'recurring'], true)) {
                $this->advanceBillingDate($subscription, $chargedAt);
                $this->clearPastDue($subscription);
            }

            return $payment;
        } catch (\Throwable $e) {
            Log::error('[PaymentSync] failed to record payment', [
                'transaction_id' => $attrs['transaction_id'] ?? null,
                'message'        => $e->getMessage(),
            ]);
            return null;
        }
    }
}
