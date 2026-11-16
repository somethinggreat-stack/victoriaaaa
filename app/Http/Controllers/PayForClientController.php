<?php

namespace App\Http\Controllers;

use App\Http\Controllers\AcceptJsPaymentController as Checkout;
use App\Models\Affiliate;
use App\Models\PaymentAgreement;
use App\Models\PaymentLink;
use App\Services\ServiceAgreements;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * An affiliate (like Mija) pays a client's whole fee up front, at the normal
 * price. She types the client's details, picks the plan and pays; the client
 * then gets their own contract and onboarding link. She still earns the
 * affiliate fee, because the client came from her.
 */
class PayForClientController extends Controller
{
    /** The pay-in-full plans an affiliate can buy, keyed by checkout plan key. */
    public static function plans(): array
    {
        $out = [];
        foreach (['standard', 'fast-track', 'couples-fast-track'] as $key) {
            $p = Checkout::PLANS[$key];
            $out[$key] = ['label' => $p['label'], 'total' => (float) $p['total']];
        }

        return $out;
    }

    private function affiliate(string $code): Affiliate
    {
        abort_unless(
            Schema::hasTable('affiliates') && Schema::hasColumn('payment_links', 'affiliate_id'),
            404,
        );

        return Affiliate::where('code', strtoupper($code))->where('active', true)->firstOrFail();
    }

    public function show(string $code)
    {
        return view('pay-for-client', ['affiliate' => $this->affiliate($code), 'plans' => self::plans()]);
    }

    public function store(Request $request, string $code)
    {
        $affiliate = $this->affiliate($code);
        $plans     = self::plans();

        $data = $request->validate([
            'plan'       => ['required', 'in:' . implode(',', array_keys($plans))],
            'first_name' => ['required', 'string', 'max:80'],
            'last_name'  => ['required', 'string', 'max:80'],
            'email'      => ['required', 'email', 'max:150'],
            'phone'      => ['nullable', 'string', 'max:30'],
        ]);

        $plan = $plans[$data['plan']];

        do {
            $token = 'pl_' . Str::random(28);
        } while (PaymentLink::where('token', $token)->exists());

        $link = PaymentLink::create([
            'token'               => $token,
            'client_name'         => trim($data['first_name'] . ' ' . $data['last_name']),
            'email'               => strtolower(trim($data['email'])),
            'amount'              => number_format($plan['total'], 2, '.', ''),
            'note'                => "{$plan['label']} — paid in full by {$affiliate->name}",
            'service_description' => "{$plan['label']} — paid in full",
            'status'              => 'unpaid',
            'affiliate_id'        => $affiliate->id,
        ]);

        return redirect()->route('pay.show', $link->token);
    }

    /** Shown to the affiliate after paying: the client's contract is on its way. */
    public function done(PaymentAgreement $agreement)
    {
        return view('pay-for-client-done', [
            'agreement' => $agreement,
            'signUrl'   => ServiceAgreements::signingUrl($agreement, 30),
        ]);
    }
}
