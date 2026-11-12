<?php

namespace App\Http\Controllers;

use App\Concerns\SavesToGoogleSheet;
use App\Concerns\SendsToGoHighLevel;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class LeadController extends Controller
{
    use SavesToGoogleSheet;
    use SendsToGoHighLevel;

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name'   => ['nullable', 'string', 'max:120'],
            'email'  => ['required', 'email:rfc', 'max:255'],
            'phone'  => ['nullable', 'string', 'max:30'],
            'score'  => ['nullable', 'string', 'max:60'],
            'issue'  => ['nullable', 'string', 'max:60'],
            'goal'   => ['nullable', 'string', 'max:60'],
            'source' => ['nullable', 'string', 'max:30'],
        ]);

        $lead = Lead::create($validated + [
            'source'     => $validated['source'] ?? 'popup',
            'ip'         => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 512),
        ]);

        Log::info('Lead captured', ['id' => $lead->id, 'email' => $lead->email]);

        $leadData = [
            'type'         => 'lead',
            'submitted_at' => now()->toDateTimeString(),
            'name'         => $validated['name'] ?? '',
            'email'        => $validated['email'],
            'phone'        => $validated['phone'] ?? '',
            'score'        => $validated['score'] ?? '',
            'issue'        => $validated['issue'] ?? '',
            'goal'         => $validated['goal'] ?? '',
            'source'       => $lead->source,
            'ip_address'   => $request->ip(),
        ];
        $this->saveToGoogleSheet($leadData);
        $this->sendToGoHighLevel($leadData);

        // Instant confirmation so nobody wonders whether their form went through.
        try {
            $first = trim(explode(' ', trim((string) ($validated['name'] ?? '')))[0] ?? '');
            Mail::raw(
                'Hi' . ($first !== '' ? " {$first}" : '') . ",\n\n"
                . "Thanks for reaching out — I got your information and a member of my team will contact you shortly.\n\n"
                . "If you're ready to start now, you can see the plans and begin today: " . url('/') . "#pricing\n\n"
                . "Questions? Call or text (469) 349-5553 (Mon–Fri, 10 AM – 5 PM Central) or reply to this email.\n\n"
                . "— Victoria Love\nVictorious Opportunities",
                fn ($m) => $m->to($validated['email'])->subject('We got your info — Victoria Love'),
            );
        } catch (\Throwable $e) {
            Log::warning('Lead auto-reply failed', ['email' => $validated['email'], 'error' => $e->getMessage()]);
        }

        return response()->json(['ok' => true, 'id' => $lead->id]);
    }
}
