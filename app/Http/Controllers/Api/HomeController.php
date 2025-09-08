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
use Illuminate\Support\Facades\Storage;
use App\Models\UserAgenda;
use App\Models\FavoriteSession;
use App\Models\UserConnection;



class HomeController extends Controller
{
    public function index(Request $request)
{
    // ================= Banner =================
    $featuredEvent = Event::with('photo')->first();

    $banner = $featuredEvent ? [
        "id" => $featuredEvent->id,
        "title" => $featuredEvent->title,
        "description" => $featuredEvent->description,
        "location" => $featuredEvent->location,
        "imageUrl" => !empty($featuredEvent->photo) ? $featuredEvent->photo->file_path : asset('images/default.png'),
        "videoUrl" => $featuredEvent->youtube_link ?? '',
        "startTime" => $featuredEvent->start_date ?? '',
        "endTime" => $featuredEvent->end_date ?? '',
        "status" => $featuredEvent->status,
    ] : [
        "title" => "No Featured Event Available",
        "imageUrl" => asset('images/default.png'),
    ];

    // ================= Upcoming Session =================
    $upcomingSession = Session::with('attendees','sponsors')->where('start_time', '>=', now())
        ->orderBy('start_time', 'ASC')
        ->first();

    $upcomingSessionData = $upcomingSession ? [
        "id" => $upcomingSession->id,
        "title" => $upcomingSession->title,
        "description" => $upcomingSession->description,
        "startDateTime" => $upcomingSession->start_time ?? '',
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
                    "id" => $session->id,
                    "title" => $session->title,
                    "description" => $session->description,
                    "keynote" => $session->keynote ?? '',
                    "demoes" => $session->demoes ?? '',
                    "panels" => $session->panels ?? '',
                    "start_time" => $session->start_time ?? '',
                    "end_time" => $session->end_time ?? '',
                    "workshop_no" => "Workshop NO : " . str_pad($session->id, 2, '0', STR_PAD_LEFT),
                    "location" => !empty($session->location) ? $session->location: '',
                    "status" => $session->status ?? 'Upcoming',
                    "speakers" => $session->speakers->map(fn ($sp) => ["name" => $sp->name, "image"=> !empty($sp->photo) ? $sp->photo->file_path : asset('images/default.png')]),
                    "isFavorite" => isFavorite($session->id)
                ];
            });
    }

   
    // ================= Home Connections (from session_sponsors) =================
 $homeConnections = User::with('photo')
    ->whereHas("roles", function ($q) {
        $q->whereIn("name", ['Attendee','Speaker']);
    })
    ->limit(3)
    ->get()
    ->map(function ($user) {
        return [
            "id" => $user->id,
            "name" => $user->full_name ?? $user->name,
            "avatarUrl" => $user->photo && $user->photo->file_path ? $user->photo->file_path :asset('images/default.png')
        ];
    });


    // ================= Notifications =================
    $user = auth()->user();
    
   
    $notificationsQuery = GeneralNotification::where('is_read', 0)
    ->where(function ($q) use ($user) {
        $q->where('user_id', $user->id);
    })->latest();

    $notificationsList = $notificationsQuery->get()->map(function ($n) {
        return [
            "id" => $n->id,
            "title" => $n->title,
            "message" => $n->body,
            "is_read" => $n->is_read==1 ? true : false
        ];
    });
    $notifications = [
        "count" => $notificationsList->count(),
        "hasNew" => $notificationsQuery->where('is_read',0)->exists(),
        "data" => $notificationsList,
    ];

    //================= My Stats =================
    $myStats = [
        "totalAgents" => !empty($upcomingSession->sponsors) ? $upcomingSession->sponsors->count() : 0,
        "totalConnections" => User::count(),
        "totalSessionAttendee" => !empty($upcomingSession->attendees) ? $upcomingSession->attendees->count() : 0,
    ];

    return response()->json([
        "banner" => $banner,
        "upcomingEvent" => $upcomingSessionData,
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
    $userPhoto = !empty($user->photo) ? $user->photo->file_path : asset('images/default.png');
    
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
                'heading'    => $n->title  ?? '',
                'message'    => $n->body  ?? '',
                'created_at' => $n->created_at?->toIso8601String(),
                'isRead'     => $n->read_at ? true : false,
                'isSpeaker'  => $isSpeaker,
            ];
        });

    return response()->json($notifications, 200);
}

