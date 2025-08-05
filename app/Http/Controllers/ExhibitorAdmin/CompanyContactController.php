<?php

namespace App\Http\Controllers\ExhibitorAdmin;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CompanyContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
         $company = Company::where('user_id', auth()->id())->firstOrFail();
    $contacts = $company->contacts()->get();

    return view('company.contacts', compact('company', 'contacts'));
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
    public function store(Request $request)
    {
        //
         $company = Company::where('user_id', auth()->id())->firstOrFail();

    $request->validate([
        // 'type' => 'required|string|max:255',
        'name' => 'required|string|max:255',
        'email' => 'nullable|email',
        'phone' => 'nullable|string|max:20',
    ]);

    $company->contacts()->create($request->only(['type', 'name', 'email', 'phone']));

    return redirect()->route('company.contacts')->with('success', 'Contact added.');
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
         if ($contact->company->user_id !== auth()->id()) {
        abort(403);
    }

    $contact->delete();
    return redirect()->route('company.contacts')->with('success', 'Contact deleted.');
    }
}
