<?php

namespace App\Http\Controllers\ExhibitorAdmin;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ServiceCategory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class ServiceCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $categories = ServiceCategory::with(['parent', 'children'])
                                   ->orderBy('sort_order')
                                   ->orderBy('name')
                                   ->paginate(20);

        return view('company.service-categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
          $parentCategories = ServiceCategory::active()->parents()->orderBy('name')->get();
        return view('company.service-categories.create', compact('parentCategories'));
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
            'parent_id' => 'nullable|exists:service_categories,id',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
            'image_url' => 'nullable|url'
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $category = ServiceCategory::create($validated);

        return redirect()->route('service-categories.index')
                        ->with('success', 'Service category created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ServiceCategory $serviceCategory)
    {
        //
        $parentCategories = ServiceCategory::active()
                                         ->parents()
                                         ->where('id', '!=', $serviceCategory->id)
                                         ->orderBy('name')
                                         ->get();

        return view('company.service-categories.edit', compact('serviceCategory', 'parentCategories'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ServiceCategory $serviceCategory)
    {
        //
         $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:service_categories,id',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
            'image_url' => 'nullable|url'
        ]);

        if ($validated['name'] !== $serviceCategory->name) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $serviceCategory->update($validated);

        return redirect()->route('service-categories.index')
                        ->with('success', 'Service category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ServiceCategory $serviceCategory)
    {
        //
           if ($serviceCategory->services()->exists()) {
            return back()->with('error', 'Cannot delete category with existing services.');
        }

        $serviceCategory->delete();
        
        return redirect()->route('service-categories.index')
                        ->with('success', 'Service category deleted successfully.');
    }
}
