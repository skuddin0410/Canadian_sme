<?php

namespace App\Http\Middleware;

use App\Models\Event;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;

class RedirectIfNotAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            $eventId = null;

            if ($request->route('slug')) {
                $eventId = Event::where('slug', $request->route('slug'))->value('id');
            }

            if ($eventId) {
                Session::put('event_id', $eventId);
            } else {
                $eventId = Session::get('event_id');
            }

            if ($eventId) {
                return redirect()->guest(route('event.user.login', ['event' => $eventId]));
            }

            return redirect()->guest(route('login'));
        }

        return $next($request);
    }
}
