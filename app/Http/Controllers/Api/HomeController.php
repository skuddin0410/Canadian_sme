<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Session;
use App\Models\User;
use App\Models\Notification;
use App\Models\GeneralNotification;


class HomeController extends Controller
{
    public function index(Request $request)
{
    // ================= Banner =================
    $featuredEvent = Event::where('start_date', '>=', now())
    // where('is_featured', true)
    //     ->
        ->inRandomOrder()
        ->first();

    $banner = $featuredEvent ? [
        "id" => $featuredEvent->id,
        "title" => $featuredEvent->title,
        "description" => $featuredEvent->description,
        "location" => $featuredEvent->location,
        "imageUrl" => $featuredEvent->image_url ?? url('images/default_event.jpg'),
        "videoUrl" => $featuredEvent->youtube_link ?? '',
        "startTime" => $featuredEvent->start_date?->toIso8601String(),
        "endTime" => $featuredEvent->end_date?->toIso8601String(),
        "status" => $featuredEvent->status,
    ] : [
        "title" => "No Featured Event Available",
        "imageUrl" => url('images/default_event.jpg'),
    ];

    // ================= Upcoming Session =================
    $upcomingSession = Session::with(['event', 'speakers', 'booth'])
        ->where('start_time', '>=', now())
        ->orderBy('start_time', 'ASC')
        ->first();

    $upcomingSessionData = $upcomingSession ? [
        "id" => $upcomingSession->id,
        "title" => $upcomingSession->title,
        "description" => $upcomingSession->description,
        "start_time" => $upcomingSession->start_time?->toIso8601String(),
        "end_time" => $upcomingSession->end_time?->toIso8601String(),
        "location" => $upcomingSession->booth->title ?? 'No Booth',
        "event" => $upcomingSession->event ? [
        "id" => $upcomingSession->event->id,
        "title" => $upcomingSession->event->title,
        ] : null,
        "speakers" => $upcomingSession->speakers->map(fn ($sp) => ["name" => $sp->name]),
        "status" => "upcoming",
    ] : null;

    // ================= Home Sessions (all sessions from the upcoming session’s event) =================
    $homeSessions = collect();
    if ($upcomingSession && $upcomingSession->event) {
        $homeSessions = Session::with(['speakers','booth'])
            ->where('is_featured', true)
            ->where('event_id', $upcomingSession->event->id)
            ->orderBy('start_time', 'ASC')
            ->get()
            ->map(function ($session) {
                return [
                    "id" => "sub-session-" . $session->id,
                    "title" => $session->title,
                    "description" => $session->description,
                    "keynote" => $session->keynote ?? '',
                    "demoes" => $session->demoes ?? '',
                    "panels" => $session->panels ?? '',
                    "start_time" => $session->start_time?->toIso8601String(),
                    "end_time" => $session->end_time?->toIso8601String(),
                    "workshop_no" => "Workshop NO : " . str_pad($session->id, 2, '0', STR_PAD_LEFT),
                    "location" => !empty($session->booth) ? $session->booth->title : 'No Booth',
                    "status" => $session->status ?? 'Upcoming',
                    "speakers" => $session->speakers->map(fn ($sp) => ["name" => $sp->name]),
                    "isFavorite" => true
                ];
            });
    }

    // ================= Quick Links =================
    // $homeConnections = [
    //     [
    //         "id" => "con-1",
    //         "name" => "Networking",
    //         "avatarUrl" => url('icons/networking.png'),
    //     ],
    //     [
    //         "id" => "con-2",
    //         "name" => "Events & Sessions",
    //         "avatarUrl" => url('icons/events.png'),
    //     ],
    //     [
    //         "id" => "con-3",
    //         "name" => "Resources",
    //         "avatarUrl" => url('icons/resources.png'),
    //     ],
    // ];
    // ================= Home Connections (from session_sponsors) =================
 $homeConnections = User::with('photo')
    ->whereHas('sponsoredSessions') // only users who are sponsors
    ->limit(20)
    ->get()
    ->map(function ($user) {
        return [
            "id" => "sponsor-" . $user->id,
            "name" => $user->full_name ?? $user->name,
            "avatarUrl" => $user->photo
                ? asset('storage/' . $user->photo->file_path . '/' . $user->photo->file_name)
                : url('images/default_avatar.png'),
        ];
    });


    // ================= Notifications =================
    $user = auth()->user();

    $notificationsQuery = GeneralNotification::query()
        ->where(function ($q) use ($user) {
            $q->whereNull('user_id'); // broadcast
            if ($user) {
                $q->orWhere('user_id', $user->id);
            }
        })
        ->latest();

    $notificationsList = $notificationsQuery->take(20)->get()->map(function ($n) {
        return [
            "id" => $n->id,
            "title" => $n->title,
            "message" => $n->body,
            "is_read" => $n->read_at ? true : false,
            "created_at" => $n->created_at->toDateTimeString(),
            "related" => [
                "type" => $n->related_type,
                "id" => $n->related_id,
                "name" => $n->related_name,
            ],
            "meta" => $n->meta,
        ];
    });

    $notifications = [
        "count" => $notificationsQuery->count(),
        "hasNew" => $notificationsQuery->whereNull('read_at')->exists(),
        "data" => $notificationsList,
    ];

    // ================= My Stats =================
    $myStats = [
        "totalAgents" => User::count(),
        "totalConnections" => User::count(),
        "totalSessionAttendee" => User::whereHas("roles", function ($q) {
            $q->where("name", "Attendee");
        })->count(),
    ];

    return response()->json([
        "banner" => $banner,
        "upcomingSession" => $upcomingSessionData,
        "home_sessions" => $homeSessions,
        "home_connections" => $homeConnections,
        "myStats" => $myStats,
        "notifications" =>  $notifications
    ]);
}









 
// public function index(Request $request)
// {
   
//     $featuredEvent = Event::where('is_featured', true)
//         ->where('start_date', '>=', now())
//         ->inRandomOrder()
//         ->first();

//     $banner = $featuredEvent ? [
//         "id" => $featuredEvent->id,
//         "title" => $featuredEvent->title,
//         "description" => $featuredEvent->description,
//         "location" => $featuredEvent->location,
//         "imageUrl" => $featuredEvent->image_url ?? url('images/default_event.jpg'),
//         "videoUrl" => $featuredEvent->youtube_link ?? '',
//         "startTime" => $featuredEvent->start_date?->toIso8601String(),
//         "endTime" => $featuredEvent->end_date?->toIso8601String(),
//         "status" => $featuredEvent->status,
//     ] : [
//         "title" => "No Featured Event Available",
//         "imageUrl" => url('images/default_event.jpg'),
//     ];

 
//     $upcomingEvent = Event::where('start_date', '>=', now())
//         ->orderBy('start_date', 'ASC')
//         ->first();

//     $upcomingEventData = $upcomingEvent ? [
//         "title" => $upcomingEvent->title,
//         "startDateTime" => $upcomingEvent->start_date->toIso8601String(),
//         "status" => "upcoming"
//     ] : null;

 
//     $homeSessions = collect(); 
//     if ($upcomingEvent) {
//         $homeSessions = Session::with(['speakers','booth'])
//             ->where('event_id', $upcomingEvent->id)
//             ->orderBy('start_time', 'ASC')
//             ->get()
//             ->map(function ($session) {
//                 return [
//                     "id" => "sub-session-" . $session->id,
//                     "title" => $session->title,
//                     "description" => $session->description,
//                     "keynote" => $session->keynote ?? '',
//                     "demoes" => $session->demoes ?? '',
//                     "panels" => $session->panels ?? '',
//                     "start_time" => $session->start_time?->toIso8601String(),
//                     "end_time" => $session->end_time?->toIso8601String(),
//                     "workshop_no" => "Workshop NO : " . str_pad($session->id, 2, '0', STR_PAD_LEFT),
//                     "location" => !empty($session->booth) ? $session->booth->title : 'No Booth',
//                     "status" => $session->status ?? 'Upcoming',
//                     "speakers" => $session->speakers->map(fn ($sp) => ["name" => $sp->name]),
//                     "isFavorite" => true
//                 ];
//             });
//     }

//     $homeConnections = [
//         [
//             "id" => "con-1",
//             "name" => "Networking",
//             "avatarUrl" => url('icons/networking.png'),
//         ],
//         [
//             "id" => "con-2",
//             "name" => "Events & Sessions",
//             "avatarUrl" => url('icons/events.png'),
//         ],
//         [
//             "id" => "con-3",
//             "name" => "Resources",
//             "avatarUrl" => url('icons/resources.png'),
//         ],
//     ];
//     $allNotifications = $query->take(20)->get();

//     $notifications = [
//         "count" => $allNotifications->count(),
//         "hasNew" => $allNotifications->whereNull('read_at')->isNotEmpty(),
//         "data" => $allNotifications->map(function ($n) {
//             return [
//                 "id" => $n->id,
//                 "title" => $n->title,
//                 "message" => $n->body,
//                 "is_read" => !is_null($n->read_at),
//                 "created_at" => $n->created_at->toDateTimeString(),
//                 "related" => [
//                     "type" => $n->related_type,
//                     "id"   => $n->related_id,
//                     "name" => $n->related_name,
//                 ],
//                 "meta" => $n->meta ? json_decode($n->meta, true) : null,
//             ];
//         }),
//     ];

//     $notifications = [
//     "count" => 3,  // static count
//     "hasNew" => true, // static flag for new notifications
//     "data" => [
//         [
//             "id" => 1,
//             "title" => "Welcome to the platform",
//             "message" => "Thanks for joining us! Explore features and get started.",
//             "is_read" => false,
//             "created_at" => now()->subMinutes(10)->toDateTimeString()
//         ],
//         [
//             "id" => 2,
//             "title" => "New Event Available",
//             "message" => "A new session has been scheduled. Don’t miss it!",
//             "is_read" => true,
//             "created_at" => now()->subHours(2)->toDateTimeString()
//         ],
//         [
//             "id" => 3,
//             "title" => "Profile Reminder",
//             "message" => "Complete your profile to get personalized recommendations.",
//             "is_read" => true,
//             "created_at" => now()->subDay()->toDateTimeString()
//         ],
//     ]
// ];


//     $myStats = [
//         "totalAgents" => User::count(),
//         "totalConnections" => User::count(),
//         "totalSessionAttendee" => User::whereHas("roles", function ($q) {
//             $q->where("name", "Attendee");
//         })->count(),
        
//     ];

//     return response()->json([
//         "banner" => $banner,
//         "upcomingEvent" => $upcomingEventData,
//         "home_sessions" => $homeSessions,
//         "home_connections" => $homeConnections,
//         "myStats" => $myStats,
//         "notifications" =>  $notifications
//     ]);
// }


}
