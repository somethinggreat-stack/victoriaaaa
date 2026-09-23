<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentAgreement;
use App\Services\ServiceAgreement;
use App\Services\ServiceAgreements;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
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
    public function index(Request $request)
    {
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
