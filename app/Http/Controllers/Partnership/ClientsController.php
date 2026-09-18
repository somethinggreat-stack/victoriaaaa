<?php

namespace App\Http\Controllers\Partnership;

use App\Http\Controllers\Controller;
use App\Models\BurgundyClient;
use Illuminate\Http\Request;

class ClientsController extends Controller
{
    public function index(Request $request)
    {
        $query = BurgundyClient::with('subscription');

        if ($search = trim((string) $request->query('q', ''))) {
            $like = '%' . $search . '%';
            $digits = preg_replace('/\D+/', '', $search);

            $query->where(function ($q) use ($like, $digits) {
                $q->where('first_name', 'like', $like)
                  ->orWhere('last_name', 'like', $like)
                  ->orWhere('email', 'like', $like);

                if ($digits !== '') {
                    // Phones are stored as typed — (817) 917-2091 — so a digit
                    // search has to ignore the punctuation in between.
                    $q->orWhereRaw(
                        "REPLACE(REPLACE(REPLACE(REPLACE(phone,'(',''),')',''),'-',''),' ','') LIKE ?",
                        ['%' . $digits . '%'],
                    );
                }
            });
        }

        if ($stage = $request->query('stage')) {
            $this->applyStageFilter($query, $stage);
        }

        $clients = $query->orderBy('last_name')->orderBy('first_name')->paginate(50)->withQueryString();

        return view('partnership.clients', [
            'clients'  => $clients,
            'search'   => $search ?? '',
            'stage'    => $stage,
            'statuses' => BurgundyClient::CONTACT_STATUSES,
        ]);
    }

    public function show(BurgundyClient $client)
    {
        $client->load(['subscription', 'onboardingSubmission', 'agreement', 'events', 'identifiers']);

        $duplicate = $client->possible_duplicate_of
            ? BurgundyClient::find($client->possible_duplicate_of)
            : BurgundyClient::where('possible_duplicate_of', $client->id)->first();

        return view('partnership.client-show', [
            'client'    => $client,
            'payments'  => $client->payments()->get(),
            'duplicate' => $duplicate,
            'statuses'  => BurgundyClient::CONTACT_STATUSES,
        ]);
    }

    /** Burgundy works the list from here: contact status + notes. */
    public function update(Request $request, BurgundyClient $client)
    {
        $validated = $request->validate([
            'contact_status' => ['nullable', 'string', 'in:' . implode(',', array_keys(BurgundyClient::CONTACT_STATUSES))],
            'notes'          => ['nullable', 'string', 'max:5000'],
        ]);

        $changes = [];

        if (! empty($validated['contact_status']) && $validated['contact_status'] !== $client->contact_status) {
            $was = BurgundyClient::CONTACT_STATUSES[$client->contact_status] ?? $client->contact_status;
            $now = BurgundyClient::CONTACT_STATUSES[$validated['contact_status']];

            $changes['contact_status'] = $validated['contact_status'];

            // Stamp the first time each milestone is reached, so the dashboard can
            // show how long a client has been sitting at a stage.
            match ($validated['contact_status']) {
                'contacted'         => $changes['contacted_at']  = $client->contacted_at ?: now(),
                'interested'        => $changes['interested_at'] = $client->interested_at ?: now(),
                'payment_link_sent' => $changes['link_sent_at']   = $client->link_sent_at ?: now(),
                default             => null,
            };

            $client->logEvent('contact_status', "Contact status changed from {$was} to {$now}.");
        }

        if (array_key_exists('notes', $validated) && $validated['notes'] !== $client->notes) {
            $changes['notes'] = $validated['notes'];
            $client->logEvent('note', 'Notes updated.');
        }

        if ($changes) {
            $client->update($changes);
        }

        return back()->with('success', 'Client updated.');
    }

    /** Add a client Burgundy brings in outside the legacy list. */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['required', 'string', 'max:100'],
            'email'      => ['nullable', 'email', 'max:150'],
            'phone'      => ['nullable', 'string', 'max:30'],
            'notes'      => ['nullable', 'string', 'max:5000'],
        ]);

        $client = BurgundyClient::create($validated + [
            'source'         => 'new',
            'match_status'   => 'matched',
            'matched_on'     => 'manual',
            'contact_status' => 'not_contacted',
        ]);

        $client->rememberIdentifier('email', $validated['email'] ?? null);
        $client->rememberIdentifier('phone', $validated['phone'] ?? null);
        $client->logEvent('imported', 'Added manually from the dashboard.');

        return redirect()
            ->route('partnership.clients.show', $client)
            ->with('success', 'Client added.');
    }

    /**
     * Filter by position in the ladder. Mirrors BurgundyClient::stage(), which
     * checks most-advanced first — so "Contacted" means contacted and not yet
     * further along, not "has ever been contacted".
     */
    private function applyStageFilter($query, string $stage): void
    {
        match ($stage) {
            'needs_review' => $query->where('match_status', 'needs_review'),
            'duplicates'   => $query->whereNotNull('possible_duplicate_of'),
            'cancelled'    => $query->where('client_status', 'cancelled'),
            'active'       => $query->where('client_status', 'active'),
            'onboarding_complete' => $query->where('onboarding_status', 'complete')
                                           ->where('client_status', '!=', 'cancelled'),
            'onboarding_pending'  => $query->whereNotNull('subscription_id')
                                           ->where('onboarding_status', 'pending'),
            'paid'         => $query->whereNotNull('subscription_id'),
            default        => $query->where('contact_status', $stage)
                                    ->whereNull('subscription_id'),
        };
    }
}
