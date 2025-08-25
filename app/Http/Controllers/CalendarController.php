<?php
namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Session;
use App\Models\Booth;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $eventId = $request->get('event_id');
        $event = Event::findOrFail($eventId);
        $booths = Booth::all();

        $speakers = User::select('id','name')->whereHas("roles", function ($q) {
                    $q->where("name", 'Speaker');
                })->orderBy('created_at', 'DESC')->get();

        $exhibitors = User::whereHas("roles", function ($q) {
                    $q->where("name", 'Exhibitor');
                })->orderBy('created_at', 'DESC')->get();

        $sponsors = User::whereHas("roles", function ($q) {
                    $q->where("name", 'Sponsors');
                })->orderBy('created_at', 'DESC')->get();
        
        return view('calendar.index', compact('event','speakers','exhibitors','sponsors','booths'));
    }

    public function speakers(Request $request)
    {

        $speakers = User::select('id','name')->whereHas("roles", function ($q) {
                    $q->whereIn("name", ['Speaker']);
                })->orderBy('created_at', 'DESC')->get();
        return response()->json($speakers);
        
    }

    public function exhibitors(Request $request)
    {

        $exhibitors = User::select('id','name')->whereHas("roles", function ($q) {
                    $q->whereIn("name", ['Exhibitor']);
                })->orderBy('created_at', 'DESC')->get();
        return response()->json($exhibitors);
        
    }

    public function sponsors(Request $request)
    {

        $sponsors = User::select('id','name')->whereHas("roles", function ($q) {
                    $q->whereIn("name", ['Sponsors']);
                })->orderBy('created_at', 'DESC')->get();
        return response()->json($sponsors);
        
    }

    public function getSessions(Request $request): JsonResponse
    {
        $eventId = $request->get('event_id');
        $start = $request->get('start');
        $end = $request->get('end');

        $query = Session::with(['booth', 'speakers','exhibitors','sponsors'])
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
                'status' => $session->status,
                'start' => $session->start_time->toISOString(),
                'end' => $session->end_time->toISOString(),
                'backgroundColor' =>  $session->color,
                'borderColor' =>  $session->color,
                'textColor' => '#ffffff',
                'description' => $session->description,
                'location' => $session->location,
                'track' => $session->track,
                'type' => $session->type,
                'venue' => !empty($session->booth) ? $session->booth->title : 'No Booth' ,
                'venue_id' => !empty($session->booth) ? $session->booth->id : null ,
                'capacity' => $session->capacity,
                'duration' => $session->getDurationInMinutes(),
                'keynote' => $session->keynote,
                'demoes' => $session->demoes,
                'panels' => $session->panels,

                'extendedProps' => [
                    'description' => $session->description,
                    'type' => $session->type,
                    'venue' => !empty($session->booth) ? $session->booth->title .'('. $session->booth->booth_number .')': 'No Booth' ,
                    'venue_id' => !empty($session->booth) ? $session->booth->id : null ,
                    'speakers' => $session->speakers->map(function ($speaker) {
                        return [
                            'id' => $speaker->id,
                            'name' => $speaker->full_name,
                            'role' => $speaker->pivot->role
                        ];
                    }),
                    'sponsors' => $session->sponsors->map(function ($speaker) {
                        return [
                            'id' => $speaker->id,
                            'name' => $speaker->full_name,
                            'role' => $speaker->pivot->role
                        ];
                    }),
                    'exhibitors' => $session->exhibitors->map(function ($speaker) {
                        return [
                            'id' => $speaker->id,
                            'name' => $speaker->full_name,
                            'role' => $speaker->pivot->role
                        ];
                    }),
                    'capacity' => $session->capacity,
                    'duration' => $session->getDurationInMinutes(),
                    'status' => $session->status,
                    'keynote' => $session->keynote,
                    'demoes' => $session->demoes,
                    'panels' => $session->panels,
                ]
            ];
        });

        return response()->json($events);
    }

    public function createSession(Request $request): JsonResponse
    {   
        $speakerIds = collect($request->all())
        ->filter(fn($value, $key) => str_starts_with($key, 'speaker_ids['))
        ->values()
        ->all();

        $request->merge([
         'speaker_ids' => $speakerIds
        ]);


        $request->validate([
            'event_id' => 'required|exists:events,id',
            'title' => 'required|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'booth_id' => 'required|exists:booths,id',
            'type' => 'required|in:presentation,workshop,panel,break,networking',
            'description' => 'nullable|string',
            'capacity' => 'required|integer|min:1',
            'speaker_ids' => 'required|array',
            'speaker_ids.*' => 'exists:users,id'
        ]);

        // Check for venue conflicts
        if ($request->booth_id) {
            $conflicts = Session::where('booth_id', $request->booth_id)
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
        if ($speakerIds) {
            foreach ($speakerIds as $index => $speakerId) {
                $session->speakers()->attach($speakerId);
            }
        }

        $session->load(['booth', 'speakers','exhibitors','sponsors']);

        return response()->json([
            'message' => 'Session created successfully',
            'session' => $session
        ], 201);
    }

    public function updateSession(Request $request, Session $session): JsonResponse
    {   
        $speakerIds = collect($request->all())
        ->filter(fn($value, $key) => str_starts_with($key, 'speaker_ids['))
        ->values()
        ->all();

        $request->merge([
         'speaker_ids' => $speakerIds
        ]);

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'start_time' => 'sometimes|required|date',
            'end_time' => 'sometimes|required|date|after:start_time',
            'booth_id' => 'nullable|exists:booths,id',
            'type' => 'sometimes|required|in:presentation,workshop,panel,break,networking',
            'description' => 'nullable|string',
            'capacity' => 'nullable|integer|min:1',
            'status' => 'sometimes|in:draft,published,cancelled'
        ]);

        // Check for venue conflicts if venue or time is being updated
        if (($request->has('booth_id') || $request->has('start_time') || $request->has('end_time')) && $request->booth_id) {
            $startTime = $request->start_time ?? $session->start_time;
            $endTime = $request->end_time ?? $session->end_time;
            
            $conflicts = Session::where('booth_id', $request->booth_id)
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
        $session->speakers()->detach();
        if ($speakerIds) {
            foreach ($speakerIds as $index => $speakerId) {
                $session->speakers()->detach($speakerId);
                $session->speakers()->attach($speakerId);
            }
        }

        $session->load(['booth', 'speakers']);

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
        $session->load(['venue', 'speakers', 'event']);

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
            if ($session->booth_id) {
                $conflicts = Session::where('booth_id', $session->booth_id)
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

    public function eventSessionList(Request $request){
       $session =Session::where('event_id',$request->event_id)->get(); 
       return response()->json(['sessions'=>$session , 'length'=>count($session)]);

    }
}