<?php

namespace Tests\Feature;

use App\Models\PaymentAgreement;
use App\Models\Subscription;
use App\Services\BurgundyLedger;
use App\Services\ServiceAgreement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Two partnership prices run side by side: $100 for Burgundy's transferring
 * clients and $149 for new signups.
 *
 * The rule that matters: a signed contract quotes the amount the card was
 * actually charged. A couple refunded because the document showed a different
 * figure from the one they paid, which is what happens when a contract is
 * rendered from a price list instead of from the transaction.
 */
class PartnershipPricingTest extends TestCase
{
    use RefreshDatabase;

    private function subscription(string $planKey, string $amount, ?string $monthly, string $invoice): Subscription
    {
        return Subscription::create([
            'first_name'       => 'Nina',
            'last_name'        => 'Okafor',
            'email'            => 'nina@example.com',
            'plan_key'         => $planKey,
            'plan_label'       => 'Credit Restoration Program',
            'amount'           => $amount,
            'recurring_amount' => $monthly,
            'invoice_number'   => $invoice,
            'status'           => 'active',
            'subscribed_at'    => now(),
        ]);
    }

    private function paidSession(string $invoice): array
    {
        return [
            'burgundy_paid'     => true,
            'burgundy_invoice'  => $invoice,
            'burgundy_customer' => ['first_name' => 'Nina', 'last_name' => 'Okafor', 'email' => 'nina@example.com'],
        ];
    }

    // ── The two links ────────────────────────────────────────────────────────

    public function test_each_link_quotes_its_own_price(): void
    {
        $this->get('/burgundy-checkout')->assertOk()->assertSee('$100.00')->assertDontSee('$149.00');
        $this->get('/burgundy-new-client')->assertOk()->assertSee('$149.00')->assertDontSee('$100.00');
    }

    public function test_the_legacy_link_is_unchanged_so_links_already_sent_still_work(): void
    {
        $this->get('/burgundy-checkout')->assertOk();
    }

    // ── The contract quotes the transaction, not the price list ──────────────

    public function test_a_149_client_signs_a_contract_that_says_149(): void
    {
        $this->subscription('burgundy-149', '149.00', '149.00', 'BG-NEW-1');

        $this->withSession($this->paidSession('BG-NEW-1'))
            ->get('/burgundy-agreement')
            ->assertOk()
            ->assertSee('149.00')
            ->assertDontSee('100.00');
    }

    public function test_a_legacy_client_still_signs_a_contract_that_says_100(): void
    {
        $this->subscription('burgundy-100', '100.00', '100.00', 'BG-OLD-1');

        $this->withSession($this->paidSession('BG-OLD-1'))
            ->get('/burgundy-agreement')
            ->assertOk()
            ->assertSee('100.00')
            ->assertDontSee('149.00');
    }

    public function test_the_stored_contract_records_what_was_charged(): void
    {
        $this->subscription('burgundy-149', '149.00', '149.00', 'BG-NEW-2');

        $this->withSession($this->paidSession('BG-NEW-2'))
            ->post('/burgundy-agreement/sign', [
                'full_name'      => 'Nina Okafor',
                'signature_data' => 'data:image/png;base64,iVBORw0KGgo=',
                'agree_terms'    => '1',
            ])->assertOk();

        $a = PaymentAgreement::first();
        $this->assertNotNull($a);
        $this->assertSame('burgundy-149', $a->plan_key);
        $this->assertSame('149.00', (string) $a->deposit_amount);
        $this->assertSame('149.00', (string) $a->installment_amount);

        // The frozen text is the thing a client would wave at you in a dispute.
        $this->assertStringContainsString('$149.00', $a->contract_text);
        $this->assertStringNotContainsString('$100.00', $a->contract_text);
    }

    public function test_changing_the_price_list_does_not_alter_an_existing_client(): void
    {
        $this->subscription('burgundy-149', '149.00', '149.00', 'BG-NEW-3');

        // Someone edits the menu later.
        config(['partnership.plans.new.enrollment' => '199.00', 'partnership.plans.new.monthly' => '199.00']);

        $this->withSession($this->paidSession('BG-NEW-3'))
            ->get('/burgundy-agreement')
            ->assertOk()
            ->assertSee('149.00')
            ->assertDontSee('199.00');
    }

    // ── Contract content ─────────────────────────────────────────────────────

    public function test_the_contract_leads_with_the_price_and_omits_credit_reporting_law(): void
    {
        $text = ServiceAgreement::build([
            'client_name'      => 'Nina Okafor',
            'plan_label'       => 'Credit Restoration Program',
            'charged_today'    => 149.00,
            'recurring_amount' => 149.00,
        ]);

        $this->assertStringContainsString('WHAT YOU ARE PAYING', $text);
        $this->assertStringContainsString('Charged today:  $149.00', $text);
        $this->assertStringContainsString('$149.00 per month until you cancel', $text);
        $this->assertStringContainsString('None — cancel any time', $text);

        // Services agreement only — no statutory disclosures.
        foreach (['Fair Credit Reporting', 'FCRA', 'three business day', 'seven years', 'YOUR RIGHTS'] as $banned) {
            $this->assertStringNotContainsString($banned, $text);
        }
    }

    public function test_a_one_time_sale_says_so_instead_of_inventing_a_subscription(): void
    {
        $text = ServiceAgreement::build([
            'client_name'   => 'Nina Okafor',
            'plan_label'    => 'Fast Dispute',
            'charged_today' => 297.00,
        ]);

        $this->assertStringContainsString('Charged today:  $297.00', $text);
        $this->assertStringContainsString('This is a one-time payment.', $text);
        $this->assertStringNotContainsString('per month', $text);
    }

    public function test_a_custom_service_description_replaces_the_default_scope(): void
    {
        $text = ServiceAgreement::build([
            'client_name'         => 'Nina Okafor',
            'plan_label'          => 'Consultation',
            'charged_today'       => 250.00,
            'service_description' => 'One 90-minute credit strategy consultation.',
        ]);

        $this->assertStringContainsString('One 90-minute credit strategy consultation.', $text);
    }

    // ── Reporting across two prices ──────────────────────────────────────────

    public function test_monthly_revenue_sums_the_real_amounts_not_a_single_price(): void
    {
        $this->subscription('burgundy-100', '100.00', '100.00', 'BG-A');
        $this->subscription('burgundy-100', '100.00', '100.00', 'BG-B');
        $this->subscription('burgundy-149', '149.00', '149.00', 'BG-C');

        $m = (new BurgundyLedger())->metrics();

        // 100 + 100 + 149 — not 3 x either price.
        $this->assertSame(349.0, (float) $m['monthly_revenue']);
    }

    public function test_both_tiers_count_towards_the_partnership(): void
    {
        $this->subscription('burgundy-100', '100.00', '100.00', 'BG-D');
        $this->subscription('burgundy-149', '149.00', '149.00', 'BG-E');

        $this->assertSame(2, (new BurgundyLedger())->subscriptions()->count());
    }
}
