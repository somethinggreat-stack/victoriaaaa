<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Affiliate;
use App\Models\AffiliateReferral;
use App\Services\Affiliates;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

/** Create affiliates, see who they referred, and track what is owed vs paid. */
class AffiliatesController extends Controller
{
    public function index()
    {
        if (! Schema::hasTable('affiliates')) {
            return view('admin.affiliates', ['needsSetup' => true, 'affiliates' => collect(), 'referrals' => collect(), 'totals' => null]);
        }

        $affiliates = Affiliate::withCount('referrals')
            ->withSum(['referrals as owed_sum' => fn ($q) => $q->where('status', 'owed')], 'amount')
            ->withSum(['referrals as paid_sum' => fn ($q) => $q->where('status', 'paid')], 'amount')
            ->latest()->get();

        return view('admin.affiliates', [
            'needsSetup' => false,
            'affiliates' => $affiliates,
            'referrals'  => AffiliateReferral::with('affiliate')->latest()->limit(100)->get(),
            'totals'     => [
                'owed'  => (float) AffiliateReferral::where('status', 'owed')->sum('amount'),
                'paid'  => (float) AffiliateReferral::where('status', 'paid')->sum('amount'),
                'count' => AffiliateReferral::count(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:150'],
            'email'         => ['nullable', 'email', 'max:150'],
            'phone'         => ['nullable', 'string', 'max:30'],
            'payout_amount' => ['required', 'numeric', 'min:1', 'max:10000'],
            'notes'         => ['nullable', 'string', 'max:500'],
        ]);

        $affiliate = Affiliate::create([
            'name'          => trim($validated['name']),
            'email'         => $validated['email'] ?? null,
            'phone'         => $validated['phone'] ?? null,
            'payout_amount' => number_format((float) $validated['payout_amount'], 2, '.', ''),
            'notes'         => $validated['notes'] ?? null,
            'code'          => Affiliates::newCode($validated['name']),
            'active'        => true,
        ]);

        return back()
            ->with('success', "Affiliate created. Send {$affiliate->name} their link below.")
            ->with('new_affiliate_link', $affiliate->link);
    }

    public function toggle(Affiliate $affiliate)
    {
        $affiliate->update(['active' => ! $affiliate->active]);

        return back()->with('success', $affiliate->active ? 'Affiliate link switched on.' : 'Affiliate link switched off — new clients will not be credited.');
    }

    public function markPaid(AffiliateReferral $referral)
    {
        if ($referral->status === 'owed') {
            $referral->update(['status' => 'paid', 'paid_at' => now()]);
        }

        return back()->with('success', 'Marked as paid.');
    }

    /** Pay out everything an affiliate is owed in one click. */
    public function markAllPaid(Affiliate $affiliate)
    {
        $n = $affiliate->referrals()->where('status', 'owed')->update(['status' => 'paid', 'paid_at' => now()]);

        return back()->with('success', $n . ' referral' . ($n === 1 ? '' : 's') . ' marked as paid.');
    }
}
