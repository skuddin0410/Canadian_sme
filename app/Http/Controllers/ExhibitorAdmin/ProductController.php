<?php

namespace App\Http\Controllers\ExhibitorAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use App\Models\ProductCategory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use DataTables;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->input('perPage', 20);
        $pageNo = (int) $request->input('page', 1);
        $offset = $perPage * ($pageNo - 1);

      if($request->ajax() && $request->ajax_request == true){
        $products = Product::with(['category', 'creator'])->orderBy('id','DESC');
        if($request->category_id){
            $products = $products->where(function($query) use($request){
                    $query->where('category_id', $request->category_id);
                });
        }

        if($request->search){
            $products = $products->where(function($query) use($request){
                    $query->where('name', 'LIKE', '%'. $request->search .'%')
                    ->orWhere('description', 'like', '%'. $request->search .'%');
                });
        }
       
        if(!empty($request->status) && $request->status == 'active'){
            
            $products = $products->where(function($query) use($request){
                    $query->where('is_active', 1);
                });
        }

        if( !empty($request->status) && $request->status == 'inactive'){
            
            $products = $products->where(function($query) use($request){
                    $query->where('is_active', 0);
                });
        }

        $productsCount = clone $products;
        $totalRecords = $productsCount->count(DB::raw('DISTINCT(products.id)'));  
        $products = $products->offset($offset)->limit($perPage)->get();       
        $products = new LengthAwarePaginator($products, $totalRecords, $perPage, $pageNo, [
                  'path'  => $request->url(),
                  'query' => $request->query(),
                ]);
        $data['offset'] = $offset;
        $data['pageNo'] = $pageNo;
        $products->setPath(route('products.index'));
        $data['html'] = view('company.products.table', compact('products', 'perPage'))
                  ->with('i', $pageNo * $perPage)
                  ->render();

         return response($data);                                              
        }
        $categories = ProductCategory::active()->orderBy('name')->get();   
                   
        return view('company.products.index',["categories"=>$categories]);
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
        'name'              => 'required|string|max:255',
        'price'             => 'required|numeric|gte:0',
        'description'       => 'required|string',
        'category_id'       => 'required|exists:products_categories,id',
        'features'          => 'nullable|string',
        'benefits'          => 'nullable|string',
        'is_active'         => 'boolean',
        'sort_order'        => 'integer|min:0',
        'meta_title'        => 'nullable|string|max:255',
        'meta_description'  => 'nullable|string|max:500',
        'meta_keywords'     => 'nullable|string|max:500',
        'main_image'        => 'required|image|mimes:jpeg,png,jpg|max:2048',
        'gallery_images.*'  => 'required|image|mimes:jpeg,png,jpg|max:2048'
    ]);

    $validated['slug']       = Str::slug($validated['name']);
    $validated['created_by'] = Auth::id();

    // Upload main image
    if ($request->hasFile('main_image')) {
        $validated['image_url'] = $request->file('main_image')->store('products/main', 'public');
    }

    // Upload gallery images
    if ($request->hasFile('gallery_images')) {
        $galleryPaths = [];
        foreach ($request->file('gallery_images') as $img) {
            $galleryPaths[] = $img->store('products/gallery', 'public');
        }
        $validated['gallery_images'] = $galleryPaths;
    }

    // Fetch the authenticated user with their company
    $user = Auth::user()->load('company');

    if (!$user->company) {
        return redirect()->back()
            ->withErrors(['company' => 'You must have a company profile before adding products.'])
            ->withInput();
    }

    $validated['company_id'] = $user->company->id;
    $validated['user_id']    = $user->id;

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
        $product->load(['category', 'technicalSpecs', 'creator', 'updater']);
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
            'price' => 'required|numeric|gte:0',
            'description' => 'required|string',
            'category_id' => 'required|exists:products_categories,id',
            'features' => 'nullable|string',
            'benefits' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'main_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'gallery_images.*' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

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

        return redirect()->route('products.index', $product)
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
