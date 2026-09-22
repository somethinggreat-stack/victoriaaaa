<?php

namespace App\Http\Controllers;

use App\Models\ApexRetryJob;
use App\Models\OnboardingSubmission;
use App\Services\ApexClient;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

/**
 * Recovers an onboarding submission whose Apex forward failed, without making
 * the client fill the whole form again.
 *
 * Submissions from before 2026-08-08 were lost twice over: they failed on the
 * old multipart transport (the origin WAF answers it with 406), and the retry
 * queue that captures documents did not exist yet. What survives in
 * onboarding_submissions is everything EXCEPT the two things never persisted by
 * policy — the uploaded documents and the credit-monitoring login.
 *
 * Apex hard-requires exactly those (verified against its 422 response):
 *   credit_monitoring_username, credit_monitoring_password,
 *   drivers_license, proof_of_address
 *
 * So this asks the client for those four things only. Their name, address,
 * date of birth and SSN are read back from the record — in particular they are
 * never asked to type their Social Security number a second time.
 *
 * The link is a signed URL: no new column, no guessable token, and it expires.
 */
class FinishOnboardingController extends Controller
{
    /** How long a link Victoria sends stays usable. */
    public const LINK_DAYS = 30;

    public static function linkFor(OnboardingSubmission $submission): string
    {
        return URL::temporarySignedRoute(
            'onboarding.finish',
            now()->addDays(self::LINK_DAYS),
            ['submission' => $submission->id],
        );
    }

    public function show(Request $request, OnboardingSubmission $submission)
    {
        // `finished` covers the case where delivery failed on our side: the
        // client is done either way and must not be shown the form again.
        if ($request->session()->get('finished') || $submission->crc_status === 'sent') {
            return view('finish-onboarding', [
                'submission'  => $submission,
                'alreadyDone' => true,
                'needsSsn'    => false,
                'postUrl'     => '#',
                'brand'       => $this->brand($submission),
            ]);
        }

        return view('finish-onboarding', [
            'submission'  => $submission,
            'alreadyDone' => false,
            // Only ask for the SSN if we genuinely cannot read it back (for
            // example if APP_KEY was rotated since they submitted).
            'needsSsn'    => $submission->ssn === null,
            'postUrl'     => URL::temporarySignedRoute(
                'onboarding.finish.submit',
                now()->addDays(2),
                ['submission' => $submission->id],
            ),
            'brand'       => $this->brand($submission),
        ]);
    }

