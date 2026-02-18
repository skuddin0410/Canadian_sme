<?php

namespace App\Http\Controllers\Frontend;

use Carbon\Carbon;
use App\Models\Booth;
use App\Models\Event;
use App\Models\User; 
use App\Models\Company;
use App\Models\Session;
use App\Models\Speaker;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use DB;
use Illuminate\Support\Str;

class LandingController extends Controller
{
    /**
     * Show the landing page.
    */

    // public function eachEvent($slug)
    // {   
    //     $event = Event::with(['photo'])->where('slug', $slug)->first();
    //     $session = Session::with(['photo','speakers','exhibitors','sponsors','attendees'])->where('start_time', '>=', now())
    //     ->orderBy('start_time', 'ASC')
    //     ->first();

    //     $shareUrl = $event ? route('events.show', $event->id) : url()->current();
        
    //     $speakers = Speaker::inRandomOrder()->take(10)->get();

    //     $exhibitors = Company::where('is_sponsor', 0)
    //         ->inRandomOrder()
    //         ->take(6)
    //         ->get();

    //     $sponsors = Company::with(['category'])
    //         ->where('is_sponsor', 1)
    //         ->inRandomOrder()
    //         ->take(6)
    //         ->get();

    //     $attendees = User::with(['photo', 'roles'])
    //         ->whereHas('roles', function ($q) {
    //             $q->where('name', 'Attendee');
    //         })
    //         ->whereNotNull('name')
    //         ->whereNotNull('slug')
    //         ->inRandomOrder()
    //         ->take(5)
    //         ->get();

    //     $schedules = Session::where('start_time', '>=', now())->orderBy('start_time', 'ASC')->take(6)->get();
    //     $location = !empty($event->location) ? $event->location : null;

    //     $googleApiKey = config('services.google_maps.key');
    //     $mapUrl = $location && $googleApiKey
    //     ? "https://www.google.com/maps/embed/v1/place?key={$googleApiKey}&q=" . urlencode($location)
    //     : null;

    //     return view('frontend.landing.index',compact('event','session','speakers','exhibitors','sponsors','attendees','schedules','location' , 'mapUrl','shareUrl'));
    // }

    public function eachEvent($slug)
    {
        $event = Event::with(['photo'])->where('slug', $slug)->firstOrFail();
        // dd($event->id);

        // ---- Helper: fetch linked IDs by type (supports "speaker" OR "App\Models\Speaker" style) ----
        $linkedIds = function (string $type) use ($event) {
            $type = Str::lower($type);

            return DB::table('event_and_entity_link')
                ->where('event_id', $event->id)
                ->where(function ($q) use ($type) {
                    $q->whereRaw('LOWER(entity_type) = ?', [$type])
                    ->orWhereRaw('LOWER(entity_type) LIKE ?', ['%\\' . $type]); // supports class names
                })
                ->pluck('entity_id')
                ->unique()
                ->values()
                ->all();
        };

        // ---- Sessions for this event only ----
        // $sessionIds = $linkedIds('session');

        $session = Session::with(['photo','speakers','exhibitors','sponsors','attendees'])
            // ->when(!empty($sessionIds), fn ($q) => $q->whereIn('id', $sessionIds))
            ->where('event_id', $event->id) // direct filter by event_id
            ->where('start_time', '>=', now())
            ->orderBy('start_time', 'ASC')
            ->first();

        $schedules = Session::query()
            // ->when(!empty($sessionIds), fn ($q) => $q->whereIn('id', $sessionIds))
            ->where('event_id', $event->id) // direct filter by event_id
            ->where('start_time', '>=', now())
            ->orderBy('start_time', 'ASC')
            ->take(6)
            ->get();

        // ---- Speakers for this event only ----
        $speakerIds = $linkedIds('speakers');
        // dd($speakerIds);

        $speakers = Speaker::query()
            ->whereIn('id', $speakerIds)   // ONLY these IDs
            ->with(['photo'])
            ->take(10)
            ->get();

        // ---- Exhibitors for this event only (Company model, is_sponsor = 0) ----
        $exhibitorIds = $linkedIds('companies');
        // dd($exhibitorIds);

        $exhibitors = Company::query()
            ->where('is_sponsor', 0)
            ->whereIn('id', $exhibitorIds)   // ONLY these IDs
            ->take(6)
            ->get();

        // ---- Sponsors for this event only (Company model, is_sponsor = 1) ----
        $sponsorIds = $linkedIds('companies');
        // dd($sponsorIds);

        $sponsors = Company::with(['category'])
            ->where('is_sponsor', 1)
            ->whereIn('id', $sponsorIds)   // ONLY these IDs
            ->take(6)
            ->get();

        // ---- Attendees for this event only ----
        $attendeeIds = $linkedIds('users');
        // dd($attendeeIds);

        $attendees = User::with(['photo', 'roles'])
            ->whereIn('id', $attendeeIds)   // ONLY these IDs
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Attendee');
            })
            ->whereNotNull('name')
            ->whereNotNull('slug')
            ->take(5)
            ->get();
        // dd($attendees);

        // ---- Share URL (use slug route if you have it) ----
        // If your route is events.show = /events/{slug}, use $event->slug
        // If your route is /events/{id}, use $event->id
        $shareUrl = route('events.show', $event->slug);

        // ---- Map ----
        $location = !empty($event->location) ? $event->location : null;

        $googleApiKey = config('services.google_maps.key');
        $mapUrl = $location && $googleApiKey
            ? "https://www.google.com/maps/embed/v1/place?key={$googleApiKey}&q=" . urlencode($location)
            : null;

        return view(
            'frontend.landing.index',
            compact('event','session','speakers','exhibitors','sponsors','attendees','schedules','location','mapUrl','shareUrl')
        );
    }


    public function index()
    {
        return view('eventzen_io_home');
    }


    public function schudled(){
        $now        = now();
        $startToday = $now->copy()->startOfDay();
        $endToday   = $now->copy()->endOfDay();

        $base = Session::with(['speakers']);

        $schedules = (clone $base)
            ->whereBetween('start_time', [$startToday, $endToday])
            ->where(function ($q) use ($now) {
                $q->where('end_time', '>=', $now) // ongoing or later today
                  ->orWhere(function ($q) use ($now) {
                      $q->whereNull('end_time')
                        ->where('start_time', '>=', $now); // upcoming today (no end_time)
                  });
            })
            ->orderBy('start_time')
            ->get();

        if ($schedules->isEmpty()) {
                $firstFuture = (clone $base)
                ->where('start_time', '>', $endToday)
                ->orderBy('start_time')
                ->first();

                if ($firstFuture) {
                    $targetDate = $firstFuture->start_time->toDateString(); // 'YYYY-MM-DD'
                    $schedules = (clone $base)
                        ->whereDate('start_time', $targetDate)
                        ->orderBy('start_time')
                        ->get();
                } else {
                    // No future sessions at all
                    $schedules = collect();
                }
            }
        return $schedules;    
    }
   
