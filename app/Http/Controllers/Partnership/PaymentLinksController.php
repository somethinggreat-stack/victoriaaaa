<?php

namespace App\Http\Controllers\Partnership;

use App\Http\Controllers\Controller;
use App\Models\BurgundyClient;
use App\Models\PaymentLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Burgundy's one-time payment links.
 *
 * Same pay page and same charge as Victoria's, but every query is scoped to
 * partner = 'burgundy' so neither side ever sees or voids the other's links.
 */
class PaymentLinksController extends Controller
{
    private function ready(): bool
    {
        return Schema::hasTable('payment_links') && Schema::hasColumn('payment_links', 'partner');
    }

    private function mine()
    {
        return PaymentLink::where('partner', 'burgundy');
    }

    public function index(Request $request)
    {
        if (! $this->ready()) {
            return view('partnership.payment-links', ['needsSetup' => true, 'rows' => null, 'kpis' => null, 'prefill' => null]);
        }

        $q = $this->mine();

        if ($search = trim((string) $request->query('q', ''))) {
            $q->where(function ($w) use ($search) {
                $w->where('client_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('payer_email', 'like', "%{$search}%")
                  ->orWhere('transaction_id', 'like', "%{$search}%");
            });
        }

        if ($status = $request->query('status')) {
            $q->where('status', $status);
        }

        $kpis = [
            'total'       => $this->mine()->count(),
            'unpaid'      => $this->mine()->where('status', 'unpaid')->count(),
            'paid'        => $this->mine()->where('status', 'paid')->count(),
            'collected'   => (float) $this->mine()->where('status', 'paid')->sum('amount'),
            'outstanding' => (float) $this->mine()->where('status', 'unpaid')->sum('amount'),
        ];

        // Arriving from a client's page: open the form with them filled in.
        $prefill = null;
        if ($clientId = $request->query('client')) {
            $prefill = BurgundyClient::find($clientId);
        }

        return view('partnership.payment-links', [
            'needsSetup' => false,
            'rows'       => $q->latest()->paginate(30)->withQueryString(),
            'kpis'       => $kpis,
            'prefill'    => $prefill,
        ]);
    }

    public function store(Request $request)
    {
        if (! $this->ready()) {
            return back()->with('error', 'Payment links are not set up yet. Please contact support.');
        }

        $validated = $request->validate([
            'client_name'         => ['required', 'string', 'max:150'],
            'amount'              => ['required', 'numeric', 'min:1', 'max:100000'],
            'email'               => ['nullable', 'email', 'max:150'],
            'note'                => ['nullable', 'string', 'max:255'],
            'service_description' => ['required', 'string', 'min:5', 'max:500'],
            'burgundy_client_id'  => ['nullable', 'integer', 'exists:burgundy_clients,id'],
        ], [
            'service_description.required' => 'Describe the service — this is what the client signs for.',
            'service_description.min'      => 'Please describe the service in a little more detail.',
            'amount.min'                   => 'Amount must be at least $1.00.',
            'amount.max'                   => 'Amount is too large — please split it into smaller links.',
        ]);

        do {
            $token = 'pl_' . Str::random(28);
        } while (PaymentLink::where('token', $token)->exists());

        $link = PaymentLink::create([
            'token'               => $token,
            'partner'             => 'burgundy',
            'burgundy_client_id'  => $validated['burgundy_client_id'] ?? null,
            'client_name'         => trim($validated['client_name']),
            'email'               => $validated['email'] ?? null,
            'amount'              => number_format((float) $validated['amount'], 2, '.', ''),
            'note'                => $validated['note'] ?? null,
            'service_description' => trim($validated['service_description']),
            'status'              => 'unpaid',
        ]);

        // Making a link for one of her clients is the same thing as marking them
        // "Payment Link Sent" — do it for her so the pipeline stays accurate.
        if ($client = BurgundyClient::find($validated['burgundy_client_id'] ?? null)) {
            $client->update([
                'contact_status' => 'payment_link_sent',
                'link_sent_at'   => $client->link_sent_at ?: now(),
            ]);
            $client->logEvent('payment_link', 'One-time payment link created for $' . number_format((float) $link->amount, 2) . '.');
        }

        return redirect()
            ->route('partnership.payment-links')
            ->with('success', 'Payment link generated — copy it below and send it to your client.')
            ->with('generated_link', $link->url)
            ->with('generated_name', $link->client_name)
            ->with('generated_amount', '$' . number_format((float) $link->amount, 2));
    }

    public function void(PaymentLink $paymentLink)
    {
        // Route-model binding finds any link; refuse one that isn't hers.
        abort_unless($paymentLink->partner === 'burgundy', 404);

        if ($paymentLink->status === 'unpaid') {
            $paymentLink->update(['status' => 'void']);
            return back()->with('success', 'Link voided — it can no longer be paid.');
        }

        return back()->with('error', 'Only unpaid links can be voided.');
    }
}
