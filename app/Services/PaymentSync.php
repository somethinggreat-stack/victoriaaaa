<?php

namespace App\Services;

use App\Models\EbookOrder;
use App\Models\Payment;
use App\Models\PaymentLink;
use App\Models\Subscription;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

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
     * Label a captured charge. Authorize.Net only supplies a payment number for
     * charges taken by the recurring schedule; a charge taken any other way
     * (virtual terminal, payment link) has none. In that case it's only a signup
     * charge if the client doesn't already have one on file.
     */
    public function typeForCharge($payNum, ?Subscription $subscription): string
    {
        if ($payNum !== null && $payNum !== '') {
            return $this->typeForPayNum($payNum);
        }

        if ($subscription && Payment::where('subscription_id', $subscription->id)
                ->where('type', 'initial')
                ->exists()) {
            return 'recurring';
        }

        return 'initial';
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

    /**
     * Work out who actually paid, so a charge is never shown as "Unlinked".
     *
     * In order of certainty: the linked subscription, a payment link we
     * generated, an eBook order, and finally the name on the card as
     * Authorize.Net holds it (charges taken straight in the gateway).
     */
    public function attribute(Payment $payment, ?array $details = null, bool $save = true): Payment
    {
        $name = $email = $source = null;

        if ($payment->subscription) {
            $name   = trim($payment->subscription->first_name . ' ' . $payment->subscription->last_name) ?: null;
            $email  = $payment->subscription->email;
            $source = 'subscription';
        }

        if (! $name && ($payment->invoice_number || $payment->transaction_id)) {
            if (Schema::hasTable('payment_links')) {
                $link = PaymentLink::where(function ($q) use ($payment) {
                    if ($payment->invoice_number) $q->orWhere('invoice_number', $payment->invoice_number);
                    if ($payment->transaction_id) $q->orWhere('transaction_id', $payment->transaction_id);
                })->first();

                if ($link) {
                    $name   = $link->client_name;
                    $email  = $link->payer_email ?: $link->email;
                    $source = 'payment_link';
                }
            }

            if (! $name && Schema::hasTable('ebook_orders')) {
                $order = EbookOrder::where(function ($q) use ($payment) {
                    if ($payment->invoice_number) $q->orWhere('invoice_number', $payment->invoice_number);
                    if ($payment->transaction_id) $q->orWhere('transaction_id', $payment->transaction_id);
                })->first();

                if ($order) {
                    $name   = trim($order->first_name . ' ' . $order->last_name) ?: null;
                    $email  = $order->email;
                    $source = 'ebook';
                }
            }
        }

        // Nothing local knows this charge — it was taken inside Authorize.Net.
        if (! $name && $details) {
            $fromCard = trim(data_get($details, 'billTo.firstName', '') . ' ' . data_get($details, 'billTo.lastName', ''));
            $fromCard = $fromCard !== '' ? $fromCard : null;
            $gatewayEmail = data_get($details, 'customer.email') ?: null;

            if ($fromCard || $gatewayEmail) {
                $name   = $fromCard;
                $email  = $gatewayEmail;
                $source = 'gateway';
            }
        }

        if ($name || $email) {
            $payment->forceFill(array_filter([
                'customer_name'  => $name,
                'customer_email' => $email,
                'source'         => $source,
            ]));

            if ($save) {
                $payment->save();
            }
        }

        return $payment;
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
    public function record(array $attrs, ?Subscription $subscription, CarbonInterface $chargedAt, ?array $details = null): ?Payment
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

            $payment->setRelation('subscription', $subscription);
            $this->attribute($payment, $details);

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
