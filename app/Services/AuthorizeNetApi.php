<?php

namespace App\Services;

use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Thin read-only client for the Authorize.Net JSON API.
 *
 * Webhooks alone can't tell us who a recurring charge belongs to — the
 * authcapture payload carries no subscription id (and often no invoice
 * number). Looking the transaction up gives us the ARB subscription id,
 * the customer email and the payment number, which is how a rebill gets
 * attached to the right client.
 */
class AuthorizeNetApi
{
    private const PAGE_LIMIT = 1000;   // Authorize.Net max per page
    private const MAX_BATCH_DAYS = 31; // Authorize.Net max settled-batch window

    private ?string $lastError = null;

    /** Why the most recent call came back empty, if it did. */
    public function lastError(): ?string
    {
        return $this->lastError;
    }

    public function isConfigured(): bool
    {
        return (string) config('services.authorize_net.api_login_id') !== ''
            && (string) config('services.authorize_net.transaction_key') !== '';
    }

    /** Full details for one transaction (type, amount, subscription, customer). */
    public function transactionDetails(string $transactionId): ?array
    {
        $data = $this->post(['getTransactionDetailsRequest' => [
            'merchantAuthentication' => $this->auth(),
            'transId'                => $transactionId,
        ]]);

        return $data ? ($data['transaction'] ?? null) : null;
    }

    /**
     * Settled batch ids between two dates, walking the range in <=31 day chunks.
     *
     * @return array<int, string>
     */
    public function settledBatchIds(CarbonInterface $from, CarbonInterface $to): array
    {
        $ids    = [];
        $cursor = $from->copy();

        while ($cursor->lessThanOrEqualTo($to)) {
            $chunkEnd = $cursor->copy()->addDays(self::MAX_BATCH_DAYS - 1);
            if ($chunkEnd->greaterThan($to)) {
                $chunkEnd = $to->copy();
            }

            $data = $this->post(['getSettledBatchListRequest' => [
                'merchantAuthentication' => $this->auth(),
                'includeStatistics'      => false,
                'firstSettlementDate'    => $cursor->copy()->startOfDay()->format('Y-m-d\TH:i:s\Z'),
                'lastSettlementDate'     => $chunkEnd->copy()->endOfDay()->format('Y-m-d\TH:i:s\Z'),
            ]]);

            foreach (data_get($data, 'batchList', []) as $batch) {
                if (! empty($batch['batchId'])) {
                    $ids[] = (string) $batch['batchId'];
                }
            }

            $cursor = $chunkEnd->copy()->addDay();
        }

        return array_values(array_unique($ids));
    }

    /**
     * Every transaction in a settled batch (summaries — transId, amount,
     * invoice, subscription id + payNum).
     *
     * @return array<int, array>
     */
    public function batchTransactions(string $batchId): array
    {
        $all    = [];
        $offset = 1;

        do {
            // Key order matters: Authorize.Net converts this JSON to XML and
            // validates it against a schema where `sorting` precedes `paging`.
            // Reversed, every call fails with E00003 and returns nothing.
            $data = $this->post(['getTransactionListRequest' => [
                'merchantAuthentication' => $this->auth(),
                'batchId'                => $batchId,
                'sorting'                => ['orderBy' => 'submitTimeUTC', 'orderDescending' => false],
                'paging'                 => ['limit' => (string) self::PAGE_LIMIT, 'offset' => (string) $offset],
            ]]);

            $page = data_get($data, 'transactions', []);
            // A single transaction can come back as an object rather than a list.
            if ($page && array_keys($page) !== range(0, count($page) - 1)) {
                $page = [$page];
            }

            foreach ($page as $txn) {
                $all[] = $txn;
            }

            $offset++;
        } while (count($page) === self::PAGE_LIMIT);

        return $all;
    }

    private function auth(): array
    {
        return [
            'name'           => (string) config('services.authorize_net.api_login_id'),
            'transactionKey' => (string) config('services.authorize_net.transaction_key'),
        ];
    }

    private function endpoint(): string
    {
        return config('services.authorize_net.environment') === 'sandbox'
            ? 'https://apitest.authorize.net/xml/v1/request.api'
            : 'https://api.authorize.net/xml/v1/request.api';
    }

    private function post(array $payload): ?array
    {
        $this->lastError = null;

        if (! $this->isConfigured()) {
            $this->lastError = 'Authorize.Net API credentials are missing';
            Log::warning('[AuthNetApi] credentials missing — skipping lookup');
            return null;
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            // Kept short: this runs inside the webhook request, which
            // Authorize.Net expects us to answer quickly.
            ])->timeout(10)->post($this->endpoint(), $payload);

            // Authorize.Net prefixes its JSON with a BOM.
            $raw  = preg_replace('/^\xEF\xBB\xBF/', '', $response->body());
            $data = json_decode(trim($raw), true);

            if (! is_array($data)) {
                $this->lastError = 'Unreadable response (HTTP ' . $response->status() . ')';
                Log::warning('[AuthNetApi] unreadable response', ['status' => $response->status()]);
                return null;
            }

            if (data_get($data, 'messages.resultCode') !== 'Ok') {
                $this->lastError = trim(sprintf('%s %s',
                    data_get($data, 'messages.message.0.code', ''),
                    data_get($data, 'messages.message.0.text', 'API error')
                ));
                Log::warning('[AuthNetApi] API error', [
                    'request' => array_key_first($payload),
                    'text'    => $this->lastError,
                ]);
                return null;
            }

            return $data;
        } catch (\Throwable $e) {
            $this->lastError = $e->getMessage();
            Log::error('[AuthNetApi] request failed', [
                'request' => array_key_first($payload),
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
