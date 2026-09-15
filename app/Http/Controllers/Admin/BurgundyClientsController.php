<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BurgundyClient;
use App\Models\BurgundyExpense;
use App\Models\BurgundyPayment;
use App\Models\BurgundyRound;
use App\Models\Payment;
use App\Models\PaymentLink;
use App\Models\Subscription;
use App\Services\BurgundyLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Burgundy Clients — separate from Paid Credit Repair Clients. Tracks the
 * client pipeline (invited → active), rounds Burgundy processes, and the
 * 50/50 profit split.
 */
class BurgundyClientsController extends Controller
{
    private const MIGRATION = 'database/migrations/2026_09_15_000000_create_burgundy_tables.php';

    // ════════════════════════════════════════════════════════════════
    // Clients list + summary
    // ════════════════════════════════════════════════════════════════
    public function index(Request $request)
    {
        if (! BurgundyLedger::isInstalled()) {
            return view('admin.burgundy.index', ['needsSetup' => true]);
        }

        BurgundyLedger::sync();

        [$period, $from, $to] = $this->period($request);

        $status = (string) $request->query('status', '');
        if (! array_key_exists($status, BurgundyClient::STATUSES)) {
            $status = '';
        }

        $q = BurgundyClient::query()->with('subscription:id,status');
        if ($status !== '') {
            $q->where('status', $status);
        }
        if ($search = trim((string) $request->query('q', ''))) {
            foreach (preg_split('/\s+/', $search) as $term) {
                $q->where(function ($w) use ($term) {
                    $w->where('first_name', 'like', "%{$term}%")
                      ->orWhere('last_name', 'like', "%{$term}%")
                      ->orWhere('email', 'like', "%{$term}%")
                      ->orWhere('phone', 'like', "%{$term}%");
                });
            }
        }

        $rows = $q->orderByDesc('last_activity_at')->latest()->paginate(25)->withQueryString();

        $pageClients = $rows->getCollection();
        $amountPaid  = BurgundyLedger::collectedByClient($pageClients);
        $pendingIds  = BurgundyPayment::whereIn('burgundy_client_id', $pageClients->pluck('id'))
            ->where('status', 'pending')->pluck('burgundy_client_id')->flip();

        $counts = BurgundyClient::selectRaw('status, COUNT(*) as c')->groupBy('status')->pluck('c', 'status');

        return view('admin.burgundy.index', [
            'needsSetup'  => false,
            'rows'        => $rows,
            'counts'      => $counts,
            'total'       => (int) $counts->sum(),
            'status'      => $status,
            'period'      => $period,
            'periods'     => $this->periodOptions(),
            'money'       => BurgundyLedger::financials($from, $to),
            'amountPaid'  => $amountPaid,
            'pendingIds'  => $pendingIds,
            'roundCost'   => BurgundyLedger::roundCost(),
        ]);
    }

    /** One-click table install for servers where `php artisan migrate` isn't run. */
    public function setup()
    {
        try {
            Artisan::call('migrate', ['--path' => self::MIGRATION, '--force' => true]);
        } catch (\Throwable $e) {
            Log::error('[Burgundy] setup failed', ['message' => $e->getMessage()]);
            return back()->with('error', 'Could not create the Burgundy tables: ' . $e->getMessage());
        }

        return BurgundyLedger::isInstalled()
            ? redirect()->route('admin.burgundy.index')->with('success', 'Burgundy Clients is set up and ready.')
            : back()->with('error', 'Tables were not created. Run database/setup.sql in phpMyAdmin instead.');
    }

    public function store(Request $request)
    {
        $data = $this->validateClient($request);

        $data['source']           = $data['source'] ?? 'new';
        $data['current_round']    = $data['current_round'] ?? 0;
        $data['last_activity_at'] = now();
        $data['invited_at']       = now();

        // Reuse an existing checkout subscription for this email if there is one.
        if (empty($data['subscription_id']) && ! empty($data['email'])) {
            $data['subscription_id'] = Subscription::where('email', $data['email'])->latest()->value('id');
        }

        $client = new BurgundyClient($data);
        $client->save();
        $client->setStatus($data['status']);

        BurgundyLedger::sync();

        return redirect()->route('admin.burgundy.show', $client)->with('success', "{$client->full_name} added to Burgundy Clients.");
    }

