<?php

namespace Tests\Feature;

use App\Http\Controllers\AcceptJsPaymentController;
use App\Models\Affiliate;
use App\Models\AffiliateReferral;
use App\Models\OnboardingSubmission;
use App\Models\Subscription;
use App\Models\User;
use App\Services\Affiliates;
use App\Services\ServiceAgreement;
use App\Services\ServiceAgreements;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Pricing, hours, mentorship, affiliates and onboarding follow-ups (Nov 2026). */
class SiteUpdatesNov2026Test extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Victoria', 'password' => 'secret-password'],
        );
    }

    private function sub(array $o = []): Subscription
    {
        return Subscription::create($o + [
            'first_name' => 'Rhonesha', 'last_name' => 'Ard', 'email' => 'rhonesha@example.com',
            'phone' => '5551112222', 'address' => '1 St', 'city' => 'Dallas', 'state' => 'TX', 'zip' => '75001',
            'plan_key' => 'fast-track', 'plan_label' => 'Fast Track', 'amount' => '250.00',
            'recurring_amount' => '250.00', 'invoice_number' => 'INV-' . uniqid(), 'status' => 'active',
            'subscribed_at' => now(),
        ]);
    }

    // ── Pricing ──────────────────────────────────────────────────────────────

    public function test_the_homepage_shows_total_start_and_then_for_every_plan(): void
    {
        $this->get('/')->assertOk()
            ->assertSee('Standard Credit Repair')
            ->assertSee('Total investment')
            ->assertSee('$597')->assertSee('$297')->assertSee('$150 every 2 weeks × 2', false)
            ->assertSee('$1,000')->assertSee('$250/week × 3', false)
            ->assertSee('$1,500')->assertSee('$250/week × 4', false)
            ->assertSee('Couples Fast Track');
    }

    public function test_fast_track_carries_the_most_popular_badge_and_standard_does_not(): void
    {
        $html = $this->get('/')->getContent();
        $fast = strpos($html, '<div class="name">Fast Track</div>');
        $this->assertNotFalse($fast);
        // the badge sits directly above the Fast Track name
        $this->assertStringContainsString('Most Popular', substr($html, $fast - 120, 120));
        $this->assertSame(1, substr_count($html, 'class="price-tag">Most Popular'));
    }

    public function test_each_new_plan_has_a_checkout_and_its_totals_add_up(): void
    {
        $expected = ['standard' => 597.0, 'fast-track' => 1000.0, 'couples-fast-track' => 1500.0];

        foreach ($expected as $key => $total) {
            $plan = AcceptJsPaymentController::PLANS[$key];
            $this->assertSame($total, (float) $plan['amount'] + (float) $plan['recurring'] * $plan['recurring_occurrences'], $key);
            $this->assertSame($total, (float) $plan['total'], $key);
            $this->get(route('checkout.show', $key))->assertOk()->assertSee('Total investment', false);
        }

        $this->assertSame(14, AcceptJsPaymentController::PLANS['standard']['interval_days']);
        $this->assertSame(7, AcceptJsPaymentController::PLANS['fast-track']['interval_days']);
    }

    public function test_an_every_two_weeks_agreement_never_says_month_or_week(): void
    {
        $a = ServiceAgreements::start([
            'source' => 'subscription', 'source_id' => 1, 'plan_key' => 'standard',
            'plan_label' => 'Standard Credit Repair', 'charged_today' => 297.0,
            'recurring_amount' => 150.0, 'recurring_count' => 2, 'recurring_interval' => 'biweek',
            'client_name' => 'Dana', 'email' => 'dana@example.com',
        ]);

        $this->assertSame('biweek', $a->recurring_interval);

        $text = ServiceAgreement::build(ServiceAgreements::saleFor($a));
        $this->assertStringContainsString('$150.00 every 2 weeks for 2 payments', $text);
        $this->assertStringContainsString('Total:          $597.00', $text);
        $this->assertStringNotContainsString('per month', $text);
        $this->assertStringNotContainsString('per week', $text);

        $this->get(ServiceAgreements::signingUrl($a))->assertOk()->assertSee('$150.00/2wk', false)->assertDontSee('/mo');
    }

    // ── Mentorship ───────────────────────────────────────────────────────────

    public function test_mentorship_is_1997_in_full_or_two_payments_of_1099(): void
    {
        $this->get(route('mentorship'))->assertOk()
            ->assertSee('$1,997')->assertSee('$1,099')
            ->assertDontSee('$1,697')->assertDontSee('$303');

        $full = AcceptJsPaymentController::PLANS['mentorship-full'];
        $two  = AcceptJsPaymentController::PLANS['mentorship-2pay'];
        $this->assertSame('1997.00', $full['amount']);
        $this->assertSame('1099.00', $two['amount']);
        $this->assertSame(1, $two['recurring_occurrences']);
    }

    public function test_the_two_payment_mentorship_plan_asks_for_the_agreement_first(): void
    {
        $this->get(route('checkout.show', 'mentorship-2pay'))
            ->assertRedirect(route('mentorship-agreement.show', 'mentorship-2pay'));
        $this->get(route('mentorship-agreement.show', 'mentorship-2pay'))->assertOk();
    }

    // ── Hours, phone, pop-up ─────────────────────────────────────────────────

    public function test_business_hours_and_phone_are_in_the_footer_and_contact_page(): void
    {
        foreach (['/', '/contact'] as $url) {
            $this->get($url)->assertOk()
                ->assertSee('(469) 349-5553', false)
                ->assertSee('10 AM – 5 PM Central', false);
        }
    }

    public function test_the_homepage_no_longer_pushes_the_call_questionnaire(): void
    {
        $html = $this->get('/')->getContent();
        $this->assertStringNotContainsString(route('strategy-call.show'), $html);
        // ...but it is still reachable from the contact page.
        $this->get('/contact')->assertSee(route('strategy-call.show'), false);
    }

    // ── Affiliates ───────────────────────────────────────────────────────────

    public function test_an_affiliate_link_remembers_who_sent_the_visitor(): void
    {
        $aff = Affiliate::create(['name' => 'Jane', 'code' => 'JANE42', 'payout_amount' => 100, 'active' => true]);

        $this->get('/r/jane42')->assertRedirect(url('/') . '#pricing')->assertSessionHas('referral_code', 'JANE42');
    }

    public function test_an_unknown_or_switched_off_code_is_ignored(): void
    {
        Affiliate::create(['name' => 'Off', 'code' => 'OFF11', 'payout_amount' => 100, 'active' => false]);

        $this->get('/r/NOPE99')->assertSessionMissing('referral_code');
        $this->get('/r/OFF11')->assertSessionMissing('referral_code');
    }

    public function test_a_paying_referred_client_makes_the_affiliate_owed_100_once(): void
    {
        $aff = Affiliate::create(['name' => 'Jane', 'code' => 'JANE42', 'payout_amount' => 100, 'active' => true]);
        $sub = $this->sub();

        Affiliates::record('jane42', $sub);
        Affiliates::record('JANE42', $sub); // same sale again — still one row

        $this->assertSame(1, AffiliateReferral::count());
        $r = AffiliateReferral::first();
        $this->assertSame('100.00', (string) $r->amount);
        $this->assertSame('owed', $r->status);
        $this->assertSame($aff->id, $r->affiliate_id);
    }

    public function test_mentorship_and_unreferred_sales_pay_no_fee(): void
    {
        Affiliate::create(['name' => 'Jane', 'code' => 'JANE42', 'payout_amount' => 100, 'active' => true]);

        Affiliates::record('JANE42', $this->sub(['plan_key' => 'mentorship-full', 'invoice_number' => 'INV-M']));
        Affiliates::record(null, $this->sub(['invoice_number' => 'INV-N']));
        Affiliates::record('WRONG1', $this->sub(['invoice_number' => 'INV-W']));

        $this->assertSame(0, AffiliateReferral::count());
    }

    public function test_victoria_can_create_an_affiliate_and_mark_them_paid(): void
    {
        $this->actingAs($this->admin())->post(route('admin.affiliates.store'), [
            'name' => 'Jane Smith', 'payout_amount' => '100',
        ])->assertSessionHas('new_affiliate_link');

        $aff = Affiliate::firstOrFail();
        $this->assertMatchesRegularExpression('/^[A-Z]{1,6}\d{2}$/', $aff->code);

        Affiliates::record($aff->code, $this->sub());

        $this->actingAs($this->admin())->get(route('admin.affiliates'))
            ->assertOk()->assertSee('Jane Smith')->assertSee('$100.00');

        $this->actingAs($this->admin())->post(route('admin.affiliates.pay-all', $aff));
        $this->assertSame('paid', AffiliateReferral::first()->status);
    }

    // ── Onboarding follow-up ─────────────────────────────────────────────────

    public function test_paid_clients_with_no_onboarding_are_listed_and_finished_ones_are_not(): void
    {
        $this->sub(['first_name' => 'Waiting', 'last_name' => 'Client', 'email' => 'wait@example.com', 'invoice_number' => 'INV-A']);
        $this->sub(['first_name' => 'Finished', 'last_name' => 'Client', 'email' => 'Done@Example.com', 'invoice_number' => 'INV-B']);
        $this->sub(['first_name' => 'Mentee', 'last_name' => 'Person', 'email' => 'm@example.com', 'plan_key' => 'mentorship-full', 'invoice_number' => 'INV-C']);

        OnboardingSubmission::create([
            'firstname' => 'Finished', 'lastname' => 'Client', 'email' => 'done@example.com', 'phone' => '5551112222', 'ssn' => '123456789', 'birth_date' => '1990-01-01', 'street_address' => '1 St', 'city' => 'Dallas', 'state' => 'TX', 'zip' => '75001', 'status' => 'new',
        ]);

        $this->actingAs($this->admin())->get(route('admin.onboarding'))
            ->assertOk()
            // exactly one waiting: the finished one matched on email, case-blind
            ->assertSee("1 paid client haven't finished onboarding", false)
            ->assertSee('Waiting Client')
            ->assertDontSee('Mentee Person');    // mentorship has no onboarding form
    }
}
