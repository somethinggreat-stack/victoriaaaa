<?php

namespace Tests\Feature;

use App\Models\BurgundyClient;
use App\Models\PaymentAgreement;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * The Burgundy partnership flow after the card is charged: agreement, then
 * onboarding, then delivery into both dashboards.
 *
 * The Authorize.Net charge itself is not covered here — it needs a live
 * merchant account, so it is verified by a real $100 test transaction instead.
 */
class BurgundyFlowTest extends TestCase
{
    use RefreshDatabase;

    private function legacyClient(array $overrides = []): BurgundyClient
    {
        $client = BurgundyClient::create(array_merge([
            'first_name'     => 'Amber',
            'last_name'      => 'Testerson',
            'email'          => 'amber.testerson@example.com',
            'phone'          => '(817) 555-0101',
            'source'         => 'legacy',
            'match_status'   => 'matched',
            'matched_on'     => 'seed',
            'contact_status' => 'not_contacted',
        ], $overrides));

        $client->rememberIdentifier('email', $client->email);
        $client->rememberIdentifier('phone', $client->phone);

        return $client;
    }

    private function paidSession(BurgundyClient $client, string $invoice = 'BG-TEST-0001'): array
    {
        Subscription::create([
            'first_name'       => $client->first_name,
            'last_name'        => $client->last_name,
            'email'            => $client->email,
            'plan_key'         => 'burgundy-100',
            'plan_label'       => 'Credit Restoration Program',
            'amount'           => '100.00',
            'recurring_amount' => '100.00',
            'invoice_number'   => $invoice,
            'status'           => 'active',
            'subscribed_at'    => now(),
        ]);

        return [
            'burgundy_paid'      => true,
            'burgundy_client_id' => $client->id,
            'burgundy_invoice'   => $invoice,
            'burgundy_customer'  => [
                'first_name' => $client->first_name,
                'last_name'  => $client->last_name,
                'email'      => $client->email,
                'phone'      => $client->phone,
                'address'    => '1 Test St',
                'city'       => 'Dallas',
                'state'      => 'TX',
                'zip'        => '75201',
            ],
        ];
    }

    private function onboardingPayload(array $overrides = []): array
    {
        return array_merge([
            'firstname'                  => 'Amber',
            'lastname'                   => 'Testerson',
            'email'                      => 'amber.testerson@example.com',
            'phone'                      => '(817) 555-0101',
            'birth_date'                 => '01/27/1992',
            'ssn'                        => '123-45-6789',
            'street_address'             => '1 Test St',
            'city'                       => 'Dallas',
            'state'                      => 'TX',
            'zip'                        => '75201',
            'credit_monitoring_email'    => 'cm@example.com',
            'credit_monitoring_password' => 'secret',
            'drivers_license'            => UploadedFile::fake()->image('dl.png'),
            'proof_of_address'           => UploadedFile::fake()->create('poa.pdf', 40, 'application/pdf'),
        ], $overrides);
    }

    // ── Agreement ────────────────────────────────────────────────────────────

    public function test_agreement_page_requires_a_paid_session(): void
    {
        // Someone who has already paid must never be pushed back to checkout.
        $this->get('/burgundy-agreement')
            ->assertOk()
            ->assertSee('Continue to onboarding', false);
    }

    public function test_signing_stores_the_agreement_and_links_it_to_the_client(): void
    {
        $client = $this->legacyClient();

        $resp = $this->withSession($this->paidSession($client))
            ->post('/burgundy-agreement/sign', [
                'full_name'      => 'Amber Testerson',
                'signature_data' => 'data:image/png;base64,iVBORw0KGgo=',
                'agree_terms'    => '1',
            ]);

        $resp->assertOk()->assertJson(['success' => true]);

        $agreement = PaymentAgreement::where('plan_key', 'burgundy-100')->first();
        $this->assertNotNull($agreement, 'agreement was not stored');
        $this->assertSame('Amber Testerson', $agreement->full_name);
        $this->assertSame('BG-TEST-0001', $agreement->invoice_number);
        $this->assertNotNull($agreement->signed_at);

        // The terms the client actually saw are frozen with the signature.
        $this->assertStringContainsString('$100.00', $agreement->contract_text);
        $this->assertStringContainsString('Amber Testerson', $agreement->contract_text);

        // Cross-referenced both ways.
        $this->assertSame($agreement->id, $client->fresh()->payment_agreement_id);
        $this->assertNotNull($agreement->subscription_id);
    }

    public function test_a_drawn_signature_is_required(): void
    {
        $client = $this->legacyClient();

        $this->withSession($this->paidSession($client))
            ->post('/burgundy-agreement/sign', [
                'full_name'      => 'Amber Testerson',
                'signature_data' => 'not-an-image',
                'agree_terms'    => '1',
            ])
            ->assertStatus(422);

        $this->assertDatabaseCount('payment_agreements', 0);
    }

    // ── Onboarding ───────────────────────────────────────────────────────────

