<?php

namespace Tests\Feature;

use App\Models\ApexRetryJob;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Dismissing a queued Apex job.
 *
 * The queue accumulated test submissions, and "Retry all pending" would have
 * created every one of them as a real client in Apex. Dismissing takes a row
 * out of that blast radius for good.
 */
class ApexRetryDismissTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Victoria', 'password' => 'secret-password'],
        );
    }

    private function job(array $overrides = []): ApexRetryJob
    {
        Storage::fake(ApexRetryJob::DISK);

        $dl  = UploadedFile::fake()->image('dl.png')->store('apex-retry/x', ApexRetryJob::DISK);
        $poa = UploadedFile::fake()->image('poa.png')->store('apex-retry/x', ApexRetryJob::DISK);

        return ApexRetryJob::create(array_merge([
            'partner'               => 'victoria',
            'client_name'           => 'Test Test',
            'email'                 => 'test@creditrepair.com',
            'payload_encrypted'     => json_encode(['v' => ['firstname' => 'Test'], 'dob' => '1990-01-01']),
            'drivers_license_path'  => $dl,
            'proof_of_address_path' => $poa,
            'status'                => 'pending',
            'attempts'              => 1,
            'last_error'            => 'Unexpected Apex response (HTTP 406).',
        ], $overrides));
    }

    public function test_dismissing_takes_the_job_out_of_the_queue(): void
    {
        $job = $this->job();

        $this->actingAs($this->admin())
            ->post(route('admin.apex-retries.dismiss', $job))
            ->assertRedirect();

        $this->assertSame('dismissed', $job->fresh()->status);
    }

    public function test_dismissing_deletes_the_stored_documents(): void
    {
        $job = $this->job();
        $dl  = $job->drivers_license_path;

        Storage::disk(ApexRetryJob::DISK)->assertExists($dl);

        $this->actingAs($this->admin())->post(route('admin.apex-retries.dismiss', $job));

        // A client's ID and proof of address should not linger on disk once we
        // have decided the job will never be sent.
        Storage::disk(ApexRetryJob::DISK)->assertMissing($dl);
        $this->assertNull($job->fresh()->drivers_license_path);
    }

    public function test_retry_all_skips_a_dismissed_job(): void
    {
        config([
            'services.apex.enabled' => true,
            'services.apex.url'     => 'https://apex.test/partner-intake',
            'services.apex.key'     => 'k',
        ]);
        Http::fake(['apex.test/*' => Http::response(['ok' => true, 'id' => 9], 201)]);

        $job = $this->job();
        $this->actingAs($this->admin())->post(route('admin.apex-retries.dismiss', $job));

        $this->actingAs($this->admin())->post(route('admin.apex-retries.retry-all'));

        // This is the whole point: a dismissed test row must never be created
        // as a real client in Apex.
        Http::assertNothingSent();
        $this->assertSame('dismissed', $job->fresh()->status);
    }

    public function test_a_dismissed_job_cannot_be_retried_individually(): void
    {
        config([
            'services.apex.enabled' => true,
            'services.apex.url'     => 'https://apex.test/partner-intake',
            'services.apex.key'     => 'k',
        ]);
        Http::fake(['apex.test/*' => Http::response(['ok' => true, 'id' => 9], 201)]);

        $job = $this->job();
        $this->actingAs($this->admin())->post(route('admin.apex-retries.dismiss', $job));

        $this->actingAs($this->admin())->post(route('admin.apex-retries.retry', $job));

        // Its documents are gone, so the retry refuses instead of sending a
        // half-built payload Apex would reject anyway.
        Http::assertNothingSent();
    }

    public function test_an_already_delivered_job_cannot_be_dismissed(): void
    {
        $job = $this->job(['status' => 'succeeded', 'apex_id' => '4242']);

        $this->actingAs($this->admin())
            ->post(route('admin.apex-retries.dismiss', $job))
            ->assertSessionHas('error');

        $this->assertSame('succeeded', $job->fresh()->status);
    }

    public function test_the_page_counts_dismissed_jobs_rather_than_hiding_them(): void
    {
        $job = $this->job();
        $this->actingAs($this->admin())->post(route('admin.apex-retries.dismiss', $job));

        $this->actingAs($this->admin())
            ->get(route('admin.apex-retries'))
            ->assertOk()
            ->assertSee('Dismissed');
    }
}
