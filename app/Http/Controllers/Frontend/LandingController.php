<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Session;
use Carbon\Carbon;
use App\Models\User; 

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

        $speakers = $session->speakers->take(3); 
        $exhibitors = $session->exhibitors->take(3);
        $sponsors = $session->sponsors->take(3);
        $attendees = $session->attendees->take(3);
        
        $schedules = $this->schudled();

        return view('frontend.landing.index',compact('event','session','speakers','exhibitors','sponsors','attendees','schedules'));
    }
//     public function attendees()
// {
//     $attendees = User::with(['photo', 'roles'])
//         ->whereHas('roles', function ($q) {
//             $q->where('name', 'Attendee');
//         })
//         ->get();

//     return view('frontend.profile', compact('attendees'));
// }




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
   
// public function profile($id)
// {
//     // Fetch attendee with specific roles and relations
//     $attendee = User::with(['photo', 'roles'])
//         ->where('id', $id)
//         ->whereHas('roles', function ($q) {
//             $q->whereIn('name', ['Attendee']); // only attendee role
//         })
//         ->firstOrFail();

//     // Fetch upcoming session(s) for context
//     $session = Session::with(['speakers', 'exhibitors', 'sponsors'])
//         ->where('start_time', '>=', now())
//         ->orderBy('start_time', 'ASC')
//         ->first();

//     // Fetch event details
//     $event = Event::with('photo')->first();

//     return view('frontend.profile', compact('attendee', 'session', 'event'));
// }
// public function profile($id)
// {
//     $attendee = User::with(['photo', 'roles'])
//         ->where('id', $id)
//         ->whereHas('roles', function ($q) {
//             $q->where('name', 'Attendee');
//         })
//         ->firstOrFail();

//     $session = Session::with(['speakers', 'exhibitors', 'sponsors'])
//         ->where('start_time', '>=', now())
//         ->orderBy('start_time', 'ASC')
//         ->first();

//     $event = Event::with('photo')->first();

//     return view('frontend.profile', compact('attendee', 'session', 'event'));
// }
//   public function profile($id)
// {
//     // Fetch attendee by ID with roles + photo
//     $attendee = User::with(['photo', 'roles'])
//         ->where('id', $id)
//         ->whereHas('roles', function ($q) {
//             $q->where('name', 'Attendee'); // only attendees
//         })
//         ->firstOrFail();

//     // Get upcoming session for context
//     $session = Session::with(['speakers', 'exhibitors', 'sponsors'])
//         ->where('start_time', '>=', now())
//         ->orderBy('start_time', 'ASC')
//         ->first();

//     // Event details
//     $event = Event::with('photo')->first();

//     // Show attendee profile page
//     return view('frontend.profile', compact('attendee', 'session', 'event'));
// }

public function profile($id)
{
    // Fetch user with photo (ignore role check)
    $attendee = User::with(['photo'])->findOrFail($id);

    // Optional: fetch the session and event for context
    $session = Session::with(['speakers', 'exhibitors', 'sponsors'])
        ->where('start_time', '>=', now())
        ->orderBy('start_time', 'ASC')
        ->first();

    $event = Event::with('photo')->first();

    return view('frontend.profile', compact('attendee', 'session', 'event'));
}


    

     public function session(Request $request){
       return view('frontend.session');
    }
}