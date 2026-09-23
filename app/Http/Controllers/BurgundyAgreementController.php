<?php

namespace App\Http\Controllers;

use App\Models\BurgundyClient;
use App\Models\PaymentAgreement;
use App\Models\Subscription;
use App\Services\ServiceAgreement;
use App\Support\PartnershipPlans;
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
    public const TERMS_VERSION = ServiceAgreement::TERMS_VERSION;

    public function show(Request $request)
    {
        // No session: we cannot tell who they are. Never send a client who has
        // already paid back to the checkout — point them at onboarding, which
        // identifies them by email and phone.
        if (! $request->session()->get('burgundy_paid')) {
            return view('burgundy.agreement-lost');
        }

        $customer = (array) session('burgundy_customer', []);
        $name     = trim(($customer['first_name'] ?? '') . ' ' . ($customer['last_name'] ?? ''));
        $sale     = $this->saleTerms($name);

        return view('burgundy.agreement', [
            'enrollment' => number_format($sale['charged_today'], 2, '.', ''),
            'monthly'    => number_format((float) ($sale['recurring_amount'] ?? 0), 2, '.', ''),
            'planLabel'  => $sale['plan_label'],
            'fullName'   => $name,
            'terms'      => ServiceAgreement::build($sale),
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

        // Priced from the subscription, so the signed document can never quote
        // a figure other than the one the card was charged.
        $sale = $this->saleTerms(trim($validated['full_name']));

        try {
            $agreement = PaymentAgreement::create([
                'plan_key'           => $sale['plan_key'],
                'plan_label'         => $sale['plan_label'],
                'deposit_amount'     => number_format($sale['charged_today'], 2, '.', ''),
                'installment_amount' => number_format((float) ($sale['recurring_amount'] ?? 0), 2, '.', ''),
                'installment_count'  => null,   // open-ended — runs until cancelled
                'total_amount'       => number_format($sale['charged_today'], 2, '.', ''), // only the enrollment is committed
                'full_name'          => trim($validated['full_name']),
                'signature_data'     => $validated['signature_data'],
                'contract_text'      => ServiceAgreement::build($sale),
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
     * The terms of THIS sale, read from the subscription created at checkout.
     *
     * Config is only a fallback for the rare case where the subscription row
     * could not be written — it is never the primary source, because a price
     * change in config must not alter what an existing client agreed to.
     */
    private function saleTerms(string $name): array
    {
        $invoice      = (string) session('burgundy_invoice', '');
        $subscription = $invoice ? Subscription::where('invoice_number', $invoice)->first() : null;

        if ($subscription) {
            return [
                'client_name'      => $name,
                'plan_key'         => (string) $subscription->plan_key,
                'plan_label'       => (string) ($subscription->plan_label ?: 'Credit Restoration Program'),
                'charged_today'    => (float) $subscription->amount,
                'recurring_amount' => $subscription->recurring_amount !== null
                    ? (float) $subscription->recurring_amount
                    : null,
                'recurring_count'  => null,
            ];
        }

        Log::warning('[Burgundy] Agreement priced from config — no subscription for invoice', [
            'invoice' => $invoice ?: '(none)',
        ]);

        $plan = PartnershipPlans::tier(session('burgundy_tier'));

        return [
            'client_name'      => $name,
            'plan_key'         => (string) $plan['key'],
            'plan_label'       => (string) $plan['label'],
            'charged_today'    => (float) $plan['enrollment'],
            'recurring_amount' => (float) $plan['monthly'],
            'recurring_count'  => null,
        ];
    }
}
