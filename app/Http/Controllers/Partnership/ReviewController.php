<?php

namespace App\Http\Controllers\Partnership;

use App\Http\Controllers\Controller;
use App\Models\BurgundyClient;
use App\Services\BurgundyClientMatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * The human half of client matching.
 *
 * Two kinds of thing land here:
 *   • Payments and onboardings the matcher could not confidently attribute.
 *   • Seeded records that look like the same person listed twice.
 *
 * Nothing in either case is ever resolved automatically — the point of this
 * screen is that a person who knows these clients makes the call.
 */
class ReviewController extends Controller
{
    public function index()
    {
        $unmatched = BurgundyClient::with('subscription')
            ->where('match_status', 'needs_review')
            ->latest()
            ->get();

        $duplicates = BurgundyClient::with('subscription')
            ->whereNotNull('possible_duplicate_of')
            ->get()
            ->map(function ($client) {
                $client->setRelation('duplicateOf', BurgundyClient::find($client->possible_duplicate_of));

                return $client;
            });

        return view('partnership.review', [
            'unmatched'  => $unmatched,
            'duplicates' => $duplicates,
        ]);
    }

    /** "This payment is actually <existing client>." */
    public function merge(Request $request, BurgundyClient $client, BurgundyClientMatcher $matcher)
    {
        $validated = $request->validate([
            'target_id' => ['required', 'integer', 'exists:burgundy_clients,id'],
        ]);

        if ((int) $validated['target_id'] === $client->id) {
            return back()->with('error', 'That is the same client.');
        }

        $target = BurgundyClient::findOrFail($validated['target_id']);

        Log::info('[Burgundy] Manual merge', [
            'source_id' => $client->id,
            'target_id' => $target->id,
            'ip'        => $request->ip(),
        ]);

        $survivor = $matcher->merge($client, $target);

        return redirect()
            ->route('partnership.clients.show', $survivor)
            ->with('success', 'Merged. Future payments from either email or phone will now match this client.');
    }

    /** "No, this really is someone new." */
    public function confirm(Request $request, BurgundyClient $client)
    {
        $client->update([
            'match_status' => 'matched',
            'matched_on'   => 'manual',
        ]);

        $client->logEvent('matched', 'Confirmed as a new client, not one of the legacy list.');

        return redirect()
            ->route('partnership.review')
            ->with('success', $client->full_name . ' confirmed as a new client.');
    }

    /** "These two are different people — stop flagging them." */
    public function notDuplicate(Request $request, BurgundyClient $client)
    {
        $client->update(['possible_duplicate_of' => null]);
        $client->logEvent('matched', 'Confirmed as a separate person, not a duplicate.');

        return redirect()
            ->route('partnership.review')
            ->with('success', 'Marked as a separate person.');
    }

    /** Type-ahead for the merge picker. */
    public function search(Request $request)
    {
        $term = trim((string) $request->query('q', ''));

        if (strlen($term) < 2) {
            return response()->json([]);
        }

        $like = '%' . $term . '%';

        $results = BurgundyClient::where('match_status', 'matched')
            ->where(function ($q) use ($like) {
                $q->where('first_name', 'like', $like)
                  ->orWhere('last_name', 'like', $like)
                  ->orWhere('email', 'like', $like);
            })
            ->limit(15)
            ->get(['id', 'first_name', 'last_name', 'email', 'phone'])
            ->map(fn ($c) => [
                'id'    => $c->id,
                'label' => $c->full_name,
                'email' => $c->email,
                'phone' => $c->phone,
            ]);

        return response()->json($results);
    }
}