public function getAllSession()
{
    try {

        if (!$user = JWTAuth::parseToken()->authenticate()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }   
        $now = now();

        $sessions = Session::with(['speakers', 'booth'])
            ->where('event_id', 1)
            ->orderBy('start_time', 'ASC')
            ->get()
            ->groupBy(function ($session) {
                // group by formatted date
                return userDateFormat($session->start_time);
            })
            ->map(function ($group, $formattedDate) use ($now) {
                return [
                    'date' => $formattedDate,
                    'session_list' => $group->map(function ($session) use ($now) {
                        // Determine status
                        if ($session->start_time > $now) {
                            $status = 'Upcoming';
                        } elseif ($session->start_time <= $now && $session->end_time >= $now) {
                            $status = 'Ongoing';
                        } else {
                            $status = 'Completed';
                        }

                        return [
                            "id"          => $session->id,
                            "title"       => $session->title,
                            "description" => $session->description,
                            "keynote"     => $session->keynote ?? '',
                            "demoes"      => $session->demoes ?? '',
                            "panels"      => $session->panels ?? '',
                            "start_time"  => $session->start_time ?? '',
                            "end_time"    => $session->end_time ?? '',
                            "workshop_no" => "Workshop NO : " . str_pad($session->id, 2, '0', STR_PAD_LEFT),
                            "location"    => $session->location ?? '',
                            "status"      => $status,
                            "speakers"    => $session->speakers->map(fn ($sp) => ["name" => $sp->name ,"image"=> !empty($sp->photo) ? $sp->photo->file_path : asset('images/default.png')])->values(),
                            "isFavorite"  => isFavorite($session->id),
                        ];
                    })->values()
                ];
            })
            ->values(); // reset keys



            if (! $sessions) {
                return response()->json([
                    "success" => false,
                    "message" => "Session not found!",
                ], 404);
            }
        return response()->json($sessions);
    

    } catch (\Exception $e) {
        return response()->json(["message" => $e->getMessage()]);
    }
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
        
        $session = Session::with(['speakers'])
            ->find($sessionId);

        if (! $session) {
            return response()->json([
                "success" => false,
                "message" => "Session not found!",
            ], 404);
        }

        $now = now();

        // Calculate status
        if ($session->start_time > $now) {
            $status = 'Upcoming';
        } elseif ($session->start_time <= $now && $session->end_time >= $now) {
            $status = 'Ongoing';
        } else {
            $status = 'Completed';
        }

        $sessionData = [
            "id"          => $session->id,
            "title"       => $session->title,
            "description" => $session->description,
            "keynote"     => $session->keynote,
            "demoes"      => $session->demoes,
            "panels"      => $session->panels,
            "start_time"  => $session->start_time,
            "end_time"    => $session->end_time,
            "workshop_no" => "Workshop NO :" . $session->id,
            "location"    => $session->location,
            "status"      => $status,
            "speakers"    => $session->speakers->map(fn ($sp) => [
                "id"   => $sp->id,
                "name" => $sp->name,
                "image"=> !empty($sp->photo) ? $sp->photo->file_path : asset('images/default.png')
            ]),
            "isFavorite"  => isFavorite($session->id),
            "isInAgenda"  => isAgenda($session->id),
        ];
        

        return response()->json($sessionData);

    } catch (\Exception $e) {
        return response()->json(["message" => $e->getMessage()]);
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

        
        $user = auth()->user();
        
        $allConnections = $user->connections
            ->union($user->connectedWithMe)
            ->unique('id')
            ->take(400);
   
        $connections = [];    
        if($allConnections){
            $connections = $allConnections->map(function ($connection) {
                return [
                    "id"              => (string) $connection->id,
                    "name"            => $connection->full_name ?? $connection->name,
                    "connection_role" => $connection->getRoleNames()->implode(', '),
                    "company_name"    => $connection->company ?? null,
                    "connection_image"=> $connection->photo ? $connection->photo->file_path : asset('images/default.png'),
                    "status"          => $connection->pivot->status ?? null, // include status if needed
                ];
            });
        }

        return response()->json($connections);

    } catch (\Exception $e) {
        return response()->json([
            "success" => false,
            "message" => $e->getMessage(),
        ], 500);
    }
}


