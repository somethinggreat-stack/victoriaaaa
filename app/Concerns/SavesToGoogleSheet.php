<?php

namespace App\Concerns;

use Illuminate\Support\Facades\Log;

/**
 * POSTs an associative array as a single row to the Google Apps Script
 * web-app webhook (services.google.sheets_webhook_url).
 *
 * Best-effort: failures are logged and swallowed so a sheet outage never
 * blocks a payment or a form submission. Include a `type` key in $data so
 * the Apps Script can route each submission to its own tab.
 */
trait SavesToGoogleSheet
{
    protected function saveToGoogleSheet(array $data): void
    {
        $url = config('services.google.sheets_webhook_url');
        $ref = $data['invoice'] ?? $data['type'] ?? $data['email'] ?? 'n/a';

        if (! $url) {
            Log::warning('[Sheets] GOOGLE_SHEETS_WEBHOOK_URL not set — skipping', ['ref' => $ref]);
            return;
        }

        try {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => json_encode($data),
                CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
                CURLOPT_TIMEOUT        => 15,
                CURLOPT_FOLLOWLOCATION => true,
            ]);
            $resp   = curl_exec($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            Log::info('[Sheets] Row saved', [
                'ref'         => $ref,
                'type'        => $data['type'] ?? null,
                'http_status' => $status,
                'response'    => $resp,
            ]);
        } catch (\Throwable $e) {
            Log::error('[Sheets] Save failed', [
                'ref'   => $ref,
                'type'  => $data['type'] ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
