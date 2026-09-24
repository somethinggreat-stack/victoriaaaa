<?php

namespace App\Services;

use App\Models\PaymentAgreement;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

/**
 * Opens and resolves the service agreement that follows a payment.
 *
 * Every path that takes money calls start() immediately after the charge
 * succeeds, then sends the client to the returned signing URL. The agreement
 * exists in a 'pending' state from that moment, so a client who closes the tab
 * before signing is visible and chaseable rather than silently missing.
 *
 * Amounts are always the ones the sale actually charged. A contract rendered
 * from a price list instead of from the transaction is how a client ends up
 * reading a different figure from the one on their statement.
 */
class ServiceAgreements
{
    /** How long a signing link stays valid. */
    public const LINK_DAYS = 30;

    /**
     * @param array{
     *   source: string, source_id: ?int, partner?: string,
     *   plan_key: string, plan_label: string, service_description?: ?string,
     *   charged_today: float, recurring_amount?: ?float, recurring_count?: ?int,
     *   client_name?: ?string, email?: ?string, client_phone?: ?string,
     *   invoice_number?: ?string, subscription_id?: ?int, next_url?: ?string
     * } $sale
     */
    public static function start(array $sale): ?PaymentAgreement
    {
        try {
            $today     = (float) $sale['charged_today'];
            $recurring = isset($sale['recurring_amount']) ? (float) $sale['recurring_amount'] : null;
            $count     = $sale['recurring_count'] ?? null;

            // What the client is committed to. With no fixed number of payments
            // only today's charge is committed, because they can cancel.
            $total = ($recurring && $count) ? $today + ($recurring * $count) : $today;

            return PaymentAgreement::create([
                'source'              => $sale['source'],
                'source_id'           => $sale['source_id'] ?? null,
                'partner'             => $sale['partner'] ?? 'victoria',
                'status'              => 'pending',
                'full_name'           => '',   // filled in when they actually sign
                'plan_key'            => $sale['plan_key'],
                'plan_label'          => $sale['plan_label'],
                'service_description' => $sale['service_description'] ?? null,
                'deposit_amount'      => number_format($today, 2, '.', ''),
                'installment_amount'  => $recurring !== null ? number_format($recurring, 2, '.', '') : null,
                'installment_count'   => $count,
                'recurring_interval'  => ($sale['recurring_interval'] ?? 'month') === 'week' ? 'week' : 'month',
                'total_amount'        => number_format($total, 2, '.', ''),
                'terms_version'       => ServiceAgreement::TERMS_VERSION,
                'next_url'            => $sale['next_url'] ?? null,
                'client_name'         => $sale['client_name'] ?? null,
                'client_phone'        => $sale['client_phone'] ?? null,
                'email'               => $sale['email'] ?? null,
                'invoice_number'      => $sale['invoice_number'] ?? null,
                'subscription_id'     => $sale['subscription_id'] ?? null,
            ]);
        } catch (\Throwable $e) {
            // The card has already been charged. Never fail the request over
            // bookkeeping — log it and let the client through.
            Log::error('[Agreement] Could not open agreement after payment', [
                'source'  => $sale['source'] ?? null,
                'invoice' => $sale['invoice_number'] ?? null,
                'error'   => $e->getMessage(),
            ]);

            return null;
        }
    }

    /** Signed, expiring link to the signing page. */
    public static function signingUrl(PaymentAgreement $agreement, ?int $days = null): string
    {
        return URL::temporarySignedRoute(
            'agreement.show',
            now()->addDays($days ?? self::LINK_DAYS),
            ['agreement' => $agreement->id],
        );
    }

    /**
     * The terms for rendering, built from what the agreement recorded at the
     * time of sale rather than from anything that could have changed since.
     */
    public static function saleFor(PaymentAgreement $agreement, ?string $name = null): array
    {
        return [
            'client_name'         => $name ?: ($agreement->full_name ?: $agreement->client_name ?: ''),
            'plan_label'          => $agreement->plan_label,
            'service_description' => $agreement->service_description,
            'charged_today'       => (float) $agreement->deposit_amount,
            'recurring_amount'    => $agreement->installment_amount !== null
                ? (float) $agreement->installment_amount
                : null,
            'recurring_count'     => $agreement->installment_count,
            'recurring_interval'  => $agreement->recurring_interval ?? 'month',
            'signed_on'           => $agreement->signed_at ?: $agreement->created_at,
        ];
    }
}
