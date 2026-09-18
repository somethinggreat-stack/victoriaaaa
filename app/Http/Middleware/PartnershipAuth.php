<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Guards the partnership dashboard.
 *
 * Checks a dedicated session flag rather than Auth::check(). That separation is
 * the whole point: /victoria-admin admits anyone Laravel considers logged in,
 * so sharing an auth guard would hand the partnership login Victoria's leads,
 * payments, mentorship records and ebook orders.
 *
 * The two areas have no path between them in either direction.
 */
class PartnershipAuth
{
    public const SESSION_KEY = 'partnership_authed';

    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->get(self::SESSION_KEY)) {
            return redirect()
                ->route('partnership.login.show')
                ->with('error', 'Please log in to continue.');
        }

        return $next($request);
    }
}
