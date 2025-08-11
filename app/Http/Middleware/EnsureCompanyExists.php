<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureCompanyExists
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if ($request->routeIs('company.store', 'company.update')) {
            return $next($request);
        }

        if ($user && !$user->company_id) {
            session()->flash('error', 'Please add your company details.');
            return redirect()->route('company.details');
        }
        return $next($request);
    }
}
