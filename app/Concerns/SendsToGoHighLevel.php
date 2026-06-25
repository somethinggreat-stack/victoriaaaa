<?php

namespace App\Concerns;

use Illuminate\Support\Facades\Log;

/**
 * POSTs a lead payload to the GoHighLevel inbound webhook
 * (services.ghl.webhook_url).
 *
 * Best-effort: failures are logged and swallowed so a GHL outage never blocks
 * a form submission. Include a `type` key in $data so the GHL workflow can
 * tell which form the lead came from (tag / source / branch on it).
 */
trait SendsToGoHighLevel
{
    protected function sendToGoHighLevel(array $data): void
    {
        $type = $data['type'] ?? null;
        $ref  = $data['email'] ?? $type ?? 'n/a';

        // Per-form webhook (one GHL workflow per form); fall back to a single
        // shared webhook if no type-specific URL is configured.
        $url = ($type ? config('services.ghl.webhooks.' . $type) : null)
            ?: config('services.ghl.webhook_url');

        if (! $url) {
            Log::warning('[GHL] No webhook configured — skipping', ['ref' => $ref, 'type' => $type]);
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

            Log::info('[GHL] Lead sent', [
                'ref'         => $ref,
                'type'        => $data['type'] ?? null,
                'http_status' => $status,
                'response'    => $resp,
            ]);
        } catch (\Throwable $e) {
            Log::error('[GHL] Send failed', [
                'ref'   => $ref,
                'type'  => $data['type'] ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
