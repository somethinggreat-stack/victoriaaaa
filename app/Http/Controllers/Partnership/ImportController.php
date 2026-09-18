<?php

namespace App\Http\Controllers\Partnership;

use App\Http\Controllers\Controller;
use App\Models\BurgundyClient;
use App\Services\BurgundyClientImporter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Imports the legacy client list from a CSV uploaded in the browser.
 *
 * Exists because the production host has no terminal, so `artisan db:seed`
 * is not available there. The upload is read straight from the temp file and
 * never written to disk, so the list — which carries SSN last-4 and dates of
 * birth — is never left sitting in a web-reachable directory.
 */
class ImportController extends Controller
{
    public function show()
    {
        return view('partnership.import', [
            'clientCount' => BurgundyClient::count(),
            'result'      => session('import_result'),
        ]);
    }

    public function store(Request $request, BurgundyClientImporter $importer)
    {
        $request->validate([
            // `txt` is allowed because some browsers report a .csv as text/plain.
            'csv' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ], [
            'csv.required' => 'Choose the client list CSV first.',
            'csv.mimes'    => 'That needs to be a .csv file. In Excel or Sheets, use File → Download → CSV.',
            'csv.max'      => 'That file is larger than 5 MB — it is probably not the client list.',
        ]);

        $result = $importer->importFile($request->file('csv')->getRealPath());

        Log::info('[Burgundy] Client list imported from the dashboard', [
            'created' => $result['created'],
            'updated' => $result['updated'],
            'skipped' => $result['skipped'],
            'ip'      => $request->ip(),
        ]);

        if ($result['errors']) {
            return back()->with('error', implode(' ', $result['errors']));
        }

        return redirect()
            ->route('partnership.import')
            ->with('import_result', $result)
            ->with('success', "Imported. {$result['created']} added, {$result['updated']} updated.");
    }
}
