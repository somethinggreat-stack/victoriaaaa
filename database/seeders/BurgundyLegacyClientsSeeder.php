<?php

namespace Database\Seeders;

use App\Services\BurgundyClientImporter;
use Illuminate\Database\Seeder;

/**
 * Console entry point for importing Burgundy's legacy client list.
 *
 * The CSV holds real PII (SSN last-4, dates of birth, emails, phones) and is
 * gitignored. Run with:
 *
 *     php artisan db:seed --class=BurgundyLegacyClientsSeeder
 *
 * If there is no terminal available, the same import is on the partnership
 * dashboard as a file upload — see Partnership\ImportController.
 *
 * All the logic lives in BurgundyClientImporter so both routes behave the same.
 */
class BurgundyLegacyClientsSeeder extends Seeder
{
    public function run(BurgundyClientImporter $importer): void
    {
        $path = env('BURGUNDY_CLIENTS_CSV', database_path('data/burgundy-legacy-clients.csv'));

        if (! is_readable($path)) {
            $this->command->error("CSV not found at: {$path}");
            $this->command->line('Place the client list there, or set BURGUNDY_CLIENTS_CSV to its path.');

            return;
        }

        $r = $importer->importFile($path);

        foreach ($r['errors'] as $error) {
            $this->command->error($error);
        }

        if ($r['errors']) {
            return;
        }

        $this->command->info("Burgundy legacy clients — created {$r['created']}, updated {$r['updated']}, skipped {$r['skipped']}.");
        $this->command->info("Total clients now: {$r['total']}");

        foreach ($r['samples'] as $sample) {
            $this->command->warn("Skipped Credit Repair Cloud demo record: {$sample}");
        }

        if ($r['duplicates'] > 0) {
            $this->command->warn("{$r['duplicates']} record(s) flagged as possible duplicates — review them in the dashboard.");
        }
    }
}
