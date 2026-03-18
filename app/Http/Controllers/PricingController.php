<?php

namespace App\Http\Controllers;

use App\Models\Pricing;
use App\Models\PricingCms;
use App\Models\PricingFeature;
use App\Models\PricingFeatureValue;
use Illuminate\Http\Request;

class PricingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pricings = Pricing::orderBy('order_by', 'asc')->get();
        return view('admin.pricing.setup', compact('pricings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pricing.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'amount' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'attendee_count' => 'nullable|integer',
            'timespan' => 'nullable|string|max:255',
            'mostpopular' => 'nullable|boolean',
            'event_no' => 'nullable|integer',
            'status' => 'nullable|boolean',
            'order_by' => 'nullable|integer',
        ]);

        Pricing::create($request->all());

        return redirect()->route('admin.pricing.setup.index')->with('success', 'Pricing added successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $pricing = Pricing::findOrFail($id);
        return view('admin.pricing.edit', compact('pricing'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $pricing = Pricing::findOrFail($id);

        $request->validate([
            'name' => 'nullable|string|max:255',
            'amount' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'attendee_count' => 'nullable|integer',
            'timespan' => 'nullable|string|max:255',
            'mostpopular' => 'nullable|boolean',
            'event_no' => 'nullable|integer',
            'status' => 'nullable|boolean',
            'order_by' => 'nullable|integer',
        ]);

        $data = $request->all();
        $data['mostpopular'] = $request->has('mostpopular') ? 1 : 0;
        $data['status'] = $request->has('status') ? 1 : 0;

        $pricing->update($data);

        return redirect()->route('admin.pricing.setup.index')->with('success', 'Pricing updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $pricing = Pricing::findOrFail($id);
        $pricing->delete();

        return redirect()->route('admin.pricing.setup.index')->with('success', 'Pricing deleted successfully.');
    }

    /**
     * Manage Pricing CMS.
     */
    public function cms()
    {
        $cms = PricingCms::first();
        $pricings = Pricing::orderBy('order_by', 'asc')->get();
        $features = PricingFeature::with('values')->orderBy('order_by', 'asc')->get();

        return view('admin.pricing.cms', compact('cms', 'pricings', 'features'));
    }

    /**
     * Update Pricing CMS.
     */
    public function updateCms(Request $request)
    {
        $request->validate([
            'main_heading' => 'nullable|string|max:255',
            'main_description' => 'nullable|string',
            'Feature_heading' => 'nullable|string|max:255',
            'Feature_description' => 'nullable|string',
        ]);

        $cms = PricingCms::first();
        if (!$cms) {
            PricingCms::create($request->all());
        } else {
            $cms->update($request->all());
        }

        return redirect()->back()->with('success', 'Pricing CMS updated successfully.');
    }

    /**
     * Store new feature.
     */
    public function storeFeature(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'order_by' => 'nullable|integer',
        ]);

        $feature = PricingFeature::create([
            'name' => $request->name,
            'order_by' => $request->order_by ?? 0,
            'status' => 1,
        ]);

        // Values for each plan
        if ($request->has('values')) {
            foreach ($request->values as $pricingId => $value) {
                PricingFeatureValue::create([
                    'feature_id' => $feature->id,
                    'pricing_id' => $pricingId,
                    'value' => $value,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Feature added successfully.');
    }

    /**
     * Update feature.
     */
    public function updateFeature(Request $request, $id)
    {
        $feature = PricingFeature::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'order_by' => 'nullable|integer',
        ]);

        $feature->update([
            'name' => $request->name,
            'order_by' => $request->order_by ?? 0,
            'status' => $request->has('status') ? 1 : 0,
        ]);

        // Sync values
        if ($request->has('values')) {
            foreach ($request->values as $pricingId => $value) {
                PricingFeatureValue::updateOrCreate(
                    ['feature_id' => $feature->id, 'pricing_id' => $pricingId],
                    ['value' => $value]
                );
            }
        }

        return redirect()->back()->with('success', 'Feature updated successfully.');
    }

    /**
     * Delete feature.
     */
    public function destroyFeature($id)
    {
        $feature = PricingFeature::findOrFail($id);
        $feature->delete();

        return redirect()->back()->with('success', 'Feature deleted successfully.');
    }
}
