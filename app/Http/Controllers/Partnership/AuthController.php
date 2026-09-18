<?php

namespace App\Http\Controllers\Partnership;

use App\Http\Controllers\Controller;
use App\Http\Middleware\PartnershipAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    public function show(Request $request)
    {
        if ($request->session()->get(PartnershipAuth::SESSION_KEY)) {
            return redirect()->route('partnership.dashboard');
        }

        return view('partnership.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $key = 'partnership-login:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            return back()->withErrors([
                'email' => "Too many attempts. Try again in {$seconds} seconds.",
            ])->onlyInput('email');
        }

        $expectedEmail    = trim((string) config('partnership.email', ''));
        $expectedPassword = (string) config('partnership.password', '');

        // An unconfigured login must never be an open door.
        if ($expectedEmail === '' || $expectedPassword === '') {
            Log::error('[Partnership] Login attempted but PARTNERSHIP_EMAIL / PARTNERSHIP_PASSWORD are not configured.');

            return back()->withErrors([
                'email' => 'This dashboard is not configured yet. Please contact support.',
            ])->onlyInput('email');
        }

        $emailOk    = hash_equals(strtolower($expectedEmail), strtolower($credentials['email']));
        $passwordOk = hash_equals($expectedPassword, $credentials['password']);

        if ($emailOk && $passwordOk) {
            $request->session()->regenerate();
            $request->session()->put(PartnershipAuth::SESSION_KEY, true);
            RateLimiter::clear($key);

            Log::info('[Partnership] Login successful', ['ip' => $request->ip()]);

            return redirect()->intended(route('partnership.dashboard'));
        }

        RateLimiter::hit($key, 60);

        Log::warning('[Partnership] Failed login attempt', [
            'ip'    => $request->ip(),
            'email' => $credentials['email'],
        ]);

        return back()->withErrors([
            'email' => 'Invalid email or password.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        $request->session()->forget(PartnershipAuth::SESSION_KEY);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('partnership.login.show')
            ->with('success', 'You have been logged out.');
    }
}
