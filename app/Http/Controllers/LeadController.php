<?php

namespace App\Http\Controllers;

use App\Concerns\SavesToGoogleSheet;
use App\Concerns\SendsToGoHighLevel;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

        return response()->json(['ok' => true, 'id' => $lead->id]);
    }
}
