<?php

namespace App\Http\Controllers;

use App\Models\OnboardingSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OnboardingController extends Controller
{
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

        $dob = \Carbon\Carbon::createFromFormat('m/d/Y', $validated['birth_date'])->toDateString();

        // ── Persist identity locally (best-effort). Per policy, the uploaded
        //    documents and the credit-monitoring password are NOT stored here —
        //    they are forwarded to Apex only. Any DB error must not block Apex.
        $suffix     = ($validated['suffix'] ?? null);
        $suffix     = ($suffix === 'None') ? null : $suffix;
        $submission = null;
        try {
            $submission = OnboardingSubmission::create([
                'firstname'      => $validated['firstname'],
                'lastname'       => $validated['lastname'],
                'middlename'     => $validated['middlename'] ?? null,
                'suffix'         => $suffix,
                'email'          => $validated['email'],
                'phone'          => $validated['phone'],
                'street_address' => trim($validated['street_address'] . (!empty($validated['address_line2']) ? ', ' . $validated['address_line2'] : '')),
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

        // 422 → surface the exact fields Apex rejected, mapped back to form names.
        if (($result['status'] ?? 0) === 422 && ! empty($result['errors'])) {
            Log::warning('Apex intake validation failed', ['errors' => $result['errors']]);

            return back()
                ->withInput($request->except(['ssn', 'credit_monitoring_password', 'drivers_license', 'proof_of_address', 'ssn_card']))
                ->withErrors($this->mapApexErrors($result['errors']))
                ->with('error', 'A few details need fixing before we can finish. Please review the highlighted fields and resubmit (you may need to re-select your documents).');
        }

        // 401 → key wrong/disabled; anything else → generic failure.
        Log::error('Apex intake forward failed', [
            'status'  => $result['status'] ?? null,
            'message' => $result['message'] ?? null,
        ]);

        return back()
            ->withInput($request->except(['ssn', 'credit_monitoring_password', 'drivers_license', 'proof_of_address', 'ssn_card']))
            ->with('error', 'We could not finish setting up your account right now. Please try again in a moment, or email support@victorialovecredit.com and we will complete it manually.');
    }

    /**
     * POST the submission to the Apex intake API as multipart/form-data.
     * Returns ['ok'=>bool, 'status'=>int, 'id'=>?int, 'errors'=>array, 'message'=>?string, 'raw'=>string].
     */
    private function forwardToApex(array $v, string $dob, Request $request): array
    {
        $url = config('services.apex.url');
        $key = config('services.apex.key');

        if (empty($key)) {
            return ['ok' => false, 'status' => 0, 'message' => 'Apex intake key not configured.', 'errors' => []];
        }

        // The /api/* path is blocked by Cloudflare/WAF for server-to-server callers
        // (403/406) before it reaches Apex's PHP. /partner-intake is the identical
        // handler that bypasses it. Rewrite defensively in case a stale APEX_API_URL
        // still points at /api/intake.
        if (str_contains($url, '/api/intake')) {
            $url = str_replace('/api/intake', '/partner-intake', $url);
        }

        // Text fields (map funnel → Apex names). credit_monitoring_name is locked
        // to "myfreescore"; the CM login email is sent as credit_monitoring_username.
        $fields = [
            'first_name'                 => $v['firstname'],
            'last_name'                  => $v['lastname'],
            'email'                      => $v['email'],
            'ssn'                        => $v['ssn'],
            'date_of_birth'              => $dob,                                   // YYYY-MM-DD
            'current_address'            => $v['street_address'],
            'city'                       => $v['city'],
            'state'                      => $v['state'],
            'zipcode'                    => substr(preg_replace('/\D+/', '', $v['zip']), 0, 5),
            'phone'                      => '+1' . $v['phone'],
            'credit_monitoring_name'     => 'myfreescore',
            'credit_monitoring_username' => $v['credit_monitoring_email'],
            'credit_monitoring_password' => $v['credit_monitoring_password'],
        ];

        if (! empty($v['middlename']))                            $fields['middle_name'] = $v['middlename'];
        if (! empty($v['suffix']) && $v['suffix'] !== 'None')     $fields['suffix'] = $v['suffix'];
        if (! empty($v['address_line2']))                         $fields['address_line2'] = $v['address_line2'];
        if (! empty($v['credit_monitoring_security_answer']))     $fields['credit_monitoring_security_answer'] = $v['credit_monitoring_security_answer'];

        // A real User-Agent is required — the default Guzzle UA gets bot-blocked
        // at Cloudflare's edge (403). Accept JSON so errors come back as JSON.
        $http = Http::timeout(60)->withHeaders([
            'X-Intake-Key' => $key,
            'User-Agent'   => 'VictoriaFunnel/1.0 (+https://victorialovecredit.com)',
            'Accept'       => 'application/json',
        ]);

        // Required files + optional SSN card. Attaching makes the request multipart.
        $dl  = $request->file('drivers_license');
        $poa = $request->file('proof_of_address');
        $http = $http->attach('drivers_license', fopen($dl->getRealPath(), 'r'), $dl->getClientOriginalName());
        $http = $http->attach('proof_of_address', fopen($poa->getRealPath(), 'r'), $poa->getClientOriginalName());
        if ($request->hasFile('ssn_card')) {
            $card = $request->file('ssn_card');
            $http = $http->attach('ssn_card', fopen($card->getRealPath(), 'r'), $card->getClientOriginalName());
        }

        try {
            $response = $http->post($url, $fields);
        } catch (\Throwable $e) {
            Log::error('Apex intake request threw', ['error' => $e->getMessage()]);
            return ['ok' => false, 'status' => 0, 'message' => 'Network error contacting Apex.', 'errors' => []];
        }

        $status = $response->status();
        $body   = $response->json() ?? [];
        $raw    = $response->body();

        if ($status === 201 && ($body['ok'] ?? false)) {
            return ['ok' => true, 'status' => 201, 'id' => $body['id'] ?? null, 'errors' => [], 'raw' => $raw];
        }
        if ($status === 401) {
            return ['ok' => false, 'status' => 401, 'message' => $body['message'] ?? 'Invalid or missing intake key.', 'errors' => [], 'raw' => $raw];
        }
        if ($status === 422) {
            return ['ok' => false, 'status' => 422, 'message' => 'Validation failed at Apex.', 'errors' => $body['errors'] ?? [], 'raw' => $raw];
        }

        return ['ok' => false, 'status' => $status, 'message' => $body['message'] ?? 'Unexpected Apex response.', 'errors' => [], 'raw' => $raw];
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
