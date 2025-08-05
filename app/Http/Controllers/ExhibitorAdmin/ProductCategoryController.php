<?php

namespace App\Http\Controllers\ExhibitorAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
         $categories = ProductCategory::with(['parent', 'children'])
                                   ->orderBy('sort_order')
                                   ->orderBy('name')
                                   ->paginate(20);

        return view('company.product-categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
         $parentCategories = ProductCategory::active()->parents()->orderBy('name')->get();
        return view('company.product-categories.create', compact('parentCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:product_categories,id',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
            'image_url' => 'nullable|url'
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $category = ProductCategory::create($validated);

        return redirect()->route('product-categories.index')
                        ->with('success', 'Product category created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductCategory $productCategory)
    {
        //
        

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $parentCategories = ProductCategory::active()
                                         ->parents()
                                         ->where('id', '!=', $productCategory->id)
                                         ->orderBy('name')
                                         ->get();

        return view('company.product-categories.edit', compact('productCategory', 'parentCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductCategory $productCategory)
    {
        //
         $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:product_categories,id',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
            'image_url' => 'nullable|url'
        ]);

        if ($validated['name'] !== $productCategory->name) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $productCategory->update($validated);

        return redirect()->route('product-categories.index')
                        ->with('success', 'Product category updated successfully.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductCategory $productCategory)
    {
        //
        if ($productCategory->products()->exists()) {
            return back()->with('error', 'Cannot delete category with existing products.');
        }

        $productCategory->delete();
        
        return redirect()->route('product-categories.index')
                        ->with('success', 'Product category deleted successfully.');
    }
}
