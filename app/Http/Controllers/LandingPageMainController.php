<?php

namespace App\Http\Controllers;

use App\Models\LandingPageMain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LandingPageMainController extends Controller
{
    public function index()
    {
        $main = LandingPageMain::first();
        return view('admin.home-page.main', compact('main'));
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'button_link' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $main = LandingPageMain::first();
        if (!$main) {
            $main = LandingPageMain::create($request->only(['title', 'subtitle', 'description', 'button_link']));
        } else {
            $main->update($request->only(['title', 'subtitle', 'description', 'button_link']));
        }

        if ($request->hasFile('image')) {
            $this->imageUpload($request->file("image"), "landing_page_mains", $main->id, 'landing_page_mains', 'main_image', $main->id);
        }

        return redirect()->back()->withSuccess('Landing page main section updated successfully!');
    }
}
