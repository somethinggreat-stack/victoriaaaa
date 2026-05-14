<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    /**
     * Guard the /victoria-admin area. Anyone who isn't logged in gets bounced to login.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login.show')
                ->with('error', 'Please log in to continue.');
        }
        return $next($request);
    }
}