public function exhibitorIndex()
{
    $event = Event::with(['photo'])->first();
    $exhibitors = Company::with('contentIconFile')->where('is_sponsor',0)->paginate(10);
    
     return view('frontend.page.exhibitor', compact('event', 'exhibitors'));
   
}
public function sponsorIndex()
{
    $event = Event::with('photo')->first();

    $sponsors = Company::with('logo')
        ->where('is_sponsor', 1)   
        ->orderby('id','DESC')->paginate(10);

    return view('frontend.page.sponsor', compact('event', 'sponsors'));
}

public function attendeeIndex()
{
    $attendees = User::with('photo')->with("roles")
                ->whereHas("roles", function ($q) {
                    $q->whereIn("name", ["Attendee"]);
                })->whereNotNull('name')
                    ->whereNotNull('slug')
                    ->orderBy('id','DESC')->paginate(10); 

    
    return view('frontend.page.attendee', compact('attendees'));
}
public function scheduleIndex()
{
    $event = Event::with(['photo'])->first();

    // 🔹 Get ALL sessions
    $allSessions = Session::with(['photo','speakers','exhibitors','sponsors','attendees'])
        ->orderBy('start_time', 'ASC')
        ->get();

    // 🔹 Get filtered sessions (today / next available date)
    $schedules = $this->schedules();

    return view('frontend.page.schedule', compact('event', 'schedules', 'allSessions'));
}

public function schedules()
{
    $now = now();
    $startOfYear = $now->copy()->startOfYear();
    $endOfYear   = $now->copy()->endOfYear();

    $baseQuery = Session::with(['speakers']);

    // Try to fetch sessions within the current year (ongoing or upcoming)
    $schedules = (clone $baseQuery)
        ->whereBetween('start_time', [$startOfYear, $endOfYear])
        ->where(function ($q) use ($now) {
            $q->where('end_time', '>=', $now)
              ->orWhere(function ($q) use ($now) {
                  $q->whereNull('end_time')
                    ->where('start_time', '>=', $now);
              });
        })
        ->orderBy('start_time')
        ->paginate(10);

    // If no sessions found, fetch from next available date
    if ($schedules->isEmpty()) {
        $nextSession = (clone $baseQuery)
            ->where('start_time', '>', $endOfYear)
            ->orderBy('start_time')
            ->first();

        if ($nextSession) {
            $targetDate = Carbon::parse($nextSession->start_time)->toDateString();

            $schedules = (clone $baseQuery)
                ->whereDate('start_time', $targetDate)
                ->orderBy('start_time')
                ->paginate(10);
        }
    }

    return $schedules;
}

