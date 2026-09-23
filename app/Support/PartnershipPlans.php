<?php

namespace App\Support;

/**
 * The partnership price list.
 *
 * Two prices run at once — $100 for Burgundy's legacy book and $149 for new
 * signups — so nothing may assume a single figure. Anywhere that needs to know
 * what a given client actually pays should read it from their subscription,
 * not from here: this is only the menu, not the receipt.
 */
class PartnershipPlans
{
    /** @return array<string, array> keyed by tier name (legacy | new) */
    public static function all(): array
    {
        return (array) config('partnership.plans', []);
    }

    /** Look a tier up by its name, e.g. 'legacy'. */
    public static function tier(?string $tier): array
    {
        $plans = self::all();
        $tier  = $tier ?: (string) config('partnership.default_plan', 'legacy');

        return $plans[$tier] ?? reset($plans) ?: [];
    }

    /** Look a plan up by the key stamped on a subscription, e.g. 'burgundy-149'. */
    public static function byKey(?string $key): ?array
    {
        foreach (self::all() as $plan) {
            if (($plan['key'] ?? null) === $key) {
                return $plan;
            }
        }

        return null;
    }

    /** Every plan key, for scoping subscription queries to the partnership. */
    public static function keys(): array
    {
        return array_values(array_filter(array_map(
            fn ($p) => $p['key'] ?? null,
            self::all(),
        )));
    }

    public static function label(?string $key): string
    {
        return self::byKey($key)['label'] ?? 'Credit Restoration Program';
    }

    /** Human name for a tier, used in the dashboard so links can't be mixed up. */
    public static function audience(?string $key): string
    {
        return self::byKey($key)['audience'] ?? '—';
    }
}