    public function test_onboarding_completes_the_client_and_forwards_under_burgundys_key(): void
    {
        config([
            'services.apex.enabled'                 => true,
            'services.apex.url'                     => 'https://apex.test/partner-intake',
            'services.apex.key'                     => 'victoria-key',
            'services.apex.partner_keys.burgundy'   => 'burgundy-key',
        ]);

        Http::fake(['apex.test/*' => Http::response(['ok' => true, 'id' => 991], 201)]);

        $client = $this->legacyClient();

        $this->withSession($this->paidSession($client))
            ->post('/burgundy-onboarding', $this->onboardingPayload())
            ->assertRedirect(route('burgundy.onboarding.show'))
            ->assertSessionHas('success', true);

        $client->refresh();
        $this->assertSame('complete', $client->onboarding_status);
        $this->assertSame('active', $client->client_status);
        $this->assertSame('sent', $client->apex_status);
        $this->assertSame('991', $client->apex_id);
        $this->assertNotNull($client->onboarding_submission_id);

        // The submission is tagged so a retry re-sends under the right key.
        $this->assertDatabaseHas('onboarding_submissions', [
            'partner' => 'burgundy',
            'email'   => 'amber.testerson@example.com',
        ]);

        // THE critical assertion: Burgundy's key, never Victoria's. The key is
        // what decides whose Apex dashboard the client appears in.
        Http::assertSent(fn ($request) => $request->hasHeader('X-Intake-Key', 'burgundy-key'));
    }

    public function test_onboarding_matches_an_existing_client_without_a_session(): void
    {
        config(['services.apex.enabled' => false]);

        // Paid earlier, came back later in a fresh browser — no session at all.
        $client = $this->legacyClient();

        $this->post('/burgundy-onboarding', $this->onboardingPayload())
            ->assertRedirect(route('burgundy.onboarding.show'));

        $client->refresh();
        $this->assertSame('complete', $client->onboarding_status);
        $this->assertSame(1, BurgundyClient::count(), 'a duplicate client was created instead of matching');
    }

    public function test_an_unrecognised_onboarding_is_flagged_for_review_not_dropped(): void
    {
        config(['services.apex.enabled' => false]);

        $this->legacyClient();

        $this->post('/burgundy-onboarding', $this->onboardingPayload([
            'firstname' => 'Complete',
            'lastname'  => 'Stranger',
            'email'     => 'stranger@example.com',
            'phone'     => '(214) 555-9999',
        ]))->assertRedirect(route('burgundy.onboarding.show'));

        $stranger = BurgundyClient::where('email', 'stranger@example.com')->first();
        $this->assertNotNull($stranger, 'the stranger was dropped instead of queued for review');
        $this->assertSame('needs_review', $stranger->match_status);
        $this->assertSame('complete', $stranger->onboarding_status);
    }

    public function test_a_failed_apex_forward_queues_a_retry_without_failing_the_client(): void
    {
        config([
            'services.apex.enabled'               => true,
            'services.apex.url'                   => 'https://apex.test/partner-intake',
            'services.apex.partner_keys.burgundy' => 'burgundy-key',
        ]);

        Http::fake(['apex.test/*' => Http::response(['message' => 'boom'], 500)]);

        $client = $this->legacyClient();

        // The client still sees success — delivery to Apex is our problem, and
        // they must never redo a form full of uploads over an issue on our side.
        $this->withSession($this->paidSession($client))
            ->post('/burgundy-onboarding', $this->onboardingPayload())
            ->assertRedirect(route('burgundy.onboarding.show'))
            ->assertSessionHas('success', true);

        $this->assertSame('failed', $client->fresh()->apex_status);

        // Queued under the right partner, so the retry uses Burgundy's key.
        $this->assertDatabaseHas('apex_retry_jobs', [
            'partner' => 'burgundy',
            'email'   => 'amber.testerson@example.com',
            'status'  => 'pending',
        ]);
    }

    public function test_apex_refuses_to_send_when_the_partner_key_is_missing(): void
    {
        // Falling back to Victoria's key would deliver Burgundy's client into
        // Victoria's dashboard — unrecoverable. Failing is the correct outcome.
        config([
            'services.apex.enabled'               => true,
            'services.apex.url'                   => 'https://apex.test/partner-intake',
            'services.apex.key'                   => 'victoria-key',
            'services.apex.partner_keys.burgundy' => null,
        ]);

        Http::fake(['apex.test/*' => Http::response(['ok' => true, 'id' => 1], 201)]);

        $client = $this->legacyClient();

        $this->withSession($this->paidSession($client))
            ->post('/burgundy-onboarding', $this->onboardingPayload());

        Http::assertNothingSent();
        $this->assertSame('failed', $client->fresh()->apex_status);
    }

    // ── Isolation ────────────────────────────────────────────────────────────

    public function test_partnership_login_does_not_unlock_victoria_admin(): void
    {
        config([
            'partnership.email'    => 'partnership@example.com',
            'partnership.password' => 'correct-horse',
        ]);

        $this->post('/partnership-login', [
            'email'    => 'partnership@example.com',
            'password' => 'correct-horse',
        ])->assertRedirect(route('partnership.dashboard'));

        $this->get('/partnership')->assertOk();

        // Same session, Victoria's admin — must be refused.
        $this->get('/victoria-admin')->assertRedirect(route('admin.login.show'));
        $this->get('/victoria-admin/payments')->assertRedirect(route('admin.login.show'));
    }

    public function test_an_unconfigured_partnership_login_is_not_an_open_door(): void
    {
        config(['partnership.email' => '', 'partnership.password' => '']);

        $this->post('/partnership-login', ['email' => 'a@b.com', 'password' => ''])
            ->assertSessionHasErrors('password');

        $this->post('/partnership-login', ['email' => 'a@b.com', 'password' => 'anything'])
            ->assertSessionHasErrors('email');

        $this->get('/partnership')->assertRedirect(route('partnership.login.show'));
    }
}
