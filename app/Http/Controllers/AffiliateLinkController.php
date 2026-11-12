<?php

namespace App\Http\Controllers;

use App\Models\Affiliate;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Schema;

/**
 * /r/{code} — what an affiliate shares. Remembers who sent the visitor (session
 * plus a 30-day cookie) and drops them on the pricing section.
 */
class AffiliateLinkController extends Controller
{
    public function __invoke(string $code)
    {
        $code = strtoupper(trim($code));

        if (Schema::hasTable('affiliates') && Affiliate::where('code', $code)->where('active', true)->exists()) {
            session(['referral_code' => $code]);
            Cookie::queue('vl_ref', $code, 60 * 24 * 30);
        }

        return redirect(url('/') . '#pricing');
    }
}
