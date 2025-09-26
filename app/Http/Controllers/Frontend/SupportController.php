<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Support;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\SupportRequest;
use Illuminate\Support\Facades\Auth;

class SupportController extends Controller
{ 
     public function store(Request $request)
    {
       $validated = $request->validate([
        'email' => 'required|email', 
        'subject' => 'required|string|max:255', 
        'description' => 'required|string|max:255', 
       ]);


        Support::create([
            'email' => $request->email,
            'subject' => $request->subject,
            'description' => $request->description,
        ]);
        return redirect()->back()->with('success', 'Your request has been submitted successfully!');
    }
    
}
