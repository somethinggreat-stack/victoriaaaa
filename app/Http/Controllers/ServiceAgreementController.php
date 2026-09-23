<?php

namespace App\Http\Controllers;

use App\Models\PaymentAgreement;
use App\Models\PaymentLink;
use App\Services\ServiceAgreement;
use App\Services\ServiceAgreements;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

/**
 * The service agreement a client signs immediately after paying.
 *
 * One page serves every payment path. Reached by signed, expiring URL, so it
 * needs no session — a client who closes the tab can be sent the same link
 * again and pick up where they left off.
 */
class ServiceAgreementController extends Controller
{
    public function show(Request $request, PaymentAgreement $agreement)
    {
        if ($agreement->status === 'signed') {
            return view('agreement.signed', [
                'agreement' => $agreement,
                'brand'     => $this->brand($agreement),
            ]);
        }

        return view('agreement.sign', [
            'agreement' => $agreement,
            'brand'     => $this->brand($agreement),
            'terms'     => ServiceAgreement::build(ServiceAgreements::saleFor($agreement)),
            'postUrl'   => URL::temporarySignedRoute(
                'agreement.sign',
                now()->addDays(2),
                ['agreement' => $agreement->id],
            ),
        ]);
    }

    public function sign(Request $request, PaymentAgreement $agreement)
    {
        if ($agreement->status === 'signed') {
            return response()->json([
                'success'  => true,
                'redirect' => $agreement->next_url ?: ServiceAgreements::signingUrl($agreement, 1),
            ]);
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

        $name = trim($validated['full_name']);

        $agreement->update([
            'status'         => 'signed',
            'full_name'      => $name,
            'signature_data' => $validated['signature_data'],
            // Frozen verbatim, so the document can be reproduced exactly even
            // after the wording changes.
            'contract_text'  => ServiceAgreement::build(ServiceAgreements::saleFor($agreement, $name)),
            'ip_address'     => $request->ip(),
            'user_agent'     => substr((string) $request->userAgent(), 0, 512),
            'signed_at'      => now(),
        ]);

        // Cross-reference a payment link so the admin can see it is signed.
        if ($agreement->source === 'payment_link' && $agreement->source_id) {
            try {
                PaymentLink::where('id', $agreement->source_id)
                    ->update(['payment_agreement_id' => $agreement->id]);
            } catch (\Throwable $e) {
                Log::warning('[Agreement] Could not link agreement to payment link', ['error' => $e->getMessage()]);
            }
        }

        Log::info('[Agreement] Signed', [
            'agreement_id' => $agreement->id,
            'source'       => $agreement->source,
            'invoice'      => $agreement->invoice_number,
        ]);

        return response()->json([
            'success'  => true,
            'redirect' => $agreement->next_url ?: ServiceAgreements::signingUrl($agreement, 1),
        ]);
    }

    private function brand(PaymentAgreement $agreement): string
    {
        return $agreement->partner === 'burgundy' ? 'Burgundy' : 'Victoria Love';
    }
}