public function profile($slug)
{
   
    $attendee = User::with(['photo'])
     ->where('slug', $slug)
     ->firstOrFail();

   
   $sessions = Session::where('start_time', '>=', now())
        ->inRandomOrder()
        ->take(2)
        ->get();
   
    $event = Event::with('photo')->first();


    return view('frontend.profile', compact('attendee', 'sessions', 'event'));
}
public function speaker($slug)
{
    $speaker = Speaker::with(['photo', 'coverphoto'])
        ->where('slug', $slug)
        ->firstOrFail();
    
    $sessions = DB::table('event_sessions as es')
        ->join('session_speakers as ss', 'es.id', '=', 'ss.session_id')
        ->where('ss.speaker_id', $speaker->id) 
        ->where('es.start_time', '>', now()) 
        ->orderBy('es.start_time', 'asc')
        ->select('es.*')
        ->get();           
   
    $event = Event::with('photo')->first();

    return view('frontend.speaker', compact('speaker', 'sessions', 'event'));
}


public function exhibitor( Request $request,$slug){ 

    $company = Company::with('Docs')->where('slug', $slug)
        ->where('is_sponsor', 0)
        ->firstOrFail();
    $sessions = DB::table('event_sessions as es')
        ->join('session_exhibitors as ss', 'es.id', '=', 'ss.session_id')
        ->where('ss.company_id', $company->id) 
        ->where('es.start_time', '>', now()) 
        ->orderBy('es.start_time', 'asc')
        ->select('es.*')
        ->get();    

    $event = Event::with('photo')->first();

    return view('frontend.company',compact('company' , 'sessions','event'));
}

public function sponsor( Request $request,$slug){ 

    $company = Company::with(['logo','banner','Docs'])
        ->where('slug', $slug)
        ->where('is_sponsor', 1)  
        ->firstOrFail();
   $sessions = DB::table('event_sessions as es')
        ->join('session_sponsors as ss', 'es.id', '=', 'ss.session_id')
        ->where('ss.company_id', $company->id) 
        ->where('es.start_time', '>', now()) 
        ->orderBy('es.start_time', 'asc')
        ->select('es.*')
        ->get();    

    $event = Event::with('photo')->first();

    return view('frontend.sponsor',compact('company' , 'sessions', 'event'));
}

    