    /**
     * Bulk-add existing Burgundy clients from pasted CSV. Header row required;
     * recognised columns: name | first_name, last_name, email, phone, status,
     * current_round, amount_paid, signup_date, next_action.
     */
    public function import(Request $request)
    {
        $request->validate(['csv' => ['required', 'string', 'max:200000']]);

        $lines = array_values(array_filter(preg_split('/\r\n|\r|\n/', trim($request->csv)), fn ($l) => trim($l) !== ''));
        if (count($lines) < 2) {
            return back()->with('error', 'Paste a header row plus at least one client row.');
        }

        $header = array_map(fn ($h) => Str::snake(trim(strtolower($h))), str_getcsv(array_shift($lines)));
        if (! in_array('name', $header, true) && ! in_array('first_name', $header, true)) {
            return back()->with('error', 'The header row needs a "name" or "first_name" column.');
        }

        $statusLookup = [];
        foreach (BurgundyClient::STATUSES as $key => $label) {
            $statusLookup[$key] = $key;
            $statusLookup[Str::snake(strtolower($label))] = $key;
        }

        $created = 0;
        $skipped = [];

        DB::transaction(function () use ($lines, $header, $statusLookup, &$created, &$skipped) {
            foreach ($lines as $i => $line) {
                $cells = str_getcsv($line);
                $row   = [];
                foreach ($header as $idx => $key) {
                    $row[$key] = trim((string) ($cells[$idx] ?? ''));
                }

                $first = $row['first_name'] ?? '';
                $last  = $row['last_name'] ?? '';
                if ($first === '' && ! empty($row['name'])) {
                    [$first, $last] = array_pad(explode(' ', $row['name'], 2), 2, '');
                    $first = rtrim($first, ',');
                }
                $email = strtolower($row['email'] ?? '');

                if ($first === '') {
                    $skipped[] = 'row ' . ($i + 2) . ' (no name)';
                    continue;
                }
                if ($email !== '' && (! filter_var($email, FILTER_VALIDATE_EMAIL) || BurgundyClient::where('email', $email)->exists())) {
                    $skipped[] = $email;
                    continue;
                }

                $signup = null;
                if (! empty($row['signup_date'])) {
                    try { $signup = Carbon::parse($row['signup_date']); } catch (\Throwable $e) { $signup = null; }
                }

                $client = BurgundyClient::create([
                    'first_name'       => mb_substr($first, 0, 100),
                    'last_name'        => mb_substr(trim($last), 0, 100) ?: null,
                    'email'            => $email ?: null,
                    'phone'            => mb_substr($row['phone'] ?? '', 0, 30) ?: null,
                    'status'           => $statusLookup[Str::snake(strtolower($row['status'] ?? ''))] ?? 'active',
                    'source'           => 'transitioned',
                    'subscription_id'  => $email ? Subscription::where('email', $email)->latest()->value('id') : null,
                    'current_round'    => max(0, min(99, (int) ($row['current_round'] ?? 0))),
                    'signed_up_at'     => $signup,
                    'next_action'      => mb_substr($row['next_action'] ?? '', 0, 255) ?: null,
                    'last_activity_at' => now(),
                ]);

                $prior = (float) preg_replace('/[^\d.]/', '', $row['amount_paid'] ?? '');
                if ($prior > 0) {
                    BurgundyPayment::create([
                        'burgundy_client_id'   => $client->id,
                        'amount'               => round($prior, 2),
                        'status'               => 'paid',
                        'method'               => 'Before transition',
                        'counts_toward_profit' => false,
                        'paid_at'              => $signup ?? now(),
                    ]);
                    if (! $client->paid_at) {
                        $client->update(['paid_at' => $signup ?? now()]);
                    }
                }

                $created++;
            }
        });

        $msg = "Imported {$created} Burgundy client" . ($created === 1 ? '' : 's') . '.';
        if ($skipped) {
            $msg .= ' Skipped ' . count($skipped) . ' (duplicate, invalid email or missing name): ' . Str::limit(implode(', ', $skipped), 200);
        }

        return redirect()->route('admin.burgundy.index')->with('success', $msg);
    }

