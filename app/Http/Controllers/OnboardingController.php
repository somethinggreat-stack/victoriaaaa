<?php

namespace App\Http\Controllers;

use App\Models\OnboardingSubmission;
use App\Services\CreditRepairCloud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OnboardingController extends Controller
{
    public function show()
    {
        return view('onboarding');
    }

    public function submit(Request $request, CreditRepairCloud $crc)
    {
        // Normalise phone to digits; allow optional leading 1 (US country code).
        $phoneDigits = preg_replace('/\D+/', '', (string) $request->input('phone', ''));
        if (strlen($phoneDigits) === 11 && str_starts_with($phoneDigits, '1')) {
            $phoneDigits = substr($phoneDigits, 1);
        }
        $request->merge(['phone' => $phoneDigits]);

        // Normalise SSN to digits.
        $ssnDigits = preg_replace('/\D+/', '', (string) $request->input('ssn', ''));
        $request->merge(['ssn' => $ssnDigits]);

        $validated = $request->validate([
            'firstname'      => ['required', 'string', 'max:100', 'regex:/^[\pL\s\-\'.]+$/u'],
            'lastname'       => ['required', 'string', 'max:100', 'regex:/^[\pL\s\-\'.]+$/u'],
            'middlename'     => ['nullable', 'string', 'max:100'],
            'suffix'         => ['nullable', 'string', 'in:Jr,Sr,II,III,IV'],
            'email'          => ['required', 'email:rfc,dns', 'max:255'],
            'phone'          => ['required', 'string', 'digits:10'],
            'street_address' => ['nullable', 'string', 'max:255'],
            'city'           => ['nullable', 'string', 'max:100'],
            'state'          => ['nullable', 'string', 'size:2'],
            'zip'            => ['nullable', 'string', 'regex:/^\d{5}(-\d{4})?$/'],
            'ssn'            => ['required', 'string', 'digits:9'],
            'birth_date'     => ['required', 'date_format:m/d/Y', 'before:-18 years', 'after:1900-01-01'],
        ], [
            'firstname.regex'        => 'First name may only contain letters, spaces, apostrophes and hyphens.',
            'lastname.regex'         => 'Last name may only contain letters, spaces, apostrophes and hyphens.',
            'phone.digits'           => 'Phone number must be exactly 10 digits (or 11 if you include the leading 1).',
            'ssn.digits'             => 'SSN must be 9 digits.',
            'birth_date.before'      => 'You must be at least 18 to enroll.',
            'birth_date.date_format' => 'Date of birth must be a valid mm/dd/yyyy date.',
            'zip.regex'              => 'Enter a valid US zip code (e.g. 90210 or 90210-1234).',
            'email.email'            => 'Enter a valid, deliverable email address.',
        ]);

        // Persist locally first (encrypted SSN), then attempt CRC sync
        $submission = OnboardingSubmission::create([
            'firstname'      => $validated['firstname'],
            'lastname'       => $validated['lastname'],
            'middlename'     => $validated['middlename'] ?? null,
            'suffix'         => $validated['suffix']     ?? null,
            'email'          => $validated['email'],
            'phone'          => $validated['phone'],
            'street_address' => $validated['street_address'] ?? null,
            'city'           => $validated['city']           ?? null,
            'state'          => $validated['state']          ?? null,
            'zip'            => $validated['zip']            ?? null,
            'ssn'            => $validated['ssn'],          // mutator encrypts + extracts last4
            'birth_date'     => \Carbon\Carbon::createFromFormat('m/d/Y', $validated['birth_date'])->toDateString(),
            'ip'             => $request->ip(),
            'user_agent'     => substr((string) $request->userAgent(), 0, 512),
        ]);

        if (!$crc->isConfigured()) {
            Log::warning('CRC API credentials not configured; onboarding submission stored locally only.');
            $submission->update(['crc_status' => 'pending', 'crc_response' => 'CRC not configured']);
            return back()
                ->withInput()
                ->with('error', 'Our system is temporarily unavailable. Please email info@victoriousopportunities.com and we will onboard you manually.');
        }

        // CRC's ssno field only accepts last 4 digits; keep the full SSN in memo for the team.
        $payload = $validated;
        $payload['ssn'] = substr($validated['ssn'], -4);
        $payload['memo'] = sprintf(
            "Submitted via onboarding form at %s\nFull SSN on file: %s",
            now()->toDateTimeString(),
            $validated['ssn']
        );

        $result = $crc->insertClient($payload);

        // Record CRC sync outcome on the submission
        $submission->update([
            'crc_status'   => $result['ok'] ? 'sent' : 'failed',
            'crc_response' => substr((string) ($result['body'] ?? ''), 0, 2000),
        ]);

        if (!$result['ok']) {
            Log::error('CRC client creation failed', $result);
            return back()
                ->withInput()
                ->with('error', 'We could not finalize your account: ' . ($result['message'] ?? 'please try again or contact support.'));
        }

        return redirect()
            ->route('onboarding.show')
            ->with('success', true)
            ->with('client_name', $validated['firstname']);
    }
}
