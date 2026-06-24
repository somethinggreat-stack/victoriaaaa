<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * TEMPORARY one-off secure payment links.
 *
 * These pages are intentionally NOT linked anywhere on the site — they are
 * reached only by their obscure token URL and are meant to be sent privately
 * to a specific client, then removed afterwards.
 */
class CustomCheckoutController extends Controller
{
    /**
     * Each token = one private checkout page with a fixed amount + schedule.
     *
     * `recurring`:
     *   null  → single one-time charge only.
     *   array → charge `amount` today, then an ARB subscription bills
     *           `recurring.amount` on `start_date`, repeating every
     *           `interval_length` `interval_unit` for `occurrences` times.
     */
    public const LINKS = [
        // Link 1 — $250 today, then $250 on Jul 10, then $250 on Jul 24 (2026).
        'vlc-7k3p9q2x' => [
            'amount'      => '250.00',
            'label'       => 'Victoria Love — Payment Plan',
            'tagline'     => '$250 today, then two more payments of $250.',
            'recurring'   => [
                'amount'          => '250.00',
                'interval_length' => 14,
                'interval_unit'   => 'days',
                'start_date'      => '2026-07-10',
                'occurrences'     => 2,
                'next_billing'    => '2026-07-10',
            ],
            'total'       => '750.00',
            'schedule'    => [
                ['label' => 'Today',         'amount' => '$250.00', 'note' => 'Charged now'],
                ['label' => 'July 10, 2026', 'amount' => '$250.00', 'note' => 'Auto-charged'],
                ['label' => 'July 24, 2026', 'amount' => '$250.00', 'note' => 'Auto-charged'],
            ],
        ],

        // Link 2 — single $1,500 payment, no subscription.
        'vlc-4m8t6w1z' => [
            'amount'      => '1500.00',
            'label'       => 'Victoria Love — One-Time Payment',
            'tagline'     => 'Single payment of $1,500.',
            'recurring'   => null,
            'total'       => '1500.00',
            'schedule'    => [
                ['label' => 'Today', 'amount' => '$1,500.00', 'note' => 'Charged now · one-time'],
            ],
        ],
    ];

    public function show(string $token)
    {
        $link = self::LINKS[$token] ?? abort(404);

        return view('payments.link-checkout', [
            'token' => $token,
            'link'  => $link,
        ]);
    }

