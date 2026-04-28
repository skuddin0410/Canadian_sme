<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Speaker;
use Illuminate\Support\Facades\DB;
use App\Models\Event;

class SpeakerAnalyticsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function speaker(Request $request)
{
    $eventId = $request->event_id;

    $query = Speaker::select(
        'speakers.id',
        'speakers.name',
        DB::raw('COUNT(user_agendas.id) as total_attendees')
    )
        ->leftJoin('session_speakers', 'speakers.id', '=', 'session_speakers.speaker_id')
        ->leftJoin('event_sessions', 'session_speakers.session_id', '=', 'event_sessions.id')
        ->leftJoin('user_agendas', 'event_sessions.id', '=', 'user_agendas.session_id');

    // Apply event filter
    if ($eventId) {
        if (!isSuperAdmin() && !in_array($eventId, getEventIds())) {
            $eventId = 0;
        }
        $query->where('event_sessions.event_id', $eventId);
    } elseif (!isSuperAdmin()) {
        $query->whereIn('event_sessions.event_id', getEventIds());
    }

    $speakerAnalytics = $query
        ->groupBy('speakers.id', 'speakers.name')
        ->get();

    // fetch events for dropdown
    $events = isSuperAdmin()
        ? Event::orderBy('title')->get()
        : Event::orderBy('title')->whereIn('id', getEventIds())->get();

    return view('admin.analytics.speaker', compact('speakerAnalytics','events'));
}
}
