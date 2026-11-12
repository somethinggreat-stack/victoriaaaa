<?php

namespace Tests\Feature;

use App\Http\Middleware\PartnershipAuth;
use App\Models\BurgundyClient;
use App\Models\PaymentLink;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Burgundy creates her own one-time payment links from the partnership dashboard. */
class PartnershipPaymentLinksTest extends TestCase
{
    use RefreshDatabase;

    private function asBurgundy(): static
    {
        return $this->withSession([PartnershipAuth::SESSION_KEY => true]);
    }

    private function payload(array $o = []): array
    {
        return array_merge([
            'client_name'         => 'Jane Smith',
            'amount'              => '450.00',
            'service_description' => 'One-time credit repair fee',
        ], $o);
    }

    public function test_it_requires_the_partnership_login(): void
    {
        $this->get(route('partnership.payment-links'))->assertRedirect(route('partnership.login.show'));
    }

    public function test_she_can_create_a_450_link_that_belongs_to_her(): void
    {
        $this->asBurgundy()->post(route('partnership.payment-links.store'), $this->payload())
            ->assertRedirect(route('partnership.payment-links'))
            ->assertSessionHas('generated_link');

        $link = PaymentLink::firstOrFail();
        $this->assertSame('burgundy', $link->partner);
        $this->assertSame('450.00', (string) $link->amount);
        $this->assertSame('unpaid', $link->status);
    }

    public function test_the_link_opens_the_normal_pay_page(): void
    {
        $this->asBurgundy()->post(route('partnership.payment-links.store'), $this->payload());

        $this->get(PaymentLink::first()->url)->assertOk();
    }

    public function test_creating_one_for_a_client_marks_them_payment_link_sent(): void
    {
        $client = BurgundyClient::create([
            'first_name' => 'Jane', 'last_name' => 'Smith', 'source' => 'new',
            'match_status' => 'matched', 'contact_status' => 'interested',
        ]);

        $this->asBurgundy()->post(route('partnership.payment-links.store'),
            $this->payload(['burgundy_client_id' => $client->id]));

        $this->assertSame('payment_link_sent', $client->fresh()->contact_status);
        $this->assertSame($client->id, PaymentLink::first()->burgundy_client_id);
    }

    public function test_her_page_never_lists_victorias_links_or_lets_her_void_them(): void
    {
        $victorias = PaymentLink::create([
            'token' => 'pl_victoriaslinkxxxxxxxxxxxx', 'client_name' => 'Victoria Client',
            'amount' => '99.00', 'status' => 'unpaid',
        ]);

        $this->asBurgundy()->get(route('partnership.payment-links'))
            ->assertOk()->assertDontSee('Victoria Client');

        $this->asBurgundy()->patch(route('partnership.payment-links.void', $victorias))->assertNotFound();
        $this->assertSame('unpaid', $victorias->fresh()->status);
    }

    public function test_she_can_void_her_own_unpaid_link(): void
    {
        $this->asBurgundy()->post(route('partnership.payment-links.store'), $this->payload());
        $link = PaymentLink::first();

        $this->asBurgundy()->patch(route('partnership.payment-links.void', $link));

        $this->assertSame('void', $link->fresh()->status);
    }

    public function test_a_service_description_is_required(): void
    {
        $this->asBurgundy()->post(route('partnership.payment-links.store'), $this->payload(['service_description' => '']))
            ->assertSessionHasErrors('service_description');
    }
}
