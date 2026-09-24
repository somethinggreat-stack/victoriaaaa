<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentAgreement;
use App\Services\ServiceAgreement;
use App\Services\ServiceAgreements;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Signed service agreements, and — the part that matters operationally — the
 * ones that are still unsigned.
 *
 * A client whose card was charged but who closed the tab before signing leaves
 * you with money taken and nothing signed, which is worse than no contract at
 * all because everyone assumes one exists. Those sit at the top of this page
 * with a link that can be sent again.
 */
class ContractsController extends Controller
{
    /**
     * The deploy runs `migrate --force || true`, so a migration that fails is
     * swallowed and the deploy still reports success. Check the schema before
     * querying it, otherwise every visit dies with a raw SQL exception.
     */
    private function ready(): bool
    {
        try {
            return Schema::hasTable('payment_agreements')
                && Schema::hasColumn('payment_agreements', 'status')
                && Schema::hasColumn('payment_agreements', 'source');
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function index(Request $request)
    {
        if (! $this->ready()) {
            return response()->view('admin.contracts-setup', [], 503);
        }

        $q = PaymentAgreement::query();

        if ($search = trim((string) $request->query('q', ''))) {
            $like = '%' . $search . '%';
            $q->where(function ($w) use ($like) {
                $w->where('full_name', 'like', $like)
                  ->orWhere('client_name', 'like', $like)
                  ->orWhere('email', 'like', $like)
                  ->orWhere('invoice_number', 'like', $like)
                  ->orWhere('plan_label', 'like', $like);
            });
        }

        if ($status = $request->query('status')) {
            $q->where('status', $status);
        }

        if ($partner = $request->query('partner')) {
            $q->where('partner', $partner);
        }

        $pendingCount = PaymentAgreement::where('status', 'pending')->count();

        return view('admin.contracts', [
            'rows'         => $q->latest()->paginate(25)->withQueryString(),
            'pendingCount' => $pendingCount,
            'signedCount'  => PaymentAgreement::where('status', 'signed')->count(),
        ]);
    }

    /**
     * Raise an agreement by hand, for a client who paid somewhere this site did
     * not process — another funnel, an invoice, a transfer — or who paid before
     * contracts existed. Produces the same signing link as a website sale.
     */
    public function store(Request $request)
    {
        if (! $this->ready()) {
            return response()->view('admin.contracts-setup', [], 503);
        }

        $validated = $request->validate([
            'client_name'         => ['required', 'string', 'max:150'],
            'email'               => ['nullable', 'email', 'max:150'],
            'client_phone'        => ['nullable', 'string', 'max:30'],
            'service_description' => ['required', 'string', 'min:5', 'max:500'],
            'charged_today'       => ['required', 'numeric', 'min:0', 'max:100000'],
            'recurring_amount'    => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'recurring_count'     => ['nullable', 'integer', 'min:1', 'max:120'],
            'partner'             => ['required', 'in:victoria,burgundy'],
        ], [
            'service_description.required' => 'Describe the service — this is what the client signs for.',
            'charged_today.required'       => 'Enter the amount they were charged.',
        ]);

        $recurring = $validated['recurring_amount'] ?? null;

        $agreement = ServiceAgreements::start([
            'source'              => 'manual',
            'source_id'           => null,
            'partner'             => $validated['partner'],
            'plan_key'            => 'manual',
            'plan_label'          => $validated['service_description'],
            'service_description' => $validated['service_description'],
            'charged_today'       => (float) $validated['charged_today'],
            // Blank monthly means a one-time payment, not a zero-value plan.
            'recurring_amount'    => ($recurring !== null && $recurring !== '' && (float) $recurring > 0)
                ? (float) $recurring
                : null,
            'recurring_count'     => $validated['recurring_count'] ?? null,
            'client_name'         => trim($validated['client_name']),
            'client_phone'        => $validated['client_phone'] ?? null,
            'email'               => $validated['email'] ?? null,
        ]);

        if (! $agreement) {
            return back()->with('error', 'Could not create the agreement. Please try again.');
        }

        return redirect()
            ->route('admin.contracts.show', $agreement)
            ->with('success', 'Agreement created — copy the link below and send it to your client.');
    }

    public function show(PaymentAgreement $contract)
    {
        return view('admin.contract-show', [
            'contract'   => $contract,
            'signingUrl' => $contract->isSigned() ? null : ServiceAgreements::signingUrl($contract),
            // Older mentorship agreements were stored before the text snapshot
            // existed, so fall back to rendering from the recorded figures.
            'text'       => $contract->contract_text
                ?: ServiceAgreement::build(ServiceAgreements::saleFor($contract)),
        ]);
    }

    /** The signed document, as a file Victoria can send or keep. */
    public function pdf(PaymentAgreement $contract)
    {
        $text = $contract->contract_text
            ?: ServiceAgreement::build(ServiceAgreements::saleFor($contract));

        $pdf = Pdf::loadView('admin.contract-pdf', [
            'contract' => $contract,
            'text'     => $text,
        ])->setPaper('letter');

        $name = Str::slug($contract->signerName() ?: 'client');
        $date = optional($contract->signed_at ?: $contract->created_at)->format('Y-m-d');

        return $pdf->download("service-agreement-{$name}-{$date}.pdf");
    }
}
