<?php

namespace App\Http\Controllers\ExhibitorAdmin;

use App\Models\Product;
use App\Models\Service;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Models\ServiceCategory;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Service::with(['category', 'creator'])
            ->when($request->category_id, function ($q, $categoryId) {
                return $q->where('category_id', $categoryId);
            })
            ->when($request->search, function ($q, $search) {
                return $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($request->status, function ($q, $status) {
                return $status === 'active' ? $q->active() : $q->where('is_active', false);
            });

        $services = $query->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15);

        $categories = ServiceCategory::active()->orderBy('name')->get();

        return view('company.services.index', compact('services', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = ServiceCategory::active()->orderBy('name')->get();
        return view('company.services.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|gte:0',
                'description' => 'required|string',
                'category_id' => 'nullable|exists:service_categories,id',
                'duration' => 'nullable|string|max:255',
                'is_active' => 'nullable|string',
                'sort_order' => 'integer|min:0',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string|max:500',
                'meta_keywords' => 'nullable|string|max:500',
                'capabilities' => 'nullable|string|max:500',
                'deliverables' => 'nullable|string|max:500',
                'main_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'gallery_images' => 'nullable|array',
                'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
            ]);

            $validated['slug'] = Str::slug($validated['name']);
            $validated['created_by'] = Auth::id() ?? null;

            // Upload main image
            if ($request->hasFile('main_image')) {
                $validated['image_url'] = $request->file('main_image')->store('services/main', 'public');
            }

            // Upload gallery images
            if ($request->hasFile('gallery_images')) {
                $galleryPaths = [];
                foreach ($request->file('gallery_images') as $img) {
                    $galleryPaths[] = $img->store('services/gallery', 'public');
                }
                $validated['gallery_images'] = $galleryPaths;
            }
            
            $service = Service::create($validated);
            return redirect()->route('services.index')->with('success', 'Service created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        $service->load(['category','creator', 'updater']);
        return view('company.services.show', compact('service'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        $categories = ServiceCategory::active()->orderBy('name')->get();
        return view('company.services.edit', compact('service', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|gte:0',
            'description' => 'required|string',
            'category_id' => 'nullable|exists:service_categories,id',
            'duration' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'capabilities' => 'nullable|string|max:500',
            'deliverables' => 'nullable|string|max:500',
             'main_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($validated['name'] !== $service->name) {
            $validated['slug'] = Str::slug($validated['name']);
        }

         if ($request->hasFile('main_image')) {
            $validated['image_url'] = $request->file('main_image')->store('services/main', 'public');
        }

        // Upload gallery images
        if ($request->hasFile('gallery_images')) {
            $galleryPaths = [];
            foreach ($request->file('gallery_images') as $img) {
                $galleryPaths[] = $img->store('services/gallery', 'public');
            }
            $validated['gallery_images'] = $galleryPaths;
        }

        $validated['updated_by'] = Auth::id();

        $service->update($validated);



        return redirect()->route('services.index', $service)
            ->with('success', 'Service updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()->route('services.index')
            ->with('success', 'Service deleted successfully.');
    }

    // API Methods for frontend
    public function apiIndex(Request $request)
    {
        $query = Service::active()
            ->with(['category', 'pricingTiers' => function ($q) {
                $q->active()->orderBy('sort_order');
            }])
            ->when($request->category, function ($q, $category) {
                return $q->whereHas('category', function ($sq) use ($category) {
                    $sq->where('slug', $category);
                });
            });

        $services = $query->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return response()->json($services);
    }

    public function apiShow($slug)
    {
        $service = Service::active()
            ->with(['category', 'pricingTiers' => function ($q) {
                $q->active()->orderBy('sort_order');
            }])
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json($service);
    }
}
