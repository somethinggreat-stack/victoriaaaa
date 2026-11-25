<?php

namespace Tests\Feature;

use App\Models\PaymentAgreement;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/** Black Friday sale: landing page, checkout, and the hand-off after payment. */
class BlackFridaySaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_sale_page_shows_both_prices_and_links_to_their_checkouts(): void
    {
        $this->get('/black-friday')->assertOk()
            ->assertSee('$99')->assertSee('$199')
            ->assertSee('The sale is open now')
            ->assertSee(route('checkout.show', 'black-friday'), false)
            ->assertSee(route('checkout.show', 'black-friday-couples'), false);
    }

    public function test_sale_prices_never_appear_in_the_regular_checkout_switcher(): void
    {
        $this->get(route('checkout.show', 'fast-track'))->assertOk()
            ->assertDontSee('value="black-friday', false);

        $this->get(route('checkout.show', 'black-friday'))->assertOk()
            ->assertSee('value="black-friday-couples"', false)
            ->assertDontSee('value="fast-track"', false);
    }

    public function test_paying_charges_once_then_goes_to_the_agreement_and_on_to_onboarding(): void
    {
        config([
            'services.authorize_net.api_login_id'    => 'test',
            'services.authorize_net.transaction_key' => 'test',
            'services.google.sheets_webhook_url'     => null,
            'services.meta.pixel_id'                 => null,
        ]);

        Http::fake(['*authorize.net*' => Http::response([
            'messages'            => ['resultCode' => 'Ok'],
            'transactionResponse' => ['responseCode' => '1', 'transId' => '123', 'authCode' => 'ABC'],
        ])]);

        $response = $this->postJson(route('checkout.process'), [
            'cardNumber' => '4111111111111111', 'expMonth' => '12', 'expYear' => '2030', 'cardCode' => '123',
            'first_name' => 'Test', 'last_name' => 'Buyer', 'email' => 'buyer@example.com', 'phone' => '5551112222',
            'address' => '1 St', 'city' => 'Dallas', 'state' => 'TX', 'zip' => '75001', 'cardName' => 'Test Buyer',
            'selected_plan' => 'black-friday', 'agree_terms' => '1', 'agree_privacy' => '1',
        ])->assertOk();

        Http::assertSentCount(1); // the charge only: no payment profile, no recurring subscription

        $subscription = Subscription::firstOrFail();
        $this->assertSame(99.0, (float) $subscription->amount);
        $this->assertNull($subscription->recurring_amount);

        $agreement = PaymentAgreement::firstOrFail();
        $this->assertStringContainsString('/agreement/' . $agreement->id, $response->json('redirect'));
        $this->assertStringEndsWith('/onboarding', $agreement->next_url);
        $this->assertStringContainsString('three (3) months', $agreement->service_description);
    }
}
