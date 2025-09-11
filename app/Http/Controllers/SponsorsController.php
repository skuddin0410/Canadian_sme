<?php

namespace App\Http\Controllers;

use DB;
use Carbon;
use Storage;
use DataTables;
use App\Models\User;
use App\Mail\KycMail;
use App\Models\Booth;
use App\Models\Drive;
use App\Models\Company;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
use Illuminate\Http\Request;
use App\Exports\SponsorExport;
use Illuminate\Support\Collection;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;


class SponsorsController extends Controller
{
   
public function index(Request $request)
{
    $perPage = (int) $request->input('perPage', 20);
    $pageNo  = (int) $request->input('page', 1);

    
    $query = Company::with('user')
        ->where('is_sponsor', true)
        ->orderBy('created_at', 'DESC');

   
    if ($request->filled("search")) {
        $search = "%" . $request->search . "%";
        $query->where(function ($q) use ($search) {
            $q->where("companies.name", "LIKE", $search)
              ->orWhere("companies.email", "LIKE", $search)
              ->orWhere("companies.phone", "LIKE", $search)
              ->orWhere("companies.industry", "LIKE", $search);
        });
    }

    
    if ($request->filled("start_at") && $request->filled("end_at")) {
        $query->whereBetween("created_at", [$request->start_at, $request->end_at]);
    }
    
    $companies = $query->paginate($perPage, ["*"], "page", $pageNo);
    $companies->appends($request->all());
    $offset = ($companies->currentPage() - 1) * $perPage;
   
    if ($request->ajax() && $request->ajax_request == true) {
        $html = view("users.sponsors.table", compact("companies", "perPage"))
            ->with("i", $offset)
            ->render();

        return response()->json([
            "html"   => $html,
            "offset" => $offset,
            "pageNo" => $companies->currentPage(),
            "total"  => $companies->total(),
        ]);
    } 
    return view("users.sponsors.index", [
        "companies" => $companies,
        "perPage"   => $perPage,
        "offset"    => $offset,
    ]);
}


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
         return view('users.sponsors.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
         $validator = Validator::make($request->all(), [
            'company_name'          => 'required|string|max:255',
            'company_email'         => 'required|email|max:255',
            'company_phone'         => 'nullable|string|max:20',
            'company_description'   => 'nullable|string',
            'website'       => 'nullable|url',
            'linkedin'      => 'nullable|url',
            'twitter'       => 'nullable|url',
            'facebook'      => 'nullable|url',
            'logo'        => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
            'banner'     => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',


        ]);
        if ($validator->fails()) {
            return redirect(route('sponsors.create'))->withInput()
                ->withErrors($validator);
        }

       DB::beginTransaction();
       try {
        $company = Company::create([
            'name'        => $request->company_name,
            'email'       => $request->company_email,
            'is_sponsor' => true,
            'phone'       => $request->company_phone,
            'description' => $request->company_description,
            'website'     => $request->website,
            'linkedin'    => $request->linkedin,
            'twitter'     => $request->twitter,
            'facebook'    => $request->facebook,
            'instagram'    => $request->instagram,
            'type'    => $request->type,

        ]);
        if ($request->file("logo")) {
            $this->imageUpload(
                $request->file("logo"),
                'logo',
                $company->id,
                'companies',
                'logo',
                $company->id
            );
        } 

        if ($request->file("banner")) {
            $this->imageUpload(
                $request->file("banner"),
                'banner',
                $company->id,
                'companies',
                'banner',
                $company->id
            );
        }
    // qrCode($user->id, "user");


    DB::commit();

    return redirect(route('sponsors.index'))
        ->withSuccess('Sponsor Created');
    }   catch (\Exception $e) {
    DB::rollBack();
    return redirect(route('sponsors.create'))
        ->withInput()
        ->withErrors(['error' => 'Something went wrong: ' . $e->getMessage()]);
     }

    }


    
   
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {  
       
        $company = Company::findOrFail($id); // ensures fresh data
        $company->load('logo' , 'banner','user');
        $user= $company->user;
        return view('users.sponsors.view', compact('company','user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $company = Company::findOrFail($id); 
        $company->load('logo' , 'banner','user');
        $user= $company?->user;
        return view('users.sponsors.edit', compact('user','company'));
        
    }


    public function update(Request $request, $id){
    $validator = Validator::make($request->all(), [
        'company_name'        => 'required|string|max:255',
        'company_email'       => 'required|email|max:255',
        'company_phone'       => 'nullable|string|max:20',
        'company_description' => 'nullable|string',
        'website'             => 'nullable|url',
        'linkedin'            => 'nullable|url',
        'twitter'             => 'nullable|url',
        'facebook'            => 'nullable|url',
        'logo'                => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
        'banner'              => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
        'sponsor_id'=>'required'
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withInput()->withErrors($validator);
    }

    DB::beginTransaction();
    try {
       
        $company = Company::where('id',$request->sponsor_id)->first();
    
        if ($company) {
        $company->update([
            'name'        => $request->company_name,
            'email'       => $request->company_email,
            'phone'       => $request->company_phone,
            'description' => $request->company_description,
            'website'     => $request->website,
            'linkedin'    => $request->linkedin,
            'twitter'     => $request->twitter,
            'facebook'    => $request->facebook,
            'instagram'    => $request->instagram,
            'type'    => $request->type
        ]);

        }
      
        if ($request->file("logo")) {
            $this->imageUpload(
                $request->file("logo"),
                'logo',
                $company->id,
                'companies',
                'logo',
                $company->id
            );
        }

       
        if ($request->file("banner")) {
            $this->imageUpload(
                $request->file("banner"),
                'banner',
                $company->id,
                'companies',
                'banner',
                $company->id
            );
        }

        
        DB::commit();

        return redirect(route('sponsors.index'))
            ->withSuccess('Sponsor Updated');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()
            ->withInput()
            ->withErrors(['error' => 'Something went wrong: ' . $e->getMessage()]);
    }
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id){

        $company = Company::findOrFail($id);
        $company->delete();
        return redirect()
            ->route('sponsors.index')
            ->withSuccess('Sponsor user deleted successfully.');

    }


    public function downloadQr($userid){
        return downloadQrCode($userid);
    }
     public function exportSponsors()
{
    return Excel::download(new SponsorExport, 'sponsors.xlsx');
}
   
    
}