    // ════════════════════════════════════════════════════════════════
    // Single client
    // ════════════════════════════════════════════════════════════════
    public function show(BurgundyClient $burgundyClient)
    {
        BurgundyLedger::sync();
        $client = $burgundyClient->fresh(['rounds', 'payments.paymentLink', 'subscription']);

        $subscriptionOptions = Subscription::query()
            ->when($client->email, fn ($q) => $q->where('email', $client->email))
            ->when(! $client->email, fn ($q) => $q->whereRaw('1 = 0'))
            ->orWhere('id', $client->subscription_id)
            ->latest()->get(['id', 'first_name', 'last_name', 'plan_label', 'status', 'created_at']);

        $gatewayPayments = $client->subscription_id
            ? Payment::where('subscription_id', $client->subscription_id)->latest('charged_at')->get()
            : collect();

        $allTime = BurgundyLedger::collectedByClient(collect([$client]))[$client->id];

        return view('admin.burgundy.show', [
            'client'              => $client,
            'subscriptionOptions' => $subscriptionOptions,
            'gatewayPayments'     => $gatewayPayments,
            'amountPaid'          => $allTime,
            'backendCost'         => (float) $client->rounds->sum('cost'),
            'roundCost'           => BurgundyLedger::roundCost(),
            'linksAvailable'      => Schema::hasTable('payment_links'),
        ]);
    }

    public function update(BurgundyClient $burgundyClient, Request $request)
    {
        $data = $this->validateClient($request, $burgundyClient);
        $status = $data['status'];
        unset($data['status']);

        $burgundyClient->fill($data);
        $burgundyClient->setStatus($status); // saves + stamps milestones

        BurgundyLedger::sync();

        return back()->with('success', 'Client updated.');
    }

    public function status(BurgundyClient $burgundyClient, Request $request)
    {
        $request->validate(['status' => ['required', Rule::in(array_keys(BurgundyClient::STATUSES))]]);
        $burgundyClient->setStatus($request->status);

        return back()->with('success', "{$burgundyClient->full_name} is now {$burgundyClient->status_label}.");
    }

    public function destroy(BurgundyClient $burgundyClient)
    {
        // Don't leave a live payment link around for a deleted client.
        if (Schema::hasTable('payment_links')) {
            $linkIds = $burgundyClient->payments()->where('status', 'pending')->whereNotNull('payment_link_id')->pluck('payment_link_id');
            PaymentLink::whereIn('id', $linkIds)->where('status', 'unpaid')->update(['status' => 'void']);
        }

        $name = $burgundyClient->full_name;
        $burgundyClient->delete();

        return redirect()->route('admin.burgundy.index')->with('success', "{$name} deleted from Burgundy Clients.");
    }

    // ─── Rounds ───
    public function storeRound(BurgundyClient $burgundyClient, Request $request)
    {
        $data = $request->validate([
            'round_number' => ['nullable', 'integer', 'min:1', 'max:99'],
            'processed_at' => ['nullable', 'date'],
            'cost'         => ['nullable', 'numeric', 'min:0', 'max:10000'],
            'note'         => ['nullable', 'string', 'max:255'],
        ]);

        $number = (int) ($data['round_number'] ?? $burgundyClient->current_round + 1);

        $burgundyClient->rounds()->create([
            'round_number' => $number,
            'processed_at' => ! empty($data['processed_at']) ? Carbon::parse($data['processed_at']) : now(),
            'cost'         => $data['cost'] ?? BurgundyLedger::roundCost(),
            'note'         => $data['note'] ?? null,
        ]);

        $burgundyClient->current_round = max($burgundyClient->current_round, $number);
        if ($burgundyClient->status === 'paid') {
            $burgundyClient->setStatus('active');
        } else {
            $burgundyClient->last_activity_at = now();
            $burgundyClient->save();
        }

        return back()->with('success', "Round {$number} logged for {$burgundyClient->full_name}.");
    }

