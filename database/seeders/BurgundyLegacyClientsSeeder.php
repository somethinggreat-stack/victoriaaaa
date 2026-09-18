<?php

namespace Database\Seeders;

use App\Models\BurgundyClient;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Imports Burgundy's existing client list into the partnership pipeline.
 *
 * The CSV holds real PII (SSN last-4, dates of birth, emails, phones) and is
 * gitignored — upload it to the server and run:
 *
 *     php artisan db:seed --class=BurgundyLegacyClientsSeeder
 *
 * Expected header:
 *   First Name,Middle Name,Last Name,Mobile Number,Email,Date of Birth,SSN Last 4 Digits,zip
 *
 * Safe to run more than once: clients are keyed on their email identifier, so a
 * second run updates rather than duplicates.
 */
class BurgundyLegacyClientsSeeder extends Seeder
{
    /** Excel serial dates count from this day (the 1900 system's real epoch). */
    private const EXCEL_EPOCH = '1899-12-30';

    public function run(): void
    {
        $path = env('BURGUNDY_CLIENTS_CSV', database_path('data/burgundy-legacy-clients.csv'));

        if (! is_readable($path)) {
            $this->command->error("CSV not found at: {$path}");
            $this->command->line('Place the client list there, or set BURGUNDY_CLIENTS_CSV to its path.');

            return;
        }

        $rows = array_map('str_getcsv', file($path));
        array_shift($rows); // header

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $samples = [];

        foreach ($rows as $row) {
            if (count($row) < 5 || trim((string) ($row[0] ?? '')) === '') {
                $skipped++;
                continue;
            }

            [$first, $middle, $last, $phone, $email] = array_map('trim', array_slice($row + array_fill(0, 8, ''), 0, 5));
            $dobRaw = trim((string) ($row[5] ?? ''));
            $ssn4   = trim((string) ($row[6] ?? ''));
            $zip    = trim((string) ($row[7] ?? ''));

            // Credit Repair Cloud ships two demo records in every account and they
            // ride along in exports. Skip them, but name them in the output so a
            // real client is never dropped without anyone noticing.
            if ($this->isCrcSampleRow($first, $last, $email)) {
                $samples[] = trim("{$first} {$last}") . " <{$email}>";
                continue;
            }

            $emailKey = BurgundyClient::normalizeEmail($email);
            $phoneKey = BurgundyClient::normalizePhone($phone);

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

            // Key on the email so a re-run updates the same person.
            $existing = $emailKey
                ? BurgundyClient::whereRaw('LOWER(email) = ?', [$emailKey])->first()
                : null;

            if ($existing) {
                $existing->update($attributes);
                $client = $existing;
                $updated++;
            } else {
                $client = BurgundyClient::create($attributes);
                $client->logEvent('imported', 'Imported from Burgundy legacy client list.');
                $created++;
            }

            $client->rememberIdentifier('email', $email);
            $client->rememberIdentifier('phone', $phone);
        }

        $this->linkPossibleDuplicates();

        $total = BurgundyClient::where('source', 'legacy')->count();

        $this->command->info("Burgundy legacy clients — created {$created}, updated {$updated}, skipped {$skipped}.");
        $this->command->info("Total legacy clients now: {$total}");

        foreach ($samples as $sample) {
            $this->command->warn("Skipped Credit Repair Cloud demo record: {$sample}");
        }

        $dupes = BurgundyClient::whereNotNull('possible_duplicate_of')->count();
        if ($dupes > 0) {
            $this->command->warn("{$dupes} record(s) flagged as possible duplicates — review them in the dashboard.");
        }
    }

    /**
     * Credit Repair Cloud's two built-in demo rows ("Sample Client" /
     * "Sample Lead", sample@client.com / sample@lead.com).
     *
     * Matched on the exact CRC defaults only — a real client called Sample
     * would need BOTH the name and the placeholder email to be skipped.
     */
    private function isCrcSampleRow(string $first, string $last, string $email): bool
    {
        $emailKey = strtolower(trim($email));
        $nameKey  = strtolower(trim("{$first} {$last}"));

        return in_array($emailKey, ['sample@client.com', 'sample@lead.com'], true)
            && in_array($nameKey, ['sample client', 'sample lead'], true);
    }

    /**
     * Flag records that look like the same person twice.
     *
     * Same SSN last-4 AND same date of birth is a strong signal, but not proof —
     * the pair is linked for a human to confirm, never merged automatically.
     * Merging two real people silently corrupts both records; leaving an extra
     * row costs nothing.
     */
    private function linkPossibleDuplicates(): void
    {
        $groups = BurgundyClient::whereNotNull('ssn_last4')
            ->whereNotNull('birth_date')
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

            // Guard against a stray number that is not a plausible birth date.
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
