<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EventSupport;
use App\Models\Event;
use App\Services\AdminInquiryAlertService;

class ContactUsController extends Controller
{
    public function index($slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        return view("frontend.new_support", compact('event'));
    }
    public function store(Request $request, $slug, AdminInquiryAlertService $alerts)
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255',
            'phone'    => ['nullable',
            'regex:/^\+?[0-9]{7,15}$/'],
            'message'  => 'required|string',
        ]);

        $support = EventSupport::create([
            'event_id' => $event->id,
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'message'  => $request->message,
        ]);

        $alerts->notifyEventTicket($support->loadMissing('event'));

        return redirect()
            ->route('support', $slug)
            ->with('success', 'Your request has been submitted successfully.');
    }
}
