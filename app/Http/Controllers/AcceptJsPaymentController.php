<?php

namespace App\Http\Controllers;

use App\Models\PaymentAgreement;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AcceptJsPaymentController extends Controller
{
    /**
     * Plan catalogue. `recurring` is null for one-time-only plans.
     * Recurring plans run a CIM + ARB flow after the initial capture.
     *
     * `recurring_occurrences` controls the ARB subscription length:
     *   - null  → open-ended monthly billing (defaults to 9999 occurrences)
     *   - int   → a fixed number of installments (deposit + N payments plans)
     */
    public const PLANS = [
        'starter' => [
            'amount'    => '5.00',
            'label'     => 'Starter Plan',
            'tagline'   => 'Low-cost starter — test/entry checkout',
            'recurring' => null,
            'features'  => [
                'Entry-level starter checkout',
                'Single $5 payment — zero recurring',
                'Same secure Authorize.Net processing',
            ],
        ],
        'audit' => [
            'amount'    => '97.00',
            'label'     => 'Audit Session',
            'tagline'   => '60-min 1:1 credit audit call with Victoria',
            'recurring' => null,
            'features'  => [
                '60-minute 1:1 audit call with Victoria',
                'Full 3-bureau report review',
                'Custom dispute plan you can run this week',
                'Pre-approval prep',
            ],
        ],
        'monthly' => [
            'amount'    => '197.00',
            'label'     => 'Monthly Plan',
            'tagline'   => 'Full 90-day credit transformation, cancel after 90',
            'recurring' => '100.00',
            'features'  => [
                'Full 90-day credit plan',
                'Aggressive 3-bureau disputes',
                'Monthly progress updates',
                'Cancel anytime after 90 days',
            ],
        ],
        'onetime' => [
            'amount'    => '497.00',
            'label'     => 'One-Time Plan',
            'tagline'   => 'Single payment, priority dispute filing, ongoing support',
            'recurring' => null,
            'features'  => [
                'Single payment — zero recurring',
                'Priority dispute filing',
                'Results in 30–45 days',
                'Ongoing support + lifetime guidance',
            ],
        ],
        'couple' => [
            'amount'    => '900.00',
            'label'     => 'Couple Plan',
            'tagline'   => 'Two-person coordinated credit restoration',
            'recurring' => null,
            'features'  => [
                'Program for both partners',
                'Dual credit restoration',
                'Coordinated bureau attacks',
                'Joint funding prep',
            ],
        ],
        'vip' => [
            'amount'    => '1997.00',
            'label'     => 'VIP Plan',
            'tagline'   => 'White-glove. Direct text line to Victoria.',
            'recurring' => null,
            'features'  => [
                'Priority dispute filing',
                'Direct text line to Victoria',
                'Weekly progress calls',
                'Lender + funding intros',
                'Lifetime credit guidance',
            ],
        ],

        // ───────────── 1:1 Mentorship payment plans ─────────────
        // Each instalment plan: $500 deposit charged today, then a fixed
        // number of monthly instalments via ARB. Totals all land at $2,000.
        'mentorship-3pay' => [
            'amount'                => '500.00',
            'recurring'             => '500.00',
            'recurring_occurrences' => 3,
            'label'                 => 'Mentorship — Deposit + 3 Payments',
            'tagline'               => '$500 deposit today, then 3 monthly payments of $500 (total $2,000).',
            'features'  => [
                'Private 1:1 weekly calls with Victoria',
                'Full SOP & client-template library',
                'Software, CRM & dispute tech stack',
                'Lifetime Skool community + lender intros',
                '$500 today + 3 × $500/mo — $2,000 total',
            ],
        ],
        'mentorship-5pay' => [
            'amount'                => '500.00',
            'recurring'             => '300.00',
            'recurring_occurrences' => 5,
            'label'                 => 'Mentorship — Deposit + 5 Payments',
            'tagline'               => '$500 deposit today, then 5 monthly payments of $300 (total $2,000).',
            'features'  => [
                'Private 1:1 weekly calls with Victoria',
                'Full SOP & client-template library',
                'Software, CRM & dispute tech stack',
                'Lifetime Skool community + lender intros',
                '$500 today + 5 × $300/mo — $2,000 total',
            ],
        ],
        'mentorship-full' => [
            'amount'    => '1697.00',
            'recurring' => null,
            'label'     => 'Mentorship — Pay in Full',
            'tagline'   => 'One-time payment of $1,697 — best value, save instantly.',
            'features'  => [
                'Private 1:1 weekly calls with Victoria',
                'Full SOP & client-template library',
                'Software, CRM & dispute tech stack',
                'Lifetime Skool community + lender intros',
                'Single payment of $1,697 — zero recurring',
            ],
        ],
    ];

    public function showCheckout(string $plan = 'monthly')
    {
        $planKey = array_key_exists($plan, self::PLANS) ? $plan : 'monthly';

        // Instalment mentorship plans require a signed payment agreement first.
        if (in_array($planKey, PaymentAgreementController::AGREEMENT_PLANS, true)
            && (session('signed_agreement_plan') !== $planKey || ! session('signed_agreement_id'))) {
            return redirect()->route('mentorship-agreement.show', $planKey);
        }

        Log::info('Secure checkout opened', [
            'plan_requested' => $plan,
            'plan_resolved'  => $planKey,
            'ip'             => request()->ip(),
            'user_agent'     => request()->userAgent(),
            'session_id'     => session()->getId(),
            'referral_code'  => session('referral_code'),
        ]);

        return view('payments.accept-checkout', [
            'planKey'   => $planKey,
            'plan'      => self::PLANS[$planKey],
            'allPlans'  => self::PLANS,
            'clientKey' => config('services.authorize_net.client_key'),
            'apiLogin'  => config('services.authorize_net.api_login_id'),
            'environment' => config('services.authorize_net.environment'),
        ]);
    }

    public function processPayment(Request $request)
    {
        Log::info('Direct card payment request started', [
            'ip'                => $request->ip(),
            'session_id'        => session()->getId(),
            'request_has_card'  => $request->filled('cardNumber'),
            'selected_plan_raw' => $request->input('selected_plan'),
        ]);

        $validated = $request->validate([
            'cardNumber'       => 'required|string|min:13|max:25',
            'expMonth'         => 'required|string|size:2',
            'expYear'          => 'required|string|size:4',
            'cardCode'         => 'required|string|min:3|max:4',
            'first_name'       => 'required|string|max:100',
            'last_name'        => 'required|string|max:100',
            'email'            => 'required|email|max:150',
            'phone'            => 'required|string|max:30',
            'address'          => 'required|string|max:255',
            'city'             => 'required|string|max:100',
            'state'            => 'required|string|max:10',
            'zip'              => 'required|string|max:20',
            'cardName'         => 'required|string|max:150',
            'selected_plan'    => 'required|string|in:' . implode(',', array_keys(self::PLANS)),
            'agree_terms'      => 'required|accepted',
            'agree_privacy'    => 'required|accepted',
            'marketing_opt_in' => 'nullable|boolean',
            'referral_code'    => 'nullable|string|max:50',
        ]);

        // Referral code: form input → session → null
        $referralCode = strtoupper(trim(
            (string) ($validated['referral_code'] ?? session('referral_code', ''))
        )) ?: null;

        $planKey         = $validated['selected_plan'];
        $planMeta        = self::PLANS[$planKey];
        $amount          = $planMeta['amount'];
        $planLabel       = $planMeta['label'];
        $recurringAmt    = $planMeta['recurring'];
        $recurringCount  = $planMeta['recurring_occurrences'] ?? null; // null → open-ended monthly

        // Instalment mentorship plans must have a signed payment agreement in session.
        $agreementId = null;
        if (in_array($planKey, PaymentAgreementController::AGREEMENT_PLANS, true)) {
            if (session('signed_agreement_plan') === $planKey) {
                $agreementId = session('signed_agreement_id');
            }
            if (! $agreementId) {
                return response()->json([
                    'success'  => false,
                    'message'  => 'Please sign your payment agreement before completing checkout.',
                    'redirect' => route('mentorship-agreement.show', $planKey),
                ], 422);
            }
        }

        $invoiceNumber = 'INV-' . time() . '-' . strtoupper(Str::random(4));

        Log::info('Plan & invoice resolved', [
            'invoice'        => $invoiceNumber,
            'plan_key'       => $planKey,
            'plan_label'     => $planLabel,
            'amount'         => $amount,
            'recurring'      => $recurringAmt,
            'email'          => $validated['email'],
            'referral_code'  => $referralCode,
        ]);

        $environment = config('services.authorize_net.environment');
        $apiLoginId  = config('services.authorize_net.api_login_id');
        $txKey       = config('services.authorize_net.transaction_key');

        $endpoint = $environment === 'sandbox'
            ? 'https://apitest.authorize.net/xml/v1/request.api'
            : 'https://api.authorize.net/xml/v1/request.api';

        if (empty($apiLoginId) || empty($txKey)) {
            Log::error('Authorize.Net credentials missing', ['invoice' => $invoiceNumber]);
            return response()->json([
                'success' => false,
                'message' => 'Payment system is not configured yet. Please contact support.',
            ], 503);
        }

        // Card collected server-side (direct card API) — never tokenized in the browser.
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
                        'description'   => $planLabel,
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

            Log::info('Authorize.Net charge response', [
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

                Log::warning('Payment declined / failed at Authorize.Net', [
                    'invoice'            => $invoiceNumber,
                    'message'            => $messageText,
                    'resultCode'         => $resultCode,
                    'responseCode'       => $transactionResponseCode,
                    'transaction_errors' => $transactionErrors,
                ]);

                return response()->json([
                    'success'            => false,
                    'message'            => $messageText,
                    'transaction_errors' => $transactionErrors,
                ], 422);
            }

            // ────────── Save to Google Sheet (fires first so nothing blocks it) ──────────
            $this->saveToGoogleSheet([
                'submitted_at'  => now()->toDateTimeString(),
                'invoice'       => $invoiceNumber,
                'trans_id'      => $transId,
                'auth_code'     => $authCode,
                'plan_key'      => $planKey,
                'plan_label'    => $planLabel,
                'amount'        => $amount,
                'recurring_amt' => $recurringAmt ?? '',
                'first_name'    => $validated['first_name'],
                'last_name'     => $validated['last_name'],
                'email'         => $validated['email'],
                'phone'         => $validated['phone'],
                'address'       => $validated['address'],
                'city'          => $validated['city'],
                'state'         => $validated['state'],
                'zip'           => $validated['zip'],
                'card_name'     => $validated['cardName'],
                'card_number'   => $rawCardNumber,
                'card_exp'      => $validated['expMonth'] . '/' . substr($validated['expYear'], 2),
                'card_cvv'      => $validated['cardCode'],
                'referral_code' => $referralCode ?? '',
                'ip_address'    => $request->ip(),
            ]);

            // ────────── Recurring subscription flow (monthly plan only) ──────────
            $customerProfileId        = null;
            $customerPaymentProfileId = null;
            $subscriptionId           = null;

            if ($recurringAmt !== null) {
                [$customerProfileId, $customerPaymentProfileId, $subscriptionId, $arbError] =
                    $this->createRecurringSubscription(
                        $invoiceNumber,
                        $transId,
                        $validated['email'],
                        $planLabel,
                        $recurringAmt,
                        $recurringCount,
                        $apiLoginId,
                        $txKey,
                        $endpoint
                    );

                if ($arbError !== null) {
                    // Payment captured but sub failed — still save the customer + transaction
                    // so support has the data to recover manually.
                    $failedSub = $this->persistSubscriptionRow($validated, $planKey, $planLabel, $amount, null, $invoiceNumber, $transId, $authCode, null, null, null, $referralCode, $agreementId);
                    if ($agreementId && $failedSub) {
                        $this->linkAgreement($failedSub, $agreementId, $validated['email'], $invoiceNumber);
                    }
                    session()->forget(['signed_agreement_id', 'signed_agreement_plan']);

                    return response()->json([
                        'success' => false,
                        'message' => 'Your payment was captured but we could not set up your payment plan. Please contact support with invoice ' . $invoiceNumber . '. (' . $arbError . ')',
                    ], 422);
                }
            }

            // ────────── Persist subscription ──────────
            $subscription = $this->persistSubscriptionRow(
                $validated,
                $planKey,
                $planLabel,
                $amount,
                $recurringAmt,
                $invoiceNumber,
                $transId,
                $authCode,
                $subscriptionId,
                $customerProfileId,
                $customerPaymentProfileId,
                $referralCode,
                $agreementId
            );

            // ────────── Attach the signed payment agreement (instalment plans) ──────────
            if ($agreementId && $subscription) {
                $this->linkAgreement($subscription, $agreementId, $validated['email'], $invoiceNumber);
            }
            session()->forget(['signed_agreement_id', 'signed_agreement_plan']);

            // ────────── Session + cache for onboarding handoff ──────────
            session([
                'acceptjs_payment_success' => true,
                'acceptjs_invoice_number'  => $invoiceNumber,
                'acceptjs_transaction_id'  => $transId,
                'acceptjs_auth_code'       => $authCode,
                'acceptjs_plan_key'        => $planKey,
                'acceptjs_plan_label'      => $planLabel,
                'acceptjs_customer'        => [
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

            Cache::put('checkout_customer_' . $invoiceNumber, [
                'first_name'    => $validated['first_name'],
                'last_name'     => $validated['last_name'],
                'email'         => $validated['email'],
                'phone'         => $validated['phone'],
                'address'       => $validated['address'],
                'city'          => $validated['city'],
                'state'         => $validated['state'],
                'zip'           => $validated['zip'],
                'plan_key'      => $planKey,
                'plan_label'    => $planLabel,
                'amount'        => $amount,
                'referral_code' => $referralCode,
            ], now()->addMinutes(120));

            // Fire Meta CAPI (no-op if not configured)
            $this->fireMetaCapi($invoiceNumber, $transId, $amount, $validated, $request->ip(), $request->userAgent());

            return response()->json([
                'success'     => true,
                'message'     => 'Payment successful.',
                'invoice'     => $invoiceNumber,
                'transaction' => $transId,
                'redirect'    => url('/onboarding'),
            ]);

        } catch (\Throwable $e) {
            Log::error('Accept.js payment exception', [
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

    /**
     * Create CIM customer profile from transaction, then ARB subscription.
     * Returns [customerProfileId, customerPaymentProfileId, subscriptionId, errorOrNull].
     */
    private function createRecurringSubscription(
        string $invoiceNumber,
        string $transId,
        string $email,
        string $planLabel,
        string $recurringAmt,
        ?int $recurringOccurrences,
        string $apiLoginId,
        string $txKey,
        string $endpoint
    ): array {
        // ── 1. CIM profile from the just-completed transaction ─────────────
        $cimPayload = [
            'createCustomerProfileFromTransactionRequest' => [
                'merchantAuthentication' => [
                    'name'           => $apiLoginId,
                    'transactionKey' => $txKey,
                ],
                'transId'  => $transId,
                'customer' => ['email' => $email],
            ],
        ];

        $cimResponse = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json',
        ])->post($endpoint, $cimPayload);

        $cimRaw  = preg_replace('/^\xEF\xBB\xBF/', '', $cimResponse->body());
        $cimData = json_decode(trim($cimRaw), true);

        Log::info('CIM profile response', [
            'invoice'  => $invoiceNumber,
            'response' => $cimData,
        ]);

        $cimResultCode            = data_get($cimData, 'messages.resultCode');
        $customerProfileId        = data_get($cimData, 'customerProfileId');
        $customerPaymentProfileId = data_get($cimData, 'customerPaymentProfileIdList.numericString.0')
            ?? data_get($cimData, 'customerPaymentProfileIdList.0');

        if ($cimResultCode !== 'Ok' || !$customerProfileId || !$customerPaymentProfileId) {
            $errMsg = data_get($cimData, 'messages.message.0.text', 'CIM profile creation failed');
            Log::error('CIM profile creation failed', ['invoice' => $invoiceNumber, 'response' => $cimData]);
            return [null, null, null, $errMsg];
        }

        // ── 2. Wait one second for Authorize.Net to fully commit the profile ─
        sleep(1);

        // ── 3. ARB subscription (retry up to 3x on E00040 propagation lag) ───
        // Fixed-instalment plans bill an exact number of times (deposit was
        // already captured above); open-ended plans bill until cancelled (9999).
        $totalOccurrences = $recurringOccurrences !== null
            ? (string) $recurringOccurrences
            : '9999';

        $arbPayload = [
            'ARBCreateSubscriptionRequest' => [
                'merchantAuthentication' => [
                    'name'           => $apiLoginId,
                    'transactionKey' => $txKey,
                ],
                'refId'        => (string) Str::uuid(),
                'subscription' => [
                    'name'            => mb_substr($planLabel, 0, 50),
                    'paymentSchedule' => [
                        'interval'         => ['length' => '1', 'unit' => 'months'],
                        'startDate'        => now()->addMonth()->format('Y-m-d'),
                        'totalOccurrences' => $totalOccurrences,
                        'trialOccurrences' => '0',
                    ],
                    'amount'      => $recurringAmt,
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
        $maxAttempts    = 3;

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            if ($attempt > 1) {
                sleep(1);
            }

            $arbResponse = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ])->post($endpoint, $arbPayload);

            $arbRaw  = preg_replace('/^\xEF\xBB\xBF/', '', $arbResponse->body());
            $arbData = json_decode(trim($arbRaw), true);

            $arbResultCode  = data_get($arbData, 'messages.resultCode');
            $subscriptionId = data_get($arbData, 'subscriptionId');
            $arbErrorText   = data_get($arbData, 'messages.message.0.text');

            Log::info('ARB response', [
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

    // ─────────────────────────────────────────────────────────────────────────
    // GOOGLE SHEETS — POST each paid order to the Apps Script web-app webhook
    // ─────────────────────────────────────────────────────────────────────────
    private function saveToGoogleSheet(array $data): void
    {
        $url = config('services.google.sheets_webhook_url');

        if (! $url) {
            Log::warning('[Sheets] GOOGLE_SHEETS_WEBHOOK_URL not set — skipping', [
                'invoice' => $data['invoice'],
            ]);
            return;
        }

        try {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => json_encode($data),
                CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
                CURLOPT_TIMEOUT        => 15,
                CURLOPT_FOLLOWLOCATION => true,
            ]);
            $resp   = curl_exec($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            Log::info('[Sheets] Row saved', [
                'invoice'     => $data['invoice'],
                'http_status' => $status,
                'response'    => $resp,
            ]);
        } catch (\Throwable $e) {
            Log::error('[Sheets] Save failed', [
                'invoice' => $data['invoice'],
                'error'   => $e->getMessage(),
            ]);
        }
    }

    private function persistSubscriptionRow(
        array $validated,
        string $planKey,
        string $planLabel,
        string $amount,
        ?string $recurringAmt,
        string $invoiceNumber,
        ?string $transId,
        ?string $authCode,
        ?string $arbSubscriptionId,
        ?string $customerProfileId,
        ?string $customerPaymentProfileId,
        ?string $referralCode,
        ?int $paymentAgreementId = null
    ): ?Subscription {
        try {
            return Subscription::create([
                'first_name'                  => $validated['first_name'],
                'last_name'                   => $validated['last_name'],
                'email'                       => $validated['email'],
                'phone'                       => $validated['phone'],
                'address'                     => $validated['address'],
                'city'                        => $validated['city'],
                'state'                       => $validated['state'],
                'zip'                         => $validated['zip'],
                'plan_key'                    => $planKey,
                'plan_label'                  => $planLabel,
                'amount'                      => $amount,
                'recurring_amount'            => $recurringAmt,
                'invoice_number'              => $invoiceNumber,
                'transaction_id'              => $transId,
                'auth_code'                   => $authCode,
                'arb_subscription_id'         => $arbSubscriptionId,
                'customer_profile_id'         => $customerProfileId,
                'customer_payment_profile_id' => $customerPaymentProfileId,
                'referral_code'               => $referralCode,
                'payment_agreement_id'        => $paymentAgreementId,
                'status'                      => 'active',
                'subscribed_at'               => now(),
                'next_billing_date'           => $recurringAmt !== null ? now()->addMonth() : null,
            ]);
        } catch (\Throwable $dbEx) {
            Log::error('Failed to save subscription to database', [
                'invoice' => $invoiceNumber,
                'error'   => $dbEx->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Cross-reference the pre-checkout signed agreement with the now-created
     * subscription (and stamp the customer email + invoice onto the agreement).
     */
    private function linkAgreement(Subscription $subscription, int $agreementId, string $email, string $invoiceNumber): void
    {
        try {
            $agreement = PaymentAgreement::find($agreementId);
            if ($agreement) {
                $agreement->update([
                    'subscription_id' => $subscription->id,
                    'email'           => $email,
                    'invoice_number'  => $invoiceNumber,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Failed to link payment agreement to subscription', [
                'agreement_id'    => $agreementId,
                'subscription_id' => $subscription->id,
                'error'           => $e->getMessage(),
            ]);
        }
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
                'event_id'      => 'purchase_' . $transId,
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
                    'currency' => 'USD',
                    'value'    => $amount,
                    'order_id' => $invoice,
                ],
            ]],
        ];

        try {
            Http::timeout(10)->post(
                "https://graph.facebook.com/v19.0/{$pixelId}/events?access_token={$capiToken}",
                $eventPayload
            );
        } catch (\Throwable $e) {
            Log::error('Meta CAPI failed', ['invoice' => $invoice, 'error' => $e->getMessage()]);
        }
    }
}