    public function destroyRound(BurgundyClient $burgundyClient, BurgundyRound $round)
    {
        abort_unless((int) $round->burgundy_client_id === (int) $burgundyClient->id, 404);

        $round->delete();

        if ($round->round_number === $burgundyClient->current_round) {
            $burgundyClient->current_round = max(0, $round->round_number - 1);
        }
        $burgundyClient->last_activity_at = now();
        $burgundyClient->save();

        return back()->with('success', "Round {$round->round_number} removed.");
    }

    // ─── Payments ───
    public function storePayment(BurgundyClient $burgundyClient, Request $request)
    {
        $data = $request->validate([
            'amount'  => ['required', 'numeric', 'min:0.01', 'max:100000'],
            'paid_at' => ['nullable', 'date'],
            'method'  => ['nullable', 'string', 'max:40'],
            'note'    => ['nullable', 'string', 'max:255'],
            'prior'   => ['nullable', 'boolean'],
        ]);

        $paidAt = ! empty($data['paid_at']) ? Carbon::parse($data['paid_at']) : now();

        $burgundyClient->payments()->create([
            'amount'               => round((float) $data['amount'], 2),
            'status'               => 'paid',
            'method'               => $data['method'] ?? null,
            'counts_toward_profit' => ! $request->boolean('prior'),
            'paid_at'              => $paidAt,
            'note'                 => $data['note'] ?? null,
        ]);

        $burgundyClient->markPaidIfWaiting($paidAt);
        $burgundyClient->touchActivity();

        return back()->with('success', 'Payment of $' . number_format((float) $data['amount'], 2) . ' recorded.');
    }

