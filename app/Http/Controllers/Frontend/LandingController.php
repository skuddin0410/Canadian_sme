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

class LandingController extends Controller
{
    /**
     * Show the landing page.
    */

    public function index()
    {   
        $event = Event::with(['photo'])->first();
        $session = Session::with(['photo','speakers','exhibitors','sponsors','attendees'])->where('start_time', '>=', now())
        ->orderBy('start_time', 'ASC')
        ->first();

        $shareUrl = $event ? route('events.show', $event->id) : url()->current();
        
        $speakers = Speaker::orderBy('created_at', 'DESC')->take(6)->get();

        $exhibitors = Company::where('is_sponsor',0)->orderBy('created_at', 'DESC')->take(6)->get();
        $sponsors = Company::with(['category'])->where('is_sponsor',1)->orderBy('created_at', 'DESC')->take(6)->get();

       $attendees = User::with(['photo', 'roles'])
                    ->whereHas('roles', function ($q) {
                        $q->where('name', 'Attendee');
                    })
                    ->whereNotNull('name')
                    ->whereNotNull('designation')
                    ->whereNotNull('company')
                    ->whereNotNull('slug')
                    ->orderBy('id', 'DESC')
                    ->take(3)
                    ->get();

        $schedules = Session::where('start_time', '>=', now())->orderBy('start_time', 'ASC')->take(6)->get();
        $location = !empty($event->location) ? $event->location : null;

        $googleApiKey = config('services.google_maps.key');
        $mapUrl = $location && $googleApiKey
        ? "https://www.google.com/maps/embed/v1/place?key={$googleApiKey}&q=" . urlencode($location)
        : null;

        return view('frontend.landing.index',compact('event','session','speakers','exhibitors','sponsors','attendees','schedules','location' , 'mapUrl','shareUrl'));
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
        ->orderby('created_at','DESC')->paginate(10);

    return view('frontend.page.sponsor', compact('event', 'sponsors'));
}

public function attendeeIndex()
{
    $attendees = User::with('photo')->with("roles")
                ->whereHas("roles", function ($q) {
                    $q->whereIn("name", ["Attendee"]);
                })->whereNotNull('name')
                    ->whereNotNull('slug')
                    ->orderBy('created_at','DESC')->paginate(10); 

    
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

    $company = Company::where('slug', $slug)
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

    $company = Company::with(['logo','banner' ])
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

}