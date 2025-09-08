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

        $speakers = $session->speakers; 
        $exhibitors = $session->exhibitors->take(3);
        $sponsors = $session->sponsors->take(3);
        $attendees = $session->attendees->take(3);
        
        $schedules = $this->schudled();
        $locationSetting = \App\Models\Setting::where('key', 'company_address')->first();
        $location = $locationSetting ? $locationSetting->value : null;
        // $mapUrl = $location ? "https://www.google.com/maps?q=" . urlencode($location) . "&output=embed" : null;
         $mapUrl = $location 
        ? "https://www.google.com/maps?q=" . urlencode($location) . "&output=embed"
        : null;
        return view('frontend.landing.index',compact('event','session','speakers','exhibitors','sponsors','attendees','schedules','location' , 'mapUrl'));
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
    
    
    public function venue(){
    $locationSetting = \App\Models\Setting::where('key', 'company_address')->first();
    $location = $locationSetting ? $locationSetting->value : null;
    $mapUrl = $location ? "https://www.google.com/maps?q=" . urlencode($location) . "&output=embed" : null;
       return view('frontend.venue',compact('location','mapUrl'));
    }
}