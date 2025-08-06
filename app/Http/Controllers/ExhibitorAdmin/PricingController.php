<?php

namespace App\Http\Controllers\ExhibitorAdmin;

use App\Models\Product;
use App\Models\Service;
use Illuminate\Http\Request;
use App\Models\ProductPricing;
use App\Models\ServicePricing;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class PricingController extends Controller
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
    public function storeProductPricing(Request $request, Product $product)
    {
        //
         $validated = $request->validate([
            'tier_name' => 'required|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'currency' => 'required|string|size:3',
            'billing_period' => 'nullable|string|max:50',
            'features' => 'nullable|array',
            'is_quote_based' => 'boolean',
            'is_popular' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0'
        ]);

        $product->pricingTiers()->create($validated);

        return back()->with('success', 'Pricing tier added successfully.');
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
    public function updateProductPricing(Request $request, Product $product, ProductPricing $pricing)
    {
        //
        $validated = $request->validate([
            'tier_name' => 'required|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'currency' => 'required|string|size:3',
            'billing_period' => 'nullable|string|max:50',
            'features' => 'nullable|array',
            'is_quote_based' => 'boolean',
            'is_popular' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0'
        ]);

        $pricing->update($validated);

        return back()->with('success', 'Pricing tier updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroyProductPricing(Product $product, ProductPricing $pricing)
    {
        //
        $pricing->delete();
        return back()->with('success', 'Pricing tier deleted successfully.');
        
    }
      public function storeServicePricing(Request $request, Service $service)
    {
        $validated = $request->validate([
            'tier_name' => 'required|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'currency' => 'required|string|size:3',
            'billing_period' => 'nullable|string|max:50',
            'features' => 'nullable|array',
            'is_quote_based' => 'boolean',
            'is_popular' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0'
        ]);

        $service->pricingTiers()->create($validated);

        return back()->with('success', 'Pricing tier added successfully.');
    }

    public function updateServicePricing(Request $request, Service $service, ServicePricing $pricing)
    {
        $validated = $request->validate([
            'tier_name' => 'required|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'currency' => 'required|string|size:3',
            'billing_period' => 'nullable|string|max:50',
            'features' => 'nullable|array',
            'is_quote_based' => 'boolean',
            'is_popular' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0'
        ]);

        $pricing->update($validated);

        return back()->with('success', 'Pricing tier updated successfully.');
    }

    public function destroyServicePricing(Service $service, ServicePricing $pricing)
    {
        $pricing->delete();
        return back()->with('success', 'Pricing tier deleted successfully.');
    }
}
