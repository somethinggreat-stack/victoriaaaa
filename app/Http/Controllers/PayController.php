<?php

namespace App\Http\Controllers;

use App\Models\BurgundyClient;
use App\Models\PaymentLink;
use App\Services\ServiceAgreements;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Public pay page for admin-generated one-time payment links.
 *
 * Victoria creates a link in the dashboard (client name + amount); the client
 * opens /pay/{token}, enters their card, and is charged once via Authorize.Net.
 * The link is then marked paid so it can't be reused.
 */
class PayController extends Controller
{
    public function show(string $token)
    {
        $link = PaymentLink::where('token', $token)->first();

        if (! $link || $link->status === 'void') {
            abort(404);
        }

        if ($link->status === 'paid') {
            return view('payments.link-paid', ['link' => $link]);
        }

        $amount = (float) $link->amount;

        return view('payments.link-checkout', [
            'token'        => $link->token,
            'processRoute' => route('pay.process'),
            'clientKey'    => config('services.authorize_net.client_key'),
            'apiLogin'     => config('services.authorize_net.api_login_id'),
            'environment'  => config('services.authorize_net.environment'),
            'link'         => [
                'label'    => 'Payment for ' . $link->client_name,
                'tagline'  => $link->note ?: 'Secure one-time payment.',
                'amount'   => $link->amount,
                'total'    => $link->amount,
                'schedule' => [
                    ['label' => 'Today', 'amount' => '$' . number_format($amount, 2), 'note' => 'One-time charge'],
                ],
            ],
        ]);
    }

    /** Email the client the link to sign their contract (then onboarding). */
    private function sendClientTheirContract(PaymentLink $link, $agreement): void
    {
        try {
            $first = explode(' ', trim($link->client_name))[0];
            \Illuminate\Support\Facades\Mail::raw(
                "Hi {$first},\n\n"
                . "Your credit repair service has been paid for. The next step takes about a minute: sign your service agreement, then fill out your onboarding form so we can start on your file.\n\n"
                . "Sign your agreement: " . ServiceAgreements::signingUrl($agreement, 30) . "\n\n"
                . "Questions? Call or text (469) 349-5553 (Mon–Fri, 10 AM – 5 PM Central).\n\n"
                . "— Victoria Love\nVictorious Opportunities",
                fn ($m) => $m->to($link->email)->subject('Your credit repair is paid for — sign your agreement'),
            );
        } catch (\Throwable $e) {
            Log::warning('[PayLink] Could not email the client their contract', ['error' => $e->getMessage()]);
        }
    }

    public function process(Request $request)
    {
        $validated = $request->validate([
            'token'         => 'required|string',
            'dataDescriptor' => 'required|string|max:255',
            'dataValue'      => 'required|string|max:8000',
            'first_name'    => 'required|string|max:100',
            'last_name'     => 'required|string|max:100',
            'email'         => 'required|email|max:150',
            'phone'         => 'required|string|max:30',
            'address'       => 'required|string|max:255',
            'city'          => 'required|string|max:100',
            'state'         => 'required|string|max:10',
            'zip'           => 'required|string|max:20',
            'cardName'      => 'required|string|max:150',
            'agree_terms'   => 'required|accepted',
            'agree_privacy' => 'required|accepted',
        ]);

        $link = PaymentLink::where('token', $validated['token'])->first();

        if (! $link || $link->status === 'void') {
            return response()->json(['success' => false, 'message' => 'This payment link is not valid.'], 404);
        }

        // Guard against a double charge (client clicks twice, or reuses the link).
        if ($link->status !== 'unpaid') {
            return response()->json([
                'success' => false,
                'message' => 'This payment has already been completed. No further charge was made.',
            ], 409);
        }

        $amount        = number_format((float) $link->amount, 2, '.', '');
        $label         = 'Payment — ' . $link->client_name;
        $invoiceNumber = 'PL-' . time() . '-' . strtoupper(Str::random(4));

        $environment = config('services.authorize_net.environment');
        $apiLoginId  = config('services.authorize_net.api_login_id');
        $txKey       = config('services.authorize_net.transaction_key');

        $endpoint = $environment === 'sandbox'
            ? 'https://apitest.authorize.net/xml/v1/request.api'
            : 'https://api.authorize.net/xml/v1/request.api';

        if (empty($apiLoginId) || empty($txKey)) {
            Log::error('[PayLink] Authorize.Net credentials missing', ['invoice' => $invoiceNumber]);
            return response()->json([
                'success' => false,
                'message' => 'Payment system is not configured yet. Please contact support.',
            ], 503);
        }


        $payload = [
            'createTransactionRequest' => [
                'merchantAuthentication' => [
                    'name'           => $apiLoginId,
                    'transactionKey' => $txKey,
                ],
                'refId' => (string) Str::uuid(),
                'transactionRequest' => [
                    'transactionType' => 'authCaptureTransaction',
                    'amount'          => $amount,
                    'payment'         => [
                        'opaqueData' => [
                            'dataDescriptor' => $validated['dataDescriptor'],
                            'dataValue'      => $validated['dataValue'],
                        ],
                    ],
                    'order' => [
                        'invoiceNumber' => $invoiceNumber,
                        'description'   => mb_substr($label, 0, 255),
                    ],
                    'customer' => ['email' => $validated['email']],
                    'billTo'   => [
                        'firstName' => $validated['first_name'],
                        'lastName'  => $validated['last_name'],
                        'address'   => $validated['address'],
                        'city'      => $validated['city'],
                        'state'     => $validated['state'],
                        'zip'       => $validated['zip'],
                        'country'   => 'USA',
                    ],
                    'customerIP' => $request->ip(),
                ],
            ],
        ];

        try {
            $httpResponse = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ])->post($endpoint, $payload);

