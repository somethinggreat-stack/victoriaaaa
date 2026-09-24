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
        if ($agreement->fullySigned()) {
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
        if ($agreement->fullySigned()) {
            return response()->json([
                'success'  => true,
                'redirect' => $agreement->next_url ?: ServiceAgreements::signingUrl($agreement, 1),
            ]);
        }

        // A joint agreement can be signed by either party first, together or
        // separately, so each block is optional on its own — but at least one
        // outstanding signature must arrive.
        $rules = [
            'full_name'               => 'nullable|string|min:3|max:150',
            'signature_data'          => 'nullable|string|max:2000000',
            'cosigner_full_name'      => 'nullable|string|min:3|max:150',
            'cosigner_signature_data' => 'nullable|string|max:2000000',
            'agree_terms'             => 'required|accepted',
        ];

        $validated = $request->validate($rules);

        $wantsPrimary  = ! $agreement->primarySigned()
            && $this->isDrawn($validated['signature_data'] ?? null)
            && trim((string) ($validated['full_name'] ?? '')) !== '';

        $wantsCosigner = $agreement->requires_cosigner
            && ! $agreement->cosignerSigned()
            && $this->isDrawn($validated['cosigner_signature_data'] ?? null)
            && trim((string) ($validated['cosigner_full_name'] ?? '')) !== '';

        if (! $wantsPrimary && ! $wantsCosigner) {
            return response()->json([
                'success' => false,
                'message' => $agreement->requires_cosigner
                    ? 'Please type a name and draw a signature for whoever is signing.'
                    : 'Please draw your signature before submitting.',
            ], 422);
        }

        $updates = [];

        if ($wantsPrimary) {
            $updates += [
                'full_name'      => trim($validated['full_name']),
                'signature_data' => $validated['signature_data'],
                'ip_address'     => $request->ip(),
                'user_agent'     => substr((string) $request->userAgent(), 0, 512),
                'signed_at'      => now(),
            ];
        }

        if ($wantsCosigner) {
            $updates += [
                'cosigner_full_name'      => trim($validated['cosigner_full_name']),
                'cosigner_signature_data' => $validated['cosigner_signature_data'],
                'cosigner_ip_address'     => $request->ip(),
                'cosigner_user_agent'     => substr((string) $request->userAgent(), 0, 512),
                'cosigner_signed_at'      => now(),
            ];
        }

        $agreement->fill($updates)->save();
        $agreement->refresh();

        $complete = $agreement->fullySigned();

        $agreement->update([
            'status' => $complete ? 'signed' : 'partial',
            // Frozen only once everybody has signed, so the stored document
            // always carries every name that is bound by it.
            'contract_text' => $complete
                ? ServiceAgreement::build(ServiceAgreements::saleFor($agreement, $agreement->full_name))
                : $agreement->contract_text,
        ]);

        if ($complete && $agreement->source === 'payment_link' && $agreement->source_id) {
            try {
                PaymentLink::where('id', $agreement->source_id)
                    ->update(['payment_agreement_id' => $agreement->id]);
            } catch (\Throwable $e) {
                Log::warning('[Agreement] Could not link agreement to payment link', ['error' => $e->getMessage()]);
            }
        }

        Log::info('[Agreement] Signature recorded', [
            'agreement_id' => $agreement->id,
            'complete'     => $complete,
            'awaiting'     => $agreement->awaitingSignatureFrom(),
        ]);

        return response()->json([
            'success'  => true,
            'complete' => $complete,
            // Not finished means back to the same page, which now shows who is
            // still outstanding and offers the link to pass on.
            'redirect' => $complete
                ? ($agreement->next_url ?: ServiceAgreements::signingUrl($agreement, 1))
                : ServiceAgreements::signingUrl($agreement, 30),
        ]);
    }

    /** A signature pad submits a data URL; anything else is not a signature. */
    private function isDrawn(?string $data): bool
    {
        return is_string($data) && str_starts_with($data, 'data:image');
    }

    private function brand(PaymentAgreement $agreement): string
    {
        return $agreement->partner === 'burgundy' ? 'Burgundy' : 'Victoria Love';
    }
}
