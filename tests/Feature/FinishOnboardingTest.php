<?php

namespace Tests\Feature;

use App\Http\Controllers\FinishOnboardingController;
use App\Models\OnboardingSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

/**
 * Recovering an onboarding whose Apex forward failed, without making the client
 * fill the whole form in again.
 */
class FinishOnboardingTest extends TestCase
{
    use RefreshDatabase;

    private function failedSubmission(array $overrides = []): OnboardingSubmission
    {
        return OnboardingSubmission::create(array_merge([
            'partner'        => 'victoria',
            'firstname'      => 'Lanique',
            'lastname'       => 'Graham',
            'email'          => 'lanique@example.com',
            'phone'          => '4692150384',
            'street_address' => '305 W. Commerce St., 164',
            'city'           => 'Dallas',
            'state'          => 'TX',
            'zip'            => '75208',
            'ssn'            => '351909701',
            'birth_date'     => '1995-03-06',
            'crc_status'     => 'failed',
            'crc_response'   => '406 Not Acceptable',
        ], $overrides));
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'credit_monitoring_email'    => 'cm@example.com',
            'credit_monitoring_password' => 'secret',
            'drivers_license'            => UploadedFile::fake()->image('dl.png'),
            'proof_of_address'           => UploadedFile::fake()->createWithContent('poa.pdf', '%PDF-1.4 bill'),
        ], $overrides);
    }

    public function test_the_link_is_signed_and_a_tampered_one_is_rejected(): void
    {
        $s = $this->failedSubmission();

        $this->get(FinishOnboardingController::linkFor($s))->assertOk();

        // Same route, no signature.
        $this->get('/finish-onboarding/' . $s->id)->assertForbidden();

        // Signature belonging to a different submission must not work here.
        $other = $this->failedSubmission(['email' => 'other@example.com']);
        $stolen = str_replace(
            '/finish-onboarding/' . $other->id,
            '/finish-onboarding/' . $s->id,
            FinishOnboardingController::linkFor($other),
        );
        $this->get($stolen)->assertForbidden();
    }

    public function test_the_form_never_asks_for_details_we_already_hold(): void
    {
        $s = $this->failedSubmission();

        $page = $this->get(FinishOnboardingController::linkFor($s));

        $page->assertOk();
        // Shown back for confirmation, masked.
        $page->assertSee('Lanique Graham');
        $page->assertSee('•••-••-9701', false);
        // Asked for.
        $page->assertSee('credit_monitoring_password', false);
        $page->assertSee('drivers_license', false);
        $page->assertSee('proof_of_address', false);
        // NOT asked for — the SSN is on file, so there is no input for it.
        $page->assertDontSee('name="ssn"', false);
        $page->assertDontSee('351909701');
    }

    public function test_it_rebuilds_the_full_apex_payload_from_stored_data(): void
    {
        config([
            'services.apex.enabled' => true,
            'services.apex.url'     => 'https://apex.test/partner-intake',
            'services.apex.key'     => 'victoria-key',
        ]);
        Http::fake(['apex.test/*' => Http::response(['ok' => true, 'id' => 7788], 201)]);

        $s = $this->failedSubmission();

        $this->post(
            URL::temporarySignedRoute('onboarding.finish.submit', now()->addHour(), ['submission' => $s->id]),
            $this->payload(),
        )->assertRedirect();

        Http::assertSent(function ($request) {
            $d = $request->data();

            // Everything below came from the stored record, not from the client.
            return $d['first_name'] === 'Lanique'
                && $d['last_name'] === 'Graham'
                && $d['ssn'] === '351909701'
                && $d['date_of_birth'] === '1995-03-06'
                && $d['current_address'] === '305 W. Commerce St., 164'
                && $d['city'] === 'Dallas'
                && $d['state'] === 'TX'
                && $d['zipcode'] === '75208'
                && $d['phone'] === '+14692150384'
                // …and these are the four the client just supplied.
                && $d['credit_monitoring_username'] === 'cm@example.com'
                && $d['credit_monitoring_password'] === 'secret'
                && isset($d['drivers_license_base64'])
                && isset($d['proof_of_address_base64']);
        });

        $s->refresh();
        $this->assertSame('sent', $s->crc_status);
        $this->assertSame('7788', $s->crc_id);
    }

    public function test_a_burgundy_submission_re_sends_under_burgundys_key(): void
    {
        config([
            'services.apex.enabled'               => true,
            'services.apex.url'                   => 'https://apex.test/partner-intake',
            'services.apex.key'                   => 'victoria-key',
            'services.apex.partner_keys.burgundy' => 'burgundy-key',
        ]);
        Http::fake(['apex.test/*' => Http::response(['ok' => true, 'id' => 1], 201)]);

        $s = $this->failedSubmission(['partner' => 'burgundy']);

        $this->post(
            URL::temporarySignedRoute('onboarding.finish.submit', now()->addHour(), ['submission' => $s->id]),
            $this->payload(),
        );

        Http::assertSent(fn ($r) => $r->hasHeader('X-Intake-Key', 'burgundy-key'));
    }

    public function test_a_second_failure_now_captures_the_documents_for_retry(): void
    {
        config([
            'services.apex.enabled' => true,
            'services.apex.url'     => 'https://apex.test/partner-intake',
            'services.apex.key'     => 'victoria-key',
        ]);
        Http::fake(['apex.test/*' => Http::response(['message' => 'boom'], 500)]);

        $s = $this->failedSubmission();

        // The client still sees success — chasing delivery is our job.
        $this->post(
            URL::temporarySignedRoute('onboarding.finish.submit', now()->addHour(), ['submission' => $s->id]),
            $this->payload(),
        )->assertRedirect();

        // This is the whole point: the documents are captured this time, so the
        // admin can retry without going back to the client again.
        $this->assertDatabaseHas('apex_retry_jobs', [
            'onboarding_submission_id' => $s->id,
            'partner'                  => 'victoria',
            'status'                   => 'pending',
        ]);
        $this->assertSame('failed', $s->fresh()->crc_status);
    }

    public function test_an_already_delivered_submission_cannot_be_pushed_twice(): void
    {
        config(['services.apex.enabled' => true, 'services.apex.url' => 'https://apex.test/partner-intake']);
        Http::fake(['apex.test/*' => Http::response(['ok' => true, 'id' => 2], 201)]);

        $s = $this->failedSubmission(['crc_status' => 'sent', 'crc_id' => '111']);

        $this->get(FinishOnboardingController::linkFor($s))
            ->assertOk()
            ->assertSee("that's everything", false);

        $this->post(
            URL::temporarySignedRoute('onboarding.finish.submit', now()->addHour(), ['submission' => $s->id]),
            $this->payload(),
        );

        Http::assertNothingSent();
        $this->assertSame('111', $s->fresh()->crc_id);
    }

    public function test_it_asks_for_the_ssn_only_when_it_cannot_be_read_back(): void
    {
        $s = $this->failedSubmission();

        // Simulate an unreadable ciphertext (e.g. APP_KEY rotated since).
        $s->forceFill(['ssn_encrypted' => 'not-decryptable'])->save();
        $s->refresh();

        $this->get(FinishOnboardingController::linkFor($s))
            ->assertOk()
            ->assertSee('name="ssn"', false);
    }
}
