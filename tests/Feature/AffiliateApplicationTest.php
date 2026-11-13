<?php

namespace Tests\Feature;

use App\Models\Affiliate;
use App\Models\AffiliateApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AffiliateApplicationTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $o = []): array
    {
        return $o + [
            'name' => 'Jane Smith', 'email' => 'Jane@Example.com', 'phone' => '5551112222',
            'payout_method' => 'zelle', 'payout_details' => 'jane@example.com', 'agree' => '1',
        ];
    }

    public function test_the_agreement_page_shows_the_terms_and_the_disclosure(): void
    {
        $this->get(route('affiliate.apply'))->assertOk()
            ->assertSee('Affiliate Referral Agreement')
            ->assertSee('one-time $100 commission', false)
            ->assertSee('within 3 business days', false)
            ->assertSee('I may earn $100 from Victorious Opportunities', false)
            ->assertSee('independent affiliate', false);
    }

    public function test_applying_records_the_acceptance(): void
    {
        $this->post(route('affiliate.apply.store'), $this->payload())->assertRedirect(route('affiliate.apply'));

        $a = AffiliateApplication::firstOrFail();
        $this->assertSame('jane@example.com', $a->email);
        $this->assertSame('pending', $a->status);
        $this->assertNotNull($a->accepted_at);
        $this->assertSame('2026-11-13', $a->agreement_version);
    }

    public function test_you_cannot_apply_without_accepting_the_agreement(): void
    {
        $this->post(route('affiliate.apply.store'), $this->payload(['agree' => null]))->assertSessionHasErrors('agree');
        $this->assertSame(0, AffiliateApplication::count());
    }

    public function test_victoria_approves_an_applicant_and_they_get_a_link(): void
    {
        $this->post(route('affiliate.apply.store'), $this->payload());
        $app = AffiliateApplication::firstOrFail();
        $admin = User::firstOrCreate(['email' => 'admin@example.com'], ['name' => 'Victoria', 'password' => 'secret-password']);

        $this->actingAs($admin)->get(route('admin.affiliates'))->assertOk()
            ->assertSee('1 application waiting for approval', false)->assertSee('Jane Smith');

        $this->actingAs($admin)->post(route('admin.affiliates.approve', $app))->assertSessionHas('new_affiliate_link');

        $this->assertSame('approved', $app->fresh()->status);
        $aff = Affiliate::firstOrFail();
        $this->assertSame($aff->id, $app->fresh()->affiliate_id);
        $this->assertStringContainsString('Zelle', $aff->notes);
        $this->assertSame('100.00', (string) $aff->payout_amount);
    }

    public function test_declining_leaves_no_affiliate(): void
    {
        $this->post(route('affiliate.apply.store'), $this->payload());
        $app = AffiliateApplication::firstOrFail();
        $admin = User::firstOrCreate(['email' => 'admin@example.com'], ['name' => 'Victoria', 'password' => 'secret-password']);

        $this->actingAs($admin)->post(route('admin.affiliates.decline', $app));

        $this->assertSame('declined', $app->fresh()->status);
        $this->assertSame(0, Affiliate::count());
    }
}
