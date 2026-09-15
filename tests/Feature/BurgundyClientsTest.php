<?php

namespace Tests\Feature;

use App\Models\BurgundyClient;
use App\Models\BurgundyExpense;
use App\Models\Payment;
use App\Models\PaymentLink;
use App\Models\Subscription;
use App\Models\User;
use App\Services\BurgundyLedger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class BurgundyClientsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow('2026-09-14 12:00:00');
        config(['services.burgundy.support_start' => '2026-09-01']);
        $this->actingAs(User::factory()->create());
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_page_renders_with_sidebar_item_cards_and_filters(): void
    {
        BurgundyClient::create(['first_name' => 'Jane', 'last_name' => "O'Neil", 'status' => 'active', 'source' => 'transitioned']);

        $this->get(route('admin.burgundy.index'))
            ->assertOk()
            ->assertSee('Burgundy Clients')
            ->assertSee('Monthly Revenue')
            ->assertSee('Customer Support Cost')
            ->assertSee('Burgundy 50% Share')
            ->assertSee('Victoria 50% Share')
            ->assertSee('Payment Pending')
            ->assertSee('Cancelled')
            ->assertSee('Next Action')
            ->assertSee('Jane');
    }

    public function test_net_profit_and_split_follow_the_formula(): void
    {
        $this->post(route('admin.burgundy.store'), [
            'first_name' => 'Tia', 'last_name' => 'Brown', 'email' => 'tia@example.com', 'status' => 'signed_up',
        ])->assertRedirect();
        $client = BurgundyClient::firstOrFail();

        $this->post(route('admin.burgundy.payments.store', $client), ['amount' => 300])->assertRedirect();
        $this->post(route('admin.burgundy.rounds.store', $client))->assertRedirect();
        BurgundyExpense::create(['description' => 'Mail', 'amount' => 50, 'expense_date' => '2026-09-10', 'status' => 'approved']);
        BurgundyExpense::create(['description' => 'Pending', 'amount' => 20, 'expense_date' => '2026-09-10', 'status' => 'pending']);

        $client->refresh();
        $this->assertSame('active', $client->status); // payment → paid, first round → active
        $this->assertSame(1, $client->current_round);

        $m = BurgundyLedger::financials(now()->startOfMonth(), now()->endOfMonth());

        // 300 revenue − 15 (1 round) − 200 (2 weeks support) − 50 approved expenses
        $this->assertEquals(300.0, $m['revenue']);
        $this->assertEquals(15.0, $m['backend']);
        $this->assertEquals(200.0, $m['support']);
        $this->assertEquals(50.0, $m['expenses']);
        $this->assertEquals(35.0, $m['net']);
        $this->assertEquals(17.5, $m['burgundy']);
        $this->assertEquals(17.5, $m['victoria']);
    }

    public function test_revenue_reuses_subscription_payments_and_payment_links(): void
    {
        $sub = Subscription::create([
            'first_name' => 'Ray', 'last_name' => 'Cole', 'email' => 'ray@example.com',
            'plan_key' => 'monthly', 'plan_label' => 'Monthly', 'amount' => 197, 'status' => 'active',
        ]);
        Payment::create(['subscription_id' => $sub->id, 'transaction_id' => 't1', 'amount' => 197, 'type' => 'initial', 'status' => 'captured', 'charged_at' => now()]);

        // Auto-linked by email, and flipped to paid from the captured payment.
        $this->post(route('admin.burgundy.store'), ['first_name' => 'Ray', 'email' => 'ray@example.com', 'status' => 'invited']);
        $client = BurgundyClient::firstOrFail();
        $this->assertSame($sub->id, (int) $client->subscription_id);
        $this->assertSame('paid', $client->status);

        // Payment link sent from the Burgundy page → counted once it's paid.
        $this->post(route('admin.burgundy.payments.link', $client), ['amount' => 100])->assertSessionHas('generated_link');
        $link = PaymentLink::firstOrFail();
        $link->update(['status' => 'paid', 'paid_at' => now()]);

        // Prior (pre-transition) money shows in Amount Paid but not in the split.
        $this->post(route('admin.burgundy.payments.store', $client), ['amount' => 500, 'prior' => 1]);

        $this->get(route('admin.burgundy.show', $client))->assertOk()->assertSee('$797.00');

        $m = BurgundyLedger::financials(now()->startOfMonth(), now()->endOfMonth());
        $this->assertEquals(297.0, $m['revenue']);
    }

    public function test_import_creates_transitioned_clients_and_skips_duplicates(): void
    {
        $csv = "name,email,phone,status,current_round,amount_paid,signup_date\n"
             . "Jane Smith,jane@example.com,555-1,Payment Pending,3,297,2026-06-02\n"
             . "Jane Again,jane@example.com,555-2,Active,1,,\n";

        $this->post(route('admin.burgundy.import'), ['csv' => $csv])->assertRedirect(route('admin.burgundy.index'));

        $this->assertSame(1, BurgundyClient::count());
        $jane = BurgundyClient::firstOrFail();
        $this->assertSame('transitioned', $jane->source);
        $this->assertSame('payment_pending', $jane->status);
        $this->assertSame(3, $jane->current_round);
        $this->assertEquals(297.0, BurgundyLedger::collectedByClient(collect([$jane]))[$jane->id]);
    }

    public function test_status_filter_and_reviewer_block(): void
    {
        BurgundyClient::create(['first_name' => 'Paused', 'last_name' => 'Person', 'status' => 'paused']);
        BurgundyClient::create(['first_name' => 'Active', 'last_name' => 'Person', 'status' => 'active']);

        $this->get(route('admin.burgundy.index', ['status' => 'paused']))
            ->assertOk()->assertSee('Paused Person')->assertDontSee('Active Person');

        $this->withSession(['review_mode' => true])
            ->get(route('admin.burgundy.index'))
            ->assertRedirect(route('admin.dashboard'));
    }
}
