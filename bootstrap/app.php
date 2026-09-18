<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin'       => \App\Http\Middleware\AdminAuth::class,
            'reviewer'    => \App\Http\Middleware\ReviewerGuard::class,
            // Separate guard for the Burgundy × Victoria partnership dashboard.
            // Intentionally NOT the 'admin' guard — see PartnershipAuth.
            'partnership' => \App\Http\Middleware\PartnershipAuth::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'authorize-net/webhook',
            'reviewer-access',
            // Public intake form — clients can spend a long time uploading docs,
            // which expires the CSRF token (419 "Page Expired"). It's a public,
            // unauthenticated form (nothing to forge), so exempting it is safe.
            'onboarding',
            // Same reasoning for Burgundy's intake form.
            'burgundy-onboarding',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
