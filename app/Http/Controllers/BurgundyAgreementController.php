<?php

namespace App\Http\Controllers;

use App\Models\BurgundyClient;
use App\Models\PaymentAgreement;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * The service agreement, signed immediately after the $100 enrollment charge
 * and before onboarding.
 *
 * Reuses the payment_agreements table the mentorship contracts already use:
 * typed legal name + drawn signature + a verbatim snapshot of the terms shown,
 * so the signed document can be reproduced later even if the copy changes.
 *
 * NOTE FOR REVIEW: the contract wording below is a working draft covering the
 * billing terms, the authorization to charge, and cancellation rights. Credit
 * repair contracts are regulated (CROA), so have counsel review this text
 * before it is used with real clients.
 */
class BurgundyAgreementController extends Controller
{
    public const TERMS_VERSION = 'burgundy-v1';

    public function show(Request $request)
    {
        // No session: we cannot tell who they are. Never send a client who has
        // already paid back to the checkout — point them at onboarding, which
        // identifies them by email and phone.
        if (! $request->session()->get('burgundy_paid')) {
            return view('burgundy.agreement-lost');
        }

        $customer = (array) session('burgundy_customer', []);

        return view('burgundy.agreement', [
            'enrollment' => (string) config('partnership.plan.enrollment', '100.00'),
            'monthly'    => (string) config('partnership.plan.monthly', '100.00'),
            'planLabel'  => (string) config('partnership.plan.label', 'Credit Restoration Program'),
            'fullName'   => trim(($customer['first_name'] ?? '') . ' ' . ($customer['last_name'] ?? '')),
            'terms'      => $this->contractText(
                trim(($customer['first_name'] ?? '') . ' ' . ($customer['last_name'] ?? '')) ?: '__________',
            ),
        ]);
    }

    public function sign(Request $request)
    {
        if (! $request->session()->get('burgundy_paid')) {
            return response()->json([
                'success' => false,
                'message' => 'Your session expired. Please continue to the onboarding form.',
                'redirect' => route('burgundy.onboarding.show'),
            ], 422);
        }

        $validated = $request->validate([
            'full_name'      => 'required|string|min:3|max:150',
            'signature_data' => 'required|string|max:2000000',
            'agree_terms'    => 'required|accepted',
        ]);

        if (! str_starts_with($validated['signature_data'], 'data:image')) {
            return response()->json([
                'success' => false,
                'message' => 'Please draw your signature before submitting.',
            ], 422);
        }

        $customer  = (array) session('burgundy_customer', []);
        $invoice   = (string) session('burgundy_invoice', '');
        $clientId  = session('burgundy_client_id');
        $enrollment = (float) config('partnership.plan.enrollment', 100);
        $monthly    = (float) config('partnership.plan.monthly', 100);

        try {
            $agreement = PaymentAgreement::create([
                'plan_key'           => (string) config('partnership.plan.key', 'burgundy-100'),
                'plan_label'         => (string) config('partnership.plan.label', 'Credit Restoration Program'),
                'deposit_amount'     => number_format($enrollment, 2, '.', ''),
                'installment_amount' => number_format($monthly, 2, '.', ''),
                'installment_count'  => null,   // open-ended — runs until cancelled
                'total_amount'       => number_format($enrollment, 2, '.', ''), // only the enrollment is committed
                'full_name'          => trim($validated['full_name']),
                'signature_data'     => $validated['signature_data'],
                'contract_text'      => $this->contractText(trim($validated['full_name'])),
                'terms_version'      => self::TERMS_VERSION,
                'email'              => $customer['email'] ?? null,
                'invoice_number'     => $invoice ?: null,
                'ip_address'         => $request->ip(),
                'user_agent'         => substr((string) $request->userAgent(), 0, 512),
                'signed_at'          => now(),
            ]);

            // Cross-reference the subscription and the client record.
            if ($invoice) {
                $subscription = Subscription::where('invoice_number', $invoice)->first();

                if ($subscription) {
                    $agreement->update(['subscription_id' => $subscription->id]);
                }
            }

            if ($clientId && $client = BurgundyClient::find($clientId)) {
                $client->update(['payment_agreement_id' => $agreement->id]);
                $client->logEvent('agreement_signed', 'Service agreement signed by ' . $agreement->full_name . '.');
            }

            session(['burgundy_agreement_signed' => true]);

            Log::info('[Burgundy] Agreement signed', [
                'agreement_id' => $agreement->id,
                'invoice'      => $invoice,
                'client_id'    => $clientId,
            ]);

            return response()->json([
                'success'  => true,
                'redirect' => route('burgundy.onboarding.show'),
            ]);

        } catch (\Throwable $e) {
            Log::error('[Burgundy] Failed to save agreement', [
                'invoice' => $invoice,
                'error'   => $e->getMessage(),
            ]);

            // They have already paid. A bookkeeping failure on our side must not
            // strand them — let them through and flag it for us to chase.
            return response()->json([
                'success'  => true,
                'redirect' => route('burgundy.onboarding.show'),
            ]);
        }
    }