            $rawBody      = preg_replace('/^\xEF\xBB\xBF/', '', $httpResponse->body());
            $responseData = json_decode(trim($rawBody), true);

            Log::info('[PayLink] charge response', [
                'invoice' => $invoiceNumber,
                'token'   => $link->token,
                'status'  => $httpResponse->status(),
                'decoded' => $responseData,
            ]);

            $resultCode              = data_get($responseData, 'messages.resultCode');
            $transactionResponseCode = data_get($responseData, 'transactionResponse.responseCode');
            $transId                 = data_get($responseData, 'transactionResponse.transId');
            $authCode                = data_get($responseData, 'transactionResponse.authCode');

            $messageText = data_get($responseData, 'transactionResponse.messages.0.description')
                ?? data_get($responseData, 'transactionResponse.errors.0.errorText')
                ?? data_get($responseData, 'messages.message.0.text')
                ?? 'Payment failed.';

            if ($resultCode !== 'Ok' || $transactionResponseCode !== '1') {
                $transactionErrors = data_get($responseData, 'transactionResponse.errors', []);
                Log::warning('[PayLink] declined / failed', [
                    'invoice'            => $invoiceNumber,
                    'message'            => $messageText,
                    'transaction_errors' => $transactionErrors,
                ]);
                return response()->json([
                    'success'            => false,
                    'message'            => $messageText,
                    'transaction_errors' => $transactionErrors,
                ], 422);
            }

            // ── Mark the link paid (only if still unpaid, to be safe) ──────────
            $updated = PaymentLink::where('id', $link->id)
                ->where('status', 'unpaid')
                ->update([
                    'status'         => 'paid',
                    'invoice_number' => $invoiceNumber,
                    'transaction_id' => $transId,
                    'auth_code'      => $authCode,
                    'payer_email'    => $validated['email'],
                    'paid_at'        => now(),
                ]);

            if (! $updated) {
                // Extremely rare race: another request marked it paid between our
                // guard and here. The charge went through; log loudly for support.
                Log::warning('[PayLink] link already paid at update time', [
                    'invoice' => $invoiceNumber,
                    'token'   => $link->token,
                    'transId' => $transId,
                ]);
            }

            // Put the payment on the client's record so Burgundy sees it there.
            if ($link->partner === 'burgundy' && $link->burgundy_client_id) {
                try {
                    BurgundyClient::find($link->burgundy_client_id)?->logEvent(
                        'payment',
                        'Paid one-time payment link: $' . number_format((float) $link->amount, 2) . '.',
                        ['invoice' => $invoiceNumber, 'transaction' => $transId],
                    );
                } catch (\Throwable $e) {
                    Log::warning('[PayLink] Could not log payment on Burgundy client', ['error' => $e->getMessage()]);
                }
            }

            // An affiliate paying a client's fee up front: the contract and
            // onboarding belong to the CLIENT, not to the person holding the card.
            $forClient = (bool) $link->affiliate_id && $link->email;

            // A payment link had no contract at all. Open one and send the
            // client to sign it rather than leaving them on an inline receipt.
            $agreement = ServiceAgreements::start([
                'source'              => 'payment_link',
                'source_id'           => $link->id,
                // Burgundy's links produce Burgundy's agreement, not Victoria's.
                'partner'             => $link->partner === 'burgundy' ? 'burgundy' : 'victoria',
                'plan_key'            => 'payment-link',
                'plan_label'          => $link->service_description ?: ($link->note ?: 'Credit services'),
                'service_description' => $link->service_description,
                'charged_today'       => (float) $link->amount,
                'recurring_amount'    => null,
                'client_name'         => $link->client_name,
                'client_phone'        => $forClient ? null : ($validated['phone'] ?? null),
                'email'               => $forClient ? $link->email : $validated['email'],
                'invoice_number'      => $invoiceNumber,
                'next_url'            => $forClient ? url('/onboarding') : null,
            ]);

            $redirect = $agreement ? ServiceAgreements::signingUrl($agreement) : null;

            if ($forClient) {
                \App\Services\Affiliates::recordForLink($link->fresh());

                if ($agreement) {
                    $this->sendClientTheirContract($link, $agreement);
                    $redirect = \Illuminate\Support\Facades\URL::temporarySignedRoute(
                        'pay-for-client.done', now()->addDays(2), ['agreement' => $agreement->id],
                    );
                }
            }

            return response()->json([
                'success'     => true,
                'message'     => 'Payment successful.',
                'invoice'     => $invoiceNumber,
                'transaction' => $transId,
                'redirect'    => $redirect,
            ]);

        } catch (\Throwable $e) {
            Log::error('[PayLink] exception', [
                'invoice' => $invoiceNumber,
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Server error processing payment. Please contact support.',
            ], 500);
        }
    }
}
