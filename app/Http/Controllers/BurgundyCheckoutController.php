<?php

namespace App\Http\Controllers;

use App\Models\BurgundyClient;
use App\Models\Subscription;
use App\Services\BurgundyClientMatcher;
use App\Support\PartnershipPlans;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * The shared $100 checkout for Burgundy's clients.
 *
 * $100 charged today, then $100/month until cancelled.
 *
 * Deliberately separate from AcceptJsPaymentController rather than another
 * entry in its plan catalogue: that controller runs Victoria's live money every
 * day, and the safest change to it is none. The Authorize.Net sequence below
 * mirrors it exactly, including the one-second pause and the E00040 retry loop,
 * which are both there because Authorize.Net needs a moment to commit a new
 * customer profile before ARB can reference it.
 *
 * The subscription row it writes is an ordinary one. That is what lets the
 * existing webhook handle every rebill, failure, refund and cancellation
 * afterwards without a line of new code.
 */
class BurgundyCheckoutController extends Controller
{
    /**
     * @param  string  $tier  'legacy' ($100, the transferring book) or 'new' ($149).
     */
    public function show(string $tier = 'legacy')
    {
        $plan = PartnershipPlans::tier($tier);

        return view('burgundy.checkout', [
            'tier'       => $tier,
            'enrollment' => (string) $plan['enrollment'],
            'monthly'    => (string) $plan['monthly'],
            'planLabel'  => (string) $plan['label'],
            'postUrl'    => route('burgundy.checkout.process', ['tier' => $tier]),
        ]);
    }