    public function process(Request $request)
    {
        $validated = $request->validate([
            'token'         => 'required|string|in:' . implode(',', array_keys(self::LINKS)),
            'cardNumber'    => 'required|string|min:13|max:25',
            'expMonth'      => 'required|string|size:2',
            'expYear'       => 'required|string|size:4',
            'cardCode'      => 'required|string|min:3|max:4',
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

        $link         = self::LINKS[$validated['token']];
        $amount       = $link['amount'];
        $label        = $link['label'];
        $recurring    = $link['recurring'];

        $invoiceNumber = 'LINK-' . time() . '-' . strtoupper(Str::random(4));

        $environment = config('services.authorize_net.environment');
        $apiLoginId  = config('services.authorize_net.api_login_id');
        $txKey       = config('services.authorize_net.transaction_key');

        $endpoint = $environment === 'sandbox'
            ? 'https://apitest.authorize.net/xml/v1/request.api'
            : 'https://api.authorize.net/xml/v1/request.api';

        if (empty($apiLoginId) || empty($txKey)) {
            Log::error('[LinkPay] Authorize.Net credentials missing', ['invoice' => $invoiceNumber]);
            return response()->json([
                'success' => false,
                'message' => 'Payment system is not configured yet. Please contact support.',
            ], 503);
        }

        $rawCardNumber = preg_replace('/\D/', '', $validated['cardNumber']);
        $expDate       = $validated['expYear'] . '-' . $validated['expMonth']; // YYYY-MM

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
                        'creditCard' => [
                            'cardNumber'     => $rawCardNumber,
                            'expirationDate' => $expDate,
                            'cardCode'       => $validated['cardCode'],
                        ],
                    ],
                    'order' => [
                        'invoiceNumber' => $invoiceNumber,
                        'description'   => $label,
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

            Log::info('[LinkPay] charge response', [
                'invoice' => $invoiceNumber,
                'token'   => $validated['token'],
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
                Log::warning('[LinkPay] declined / failed', [
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

            // ── Recurring schedule (Link 1 only) ────────────────────────────
            $customerProfileId        = null;
            $customerPaymentProfileId = null;
            $subscriptionId           = null;

            if ($recurring !== null) {
                [$customerProfileId, $customerPaymentProfileId, $subscriptionId, $arbError] =
                    $this->createCustomSubscription(
                        $invoiceNumber, $transId, $validated['email'], $label,
                        $recurring, $apiLoginId, $txKey, $endpoint
                    );

                if ($arbError !== null) {
                    $this->persistRow($validated, $validated['token'], $label, $amount, null, $invoiceNumber, $transId, $authCode, null, null, null, $recurring);
                    return response()->json([
                        'success' => false,
                        'message' => 'Your payment was captured but we could not set up the remaining scheduled payments. Please contact support with invoice ' . $invoiceNumber . '. (' . $arbError . ')',
                    ], 422);
                }
            }

            $this->persistRow(
                $validated, $validated['token'], $label, $amount,
                $recurring['amount'] ?? null, $invoiceNumber, $transId, $authCode,
                $subscriptionId, $customerProfileId, $customerPaymentProfileId, $recurring
            );

            return response()->json([
                'success'     => true,
                'message'     => 'Payment successful.',
                'invoice'     => $invoiceNumber,
                'transaction' => $transId,
            ]);

        } catch (\Throwable $e) {
            Log::error('[LinkPay] exception', [
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

    /**
     * CIM profile from the completed transaction, then an ARB subscription
     * using the link's custom interval / start date / occurrence count.
     * Returns [customerProfileId, customerPaymentProfileId, subscriptionId, errorOrNull].
     */
    private function createCustomSubscription(
        string $invoiceNumber,
        string $transId,
        string $email,
        string $label,
        array $recurring,
        string $apiLoginId,
        string $txKey,
        string $endpoint
    ): array {
        $cimPayload = [
            'createCustomerProfileFromTransactionRequest' => [
                'merchantAuthentication' => ['name' => $apiLoginId, 'transactionKey' => $txKey],
                'transId'  => $transId,
                'customer' => ['email' => $email],
            ],
        ];

        $cimResponse = Http::withHeaders(['Content-Type' => 'application/json', 'Accept' => 'application/json'])
            ->post($endpoint, $cimPayload);
        $cimData = json_decode(trim(preg_replace('/^\xEF\xBB\xBF/', '', $cimResponse->body())), true);

        Log::info('[LinkPay] CIM response', ['invoice' => $invoiceNumber, 'response' => $cimData]);

        $cimResultCode            = data_get($cimData, 'messages.resultCode');
        $customerProfileId        = data_get($cimData, 'customerProfileId');
        $customerPaymentProfileId = data_get($cimData, 'customerPaymentProfileIdList.numericString.0')
            ?? data_get($cimData, 'customerPaymentProfileIdList.0');

        if ($cimResultCode !== 'Ok' || !$customerProfileId || !$customerPaymentProfileId) {
            $errMsg = data_get($cimData, 'messages.message.0.text', 'CIM profile creation failed');
            Log::error('[LinkPay] CIM failed', ['invoice' => $invoiceNumber, 'response' => $cimData]);
            return [null, null, null, $errMsg];
        }

        sleep(1);

        $arbPayload = [
            'ARBCreateSubscriptionRequest' => [
                'merchantAuthentication' => ['name' => $apiLoginId, 'transactionKey' => $txKey],
                'refId'        => (string) Str::uuid(),
                'subscription' => [
                    'name'            => mb_substr($label, 0, 50),
                    'paymentSchedule' => [
                        'interval'         => [
                            'length' => (string) $recurring['interval_length'],
                            'unit'   => $recurring['interval_unit'],
                        ],
                        'startDate'        => $recurring['start_date'],
                        'totalOccurrences' => (string) $recurring['occurrences'],
                        'trialOccurrences' => '0',
                    ],
                    'amount'      => $recurring['amount'],
                    'trialAmount' => '0.00',
                    'profile'     => [
                        'customerProfileId'        => $customerProfileId,
                        'customerPaymentProfileId' => $customerPaymentProfileId,
                    ],
                ],
            ],
        ];

        $arbResultCode  = null;
        $subscriptionId = null;
        $arbErrorText   = null;

        for ($attempt = 1; $attempt <= 3; $attempt++) {
            if ($attempt > 1) {
                sleep(1);
            }

            $arbResponse = Http::withHeaders(['Content-Type' => 'application/json', 'Accept' => 'application/json'])
                ->post($endpoint, $arbPayload);
            $arbData = json_decode(trim(preg_replace('/^\xEF\xBB\xBF/', '', $arbResponse->body())), true);

            $arbResultCode  = data_get($arbData, 'messages.resultCode');
            $subscriptionId = data_get($arbData, 'subscriptionId');
            $arbErrorText   = data_get($arbData, 'messages.message.0.text');

            Log::info('[LinkPay] ARB response', [
                'invoice'        => $invoiceNumber,
                'attempt'        => $attempt,
                'arbResultCode'  => $arbResultCode,
                'subscriptionId' => $subscriptionId,
            ]);

            if ($arbResultCode === 'Ok' && $subscriptionId) {
                return [$customerProfileId, $customerPaymentProfileId, $subscriptionId, null];
            }
        }

        return [$customerProfileId, $customerPaymentProfileId, null, $arbErrorText ?? 'ARB subscription create failed'];
    }

    private function persistRow(
        array $validated,
        string $token,
        string $label,
        string $amount,
        ?string $recurringAmt,
        string $invoiceNumber,
        ?string $transId,
        ?string $authCode,
        ?string $arbSubscriptionId,
        ?string $customerProfileId,
        ?string $customerPaymentProfileId,
        ?array $recurring
    ): void {
        try {
            Subscription::create([
                'first_name'                  => $validated['first_name'],
                'last_name'                   => $validated['last_name'],
                'email'                       => $validated['email'],
                'phone'                       => $validated['phone'],
                'address'                     => $validated['address'],
                'city'                        => $validated['city'],
                'state'                       => $validated['state'],
                'zip'                         => $validated['zip'],
                'plan_key'                    => 'link-' . $token,
                'plan_label'                  => $label,
                'amount'                      => $amount,
                'recurring_amount'            => $recurringAmt,
                'invoice_number'              => $invoiceNumber,
                'transaction_id'              => $transId,
                'auth_code'                   => $authCode,
                'arb_subscription_id'         => $arbSubscriptionId,
                'customer_profile_id'         => $customerProfileId,
                'customer_payment_profile_id' => $customerPaymentProfileId,
                'status'                      => 'active',
                'subscribed_at'               => now(),
                'next_billing_date'           => $recurring['next_billing'] ?? null,
            ]);
        } catch (\Throwable $dbEx) {
            Log::error('[LinkPay] failed to save subscription', [
                'invoice' => $invoiceNumber,
                'error'   => $dbEx->getMessage(),
            ]);
        }
    }
}
