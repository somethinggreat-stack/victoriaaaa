<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApexRetryJob;
use App\Models\OnboardingSubmission;
use App\Services\ApexClient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ApexRetryController extends Controller
{
    /** How many jobs a single "retry all" pass will attempt (avoids timeouts). */
    private const BATCH = 20;

    public function index()
    {
        if (! Schema::hasTable('apex_retry_jobs')) {
            return view('admin.apex-retries', ['needsSetup' => true, 'pending' => null, 'done' => null, 'kpis' => null]);
        }

        return view('admin.apex-retries', [
            'needsSetup' => false,
            'pending'    => ApexRetryJob::where('status', 'pending')->latest()->paginate(25, ['*'], 'pending'),
            'done'       => ApexRetryJob::where('status', 'succeeded')->latest('succeeded_at')->paginate(10, ['*'], 'done'),
            'kpis'       => [
                'pending'   => ApexRetryJob::where('status', 'pending')->count(),
                'succeeded' => ApexRetryJob::where('status', 'succeeded')->count(),
            ],
        ]);
    }

    public function retry(ApexRetryJob $apexRetry, ApexClient $apex)
    {
        [$ok, $message] = $this->attempt($apexRetry, $apex);

        return back()->with($ok ? 'success' : 'error', $message);
    }

    public function retryAll(ApexClient $apex)
    {
        if (! $apex->isEnabled()) {
            return back()->with('error', 'Apex forwarding is disabled (APEX_ENABLED). Enable it before retrying.');
        }

        $jobs      = ApexRetryJob::where('status', 'pending')->oldest()->limit(self::BATCH)->get();
        $succeeded = 0;
        $failed    = 0;

        foreach ($jobs as $job) {
            [$ok] = $this->attempt($job, $apex);
            $ok ? $succeeded++ : $failed++;
        }

        $remaining = ApexRetryJob::where('status', 'pending')->count();
        $msg = "Retried {$jobs->count()}: {$succeeded} sent to Apex, {$failed} still failing."
            . ($remaining > 0 ? " {$remaining} still pending — run again to continue." : '');

        return back()->with($succeeded > 0 ? 'success' : 'error', $msg);
    }

    /**
     * Re-post one stored job to Apex. Returns [bool $ok, string $message].
     */
    private function attempt(ApexRetryJob $job, ApexClient $apex): array
    {
        if ($job->status === 'succeeded') {
            return [true, 'That submission was already sent to Apex.'];
        }
        if (! $apex->isEnabled()) {
            return [false, 'Apex forwarding is disabled (APEX_ENABLED). Enable it first.'];
        }

        $payload = $job->payload();
        $v       = $payload['v']  ?? [];
        $dob     = $payload['dob'] ?? null;

        if (empty($v) || ! $dob) {
            return [false, 'This job is missing its stored payload and cannot be retried.'];
        }

        $files = $job->fileStreams();
        // Apex requires these two documents; if they were purged/lost, don't waste a call.
        if (empty($files['drivers_license']) || empty($files['proof_of_address'])) {
            $job->update(['attempts' => $job->attempts + 1, 'last_attempt_at' => now(), 'last_error' => 'Stored documents are missing — cannot retry.']);
            return [false, 'The stored documents for this submission are missing — the client must re-submit.'];
        }

        $result = $apex->post($apex->buildFields($v, $dob), $files);

        // Close any file streams we opened.
        foreach ($files as $f) {
            if (is_resource($f['stream'] ?? null)) {
                @fclose($f['stream']);
            }
        }

        if ($result['ok']) {
            $job->update([
                'status'       => 'succeeded',
                'succeeded_at' => now(),
                'apex_id'      => $result['id'] ? (string) $result['id'] : null,
                'attempts'     => $job->attempts + 1,
                'last_attempt_at' => now(),
                'last_error'   => null,
            ]);
            $job->purgeFiles();

            // Reflect success on the linked onboarding row.
            if ($job->onboarding_submission_id) {
                try {
                    OnboardingSubmission::where('id', $job->onboarding_submission_id)
                        ->update(['crc_status' => 'sent', 'crc_id' => $result['id'] ? (string) $result['id'] : null]);
                } catch (\Throwable $e) {
                    Log::warning('Retry succeeded but could not update onboarding row', ['error' => $e->getMessage()]);
                }
            }

            return [true, 'Sent to Apex successfully — the client now appears in New Clients.'];
        }

        $errText = $result['message'] ?? ('HTTP ' . ($result['status'] ?? '?'));
        if (($result['status'] ?? 0) === 422 && ! empty($result['errors'])) {
            $errText = 'Apex rejected the data: ' . collect($result['errors'])->flatten()->implode(' ');
        }

        $job->update([
            'attempts'        => $job->attempts + 1,
            'last_attempt_at' => now(),
            'last_error'      => substr($errText, 0, 1000),
        ]);

        return [false, 'Still failing: ' . $errText];
    }
}
