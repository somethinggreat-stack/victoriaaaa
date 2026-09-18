<?php

namespace App\Services;

use App\Models\BurgundyClient;
use App\Models\BurgundyClientIdentifier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Works out WHICH Burgundy client a payment or onboarding submission belongs to.
 *
 * The $100 checkout link is shared by everyone, so a charge arrives with only
 * whatever the payer typed into the billing form. This class is the single
 * place that decides who they are.
 *
 * The rule that matters: it never guesses. Anything ambiguous is handed to a
 * human through the review queue rather than attached to the wrong person —
 * a wrong match silently corrupts two client records, while an unresolved one
 * is just a short worklist.
 */
class BurgundyClientMatcher
{
    /**
     * Find the client these details belong to.
     *
     * @return array{client: ?BurgundyClient, matched_on: ?string, reason: string, candidates: array<int>}
     */
    public function find(?string $email, ?string $phone, ?string $firstName = null, ?string $lastName = null): array
    {
        $emailKey = BurgundyClient::normalizeEmail($email);
        $phoneKey = BurgundyClient::normalizePhone($phone);

        $byEmail = $emailKey ? $this->clientIdFor('email', $emailKey) : null;
        $byPhone = $phoneKey ? $this->clientIdFor('phone', $phoneKey) : null;

        // ── Email and phone both known, but pointing at different people ──
        // Never pick a side. Someone reused a phone number, or two records need
        // merging — either way a human has to look.
        if ($byEmail && $byPhone && $byEmail !== $byPhone) {
            return [
                'client'     => null,
                'matched_on' => null,
                'reason'     => 'Email and phone belong to two different clients.',
                'candidates' => [$byEmail, $byPhone],
            ];
        }

        // ── 1 & 2. A known email or phone is conclusive ──
        if ($id = ($byEmail ?: $byPhone)) {
            return [
                'client'     => BurgundyClient::find($id),
                'matched_on' => $byEmail ? 'email' : 'phone',
                'reason'     => $byEmail ? 'Matched a known email address.' : 'Matched a known phone number.',
                'candidates' => [$id],
            ];
        }

        // ── 3. Exact name, and only one person carries it ──
        $nameKey = BurgundyClient::normalizeName($firstName, $lastName);

        if ($nameKey) {
            $named = BurgundyClient::all(['id', 'first_name', 'last_name'])
                ->filter(fn ($c) => BurgundyClient::normalizeName($c->first_name, $c->last_name) === $nameKey)
                ->values();

            if ($named->count() === 1) {
                return [
                    'client'     => BurgundyClient::find($named[0]->id),
                    'matched_on' => 'name',
                    'reason'     => 'Matched on exact name (new email and phone).',
                    'candidates' => [$named[0]->id],
                ];
            }

            if ($named->count() > 1) {
                return [
                    'client'     => null,
                    'matched_on' => null,
                    'reason'     => 'Several clients share this exact name.',
                    'candidates' => $named->pluck('id')->all(),
                ];
            }
        }

        return [
            'client'     => null,
            'matched_on' => null,
            'reason'     => 'No existing client matched this email, phone or name.',
            'candidates' => [],
        ];
    }

