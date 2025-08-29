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
use App\Models\Order;
use App\Models\Wallet;

use App\Models\Company;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
use Illuminate\Http\Request;
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
            'company_phone'         => 'required|string|max:20',
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
  
    $user = User::create([
        'name'       => $request->company_name,
        'email'      => $request->company_email,
        'password'   => Hash::make('password'),
    ]);
    $user->assignRole('Sponsors');

    
    $company = Company::create([
        'user_id'     => $user->id,
        'name'        => $request->company_name,
        'email'       => $request->company_email,
        'is_sponsor' => true,
        'phone'       => $request->company_phone,
        'description' => $request->company_description,
        'website'     => $request->website,
        'linkedin'    => $request->linkedin,
        'twitter'     => $request->twitter,
        'facebook'    => $request->facebook,

    ]);

   
    $user->company_id = $company->id;
    $user->save();
       if ($request->file("logo")) {
        $this->imageUpload(
        $request->file("logo"),
        'logo',
        $company->id,
        'companies',
        'logo'
        );
    } 

    if ($request->file("banner")) {
    $this->imageUpload(
        $request->file("banner"),
        'banner',
        $company->id,
        'companies',
        'banner'
    );
    }
    qrCode($user->id);


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
        $company->load('logo' , 'banner');
        return view('users.sponsors.view', compact('company'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
    $user = User::findOrFail($id);
    return view('users.sponsors.edit', compact('user'));
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|max:255|email|unique:users,email,' . $user->id,
            'designation' => 'nullable|string|max:255' ,
            'tags' => 'nullable|string|max:255'  ,
            'website_url' => 'nullable|string|max:255',
            'linkedin_url' => 'nullable|string|max:255',
            'instagram_url' => 'nullable|string|max:255',
            'facebook_url' => 'nullable|string|max:255',
            'twitter_url' => 'nullable|string|max:255',
            'mobile' => 'required|string|digits:10|unique:users,mobile,' . $user->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'bio' => 'string|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $user->name = $request->first_name;
        $user->lastname = $request->last_name;
        $user->email = $request->email;
        $user->designation = $request->designation;
        $user->tags = $request->tags;
        $user->website_url = $request->website_url;
        $user->linkedin_url = $request->linkedin_url;
        $user->instagram_url = $request->linkedin_url;
        $user->facebook_url = $request->facebook_url;
        $user->twitter_url = $request->twitter_url;
        $user->mobile = $request->mobile;
        $user->bio=$request->bio;
        $user->save();
 
        
        if ($request->hasFile('image')) {
            if ($request->hasFile('image')) {
             $this->imageUpload($request->file("image"),"users",$user->id,'users','photo',$user->id);
            }
        }

   

    return redirect(route('sponsors.index'))->withSuccess('Sponsors data has been updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->roles()->detach();
        $user->delete();

        return redirect()
            ->route('sponsors.index')
            ->withSuccess('Sponsor user deleted successfully.');

    }
   
    
}
