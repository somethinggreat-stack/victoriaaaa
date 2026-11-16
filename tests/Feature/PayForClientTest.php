<?php

namespace Tests\Feature;

use App\Models\Affiliate;
use App\Models\AffiliateReferral;
use App\Models\PaymentLink;
use App\Services\Affiliates;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayForClientTest extends TestCase
{
    use RefreshDatabase;

    private function mija(): Affiliate
    {
        return Affiliate::create(['name' => 'Mija Lee', 'code' => 'MIJA42', 'payout_amount' => 100, 'active' => true]);
    }

    public function test_the_page_lists_the_three_plans_at_full_price(): void
    {
        $this->mija();

        $this->get(route('pay-for-client.show', 'mija42'))->assertOk()
            ->assertSee('Standard Credit Repair')->assertSee('$597')
            ->assertSee('Fast Track')->assertSee('$1,000')
            ->assertSee('$1,500');
    }

    public function test_an_unknown_or_switched_off_affiliate_gets_nothing(): void
    {
        Affiliate::create(['name' => 'Off', 'code' => 'OFF11', 'payout_amount' => 100, 'active' => false]);

        $this->get(route('pay-for-client.show', 'NOPE99'))->assertNotFound();
        $this->get(route('pay-for-client.show', 'OFF11'))->assertNotFound();
    }

    public function test_submitting_makes_a_full_price_link_for_that_client(): void
    {
        $aff = $this->mija();

        $res = $this->post(route('pay-for-client.store', 'MIJA42'), [
            'plan' => 'fast-track', 'first_name' => 'Bryce', 'last_name' => 'Jones', 'email' => 'Bryce@Example.com',
        ]);

        $link = PaymentLink::firstOrFail();
        $res->assertRedirect(route('pay.show', $link->token));
        $this->assertSame('1000.00', (string) $link->amount);
        $this->assertSame('bryce@example.com', $link->email);
        $this->assertSame($aff->id, $link->affiliate_id);
        $this->assertSame('unpaid', $link->status);
    }

    public function test_the_price_cannot_be_chosen_by_the_caller(): void
    {
        $this->mija();

        $this->post(route('pay-for-client.store', 'MIJA42'), [
            'plan' => 'mentorship-full', 'first_name' => 'A', 'last_name' => 'B', 'email' => 'a@b.co', 'amount' => '1',
        ])->assertSessionHasErrors('plan');

        $this->assertSame(0, PaymentLink::count());
    }

    public function test_paying_for_a_client_credits_the_affiliate_100_once(): void
    {
        $aff  = $this->mija();
        $link = PaymentLink::create([
            'token' => 'pl_' . str_repeat('a', 28), 'client_name' => 'Bryce Jones', 'email' => 'bryce@example.com',
            'amount' => '597.00', 'service_description' => 'Standard Credit Repair — paid in full',
            'status' => 'paid', 'affiliate_id' => $aff->id,
        ]);

        Affiliates::recordForLink($link);
        Affiliates::recordForLink($link);

        $this->assertSame(1, AffiliateReferral::count());
        $r = AffiliateReferral::first();
        $this->assertSame('100.00', (string) $r->amount);
        $this->assertSame('owed', $r->status);
        $this->assertSame($link->id, $r->payment_link_id);
    }

    public function test_an_unpaid_link_earns_nothing(): void
    {
        $aff  = $this->mija();
        $link = PaymentLink::create([
            'token' => 'pl_' . str_repeat('b', 28), 'client_name' => 'X Y', 'email' => 'x@y.co',
            'amount' => '597.00', 'service_description' => 'Standard', 'status' => 'unpaid', 'affiliate_id' => $aff->id,
        ]);

        $this->assertNull(Affiliates::recordForLink($link));
        $this->assertSame(0, AffiliateReferral::count());
    }
}
