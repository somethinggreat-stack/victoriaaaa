<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Services\AuthorizeNetApi;
use App\Services\PaymentSync;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

/**
 * Re-imports settled Authorize.Net transactions that never made it into the
 * dashboard — mainly ARB rebills, which the webhook used to discard because
 * they arrive with no invoice number.
 *
 *   php artisan payments:backfill-authnet --days=180 --dry
 *   php artisan payments:backfill-authnet --days=180
 *
 * Safe to run repeatedly: a transaction already on file is skipped.
 */
class BackfillAuthorizeNetPayments extends Command
{
    protected $signature = 'payments:backfill-authnet
        {--days=180 : How far back to look}
        {--from= : Start date YYYY-MM-DD (overrides --days)}
        {--to= : End date YYYY-MM-DD (defaults to today)}
        {--dry : Show what would be imported without saving anything}';

    protected $description = 'Import settled Authorize.Net transactions missing from the dashboard and attach them to the right client';

    public function handle(AuthorizeNetApi $api, PaymentSync $sync): int
    {
        if (! $api->isConfigured()) {
            $this->error('Authorize.Net API credentials are missing — set them in .env first.');
            return self::FAILURE;
        }

        $dry  = (bool) $this->option('dry');
        $from = $this->option('from')
            ? Carbon::parse($this->option('from'))->startOfDay()
            : now()->subDays((int) $this->option('days'))->startOfDay();
        $to = $this->option('to')
            ? Carbon::parse($this->option('to'))->endOfDay()
            : now()->endOfDay();

        $this->info(sprintf('%sScanning settled transactions %s → %s',
            $dry ? '[DRY RUN] ' : '', $from->toDateString(), $to->toDateString()));

        $batches = $api->settledBatchIds($from, $to);
        $this->line(sprintf('  %d settled batch(es) found.', count($batches)));

        $imported = 0;
        $skipped  = 0;
        $unmatched = 0;
        $failed   = 0;
        $rows     = [];
        $touched  = [];

        foreach ($batches as $batchId) {
            foreach ($api->batchTransactions($batchId) as $summary) {
                $transactionId = (string) ($summary['transId'] ?? '');
                if ($transactionId === '') {
                    continue;
                }

                // Already on file (under any type) — leave it alone.
                if ($sync->alreadyRecorded($transactionId)) {
                    $skipped++;
                    continue;
                }

                $details = $api->transactionDetails($transactionId);
                if (! $details) {
                    $failed++;
                    $this->warn("  Could not read transaction {$transactionId}");
                    continue;
                }
                usleep(150000); // stay well under the API rate limit

                $payNum     = data_get($details, 'subscription.payNum');
                $classified = $sync->classify(
                    data_get($details, 'transactionType'),
                    data_get($details, 'transactionStatus'),
                    $payNum
                );

                if (! $classified) {
                    $skipped++;
                    continue;
                }

                [$type, $status] = $classified;

                $invoice = data_get($details, 'order.invoiceNumber');
                $amount  = (float) (data_get($details, 'settleAmount') ?? data_get($details, 'authAmount') ?? 0);

                $chargedAt = now();
                if ($submitted = data_get($details, 'submitTimeUTC')) {
                    try {
                        $chargedAt = Carbon::parse($submitted);
                    } catch (\Throwable $e) {
                        // keep now()
                    }
                }

                // Refunds follow the transaction they reverse.
                $subscription = null;
                if (in_array($type, ['refund', 'void'], true) && ($refId = data_get($details, 'refTransId'))) {
                    $subscription = Payment::where('transaction_id', (string) $refId)
                        ->whereIn('type', ['initial', 'recurring'])
                        ->first()?->subscription;
                }

                $subscription = $subscription ?: $sync->subscriptionFor(
                    data_get($details, 'subscription.id'),
                    $invoice,
                    data_get($details, 'customer.email')
                );

                if (! $subscription) {
                    $unmatched++;
                }

                if (! $dry) {
                    $sync->record([
                        'transaction_id' => $transactionId,
                        'invoice_number' => $invoice,
                        'amount'         => abs($amount),
                        'type'           => $type,
                        'status'         => $status,
                        'event_type_raw' => 'backfill.authorize_net',
                        'raw_payload'    => ['backfill' => true, 'transaction' => $details],
                    ], $subscription, $chargedAt);
                }

                if ($subscription) {
                    $touched[$subscription->id] = true;
                }

                $imported++;
                $rows[] = [
                    $chargedAt->format('M j, Y'),
                    $type,
                    '$' . number_format(abs($amount), 2),
                    $subscription ? trim($subscription->first_name . ' ' . $subscription->last_name) : '— unmatched —',
                    $transactionId,
                ];
            }
        }

        if ($rows) {
            $this->newLine();
            $this->table(['Charged', 'Type', 'Amount', 'Client', 'Transaction'], $rows);
        }

        // ── Second pass: charges already on file but attached to nobody ──
        // (recorded before this fix, or when a lookup was unavailable).
        $relinked = $this->relinkOrphans($api, $sync, $from, $to, $dry, $touched);

        $this->newLine();
        $this->info(sprintf(
            '%s%d imported · %d relinked · %d already on file · %d could not be matched to a client · %d unreadable · %d subscription(s) updated',
            $dry ? '[DRY RUN — nothing saved] ' : '',
            $imported, $relinked, $skipped, $unmatched, $failed, count($touched)
        ));

        if ($dry && ($imported > 0 || $relinked > 0)) {
            $this->comment('Re-run without --dry to save these.');
        }

        return self::SUCCESS;
    }

