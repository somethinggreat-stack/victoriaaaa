<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookEvent extends Model
{
    protected $fillable = [
        'notification_id',
        'event_type',
        'entity_id',
        'amount',
        'invoice_number',
        'arb_status',
        'response_code',
        'signature_valid',
        'source_ip',
        'received_at',
        'payload',
        'matched_subscription_id',
        'customer_first_name',
        'customer_last_name',
        'customer_email',
        'description',
    ];

    protected $casts = [
        'amount'          => 'decimal:2',
        'signature_valid' => 'boolean',
        'received_at'     => 'datetime',
        'payload'         => 'array',
    ];

    public static function describeEvent(string $eventType, array $payload, ?string $firstName = null, ?string $lastName = null): string
    {
        $name = trim(($firstName ?? '') . ' ' . ($lastName ?? ''));
        $who  = $name !== '' ? $name : 'Customer';
        $amt  = data_get($payload, 'payload.authAmount');
        $status = data_get($payload, 'payload.status');

        return match ($eventType) {
            'net.authorize.payment.authcapture.created'    => "$who paid" . ($amt !== null ? " \${$amt}" : ''),
            'net.authorize.payment.fraud.declined'         => "$who payment flagged for fraud review",
            'net.authorize.payment.refund.created'         => "Refund issued to $who" . ($amt !== null ? " for \${$amt}" : ''),
            'net.authorize.payment.void.created'           => "Auth voided for $who" . ($amt !== null ? " (\${$amt})" : ''),
            'net.authorize.customer.subscription.created'  => "Subscription created for $who",
            'net.authorize.customer.subscription.updated'  => "Subscription updated for $who" . ($status ? " (status: $status)" : ''),
            'net.authorize.customer.subscription.failed'   => "Recurring payment FAILED for $who",
            'net.authorize.customer.subscription.suspended'=> "Subscription suspended for $who",
            'net.authorize.customer.subscription.cancelled'=> "Subscription cancelled for $who",
            'net.authorize.customer.subscription.expired'  => "Subscription expired for $who",
            'net.authorize.customer.subscription.terminated'=>"Subscription terminated for $who",
            'net.authorize.customer.created'               => "Customer profile created for $who",
            'net.authorize.customer.updated'               => "Customer profile updated for $who",
            'net.authorize.customer.paymentProfile.created'=> "Payment profile saved for $who",
            'net.authorize.customer.paymentProfile.updated'=> "Payment profile updated for $who",
            default                                        => "Event: $eventType",
        };
    }
}
