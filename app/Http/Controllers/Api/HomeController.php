<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Session;
use App\Models\User;
use App\Models\Notification;


class HomeController extends Controller
{
    // public function index(Request $request)
    // {
       
    //     $banner = [
    //         "title" => "Welcome to the Tech Event",
    //         "imageUrl" => url('images/event_banner.jpg'),
    //         "videoUrl" => url('videos/event_banner.mp4'),
    //         "startTime" => "2025-08-15T08:00:00Z",
    //         "endTime" => "2025-08-15T10:00:00Z",
    //     ];

       
    //     $upcomingEvent = Event::where('start_date', '>=', now())
    //         ->orderBy('start_date', 'ASC')
    //         ->first();

    //     $upcomingEventData = $upcomingEvent ? [
    //         "title" => $upcomingEvent->title,
    //         "startDateTime" => $upcomingEvent->start_date->toIso8601String(),
    //         "status" => "upcoming"
    //     ] : null;

        
    //     $homeSessions = Session::with('speakers')
    //         ->orderBy('start_time', 'ASC')
    //         ->get()
    //         ->map(function($session){
    //             return [
    //                 "id" => "sub-session-" . $session->id,
    //                 "title" => $session->title,
    //                 "description" => $session->description,
    //                 "keynote" => $session->keynote ?? '',
    //                 "demoes" => $session->demoes ?? '',
    //                 "panels" => $session->panels ?? '',
    //                 "start_time" => $session->start_time->toIso8601String(),
    //                 "end_time" => $session->end_time->toIso8601String(),
    //                 "worshop_no" => "Workshop NO : " . str_pad($session->id, 2, '0', STR_PAD_LEFT),
    //                 "location" => $session->location ?? '',
    //                 "status" => $session->status ?? 'Upcoming',
    //                 "speakers" => $session->speakers->map(fn($sp) => ["name" => $sp->name]),
    //                 "isFavorite" => true 
    //             ];
    //         });

        
    //     $homeConnections = User::take(5)->get()->map(function($user){
    //         return [
    //             "id" => "con-" . $user->id,
    //             "name" => $user->name,
    //             "avatarUrl" => $user->avatar ?? url('avatars/default.png'),
    //         ];
    //     });

     
    //     $myStats = [
    //         "totalAgents" => User::count(),
    //         "totalConnections" => User::count(), 
    //         "totalSessionAttendee" => Session::sum('attendee_count') ?? 0
    //     ];

       
    //     $notifications = [
    //         "count" => Notification::where('user_id', $request->user()->id)->count(),
    //         "hasNew" => Notification::where('user_id', $request->user()->id)->where('is_read', false)->exists()
    //     ];

    //     return response()->json([
    //         "banner" => $banner,
    //         "upcomingEvent" => $upcomingEventData,
    //         "home_sessions" => $homeSessions,
    //         "home_connections" => $homeConnections,
    //         "myStats" => $myStats,
    //         "notifications" => $notifications
    //     ]);
    // }
 
   public function index(Request $request)
{
   
    $featuredEvent = Event::where('is_featured', true)
        ->where('start_date', '>=', now())
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

 
    $upcomingEvent = Event::where('start_date', '>=', now())
        ->orderBy('start_date', 'ASC')
        ->first();

    $upcomingEventData = $upcomingEvent ? [
        "title" => $upcomingEvent->title,
        "startDateTime" => $upcomingEvent->start_date->toIso8601String(),
        "status" => "upcoming"
    ] : null;

 
    $homeSessions = collect(); 
    if ($upcomingEvent) {
        $homeSessions = Session::with(['speakers','booth'])
            ->where('event_id', $upcomingEvent->id)
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

    $homeConnections = [
        [
            "id" => "con-1",
            "name" => "Networking",
            "avatarUrl" => url('icons/networking.png'),
        ],
        [
            "id" => "con-2",
            "name" => "Events & Sessions",
            "avatarUrl" => url('icons/events.png'),
        ],
        [
            "id" => "con-3",
            "name" => "Resources",
            "avatarUrl" => url('icons/resources.png'),
        ],
    ];
    $notifications = [
    "count" => 3,  // static count
    "hasNew" => true, // static flag for new notifications
    "data" => [
        [
            "id" => 1,
            "title" => "Welcome to the platform",
            "message" => "Thanks for joining us! Explore features and get started.",
            "is_read" => false,
            "created_at" => now()->subMinutes(10)->toDateTimeString()
        ],
        [
            "id" => 2,
            "title" => "New Event Available",
            "message" => "A new session has been scheduled. Don’t miss it!",
            "is_read" => true,
            "created_at" => now()->subHours(2)->toDateTimeString()
        ],
        [
            "id" => 3,
            "title" => "Profile Reminder",
            "message" => "Complete your profile to get personalized recommendations.",
            "is_read" => true,
            "created_at" => now()->subDay()->toDateTimeString()
        ],
    ]
];


    $myStats = [
        "totalAgents" => User::count(),
        "totalConnections" => User::count(),
        "totalSessionAttendee" => User::whereHas("roles", function ($q) {
            $q->where("name", "Attendee");
        })->count(),
        
    ];

    return response()->json([
        "banner" => $banner,
        "upcomingEvent" => $upcomingEventData,
        "home_sessions" => $homeSessions,
        "home_connections" => $homeConnections,
        "myStats" => $myStats,
        "notifications" =>  $notifications
    ]);
}


}
