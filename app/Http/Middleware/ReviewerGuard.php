<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ReviewerGuard
{
    /**
     * Routes the read-only reviewer session may reach. The four lead-list
     * pages are exposed so reviewers can see column shape + activity volume;
     * each view masks PII (see App\Support\Mask). Detail pages, status
     * mutations, paid-client onboarding (SSN/DOB), payment ledgers, and the
     * global search route stay blocked.
     */
    protected array $allowed = [
        'admin.dashboard',
        'admin.logout',
        'admin.leads',
        'admin.contacts',
        'admin.funding',
        'admin.mentorship',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || !$request->session()->get('review_mode')) {
            return $next($request);
        }

        $routeName = optional($request->route())->getName();
        if (!in_array($routeName, $this->allowed, true)) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'This view is hidden in the read-only reviewer account.');
        }

        return $next($request);
    }
}
