<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OnboardingApexTest extends TestCase
{
    use WithoutMiddleware;

    public function test_onboarding_forwards_mapped_payload_to_apex(): void
    {
        config([
            'services.apex.enabled' => true,
            'services.apex.url'     => 'https://apex.test/api/intake',
            'services.apex.key'     => 'testkey123',
        ]);

        Http::fake([
            'apex.test/*' => Http::response(['ok' => true, 'id' => 4242], 201),
        ]);

        $resp = $this->post('/onboarding', [
            'firstname'                         => 'John',
            'middlename'                        => 'Q',
            'lastname'                          => 'Doe',
            'suffix'                            => 'Jr.',
            'email'                             => 'john@example.com',
            'phone'                             => '(305) 555-1234',
            'birth_date'                        => '05/01/1990',
            'ssn'                               => '123-45-6789',
            'street_address'                    => '123 Main St',
            'address_line2'                     => 'Apt 4B',
            'city'                              => 'Miami',
            'state'                             => 'FL',
            'zip'                               => '33101-1234',
            'credit_monitoring_provider'        => 'myfreescore',
            'credit_monitoring_email'           => 'cmuser@example.com',
            'credit_monitoring_password'        => 'secretpass',
            'credit_monitoring_security_answer' => 'Fluffy',
            'drivers_license'                   => UploadedFile::fake()->image('dl.png'),
            'proof_of_address'                  => UploadedFile::fake()->create('poa.pdf', 40, 'application/pdf'),
        ]);

        $resp->assertRedirect(route('onboarding.show'));
        $resp->assertSessionHas('success', true);

        Http::assertSent(function ($request) {
            $parts = collect($request->data());
            $text  = $parts->filter(fn ($p) => is_string($p['contents']))
                           ->mapWithKeys(fn ($p) => [$p['name'] => $p['contents']]);
            $files = $parts->reject(fn ($p) => is_string($p['contents']))->pluck('name')->all();

            $expected = [
                'first_name'                 => 'John',
                'last_name'                  => 'Doe',
                'middle_name'                => 'Q',
                'suffix'                     => 'Jr.',
                'email'                      => 'john@example.com',
                'ssn'                        => '123456789',
                'date_of_birth'              => '1990-05-01',
                'current_address'            => '123 Main St',
                'address_line2'              => 'Apt 4B',
                'city'                       => 'Miami',
                'state'                      => 'FL',
                'zipcode'                    => '33101',
                'phone'                      => '+13055551234',
                'credit_monitoring_name'     => 'myfreescore',
                'credit_monitoring_username' => 'cmuser@example.com',
                'credit_monitoring_password' => 'secretpass',
                'credit_monitoring_security_answer' => 'Fluffy',
            ];

            foreach ($expected as $k => $v) {
                if (($text[$k] ?? null) !== $v) {
                    fwrite(STDERR, "MISMATCH {$k}: expected '{$v}', got '" . ($text[$k] ?? 'MISSING') . "'\n");
                    return false;
                }
            }

            return $request->hasHeader('X-Intake-Key', 'testkey123')
                && in_array('drivers_license', $files, true)
                && in_array('proof_of_address', $files, true);
        });
    }

    public function test_apex_forward_is_skipped_when_disabled(): void
    {
        config(['services.apex.enabled' => false]);
        Http::fake();

        $resp = $this->post('/onboarding', [
            'firstname'                  => 'Jane',
            'lastname'                   => 'Roe',
            'email'                      => 'jane@example.com',
            'phone'                      => '3055559999',
            'birth_date'                 => '01/15/1985',
            'ssn'                        => '987654321',
            'street_address'             => '9 Palm Ave',
            'city'                       => 'Tampa',
            'state'                      => 'FL',
            'zip'                        => '33601',
            'credit_monitoring_email'    => 'jane@example.com',
            'credit_monitoring_password' => 'pw',
            'drivers_license'            => UploadedFile::fake()->image('dl.png'),
            'proof_of_address'           => UploadedFile::fake()->create('poa.pdf', 20, 'application/pdf'),
        ]);

        $resp->assertRedirect(route('onboarding.show'));
        $resp->assertSessionHas('success', true);
        Http::assertNothingSent();
    }
}
