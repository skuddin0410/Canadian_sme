<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Session;

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
        return view('frontend.landing.index',compact('event','session','speakers','exhibitors','sponsors','attendees'));
    }
}
