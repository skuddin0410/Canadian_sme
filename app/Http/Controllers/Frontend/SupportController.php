<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Support;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\SupportRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactQuery;

class SupportController extends Controller
{ 
    public function index(){
        return view('new_contact_us');
    }
    public function store(Request $request)
    {
       $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|max:255',
            'phone'       => ['required','regex:/^\+?[0-9]{7,15}$/'],
            'location'    => 'required|string|max:255',
            'subject'     => 'required|string|max:255',
            'description' => 'required|string|max:1000',
        ]);

        // Store in DB with default pending status
        $support = Support::create([
            'name'        => $validated['name'],
            'email'       => $validated['email'],
            'phone'       => $validated['phone'],
            'location'    => $validated['location'],
            'subject'     => $validated['subject'],
            'description' => $validated['description'],
            'status'      => 'pending'
        ]);

        // Send confirmation email to user
        Mail::to($support->email)->send(new ContactQuery($support));

        return redirect()->back()->with('success', 'Your request has been submitted successfully!');
    }
    
}
