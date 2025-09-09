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
use OneSignal;
use Illuminate\Support\Facades\Validator;



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
        $connecteduser = UserConnection::with('connection')->where('connection_id', $request->connectionId)
                     ->where('user_id', $user->id)
                     ->first();

        if (!$connecteduser) {
            return response()->json([
                'success' => false,
                'message' => 'Connection not found'
            ], 404);
        }
        
       
        $connecteduser = UserConnection::with('connection', 'connection.photo', 'connection.visitingcard')
        ->where('connection_id', $request->connectionId)
        ->where('user_id', $user->id)
        ->first();

        $connectionDetails = [
            "id"              => $connecteduser && $connecteduser->connection ? $connecteduser->connection->id : '',
            "rep_name"        => $connecteduser && $connecteduser->connection 
                                    ? ($connecteduser->connection->full_name ?: $connecteduser->connection->name)
                                    : '',
            
            "rep_email"       => $connecteduser && $connecteduser->connection ? $connecteduser->connection->email : '',
            "rep_phone"       => $connecteduser && $connecteduser->connection ? $connecteduser->connection->mobile : '',
            
            "connection_role" => $connecteduser && $connecteduser->connection ? 
                                    $connecteduser->connection->getRoleNames()->implode(', ') 
                                    : '',

            "companyName"     => $connecteduser && $connecteduser->connection ? $connecteduser->connection->company : null,

            "company_website" => $connecteduser && $connecteduser->connection ? $connecteduser->connection->website_url : null,
            "tags"            => $connecteduser && $connecteduser->connection && !empty($connecteduser->connection->tags) 
                                    ? array_map('trim', explode(',', $connecteduser->connection->tags)) 
                                    : [],
            "rating"          => $connecteduser->rating ?? '',
            "visitingCardUrl" => $connecteduser && $connecteduser->connection && $connecteduser->connection->visitingcard 
                                    ? $connecteduser->connection->visitingcard->file_path 
                                    : asset('images/default.png'),

            "note"            => $connecteduser->note ?? '',
            "avatarUrl"       => $connecteduser && $connecteduser->connection && $connecteduser->connection->photo 
                                    ? $connecteduser->connection->photo->file_path 
                                    : asset('images/default.png'),
            "status"          => $connecteduser && $connecteduser->connection ? $connecteduser->connection->pivot->status ?? null : null,
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
       
        if(!$data){
           return response()->json([
             "message"=> "Connection not found"
           ]);
        } 
        $data->load('connection.photo','connection.visitingcard');
      
     
        return response()->json([
            "message"=> "Connection found!",
            "id"=>  !empty($data->connection) ? $data->connection->id: '',
            "name"=> !empty($data->connection) ? $data->connection->full_name: '' ,
            "company"=> !empty($data->connection) ? $data->connection->company: '',
            "designation"=> !empty($data->connection) ? $data->connection->designation: '',
            "company_website"=> !empty($data->connection) ? $data->connection->website_url: '',
            "email"=> !empty($data->connection) ? $data->connection->email: '',
            "phone"=> !empty($data->connection) ? $data->connection->mobile: '',
            "avatar"=> !empty($data->connection) && !empty($data->connection->photo) ? $data->connection->photo->file_path: asset('images/default.png'),
            "visiting_card_image" => !empty($data->connection) && !empty($data->connection->visitingcard) ? $data->connection->visitingcard->file_path: asset('images/default.png'),
            "tags"=> !empty($data->connection) ? $data->connection->tags: '' ,
            "rating"=> !empty($data->rating) ? $data->rating: '' ,
            "address"=> !empty($data->connection) ? $data->connection->address: '' ,
            "bio"=> !empty($data->connection) ? $data->connection->bio: '' ,
            "note"=> !empty($data->note) ? $data->note: '' ,
        ]);
    

    } catch (\Exception $e) {
        return response()->json(["message" => 'Scan failed!']);
    }
}

public function scanDetailsUpdate(Request $request){

     try {
        if (!$user = JWTAuth::parseToken()->authenticate()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }
        $connetion = UserConnection::where('connection_id',$request->qrData)->where('user_id',$user->id)->first();
        if(!$connetion){
           return response()->json([
            "message"=> "Fail to add note!",
           ]);
        }
        $connetion->note =$request->note ?? '';
        $connetion->save(); 
        return response()->json([
            "message"=> "Connection note added!",
        ]);
    

    } catch (\Exception $e) {
        return response()->json(["message" => "Fail to add note!"]);
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
        
        $connetion = UserConnection::where('connection_id',$request->connectionId)->where('user_id',$user->id)->first();

        if(!$connetion){
           return response()->json([
            "message"=> "No connection found.",
           ]);
        }
        $tagsString = is_array($request->tags)? implode(',', array_map('trim', $request->tags)) : trim((string) $request->tags);
        $user = User::where('id',$request->connectionId)->first();
        $user->tags = $tagsString;
        $user->save(); 
        
        $connetion->rating = $request->rating ?? '';
        $connetion->note =$request->note ?? '';
        $connetion->save(); 

        if(!empty($request->visitingCardImage)){
          $this->imageBase64Upload($request->visitingCardImage,'users',$user->id,'users','visiting_card',$user->id); 
        }

        return response()->json([
            "message"=> "Connected updated.",
        ]);
    

    } catch (\Exception $e) {
        return response()->json(["message" => $e->getMessage()]);
    }
}

public function createConnection(Request $request){
     try {
        if (!$user = JWTAuth::parseToken()->authenticate()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }
        
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|max:255|email|unique:users,email',
            'designation' => 'nullable|string|max:255' ,
            'tags' => 'nullable|string|max:255'  ,
            'mobile' => 'nullable|string|unique:users,mobile',
            'bio' => 'nullable|string',       
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()]);
        }
        $tagsString = is_array($request->tag)? implode(',', array_map('trim', $request->tag)) : trim((string) $request->tag);
        $connection = new User();
        $connection->title = $request->title;
        $connection->name = $request->first_name;
        $connection->lastname = $request->last_name;
        $connection->email = $request->email;
        $connection->company = $request->company_name;
        $connection->designation = $request->job_title;
        $connection->tags =  $tagsString;
        $connection->mobile = $request->phone;
        $connection->street = $request->address;
        $connection->is_approve = true;
        $connection->save();
        $connection->assignRole('Attendee');
        
        qrCode($connection->id);
        notification($connection->id);
        Mail::to($connection->email)->send(new UserWelcome($connection));

        userConnection($user->id, $connection->id);
        $connetionUpdate = UserConnection::where('connection_id',$connection->id)->where('user_id',$user->id)->first();
        $connetionUpdate->rating = $request->rating ?? '';
        $connetionUpdate->note =$request->note ?? '';
        $connetionUpdate->save(); 

        if(!empty($request->visitingCardImage)){
          $this->imageBase64Upload($request->visitingCardImage,'users',$user->id,'users','visiting_card',$user->id); 
        }

        return response()->json([
            "message"=> "Connection added!",
        ]);
    

    } catch (\Exception $e) {
        return response()->json(["message" => $e->getMessage()]);
    }
}


public function sendPushNotification(Request $request){
    OneSignal::sendNotificationToAll(
       "Hello from Subhabrata!",
       $url = "https://sme.nodejsdapldevelopments.com/", $data = null, $buttons =null, $schedule = null
    );
}

}
