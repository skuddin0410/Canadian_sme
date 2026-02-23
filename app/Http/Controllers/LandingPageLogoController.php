<?php

namespace App\Http\Controllers;

use App\Models\LandingPageLogo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LandingPageLogoController extends Controller
{
    public function index()
    {
        $logos = LandingPageLogo::orderBy('order_by', 'asc')->get();
        return view('admin.home-page.logos.index', compact('logos'));
    }

    public function create()
    {
        return view('admin.home-page.logos.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'status' => 'required|boolean',
            'order_by' => 'required|integer',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $logo = LandingPageLogo::create($request->only(['title', 'status', 'order_by']));

        if ($request->hasFile('image')) {
            $this->imageUpload($request->file("image"), "landing_page_logos", $logo->id, 'landing_page_logos', 'logo', $logo->id);
        }

        return redirect(route('admin.home-page.logos.index'))->withSuccess('Logo created successfully!');
    }

    public function edit($id)
    {
        $logo = LandingPageLogo::findOrFail($id);
        return view('admin.home-page.logos.edit', compact('logo'));
    }

    public function update(Request $request, $id)
    {
        $logo = LandingPageLogo::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'status' => 'required|boolean',
            'order_by' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $logo->update($request->only(['title', 'status', 'order_by']));

        if ($request->hasFile('image')) {
            $this->imageUpload($request->file("image"), "landing_page_logos", $logo->id, 'landing_page_logos', 'logo', $logo->id);
        }

        return redirect(route('admin.home-page.logos.index'))->withSuccess('Logo updated successfully!');
    }

    public function destroy($id)
    {
        $logo = LandingPageLogo::findOrFail($id);
        static::deleteFile($logo->id, 'landing_page_logos', 'logo');
        $logo->delete();

        return redirect(route('admin.home-page.logos.index'))->withSuccess('Logo deleted successfully!');
    }
}
