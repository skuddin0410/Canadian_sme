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
use App\Mail\UserWelcome;
use Illuminate\Support\Facades\Mail;
use App\Services\ActivityTrackingService;
use App\Mail\UserConnectionsExportMail;


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
        $homeSessions = Session::with(['speakers'])
            ->where('start_time', '>=', now())
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
                    "workshop_no" => $session->track ?? '',
                    "location" => !empty($session->location) ? $session->location: '',
                    "status" => $session->status ?? 'Upcoming',
                    "speakers" => $session->speakers->map(fn ($sp) => ["name" => $sp->name, "image"=> !empty($sp->photo) ? $sp->photo->file_path : asset('images/default.png')]),
                    "isFavorite" => isFavorite($session->id)
                ];
            });
    }

   
    // ================= Home Connections (from session_sponsors) =================
    $user = auth()->user();
    $homeConnections = $user->connections->map(function ($connection) {
        return [
            "id" => $connection->id,
            "name" => $connection->full_name ?? $connection->name,
            "avatarUrl" => $connection->photo && $connection->photo->file_path ? $connection->photo->file_path : asset('images/default.png')
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
        "totalAgents" => UserAgenda::where('user_id', auth()->id())->count(),
        "totalConnections" => UserConnection::where('user_id', auth()->id())->count(),
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
            $q->Where('user_id', $user->id); 
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
            ->where('end_time', '>', now())
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
                            "workshop_no" => $session->track ?? '',
                            "location"    => $session->location ?? '',
                            "status"      => $status,
                            "speakers"    => $session->speakers->map(fn ($sp) => ["name" => $sp->name ,"image"=> !empty($sp->photo) ? $sp->photo->file_path : asset('images/default.png')])->values(),
                            "isFavorite"  => isFavorite($session->id),
                            "agenda" => isAgenda($session->id),
                            "my_agenda" => agendaNote($session->id)
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

        ActivityTrackingService::trackSessionInquiry($sessionId, $user->email);

        $sessionData = [
            "id"          => $session->id,
            "title"       => $session->title,
            "description" => $session->description,
            "keynote"     => $session->keynote,
            "demoes"      => $session->demoes,
            "panels"      => $session->panels,
            "start_time"  => $session->start_time,
            "end_time"    => $session->end_time,
            "workshop_no" => $session->track ?? '',
            "location"    => $session->location,
            "status"      => $status,
            "speakers"    => $session->speakers->map(fn ($sp) => [
                "id"   => $sp->id,
                "name" => $sp->name,
                "image"=> !empty($sp->photo) ? $sp->photo->file_path : asset('images/default.png'),
                "designation" => $sp->designation ?? '',
                "company" => $sp->company ?? ''
            ]),
            "isFavorite"  => isFavorite($session->id),
            "isInAgenda"  => isAgenda($session->id),
            "agenda"=> agendaNote($session->id)
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

        
        $connections = UserConnection::with('connection')  // Eager load the 'connection' relationship
       ->where('user_id', $user->id)
       ->get();

        if ($connections->isNotEmpty()) {
            $connections = $connections->map(function ($connection) {
                if ($connection->connection && $connection->connection->full_name) {
                    return [
                        "id"              => (string) $connection->id,
                        "name"            => $connection->connection->full_name ?? $connection->connection->name,
                        "connection_role" => $connection->connection->getRoleNames()->implode(', '),
                        "company_name"    => $connection->connection->company ?? null,
                        "connection_image"=> $connection->connection->photo ? $connection->connection->photo->file_path : asset('images/default.png'),
                        "status"          => $connection->status ?? null, 
                    ];
                }
            })->filter();  
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
        $connecteduser = UserConnection::with('connection')->where('connection_id', $request->connectionId)->where('user_id', $user->id)->first();

        if (!$connecteduser) {
            return response()->json([
                'success' => false,
                'message' => 'Connection not found'
            ], 404);
        }
        
       
        $connecteduser = UserConnection::with('connection', 'connection.photo', 'connection.visitingcard','connection.privateDocs')
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
            "visiting_card_file_id"=>   $connecteduser && $connecteduser->connection && $connecteduser->connection->visitingcard 
                                    ? $connecteduser->connection->visitingcard->id 
                                    : '',                     

            "note"            => $connecteduser->note ?? '',
            "avatarUrl"       => $connecteduser && $connecteduser->connection && $connecteduser->connection->photo 
                                    ? $connecteduser->connection->photo->file_path 
                                    : asset('images/default.png'),
            "status"          => $connecteduser && $connecteduser->connection ? $connecteduser->connection->pivot->status ?? null : null,
            "address"          => $connecteduser && $connecteduser->connection ? $connecteduser->connection->pivot->street ?? null : null,

            "uploaded_files" => $connecteduser && $connecteduser->connection && $connecteduser->connection->privateDocs 
                            ? $connecteduser->connection->privateDocs->map(fn ($doc) => [
                                "fileID" => $doc->id,
                                "name"   => $doc->file_name,
                                "url"    => $doc->file_path,
                              ])->values()->toArray()
                            : []
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

        if (!$user = JWTAuth::parseToken()->authenticate()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        } 

    if(!isFavorite($request->sessionId)){
          addFavorite($request->sessionId);
          ActivityTrackingService::trackSessionInquiry($request->sessionId, $user->email);
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

        if(isAgenda($request->sessionId) == false){
           addAgenda($request->sessionId,$agenda_type=null,null,$request->message);
           return response()->json([
            "message"=> "Session added to your agenda.",
            "isInAgenda" => isAgenda($request->sessionId)
           ]);
    
        }else{

           addAgenda($request->sessionId,$agenda_type=null,null,$request->message); 
           return response()->json([
            "message"=> "You’ve already added this agenda.",
            "isInAgenda" => isAgenda($request->sessionId)
           ]);  
        }
         ActivityTrackingService::trackSessionInquiry($request->sessionId, $user->email);

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
          userConnection($user->id, $request->qrData);
          $data = UserConnection::with('connection')->where('user_id',$user->id)->where('connection_id',$request->qrData)->first();
        }
        
        if(empty($data->connection)){
            return response()->json(['message' => 'Connection is inactive'], 404);  
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
            "address"=> !empty($data->connection) ? $data->connection->street: '' ,
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
            'email' => 'required|string|max:255|email',
            'designation' => 'nullable|string|max:255' ,
            'tags' => 'nullable|string|max:255'  ,
            'mobile' => 'nullable|string|unique:users,mobile',
            'bio' => 'nullable|string',       
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()]);
        }

        $tagsString = is_array($request->tag)? implode(',', array_map('trim', $request->tag)) : trim((string) $request->tag);
        $connection = User::where('email',$request->email)->first();
       
        if($connection){
          $connection->tags =  $tagsString;
          $connection->street = $request->address;
          $connection->save();
        }else{ 
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
        }
        
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

    try {
        if (!$user = JWTAuth::parseToken()->authenticate()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'onesignal_userid' => 'required|string'    
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => "Fail to change!"]);
        }
        $user->onesignal_userid = $request->onesignal_userid;
        $user->save(); 
        
   
        return response()->json(["message" => "Onesignal added with profile"]);
    
    } catch (\Exception $e) {
        return response()->json(["message" => $e->getMessage()]);
    }
}

