<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EventSupport;
use App\Models\Event;

class ContactUsController extends Controller
{
    public function index($slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        return view("frontend.new_support", compact('event'));
    }
    public function store(Request $request, $slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255',
            'phone'    => 'nullable|string|max:20',
            'message'  => 'required|string',
        ]);

        EventSupport::create([
            'event_id' => $event->id,
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'message'  => $request->message,
        ]);

        return redirect()
            ->route('support', $slug)
            ->with('success', 'Your message has been submitted successfully.');
    }
}
