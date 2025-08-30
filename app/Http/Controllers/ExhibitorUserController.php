<?php

namespace App\Http\Controllers;

// use DB;
use Carbon;
// use Storage;
use DataTables;
use App\Models\User;
use App\Mail\KycMail;
use App\Models\Booth;
use App\Models\Drive;
use App\Models\Order;
use App\Models\Wallet;

use App\Models\Company;
use App\Models\BoothUser;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;

class ExhibitorUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */

public function index(Request $request)
{
    $perPage = (int) $request->input('perPage', 20);
    $pageNo = (int) $request->input('page', 1);

    
    $query = Company::with(['contentIconFile','quickLinkIconFile','boothUsers.booth'])->where('is_sponsor',0)->orderBy("created_at", "DESC");

    if ($request->filled("search")) {
        $search = "%" . $request->search . "%";
        $query->where(function ($q) use ($search) {
            $q->where("companies.name", "LIKE", $search)
              ->orWhere("companies.phone", "LIKE", $search)
              ->orWhere("companies.email", "LIKE", $search);
        });
    }
   
    $users = $query->paginate($perPage, ["*"], "page", $pageNo);
    $users->appends($request->all());
    $offset = ($users->currentPage() - 1) * $perPage;
    if ($request->ajax() && $request->ajax_request == true) {
        $html = view("users.exhibitor_users.table", compact("users", "perPage"))
            ->with("i", $offset)
            ->render();

        return response()->json([
            "html"   => $html,
            "offset" => $offset,
            "pageNo" => $users->currentPage(),
            "total"  => $users->total(),
        ]);
    }

    
    return view("users.exhibitor_users.index", [
        "users"  => $users,
        "perPage" => $perPage,
        "offset" => $offset,
        "kyc"    => "",
    ]);
}



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

         return view('users.exhibitor_users.create');
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
            'website'       => 'required|url',
            'linkedin'      => 'nullable|url',
            'twitter'       => 'nullable|url',
            'facebook'      => 'nullable|url',
            'content_icon'    => 'nullable|mimes:jpg,jpeg,png,svg|max:2048',
            'quick_link_icon' => 'nullable|mimes:jpg,jpeg,png,svg|max:2048',


        ]);
        if ($validator->fails()) {
            return redirect(route('exhibitor-users.create'))->withInput()
                ->withErrors($validator);
        }

       try {
  
            $user = User::create([
                'name'       => $request->company_name,
                'email'      => $request->company_email
            ]);
            $user->assignRole('Exhibitor');
            qrCode($user->id);
            $company = Company::create([
                'user_id'     => $user->id,
                'name'        => $request->company_name,
                'email'       => $request->company_email,
                'phone'       => $request->company_phone,
                'description' => $request->company_description,
                'website'     => $request->website,
                'linkedin'    => $request->linkedin,
                'twitter'     => $request->twitter,
                'facebook'    => $request->facebook,
                'instagram'    => $request->instagram,
                
            ]);
            $user->company_id = $company->id;
            $user->save();

        if ($request->file("content_icon")) {
            $this->imageUpload(
            $request->file("content_icon"),
            'content_icon',
            $company->id,
            'companies',
            'content_icon',
            $company->id,
            );
        } 

        if ($request->file("quick_link_icon")) {
            $this->imageUpload(
                $request->file("quick_link_icon"),
                'quick_link_icon',
                $company->id,
                'companies',
                'quick_link_icon',
                $company->id,
            );
        }


        return redirect(route('exhibitor-users.index'))
            ->withSuccess('Exhibitor Created');
    }   catch (\Exception $e) {
        return redirect(route('exhibitor-users.create'))
        ->withInput()
        ->withErrors(['error' => 'Something went wrong: ' . $e->getMessage()]);
     }

    }



public function show(User $exhibitor_user, Request $request){ 

    $company = Company::with(['boothUsers.booth','contentIconFile','quickLinkIconFile','user'])
        ->where('id', $exhibitor_user->id)
        ->firstOrFail();

    $booths = Booth::all(); 

    return view('users.exhibitor_users.view', [
        'user' => $exhibitor_user,
        'company' => $company,
        'booths' => $booths
    ]);
}



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $exhibitor_user){    

       $user = Company::with(['contentIconFile','quickLinkIconFile'])->where('id', $exhibitor_user->id)->firstOrFail();
        return view('users.exhibitor_users.edit', ['user' => $user]);
    }

    public function update(Request $request, User $exhibitor_user){
        try {
            
           $company = Company::find($request->company_id);
            
            $validator = Validator::make($request->all(), [
            'company_name'        => 'required|string|max:255',
            'company_email'       => 'required|email|max:255|unique:companies,email,'.$company->id,
            'company_phone'       => 'required|string|max:20',
            'company_description' => 'nullable|string',
            'website'             => 'nullable|url',
            'linkedin'            => 'nullable|url',
            'twitter'             => 'nullable|url',
            'facebook'            => 'nullable|url',
            'content_icon'        => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
            'quick_link_icon'     => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
            'company_id'       => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }


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
                ]);

                $user = User::where('id',$company->user_id)->first(); 

                if ($user) {
                    $user->update([
                        'name'  => $request->company_name,
                        'email' => $request->company_email,
                    ]);
                    qrCode($user->id);
                }
            }
         
            if ($request->hasFile('content_icon')) {
                $this->imageUpload(
                    $request->file("content_icon"),
                    'content_icon',
                    $company->id,
                    'companies',
                    'content_icon',
                    $company->id,
                );
            }

          
            if ($request->hasFile('quick_link_icon')) {
                $this->imageUpload(
                    $request->file("quick_link_icon"),
                    'quick_link_icon',
                    $company->id,
                    'companies',
                    'quick_link_icon',
                    $company->id,
                );
            }


            return redirect(route('exhibitor-users.index'))
                ->withSuccess('Exhibitor updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Something went wrong: ' . $e->getMessage()]);
        }
    }



    public function approve($id){
        $user = User::findOrFail($id);
        $user->is_approve = true;
        $user->save();

        return redirect()->route('exhibitor-users.show', $id)->with('success', 'User has been approved successfully.');
    }


    public function assignBoothForm($companyId){
        $company = Company::findOrFail($companyId);
        $booths = Booth::all(); // Fetch all booths

        return view('users.exhibitor_users.show', compact('company', 'booths'));
    }



    public function assignBooth(Request $request, $companyId){
        $company = Company::findOrFail($companyId);
        $request->validate([
            'booth_id' => 'required|exists:booths,id',
        ]);
        $boothId = $request->input('booth_id');
        if (BoothUser::where('booth_id', $boothId)->exists()) {
            return redirect()->back()->withErrors('This booth is already assigned to another company.');
        }

        BoothUser::create([
            'company_id' => $company->id,
            'booth_id' => $boothId,
        ]);

        return redirect()->route('exhibitor-users.show', ['exhibitor_user' => $company->id])->with('success', 'Booth assigned successfully.');
    }



    public function toggleBlock(User $user){
        $currentUser = auth()->user();
        if ($currentUser->hasRole(['Admin'])) {
                $user->is_block = true;
                $user->save();
                return back()->withSuccess('User has been blocked successfully.');

        }
        return back()->withErrors('You do not have permission to perform this action.');
    }

}
