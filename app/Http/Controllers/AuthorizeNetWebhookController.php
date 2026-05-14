<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Subscription;
use App\Models\SubscriptionEvent;
use App\Models\WebhookEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AuthorizeNetWebhookController extends Controller
{
    private const EVT_PAYMENT_SUCCESS  = 'net.authorize.payment.authcapture.created';
    private const EVT_PAYMENT_FRAUD    = 'net.authorize.payment.fraud.declined';
    private const EVT_PAYMENT_REFUND   = 'net.authorize.payment.refund.created';
    private const EVT_PAYMENT_VOID     = 'net.authorize.payment.void.created';

    private const EVT_SUB_CREATED      = 'net.authorize.customer.subscription.created';
    private const EVT_SUB_UPDATED      = 'net.authorize.customer.subscription.updated';
    private const EVT_SUB_FAILED       = 'net.authorize.customer.subscription.failed';
    private const EVT_SUB_SUSPENDED    = 'net.authorize.customer.subscription.suspended';
    private const EVT_SUB_CANCELLED    = 'net.authorize.customer.subscription.cancelled';
    private const EVT_SUB_EXPIRED      = 'net.authorize.customer.subscription.expired';
    private const EVT_SUB_TERMINATED   = 'net.authorize.customer.subscription.terminated';

    private const EVT_CUSTOMER_CREATED        = 'net.authorize.customer.created';
    private const EVT_CUSTOMER_UPDATED        = 'net.authorize.customer.updated';
    private const EVT_PAYMENT_PROFILE_CREATED = 'net.authorize.customer.paymentProfile.created';
    private const EVT_PAYMENT_PROFILE_UPDATED = 'net.authorize.customer.paymentProfile.updated';

    private const FAILURE_EVENTS = [
        self::EVT_SUB_FAILED,
        self::EVT_SUB_SUSPENDED,
    ];

    private const TERMINATION_EVENTS = [
        self::EVT_SUB_CANCELLED,
        self::EVT_SUB_EXPIRED,
        self::EVT_SUB_TERMINATED,
    ];

    private const INFORMATIONAL_EVENTS = [
        self::EVT_PAYMENT_FRAUD,
        self::EVT_SUB_CREATED,
        self::EVT_CUSTOMER_CREATED,
        self::EVT_CUSTOMER_UPDATED,
        self::EVT_PAYMENT_PROFILE_CREATED,
        self::EVT_PAYMENT_PROFILE_UPDATED,
    ];

    public function handle(Request $request)
    {
        $sigResult  = $this->verifySignature($request);
        $enforceSig = (bool) config('services.authorize_net.webhook_enforce_signature', false);

        if ($sigResult === false) {
            Log::warning('Authorize.Net webhook signature INVALID', [
                'ip'      => $request->ip(),
                'enforce' => $enforceSig,
            ]);
            if ($enforceSig) {
                return response('Invalid signature', 401);
            }
        } elseif ($sigResult === true) {
            Log::info('Authorize.Net webhook signature OK');
        }

        $payload = json_decode($request->getContent(), true) ?? [];

        Log::info('Authorize.Net webhook received', ['payload' => $payload]);

        $eventType      = $payload['eventType'] ?? null;
        $notificationId = $payload['notificationId'] ?? null;
        $entityId       = $payload['payload']['id'] ?? null;
        $amount         = $payload['payload']['authAmount'] ?? null;
        $invoiceNumber  = $payload['payload']['invoiceNumber'] ?? null;
        $arbStatus      = $payload['payload']['status'] ?? null;

        $webhookEvent = $this->persistWebhookEvent([
            'notification_id' => $notificationId,
            'event_type'      => (string) ($eventType ?? 'unknown'),
            'entity_id'       => $entityId ? (string) $entityId : null,
            'amount'          => is_numeric($amount) ? (float) $amount : null,
            'invoice_number'  => $invoiceNumber,
            'arb_status'      => $arbStatus,
            'signature_valid' => $sigResult,
            'source_ip'       => $request->ip(),
            'received_at'     => now(),
            'payload'         => $payload,
        ]);

        // Idempotency: dedupe by notificationId (24h window)
        if ($notificationId) {
            $dedupeKey = 'authnet_notif_' . $notificationId;
            if (Cache::has($dedupeKey)) {
                Log::info('Duplicate webhook notification — skipping', [
                    'notification_id' => $notificationId,
                    'event_type'      => $eventType,
                ]);
                return response('Duplicate', 200);
            }
            Cache::put($dedupeKey, true, now()->addHours(24));
        }

        if ($eventType === self::EVT_PAYMENT_SUCCESS) {
            return $this->handlePaymentSuccess($invoiceNumber, $entityId, $amount, $payload, $webhookEvent);
        }

        if (in_array($eventType, self::FAILURE_EVENTS, true)) {
            return $this->handleSubscriptionFailure($eventType, $entityId, $payload);
        }

        if (in_array($eventType, self::TERMINATION_EVENTS, true)) {
            return $this->handleSubscriptionTermination($eventType, $entityId, $payload);
        }

        if ($eventType === self::EVT_SUB_UPDATED) {
            return $this->handleSubscriptionUpdate($entityId, $arbStatus, $payload);
        }

        if ($eventType === self::EVT_PAYMENT_REFUND || $eventType === self::EVT_PAYMENT_VOID) {
            return $this->handleRefundOrVoid($eventType, $entityId, $amount, $invoiceNumber, $payload, $webhookEvent);
        }

        if (in_array($eventType, self::INFORMATIONAL_EVENTS, true)) {
            Log::info('Informational webhook acknowledged', ['event_type' => $eventType]);
            return response('Event received', 200);
        }

        Log::warning('UNHANDLED webhook event type', ['event_type' => $eventType]);
        return response('Event received', 200);
    }

    private function handlePaymentSuccess(?string $invoiceNumber, ?string $transactionId, $amount, array $payload, ?WebhookEvent $webhookEvent = null)
    {
        if (!$invoiceNumber) {
            Log::warning('authcapture without invoice number, ignoring');
            return response('Event received', 200);
        }

        Cache::put('paid_invoice_' . $invoiceNumber, true, now()->addMinutes(30));

        $cachedCustomer = Cache::get('checkout_customer_' . $invoiceNumber);

        if ($cachedCustomer) {
            $this->persistPayment([
                'subscription_id' => Subscription::where('invoice_number', $invoiceNumber)->value('id'),
                'transaction_id'  => $transactionId,
                'invoice_number'  => $invoiceNumber,
                'amount'          => (float) ($amount ?? 0),
                'type'            => 'initial',
                'status'          => 'captured',
                'event_type_raw'  => self::EVT_PAYMENT_SUCCESS,
                'charged_at'      => now(),
                'raw_payload'     => $payload,
            ]);

            return response('Payment confirmed', 200);
        }

        // Recurring charge (no cached customer)
        $subscription = Subscription::where('invoice_number', $invoiceNumber)->first();
        if (!$subscription && $transactionId) {
            $subscription = Subscription::where('transaction_id', $transactionId)->first();
        }

        if (!$subscription) {
            Log::info('Recurring charge: no local subscription match', [
                'invoice_number' => $invoiceNumber,
                'transaction_id' => $transactionId,
            ]);

            $this->persistPayment([
                'subscription_id' => null,
                'transaction_id'  => $transactionId,
                'invoice_number'  => $invoiceNumber,
                'amount'          => (float) ($amount ?? 0),
                'type'            => 'recurring',
                'status'          => 'captured',
                'event_type_raw'  => self::EVT_PAYMENT_SUCCESS,
                'charged_at'      => now(),
                'raw_payload'     => $payload,
            ]);

            return response('Event received', 200);
        }

        $this->persistPayment([
            'subscription_id' => $subscription->id,
            'transaction_id'  => $transactionId,
            'invoice_number'  => $invoiceNumber,
            'amount'          => (float) ($amount ?? 0),
            'type'            => 'recurring',
            'status'          => 'captured',
            'event_type_raw'  => self::EVT_PAYMENT_SUCCESS,
            'charged_at'      => now(),
            'raw_payload'     => $payload,
        ]);

        SubscriptionEvent::create([
            'subscription_id' => $subscription->id,
            'event_type'      => 'payment_recovered',
            'payload'         => $payload,
            'note'            => sprintf('Recurring payment successful. Invoice %s, Txn %s, Amount $%s', $invoiceNumber, $transactionId, $amount),
        ]);

        if ($subscription->status === 'past_due') {
            $subscription->update([
                'status'               => 'active',
                'failed_payment_count' => 0,
                'first_failed_at'      => null,
                'grace_period_ends_at' => null,
            ]);
        }

        return response('Event received', 200);
    }

    private function handleSubscriptionFailure(string $eventType, ?string $arbId, array $payload)
    {
        if (!$arbId) {
            return response('Event received', 200);
        }

        $subscription = Subscription::where('arb_subscription_id', $arbId)->first();
        if (!$subscription || $subscription->status === 'terminated') {
            return response('Event received', 200);
        }

        $failedCount = $subscription->failed_payment_count + 1;
        $isFirstFail = $subscription->first_failed_at === null;

        $updates = [
            'status'               => 'past_due',
            'failed_payment_count' => $failedCount,
        ];
        if ($isFirstFail) {
            $updates['first_failed_at']      = now();
            $updates['grace_period_ends_at'] = now()->addDays(7);
        }
        $subscription->update($updates);
        $subscription->refresh();

        SubscriptionEvent::create([
            'subscription_id' => $subscription->id,
            'event_type'      => 'payment_failed',
            'payload'         => $payload,
            'note'            => 'Auth.net event: ' . $eventType . '. Failure #' . $failedCount,
        ]);

        return response('Event received', 200);
    }

    private function handleSubscriptionTermination(string $eventType, ?string $arbId, array $payload)
    {
        if (!$arbId) {
            return response('Event received', 200);
        }

        $subscription = Subscription::where('arb_subscription_id', $arbId)->first();
        if (!$subscription || $subscription->status === 'terminated') {
            return response('Event received', 200);
        }

        $subscription->update([
            'status'        => 'terminated',
            'terminated_at' => now(),
        ]);

        SubscriptionEvent::create([
            'subscription_id' => $subscription->id,
            'event_type'      => 'terminated',
            'payload'         => $payload,
            'note'            => 'Auth.net event: ' . $eventType,
        ]);

        return response('Event received', 200);
    }

    private function handleSubscriptionUpdate(?string $arbId, ?string $arbStatus, array $payload)
    {
        if (!$arbId || !$arbStatus) {
            return response('Event received', 200);
        }

        $subscription = Subscription::where('arb_subscription_id', $arbId)->first();
        if (!$subscription) {
            return response('Event received', 200);
        }

        if ($arbStatus === 'active' && $subscription->status === 'past_due') {
            $subscription->update([
                'status'               => 'active',
                'failed_payment_count' => 0,
                'first_failed_at'      => null,
                'grace_period_ends_at' => null,
            ]);

            SubscriptionEvent::create([
                'subscription_id' => $subscription->id,
                'event_type'      => 'payment_recovered',
                'payload'         => $payload,
                'note'            => 'Auth.net subscription.updated with status=active',
            ]);
        }

        return response('Event received', 200);
    }

    private function handleRefundOrVoid(string $eventType, ?string $transactionId, $amount, ?string $invoiceNumber, array $payload, ?WebhookEvent $webhookEvent = null)
    {
        $type   = $eventType === self::EVT_PAYMENT_REFUND ? 'refund' : 'void';
        $status = $eventType === self::EVT_PAYMENT_REFUND ? 'refunded' : 'voided';

        $subscription = null;
        if ($invoiceNumber) {
            $subscription = Subscription::where('invoice_number', $invoiceNumber)->first();
        }
        if (!$subscription && $transactionId) {
            $priorPayment = Payment::where('transaction_id', $transactionId)
                ->whereIn('type', ['initial', 'recurring'])
                ->first();
            if ($priorPayment) {
                $subscription = $priorPayment->subscription;
            }
        }

        $this->persistPayment([
            'subscription_id' => optional($subscription)->id,
            'transaction_id'  => $transactionId,
            'invoice_number'  => $invoiceNumber,
            'amount'          => abs((float) ($amount ?? 0)),
            'type'            => $type,
            'status'          => $status,
            'event_type_raw'  => $eventType,
            'charged_at'      => now(),
            'raw_payload'     => $payload,
        ]);

        if ($subscription) {
            SubscriptionEvent::create([
                'subscription_id' => $subscription->id,
                'event_type'      => 'manual_note',
                'payload'         => $payload,
                'note'            => sprintf('%s recorded. Txn %s, Amount $%s', ucfirst($type), $transactionId ?? 'unknown', $amount ?? '0'),
            ]);
        }

        return response('Event received', 200);
    }

    private function persistWebhookEvent(array $attrs): ?WebhookEvent
    {
        try {
            $matchedSub = null;
            if (!empty($attrs['entity_id'])) {
                $matchedSub = Subscription::where('arb_subscription_id', $attrs['entity_id'])->first();
            }
            if (!$matchedSub && !empty($attrs['invoice_number'])) {
                $matchedSub = Subscription::where('invoice_number', $attrs['invoice_number'])->first();
            }

            $attrs['matched_subscription_id'] = $matchedSub?->id;
            $attrs['customer_first_name']     = $matchedSub?->first_name;
            $attrs['customer_last_name']      = $matchedSub?->last_name;
            $attrs['customer_email']          = $matchedSub?->email;

            $payload = $attrs['payload'] ?? [];
            $rc = data_get($payload, 'payload.responseCode');
            if ($rc !== null && $rc !== '') {
                $attrs['response_code'] = (string) $rc;
            }

            $attrs['description'] = WebhookEvent::describeEvent(
                $attrs['event_type'] ?? 'unknown',
                is_array($payload) ? $payload : [],
                $matchedSub?->first_name,
                $matchedSub?->last_name
            );

            if (!empty($attrs['notification_id'])) {
                return WebhookEvent::updateOrCreate(
                    ['notification_id' => $attrs['notification_id']],
                    $attrs
                );
            }
            return WebhookEvent::create($attrs);
        } catch (\Throwable $e) {
            Log::error('Failed to persist webhook_events row', [
                'error'           => $e->getMessage(),
                'event_type'      => $attrs['event_type'] ?? null,
                'notification_id' => $attrs['notification_id'] ?? null,
            ]);
            return null;
        }
    }

    private function persistPayment(array $attrs): ?Payment
    {
        try {
            if (!empty($attrs['transaction_id'])) {
                return Payment::updateOrCreate(
                    [
                        'transaction_id' => $attrs['transaction_id'],
                        'type'           => $attrs['type'],
                    ],
                    $attrs
                );
            }
            return Payment::create($attrs);
        } catch (\Throwable $e) {
            Log::error('Failed to persist payments row', [
                'error'          => $e->getMessage(),
                'transaction_id' => $attrs['transaction_id'] ?? null,
                'type'           => $attrs['type'] ?? null,
            ]);
            return null;
        }
    }

    private function verifySignature(Request $request): ?bool
    {
        $signatureKey = (string) config('services.authorize_net.signature_key', '');
        if ($signatureKey === '') {
            return null;
        }

        $header = (string) $request->header('X-ANET-Signature', '');
        if ($header === '') {
            return null;
        }

        $parts = explode('=', $header, 2);
        if (count($parts) !== 2 || strtolower($parts[0]) !== 'sha512' || $parts[1] === '') {
            return false;
        }

        $provided = strtoupper($parts[1]);
        $expected = strtoupper(hash_hmac('sha512', (string) $request->getContent(), $signatureKey));

        return hash_equals($expected, $provided);
    }
}