    public function process(Request $request, BurgundyClientMatcher $matcher, string $tier = 'legacy')
    {
        $validated = $request->validate([
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

        // Resolved from the tier in the URL, then stamped onto the subscription.
        // A client's price is therefore fixed at the moment they sign up and is
        // never re-derived from config afterwards.
        $plan       = PartnershipPlans::tier($tier);
        $planKey    = (string) $plan['key'];
        $planLabel  = (string) $plan['label'];
        $enrollment = number_format((float) $plan['enrollment'], 2, '.', '');
        $monthly    = number_format((float) $plan['monthly'], 2, '.', '');

        $invoiceNumber = 'BG-' . time() . '-' . strtoupper(Str::random(4));

        $environment = config('services.authorize_net.environment');
        $apiLoginId  = config('services.authorize_net.api_login_id');
        $txKey       = config('services.authorize_net.transaction_key');

        $endpoint = $environment === 'sandbox'
            ? 'https://apitest.authorize.net/xml/v1/request.api'
            : 'https://api.authorize.net/xml/v1/request.api';

        if (empty($apiLoginId) || empty($txKey)) {
            Log::error('[Burgundy] Authorize.Net credentials missing', ['invoice' => $invoiceNumber]);

            return response()->json([
                'success' => false,
                'message' => 'Payment system is not configured yet. Please contact support.',
            ], 503);
        }

        $rawCardNumber = preg_replace('/\D/', '', $validated['cardNumber']);
        $expDate       = $validated['expYear'] . '-' . $validated['expMonth']; // YYYY-MM

        $payload = [
            'createTransactionRequest' => [
                'merchantAuthentication' => ['name' => $apiLoginId, 'transactionKey' => $txKey],
                'refId'                  => (string) Str::uuid(),
                'transactionRequest'     => [
                    'transactionType' => 'authCaptureTransaction',
                    'amount'          => $enrollment,
                    'payment'         => [
                        'creditCard' => [
                            'cardNumber'     => $rawCardNumber,
                            'expirationDate' => $expDate,
                            'cardCode'       => $validated['cardCode'],
                        ],
                    ],
                    'order' => [
                        'invoiceNumber' => $invoiceNumber,
                        'description'   => mb_substr($planLabel, 0, 255),
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

            Log::info('[Burgundy] charge response', [
                'invoice' => $invoiceNumber,
                'status'  => $httpResponse->status(),
                'result'  => data_get($responseData, 'messages.resultCode'),
            ]);

            $resultCode   = data_get($responseData, 'messages.resultCode');
            $responseCode = data_get($responseData, 'transactionResponse.responseCode');
            $transId      = data_get($responseData, 'transactionResponse.transId');
            $authCode     = data_get($responseData, 'transactionResponse.authCode');

            $messageText = data_get($responseData, 'transactionResponse.messages.0.description')
                ?? data_get($responseData, 'transactionResponse.errors.0.errorText')
                ?? data_get($responseData, 'messages.message.0.text')
                ?? 'Payment failed.';

            if ($resultCode !== 'Ok' || $responseCode !== '1') {
                Log::warning('[Burgundy] declined / failed', [
                    'invoice' => $invoiceNumber,
                    'message' => $messageText,
                ]);

                return response()->json([
                    'success'            => false,
                    'message'            => $messageText,
                    'transaction_errors' => data_get($responseData, 'transactionResponse.errors', []),
                ], 422);
            }

            // ── Recurring: CIM profile from this transaction, then ARB ──
            [$profileId, $paymentProfileId, $arbId, $arbError] = $this->createRecurringSubscription(
                $invoiceNumber, $transId, $validated['email'], $planLabel, $monthly, $apiLoginId, $txKey, $endpoint
            );

            // The $100 is already captured either way, so the client record is
            // written regardless — support can rebuild the schedule by hand, but
            // only if we kept the customer and the transaction.
            $subscription = $this->persistSubscription(
                $validated, $planKey, $planLabel, $enrollment, $monthly,
                $invoiceNumber, $transId, $authCode, $arbId, $profileId, $paymentProfileId
            );

            $client = $this->attachClient($matcher, $validated, $subscription, $invoiceNumber, $transId, $arbError);

            // Tells the existing webhook this is a signup charge rather than a
            // rebill, so it records the payment as 'initial' against this invoice.
            Cache::put('checkout_customer_' . $invoiceNumber, [
                'first_name' => $validated['first_name'],
                'last_name'  => $validated['last_name'],
                'email'      => $validated['email'],
                'phone'      => $validated['phone'],
                'address'    => $validated['address'],
                'city'       => $validated['city'],
                'state'      => $validated['state'],
                'zip'        => $validated['zip'],
                'plan_key'   => $planKey,
                'plan_label' => $planLabel,
                'amount'     => $enrollment,
            ], now()->addMinutes(120));

            // Hand off to the agreement, then onboarding.
            session([
                'burgundy_paid'        => true,
                'burgundy_tier'        => $tier,
                'burgundy_client_id'   => $client?->id,
                'burgundy_invoice'     => $invoiceNumber,
                'burgundy_first_name'  => $validated['first_name'],
                'burgundy_customer'    => [
                    'first_name' => $validated['first_name'],
                    'last_name'  => $validated['last_name'],
                    'email'      => $validated['email'],
                    'phone'      => $validated['phone'],
                    'address'    => $validated['address'],
                    'city'       => $validated['city'],
                    'state'      => $validated['state'],
                    'zip'        => $validated['zip'],
                ],
            ]);

            if ($arbError !== null) {
                // Charged, but the monthly schedule did not attach. The client
                // still continues — chasing this is our job, not theirs.
                Log::error('[Burgundy] ARB setup failed after successful charge', [
                    'invoice' => $invoiceNumber,
                    'error'   => $arbError,
                ]);
            }

            return response()->json([
                'success'     => true,
                'message'     => 'Payment successful.',
                'invoice'     => $invoiceNumber,
                'transaction' => $transId,
                'redirect'    => route('burgundy.agreement.show'),
            ]);

        } catch (\Throwable $e) {
            Log::error('[Burgundy] checkout exception', [
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
     * CIM customer profile from the completed transaction, then an open-ended
     * monthly ARB subscription starting in one month.
     *
     * @return array{0:?string,1:?string,2:?string,3:?string} [profileId, paymentProfileId, arbId, error]
     */
    private function createRecurringSubscription(
        string $invoiceNumber,
        string $transId,
        string $email,
        string $planLabel,
        string $monthly,
        string $apiLoginId,
        string $txKey,
        string $endpoint
    ): array {
        $cimResponse = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json',
        ])->post($endpoint, [
            'createCustomerProfileFromTransactionRequest' => [
                'merchantAuthentication' => ['name' => $apiLoginId, 'transactionKey' => $txKey],
                'transId'                => $transId,
                'customer'               => ['email' => $email],
            ],
        ]);

        $cimData = json_decode(trim(preg_replace('/^\xEF\xBB\xBF/', '', $cimResponse->body())), true);

        $profileId        = data_get($cimData, 'customerProfileId');
        $paymentProfileId = data_get($cimData, 'customerPaymentProfileIdList.numericString.0')
            ?? data_get($cimData, 'customerPaymentProfileIdList.0');

        if (data_get($cimData, 'messages.resultCode') !== 'Ok' || ! $profileId || ! $paymentProfileId) {
            $err = data_get($cimData, 'messages.message.0.text', 'CIM profile creation failed');
            Log::error('[Burgundy] CIM profile creation failed', ['invoice' => $invoiceNumber, 'error' => $err]);

            return [null, null, null, $err];
        }

        // Authorize.Net needs a beat to commit the new profile before ARB can
        // reference it — without this the first ARB call fails with E00040.
        sleep(1);

        $arbPayload = [
            'ARBCreateSubscriptionRequest' => [
                'merchantAuthentication' => ['name' => $apiLoginId, 'transactionKey' => $txKey],
                'refId'                  => (string) Str::uuid(),
                'subscription'           => [
                    'name'            => mb_substr($planLabel, 0, 50),
                    'paymentSchedule' => [
                        'interval'         => ['length' => '1', 'unit' => 'months'],
                        'startDate'        => now()->addMonth()->format('Y-m-d'),
                        'totalOccurrences' => '9999',   // open-ended — bills until cancelled
                        'trialOccurrences' => '0',
                    ],
                    'amount'      => $monthly,
                    'trialAmount' => '0.00',
                    // Stamping the invoice here is what lets the webhook tie each
                    // monthly rebill back to this client.
                    'order'       => [
                        'invoiceNumber' => mb_substr($invoiceNumber, 0, 20),
                        'description'   => mb_substr($planLabel, 0, 255),
                    ],
                    'profile'     => [
                        'customerProfileId'        => $profileId,
                        'customerPaymentProfileId' => $paymentProfileId,
                    ],
                ],
            ],
        ];

        $arbError = null;

        for ($attempt = 1; $attempt <= 3; $attempt++) {
            if ($attempt > 1) {
                sleep(1);
            }

            $arbResponse = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ])->post($endpoint, $arbPayload);

            $arbData = json_decode(trim(preg_replace('/^\xEF\xBB\xBF/', '', $arbResponse->body())), true);

            $arbId    = data_get($arbData, 'subscriptionId');
            $arbError = data_get($arbData, 'messages.message.0.text');

            Log::info('[Burgundy] ARB response', [
                'invoice' => $invoiceNumber,
                'attempt' => $attempt,
                'result'  => data_get($arbData, 'messages.resultCode'),
                'arb_id'  => $arbId,
            ]);

            if (data_get($arbData, 'messages.resultCode') === 'Ok' && $arbId) {
                return [$profileId, $paymentProfileId, $arbId, null];
            }
        }

        return [$profileId, $paymentProfileId, null, $arbError ?? 'ARB subscription create failed'];
    }

    private function persistSubscription(
        array $v,
        string $planKey,
        string $planLabel,
        string $enrollment,
        string $monthly,
        string $invoiceNumber,
        ?string $transId,
        ?string $authCode,
        ?string $arbId,
        ?string $profileId,
        ?string $paymentProfileId
    ): ?Subscription {
        try {
            return Subscription::create([
                'first_name'                  => $v['first_name'],
                'last_name'                   => $v['last_name'],
                'email'                       => $v['email'],
                'phone'                       => $v['phone'],
                'address'                     => $v['address'],
                'city'                        => $v['city'],
                'state'                       => $v['state'],
                'zip'                         => $v['zip'],
                'plan_key'                    => $planKey,
                'plan_label'                  => $planLabel,
                'amount'                      => $enrollment,
                'recurring_amount'            => $monthly,
                'invoice_number'              => $invoiceNumber,
                'transaction_id'              => $transId,
                'auth_code'                   => $authCode,
                'arb_subscription_id'         => $arbId,
                'customer_profile_id'         => $profileId,
                'customer_payment_profile_id' => $paymentProfileId,
                'status'                      => 'active',
                'subscribed_at'               => now(),
                'next_billing_date'           => now()->addMonth(),
            ]);
        } catch (\Throwable $e) {
            Log::error('[Burgundy] Failed to save subscription', [
                'invoice' => $invoiceNumber,
                'error'   => $e->getMessage(),
            ]);

            return null;
        }
    }

    /** Identify who just paid and attach the subscription to their record. */
    private function attachClient(
        BurgundyClientMatcher $matcher,
        array $v,
        ?Subscription $subscription,
        string $invoiceNumber,
        ?string $transId,
        ?string $arbError
    ): ?BurgundyClient {
        try {
            $client = $matcher->matchOrFlag([
                'first_name' => $v['first_name'],
                'last_name'  => $v['last_name'],
                'email'      => $v['email'],
                'phone'      => $v['phone'],
                'zip'        => $v['zip'],
            ], 'payment');

            $updates = ['onboarding_status' => 'pending'];

            if ($subscription && ! $client->subscription_id) {
                $updates['subscription_id'] = $subscription->id;
            }

            $client->update($updates);

            $client->logEvent('paid', sprintf(
                'Paid $%s enrollment on %s. Invoice %s.',
                number_format((float) ($subscription?->amount ?? 0), 2),
                $subscription?->plan_key ?: 'unknown plan',
                $invoiceNumber,
            ), ['invoice' => $invoiceNumber, 'transaction_id' => $transId]);

            if ($arbError !== null) {
                $client->logEvent(
                    'arb_failed',
                    'The $100 enrollment was charged, but the monthly schedule could not be created at Authorize.Net. Set it up manually. (' . $arbError . ')',
                    ['invoice' => $invoiceNumber],
                );
            }

            return $client;
        } catch (\Throwable $e) {
            // The charge succeeded; never fail the request over bookkeeping.
            Log::error('[Burgundy] Failed to attach client to payment', [
                'invoice' => $invoiceNumber,
                'error'   => $e->getMessage(),
            ]);

            return null;
        }
    }
}
