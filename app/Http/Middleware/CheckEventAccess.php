<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventAndEntityLink;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckEventAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. If not authenticated, we allow access to public event pages 
        // (the login middleware should handle auth if required)
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        // 2. Super Admin can access everything
        if ($user->id == 1 || $user->hasRole('Super Admin')) {
            return $next($request);
        }

        // 3. Identify the event from the route
        $event = $request->route('event');
        
        if (!$event instanceof Event) {
            $eventParam = $request->route('event') ?? $request->route('slug') ?? $request->route('eventId');
            if ($eventParam) {
                $event = Event::where('slug', $eventParam)->orWhere('id', $eventParam)->first();
            }
        }

        if ($event) {
            // 4. Check association in event_and_entity_link table
            $isMapped = EventAndEntityLink::where('event_id', $event->id)
                ->where('entity_type', 'users')
                ->where('entity_id', Auth::id())
                ->exists();

            if (!$isMapped && Auth::id() != $event->created_by) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You are not authorized to access this event.'
                    ], 403);
                }

                // Redirect back with both a standard error and a swal flag
                return redirect()->back()
                    ->with('error', 'You do not have permission to access this event.')
                    ->with('error_swal', 'You do not have permission to access this event.');
            }
        }

        return $next($request);
    }
}
