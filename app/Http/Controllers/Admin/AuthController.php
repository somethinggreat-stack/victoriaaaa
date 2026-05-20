<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    public function show()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $key = 'admin-login:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors([
                'email' => "Too many attempts. Try again in {$seconds} seconds.",
            ])->onlyInput('email');
        }

        // Read-only reviewer login (e.g. Authorize.Net underwriting reviews).
        // Credentials live in .env so they can be rotated without touching code
        // or the database. The reviewer still needs a row in `users` so Laravel's
        // session guard can rehydrate the user on every request.
        $reviewerEmail    = trim((string) env('REVIEWER_EMAIL', ''));
        $reviewerPassword = (string) env('REVIEWER_PASSWORD', '');
        if (
            $reviewerEmail !== '' && $reviewerPassword !== ''
            && hash_equals(strtolower($reviewerEmail), strtolower($credentials['email']))
            && hash_equals($reviewerPassword, $credentials['password'])
        ) {
            $reviewer = User::where('email', $reviewerEmail)->first();
            if ($reviewer) {
                Auth::login($reviewer, $request->boolean('remember'));
                $request->session()->regenerate();
                $request->session()->put('review_mode', true);
                RateLimiter::clear($key);
                return redirect()->route('admin.dashboard');
            }
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $request->session()->forget('review_mode');
            RateLimiter::clear($key);
            return redirect()->intended(route('admin.dashboard'));
        }

        RateLimiter::hit($key, 60);
        return back()->withErrors([
            'email' => 'Invalid email or password.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login.show')->with('success', 'You have been logged out.');
    }
}
