<?php

namespace App\Http\Controllers\ExhibitorAdmin;

use App\Models\Company;
use App\Models\CompanyContact;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use DataTables;

class CompanyContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $company = Company::where('user_id', auth()->id())->first();
        $perPage = (int) $request->input('perPage', 20);
        $pageNo = (int) $request->input('page', 1);
        $offset = $perPage * ($pageNo - 1);

      if($request->ajax() && $request->ajax_request == true){
        $contacts = CompanyContact::orderBy('id','DESC');

        if($request->search){
            $contacts = $contacts->where(function($query) use($request){
                    $query->where('name', 'LIKE', '%'. $request->search .'%');
                });
        }


        $contactsCount = clone $contacts;
        $totalRecords = $contactsCount->count(DB::raw('DISTINCT(company_contacts.company_id)'));  
        $contacts = $contacts->offset($offset)->limit($perPage)->get();     
        $contacts = new LengthAwarePaginator($contacts, $totalRecords, $perPage, $pageNo, [
                  'path'  => $request->url(),
                  'query' => $request->query(),
                ]);
        $data['offset'] = $offset;
        $data['pageNo'] = $pageNo;
        $contacts->setPath(route('company.contacts.index'));
        $data['html'] = view('company.contact-table', compact('contacts', 'perPage'))
                  ->with('i', $pageNo * $perPage)
                  ->render();

         return response($data);                                              
        }   
                   
        return view('company.contacts');
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
        return redirect()->route('company.contacts.index')->with('success', 'Contact added successfully.');
   
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
        $contact = CompanyContact::findOrFail($id);
        $contact->delete();
        return redirect()->route('company.contacts.index')->with('success', 'Contact deleted.');
    }
}
