<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Session;
use Carbon\Carbon;

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

    public function profile(Request $request){
       return view('frontend.profile');
    }

     public function session(Request $request){
       return view('frontend.session');
    }
}