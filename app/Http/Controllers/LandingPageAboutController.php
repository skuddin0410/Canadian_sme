<?php

namespace App\Http\Controllers;

use App\Models\LandingPageAbout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LandingPageAboutController extends Controller
{
    public function index()
    {
        $about = LandingPageAbout::first();
        return view('admin.home-page.about', compact('about'));
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'heading' => 'required|string|max:255',
            'sub_heading' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'desc_points' => 'nullable|string',
            'button_text' => 'nullable|string|max:255',
            'button_link' => 'nullable|string|max:255',
            'banner_button_link' => 'nullable|string|max:255',
            'exp_year' => 'nullable|string|max:255',
            'exp_text' => 'nullable|string|max:255',
            'bg_banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'front_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'banner_button_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'exp_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $about = LandingPageAbout::first();
        $data = $request->only([
            'heading', 'sub_heading', 'description', 'desc_points', 
            'button_text', 'button_link', 'banner_button_link', 
            'exp_year', 'exp_text'
        ]);

        if (!$about) {
            $about = LandingPageAbout::create($data);
        } else {
            $about->update($data);
        }

        // Handle multiple image uploads
        $images = [
            'bg_banner' => 'bg_banner',
            'banner_image' => 'banner_image',
            'front_image' => 'front_image',
            'banner_button_image' => 'banner_button_image',
            'exp_image' => 'exp_image',
        ];

        foreach ($images as $inputName => $fileType) {
            if ($request->hasFile($inputName)) {
                $this->imageUpload($request->file($inputName), "landing_page_abouts", $about->id, 'landing_page_abouts', $fileType, $about->id);
            }
        }

        return redirect()->back()->withSuccess('About section updated successfully!');
    }
}