    /**
     * Charges already on file but attached to no client — everything the old
     * webhook filed as "Unlinked". Looks each one up and attaches it.
     */
    private function relinkOrphans(AuthorizeNetApi $api, PaymentSync $sync, Carbon $from, Carbon $to, bool $dry, array &$touched): int
    {
        $orphans = Payment::whereNull('subscription_id')
            ->whereNotNull('transaction_id')
            ->where(function ($q) use ($from, $to) {
                $q->whereBetween('charged_at', [$from, $to])->orWhereNull('charged_at');
            })
            ->get();

        if ($orphans->isEmpty()) {
            return 0;
        }

        $this->newLine();
        $this->line(sprintf('  Re-checking %d charge(s) on file with no client attached...', $orphans->count()));

        $relinked = 0;
        $rows     = [];

        foreach ($orphans as $payment) {
            $details = $api->transactionDetails((string) $payment->transaction_id);
            if (! $details) {
                continue;
            }
            usleep(150000);

            $subscription = null;
            if (in_array($payment->type, ['refund', 'void'], true) && ($refId = data_get($details, 'refTransId'))) {
                $subscription = Payment::where('transaction_id', (string) $refId)
                    ->whereIn('type', ['initial', 'recurring'])
                    ->first()?->subscription;
            }

            $subscription = $subscription ?: $sync->subscriptionFor(
                data_get($details, 'subscription.id'),
                data_get($details, 'order.invoiceNumber') ?: $payment->invoice_number,
                data_get($details, 'customer.email')
            );

            if (! $subscription) {
                continue;
            }

            $chargedAt = $payment->charged_at;
            if ($submitted = data_get($details, 'submitTimeUTC')) {
                try {
                    $chargedAt = Carbon::parse($submitted);
                } catch (\Throwable $e) {
                    // keep what we had
                }
            }

            if (! $dry) {
                $payment->update([
                    'subscription_id' => $subscription->id,
                    'charged_at'      => $chargedAt,
                ]);

                // Repair a stale billing date, but leave dunning status alone —
                // an old charge says nothing about the card's state today.
                if (in_array($payment->type, ['initial', 'recurring'], true)) {
                    $sync->advanceBillingDate($subscription, $chargedAt);
                }
            }

            $touched[$subscription->id] = true;
            $relinked++;
            $rows[] = [
                $chargedAt?->format('M j, Y') ?? '—',
                $payment->type,
                '$' . number_format((float) $payment->amount, 2),
                trim($subscription->first_name . ' ' . $subscription->last_name),
                $payment->transaction_id,
            ];
        }

        if ($rows) {
            $this->table(['Charged', 'Type', 'Amount', 'Now attached to', 'Transaction'], $rows);
        }

        return $relinked;
    }
}
