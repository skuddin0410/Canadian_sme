<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Session;
use App\Models\User;
use App\Models\Notification;
use App\Models\GeneralNotification;
use Tymon\JWTAuth\Facades\JWTAuth;


class HomeController extends Controller
{
    public function index(Request $request)
{
    // ================= Banner =================
    $featuredEvent = Event::where('start_date', '>=', now())
    
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


public function getNotifications(Request $request)
{
   
    if (!$user = JWTAuth::parseToken()->authenticate()) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized'
        ], 401);
    }

    
    $isSpeaker = $user->hasRole('Speaker');

    
    $photo = $user->photo;
   
    $userPhoto = $user->photo
        ? Storage::url('users/' . $user->photo->file_name)
        : url('images/default.jpg');

    
    $notifications = GeneralNotification::query()
        ->where(function ($q) use ($user) {
            $q->whereNull('user_id');          
            $q->orWhere('user_id', $user->id); 
        })
        ->latest()
        ->take(20)
        ->get()
        ->map(function ($n) use ($isSpeaker, $userPhoto) {
            return [
                'imageUrl'   => $userPhoto, 
                'heading'    => $n->title ?? $n->body ?? '',
                'created_at' => $n->created_at?->toIso8601String(),
                'isRead'     => $n->read_at ? true : false,
                'isSpeaker'  => $isSpeaker,
            ];
        });

    return response()->json([
        'success' => true,
        'data'    => $notifications,
    ], 200);
}
public function getSession($sessionId)
{
    try {
          if (!$user = JWTAuth::parseToken()->authenticate()) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized'
        ], 401);
    }
        $session = Session::with(['event', 'speakers', 'booth'])
            ->find($sessionId);

        if (! $session) {
            return response()->json([
                "success" => false,
                "message" => "Session not found!",
            ], 404);
        }

        $sessionData = [
            "id" => $session->id,
            "title" => $session->title,
            "description" => $session->description,
          
            "status" => $session->status ?? 'Upcoming',
            
            "speakers" => $session->speakers->map(fn ($sp) => [
                "id" => $sp->id,
                "name" => $sp->name,
           

            ]),
             "isFavorite" =>  $session->is_featured == 1,
            "isInAgenda" => true, 
        ];
        

        return response()->json([
            "success" => true,
            "data" => $sessionData,
        ]);

    } catch (\Exception $e) {
        return response()->json([
            "success" => false,
            "message" => $e->getMessage(),
        ], 500);
    }
}
public function getConnections(Request $request)
{
    try {
        if (!$user = JWTAuth::parseToken()->authenticate()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        
        $connections = User::with(['photo', 'company']) // eager load relations
            ->limit(50)
            ->get()
            ->map(function ($connection) {
                return [
                    "id" => (string) $connection->id,
                    "name" => $connection->full_name ?? $connection->name,
                    "connection_role" => $connection->getRoleNames(),
                    "company_name" =>$connection->company ? $connection->company->name : null,
                    "connection_image" => $connection->photo ? $connection->photo->file_path : null,
                       
                ];
            });

        return response()->json([
            "success" => true,
            "data" => $connections
        ]);

    } catch (\Exception $e) {
        return response()->json([
            "success" => false,
            "message" => $e->getMessage(),
        ], 500);
    }
}





}
