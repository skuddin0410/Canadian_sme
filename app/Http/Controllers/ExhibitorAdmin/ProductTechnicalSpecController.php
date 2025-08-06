<?php

namespace App\Http\Controllers\ExhibitorAdmin;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ProductTechnicalSpec;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class ProductTechnicalSpecController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request , Product $product)
    {
        //
          $validated = $request->validate([
            'spec_name' => 'required|string|max:255',
            'spec_value' => 'required|string',
            'spec_unit' => 'nullable|string|max:50',
            'spec_category' => 'nullable|string|max:100',
            'is_important' => 'boolean',
            'sort_order' => 'integer|min:0'
        ]);

        $product->technicalSpecs()->create($validated);

        return back()->with('success', 'Technical specification added successfully.');
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product, ProductTechnicalSpec $spec)
    {
        //
          $validated = $request->validate([
            'spec_name' => 'required|string|max:255',
            'spec_value' => 'required|string',
            'spec_unit' => 'nullable|string|max:50',
            'spec_category' => 'nullable|string|max:100',
            'is_important' => 'boolean',
            'sort_order' => 'integer|min:0'
        ]);

        $spec->update($validated);

        return back()->with('success', 'Technical specification updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product, ProductTechnicalSpec $spec)
    {
        //
        $spec->delete();
        return back()->with('success', 'Technical specification deleted successfully.');
    }
}
