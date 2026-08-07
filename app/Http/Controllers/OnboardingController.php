<?php

namespace App\Http\Controllers;

use App\Concerns\SavesToGoogleSheet;
use App\Concerns\SendsToGoHighLevel;
use App\Models\ApexRetryJob;
use App\Models\OnboardingSubmission;
use App\Services\ApexClient;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class OnboardingController extends Controller
{
    use SavesToGoogleSheet;
    use SendsToGoHighLevel;

    /** Suffix values accepted by Apex (and shown in the form dropdown). */
    private const SUFFIXES = ['None', 'Jr.', 'Sr.', 'I', 'II', 'III', 'IV', 'V'];

    /** Apex field name → onboarding form field name (for surfacing 422 errors). */
    private const APEX_TO_FORM = [
        'first_name'                        => 'firstname',
        'last_name'                         => 'lastname',
        'middle_name'                       => 'middlename',
        'suffix'                            => 'suffix',
        'email'                             => 'email',
        'phone'                             => 'phone',
        'ssn'                               => 'ssn',
        'date_of_birth'                     => 'birth_date',
        'current_address'                   => 'street_address',
        'address_line2'                     => 'address_line2',
        'city'                              => 'city',
        'state'                             => 'state',
        'zipcode'                           => 'zip',
        'credit_monitoring_name'            => 'credit_monitoring_provider',
        'credit_monitoring_username'        => 'credit_monitoring_email',
        'credit_monitoring_password'        => 'credit_monitoring_password',
        'credit_monitoring_security_answer' => 'credit_monitoring_security_answer',
        'drivers_license'                   => 'drivers_license',
        'proof_of_address'                  => 'proof_of_address',
        'ssn_card'                          => 'ssn_card',
    ];

    public function show()
    {
        return view('onboarding');
    }

    public function submit(Request $request)
    {
        // Normalise phone to digits; allow optional leading 1 (US country code).
        $phoneDigits = preg_replace('/\D+/', '', (string) $request->input('phone', ''));
        if (strlen($phoneDigits) === 11 && str_starts_with($phoneDigits, '1')) {
            $phoneDigits = substr($phoneDigits, 1);
        }
        $request->merge(['phone' => $phoneDigits]);

        // Normalise SSN to digits.
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
            'email.email'            => 'Enter a valid email address.',
            'credit_monitoring_email.email'    => 'Enter a valid credit-monitoring login email.',
            'drivers_license.required'         => "Please upload your driver's license.",
            'drivers_license.mimes'            => "Driver's license must be a PDF or image (pdf, jpg, png, webp).",
            'drivers_license.max'              => "Driver's license must be 10 MB or smaller.",
            'proof_of_address.required'        => 'Please upload your proof of address.',
            'proof_of_address.mimes'           => 'Proof of address must be a PDF or image (pdf, jpg, png, webp).',
            'proof_of_address.max'             => 'Proof of address must be 10 MB or smaller.',
            'ssn_card.mimes'                   => 'Social Security card must be a PDF or image (pdf, jpg, png, webp).',
            'ssn_card.max'                     => 'Social Security card must be 10 MB or smaller.',
        ]);

        $dob        = \Carbon\Carbon::createFromFormat('m/d/Y', $validated['birth_date'])->toDateString();
        $suffix     = ($validated['suffix'] ?? null);
        $suffix     = ($suffix === 'None') ? null : $suffix;
        $fullStreet = trim($validated['street_address'] . (! empty($validated['address_line2']) ? ', ' . $validated['address_line2'] : ''));

        // ── Save to Google Sheet + GoHighLevel (best effort). SSN is NEVER sent in
        //    full — last 4 only. These helpers swallow their own errors.
        $leadData = [
            'type'           => 'onboarding',
            'submitted_at'   => now()->toDateTimeString(),
            'firstname'      => $validated['firstname'],
            'lastname'       => $validated['lastname'],
            'middlename'     => $validated['middlename'] ?? '',
            'suffix'         => $suffix ?? '',
            'email'          => $validated['email'],
            'phone'          => $validated['phone'],
            'street_address' => $fullStreet,
            'city'           => $validated['city'],
            'state'          => $validated['state'],
            'zip'            => $validated['zip'],
            'ssn_last4'      => substr($validated['ssn'], -4),
            'birth_date'     => $validated['birth_date'],
            'ip_address'     => $request->ip(),
        ];
        $this->saveToGoogleSheet($leadData);
        $this->sendToGoHighLevel($leadData);

        // ── Persist identity locally (best-effort). Per policy, the uploaded
        //    documents and the credit-monitoring password are NOT stored here —
        //    they are forwarded to Apex only. Any DB error must not block Apex.
        $submission = null;
        try {
            $submission = OnboardingSubmission::create([
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
                'ssn'            => $validated['ssn'],   // mutator encrypts + extracts last4
                'birth_date'     => $dob,
                'ip'             => $request->ip(),
                'user_agent'     => substr((string) $request->userAgent(), 0, 512),
            ]);
        } catch (\Throwable $e) {
            Log::error('Onboarding local save failed (continuing to Apex forward)', ['error' => $e->getMessage()]);
        }

        // ── Forward to Apex (gated by APEX_ENABLED) ────────────────────────────
        if (! config('services.apex.enabled')) {
            Log::info('APEX_ENABLED is false — onboarding submission not forwarded.', ['submission_id' => $submission?->id]);
            $this->recordOutcome($submission, 'pending', null, 'APEX_ENABLED is false — not forwarded.');

            return $this->successRedirect($validated['firstname']);
        }

        $result = $this->forwardToApex($validated, $dob, $request);

        $this->recordOutcome(
            $submission,
            $result['ok'] ? 'sent' : 'failed',
            $result['id'] ?? null,
            substr((string) ($result['raw'] ?? $result['message'] ?? ''), 0, 2000)
        );

        if ($result['ok']) {
            return $this->successRedirect($validated['firstname']);
        }

        // Forward failed → the client's info + documents are saved locally and
        // queued for retry (admin → Apex Retries). Delivery to Apex is our
        // backend concern, so the client still sees success — we never make them
        // re-do the form or stare at an error over an issue on our side. Full
        // detail is in storage/logs/apex-*.log for us to fix + retry.
        Log::warning('Apex forward failed — saved + queued for retry', [
            'status'  => $result['status'] ?? null,
            'message' => $result['message'] ?? null,
        ]);
        $this->storeApexRetryJob($validated, $dob, $request, $submission, $result);

        return $this->successRedirect($validated['firstname']);
    }

    /**
     * POST the submission to the Apex intake API (via the shared ApexClient),
     * streaming the uploaded documents from the current request.
     */
    private function forwardToApex(array $v, string $dob, Request $request): array
    {
        $apex   = app(ApexClient::class);
        $fields = $apex->buildFields($v, $dob);

        $files = [];
        foreach (['drivers_license', 'proof_of_address', 'ssn_card'] as $name) {
            if ($request->hasFile($name)) {
                $f = $request->file($name);
                $files[$name] = ['stream' => fopen($f->getRealPath(), 'r'), 'filename' => $f->getClientOriginalName()];
            }
        }

        return $apex->post($fields, $files);
    }

    /**
     * Persist a failed forward so the admin can retry it later without losing the
     * client's documents. Stores the funnel payload (encrypted) + the uploaded
     * files on the private disk. Best-effort — never breaks the user flow.
     */
    private function storeApexRetryJob(array $v, string $dob, Request $request, ?OnboardingSubmission $submission, array $result): void
    {
        try {
            if (! Schema::hasTable('apex_retry_jobs')) {
                Log::warning('apex_retry_jobs table missing — failed Apex submission NOT stored for retry.', ['email' => $v['email'] ?? null]);
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
            Log::error('Failed to store Apex retry job', ['error' => $e->getMessage()]);
        }
    }

    /** Map Apex's error keys back to the funnel's form field names for display. */
    private function mapApexErrors(array $apexErrors): array
    {
        $mapped = [];
        foreach ($apexErrors as $apexField => $messages) {
            $formField = self::APEX_TO_FORM[$apexField] ?? $apexField;
            $mapped[$formField] = is_array($messages) ? implode(' ', $messages) : (string) $messages;
        }

        return $mapped;
    }

    /** Record the forward outcome on the local submission, reusing existing columns. */
    private function recordOutcome(?OnboardingSubmission $submission, string $status, $id, string $response): void
    {
        if (! $submission) {
            return;
        }
        try {
            $submission->update([
                'crc_status'   => $status,
                'crc_id'       => $id ? (string) $id : null,
                'crc_response' => $response,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to record Apex outcome on submission', ['error' => $e->getMessage()]);
        }
    }

    private function successRedirect(string $firstName)
    {
        return redirect()
            ->route('onboarding.show')
            ->with('success', true)
            ->with('client_name', $firstName);
    }
}
