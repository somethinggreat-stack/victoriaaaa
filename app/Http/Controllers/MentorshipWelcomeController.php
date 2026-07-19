<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MentorshipWelcomeController extends Controller
{
    /** Plan keys that grant access to the mentorship welcome page. */
    public const MENTORSHIP_PLANS = [
        'mentorship-3pay',
        'mentorship-5pay',
        'mentorship-full',
    ];

    /**
     * Post-purchase welcome page: Skool, the weekly Zoom class and Telegram.
     *
     * Access is granted either by:
     *   1. a just-completed mentorship purchase (session flag), or
     *   2. Victoria's private token link, so she can send it manually.
     */
    public function show(Request $request, ?string $token = null)
    {
        $expected  = (string) config('services.mentorship.welcome_token');
        $viaToken  = $token !== null && $expected !== '' && hash_equals($expected, $token);
        $viaPaid   = (bool) $request->session()->get('mentorship_purchase_success');

        if (! $viaToken && ! $viaPaid) {
            return redirect()->route('mentorship');
        }

        Log::info('Mentorship welcome page opened', [
            'via'  => $viaToken ? 'token' : 'purchase',
            'plan' => $request->session()->get('mentorship_plan_label'),
        ]);

        return view('mentorship-welcome', [
            'firstName'  => $request->session()->get('mentorship_first_name', ''),
            'planLabel'  => $request->session()->get('mentorship_plan_label', ''),
            'skoolUrl'   => config('services.mentorship.skool_url'),
            'telegram'   => config('services.mentorship.telegram_url'),
            'zoomUrl'    => config('services.mentorship.zoom_join_url'),
            'zoomIcs'    => config('services.mentorship.zoom_ics_url'),
            'zoomId'     => config('services.mentorship.zoom_meeting_id'),
            'zoomPass'   => config('services.mentorship.zoom_passcode'),
            'zoomWhen'   => config('services.mentorship.zoom_schedule'),
            'zoomDialIn' => config('services.mentorship.zoom_dial_in'),
        ]);
    }
}
