<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventWaitlist;
use Illuminate\Http\Request;

class EventWaitlistController extends Controller
{
    public function index(Request $request)
    {
        $query = EventWaitlist::with(['event', 'ticketType'])
            ->latest('joined_at')
            ->latest();

        if ($request->filled('event_id')) {
            $eventId = (int) $request->event_id;

            if (!isSuperAdmin() && !in_array($eventId, getEventIds(), true)) {
                $eventId = 0;
            }

            $query->where('event_id', $eventId);
        } elseif (!isSuperAdmin()) {
            $query->whereIn('event_id', getEventIds());
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('company', 'like', '%' . $search . '%');
            });
        }

        $waitlists = $query->paginate(20)->withQueryString();
        $events = isSuperAdmin()
            ? Event::orderBy('title')->get()
            : Event::whereIn('id', getEventIds())->orderBy('title')->get();

        return view('tickets.waitlists.index', compact('waitlists', 'events'));
    }
}
