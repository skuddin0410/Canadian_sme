<?php
namespace App\Http\Controllers\Api;

use App\Models\Event;
use App\Http\Requests\EventRequest;
use App\Http\Resources\EventResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EventController extends Controller
{
    public function index()
    {
        return EventResource::collection(Event::latest()->paginate(10));
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
