<?php

namespace App\Http\Controllers;

use App\Models\Ebook;
use App\Models\EbookOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EbookCheckoutController extends Controller
{
    public function show(string $slug)
    {
        $ebook = Ebook::where('slug', $slug)->where('is_active', true)->firstOrFail();

        Log::info('Ebook checkout opened', [
            'ebook_slug' => $slug,
            'ip'         => request()->ip(),
            'session_id' => session()->getId(),
        ]);

        return view('payments.ebook-checkout', [
            'ebook'       => $ebook,
            'clientKey'   => config('services.authorize_net.client_key'),
            'apiLogin'    => config('services.authorize_net.api_login_id'),
            'environment' => config('services.authorize_net.environment'),
        ]);
    }

    public function processPayment(Request $request)
    {
        $validated = $request->validate([
            'dataDescriptor'   => 'required|string',
            'dataValue'        => 'required|string',
            'ebook_slug'       => 'required|string|exists:ebooks,slug',
            'first_name'       => 'required|string|max:100',
            'last_name'        => 'required|string|max:100',
            'email'            => 'required|email|max:150',
            'phone'            => 'required|string|max:30',
            'address'          => 'required|string|max:255',
            'city'             => 'required|string|max:100',
            'state'            => 'required|string|max:10',
            'zip'              => 'required|string|max:20',
            'cardName'         => 'required|string|max:150',
            'agree_terms'      => 'required|accepted',
            'agree_privacy'    => 'required|accepted',
            'marketing_opt_in' => 'nullable|boolean',
        ]);

        $ebook = Ebook::where('slug', $validated['ebook_slug'])
            ->where('is_active', true)
            ->firstOrFail();

        $amount        = number_format((float) $ebook->price, 2, '.', '');
        $invoiceNumber = 'EB-' . time() . '-' . strtoupper(Str::random(4));

        $environment = config('services.authorize_net.environment');
        $apiLoginId  = config('services.authorize_net.api_login_id');
        $txKey       = config('services.authorize_net.transaction_key');

        $endpoint = $environment === 'sandbox'
            ? 'https://apitest.authorize.net/xml/v1/request.api'
            : 'https://api.authorize.net/xml/v1/request.api';

        if (empty($apiLoginId) || empty($txKey)) {
            Log::error('Authorize.Net credentials missing (ebook)', ['invoice' => $invoiceNumber]);
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
                        'description'   => Str::limit('eBook · ' . $ebook->title, 250),
                    ],
                    'customer' => [
                        'email' => $validated['email'],
                    ],
                    'billTo' => [
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

            $rawBody = preg_replace('/^\xEF\xBB\xBF/', '', $httpResponse->body());
            $responseData = json_decode(trim($rawBody), true);

            Log::info('Authorize.Net ebook charge response', [
                'invoice' => $invoiceNumber,
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

                Log::warning('Ebook payment declined', [
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

            // Persist the order
            $order = EbookOrder::create([
                'ebook_id'         => $ebook->id,
                'ebook_slug'       => $ebook->slug,
                'ebook_title'      => $ebook->title,
                'amount'           => $amount,
                'first_name'       => $validated['first_name'],
                'last_name'        => $validated['last_name'],
                'email'            => $validated['email'],
                'phone'            => $validated['phone'],
                'address'          => $validated['address'],
                'city'             => $validated['city'],
                'state'            => $validated['state'],
                'zip'              => $validated['zip'],
                'invoice_number'   => $invoiceNumber,
                'transaction_id'   => $transId,
                'auth_code'        => $authCode,
                'status'           => 'paid',
                'marketing_opt_in' => (bool) ($validated['marketing_opt_in'] ?? false),
                'ip_address'       => $request->ip(),
                'user_agent'       => Str::limit((string) $request->userAgent(), 250, ''),
                'charged_at'       => now(),
                'raw_payload'      => [
                    'authorize_net' => $responseData,
                ],
            ]);

            // Fire Meta CAPI (best effort)
            $this->fireMetaCapi($invoiceNumber, $transId, $amount, $validated, $request->ip(), $request->userAgent());

            return response()->json([
                'success'     => true,
                'message'     => 'Payment successful.',
                'invoice'     => $invoiceNumber,
                'transaction' => $transId,
                'redirect'    => route('ebooks.thanks', ['order' => $order->invoice_number]),
            ]);
        } catch (\Throwable $e) {
            Log::error('Ebook payment exception', [
                'invoice' => $invoiceNumber ?? 'N/A',
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

    public function thanks(EbookOrder $order)
    {
        if ($order->status !== 'paid') {
            abort(404);
        }

        // Tag the first view so admin sees engagement
        if (!$order->downloaded_at) {
            $order->forceFill([
                'downloaded_at'  => now(),
                'download_count' => $order->download_count + 1,
            ])->save();
        }

        $ebook = $order->ebook ?: Ebook::where('slug', $order->ebook_slug)->first();

        return view('payments.ebook-thanks', [
            'order'     => $order,
            'ebook'     => $ebook,
            'driveLink' => $ebook?->drive_link,
        ]);
    }

    private function fireMetaCapi(
        string $invoice,
        string $transId,
        string $amount,
        array $validated,
        string $ip,
        ?string $userAgent
    ): void {
        $pixelId   = config('services.meta.pixel_id');
        $capiToken = config('services.meta.capi_token');

        if (!$pixelId || !$capiToken) {
            return;
        }

        $eventPayload = [
            'data' => [[
                'event_name'    => 'Purchase',
                'event_time'    => time(),
                'action_source' => 'website',
                'event_id'      => 'ebook_' . $transId,
                'user_data'     => [
                    'em' => [hash('sha256', strtolower(trim($validated['email'])))],
                    'ph' => [hash('sha256', preg_replace('/\D/', '', trim($validated['phone'])))],
                    'fn' => [hash('sha256', strtolower(trim($validated['first_name'])))],
                    'ln' => [hash('sha256', strtolower(trim($validated['last_name'])))],
                    'zp' => [hash('sha256', trim($validated['zip']))],
                    'ct' => [hash('sha256', strtolower(trim($validated['city'])))],
                    'st' => [hash('sha256', strtolower(trim($validated['state'])))],
                    'client_ip_address' => $ip,
                    'client_user_agent' => $userAgent ?? '',
                ],
                'custom_data' => [
                    'currency'      => 'USD',
                    'value'         => $amount,
                    'order_id'      => $invoice,
                    'content_type'  => 'ebook',
                ],
            ]],
        ];

        try {
            Http::timeout(10)->post(
                "https://graph.facebook.com/v19.0/{$pixelId}/events?access_token={$capiToken}",
                $eventPayload
            );
        } catch (\Throwable $e) {
            Log::error('Meta CAPI failed (ebook)', ['invoice' => $invoice, 'error' => $e->getMessage()]);
        }
    }
}