    public function submit(Request $request, OnboardingSubmission $submission)
    {
        if ($submission->crc_status === 'sent') {
            return redirect()->to(self::linkFor($submission));
        }

        $storedSsn = $submission->ssn;

        if ($storedSsn === null) {
            $request->merge(['ssn' => preg_replace('/\D+/', '', (string) $request->input('ssn', ''))]);
        }

        $validated = $request->validate([
            'credit_monitoring_email'           => ['required', 'email:rfc', 'max:255'],
            'credit_monitoring_password'        => ['required', 'string', 'max:255'],
            'credit_monitoring_security_answer' => ['nullable', 'string', 'max:255'],
            'drivers_license'                   => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'],
            'proof_of_address'                  => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'],
            'ssn_card'                          => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'],
            'ssn'                               => [$storedSsn === null ? 'required' : 'nullable', 'string', 'digits:9'],
        ], [
            'credit_monitoring_email.required'    => 'Enter the email you use to log in to your credit monitoring.',
            'credit_monitoring_password.required' => 'Enter your credit-monitoring password.',
            'drivers_license.required'            => "Please upload your driver's license.",
            'proof_of_address.required'           => 'Please upload your proof of address.',
            'drivers_license.max'                 => "Driver's license must be 10 MB or smaller.",
            'proof_of_address.max'                => 'Proof of address must be 10 MB or smaller.',
            'ssn.digits'                          => 'SSN must be 9 digits.',
        ]);

        $ssn = $storedSsn ?? $validated['ssn'];

        // Rebuild the funnel-shaped payload from what we already hold, so the
        // client re-supplies nothing that survived.
        $v = [
            'firstname'                         => $submission->firstname,
            'lastname'                          => $submission->lastname,
            'middlename'                        => $submission->middlename,
            'suffix'                            => $submission->suffix,
            'email'                             => $submission->email,
            'phone'                             => $submission->phone,
            'ssn'                               => $ssn,
            'street_address'                    => $submission->street_address,
            'city'                              => $submission->city,
            'state'                             => $submission->state,
            'zip'                               => $submission->zip,
            'credit_monitoring_email'           => $validated['credit_monitoring_email'],
            'credit_monitoring_password'        => $validated['credit_monitoring_password'],
            'credit_monitoring_security_answer' => $validated['credit_monitoring_security_answer'] ?? null,
        ];

        $dob = $submission->birth_date instanceof \DateTimeInterface
            ? $submission->birth_date->format('Y-m-d')
            : (string) $submission->birth_date;

        $partner = $submission->partner ?: 'victoria';

        if (! config('services.apex.enabled')) {
            Log::warning('[FinishOnboarding] APEX_ENABLED is false — storing for retry instead.', [
                'submission_id' => $submission->id,
            ]);
            $this->queueRetry($v, $dob, $request, $submission, ['message' => 'APEX_ENABLED is false.']);

            return $this->done($submission);
        }

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

        $result = $apex->post($fields, $files, $partner);

        foreach ($files as $f) {
            if (is_resource($f['stream'] ?? null)) {
                @fclose($f['stream']);
            }
        }

        try {
            $submission->update([
                'crc_status'   => $result['ok'] ? 'sent' : 'failed',
                'crc_id'       => ! empty($result['id']) ? (string) $result['id'] : null,
                'crc_response' => substr((string) ($result['raw'] ?? $result['message'] ?? ''), 0, 2000),
            ]);
        } catch (\Throwable $e) {
            Log::error('[FinishOnboarding] Could not record outcome', ['error' => $e->getMessage()]);
        }

        if ($result['ok']) {
            Log::info('[FinishOnboarding] Recovered submission delivered to Apex', [
                'submission_id' => $submission->id,
                'apex_id'       => $result['id'] ?? null,
                'partner'       => $partner,
            ]);
        } else {
            // This time the documents ARE captured, so the admin can one-click
            // retry rather than going back to the client again.
            Log::warning('[FinishOnboarding] Apex forward failed — queued for retry', [
                'submission_id' => $submission->id,
                'status'        => $result['status'] ?? null,
                'message'       => $result['message'] ?? null,
            ]);
            $this->queueRetry($v, $dob, $request, $submission, $result);
        }

        return $this->done($submission);
    }

    /**
     * Store the documents so a failure is recoverable without the client.
     * Best effort — never breaks the client's flow.
     */
    private function queueRetry(array $v, string $dob, Request $request, OnboardingSubmission $submission, array $result): void
    {
        try {
            if (! Schema::hasTable('apex_retry_jobs')) {
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
                'partner'                  => $submission->partner ?: 'victoria',
                'onboarding_submission_id' => $submission->id,
                'client_name'              => trim($submission->firstname . ' ' . $submission->lastname),
                'email'                    => $submission->email,
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
            Log::error('[FinishOnboarding] Failed to store retry job', ['error' => $e->getMessage()]);
        }
    }

    /**
     * The client always sees success. Delivery is our problem, and with the
     * documents now captured a failure is recoverable from the admin side.
     */
    private function done(OnboardingSubmission $submission)
    {
        return redirect()
            ->to(URL::temporarySignedRoute('onboarding.finish', now()->addMinutes(30), ['submission' => $submission->id]))
            ->with('finished', true);
    }

    private function brand(OnboardingSubmission $submission): string
    {
        return ($submission->partner ?? 'victoria') === 'burgundy' ? 'Burgundy' : 'Victoria Love';
    }
}
