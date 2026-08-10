<?php

namespace App\Http\Controllers;

use App\Models\StrategyCallRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class StrategyCallController extends Controller
{
    public function show()
    {
        return view('strategy-call');
    }

    public function submit(Request $request)
    {
        // Simple "request a free 15-min phone call": name + phone + when to call.
        $validated = $request->validate([
            'name'           => ['required', 'string', 'max:120'],
            'email'          => ['required', 'email:rfc', 'max:255'],
            'phone'          => ['required', 'string', 'max:30'],
            'preferred_day'  => ['required', 'string', 'max:40'],
            'preferred_time' => ['required', 'string', 'max:40'],
            'goal'           => ['nullable', 'string', 'max:2000'],
        ]);

        // Store the requested call time in the existing best_time column.
        $when = trim($validated['preferred_day'] . ' at ' . $validated['preferred_time']);

        $row = StrategyCallRequest::create([
            'name'             => $validated['name'],
            'email'            => $validated['email'],
            'phone'            => $validated['phone'],
            'best_time'        => $when,
            'goal'             => $validated['goal'] ?? null,
            'will_bring_login' => false,
            'showup_confirmed' => false,
            'status'           => 'new',
            'ip'               => $request->ip(),
            'user_agent'       => substr((string) $request->userAgent(), 0, 512),
        ]);

        Log::info('Free phone call request', ['id' => $row->id, 'email' => $row->email, 'when' => $when]);

        // Notify Victoria so she can call them at the requested time.
        try {
            $body = "NEW FREE CALL REQUEST — call this person\n\n"
                . "Name:        {$row->name}\n"
                . "Phone:       {$row->phone}\n"
                . "Email:       {$row->email}\n"
                . "Call them:   {$when}\n\n"
                . "What they need:\n" . ($row->goal ?: '—') . "\n";

            Mail::raw($body, function ($mail) use ($row, $when) {
                $mail->to('support@victorialovecredit.com')
                     ->replyTo($row->email, $row->name)
                     ->subject('📞 Free call request — ' . $row->name . ' (' . $when . ')');
            });
        } catch (\Throwable $e) {
            Log::error('Free call request mail failed', ['error' => $e->getMessage()]);
        }

        return redirect()
            ->route('strategy-call.booked')
            ->with('booked_strategy_call', true)
            ->with('lead_name', $row->name)
            ->with('call_when', $when);
    }

    public function booked(Request $request)
    {
        if (! $request->session()->get('booked_strategy_call')) {
            return redirect()->route('strategy-call.show');
        }
        return view('strategy-call-booked', [
            'leadName' => $request->session()->get('lead_name', ''),
            'callWhen' => $request->session()->get('call_when', ''),
        ]);
    }
}
