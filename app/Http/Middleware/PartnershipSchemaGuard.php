<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

/**
 * Stops the partnership dashboard before it queries a schema that isn't ready.
 *
 * The deploy script runs `migrate --force || true`, so a failed migration is
 * swallowed and the deploy still reports success. When that happens every page
 * dies on its first query with a raw SQL exception.
 *
 * This runs ahead of the controllers — a guard inside the layout is too late,
 * because the controller has already queried by the time the view renders.
 *
 * It checks a COLUMN, not just the table. A reverted earlier module left an
 * empty `burgundy_clients` behind with a completely different shape, so
 * hasTable() alone returned true and the page still fatalled.
 */
class PartnershipSchemaGuard
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->ready()) {
            return response()->view('partnership.needs-setup', [], 503);
        }

        return $next($request);
    }

    private function ready(): bool
    {
        try {
            return Schema::hasTable('burgundy_clients')
                && Schema::hasColumn('burgundy_clients', 'match_status')
                && Schema::hasTable('burgundy_client_identifiers')
                && Schema::hasTable('burgundy_client_events');
        } catch (\Throwable $e) {
            // Database unreachable entirely — still better than a stack trace.
            return false;
        }
    }
}
