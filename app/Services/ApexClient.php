<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Talks to the Apex Growth intake API. Shared by the live onboarding forward
 * and the admin "retry" flow so both build the payload and post identically.
 */
class ApexClient
{
    public function isEnabled(): bool
    {
        return (bool) config('services.apex.enabled');
    }

    /**
     * Map funnel field names → Apex field names. `$v` uses the funnel's own keys
     * (firstname, lastname, credit_monitoring_email, …); `$dob` is YYYY-MM-DD.
     */
    public function buildFields(array $v, string $dob): array
    {
        $fields = [
            'first_name'                 => $v['firstname'],
            'last_name'                  => $v['lastname'],
            'email'                      => $v['email'],
            'ssn'                        => $v['ssn'],
            'date_of_birth'              => $dob,                                   // YYYY-MM-DD
            'current_address'            => $v['street_address'],
            'city'                       => $v['city'],
            'state'                      => $v['state'],
            'zipcode'                    => substr(preg_replace('/\D+/', '', (string) $v['zip']), 0, 5),
            'phone'                      => '+1' . preg_replace('/\D+/', '', (string) $v['phone']),
            'credit_monitoring_name'     => 'myfreescore',
            'credit_monitoring_username' => $v['credit_monitoring_email'],
            'credit_monitoring_password' => $v['credit_monitoring_password'],
        ];

        if (! empty($v['middlename']))                        $fields['middle_name'] = $v['middlename'];
        if (! empty($v['suffix']) && $v['suffix'] !== 'None') $fields['suffix'] = $v['suffix'];
        if (! empty($v['address_line2']))                     $fields['address_line2'] = $v['address_line2'];
        if (! empty($v['credit_monitoring_security_answer'])) $fields['credit_monitoring_security_answer'] = $v['credit_monitoring_security_answer'];

        return $fields;
    }

    /**
     * POST to Apex as multipart/form-data.
     *
     * @param  array  $fields  text fields (already Apex-named, from buildFields)
     * @param  array  $files   [apexName => ['stream' => resource, 'filename' => string]]
     *                         `drivers_license` and `proof_of_address` are required by Apex.
     * @return array  ['ok'=>bool, 'status'=>int, 'id'=>?int, 'errors'=>array, 'message'=>?string, 'raw'=>string]
     */
    public function post(array $fields, array $files): array
    {
        $url = (string) config('services.apex.url');
        $key = (string) config('services.apex.key');

        if ($key === '') {
            return ['ok' => false, 'status' => 0, 'message' => 'Apex intake key not configured.', 'errors' => [], 'raw' => ''];
        }

        // The /api/* path is blocked by Cloudflare/WAF for server-to-server callers
        // (403/406). /partner-intake is the identical handler that bypasses it.
        if (str_contains($url, '/api/intake')) {
            $url = str_replace('/api/intake', '/partner-intake', $url);
        }

        // A real User-Agent is required — the default Guzzle UA gets bot-blocked
        // at Cloudflare's edge (403).
        $http = Http::timeout(60)->withHeaders([
            'X-Intake-Key' => $key,
            'User-Agent'   => 'VictoriaFunnel/1.0 (+https://victorialovecredit.com)',
            'Accept'       => 'application/json',
        ]);

        foreach ($files as $name => $file) {
            if (! empty($file['stream'])) {
                $http = $http->attach($name, $file['stream'], $file['filename'] ?? $name);
            }
        }

        // Log context — field NAMES only (never SSN/password values).
        $ctx = [
            'url'           => $url,
            'client'        => ($fields['first_name'] ?? '') . ' ' . ($fields['last_name'] ?? ''),
            'email'         => $fields['email'] ?? null,
            'fields_sent'   => array_keys($fields),
            'files_sent'    => array_keys(array_filter($files, fn ($f) => ! empty($f['stream']))),
            'key_present'   => $key !== '',
            'key_last4'     => $key !== '' ? substr($key, -4) : null,
        ];

        try {
            $response = $http->post($url, $fields);
        } catch (\Throwable $e) {
            $this->log('error', 'Apex forward EXCEPTION', $ctx + ['error' => $e->getMessage()]);
            return ['ok' => false, 'status' => 0, 'message' => 'Network error contacting Apex.', 'errors' => [], 'raw' => ''];
        }

        $status = $response->status();
        $body   = $response->json() ?? [];
        $raw    = $response->body();

        $ctx += ['http_status' => $status, 'response_body' => mb_substr((string) $raw, 0, 3000)];

        if ($status === 201 && ($body['ok'] ?? false)) {
            $this->log('info', 'Apex forward OK', $ctx + ['apex_id' => $body['id'] ?? null]);
            return ['ok' => true, 'status' => 201, 'id' => $body['id'] ?? null, 'errors' => [], 'raw' => $raw];
        }

        // Any non-success → detailed error log so it can be diagnosed from apex.log.
        $this->log('error', 'Apex forward FAILED (HTTP ' . $status . ')', $ctx);

        if ($status === 401) {
            return ['ok' => false, 'status' => 401, 'message' => $body['message'] ?? 'Invalid or missing intake key.', 'errors' => [], 'raw' => $raw];
        }
        if ($status === 422) {
            return ['ok' => false, 'status' => 422, 'message' => 'Validation failed at Apex.', 'errors' => $body['errors'] ?? [], 'raw' => $raw];
        }

        return ['ok' => false, 'status' => $status, 'message' => $body['message'] ?? ('Unexpected Apex response (HTTP ' . $status . ').'), 'errors' => [], 'raw' => $raw];
    }

    /** Log to the dedicated apex channel, falling back to the default log. */
    private function log(string $level, string $message, array $context): void
    {
        try {
            Log::channel('apex')->{$level}($message, $context);
        } catch (\Throwable $e) {
            Log::{$level}($message, $context);
        }
    }
}
