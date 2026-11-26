<?php

namespace Tests\Feature;

use App\Models\PaymentLink;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Every checkout now takes Authorize.Net's Accept.js token (opaqueData) instead
 * of a raw card number, expiry and CVV. The card itself never reaches our
 * server, so the request to Authorize.Net must carry `opaqueData`, never
 * `creditCard`.
 */
class AcceptJsOnlyCheckoutTest extends TestCase
{
    use RefreshDatabase;

    private function fakeAuthorizeNet(): void
    {
        config([
            'services.authorize_net.api_login_id'    => 'test',
            'services.authorize_net.transaction_key' => 'test',
        ]);

        Http::fake(['*authorize.net*' => Http::response([
            'messages'            => ['resultCode' => 'Ok'],
            'transactionResponse' => ['responseCode' => '1', 'transId' => '123', 'authCode' => 'ABC'],
        ])]);
    }

    private function assertChargeUsedOpaqueData(): void
    {
        Http::assertSent(function ($request) {
            $payment = $request->data()['createTransactionRequest']['transactionRequest']['payment'] ?? null;

            return $payment !== null
                && array_key_exists('opaqueData', $payment)
                && ! array_key_exists('creditCard', $payment)
                && $payment['opaqueData']['dataValue'] === 'fake-opaque-value';
        });
    }

    private function opaqueFields(): array
    {
        return ['dataDescriptor' => 'COMMON.ACCEPT.INAPP.PAYMENT', 'dataValue' => 'fake-opaque-value'];
    }

    public function test_the_main_checkout_rejects_a_raw_card_number_and_takes_a_token_instead(): void
    {
        $this->fakeAuthorizeNet();

        $this->postJson(route('checkout.process'), [
            'cardNumber' => '4111111111111111', 'expMonth' => '12', 'expYear' => '2030', 'cardCode' => '123',
            'first_name' => 'Test', 'last_name' => 'Buyer', 'email' => 'buyer@example.com', 'phone' => '5551112222',
            'address' => '1 St', 'city' => 'Dallas', 'state' => 'TX', 'zip' => '75001', 'cardName' => 'Test Buyer',
            'selected_plan' => 'audit', 'agree_terms' => '1', 'agree_privacy' => '1',
        ])->assertJsonValidationErrors(['dataDescriptor', 'dataValue']);

        $this->postJson(route('checkout.process'), $this->opaqueFields() + [
            'first_name' => 'Test', 'last_name' => 'Buyer', 'email' => 'buyer@example.com', 'phone' => '5551112222',
            'address' => '1 St', 'city' => 'Dallas', 'state' => 'TX', 'zip' => '75001', 'cardName' => 'Test Buyer',
            'selected_plan' => 'audit', 'agree_terms' => '1', 'agree_privacy' => '1',
        ])->assertOk();

        $this->assertChargeUsedOpaqueData();
    }

    public function test_burgundys_checkout_takes_a_token_instead_of_a_card(): void
    {
        $this->fakeAuthorizeNet();

        $this->postJson(route('burgundy.checkout.process', ['tier' => 'legacy']), $this->opaqueFields() + [
            'first_name' => 'Test', 'last_name' => 'Client', 'email' => 'client@example.com', 'phone' => '5551112222',
            'address' => '1 St', 'city' => 'Dallas', 'state' => 'TX', 'zip' => '75001', 'cardName' => 'Test Client',
            'agree_terms' => '1', 'agree_privacy' => '1',
        ])->assertOk();

        $this->assertChargeUsedOpaqueData();
    }

    public function test_the_custom_link_checkout_takes_a_token_instead_of_a_card(): void
    {
        $this->fakeAuthorizeNet();

        $this->postJson(route('custom-pay.process'), $this->opaqueFields() + [
            'token' => 'vlc-4m8t6w1z', // one-time only — no CIM/ARB follow-up to fake
            'first_name' => 'Test', 'last_name' => 'Client', 'email' => 'client@example.com', 'phone' => '5551112222',
            'address' => '1 St', 'city' => 'Dallas', 'state' => 'TX', 'zip' => '75001', 'cardName' => 'Test Client',
            'agree_terms' => '1', 'agree_privacy' => '1',
        ])->assertOk();

        $this->assertChargeUsedOpaqueData();
    }

    public function test_an_admin_payment_link_takes_a_token_instead_of_a_card(): void
    {
        $this->fakeAuthorizeNet();

        $link = PaymentLink::create([
            'token' => 'pl_' . Str::random(28), 'client_name' => 'Test Client', 'amount' => '100.00',
            'service_description' => 'Test service', 'status' => 'unpaid',
        ]);

        $this->postJson(route('pay.process'), $this->opaqueFields() + [
            'token' => $link->token,
            'first_name' => 'Test', 'last_name' => 'Client', 'email' => 'client@example.com', 'phone' => '5551112222',
            'address' => '1 St', 'city' => 'Dallas', 'state' => 'TX', 'zip' => '75001', 'cardName' => 'Test Client',
            'agree_terms' => '1', 'agree_privacy' => '1',
        ])->assertOk();

        $this->assertChargeUsedOpaqueData();
    }

    public function test_no_checkout_controller_still_builds_a_creditcard_payload(): void
    {
        foreach ([
            'app/Http/Controllers/AcceptJsPaymentController.php',
            'app/Http/Controllers/BurgundyCheckoutController.php',
            'app/Http/Controllers/CustomCheckoutController.php',
            'app/Http/Controllers/PayController.php',
        ] as $file) {
            $src = file_get_contents(base_path($file));
            $this->assertStringNotContainsString("'creditCard'", $src, $file);
            $this->assertStringNotContainsString('cardNumber', $src, $file);
            $this->assertStringContainsString("'opaqueData'", $src, $file);
        }
    }
}
