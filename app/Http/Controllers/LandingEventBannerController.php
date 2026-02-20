<?php

namespace App\Http\Controllers;

use App\Models\LandingEventBanner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LandingEventBannerController extends Controller
{
    public function index()
    {
        $banner = LandingEventBanner::first();
        return view('admin.home-page.events.banner', compact('banner'));
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'heading' => 'required|string|max:255',
            'sub_heading' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $banner = LandingEventBanner::first();
        if (!$banner) {
            $banner = LandingEventBanner::create($request->only(['heading', 'sub_heading']));
        } else {
            $banner->update($request->only(['heading', 'sub_heading']));
        }

        return redirect()->back()->withSuccess('Event banner text updated successfully!');
    }
}
