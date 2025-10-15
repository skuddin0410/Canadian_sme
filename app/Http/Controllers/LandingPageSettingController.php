<?php

namespace App\Http\Controllers;

use App\Models\LandingPageSetting;
use Illuminate\Http\Request;

class LandingPageSettingController extends Controller
{
    public function index()
    {
        $setting = LandingPageSetting::first(); // Assuming only one record is stored
        return view('landing-page-settings',compact('setting'));
    }

    public function update(Request $request)
    {

        $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'location' => 'required|string|max:255',
            'website' => 'required|url',
        ]);

        $setting = LandingPageSetting::first();

        if (!$setting) {
            $setting = LandingPageSetting::create([
                'title' => $request->title,
                'date' => $request->date,
                'location' => $request->location,
                'website' => $request->website,
            ]);
        } else {
            
            $setting->update([
                'title' => $request->title,
                'date' => $request->date,
                'location' => $request->location,
                'website' => $request->website,
            ]);
        }
        $setting = LandingPageSetting::first();
        return view('landing-page-settings',compact('setting'))->with('success', 'Landing page settings updated successfully!');
    }
}