    /**
     * Verbatim snapshot of what the client agreed to. Stored with the signature
     * so the document can be reproduced exactly, whatever the page says later.
     */
    private function contractText(string $name): string
    {
        $enrollment = '$' . number_format((float) config('partnership.plan.enrollment', 100), 2);
        $monthly    = '$' . number_format((float) config('partnership.plan.monthly', 100), 2);
        $date       = now()->format('F j, Y');

        return implode("\n", [
            'CREDIT RESTORATION SERVICE AGREEMENT',
            "Date: {$date}",
            '',
            "This Agreement is entered into between Victoria Love Credit (\"Company\") and {$name} (\"Client\").",
            '',
            '1. SERVICES. Company will review Client\'s credit reports from the three major credit bureaus and, on Client\'s behalf, dispute items Client believes to be inaccurate, incomplete or unverifiable, and provide ongoing credit guidance for as long as this Agreement remains active.',
            '',
            '2. FEES AND BILLING SCHEDULE.',
            "   • An enrollment fee of {$enrollment}, charged today, {$date}.",
            "   • Thereafter {$monthly} per month, beginning approximately 30 days from today and recurring monthly until cancelled.",
            '   • There is no minimum term and no total contract amount. Client pays only for the months in which the Agreement is active.',
            '',
            '3. AUTHORIZATION TO CHARGE. Client authorizes Company to charge the payment method provided at checkout for the enrollment fee and for each monthly fee on its due date, processed securely through Authorize.Net. This authorization remains in effect until Client cancels as described below.',
            '',
            '4. CANCELLATION.',
            '   • Client may cancel at any time, for any reason, by written notice to Company.',
            '   • Cancellation takes effect at the end of the current billing month; no further monthly charges will be made after Company receives notice.',
            '   • Fees already charged for months in which services were provided are non-refundable, except as stated in Section 5.',
            '',
            '5. YOUR RIGHT TO CANCEL WITHIN THREE DAYS. Client may cancel this Agreement, without any penalty or obligation, at any time before midnight of the third business day after the date this Agreement is signed. If Client cancels within that period, any amount paid will be refunded in full within 10 business days. To cancel, Client must send written notice to Company stating the intention to cancel.',
            '',
            '6. NO GUARANTEE. Company does not guarantee any specific credit score increase, the removal of any particular item, approval for any loan or credit product, or any particular outcome or timeframe. Results depend on the accuracy of the information reported and the responses of the credit bureaus and furnishers.',
            '',
            '7. CLIENT RESPONSIBILITIES. Client agrees to provide accurate and complete information, to forward correspondence received from the credit bureaus promptly, and to keep the payment method on file current.',
            '',
            '8. YOUR RIGHTS. Client has the right to dispute inaccurate information in their credit report by contacting the credit bureaus directly, at no cost. Client has the right to obtain a copy of their credit report from each bureau, and accurate negative information may generally be reported for up to seven years, or ten years for bankruptcies.',
            '',
            '9. ELECTRONIC SIGNATURE. By typing my full legal name and drawing my signature below, I acknowledge that I have read, understand, and agree to be legally bound by this Agreement, and that my electronic signature is the legal equivalent of my handwritten signature.',
            '',
            "Signed by: {$name}",
            "Signed at: {$date}",
        ]);
    }
}
