<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Recurring (ARB) charges arrive with a new transaction id and — unless the
 * subscription was created with an order invoice — no invoice number, so they
 * match nothing stored at checkout. They used to be discarded outright.
 */
class RecurringPaymentLinkingTest extends TestCase
{
    use RefreshDatabase;

    private Subscription $subscription;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-09-16 10:00:00');

        config([
            'services.authorize_net.api_login_id'              => 'fake_login',
            'services.authorize_net.transaction_key'           => 'fake_key',
            'services.authorize_net.signature_key'             => '',
            'services.authorize_net.webhook_enforce_signature' => false,
        ]);

        $this->subscription = Subscription::create([
            'first_name' => 'Patrick', 'last_name' => 'Cato',
            'email' => 'patrick@example.com', 'phone' => '5551234567',
            'plan_key' => 'monthly', 'plan_label' => 'Monthly Plan',
            'amount' => 197.00, 'recurring_amount' => 100.00,
            'invoice_number' => 'INV-1782306637-476Y', 'transaction_id' => '121688665999',
            'arb_subscription_id' => '73489696', 'status' => 'active',
            'subscribed_at' => '2026-06-24 13:10:00', 'next_billing_date' => '2026-07-24 13:10:00',
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    /** Authorize.Net prefixes its JSON with a BOM. */
    private function anet(array $data)
    {
        return Http::response("\xEF\xBB\xBF" . json_encode($data + [
            'messages' => ['resultCode' => 'Ok', 'message' => [['code' => 'I00001', 'text' => 'Successful.']]],
        ]), 200);
    }

    private function anetError()
    {
        return Http::response("\xEF\xBB\xBF" . json_encode([
            'messages' => ['resultCode' => 'Error', 'message' => [['code' => 'E00040', 'text' => 'not found']]],
        ]), 200);
    }

    private function transactionDetails(array $overrides = []): array
    {
        return ['transaction' => array_merge([
            'transId'           => '121799000001',
            'submitTimeUTC'     => '2026-08-25T18:22:11Z',
            'transactionType'   => 'authCaptureTransaction',
            'transactionStatus' => 'settledSuccessfully',
            'authAmount'        => 100.00,
            'settleAmount'      => 100.00,
            'customer'          => ['email' => 'patrick@example.com'],
            'subscription'      => ['id' => 73489696, 'payNum' => 2],
        ], $overrides)];
    }

    private function capture(string $transactionId, float $amount, ?string $invoice = null): array
    {
        $payload = ['id' => $transactionId, 'responseCode' => 1, 'authAmount' => $amount, 'entityName' => 'transaction'];
        if ($invoice !== null) {
            $payload['invoiceNumber'] = $invoice;
        }

        return [
            'notificationId' => 'notif-' . $transactionId,
            'eventType'      => 'net.authorize.payment.authcapture.created',
            'payload'        => $payload,
        ];
    }

    public function test_rebill_without_an_invoice_number_is_recorded_and_linked(): void
    {
        Http::fake(['*' => $this->anet($this->transactionDetails())]);

        $this->postJson('/authorize-net/webhook', $this->capture('121799000001', 100.00))
            ->assertOk();

        $payment = Payment::where('transaction_id', '121799000001')->first();

        $this->assertNotNull($payment, 'the charge must be recorded, not discarded');
        $this->assertSame($this->subscription->id, (int) $payment->subscription_id);
        $this->assertSame('recurring', $payment->type);
        $this->assertSame('2026-08-25', $payment->charged_at->toDateString());

        // The stuck billing date moves forward.
        $this->assertSame('2026-09-25', $this->subscription->fresh()->next_billing_date->toDateString());
    }

    public function test_charge_is_still_recorded_when_the_lookup_fails(): void
    {
        Http::fake(['*' => $this->anetError()]);

        $this->postJson('/authorize-net/webhook', $this->capture('121799000002', 250.00))->assertOk();

        $payment = Payment::where('transaction_id', '121799000002')->first();

        $this->assertNotNull($payment);
        $this->assertNull($payment->subscription_id, 'unmatched money still belongs in the ledger');
    }

    public function test_a_rebill_carrying_the_invoice_matches_without_an_api_call(): void
    {
        Http::fake(['*' => $this->anetError()]);

        $this->postJson('/authorize-net/webhook', $this->capture('121799000003', 100.00, 'INV-1782306637-476Y'))
            ->assertOk();

        $this->assertSame(
            $this->subscription->id,
            (int) Payment::where('transaction_id', '121799000003')->value('subscription_id')
        );
        Http::assertNothingSent();
    }

    public function test_repeated_delivery_does_not_duplicate_the_payment(): void
    {
        Http::fake(['*' => $this->anet($this->transactionDetails())]);

        $this->postJson('/authorize-net/webhook', $this->capture('121799000001', 100.00))->assertOk();
        $this->postJson('/authorize-net/webhook', $this->capture('121799000001', 100.00))->assertOk();

        $this->assertSame(1, Payment::where('transaction_id', '121799000001')->count());
    }

    public function test_backfill_imports_missing_charges_and_relinks_orphans(): void
    {
        // Already on file, attached to nobody — what the old webhook produced.
        $orphan = Payment::create([
            'transaction_id' => '950001', 'invoice_number' => '4pxSpiAU5',
            'amount' => 100.00, 'type' => 'recurring', 'status' => 'captured',
            'charged_at' => '2026-09-11 11:39:00',
        ]);

        Http::fake(function ($request) {
            $body = json_decode($request->body(), true);

            if (isset($body['getSettledBatchListRequest'])) {
                return $this->anet(['batchList' => [['batchId' => '555']]]);
            }

            if (isset($body['getTransactionListRequest'])) {
                return $this->anet(['transactions' => [
                    ['transId' => '900001', 'submitTimeUTC' => '2026-07-27T12:00:00Z', 'transactionStatus' => 'settledSuccessfully', 'settleAmount' => 100.00],
                ]]);
            }

            if (isset($body['getTransactionDetailsRequest'])) {
                $id = $body['getTransactionDetailsRequest']['transId'];

                return $this->anet($this->transactionDetails([
                    'transId'       => $id,
                    'submitTimeUTC' => $id === '900001' ? '2026-07-27T12:00:00Z' : '2026-09-11T11:39:00Z',
                    'subscription'  => ['id' => 73489696, 'payNum' => $id === '900001' ? 2 : 3],
                ]));
            }

            return $this->anetError();
        });

        Artisan::call('payments:backfill-authnet', ['--days' => 180]);

        $imported = Payment::where('transaction_id', '900001')->first();
        $this->assertNotNull($imported, 'a settled charge missing from the dashboard is imported');
        $this->assertSame($this->subscription->id, (int) $imported->subscription_id);
        $this->assertSame('2026-07-27', $imported->charged_at->toDateString());

        $this->assertSame(
            $this->subscription->id,
            (int) $orphan->fresh()->subscription_id,
            'charges already on file as Unlinked get attached'
        );

        // Idempotent: a second run changes nothing.
        $count = Payment::count();
        Artisan::call('payments:backfill-authnet', ['--days' => 180]);
        $this->assertSame($count, Payment::count());
    }
}
