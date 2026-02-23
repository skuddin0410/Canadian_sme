<?php
namespace App\Http\Controllers\Api;

use App\Models\Event;
use App\Http\Requests\EventRequest;
use App\Http\Resources\EventResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;

class EventController extends Controller
{
    // public function index()
    // {
    //     return EventResource::collection(Event::latest()->paginate(10));
    // }

    public function index(Request $request)
    {
        $type = $request->type; // past | ongoing | upcoming
        $today = Carbon::today();

        // $query = Event::query();

        // if (! $requester = JWTAuth::parseToken()->authenticate()) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Unauthorized',
        //         'data'    => collect(),
        //     ], 401);
        // }
        // $userId = $requester->id;
        // // dd($userId);
        
        // $query = Event::query()
        //     ->withExists([
        //         'entityLinks as is_registered' => function ($q) use ($userId) {
        //             $q->where('entity_type', 'users')
        //             ->where('entity_id', $userId);
        //         }
        //     ]);

        $query = Event::query();

        if ($type === 'past') {
            $query->whereDate('end_date', '<', $today);
        }

        if ($type === 'ongoing') {
            $query->whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '>=', $today);
        }

        if ($type === 'upcoming') {
            $query->whereDate('start_date', '>', $today);
        }

        return EventResource::collection($query->latest()->paginate(6));
    }

    public function store(EventRequest $request)
    {
        $event = Event::create($request->validated());
        return new EventResource($event);
    }

    public function show(Event $event)
    {
        return new EventResource($event);
    }

    public function update(EventRequest $request, Event $event)
    {
        $event->update($request->validated());
        return new EventResource($event);
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return response()->json(['message' => 'Event deleted successfully.']);
    }
}
