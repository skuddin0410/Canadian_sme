<?php

namespace App\Http\Controllers;

use App\Models\LandingHomeReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LandingHomeReviewController extends Controller
{
    public function index()
    {
        $reviews = LandingHomeReview::orderBy('order_by', 'asc')->get();
        return view('admin.home-page.customer.reviews.index', compact('reviews'));
    }

    public function create()
    {
        return view('admin.home-page.customer.reviews.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|boolean',
            'order_by' => 'required|integer',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $slug = createUniqueSlug('landing_home_reviews', $request->customer_name);

        $review = LandingHomeReview::create([
            'customer_name' => $request->customer_name,
            'slug' => $slug,
            'description' => $request->description,
            'status' => $request->status,
            'order_by' => $request->order_by,
        ]);

        if ($request->hasFile('image')) {
            $this->imageUpload($request->file("image"), "landing_home_reviews", $review->id, 'landing_home_reviews', 'profile_image', $review->id);
        }

        return redirect(route('admin.home-page.customer.reviews.index'))->withSuccess('Home review created successfully!');
    }

    public function edit($id)
    {
        $review = LandingHomeReview::findOrFail($id);
        return view('admin.home-page.customer.reviews.edit', compact('review'));
    }

    public function update(Request $request, $id)
    {
        $review = LandingHomeReview::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|boolean',
            'order_by' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        if ($review->customer_name != $request->customer_name) {
            $slug = createUniqueSlug('landing_home_reviews', $request->customer_name, 'slug', $review->id);
            $review->slug = $slug;
        }

        $review->update([
            'customer_name' => $request->customer_name,
            'description' => $request->description,
            'status' => $request->status,
            'order_by' => $request->order_by,
        ]);

        if ($request->hasFile('image')) {
            $this->imageUpload($request->file("image"), "landing_home_reviews", $review->id, 'landing_home_reviews', 'profile_image', $review->id);
        }

        return redirect(route('admin.home-page.customer.reviews.index'))->withSuccess('Home review updated successfully!');
    }

    public function destroy($id)
    {
        $review = LandingHomeReview::findOrFail($id);
        static::deleteFile($review->id, 'landing_home_reviews', 'profile_image');
        $review->delete();

        return redirect(route('admin.home-page.customer.reviews.index'))->withSuccess('Home review deleted successfully!');
    }
}
