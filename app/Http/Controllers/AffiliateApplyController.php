<?php

namespace App\Http\Controllers;

use App\Models\AffiliateApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

/** Public affiliate application: read the agreement, accept it, apply. */
class AffiliateApplyController extends Controller
{
    /** Bump when the wording of the agreement changes. */
    public const VERSION = '2026-11-13';

    public function show()
    {
        return view('affiliate-apply', ['ready' => Schema::hasTable('affiliate_applications')]);
    }

    public function store(Request $request)
    {
        abort_unless(Schema::hasTable('affiliate_applications'), 503);

        $data = $request->validate([
            'name'           => ['required', 'string', 'max:150'],
            'email'          => ['required', 'email', 'max:150'],
            'phone'          => ['nullable', 'string', 'max:30'],
            'payout_method'  => ['required', Rule::in(array_keys(AffiliateApplication::METHODS))],
            'payout_details' => ['required', 'string', 'max:200'],
            'agree'          => ['accepted'],
            'website'        => ['nullable', 'max:0'], // honeypot
        ], [
            'agree.accepted' => 'Please check the box to accept the Affiliate Referral Agreement.',
        ]);

        $app = AffiliateApplication::create([
            'name'              => trim($data['name']),
            'email'             => strtolower(trim($data['email'])),
            'phone'             => $data['phone'] ?? null,
            'payout_method'     => $data['payout_method'],
            'payout_details'    => trim($data['payout_details']),
            'status'            => 'pending',
            'agreement_version' => self::VERSION,
            'accepted_at'       => now(),
            'accepted_ip'       => $request->ip(),
        ]);

        try {
            Mail::raw(
                "Hi " . explode(' ', $app->name)[0] . ",\n\n"
                . "Thanks for applying to the Victorious Opportunities Affiliate Referral Program. "
                . "You accepted the agreement on " . $app->accepted_at->format('F j, Y') . ".\n\n"
                . "We'll review your application and, once approved, email you your unique signup link.\n\n"
                . "— Victoria Love\nVictorious Opportunities",
                fn ($m) => $m->to($app->email)->subject('We got your affiliate application'),
            );
        } catch (\Throwable $e) {
            Log::warning('Affiliate application email failed', ['error' => $e->getMessage()]);
        }

        return redirect()->route('affiliate.apply')->with('applied', true);
    }
}
