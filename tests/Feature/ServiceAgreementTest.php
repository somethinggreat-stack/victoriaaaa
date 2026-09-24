<?php

namespace Tests\Feature;

use App\Models\PaymentAgreement;
use App\Models\PaymentLink;
use App\Models\User;
use App\Services\ServiceAgreements;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

/**
 * The service agreement that follows a payment.
 *
 * Before this, only Burgundy's flow and the mentorship instalment plans signed
 * anything. Every credit-repair sale and every dashboard payment link took
 * money with no signed agreement at all.
 */
class ServiceAgreementTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Victoria', 'password' => 'secret-password'],
        );
    }

    private function pending(array $overrides = []): PaymentAgreement
    {
        return ServiceAgreements::start(array_merge([
            'source'           => 'subscription',
            'source_id'        => 1,
            'partner'          => 'victoria',
            'plan_key'         => 'monthly',
            'plan_label'       => 'Monthly Plan',
            'charged_today'    => 197.00,
            'recurring_amount' => 100.00,
            'client_name'      => 'Dana Whitfield',
            'email'            => 'dana@example.com',
            'invoice_number'   => 'INV-TEST-1',
            'next_url'         => 'http://localhost/onboarding',
        ], $overrides));
    }

    private function signPayload(): array
    {
        return [
            'full_name'      => 'Dana Whitfield',
            'signature_data' => 'data:image/png;base64,iVBORw0KGgo=',
            'agree_terms'    => '1',
        ];
    }

    private function signUrl(PaymentAgreement $a): string
    {
        return URL::temporarySignedRoute('agreement.sign', now()->addHour(), ['agreement' => $a->id]);
    }

    // ── The gap between paying and signing ───────────────────────────────────

    public function test_an_agreement_opens_as_pending_the_moment_the_card_is_charged(): void
    {
        $a = $this->pending();

        $this->assertNotNull($a);
        $this->assertSame('pending', $a->status);
        $this->assertNull($a->signed_at);
        // Priced from the sale, so it cannot disagree with the client's statement.
        $this->assertSame('197.00', (string) $a->deposit_amount);
        $this->assertSame('100.00', (string) $a->installment_amount);
    }

    public function test_the_signing_page_shows_the_amount_that_was_charged(): void
    {
        $a = $this->pending();

        $this->get(ServiceAgreements::signingUrl($a))
            ->assertOk()
            ->assertSee('$197.00')
            ->assertSee('$100.00')
            ->assertSee('What you are paying');
    }

    public function test_the_link_is_signed_and_cannot_be_forged(): void
    {
        $a = $this->pending();

        $this->get(ServiceAgreements::signingUrl($a))->assertOk();
        $this->get('/agreement/' . $a->id)->assertForbidden();
    }

    public function test_signing_records_the_signature_and_freezes_the_wording(): void
    {
        $a = $this->pending();

        $this->post($this->signUrl($a), $this->signPayload())
            ->assertOk()
            ->assertJson(['success' => true]);

        $a->refresh();
        $this->assertSame('signed', $a->status);
        $this->assertSame('Dana Whitfield', $a->full_name);
        $this->assertNotNull($a->signed_at);
        $this->assertNotNull($a->ip_address);
        $this->assertStringContainsString('$197.00', $a->contract_text);
        $this->assertStringContainsString('Dana Whitfield', $a->contract_text);
    }

    public function test_signing_sends_the_client_on_to_where_they_were_going(): void
    {
        $a = $this->pending();

        $this->post($this->signUrl($a), $this->signPayload())
            ->assertJson(['redirect' => 'http://localhost/onboarding']);
    }

    public function test_a_drawn_signature_is_required(): void
    {
        $a = $this->pending();

        $this->post($this->signUrl($a), [
            'full_name'      => 'Dana Whitfield',
            'signature_data' => 'not-an-image',
            'agree_terms'    => '1',
        ])->assertStatus(422);

        $this->assertSame('pending', $a->fresh()->status);
    }

    public function test_an_already_signed_agreement_cannot_be_overwritten(): void
    {
        $a = $this->pending();
        $this->post($this->signUrl($a), $this->signPayload());
        $first = $a->fresh()->signature_data;

        $this->post($this->signUrl($a), [
            'full_name'      => 'Someone Else',
            'signature_data' => 'data:image/png;base64,ZZZZ',
            'agree_terms'    => '1',
        ]);

        $a->refresh();
        $this->assertSame('Dana Whitfield', $a->full_name);
        $this->assertSame($first, $a->signature_data);
    }

    public function test_revisiting_a_signed_agreement_shows_the_receipt_not_the_form(): void
    {
        $a = $this->pending();
        $this->post($this->signUrl($a), $this->signPayload());

        $this->get(ServiceAgreements::signingUrl($a))
            ->assertOk()
            ->assertSee('Signed and on file')
            ->assertDontSee('Draw your signature');
    }

    public function test_mentorship_still_signs_before_payment_not_after(): void
    {
        // Mentorship commits the buyer to a $2,000 plan, so its agreement is
        // signed BEFORE the card is charged. Wiring the post-payment contract
        // into every other plan must not have changed that.
        $this->get('/checkout/mentorship-3pay')
            ->assertRedirect(route('mentorship-agreement.show', 'mentorship-3pay'));
    }

    // ── Payment links ────────────────────────────────────────────────────────

    public function test_creating_a_payment_link_requires_a_service_description(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.payment-links.store'), [
                'client_name' => 'Dana Whitfield',
                'amount'      => '250.00',
            ])
            ->assertSessionHasErrors('service_description');

        $this->assertSame(0, PaymentLink::count());
    }

    public function test_the_service_description_reaches_the_contract(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.payment-links.store'), [
                'client_name'         => 'Dana Whitfield',
                'amount'              => '250.00',
                'service_description' => 'One 90-minute credit strategy consultation.',
            ]);

        $link = PaymentLink::first();
        $this->assertSame('One 90-minute credit strategy consultation.', $link->service_description);

        $a = ServiceAgreements::start([
            'source'              => 'payment_link',
            'source_id'           => $link->id,
            'plan_key'            => 'payment-link',
            'plan_label'          => $link->service_description,
            'service_description' => $link->service_description,
            'charged_today'       => (float) $link->amount,
            'client_name'         => $link->client_name,
        ]);

        $this->get(ServiceAgreements::signingUrl($a))
            ->assertOk()
            ->assertSee('One 90-minute credit strategy consultation.')
            ->assertSee('$250.00');
    }

    public function test_signing_links_the_agreement_back_to_the_payment_link(): void
    {
        $link = PaymentLink::create([
            'token' => 'pl_' . str_repeat('a', 28), 'client_name' => 'Dana Whitfield',
            'amount' => '250.00', 'service_description' => 'Consultation', 'status' => 'paid',
        ]);

        $a = $this->pending(['source' => 'payment_link', 'source_id' => $link->id, 'next_url' => null]);
        $this->post($this->signUrl($a), $this->signPayload());

        $this->assertSame($a->id, $link->fresh()->payment_agreement_id);
    }

    // ── Raising an agreement by hand ─────────────────────────────────────────

    public function test_an_agreement_can_be_raised_for_a_client_who_paid_elsewhere(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.contracts.store'), [
                'client_name'         => 'Brice Wilson',
                'email'               => 'brice@example.com',
                'service_description' => 'Full-service credit restoration for two people.',
                'charged_today'       => '1200.00',
                'partner'             => 'victoria',
            ])
            ->assertRedirect();

        $a = PaymentAgreement::where('source', 'manual')->first();
        $this->assertNotNull($a);
        $this->assertSame('pending', $a->status);
        $this->assertSame('Brice Wilson', $a->client_name);
        $this->assertSame('1200.00', (string) $a->deposit_amount);
        // Blank monthly means one-time, not a zero-value plan.
        $this->assertNull($a->installment_amount);

        $this->get(ServiceAgreements::signingUrl($a))
            ->assertOk()
            ->assertSee('$1,200.00')
            ->assertSee('Full-service credit restoration for two people.')
            ->assertSee('This is a one-time payment.');
    }

    public function test_a_manual_agreement_can_carry_a_monthly_amount(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.contracts.store'), [
                'client_name'         => 'Mija Mcmann',
                'service_description' => 'Credit restoration programme.',
                'charged_today'       => '149.00',
                'recurring_amount'    => '149.00',
                'partner'             => 'victoria',
            ]);

        $a = PaymentAgreement::where('source', 'manual')->first();
        $this->assertSame('149.00', (string) $a->installment_amount);

        $this->get(ServiceAgreements::signingUrl($a))
            ->assertOk()
            ->assertSee('until you cancel');
    }

    public function test_a_manual_agreement_needs_an_amount_and_a_description(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.contracts.store'), ['client_name' => 'Brice Wilson', 'partner' => 'victoria'])
            ->assertSessionHasErrors(['service_description', 'charged_today']);

        $this->assertSame(0, PaymentAgreement::count());
    }

    public function test_a_manually_raised_agreement_signs_like_any_other(): void
    {
        $this->actingAs($this->admin())->post(route('admin.contracts.store'), [
            'client_name'         => 'Brice Wilson',
            'service_description' => 'Full-service credit restoration.',
            'charged_today'       => '1200.00',
            'partner'             => 'victoria',
        ]);

        $a = PaymentAgreement::where('source', 'manual')->first();

        $this->post($this->signUrl($a), [
            'full_name'      => 'Brice Wilson',
            'signature_data' => 'data:image/png;base64,iVBORw0KGgo=',
            'agree_terms'    => '1',
        ])->assertOk();

        $a->refresh();
        $this->assertSame('signed', $a->status);
        $this->assertStringContainsString('$1,200.00', $a->contract_text);

        $this->actingAs($this->admin())
            ->get(route('admin.contracts.pdf', $a))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    // ── Admin ────────────────────────────────────────────────────────────────

    public function test_the_admin_warns_about_clients_who_paid_but_never_signed(): void
    {
        $this->pending();

        $this->actingAs($this->admin())
            ->get(route('admin.contracts'))
            ->assertOk()
            ->assertSee('1 client paid but never signed.');
    }

    public function test_the_warning_disappears_once_everyone_has_signed(): void
    {
        $a = $this->pending();
        $this->post($this->signUrl($a), $this->signPayload());

        $this->actingAs($this->admin())
            ->get(route('admin.contracts'))
            ->assertOk()
            ->assertDontSee('paid but never signed');
    }

    public function test_an_unsigned_contract_offers_a_link_to_send_again(): void
    {
        $a = $this->pending();

        $this->actingAs($this->admin())
            ->get(route('admin.contracts.show', $a))
            ->assertOk()
            ->assertSee('Paid, but not signed')
            ->assertSee('/agreement/' . $a->id, false);
    }

    public function test_a_signed_contract_downloads_as_a_pdf(): void
    {
        $a = $this->pending();
        $this->post($this->signUrl($a), $this->signPayload());

        $res = $this->actingAs($this->admin())->get(route('admin.contracts.pdf', $a->fresh()));

        $res->assertOk();
        $res->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $res->getContent());
    }
}
