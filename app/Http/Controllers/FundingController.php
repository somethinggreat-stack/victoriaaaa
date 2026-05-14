<?php

namespace App\Http\Controllers;

use App\Models\FundingApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class FundingController extends Controller
{
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'amount'       => ['required', 'string', 'max:60'],
            'confirmed'    => ['required', 'string', 'max:10'],
            'limits'       => ['required', 'string', 'max:30'],
            'usage'        => ['required', 'string', 'max:40'],
            'fico'         => ['required', 'string', 'max:20'],
            'situation'    => ['required', 'string', 'max:40'],
            'income'       => ['required', 'string', 'max:20'],
            'negatives'    => ['nullable', 'array'],
            'negatives.*'  => ['string', 'max:40'],
            'first_name'   => ['required', 'string', 'max:80'],
            'last_name'    => ['required', 'string', 'max:80'],
            'phone'        => ['required', 'string', 'max:30'],
            'email'        => ['required', 'email:rfc', 'max:255'],
        ]);

        // Normalize phone to digits
        $phoneDigits = preg_replace('/\D+/', '', $validated['phone']);
        if (strlen($phoneDigits) === 11 && str_starts_with($phoneDigits, '1')) {
            $phoneDigits = substr($phoneDigits, 1);
        }

        $negatives = empty($validated['negatives']) ? ['—'] : $validated['negatives'];

        // Persist to DB so it shows up in the admin dashboard
        FundingApplication::create([
            'first_name' => $validated['first_name'],
            'last_name'  => $validated['last_name'],
            'email'      => $validated['email'],
            'phone'      => $phoneDigits,
            'amount'     => $validated['amount'],
            'confirmed'  => $validated['confirmed'],
            'limits'     => $validated['limits'],
            'usage'      => $validated['usage'],
            'fico'       => $validated['fico'],
            'situation'  => $validated['situation'],
            'income'     => $validated['income'],
            'negatives'  => $validated['negatives'] ?? [],
            'ip'         => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 512),
        ]);

        Log::info('Funding application submission', ['email' => $validated['email']]);

        // Email the application — falls back to the configured Mail driver in .env
        try {
            Mail::raw(
                "New funding qualification application\n\n"
                . "Name:        {$validated['first_name']} {$validated['last_name']}\n"
                . "Email:       {$validated['email']}\n"
                . "Phone:       {$phoneDigits}\n\n"
                . "Funding goal:    {$validated['amount']}\n"
                . "Confirmation:    {$validated['confirmed']}\n"
                . "CC limits:       {$validated['limits']}\n"
                . "CC usage:        {$validated['usage']}\n"
                . "FICO:            {$validated['fico']}\n"
                . "Situation:       {$validated['situation']}\n"
                . "Income:          {$validated['income']}\n"
                . "Negative marks:  " . implode(', ', $negatives) . "\n",
                function ($mail) use ($validated) {
                    $mail->to('info@victoriousopportunities.com')
                         ->replyTo($validated['email'], "{$validated['first_name']} {$validated['last_name']}")
                         ->subject("New funding application — {$validated['first_name']} {$validated['last_name']}");
                }
            );
        } catch (\Throwable $e) {
            Log::error('Funding mail failed', ['error' => $e->getMessage()]);
        }

        // TODO: Optionally push to Credit Repair Cloud as a Lead. Uncomment to enable:
        //
        // app(\App\Services\CreditRepairCloud::class)->insertClient([
        //     'type'      => 'Lead',
        //     'firstname' => $validated['first_name'],
        //     'lastname'  => $validated['last_name'],
        //     'email'     => $validated['email'],
        //     'phone'     => $phoneDigits,
        //     'memo'      => "Funding application — goal {$validated['amount']}, FICO {$validated['fico']}, income {$validated['income']}",
        // ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['ok' => true]);
        }

        return redirect()->route('services.diy-business-funding')->with('funding_submitted', true);
    }
}