    /**
     * Find the client these payer details belong to, or create one flagged for
     * review. Always returns a client — a paid customer is never dropped on the
     * floor because we could not identify them.
     *
     * @param  array  $payer  first_name, last_name, email, phone, zip
     */
    public function matchOrFlag(array $payer, string $context = 'payment'): BurgundyClient
    {
        $result = $this->find(
            $payer['email'] ?? null,
            $payer['phone'] ?? null,
            $payer['first_name'] ?? null,
            $payer['last_name'] ?? null,
        );

        if ($result['client']) {
            $client = $result['client'];

            // Learn whatever is new, so the next charge matches outright.
            $client->rememberIdentifier('email', $payer['email'] ?? null);
            $client->rememberIdentifier('phone', $payer['phone'] ?? null);

            if ($result['matched_on'] === 'name') {
                $client->update(['matched_on' => 'name']);
                $client->logEvent(
                    'matched',
                    'Matched on name only — the email and phone used at checkout were new. Worth a glance.',
                    ['context' => $context, 'email' => $payer['email'] ?? null, 'phone' => $payer['phone'] ?? null],
                );
            }

            return $client;
        }

        // ── Nothing conclusive: create the client, flagged for review ──
        $client = BurgundyClient::create([
            'first_name'     => $payer['first_name'] ?? 'Unknown',
            'last_name'      => $payer['last_name'] ?? '',
            'email'          => $payer['email'] ?? null,
            'phone'          => $payer['phone'] ?? null,
            'zip'            => $payer['zip'] ?? null,
            'source'         => 'new',
            'match_status'   => 'needs_review',
            'matched_on'     => null,
            'contact_status' => 'not_contacted',
        ]);

        $client->rememberIdentifier('email', $payer['email'] ?? null);
        $client->rememberIdentifier('phone', $payer['phone'] ?? null);

        $client->logEvent(
            'needs_review',
            $result['reason'] . ' Confirm as a new client, or merge into an existing one.',
            ['context' => $context, 'candidates' => $result['candidates']],
        );

        Log::info('[Burgundy] Unmatched ' . $context . ' — queued for review', [
            'client_id' => $client->id,
            'email'     => $payer['email'] ?? null,
            'reason'    => $result['reason'],
        ]);

        return $client;
    }

    /**
     * Fold $source into $target and delete $source.
     *
     * Used when a human resolves a review ("this payment is actually Amber
     * Mack"). Everything the source learned — its subscription, onboarding,
     * signed agreement, emails and phones — moves across, so the next charge
     * from either identity matches the surviving record.
     */
    public function merge(BurgundyClient $source, BurgundyClient $target): BurgundyClient
    {
        if ($source->id === $target->id) {
            return $target;
        }

        DB::transaction(function () use ($source, $target) {
            // Links move only into empty slots — never overwrite a subscription
            // or onboarding record the target already has.
            foreach (['subscription_id', 'onboarding_submission_id', 'payment_agreement_id'] as $field) {
                if (! $target->$field && $source->$field) {
                    $target->$field = $source->$field;
                }
            }

            foreach (['email', 'phone', 'zip', 'birth_date', 'ssn_last4', 'apex_status', 'apex_id'] as $field) {
                if (! $target->$field && $source->$field) {
                    $target->$field = $source->$field;
                }
            }

            // The merged record keeps whichever side got furthest.
            if ($source->onboarding_status === 'complete') {
                $target->onboarding_status = 'complete';
            }
            if ($source->client_status === 'active' && $target->client_status !== 'cancelled') {
                $target->client_status = 'active';
            }

            $target->match_status          = 'matched';
            $target->matched_on            = 'manual';
            $target->possible_duplicate_of = null;
            $target->save();

            // Identifiers and timeline follow the client.
            BurgundyClientIdentifier::where('burgundy_client_id', $source->id)
                ->update(['burgundy_client_id' => $target->id]);

            DB::table('burgundy_client_events')
                ->where('burgundy_client_id', $source->id)
                ->update(['burgundy_client_id' => $target->id]);

            // Anything that pointed at the source now points at the survivor.
            BurgundyClient::where('possible_duplicate_of', $source->id)
                ->update(['possible_duplicate_of' => $target->id]);

            $target->logEvent(
                'merged',
                sprintf('Merged "%s" (#%d) into this client.', $source->full_name, $source->id),
                [
                    'merged_from_id'    => $source->id,
                    'merged_from_email' => $source->email,
                    'merged_from_phone' => $source->phone,
                ],
            );

            $source->delete();
        });

        return $target->fresh();
    }

    /** Which client, if any, owns this normalized identifier. */
    private function clientIdFor(string $type, string $value): ?int
    {
        return BurgundyClientIdentifier::where('type', $type)
            ->where('value', $value)
            ->value('burgundy_client_id');
    }
}