public function readAllNotifications(Request $request){

    try {
        if (!$user = JWTAuth::parseToken()->authenticate()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

       GeneralNotification::where("user_id",$user->id)->update(["is_read"=>1, "read_at"=>Now()]);
       return response()->json(["message" => "Notification all read!"]);
    
    } catch (\Exception $e) {
        return response()->json(["message" => $e->getMessage()]);
    }
}

   public function exportConnections(Request $request){
    try {

        if (!$user = JWTAuth::parseToken()->authenticate()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }
        
        if($request->qr_code == 'm' ){
            $connections = UserConnection::with('connection')->where('user_id', $user->id)->get();
            if(empty($connections)){

                return response()->json([
                    'success' => false,
                    'message' => 'Connection not found'
                ], 401);

            }

            $filename = 'connections_' . $user->id . '.csv';
            $columns = ['Connection Name', 'Email', 'Company', 'Designation', 'Rating', 'Note'];
            $csvContent = '';

            $csvContent .= implode(',', $columns) . "\n"; 

            foreach ($connections as $connection) {
                if(!empty($connection->connection)){
                    $csvContent .= implode(',', [
                        $connection->connection->full_name ?? '',
                        $connection->connection->email ?? '',
                        $connection->connection->company ?? '',
                        $connection->connection->designation ?? '',
                        $connection->rating ?? '',
                        $connection->note ?? '',
                    ]) . "\n";
                }
            }
            

            $mailData = [
                'csvContent' => $csvContent,
                'filename' => $filename,
                'user' => $user
            ];

            Mail::to($user->email)->send(new UserConnectionsExportMail($mailData));
            return response()->json(["message" => "The export email has been sent successfully."]);
            
        }else{

            $connection = UserConnection::with('connection')->where('connection_id', $request->qr_code)->first();

            if (!$connection) {
              return response()->json(['message' => 'Connection not found'], 404);
            }

            if(empty($connection->connection)){
              return response()->json(['message' => 'Connection not found'], 404);  
            }

            $responseData = [
                'name' => $connection->connection->full_name ?? '',
                'email' => $connection->connection->email ?? '',
                'company' => $connection->connection->company ?? '',
                'designation' => $connection->connection->designation ?? '',
                'tags' => $connection->connection->tags ?? '',
                'rating' => $connection->rating ?? '',
                'note' => $connection->note ?? '',
            ];
          
           return response()->json($responseData, 200);
        }
       

        } catch (\Exception $e) {

          return response()->json(["message" => $e->getMessage()]);

        }
   }

}
