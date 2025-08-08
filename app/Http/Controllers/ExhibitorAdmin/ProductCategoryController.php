<?php

namespace App\Http\Controllers\ExhibitorAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use DataTables;
use App\Models\Drive;
use Illuminate\Support\Facades\Storage;

class ProductCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->input('perPage', 20);
        $pageNo = (int) $request->input('page', 1);
        $offset = $perPage * ($pageNo - 1);

        $query = ProductCategory::query()->orderBy('created_at', 'DESC');

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                  ->orWhere('slug', 'LIKE', '%' . $search . '%')
                  ->orWhere('description', 'LIKE', '%' . $search . '%');
            });
        }

        // Optional filter example (like is_active)
        if ($request->has('is_active') && in_array($request->is_active, ['0', '1'])) {
            $query->where('is_active', $request->is_active);
        }

        $totalRecords = $query->count();

        $categories = $query->offset($offset)->limit($perPage)->get();

        $categoriesPaginated = new LengthAwarePaginator(
            $categories,
            $totalRecords,
            $perPage,
            $pageNo,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        if ($request->ajax() && $request->ajax_request == true) {
            $data['offset'] = $offset;
            $data['pageNo'] = $pageNo;
            $categoriesPaginated->setPath(route('product-categories.index'));

            $data['html'] = view('company.product-categories.table', [
                'categories' => $categoriesPaginated,
                'perPage' => $perPage,
            ])->with('i', $pageNo * $perPage)->render();

            return response()->json($data);
        }

        return view('company.product-categories.index', [
            'categories' => $categoriesPaginated,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        //  $parentCategories = ProductCategory::active()->parents()->orderBy('name')->get();
        return view('company.product-categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
     
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    $validated['slug'] = Str::slug($validated['name']);

    if ($request->hasFile('image')) {
        $path = $request->file('image')->store('product_categories', 'public');
        $validated['image_url'] = '/storage/' . $path;
    } else {
        $validated['image_url'] = null;
    }

    ProductCategory::create($validated);

    return redirect()->route('product-categories.index')
        ->with('success', 'Product category created successfully.');
}

    

    /**
     * Display the specified resource.
     */
   public function show(ProductCategory $productCategory)
{
    return view('company.product-categories.show', ['category' => $productCategory]);
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        // $parentCategories = ProductCategory::active()
        //                                  ->parents()
        //                                  ->where('id', '!=', $productCategory->id)
        //                                  ->orderBy('name')
        //                                  ->get();

        return view('company.product-categories.edit', compact('productCategory'));
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
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        // add more fields if you have them, like 'is_active', 'sort_order' etc.
    ]);

    // If the name changed, update slug too
    if ($validated['name'] !== $productCategory->name) {
        $validated['slug'] = Str::slug($validated['name']);
    }

    // Handle image upload if any
    if ($request->hasFile('image')) {
        $path = $request->file('image')->store('product_categories', 'public');
        $validated['image_url'] = '/storage/' . $path;
    }

    // Update the model
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
