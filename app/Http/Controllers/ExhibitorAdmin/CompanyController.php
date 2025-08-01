<?php

namespace App\Http\Controllers\ExhibitorAdmin;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
//     public function details(Request $request)
// {
//     if ($request->ajax() && $request->ajax_request == true) {
//         $companies = Company::where('user_id', auth()->id())->orderBy('id', 'DESC');

//         $perPage = $request->get('perPage', 10);
//         $pageNo = $request->get('page', 1);
//         $offset = $perPage * ($pageNo - 1);

//         $totalRecords = $companies->count();
//         $companies = $companies->offset($offset)->limit($perPage)->get();

//         $companies = new \Illuminate\Pagination\LengthAwarePaginator($companies, $totalRecords, $perPage, $pageNo, [
//             'path' => $request->url(),
//             'query' => $request->query(),
//         ]);

//         $html = view('company.table', compact('companies'))->render();
//         return response()->json(['html' => $html, 'offset' => $offset, 'pageNo' => $pageNo]);
//     }

//     return view('company.index');
// }

 public function details()
{
    $company = Company::where('user_id', Auth::id())->first();
    if (!$company) {
        return redirect()->route('company.create')->with('info', 'Please add your company information.');
    }

    return view('company.details', compact('company'));
}


    public function index(Request $request)
    {
        //
        if ($request->ajax() && $request->ajax_request == true) {
        $companies = Company::where('user_id', Auth::id())->orderBy('id', 'DESC');

        if ($request->search) {
            $companies = $companies->where('name', 'like', '%' . $request->search . '%');
        }

        $companies = $companies->paginate($request->get('perPage', 10));

        $data['html'] = view('company.table', compact('companies'))->render();
        return response($data);
    }

    return view('company.index');
        //  if ($request->ajax() && $request->ajax_request == true) {
        //     $companies = Company::where('user_id', Auth::id())->orderBy('id', 'DESC');

        //     if ($request->search) {
        //         $companies = $companies->where('name', 'like', '%' . $request->search . '%');
        //     }

        //     $companies = $companies->paginate($request->get('perPage', 10));

        //     $data['html'] = view('company.table', compact('companies'))->render();
        //     return response($data);
        // }

        // return view('company.index');

       
        // $companies = Company::with('user')->latest()->paginate(10);
        // return view('company.index', compact('companies'));
   
    }

    // /**
    //  * Show the form for creating a new resource.
    //  */
    public function create()
    {
        //
         
        return view('company.create');
    
    }

    // /**
    //  * Store a newly created resource in storage.
    //  */
    public function store(Request $request)
    {
        //
          $validator = Validator::make($request->all(), [
            'name'          => 'required|string|max:255',
            'industry'      => 'required|string|max:255',
            'size'          => 'required|string|max:255',
            'location'      => 'required|string|max:255',
            'email'         => 'required|email|max:255',
            'phone'         => 'required|string|max:20',
            // 'description'   => 'required|string',
            // 'website'       => 'required|url',
            // 'linkedin'      => 'nullable|url',
            // 'twitter'       => 'nullable|url',
            // 'facebook'      => 'nullable|url',
            // 'certifications'=> 'nullable|string',
        ]);

        //    $request->validate([
        //     'name' => 'required|string|max:255',
        // ]);

        // $company = Company::create([
        //     'user_id' => auth()->id(),
        //     'name' => $request->name,
        //     'industry' => $request->industry,
        //     'size' => $request->size,
        //     'location' => $request->location,
        //     'email' => $request->email,
        //     'phone' => $request->phone,
        //     'description' => $request->description,
        //     'website' => $request->website,
        //     'linkedin' => $request->linkedin,
        //     'twitter' => $request->twitter,
        //     'facebook' => $request->facebook,
        //     'certifications' => $request->certifications,
        // ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $company = new Company();
        $company->user_id = Auth::id();
        $company->fill($request->only(['name', 'industry', 'size', 'location', 'email' , 'phone']));

        // if ($request->hasFile('logo')) {
        //     $file = $request->file('logo');
        //     $path = $file->store('uploads/companies', 'public');
        //     $company->logo = $path;
        // }

        $company->save();


        return redirect()->route('company.show', $company)->with('success', 'Company created.');
    }

    // /**
    //  * Display the specified resource.
    //  */
    public function show(Company $company)
    {
        //
        if ($company->user_id !== Auth::id()) {
            abort(403);
        }
        return view('company.show', compact('company'));
    }

    // /**
    //  * Show the form for editing the specified resource.
    //  */
    public function edit(Company $company)
    {
        //
         if ($company->user_id !== Auth::id()) {
            abort(403);
        }
        return view('company.edit', compact('company'));
    }

    // /**
    //  * Update the specified resource in storage.
    //  */
    public function update(Request $request, Company $company)
    {
        //
        if ($company->user_id !== Auth::id()) {
            abort(403);
        }
        $validator = Validator::make($request->all(), [
            'name'          => 'required|string|max:255',
            'industry'      => 'nullable|string|max:255',
            'size'          => 'nullable|string|max:255',
            'location'      => 'nullable|string|max:255',
            'email'         => 'nullable|email|max:255',
            'phone'         => 'nullable|string|max:20',
            'description'   => 'nullable|string',
            'website'       => 'nullable|url',
            'linkedin'      => 'nullable|url',
            'twitter'       => 'nullable|url',
            'facebook'      => 'nullable|url',
            'certifications'=> 'nullable|string',
        ]);
         if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $company->update($request->only([
            'name', 'industry', 'size', 'location', 'email', 'phone',
            'description', 'website', 'linkedin', 'twitter', 'facebook', 'certifications'
        ]));

        // $company->update($request->all());
        return redirect()->back()->with('success', 'Company details has been updated successfully.');
    }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    public function destroy(Company $company)
    {
        //
         if ($company->user_id !== Auth::id()) {
            abort(403);
        }

        $company->delete();

        return redirect()->route('company.index')->with('success', 'Company deleted successfully.');
        // $company->delete();
        // return redirect()->route('company.index')->with('success', 'Company deleted.');
    }
    public function editDescription()
{
    $company = Company::where('user_id', auth()->id())->firstOrFail();
    return view('company.description', compact('company'));
}
public function updateDescription(Request $request)
{
    $request->validate([
        'description' => 'nullable|string',
    ]);

    $company = Company::where('user_id', auth()->id())->firstOrFail();
    $company->description = $request->description;
    $company->save();

    return redirect()->route('company.description')->with('success', 'Description updated successfully.');
}
public function websites()
{
    $company = Company::where('user_id', auth()->id())->first();

    if (!$company) {
        return redirect()->route('company.create')->with('error', 'Please create your company first.');
    }

    return view('company.websites', compact('company'));
}

public function updateWebsites(Request $request)
{
    $company = Company::where('user_id', auth()->id())->firstOrFail();

    $request->validate([
        'website' => 'nullable|url|max:255',
        'linkedin' => 'nullable|url|max:255',
        'twitter' => 'nullable|url|max:255',
        'facebook' => 'nullable|url|max:255',
    ]);

    $company->update($request->only(['website', 'linkedin', 'twitter', 'facebook']));

    return redirect()->route('company.websites')->with('success', 'Website links updated successfully.');
}
public function certifications()
{
    $company = Company::where('user_id', auth()->id())->first();

    if (!$company) {
        return redirect()->route('company.create')->with('error', 'Please create your company first.');
    }

    return view('company.certifications', compact('company'));
}

public function updateCertifications(Request $request)
{
    $company = Company::where('user_id', auth()->id())->firstOrFail();

    $request->validate([
        'certifications' => 'nullable|string|max:1000'
    ]);

    $company->update([
        'certifications' => $request->certifications,
    ]);

    return redirect()->route('company.certifications')->with('success', 'Certifications updated successfully.');
}


}
