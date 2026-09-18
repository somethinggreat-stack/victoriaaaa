<?php

namespace App\Http\Controllers;

use App\Models\ApexRetryJob;
use App\Models\BurgundyClient;
use App\Models\OnboardingSubmission;
use App\Services\ApexClient;
use App\Services\BurgundyClientMatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Burgundy's onboarding form — the second half of the handoff.
 *
 * One submission feeds both systems:
 *   • Victoria's partnership dashboard (this database)
 *   • The Apex operations dashboard (POST, under BURGUNDY's intake key)
 *
 * Separate from Victoria's /onboarding so the branding, the client matching
 * and — critically — the Apex key are Burgundy's, not Victoria's.
 */
class BurgundyOnboardingController extends Controller
{
    public const PARTNER = 'burgundy';

    private const SUFFIXES = ['None', 'Jr.', 'Sr.', 'I', 'II', 'III', 'IV', 'V'];

    public function show(Request $request)
    {
        // Prefill from checkout when we have it. The form stays open to direct
        // visitors too: with a shared payment link, people close the tab and come
        // back, and a paid client must never be locked out of onboarding.
        $customer = (array) session('burgundy_customer', []);

        return view('burgundy.onboarding', [
            'prefill'       => $customer,
            'justPaid'      => (bool) session('burgundy_paid'),
            'signedContract'=> (bool) session('burgundy_agreement_signed'),
            'suffixes'      => self::SUFFIXES,
        ]);
    }

    public function submit(Request $request, BurgundyClientMatcher $matcher)
    {
        $phoneDigits = preg_replace('/\D+/', '', (string) $request->input('phone', ''));
        if (strlen($phoneDigits) === 11 && str_starts_with($phoneDigits, '1')) {
            $phoneDigits = substr($phoneDigits, 1);
        }
        $request->merge(['phone' => $phoneDigits]);
        $request->merge(['ssn' => preg_replace('/\D+/', '', (string) $request->input('ssn', ''))]);

        $validated = $request->validate([
            'firstname'                         => ['required', 'string', 'max:100', 'regex:/^[\pL\s\-\'.]+$/u'],
            'middlename'                        => ['nullable', 'string', 'max:100'],
            'lastname'                          => ['required', 'string', 'max:100', 'regex:/^[\pL\s\-\'.]+$/u'],
            'suffix'                            => ['nullable', 'string', 'in:' . implode(',', self::SUFFIXES)],
            'email'                             => ['required', 'email:rfc', 'max:255'],
            'phone'                             => ['required', 'string', 'digits:10'],
            'birth_date'                        => ['required', 'date_format:m/d/Y', 'before:today', 'after:1900-01-01'],
            'ssn'                               => ['required', 'string', 'digits:9'],
            'street_address'                    => ['required', 'string', 'max:255'],
            'address_line2'                     => ['nullable', 'string', 'max:100'],
            'city'                              => ['required', 'string', 'max:100'],
            'state'                             => ['required', 'string', 'size:2'],
            'zip'                               => ['required', 'string', 'regex:/^\d{5}(-\d{4})?$/'],
            'credit_monitoring_email'           => ['required', 'email:rfc', 'max:255'],
            'credit_monitoring_password'        => ['required', 'string', 'max:255'],
            'credit_monitoring_security_answer' => ['nullable', 'string', 'max:255'],
            'drivers_license'                   => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'],
            'proof_of_address'                  => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'],
            'ssn_card'                          => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'],
        ], [
            'firstname.regex'        => 'First name may only contain letters, spaces, apostrophes and hyphens.',
            'lastname.regex'         => 'Last name may only contain letters, spaces, apostrophes and hyphens.',
            'phone.digits'           => 'Phone number must be exactly 10 digits (or 11 if you include the leading 1).',
            'ssn.digits'             => 'SSN must be 9 digits.',
            'birth_date.before'      => 'Date of birth must be in the past.',
            'birth_date.date_format' => 'Date of birth must be a valid mm/dd/yyyy date.',
            'zip.regex'              => 'Enter a valid US zip code (e.g. 90210 or 90210-1234).',
            'credit_monitoring_email.email' => 'Enter a valid credit-monitoring login email.',
            'drivers_license.required'      => "Please upload your driver's license.",
            'proof_of_address.required'     => 'Please upload your proof of address.',
        ]);

        $dob        = \Carbon\Carbon::createFromFormat('m/d/Y', $validated['birth_date'])->toDateString();
        $suffix     = ($validated['suffix'] ?? null) === 'None' ? null : ($validated['suffix'] ?? null);
        $fullStreet = trim($validated['street_address'] . (! empty($validated['address_line2']) ? ', ' . $validated['address_line2'] : ''));

        // ── Who is this? ──────────────────────────────────────────────────────
        // Prefer the client the checkout already identified; fall back to matching
        // on what they typed, which is what makes the form work for someone who
        // came back later in a fresh browser session.
        $client = null;
        if ($clientId = session('burgundy_client_id')) {
            $client = BurgundyClient::find($clientId);
        }
        if (! $client) {
            $client = $matcher->matchOrFlag([
                'first_name' => $validated['firstname'],
                'last_name'  => $validated['lastname'],
                'email'      => $validated['email'],
                'phone'      => $validated['phone'],
                'zip'        => $validated['zip'],
            ], 'onboarding');
        }

        // ── Persist locally. Documents and the credit-monitoring password are
        //    forwarded to Apex only, never stored here. ─────────────────────────
        $submission = null;
        try {
            $submission = OnboardingSubmission::create([
                'partner'        => self::PARTNER,
                'firstname'      => $validated['firstname'],
                'lastname'       => $validated['lastname'],
                'middlename'     => $validated['middlename'] ?? null,
                'suffix'         => $suffix,
                'email'          => $validated['email'],
                'phone'          => $validated['phone'],
                'street_address' => $fullStreet,
                'city'           => $validated['city'],
                'state'          => $validated['state'],
                'zip'            => $validated['zip'],
                'ssn'            => $validated['ssn'],   // mutator encrypts + keeps last4
                'birth_date'     => $dob,
                'ip'             => $request->ip(),
                'user_agent'     => substr((string) $request->userAgent(), 0, 512),
            ]);
        } catch (\Throwable $e) {
            Log::error('[Burgundy] Onboarding local save failed (continuing to Apex)', ['error' => $e->getMessage()]);
        }

        // ── Mark the client onboarded ─────────────────────────────────────────
        if ($client) {
            $client->update([
                'onboarding_status'        => 'complete',
                'onboarding_submission_id' => $submission?->id,
                'client_status'            => $client->client_status === 'cancelled' ? 'cancelled' : 'active',
                'birth_date'               => $client->birth_date ?: $dob,
                'ssn_last4'                => $client->ssn_last4 ?: substr($validated['ssn'], -4),
            ]);
            $client->rememberIdentifier('email', $validated['email']);
            $client->rememberIdentifier('phone', $validated['phone']);
            $client->logEvent('onboarded', 'Onboarding form completed.');
        }

        // ── Forward to Apex under Burgundy's key ──────────────────────────────
        if (! config('services.apex.enabled')) {
            Log::info('[Burgundy] APEX_ENABLED is false — not forwarded.', ['submission_id' => $submission?->id]);
            $this->recordOutcome($submission, $client, 'pending', null, 'APEX_ENABLED is false.');

            return $this->done($validated['firstname']);
        }

        $result = $this->forwardToApex($validated, $dob, $request);

        $this->recordOutcome(
            $submission,
            $client,
            $result['ok'] ? 'sent' : 'failed',
            $result['id'] ?? null,
            substr((string) ($result['raw'] ?? $result['message'] ?? ''), 0, 2000),
        );

        if (! $result['ok']) {
            // Delivery to Apex is our problem, not theirs. Queue it, show success,
            // and never make a client redo a form full of document uploads over an
            // issue on our side.
            Log::warning('[Burgundy] Apex forward failed — queued for retry', [
                'status'  => $result['status'] ?? null,
                'message' => $result['message'] ?? null,
            ]);
            $this->storeRetryJob($validated, $dob, $request, $submission, $result);
        }

        return $this->done($validated['firstname']);
    }

    private function forwardToApex(array $v, string $dob, Request $request): array
    {
        $apex   = app(ApexClient::class);
        $fields = $apex->buildFields($v, $dob);

        $files = [];
        foreach (['drivers_license', 'proof_of_address', 'ssn_card'] as $name) {
            if ($request->hasFile($name)) {
                $f = $request->file($name);
                $files[$name] = [
                    'stream'   => fopen($f->getRealPath(), 'r'),
                    'filename' => $f->getClientOriginalName(),
                ];
            }
        }

        return $apex->post($fields, $files, self::PARTNER);
    }

    private function storeRetryJob(array $v, string $dob, Request $request, ?OnboardingSubmission $submission, array $result): void
    {
        try {
            if (! Schema::hasTable('apex_retry_jobs')) {
                Log::warning('[Burgundy] apex_retry_jobs missing — failed submission NOT stored.', ['email' => $v['email'] ?? null]);

                return;
            }

            $dir   = 'apex-retry/' . Str::uuid();
            $paths = [];
            foreach (['drivers_license', 'proof_of_address', 'ssn_card'] as $name) {
                if ($request->hasFile($name)) {
                    $paths[$name] = $request->file($name)->store($dir, ApexRetryJob::DISK);
                }
            }

            ApexRetryJob::create([
                'partner'                  => self::PARTNER,
                'onboarding_submission_id' => $submission?->id,
                'client_name'              => trim(($v['firstname'] ?? '') . ' ' . ($v['lastname'] ?? '')),
                'email'                    => $v['email'] ?? null,
                'payload_encrypted'        => json_encode([
                    'v'   => Arr::except($v, ['drivers_license', 'proof_of_address', 'ssn_card']),
                    'dob' => $dob,
                ]),
                'drivers_license_path'  => $paths['drivers_license'] ?? null,
                'proof_of_address_path' => $paths['proof_of_address'] ?? null,
                'ssn_card_path'         => $paths['ssn_card'] ?? null,
                'status'                => 'pending',
                'attempts'              => 1,
                'last_error'            => substr((string) ($result['message'] ?? ('HTTP ' . ($result['status'] ?? '?'))), 0, 1000),
                'last_attempt_at'       => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error('[Burgundy] Failed to store Apex retry job', ['error' => $e->getMessage()]);
        }
    }

    private function recordOutcome(?OnboardingSubmission $submission, ?BurgundyClient $client, string $status, $id, string $response): void
    {
        try {
            $submission?->update([
                'crc_status'   => $status,
                'crc_id'       => $id ? (string) $id : null,
                'crc_response' => $response,
            ]);

            $client?->update([
                'apex_status' => $status,
                'apex_id'     => $id ? (string) $id : null,
            ]);

            if ($client && $status === 'sent') {
                $client->logEvent('apex_sent', 'Delivered to the Apex operations dashboard.');
            } elseif ($client && $status === 'failed') {
                $client->logEvent('apex_failed', 'Could not deliver to Apex — queued for retry.');
            }
        } catch (\Throwable $e) {
            Log::error('[Burgundy] Failed to record Apex outcome', ['error' => $e->getMessage()]);
        }
    }

    private function done(string $firstName)
    {
        session()->forget(['burgundy_paid', 'burgundy_client_id', 'burgundy_invoice', 'burgundy_customer', 'burgundy_agreement_signed']);

        return redirect()
            ->route('burgundy.onboarding.show')
            ->with('success', true)
            ->with('client_name', $firstName);
    }
}
