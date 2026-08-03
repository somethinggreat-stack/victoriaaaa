<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class PaymentLinksController extends Controller
{
    /** List existing links + the "create link" form. */
    public function index(Request $request)
    {
        // Feature needs its table. If it hasn't been created yet, render the
        // page with a setup notice instead of a hard 500.
        if (! Schema::hasTable('payment_links')) {
            return view('admin.payment-links', [
                'needsSetup' => true,
                'rows'       => null,
                'kpis'       => null,
            ]);
        }

        $q = PaymentLink::query();

        if ($search = $request->query('q')) {
            $q->where(function ($w) use ($search) {
                $w->where('client_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('payer_email', 'like', "%{$search}%")
                  ->orWhere('invoice_number', 'like', "%{$search}%")
                  ->orWhere('transaction_id', 'like', "%{$search}%");
            });
        }

        if ($status = $request->query('status')) {
            $q->where('status', $status);
        }

        $kpis = [
            'total'      => PaymentLink::count(),
            'unpaid'     => PaymentLink::where('status', 'unpaid')->count(),
            'paid'       => PaymentLink::where('status', 'paid')->count(),
            'collected'  => (float) PaymentLink::where('status', 'paid')->sum('amount'),
            'outstanding'=> (float) PaymentLink::where('status', 'unpaid')->sum('amount'),
        ];

        return view('admin.payment-links', [
            'needsSetup' => false,
            'rows'       => $q->latest()->paginate(30)->withQueryString(),
            'kpis'       => $kpis,
        ]);
    }

    /** Generate a new one-time payment link. */
    public function store(Request $request)
    {
        if (! Schema::hasTable('payment_links')) {
            return back()->with('error', 'The payment_links table does not exist yet. Please run the setup SQL first.');
        }

        $validated = $request->validate([
            'client_name' => ['required', 'string', 'max:150'],
            'amount'      => ['required', 'numeric', 'min:1', 'max:100000'],
            'email'       => ['nullable', 'email', 'max:150'],
            'note'        => ['nullable', 'string', 'max:255'],
        ], [
            'amount.min' => 'Amount must be at least $1.00.',
            'amount.max' => 'Amount is too large — please split it into smaller links.',
        ]);

        // Unique, hard-to-guess token.
        do {
            $token = 'pl_' . Str::random(28);
        } while (PaymentLink::where('token', $token)->exists());

        $link = PaymentLink::create([
            'token'       => $token,
            'client_name' => trim($validated['client_name']),
            'email'       => $validated['email'] ?? null,
            'amount'      => number_format((float) $validated['amount'], 2, '.', ''),
            'note'        => $validated['note'] ?? null,
            'status'      => 'unpaid',
        ]);

        return back()
            ->with('success', 'Payment link generated — copy it below and send it to your client.')
            ->with('generated_link', $link->url)
            ->with('generated_name', $link->client_name)
            ->with('generated_amount', '$' . number_format((float) $link->amount, 2));
    }

    /** Void an unpaid link so it can no longer be used. */
    public function void(PaymentLink $paymentLink)
    {
        if ($paymentLink->status === 'unpaid') {
            $paymentLink->update(['status' => 'void']);
            return back()->with('success', 'Link voided — it can no longer be paid.');
        }

        return back()->with('error', 'Only unpaid links can be voided.');
    }
}
