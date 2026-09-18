<?php

namespace App\Services;

use App\Models\BurgundyClient;
use Illuminate\Support\Carbon;

/**
 * Imports Burgundy's legacy client list from a CSV.
 *
 * Shared by the console seeder and the dashboard's upload form, so an import
 * behaves identically whichever way it is run.
 *
 * Expected header:
 *   First Name,Middle Name,Last Name,Mobile Number,Email,Date of Birth,SSN Last 4 Digits,zip
 *
 * Safe to run repeatedly: clients are keyed on email, so a second run updates
 * rather than duplicates.
 */
class BurgundyClientImporter
{
    /** Excel serial dates count from this day (the 1900 system's real epoch). */
    private const EXCEL_EPOCH = '1899-12-30';

    /**
     * @return array{created:int, updated:int, skipped:int, samples:array<string>,
     *               duplicates:int, total:int, errors:array<string>}
     */
    public function importFile(string $path): array
    {
        $result = [
            'created' => 0, 'updated' => 0, 'skipped' => 0,
            'samples' => [], 'duplicates' => 0, 'total' => 0, 'errors' => [],
        ];

        if (! is_readable($path)) {
            $result['errors'][] = "Could not read the file at {$path}.";

            return $result;
        }

        $rows = array_map('str_getcsv', file($path));

        if (empty($rows)) {
            $result['errors'][] = 'That file appears to be empty.';

            return $result;
        }

        $header = array_map(fn ($h) => strtolower(trim((string) $h)), $rows[0]);

        // Guard against someone uploading the wrong spreadsheet entirely.
        if (! str_contains(implode(',', $header), 'first name')) {
            $result['errors'][] = 'That does not look like the client list — the first column should be "First Name".';

            return $result;
        }

        array_shift($rows);

        foreach ($rows as $row) {
            if (count($row) < 5 || trim((string) ($row[0] ?? '')) === '') {
                $result['skipped']++;
                continue;
            }

            $row = $row + array_fill(0, 8, '');

            [$first, $middle, $last, $phone, $email] = array_map('trim', array_slice($row, 0, 5));
            $dobRaw = trim((string) $row[5]);
            $ssn4   = trim((string) $row[6]);
            $zip    = trim((string) $row[7]);

            // Credit Repair Cloud ships two demo records in every account and they
            // ride along in exports. Skip them, but report them by name so a real
            // client is never dropped without anyone noticing.
            if ($this->isCrcSampleRow($first, $last, $email)) {
                $result['samples'][] = trim("{$first} {$last}") . " <{$email}>";
                continue;
            }

            $attributes = [
                'first_name'     => $first,
                'middle_name'    => $middle ?: null,
                'last_name'      => $last,
                'email'          => $email ?: null,
                'phone'          => $phone ?: null,
                'zip'            => $zip ?: null,
                'birth_date'     => $this->parseDate($dobRaw),
                'ssn_last4'      => $ssn4 !== '' ? substr(preg_replace('/\D+/', '', $ssn4), -4) : null,
                'source'         => 'legacy',
                'match_status'   => 'matched',
                'matched_on'     => 'seed',
                'contact_status' => 'not_contacted',
            ];

            $emailKey = BurgundyClient::normalizeEmail($email);

            $existing = $emailKey
                ? BurgundyClient::whereRaw('LOWER(email) = ?', [$emailKey])->first()
                : null;

            if ($existing) {
                // Never undo work already done on a client — an import must not
                // reset someone back to "Not Contacted" or unlink their payment.
                unset($attributes['contact_status'], $attributes['source'], $attributes['match_status'], $attributes['matched_on']);
                $existing->update($attributes);
                $client = $existing;
                $result['updated']++;
            } else {
                $client = BurgundyClient::create($attributes);
                $client->logEvent('imported', 'Imported from Burgundy legacy client list.');
                $result['created']++;
            }

            $client->rememberIdentifier('email', $email);
            $client->rememberIdentifier('phone', $phone);
        }

        $this->linkPossibleDuplicates();

        $result['duplicates'] = BurgundyClient::whereNotNull('possible_duplicate_of')->count();
        $result['total']      = BurgundyClient::count();

        return $result;
    }

    /**
     * Flag records that look like the same person twice.
     *
     * Same SSN last-4 AND same date of birth is a strong signal, but not proof —
     * the pair is linked for a human to confirm, never merged automatically.
     * Merging two real people silently corrupts both records; an extra row costs
     * nothing.
     */
    private function linkPossibleDuplicates(): void
    {
        $groups = BurgundyClient::whereNotNull('ssn_last4')
            ->whereNotNull('birth_date')
            ->whereNull('possible_duplicate_of')
            ->get()
            ->groupBy(fn ($c) => $c->ssn_last4 . '|' . $c->birth_date->toDateString())
            ->filter(fn ($group) => $group->count() > 1);

        foreach ($groups as $group) {
            $keeper = $group->first();

            foreach ($group->skip(1) as $duplicate) {
                $duplicate->update(['possible_duplicate_of' => $keeper->id]);
                $duplicate->logEvent(
                    'possible_duplicate',
                    sprintf(
                        'Same SSN last-4 and date of birth as "%s" (#%d). Confirm whether these are the same person.',
                        $keeper->full_name,
                        $keeper->id,
                    ),
                );
            }
        }
    }

    /**
     * Credit Repair Cloud's two built-in demo rows. Matched on the exact CRC
     * defaults only — a real client called Sample would need BOTH the name and
     * the placeholder email to be skipped.
     */
    private function isCrcSampleRow(string $first, string $last, string $email): bool
    {
        return in_array(strtolower(trim($email)), ['sample@client.com', 'sample@lead.com'], true)
            && in_array(strtolower(trim("{$first} {$last}")), ['sample client', 'sample lead'], true);
    }

    /**
     * The export writes dates as Excel serial numbers (33630 → 1992-01-27).
     * Plain date strings are accepted too, in case a later export differs.
     */
    private function parseDate(string $value): ?string
    {
        if ($value === '') {
            return null;
        }

        if (ctype_digit($value)) {
            $serial = (int) $value;

            if ($serial < 1 || $serial > 80000) {
                return null;
            }

            return Carbon::parse(self::EXCEL_EPOCH)->addDays($serial)->toDateString();
        }

        try {
            return Carbon::parse($value)->toDateString();
        } catch (\Throwable $e) {
            return null;
        }
    }
}
