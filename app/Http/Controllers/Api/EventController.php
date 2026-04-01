<?php

namespace App\Http\Controllers\Api;

use App\Models\Event;
use App\Http\Requests\EventRequest;
use App\Http\Resources\EventResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\GalleryItem;

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
    public function eventGallery(Request $request, $eventId)
    {
        $type     = $request->query('type');   
        $page     = $request->query('page');   
        $perPage  = $request->query('per_page', 10);

        $query = GalleryItem::where('event_id', $eventId)
            ->where('is_approved', true);

        //  Optional type filter
        if ($type && in_array($type, ['image', 'video', 'document'])) {
            $query->where('file_type', $type);
        }

        $query->latest();

        
        if ($page) {

            $items = $query->paginate($perPage);

            $data = $items->getCollection()->map(function ($item) {
                return [
                    'id'          => $item->id,
                    'file_name'   => $item->file_name,
                    'file_type'   => $item->file_type,
                    'file_url'    => asset('storage/' . $item->file_path),
                    'uploaded_by' => optional($item->user)->name,
                    'uploaded_at' => $item->created_at->toDateTimeString(),
                ];
            });

            return response()->json([
                'success'    => true,
                'event_id'   => (int) $eventId,
                'filter'     => $type ?? 'all',
                'pagination' => [
                    'current_page' => $items->currentPage(),
                    'per_page'     => $items->perPage(),
                    'total'        => $items->total(),
                    'last_page'    => $items->lastPage(),
                ],
                'data' => $data,
            ]);
        }

        
        $items = $query->get();

        if ($items->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No gallery items found.'
            ], 404);
        }

        $data = $items->map(function ($item) {
            return [
                'id'          => $item->id,
                'file_name'   => $item->file_name,
                'file_type'   => $item->file_type,
                'file_url'    => asset('storage/' . $item->file_path),
                'uploaded_by' => optional($item->user)->name,
                'uploaded_at' => $item->created_at->toDateTimeString(),
            ];
        });

        return response()->json([
            'success'  => true,
            'event_id' => (int) $eventId,
            'filter'   => $type ?? 'all',
            'total'    => $data->count(),
            'data'     => $data,
        ]);
    }

    // public function eventGallery(Request $request, $eventId)
    // {
    //     $type = $request->query('type'); 

    //     $query = GalleryItem::where('event_id', $eventId)
    //         ->where('is_approved', true);


    //     if ($type && in_array($type, ['image', 'video', 'document'])) {
    //         $query->where('file_type', $type);
    //     }

    //     $items = $query->latest()->get();

    //     if ($items->isEmpty()) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'No gallery items found.'
    //         ], 404);
    //     }

    //     $data = $items->map(function ($item) {
    //         return [
    //             'id'          => $item->id,
    //             'file_name'   => $item->file_name,
    //             'file_type'   => $item->file_type,
    //             'file_url'    => asset('storage/' . $item->file_path),
    //             'uploaded_by' => optional($item->user)->name,
    //             'uploaded_at' => $item->created_at->toDateTimeString(),
    //         ];
    //     });

    //     return response()->json([
    //         'success'  => true,
    //         'event_id' => (int) $eventId,
    //         'filter'   => $type ?? 'all',
    //         'total'    => $data->count(),
    //         'data'     => $data,
    //     ]);
    // }
}