    /** Send a one-time payment link (reuses the existing Payment Links feature). */
    public function storePaymentLink(BurgundyClient $burgundyClient, Request $request)
    {
        if (! Schema::hasTable('payment_links')) {
            return back()->with('error', 'The payment_links table does not exist yet. Please run the setup SQL first.');
        }

        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:1', 'max:100000'],
            'note'   => ['nullable', 'string', 'max:255'],
        ]);

        do {
            $token = 'pl_' . Str::random(28);
        } while (PaymentLink::where('token', $token)->exists());

        $link = PaymentLink::create([
            'token'       => $token,
            'client_name' => $burgundyClient->full_name,
            'email'       => $burgundyClient->email,
            'amount'      => number_format((float) $data['amount'], 2, '.', ''),
            'note'        => $data['note'] ?? 'Credit Repair — monthly service',
            'status'      => 'unpaid',
        ]);

        $burgundyClient->payments()->create([
            'amount'          => $link->amount,
            'status'          => 'pending',
            'method'          => 'Payment link',
            'payment_link_id' => $link->id,
            'note'            => $link->note,
        ]);

        if (in_array($burgundyClient->status, ['invited', 'signed_up'], true)) {
            $burgundyClient->setStatus('payment_pending');
        } else {
            $burgundyClient->touchActivity();
        }

        return back()
            ->with('success', 'Payment link generated — copy it below and send it to your client.')
            ->with('generated_link', $link->url);
    }

    public function destroyPayment(BurgundyClient $burgundyClient, BurgundyPayment $payment)
    {
        abort_unless((int) $payment->burgundy_client_id === (int) $burgundyClient->id, 404);

        if ($payment->payment_link_id && $payment->status === 'pending' && Schema::hasTable('payment_links')) {
            PaymentLink::where('id', $payment->payment_link_id)->where('status', 'unpaid')->update(['status' => 'void']);
        }

        $payment->delete();
        $burgundyClient->touchActivity();

        return back()->with('success', 'Payment record removed.');
    }

    // ════════════════════════════════════════════════════════════════
    // Other expenses (only approved ones hit net profit)
    // ════════════════════════════════════════════════════════════════
    public function expenses(Request $request)
    {
        if (! BurgundyLedger::isInstalled()) {
            return redirect()->route('admin.burgundy.index');
        }

        [$period, $from, $to] = $this->period($request);

        $q = BurgundyExpense::query()->whereDate('expense_date', '<=', $to);
        if ($from) {
            $q->whereDate('expense_date', '>=', $from);
        }
        if (in_array($request->query('status'), BurgundyExpense::STATUSES, true)) {
            $q->where('status', $request->query('status'));
        }

        $totals = (clone $q)->reorder()->selectRaw('status, SUM(amount) as total')->groupBy('status')->pluck('total', 'status');

        return view('admin.burgundy.expenses', [
            'rows'    => $q->orderByDesc('expense_date')->latest()->paginate(30)->withQueryString(),
            'totals'  => $totals,
            'period'  => $period,
            'periods' => $this->periodOptions(),
        ]);
    }

    public function storeExpense(Request $request)
    {
        $data = $request->validate([
            'description'  => ['required', 'string', 'max:150'],
            'amount'       => ['required', 'numeric', 'min:0.01', 'max:1000000'],
            'expense_date' => ['required', 'date'],
            'status'       => ['required', Rule::in(BurgundyExpense::STATUSES)],
            'notes'        => ['nullable', 'string', 'max:255'],
        ]);

        BurgundyExpense::create($data);

        return back()->with('success', 'Expense added.');
    }

    public function expenseStatus(BurgundyExpense $burgundyExpense, Request $request)
    {
        $request->validate(['status' => ['required', Rule::in(BurgundyExpense::STATUSES)]]);
        $burgundyExpense->update(['status' => $request->status]);

        return back()->with('success', 'Expense marked ' . $request->status . '.');
    }

    public function destroyExpense(BurgundyExpense $burgundyExpense)
    {
        $burgundyExpense->delete();

        return back()->with('success', 'Expense deleted.');
    }

    // ════════════════════════════════════════════════════════════════
    // Helpers
    // ════════════════════════════════════════════════════════════════
    private function validateClient(Request $request, ?BurgundyClient $client = null): array
    {
        if ($request->filled('email')) {
            $request->merge(['email' => strtolower(trim($request->email))]);
        }

        return $request->validate([
            'first_name'      => ['required', 'string', 'max:100'],
            'last_name'       => ['nullable', 'string', 'max:100'],
            'email'           => ['nullable', 'email', 'max:150', Rule::unique('burgundy_clients', 'email')->ignore($client?->id)],
            'phone'           => ['nullable', 'string', 'max:30'],
            'status'          => ['required', Rule::in(array_keys(BurgundyClient::STATUSES))],
            'source'          => ['nullable', Rule::in(array_keys(BurgundyClient::SOURCES))],
            'subscription_id' => ['nullable', 'integer', 'exists:subscriptions,id'],
            'monthly_fee'     => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'current_round'   => ['nullable', 'integer', 'min:0', 'max:99'],
            'signed_up_at'    => ['nullable', 'date'],
            'next_action'     => ['nullable', 'string', 'max:255'],
            'notes'           => ['nullable', 'string', 'max:5000'],
        ], [
            'email.unique' => 'A Burgundy client with this email already exists.',
        ]);
    }

    /** @return array{0: string, 1: ?Carbon, 2: Carbon} */
    private function period(Request $request): array
    {
        $period = (string) $request->query('period', now()->format('Y-m'));

        if ($period === 'all') {
            return ['all', null, now()];
        }
        if (! preg_match('/^\d{4}-\d{2}$/', $period)) {
            $period = now()->format('Y-m');
        }

        $from = Carbon::createFromFormat('Y-m-d', $period . '-01')->startOfMonth();

        return [$period, $from, $from->copy()->endOfMonth()];
    }

    private function periodOptions(): array
    {
        $options = [];
        for ($i = 0; $i < 12; $i++) {
            $m = now()->startOfMonth()->subMonths($i);
            $options[$m->format('Y-m')] = $m->format('F Y');
        }
        $options['all'] = 'All time';

        return $options;
    }
}
