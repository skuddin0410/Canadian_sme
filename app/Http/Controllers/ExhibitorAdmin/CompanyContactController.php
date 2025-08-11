<?php

namespace App\Http\Controllers\ExhibitorAdmin;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Models\CompanyContact;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CompanyContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
   
    $company = Company::where('user_id', auth()->id())->first();
    if(empty($company)){
      return redirect()->route('company.details')->with('success', 'Update Company details first.');
    }

    $contacts = $company->contacts()->get();
    if ($request->ajax()) {
        $html = view('company.partials.contacts-table', compact('contacts'))->render();
        return response()->json(['html' => $html]);
    }

    return view('company.contacts', compact('company', 'contacts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {   
        return view('company.contacts-create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $company = Company::where('user_id', auth()->id())->firstOrFail();
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
        ]);
        $company->contacts()->create($request->only(['name', 'email', 'phone']));
        return redirect()->route('contacts.index')->with('success', 'Contact added successfully.');
   
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
    // public function destroy(Request $request,string $id)
    // {
    //      $contact = CompanyContact::findOrFail($id);

    //     if ($contact->company->user_id !== auth()->id()) {
    //         abort(403);
    //     }

    //     $contact->delete();

    //     if ($request->ajax()) {
    //         $company = Company::where('user_id', auth()->id())->firstOrFail();
    //         $contacts = $company->contacts()->get();
    //         $html = view('company.partials.contacts-table', compact('contacts'))->render();
    //         return response()->json(['success' => true, 'html' => $html]);
    //     }

    //     return redirect()->route('contacts.index')->with('success', 'Contact deleted.');
    // }
    public function destroy(Request $request, string $id)
{
    $contact = CompanyContact::findOrFail($id);

    if ($contact->company->user_id !== auth()->id()) {
        abort(403);
    }

    $contact->delete();

    if ($request->ajax()) {
        $company = Company::where('user_id', auth()->id())->firstOrFail();
        $contacts = $company->contacts()->get();
        $html = view('company.partials.contacts-table', compact('contacts'))->render();
        return response()->json(['success' => true, 'html' => $html]);
    }

    return redirect()->route('contacts.index')->with('success', 'Contact deleted.');
}

}
