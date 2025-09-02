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

        $speakers = $session->speakers; 
        $exhibitors = $session->exhibitors; 
        $sponsors = $session->sponsors; 
        $attendees = $session->attendees; 
        return view('frontend.landing.index',compact('event','session','speakers','exhibitors','sponsors','attendees'));
    }
}
