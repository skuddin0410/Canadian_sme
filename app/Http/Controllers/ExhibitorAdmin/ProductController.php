<?php

namespace App\Http\Controllers\ExhibitorAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
         $query = Product::with(['category', 'creator'])
            ->when($request->category_id, function($q, $categoryId) {
                return $q->where('category_id', $categoryId);
            })
            ->when($request->search, function($q, $search) {
                return $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($request->status, function($q, $status) {
                return $status === 'active' ? $q->active() : $q->where('is_active', false);
            });

        $products = $query->orderBy('sort_order')
                         ->orderBy('name')
                         ->paginate(15);
                        //  dd($products);

        $categories = ProductCategory::active()->orderBy('name')->get();

        return view('company.products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $categories = ProductCategory::active()->orderBy('name')->get();
        return view('company.products.create', compact('categories'));

    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'required|string',
        'category_id' => 'nullable|exists:products_categories,id',
        'features' => 'nullable|string',
        'benefits' => 'nullable|string',
        'is_active' => 'boolean',
        'sort_order' => 'integer|min:0',
        'meta_title' => 'nullable|string|max:255',
        'meta_description' => 'nullable|string|max:500',
        'main_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
    ]);

    // Parse string inputs to arrays
    $validated['features'] = $request->filled('features')
        ? array_filter(preg_split('/\r\n|\r|\n/', $request->input('features')))
        : [];

    $validated['benefits'] = $request->filled('benefits')
        ? array_filter(preg_split('/\r\n|\r|\n/', $request->input('benefits')))
        : [];

    $validated['slug'] = Str::slug($validated['name']);
    $validated['created_by'] = Auth::id();

    // Main Image Upload
    if ($request->hasFile('main_image')) {
        $path = $request->file('main_image')->store('products/main', 'public');
        $validated['image_url'] = $path;
    }

    // Gallery Images Upload
    if ($request->hasFile('gallery_images')) {
        $galleryPaths = [];
        foreach ($request->file('gallery_images') as $img) {
            $galleryPaths[] = $img->store('products/gallery', 'public');
        }
        $validated['gallery_images'] = $galleryPaths;
    }

    Product::create($validated);

    return redirect()->route('products.index')
        ->with('success', 'Product created successfully.');
}

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
        $product->load(['category', 'technicalSpecs', 'pricingTiers.active', 'creator', 'updater']);
        return view('company.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
         $categories = ProductCategory::active()->orderBy('name')->get();
        return view('company.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        //
         $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'nullable|exists:products_categories,id',
            'features' => 'nullable|array',
            'benefits' => 'nullable|array',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
             'main_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
           'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
            // 'image_url' => 'nullable|url',
            // 'gallery_images' => 'nullable|array',
            // 'gallery_images.*' => 'url'
        ]);
        // Parse string inputs to arrays
    $validated['features'] = $request->filled('features')
        ? array_filter(preg_split('/\r\n|\r|\n/', $request->input('features')))
        : [];

    $validated['benefits'] = $request->filled('benefits')
        ? array_filter(preg_split('/\r\n|\r|\n/', $request->input('benefits')))
        : [];

        if ($validated['name'] !== $product->name) {
            $validated['slug'] = Str::slug($validated['name']);
        }
        
        $validated['updated_by'] = Auth::id();
           // Main Image Upload
    if ($request->hasFile('main_image')) {
        $path = $request->file('main_image')->store('products/main', 'public');
        $validated['image_url'] = $path;
    }

    // Gallery Images Upload
    if ($request->hasFile('gallery_images')) {
        $galleryPaths = [];
        foreach ($request->file('gallery_images') as $img) {
            $galleryPaths[] = $img->store('products/gallery', 'public');
        }
        $validated['gallery_images'] = $galleryPaths;
    }


        $product->update($validated);

        return redirect()->route('products.show', $product)
                        ->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
        $product->delete();
        return redirect()->route('products.index')
                        ->with('success', 'Product deleted successfully.');
    }
    // API Methods for frontend
    public function apiIndex(Request $request)
    {
        $query = Product::active()
            ->with(['category', 'pricingTiers' => function($q) {
                $q->active()->orderBy('sort_order');
            }])
            ->when($request->category, function($q, $category) {
                return $q->whereHas('category', function($sq) use ($category) {
                    $sq->where('slug', $category);
                });
            });

        $products = $query->orderBy('sort_order')
                         ->orderBy('name')
                         ->get();

        return response()->json($products);
    }

    public function apiShow($slug)
    {
        $product = Product::active()
            ->with(['category', 'technicalSpecs', 'pricingTiers' => function($q) {
                $q->active()->orderBy('sort_order');
            }])
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json($product);
    }
}
