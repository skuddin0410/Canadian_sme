<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\DemoRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Mail\DemoBookingMail;
use Illuminate\Support\Facades\Mail;


class DemoController extends Controller
{
    public function store(Request $request)
    {
        if (Auth::check()) {

            $request->validate([
                'timezone'     => 'required|string',
                'booking_date' => 'required|date',
                'time_slot'    => 'required|string',
            ]);

            $demo = DemoRequests::create([
                'user_id'      => Auth::id(),
                'timezone'     => $request->timezone,
                'booking_date' => $request->booking_date,
                'time_slot'    => $request->time_slot,
                'status'       => 'pending',
            ]);

            $email = Auth::user()->email;
        } else {

            $request->validate([
                'name'         => 'required|string|max:255',
                'email'        => 'required|email|max:255',
                'phone'        => 'required|string|max:20',
                'timezone'     => 'required|string',
                'booking_date' => 'required|date',
                'time_slot'    => 'required|string',
            ]);

            $demo = DemoRequests::create([
                'name'         => $request->name,
                'email'        => $request->email,
                'phone'        => $request->phone,
                'timezone'     => $request->timezone,
                'booking_date' => $request->booking_date,
                'time_slot'    => $request->time_slot,
                'status'       => 'pending',
            ]);

            $email = $request->email;
        }

        // Send Email
        Mail::to($email)->send(new DemoBookingMail($demo));

        return back()->with('success', 'Demo booked successfully!');
    }
}
