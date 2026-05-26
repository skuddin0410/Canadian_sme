<?php

namespace App\Http\Controllers\Api;

use App\Models\Event;
use App\Models\EventGuide;
use App\Http\Requests\EventRequest;
use App\Http\Resources\EventResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\GalleryItem;
use Illuminate\Support\Str;

class EventController extends Controller
{
    // public function index()
    // {
    //     return EventResource::collection(Event::latest()->paginate(10));
    // }

    public function index(Request $request)
    {
        $type = $request->type; // past | ongoing | upcoming
        $search = trim((string) $request->query('q', ''));
        $perPage = (int) $request->query('per_page', 6);
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

        $query = Event::query()
            ->with([
                'photo', 
                'splashScreen.iosIphone', 
                'splashScreen.iosIpad', 
                'splashScreen.androidHdpi', 
                'splashScreen.androidMdpi', 
                'splashScreen.androidXhdpi', 
                'splashScreen.androidXxhdpi'
            ])
            ->where('status', 'published')
            ->where('visibility', 'listed')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($searchQuery) use ($search) {
                    $searchQuery->where('title', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%")
                        ->orWhere('tags', 'like', "%{$search}%")
                        ->orWhere('tracks', 'like', "%{$search}%");
                });
            });

        if ($type === 'past') {
            $query->whereDate(\DB::raw('COALESCE(end_date, start_date)'), '<', $today)
                ->orderBy('start_date', 'DESC');
        }

        if ($type === 'ongoing') {
            $query->whereDate('start_date', '<=', $today)
                ->whereDate(\DB::raw('COALESCE(end_date, start_date)'), '>=', $today)
                ->orderBy('start_date', 'DESC');
        }

        if ($type === 'upcoming') {
            $query->whereDate('start_date', '>', $today)
                ->orderBy('start_date', 'ASC');
        }

        if (! in_array($type, ['past', 'ongoing', 'upcoming'], true)) {
            $query->orderBy('start_date', 'DESC');
        }

        return EventResource::collection($query->paginate($perPage));
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
                    'file_url'    => $item->file_path,
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
                'file_url'    => $item->file_path,
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

    public function eventGuide(Event $event)
    {
        $guides = EventGuide::with(['documentFile'])
            ->where(function ($query) use ($event) {
                $query->where('event_id', $event->id)
                    ->orWhereNull('event_id');
            })
            ->orderByRaw("CASE WHEN category IS NULL OR category = '' THEN 1 ELSE 0 END")
            ->orderBy('category', 'ASC')
            ->orderBy('id', 'DESC')
            ->get();

        $downloadGuides = $guides->filter(function ($guide) {
            $section = Str::lower(trim((string) $guide->category));
            $hasDownload = !empty($guide->weblink) || !empty($guide->doc) || !empty(optional($guide->documentFile)->file_path);

            return $section === 'files to download' || ($hasDownload && blank($guide->type));
        })->values();

        $guideSections = $guides
            ->reject(fn ($guide) => $downloadGuides->contains('id', $guide->id))
            ->groupBy(function ($guide) {
                return trim((string) ($guide->category ?: 'Event Guide'));
            })
            ->map(function ($items, $sectionTitle) {
                return [
                    'title' => $sectionTitle,
                    'items' => $items->values()->map(function ($guide) {
                        $fileUrl = optional($guide->documentFile)->file_path;

                        return [
                            'id' => $guide->id,
                            'title' => $guide->title,
                            'description' => $guide->type,
                            'link' => $guide->weblink,
                            'file_url' => $fileUrl,
                            'file_name' => $fileUrl ? basename(parse_url($fileUrl, PHP_URL_PATH)) : null,
                        ];
                    }),
                ];
            })
            ->values();

        $downloads = $downloadGuides->map(function ($guide) {
            $fileUrl = optional($guide->documentFile)->file_path;
            $downloadUrl = $guide->weblink ?: $fileUrl;

            return [
                'id' => $guide->id,
                'section' => $guide->category ?: 'Files to Download',
                'title' => $guide->title,
                'description' => $guide->type,
                'link' => $guide->weblink,
                'file_url' => $fileUrl,
                'download_url' => $downloadUrl,
                'file_name' => $fileUrl ? basename(parse_url($fileUrl, PHP_URL_PATH)) : null,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'event' => [
                'id' => $event->id,
                'title' => $event->title,
                'slug' => $event->slug,
            ],
            'sections' => $guideSections,
            'downloads' => $downloads,
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
