<?php

namespace App\Http\Controllers;

use App\Models\Track;
use Illuminate\Http\Request;

class EventTrackController extends Controller
{
    // Show the form to create a new event track (optional, if using a modal)
    public function create()
    {
        return view('event-tracks.create');
    }

    // Store a new event track
    public function store(Request $request)
    {
        // Validate the input
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Create and store the event track
        Track::create([
            'name' => $request->name,
        ]);

        return redirect()->route('calendar.index')->with('success', 'Event Track added successfully!');
    }

    // Show all event tracks (optional)
    public function index()
    {
        $eventTracks = Track::all();
        return view('event-tracks.index', compact('eventTracks'));
    }
}
