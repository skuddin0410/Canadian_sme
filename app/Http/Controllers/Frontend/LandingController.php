<?php

namespace App\Http\Controllers\Frontend;

use Carbon\Carbon;
use App\Models\Booth;
use App\Models\Event;
use App\Models\User; 
use App\Models\Company;
use App\Models\Session;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
        
        $speakers = User::with("roles")
                ->whereHas("roles", function ($q) {
                    $q->whereIn("name", ["Speaker"]);
                })->orderBy('created_at', 'DESC')->take(4)->get();

        $exhibitors = Company::where('is_sponsor',0)->orderBy('created_at', 'DESC')->take(3)->get();
        $sponsors = Company::with(['category'])->where('is_sponsor',1)->orderBy('created_at', 'DESC')->take(6)->get();
        $attendees = $session->attendees->take(3);
        $schedules = $this->schudled();
        $locationSetting = \App\Models\Setting::where('key', 'company_address')->first();
        $location = $locationSetting ? $locationSetting->value : null;

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

        /**
         * 1) Try: today’s sessions that are still relevant
         *    - If you have `end_time`: include sessions whose end_time >= now (ongoing/upcoming)
         *    - If a session has no end_time, treat it as upcoming only if start_time >= now
         */
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

        /**
         * 2) If today is over (no matching sessions),
         *    find the earliest future session and then fetch ALL sessions on that date.
         */
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
    $exhibitors = Company::with('contentIconFile')->get();
    
     return view('frontend.page.exhibitor', compact('event', 'exhibitors'));
   
}
public function sponsorIndex()
{
    $event = Event::with('photo')->first();

    $sponsors = Company::with('logo')
        ->where('is_sponsor', 1)   
        ->get();

    return view('frontend.page.sponsor', compact('event', 'sponsors'));
}

public function attendeeIndex()
{
    $attendees = User::with('photo')->get(); // all attendees
    
    $session = Session::with(['speakers', 'exhibitors', 'sponsors'])
        ->where('start_time', '>=', now())
        ->orderBy('start_time', 'ASC')
        ->first();

    $event = Event::with('photo')->first();

    return view('frontend.page.attendee', compact('attendees', 'session', 'event'));
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
    $now        = now();
    $startToday = $now->copy()->startOfYear();
    $endToday   = $now->copy()->endOfYear();

    $base = Session::with(['speakers']);

    // Today’s sessions (ongoing or upcoming)
    $schedules = (clone $base)
        ->whereBetween('start_time', [$startToday, $endToday])
        ->where(function ($q) use ($now) {
            $q->where('end_time', '>=', $now)
              ->orWhere(function ($q) use ($now) {
                  $q->whereNull('end_time')
                    ->where('start_time', '>=', $now);
              });
        })
        ->orderBy('start_time')
        ->get();

    // If no sessions today, pick the next date’s sessions
    if ($schedules->isEmpty()) {
        $firstFuture = (clone $base)
            ->where('start_time', '>', $endToday)
            ->orderBy('start_time')
            ->first();

        if ($firstFuture) {
            $targetDate = $firstFuture->start_time->toDateString();
            $schedules = (clone $base)
                ->whereDate('start_time', $targetDate)
                ->orderBy('start_time')
                ->get();
        } else {
            $schedules = collect();
        }
    }

    return $schedules;
}

public function profile($id)
{
   
    $attendee = User::with(['photo'])->findOrFail($id);

   
    $session = Session::with(['speakers', 'exhibitors', 'sponsors'])
        ->where('start_time', '>=', now())
        ->orderBy('start_time', 'ASC')
        ->first();

    $event = Event::with('photo')->first();

    return view('frontend.profile', compact('attendee', 'session', 'event'));
}
   public function speaker($id)
  {
   
    $speaker = User::with(['photo','coverphoto' ])->findOrFail($id);

   
    $session = Session::with(['speakers', 'exhibitors', 'sponsors'])
        ->where('start_time', '>=', now())
        ->orderBy('start_time', 'ASC')
        ->first();

    $event = Event::with('photo')->first();

    return view('frontend.speaker', compact('speaker', 'session', 'event'));
}

public function exhibitor( Request $request,$id){ 

    $company = Company::with(['contentIconFile','quickLinkIconFile','user','Docs' ])
        ->where('id', $id)
        ->firstOrFail();
    $sessions = Session::with(['photo','speakers','exhibitors','sponsors','attendees'])
        ->where('start_time', '>=', now())
        ->inRandomOrder()
        ->take(2)
        ->get();

    

    return view('frontend.company',compact('company' , 'sessions'));
}

public function sponsor( Request $request,$id){ 

    $company = Company::with(['logo','banner' ])
        ->where('id', $id)
        ->where('is_sponsor', 1)  
        ->firstOrFail();
    $sessions = Session::with(['photo','speakers','exhibitors','sponsors','attendees'])
        ->where('start_time', '>=', now())
        ->inRandomOrder()
        ->take(2)
        ->get();

    

    return view('frontend.sponsor',compact('company' , 'sessions'));
}

    

public function session(Request $request , $id){
    $speaker = User::with(['photo'])->findOrFail($id);

   
    $session = Session::with(['speakers'])->findOrFail($id);
        
    $event = Event::with('photo')->first();
       return view('frontend.session',compact('session','event','speaker'));
    }
    
    
    
    public function venue()
{
    $locationSetting = \App\Models\Setting::where('key', 'company_address')->first();
    $location = $locationSetting ? $locationSetting->value : null;

    $googleApiKey = config('services.google_maps.key'); 

    $mapUrl = $location && $googleApiKey
        ? "https://www.google.com/maps/embed/v1/place?key={$googleApiKey}&q=" . urlencode($location)
        : null;

    return view('frontend.venue', compact('location', 'mapUrl'));
}




}