public function getConnectionsDetails(Request $request)
{
    try {
        if (!$user = JWTAuth::parseToken()->authenticate()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        
        $user = auth()->user();

        // merge both sides
        $allConnections = $user->connections
            ->merge($user->connectedWithMe)
            ->unique('id');

        // pick specific connection by ID
        $connectionId = $request->connectionId ?? null; // or pass directly
        $connection = $allConnections->firstWhere('id', $connectionId);

        if (!$connection) {
            return response()->json([
                'success' => false,
                'message' => 'Connection not found'
            ], 404);
        }

        $connectionDetails = [
            "id"              => $connection->id,
            "rep_name"            => $connection->full_name ?? $connection->name,
            "rep_email"            => $connection->email ?? '',
            "rep_phone"            => $connection->mobile ?? '',
            "connection_role" => $connection->getRoleNames()->implode(', '),
            "companyName"    => $connection->company ?? null,
            "company_website" => $connection->website ?? null,
            "tags" => !empty($connection->tags) 
                                ? array_map('trim', explode(',', $connection->tags)) 
                                : [],
            "rating" => $connection->rating ,
            "visitingCardUrl"=>  asset('images/default.png'),
            "note"=> $connection->note,
            "avatarUrl"=> $connection->photo ? $connection->photo->file_path : asset('images/default.png'),
            "rep_name"          => $connection->pivot->status ?? null,
        ];

        return response()->json($connectionDetails);

    } catch (\Exception $e) {
        return response()->json([
            "success" => false,
            "message" => $e->getMessage(),
        ], 500);
    }
}

public function addSessionToFavourite(Request $request){

    if(!isFavorite($request->sessionId)){
         addFavorite($request->sessionId);
        
          return response()->json(["message"=> "Session added as favourite"]);
        }else{
          removeFavorite($request->sessionId);
          return response()->json(["message"=> "Session moved from favourite"]);
        }
}


public function getAgenda()
{
    try {

        if (!$user = JWTAuth::parseToken()->authenticate()) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized'
        ], 401);
    }   

   $agendas = UserAgenda::with('session')
    ->where('user_id', auth()->id())
    ->get()
    ->groupBy(function ($agenda) {
        return $agenda->session->start_time->format('Y-m-d'); // group by date
    })
    ->map(function ($grouped, $date) {
        return [
            "date"        => $date,
            "agenda_list" => $grouped->map(function ($agenda) {
                return [
                    "id"          => $agenda->id,
                    "title"       => $agenda->session->title,
                    "description" => $agenda->session->description,
                    "location"    => $agenda->session->location,
                    "start_time"  => $agenda->session->start_time->format('Y-m-d H:i'),
                    "end_time"    => $agenda->session->end_time->format('Y-m-d H:i'),
                ];
            })->values(), // reset keys
        ];
    })->values(); // reset main keys


        return response()->json($agendas);

    } catch (\Exception $e) {
        return response()->json(["message" => $e->getMessage()]);
    }
}

public function createAgenda(Request $request){
    try {
        if (!$user = JWTAuth::parseToken()->authenticate()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }
        addAgenda($request->sessionId);
        return response()->json([
            "message"=> "Session added to your agenda.",
            "isInAgenda" => isAgenda($request->sessionId)
        ]);
    

    } catch (\Exception $e) {
        return response()->json(["message" => $e->getMessage()]);
    }     
}

public function scanDetails(Request $request){
    try {
        if (!$user = JWTAuth::parseToken()->authenticate()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

         $validator = Validator::make($request->all(), [
            'qrData' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
                'data' => $request->all(),
            ], 422);
        }

        $data = UserConnection::with('connection')->where('user_id',$user->id)->where('connection_id',$request->qrData)->first();

        return response()->json([
            "message"=> "Session added to your agenda.",
            "isInAgenda" => isAgenda($request->sessionId)
        ]);
    

    } catch (\Exception $e) {
        return response()->json(["message" => $e->getMessage()]);
    }
}


public function connectionUpdate(Request $request){
    try {
        if (!$user = JWTAuth::parseToken()->authenticate()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }
        
        $user = User::where('id',$request->connectionId)->first();
        if(!$user){
           return response()->json([
            "message"=> "No connection found.",
           ]);
        }

        $tagsString = is_array($request->tags)? implode(',', array_map('trim', $request->tags)) : trim((string) $request->tags);      
        $user->rating = $request->rating ?? '';
        $user->tags =$tagsString ?? '';
        $user->note =$request->note ?? '';
        $user->save(); 
        return response()->json([
            "message"=> "Connected updated.",
        ]);
    

    } catch (\Exception $e) {
        return response()->json(["message" => $e->getMessage()]);
    }
}


public function scanCreate(Request $request){
    try {
        if (!$user = JWTAuth::parseToken()->authenticate()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }
        addAgenda($request->sessionId);
        return response()->json([
            "message"=> "Session added to your agenda.",
            "isInAgenda" => isAgenda($request->sessionId)
        ]);
    

    } catch (\Exception $e) {
        return response()->json(["message" => $e->getMessage()]);
    }
}

}
