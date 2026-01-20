<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminOnly
{
    public function handle(Request $request, Closure $next)
    {
        // 1) Not logged in -> go login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // 2) Logged in but not admin -> logout (optional) + go login
        // Use whichever role check your project uses:

        // (A) If using spatie/laravel-permission:
        if (method_exists($user, 'hasRole')) {
            if (!$user->hasRole('Admin')) {
                Auth::logout();
                return redirect()->route('login');
            }
        }
        // (B) If you store role in column like `role`:
        else {
            if (($user->role ?? null) !== 'Admin') {
                Auth::logout();
                return redirect()->route('login');
            }
        }

        return $next($request);
    }
}
