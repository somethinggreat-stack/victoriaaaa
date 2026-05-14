<?php

namespace App\Http\Controllers;

use App\Models\MentorshipLead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MentorshipController extends Controller
{
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:80'],
            'last_name'  => ['required', 'string', 'max:80'],
            'email'      => ['required', 'email:rfc', 'max:255'],
            'phone'      => ['required', 'string', 'max:30'],
            'situation'  => ['nullable', 'string', 'max:80'],
            'timeline'   => ['nullable', 'string', 'max:80'],
            'hours'      => ['nullable', 'string', 'max:80'],
            'investment' => ['nullable', 'string', 'max:80'],
        ]);

        // Strip phone to digits (drop leading 1 if present)
        $phoneDigits = preg_replace('/\D+/', '', $validated['phone']);
        if (strlen($phoneDigits) === 11 && str_starts_with($phoneDigits, '1')) {
            $phoneDigits = substr($phoneDigits, 1);
        }

        MentorshipLead::create([
            'first_name' => $validated['first_name'],
            'last_name'  => $validated['last_name'],
            'email'      => $validated['email'],
            'phone'      => $phoneDigits,
            'situation'  => $validated['situation']  ?? null,
            'timeline'   => $validated['timeline']   ?? null,
            'hours'      => $validated['hours']      ?? null,
            'investment' => $validated['investment'] ?? null,
            'ip'         => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 512),
        ]);

        Log::info('Mentorship application', ['email' => $validated['email']]);

        try {
            Mail::raw(
                "New mentorship application\n\n"
                . "Name:        {$validated['first_name']} {$validated['last_name']}\n"
                . "Email:       {$validated['email']}\n"
                . "Phone:       {$phoneDigits}\n\n"
                . "Situation:   " . ($validated['situation']  ?? '—') . "\n"
                . "Timeline:    " . ($validated['timeline']   ?? '—') . "\n"
                . "Weekly hrs:  " . ($validated['hours']      ?? '—') . "\n"
                . "Investment:  " . ($validated['investment'] ?? '—') . "\n",
                function ($mail) use ($validated) {
                    $mail->to('info@victoriousopportunities.com')
                         ->replyTo($validated['email'], "{$validated['first_name']} {$validated['last_name']}")
                         ->subject("New mentorship application — {$validated['first_name']} {$validated['last_name']}");
                }
            );
        } catch (\Throwable $e) {
            Log::error('Mentorship mail failed', ['error' => $e->getMessage()]);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['ok' => true]);
        }

        return redirect()->route('mentorship')->with('mentorship_submitted', true);
    }
}
