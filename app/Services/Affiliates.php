<?php

namespace App\Services;

use App\Models\Affiliate;
use App\Models\AffiliateReferral;
use App\Models\PaymentLink;
use App\Models\Subscription;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Credits an affiliate when someone they referred pays.
 *
 * Never allowed to break a checkout: the card has already been charged by the
 * time this runs, so every failure is logged and swallowed.
 */
class Affiliates
{
    /** Plans an affiliate is NOT paid for (entry-level / mentorship products). */
    private const EXCLUDED_PREFIXES = ['mentorship', 'starter', 'audit'];

    public static function record(?string $code, ?Subscription $subscription): ?AffiliateReferral
    {
        try {
            $code = strtoupper(trim((string) $code));
            if ($code === '' || ! $subscription || ! Schema::hasTable('affiliates')) {
                return null;
            }

            foreach (self::EXCLUDED_PREFIXES as $prefix) {
                if (str_starts_with((string) $subscription->plan_key, $prefix)) {
                    return null;
                }
            }

            $affiliate = Affiliate::where('code', $code)->where('active', true)->first();
            if (! $affiliate) {
                return null;
            }

            // One referral per sale, even if this is called twice.
            return AffiliateReferral::firstOrCreate(
                ['subscription_id' => $subscription->id],
                [
                    'affiliate_id' => $affiliate->id,
                    'client_name'  => trim($subscription->first_name . ' ' . $subscription->last_name),
                    'client_email' => $subscription->email,
                    'plan_label'   => $subscription->plan_label,
                    'amount'       => $affiliate->payout_amount,
                    'status'       => 'owed',
                ],
            );
        } catch (\Throwable $e) {
            Log::error('[Affiliate] Could not record referral', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Credit the affiliate who paid a client's fee through a pay-in-full link.
     * One referral per link, however many times this runs.
     */
    public static function recordForLink(PaymentLink $link): ?AffiliateReferral
    {
        try {
            if (! $link->affiliate_id || $link->status !== 'paid' || ! Schema::hasColumn('affiliate_referrals', 'payment_link_id')) {
                return null;
            }

            $affiliate = Affiliate::where('id', $link->affiliate_id)->where('active', true)->first();
            if (! $affiliate) {
                return null;
            }

            return AffiliateReferral::firstOrCreate(
                ['payment_link_id' => $link->id],
                [
                    'affiliate_id' => $affiliate->id,
                    'client_name'  => $link->client_name,
                    'client_email' => $link->email,
                    'plan_label'   => $link->service_description,
                    'amount'       => $affiliate->payout_amount,
                    'status'       => 'owed',
                ],
            );
        } catch (\Throwable $e) {
            Log::error('[Affiliate] Could not record pay-for-client referral', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /** A short, readable, unique code. */
    public static function newCode(string $name): string
    {
        $base = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', Str::ascii($name)), 0, 6)) ?: 'REF';

        do {
            $code = $base . random_int(10, 99);
        } while (Affiliate::where('code', $code)->exists());

        return $code;
    }
}
