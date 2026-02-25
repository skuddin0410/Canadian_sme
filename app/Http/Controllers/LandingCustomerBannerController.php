<?php

namespace App\Http\Controllers;

use App\Models\LandingCustomerBanner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LandingCustomerBannerController extends Controller
{
    public function index()
    {
        $banner = LandingCustomerBanner::first();
        return view('admin.home-page.customer.banner', compact('banner'));
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $banner = LandingCustomerBanner::first();
        if (!$banner) {
            $banner = LandingCustomerBanner::create($request->only(['title', 'description']));
        } else {
            $banner->update($request->only(['title', 'description']));
        }

        return redirect()->back()->withSuccess('Customer banner text updated successfully!');
    }
}
