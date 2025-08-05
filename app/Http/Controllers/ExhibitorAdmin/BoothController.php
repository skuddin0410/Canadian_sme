<?php

namespace App\Http\Controllers\ExhibitorAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booth;
use App\Models\Company;

class BoothController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $booths = Booth::with('company')->latest()->paginate(10);
        if ($request->ajax()) {
        return view('company.booths.partials.booth-table', compact('booths'))->render();
    }
        return view('company.booths.index', compact('booths'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $companies = Company::all();
        return view('company.booths.create', compact('companies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
         $request->validate([
            // 'company_id' => 'required|exists:companies,id',
            'title' => 'required|string|max:255',
            'booth_number' => 'required|string|max:50',
            'size' => 'required|string|max:50',
            'location_preferences' => 'required|string',
        ]);

        Booth::create($request->all());

        return redirect()->route('booths.index')->with('success', 'Booth created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    $booth = Booth::with('company')->findOrFail($id);
    return view('company.booths.show', compact('booth'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $booth = Booth::findOrFail($id);
        $companies = Company::all();
        return view('company.booths.edit', compact('booth', 'companies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $booth = Booth::findOrFail($id);

        $request->validate([
            // 'company_id' => 'required|exists:companies,id',
            'title' => 'required|string|max:255',
            'booth_number' => 'required|string|max:50',
            'size' => 'required|string|max:50',
            'location_preferences' => 'required|string',
        ]);

        $booth->update($request->all());

        return redirect()->route('booths.index')->with('success', 'Booth updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
         Booth::findOrFail($id)->delete();
        return back()->with('success', 'Booth deleted successfully.');
    }
}
