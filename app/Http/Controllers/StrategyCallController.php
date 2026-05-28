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
        $validated = $request->validate([
            'name'                 => ['required', 'string', 'max:120'],
            'email'                => ['required', 'email:rfc', 'max:255'],
            'phone'                => ['required', 'string', 'max:30'],
            'best_time'            => ['nullable', 'string', 'max:120'],
            'situation'            => ['nullable', 'string', 'max:60'],
            'score'                => ['nullable', 'string', 'max:60'],
            'timeline'             => ['nullable', 'string', 'max:60'],
            'goal'                 => ['nullable', 'string', 'max:2000'],
            'prior_repair'         => ['nullable', 'in:yes,no'],
            'prior_repair_notes'   => ['nullable', 'string', 'max:2000'],
            'monitoring_service'   => ['nullable', 'string', 'max:80'],
            'monitoring_username'  => ['nullable', 'string', 'max:120'],
            'investment_range'     => ['nullable', 'string', 'max:60'],
            'will_bring_login'     => ['accepted'],
            'showup_confirmed'     => ['accepted'],
        ], [
            'will_bring_login.accepted' => 'Please confirm you will bring your monitoring login to the call.',
            'showup_confirmed.accepted' => 'Please confirm you will show up for the call.',
        ]);

        $row = StrategyCallRequest::create([
            'name'                 => $validated['name'],
            'email'                => $validated['email'],
            'phone'                => $validated['phone'],
            'best_time'            => $validated['best_time']           ?? null,
            'situation'            => $validated['situation']           ?? null,
            'score'                => $validated['score']               ?? null,
            'timeline'             => $validated['timeline']            ?? null,
            'goal'                 => $validated['goal']                ?? null,
            'prior_repair'         => ($validated['prior_repair'] ?? 'no') === 'yes',
            'prior_repair_notes'   => $validated['prior_repair_notes']  ?? null,
            'monitoring_service'   => $validated['monitoring_service']  ?? null,
            'monitoring_username'  => $validated['monitoring_username'] ?? null,
            'will_bring_login'     => true,
            'investment_range'     => $validated['investment_range']    ?? null,
            'showup_confirmed'     => true,
            'status'               => 'new',
            'ip'                   => $request->ip(),
            'user_agent'           => substr((string) $request->userAgent(), 0, 512),
        ]);

        Log::info('Strategy call request', ['id' => $row->id, 'email' => $row->email]);

        try {
            $body = "New strategy-call qualification — book follow-up\n\n"
                . "Name:              {$row->name}\n"
                . "Email:             {$row->email}\n"
                . "Phone:             {$row->phone}\n"
                . "Best time to call: " . ($row->best_time ?? '—') . "\n\n"
                . "Situation:         " . ($row->situation ?? '—') . "\n"
                . "Score band:        " . ($row->score ?? '—') . "\n"
                . "Timeline:          " . ($row->timeline ?? '—') . "\n"
                . "Investment range:  " . ($row->investment_range ?? '—') . "\n"
                . "Prior repair:      " . ($row->prior_repair ? 'Yes' : 'No') . "\n"
                . "Prior notes:       " . ($row->prior_repair_notes ?? '—') . "\n\n"
                . "Monitoring svc:    " . ($row->monitoring_service ?? '—') . "\n"
                . "Monitoring user:   " . ($row->monitoring_username ?? '—') . "\n"
                . "Will bring login:  " . ($row->will_bring_login ? 'Yes' : 'No') . "\n"
                . "Show-up commit:    " . ($row->showup_confirmed ? 'Yes' : 'No') . "\n\n"
                . "Goal (90 days):\n" . ($row->goal ?: '—') . "\n";

            Mail::raw($body, function ($mail) use ($row) {
                $mail->to('support@victorialovecredit.com')
                     ->replyTo($row->email, $row->name)
                     ->subject('Strategy call request — ' . $row->name);
            });
        } catch (\Throwable $e) {
            Log::error('Strategy call mail failed', ['error' => $e->getMessage()]);
        }

        $calendlyUrl = (string) config('services.calendly.url');
        $isConfigured = $calendlyUrl !== ''
            && $calendlyUrl !== 'https://calendly.com/your-handle/15min'
            && filter_var($calendlyUrl, FILTER_VALIDATE_URL);

        if ($isConfigured) {
            $url = $calendlyUrl
                . (str_contains($calendlyUrl, '?') ? '&' : '?')
                . http_build_query([
                    'name'          => $row->name,
                    'email'         => $row->email,
                    'a1'            => $row->phone,
                ]);
            return redirect()->away($url);
        }

        return redirect()
            ->route('strategy-call.booked')
            ->with('booked_strategy_call', true)
            ->with('lead_name', $row->name);
    }

    public function booked(Request $request)
    {
        if (! $request->session()->get('booked_strategy_call')) {
            return redirect()->route('strategy-call.show');
        }
        return view('strategy-call-booked', [
            'leadName'    => $request->session()->get('lead_name', ''),
            'calendlyUrl' => config('services.calendly.url'),
        ]);
    }
}
