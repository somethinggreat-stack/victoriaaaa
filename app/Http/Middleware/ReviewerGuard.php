<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ReviewerGuard
{
    /**
     * When the active session was opened by the read-only reviewer login,
     * only let it reach the dashboard + logout. Everything else (customer
     * lists, payment records, lead detail pages) redirects back to the
     * dashboard with a notice. PII never reaches the reviewer.
     */
    protected array $allowed = [
        'admin.dashboard',
        'admin.logout',
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
