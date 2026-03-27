<?php

namespace App\Http\Controllers;

use App\Models\NavbarDynamic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NavbarDynamicController extends Controller
{
    public function index()
    {
        $navbars = NavbarDynamic::orderBy('order_by', 'asc')->get();
        return view('admin.navbar-dynamic.index', compact('navbars'));
    }

    public function create()
    {
        $categories = NavbarDynamic::whereNotNull('category')->distinct()->pluck('category');
        return view('admin.navbar-dynamic.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
            'order_by' => 'required|integer',
            'category' => 'nullable|string|max:255',
            'content' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        NavbarDynamic::create($request->only(['title', 'status', 'order_by', 'category', 'content']));

        return redirect(route('admin.navbar-dynamic.index'))->withSuccess('Navbar item created successfully!');
    }

    public function edit($id)
    {
        $navbar = NavbarDynamic::findOrFail($id);
        $categories = NavbarDynamic::whereNotNull('category')->distinct()->pluck('category');
        return view('admin.navbar-dynamic.edit', compact('navbar', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $navbar = NavbarDynamic::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
            'order_by' => 'required|integer',
            'category' => 'nullable|string|max:255',
            'content' => 'nullable',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $navbar->update($request->only(['title', 'status', 'order_by', 'category', 'content']));

        return redirect(route('admin.navbar-dynamic.index'))->withSuccess('Navbar item updated successfully!');
    }

    public function destroy($id)
    {
        $navbar = NavbarDynamic::findOrFail($id);
        $navbar->delete();

        return redirect(route('admin.navbar-dynamic.index'))->withSuccess('Navbar item deleted successfully!');
    }
    public function uploadImage(Request $request)
{
    try {
        // ✅ Ensure request expects JSON (avoids HTML validation errors)
        if (!$request->expectsJson()) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid request type'
            ], 400);
        }

        // ✅ Validate
        $validator = \Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()->first()
            ], 422);
        }

        // ✅ Check file exists
        if (!$request->hasFile('image')) {
            return response()->json([
                'success' => false,
                'error' => 'No file uploaded'
            ], 400);
        }

        $file = $request->file('image');

        // ✅ Generate unique filename
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        // ✅ Store file
        $path = $file->storeAs('uploads', $filename, 'public');

        // ✅ Generate URL
        $url = asset('storage/' . $path);

        return response()->json([
            'success' => true,
            'url' => $url
        ]);

    } catch (\Throwable $e) {

        // ✅ Log error (VERY IMPORTANT for debugging)
        \Log::error('Image Upload Error: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'error' => 'Upload failed. Please try again.'
        ], 500);
    }
}
    
}