public function session(Request $request, $slug)
{
    $session = Session::with(['speakers','exhibitors','sponsors'])->where('slug', $slug)->firstOrFail();
    $speaker = $session->speakers->first();
    $event = Event::with('photo')->first();
    return view('frontend.session', compact('session', 'event', 'speaker'));
}

    
    
    
    public function venue(){
    $locationSetting = \App\Models\Setting::where('key', 'company_address')->first();
    $location = $locationSetting ? $locationSetting->value : null;
    $event = Event::with(['photo'])->first();
    $location = $event->location ?? '';

    $googleApiKey = config('services.google_maps.key'); 

    $mapUrl = $location && $googleApiKey
        ? "https://www.google.com/maps/embed/v1/place?key={$googleApiKey}&q=" . urlencode($location)
        : null;

    $sessions = Session::where('start_time', '>=', now())
        ->inRandomOrder()
        ->take(2)
        ->get();

    $event = Event::with('photo')->first();
    return view('frontend.venue', compact('location', 'mapUrl','sessions','event'));
    }


    public function showUpdateForm($userId){
        try{
            $decryptedUserId = Crypt::decryptString($userId);
            $user = User::findOrFail($decryptedUserId);
            return view('frontend.update-user-form', compact('user'));

        } catch (\Exception $e) {
          return response()->json(["message" => $e->getMessage()]);
        }
    }

    public function updateUserDetails(Request $request, $userId)
    {
        try{
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'company' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:500',
        ]);
        
        $encryptedUserId = Crypt::encryptString($userId);
        $user = User::findOrFail($userId);
        $user->update([
        'name'        => $request->first_name,
        'lastname'    => $request->last_name,
        'mobile'      => $request->mobile ?? null,
        'designation' => $request->designation ?? null,
        'company'     => $request->company ?? null,
        'bio'         => $request->bio ?? null,
        ]);
        
        return redirect()->route('update-user', $encryptedUserId)->with('success', 'Your details have been updated successfully!');
        }catch (\Exception $e) {
          return response()->json(["message" => $e->getMessage()]);
        }
    }

    public function getVenuInfoForApp(){

        $event = Event::with(['photo'])->first();
        $location = $event->location ?? '';
        $googleApiKey = config('services.google_maps.key'); 

        $mapUrl = $location && $googleApiKey
            ? "https://www.google.com/maps/embed/v1/place?key={$googleApiKey}&q=" . urlencode($location)
            : null;
       
        return view('frontend.venue_app', compact('location', 'mapUrl'));
        
    }
    
    public function speakerIndex()
    {
        $speakers = Speaker::orderBy('created_at','DESC')->paginate(10); 
        return view('frontend.page.speaker', compact('speakers'));
    } 

    public function search(Request $request)
    {   
        $query = $request->input('q');
     
     
        // Sessions
        $session = Session::with(['photo','speakers','exhibitors','sponsors','attendees'])
            ->where(function($q) use ($query) {
                $q->where('title','like',"%{$query}%")
                ->orWhereHas('speakers', fn($s)=>$s->where('name','like',"%{$query}%"))
                ->orWhereHas('exhibitors', fn($e)=>$e->where('name','like',"%{$query}%"))
                ->orWhereHas('sponsors', fn($sp)=>$sp->where('name','like',"%{$query}%"))
                ->orWhereHas('attendees', fn($a)=>$a->where('name','like',"%{$query}%"));
            })
            ->orderBy('start_time','ASC')
            ->first();
     
        // Speakers
        $speakers = Speaker::where(function($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
                  ->orWhere('lastname', 'like', "%{$query}%")
                  ->orWhere('company', 'like', "%{$query}%")
                  ->orWhere('designation', 'like', "%{$query}%")
                  ->orWhere('bio', 'like', "%{$query}%");
           })->get();
             
        // Exhibitors
        $exhibitors = Company::where('is_sponsor', 0)
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                      ->orWhere('location', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%");
            })->get();
     
        // Sponsors
        $sponsors = Company::with('category')
            ->where('is_sponsor', 1)
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                      ->orWhere('location', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%");
            })->get();
     
        // Attendees
        $attendees = User::with(['photo','roles'])
            ->whereHas('roles', fn($q)=>$q->where('name','Attendee'))
            ->where(function($q) use ($query){
                $q->where('name','like',"%{$query}%")
                  ->orWhere('lastname','like',"%{$query}%");
            })->get();
     
        // Schedules
        $schedules = Session::where('start_time', '>=', now())
        ->where(function($q) use ($query) {
            $q->where('title', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('location', 'like', "%{$query}%")
                  ->orWhere('track', 'like', "%{$query}%");
        })
        ->get();
     
        return view('frontend.search.results', [
            'session' => $session,
            'speakers' => $speakers,
            'exhibitors' => $exhibitors,
            'sponsors' => $sponsors,
            'attendees' => $attendees,
            'schedules' => $schedules,
        ]);
    }

    public function allEvents(Request $request)
    {
        // $events = Event::with(['photo'])->orderBy('start_date', 'DESC')->paginate(10);
        // return view('eventzen_io_events', compact('events'));
        $userId = auth()->id();
        // dd($userId);

        $tab = $request->get('tab', 'ongoing'); // default ongoing

        $today = Carbon::today(); // server timezone
        $q = trim((string) $request->get('q', ''));

        // base query (apply common filters here if needed)
        $base = Event::query();

            if (auth()->user() && auth()->user()->hasRole('Admin')) {
                // dd(1);
                $base->select('*')->selectRaw('1 as is_registered'); // always true
            } else {
                // dd(2);
                $base->withExists([
                    'entityLinks as is_registered' => function ($q) use ($userId) {
                        $q->where('entity_type', 'users')
                        ->where('entity_id', $userId);
                    }
                ]);
            }

        $base = $base->with(['photo'])
            // optional: only active/visible events
            // ->where('status', 1)
            // ->where('visibility', 'public')
            ->when($q, function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('title', 'like', "%{$q}%")
                    ->orWhere('location', 'like', "%{$q}%")
                    ->orWhere('tags', 'like', "%{$q}%")
                    ->orWhere('tracks', 'like', "%{$q}%");
                });
            });

        // NOTE: handle null end_date => treat it as start_date
        $ongoing = (clone $base)
            ->whereDate('start_date', '<=', $today)
            ->whereDate(\DB::raw('COALESCE(end_date, start_date)'), '>=', $today)
            ->orderBy('start_date', 'DESC')
            ->paginate(9, ['*'], 'ongoing_page');

        $upcoming = (clone $base)
            ->whereDate('start_date', '>', $today)
            ->orderBy('start_date', 'ASC')
            ->paginate(9, ['*'], 'upcoming_page');

        $past = (clone $base)
            ->whereDate(\DB::raw('COALESCE(end_date, start_date)'), '<', $today)
            ->orderBy('start_date', 'DESC')
            ->paginate(9, ['*'], 'past_page');

        // dd($ongoing, $upcoming, $past);

        return view('eventzen_io_events', compact('ongoing', 'upcoming', 'past', 'q', 'tab'));
    }

}