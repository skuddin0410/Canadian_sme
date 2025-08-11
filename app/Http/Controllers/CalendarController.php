<?php
namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Session;
use App\Models\Track;
use App\Models\Venue;
use App\Models\Speaker;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $eventId = $request->get('event_id');
        $event = Event::findOrFail($eventId);
        
        return view('calendar.index', compact('event'));
    }

    public function getSessions(Request $request): JsonResponse
    {
        $eventId = $request->get('event_id');
        $start = $request->get('start');
        $end = $request->get('end');

        $query = Session::with(['track', 'venue', 'speakers'])
            ->where('event_id', $eventId)
            ->where('status', 'published');

        if ($start && $end) {
            $query->whereBetween('start_time', [
                Carbon::parse($start)->startOfDay(),
                Carbon::parse($end)->endOfDay()
            ]);
        }

        $sessions = $query->orderBy('start_time')->get();

        $events = $sessions->map(function ($session) {
            return [
                'id' => $session->id,
                'title' => $session->title,
                'start' => $session->start_time->toISOString(),
                'end' => $session->end_time->toISOString(),
                'backgroundColor' => $session->track->color ?? '#3498db',
                'borderColor' => $session->track->color ?? '#3498db',
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'description' => $session->description,
                    'type' => $session->type,
                    'track' => $session->track->name ?? null,
                    'venue' => $session->venue->name ?? null,
                    'speakers' => $session->speakers->map(function ($speaker) {
                        return [
                            'id' => $speaker->id,
                            'name' => $speaker->name,
                            'role' => $speaker->pivot->role
                        ];
                    }),
                    'capacity' => $session->capacity,
                    'duration' => $session->getDurationInMinutes()
                ]
            ];
        });

        return response()->json($events);
    }

    public function createSession(Request $request): JsonResponse
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'title' => 'required|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'track_id' => 'nullable|exists:tracks,id',
            'venue_id' => 'nullable|exists:venues,id',
            'type' => 'required|in:presentation,workshop,panel,break,networking',
            'description' => 'nullable|string',
            'capacity' => 'nullable|integer|min:1',
            'speaker_ids' => 'nullable|array',
            'speaker_ids.*' => 'exists:speakers,id'
        ]);

        // Check for venue conflicts
        if ($request->venue_id) {
            $conflicts = Session::where('venue_id', $request->venue_id)
                ->where('event_id', $request->event_id)
                ->where('status', '!=', 'cancelled')
                ->where(function ($query) use ($request) {
                    $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                          ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                          ->orWhere(function ($q) use ($request) {
                              $q->where('start_time', '<=', $request->start_time)
                                ->where('end_time', '>=', $request->end_time);
                          });
                })
                ->exists();

            if ($conflicts) {
                return response()->json([
                    'error' => 'Venue is already booked for this time slot'
                ], 422);
            }
        }

        $session = Session::create($request->except(['speaker_ids']));

        // Attach speakers if provided
        if ($request->speaker_ids) {
            foreach ($request->speaker_ids as $index => $speakerId) {
                $session->speakers()->attach($speakerId, [
                    'role' => $index === 0 ? 'primary' : 'co-speaker'
                ]);
            }
        }

        $session->load(['track', 'venue', 'speakers']);

        return response()->json([
            'message' => 'Session created successfully',
            'session' => $session
        ], 201);
    }

    public function updateSession(Request $request, Session $session): JsonResponse
    {
        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'start_time' => 'sometimes|required|date',
            'end_time' => 'sometimes|required|date|after:start_time',
            'track_id' => 'nullable|exists:tracks,id',
            'venue_id' => 'nullable|exists:venues,id',
            'type' => 'sometimes|required|in:presentation,workshop,panel,break,networking',
            'description' => 'nullable|string',
            'capacity' => 'nullable|integer|min:1',
            'status' => 'sometimes|in:draft,published,cancelled'
        ]);

        // Check for venue conflicts if venue or time is being updated
        if (($request->has('venue_id') || $request->has('start_time') || $request->has('end_time')) && $request->venue_id) {
            $startTime = $request->start_time ?? $session->start_time;
            $endTime = $request->end_time ?? $session->end_time;
            
            $conflicts = Session::where('venue_id', $request->venue_id)
                ->where('event_id', $session->event_id)
                ->where('id', '!=', $session->id)
                ->where('status', '!=', 'cancelled')
                ->where(function ($query) use ($startTime, $endTime) {
                    $query->whereBetween('start_time', [$startTime, $endTime])
                          ->orWhereBetween('end_time', [$startTime, $endTime])
                          ->orWhere(function ($q) use ($startTime, $endTime) {
                              $q->where('start_time', '<=', $startTime)
                                ->where('end_time', '>=', $endTime);
                          });
                })
                ->exists();

            if ($conflicts) {
                return response()->json([
                    'error' => 'Venue is already booked for this time slot'
                ], 422);
            }
        }

        $session->update($request->all());
        $session->load(['track', 'venue', 'speakers']);

        return response()->json([
            'message' => 'Session updated successfully',
            'session' => $session
        ]);
    }

    public function deleteSession(Session $session): JsonResponse
    {
        $session->delete();

        return response()->json([
            'message' => 'Session deleted successfully'
        ]);
    }

    public function getCalendarData(Request $request): JsonResponse
    {
        $eventId = $request->get('event_id');
        $event = Event::with(['tracks', 'venues'])->findOrFail($eventId);

        return response()->json([
            'event' => $event,
            'tracks' => $event->tracks,
            'venues' => $event->venues,
            'session_types' => [
                'presentation' => 'Presentation',
                'workshop' => 'Workshop',
                'panel' => 'Panel Discussion',
                'break' => 'Break',
                'networking' => 'Networking'
            ]
        ]);
    }

    public function getSessionDetails(Session $session): JsonResponse
    {
        $session->load(['track', 'venue', 'speakers', 'event']);

        return response()->json($session);
    }

    public function bulkUpdateSessions(Request $request): JsonResponse
    {
        $request->validate([
            'sessions' => 'required|array',
            'sessions.*.id' => 'required|exists:sessions,id',
            'sessions.*.start_time' => 'required|date',
            'sessions.*.end_time' => 'required|date|after:sessions.*.start_time'
        ]);

        $updatedSessions = [];

        foreach ($request->sessions as $sessionData) {
            $session = Session::find($sessionData['id']);
            
            // Check for venue conflicts
            if ($session->venue_id) {
                $conflicts = Session::where('venue_id', $session->venue_id)
                    ->where('event_id', $session->event_id)
                    ->where('id', '!=', $session->id)
                    ->where('status', '!=', 'cancelled')
                    ->where(function ($query) use ($sessionData) {
                        $query->whereBetween('start_time', [$sessionData['start_time'], $sessionData['end_time']])
                              ->orWhereBetween('end_time', [$sessionData['start_time'], $sessionData['end_time']])
                              ->orWhere(function ($q) use ($sessionData) {
                                  $q->where('start_time', '<=', $sessionData['start_time'])
                                    ->where('end_time', '>=', $sessionData['end_time']);
                              });
                    })
                    ->exists();

                if ($conflicts) {
                    return response()->json([
                        'error' => "Session '{$session->title}' conflicts with another session in the same venue"
                    ], 422);
                }
            }

            $session->update([
                'start_time' => $sessionData['start_time'],
                'end_time' => $sessionData['end_time']
            ]);

            $updatedSessions[] = $session;
        }

        return response()->json([
            'message' => 'Sessions updated successfully',
            'sessions' => $updatedSessions
        ]);
    }
}