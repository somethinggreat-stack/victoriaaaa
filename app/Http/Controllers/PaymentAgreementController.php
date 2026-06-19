<?php

namespace App\Http\Controllers;

use App\Models\PaymentAgreement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentAgreementController extends Controller
{
    /**
     * Plans that require a signed payment agreement BEFORE checkout.
     * (The pay-in-full plan has no instalment schedule, so no contract.)
     */
    public const AGREEMENT_PLANS = [
        'mentorship-3pay',
        'mentorship-5pay',
    ];

    public const TERMS_VERSION = 'v1';

    /**
     * Resolve the frozen financial terms for a plan straight from the
     * single source of truth (the checkout plan catalogue).
     */
    public static function terms(string $planKey): ?array
    {
        if (! in_array($planKey, self::AGREEMENT_PLANS, true)) {
            return null;
        }

        $plan = AcceptJsPaymentController::PLANS[$planKey] ?? null;
        if (! $plan) {
            return null;
        }

        $deposit     = (float) $plan['amount'];
        $installment = (float) ($plan['recurring'] ?? 0);
        $count       = (int) ($plan['recurring_occurrences'] ?? 0);

        return [
            'plan_key'           => $planKey,
            'plan_label'         => $plan['label'],
            'deposit_amount'     => $deposit,
            'installment_amount' => $installment,
            'installment_count'  => $count,
            'total_amount'       => $deposit + ($installment * $count),
        ];
    }

    public function show(string $plan)
    {
        $terms = self::terms($plan);

        if (! $terms) {
            // Unknown / non-instalment plan — send straight to checkout.
            return redirect()->route('checkout.show', $plan);
        }

        return view('payments.mentorship-agreement', [
            'planKey' => $plan,
            'terms'   => $terms,
        ]);
    }

    public function sign(Request $request)
    {
        $validated = $request->validate([
            'selected_plan'  => 'required|string|in:' . implode(',', self::AGREEMENT_PLANS),
            'full_name'      => 'required|string|min:3|max:150',
            'signature_data' => 'required|string|max:2000000',
            'agree_terms'    => 'required|accepted',
        ]);

        $terms = self::terms($validated['selected_plan']);
        if (! $terms) {
            return response()->json([
                'success' => false,
                'message' => 'That plan is not available for signing.',
            ], 422);
        }

        // Basic sanity check that the signature is an image data URL.
        if (! str_starts_with($validated['signature_data'], 'data:image')) {
            return response()->json([
                'success' => false,
                'message' => 'Please draw your signature before submitting.',
            ], 422);
        }

        $contractText = $this->buildContractText($terms, $validated['full_name']);

        $agreement = PaymentAgreement::create([
            'plan_key'           => $terms['plan_key'],
            'plan_label'         => $terms['plan_label'],
            'deposit_amount'     => number_format($terms['deposit_amount'], 2, '.', ''),
            'installment_amount' => number_format($terms['installment_amount'], 2, '.', ''),
            'installment_count'  => $terms['installment_count'],
            'total_amount'       => number_format($terms['total_amount'], 2, '.', ''),
            'full_name'          => trim($validated['full_name']),
            'signature_data'     => $validated['signature_data'],
            'contract_text'      => $contractText,
            'terms_version'      => self::TERMS_VERSION,
            'ip_address'         => $request->ip(),
            'user_agent'         => substr((string) $request->userAgent(), 0, 512),
            'signed_at'          => now(),
        ]);

        // Remember the signed agreement so checkout can require + link it.
        session([
            'signed_agreement_id'   => $agreement->id,
            'signed_agreement_plan' => $terms['plan_key'],
        ]);

        Log::info('Payment agreement signed', [
            'agreement_id' => $agreement->id,
            'plan'         => $terms['plan_key'],
            'name'         => $agreement->full_name,
            'ip'           => $request->ip(),
        ]);

        return response()->json([
            'success'  => true,
            'redirect' => route('checkout.show', $terms['plan_key']),
        ]);
    }

    /**
     * Plain-text snapshot of the exact terms the client agreed to — stored
     * verbatim so the signed contract can be reproduced later, regardless of
     * any future copy changes.
     */
    private function buildContractText(array $terms, string $name): string
    {
        $deposit = '$' . number_format($terms['deposit_amount'], 2);
        $inst    = '$' . number_format($terms['installment_amount'], 2);
        $count   = $terms['installment_count'];
        $total   = '$' . number_format($terms['total_amount'], 2);
        $date    = now()->format('F j, Y');

        return implode("\n", [
            'VICTORIOUS OPPORTUNITIES — 1:1 MENTORSHIP PROGRAM PAYMENT AGREEMENT',
            "Plan: {$terms['plan_label']}",
            "Date: {$date}",
            '',
            "This Payment Agreement is entered into between Victorious Opportunities (\"Company\") and {$name} (\"Client\").",
            '',
            "1. TOTAL INVESTMENT. Client enrolls in the 1:1 Mentorship Program for a total of {$total}.",
            '',
            '2. PAYMENT SCHEDULE. Client agrees to the following schedule:',
            "   • A deposit of {$deposit} charged today, {$date}.",
            "   • Followed by {$count} monthly payments of {$inst} each, beginning approximately 30 days after the deposit and recurring monthly until the total is paid in full.",
            '',
            '3. AUTHORIZATION. Client authorizes the Company to automatically charge the payment method provided at checkout for each scheduled payment on its due date, processed securely through Authorize.Net.',
            '',
            '4. COMMITMENT. The deposit is non-refundable. Client remains responsible for the full total above regardless of program usage. Missed or failed payments may pause program access until the balance is current.',
            '',
            '5. E-SIGNATURE. By typing my full legal name and drawing my signature below, I acknowledge that I have read, understand, and agree to be legally bound by this Agreement, and that my electronic signature is the legal equivalent of my handwritten signature.',
            '',
            "Signed by: {$name}",
            "Signed at: {$date}",
        ]);
    }
}
