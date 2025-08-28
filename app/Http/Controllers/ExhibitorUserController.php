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

    
    $query = User::with("roles" , "company")
        ->whereHas("roles", function ($q) {
            $q->where("name", "Exhibitor");
        })
        ->orderBy("created_at", "DESC");

   
    if ($request->filled("search")) {
        $search = "%" . $request->search . "%";
        $query->where(function ($q) use ($search) {
            $q->where("users.name", "LIKE", $search)
              ->orWhere("users.username", "LIKE", $search)
              ->orWhere("users.mobile", "LIKE", $search)
              ->orWhere("users.email", "LIKE", $search);
        });
    }

   
    if ($request->filled("start_at") && $request->filled("end_at")) {
        $query->whereBetween("created_at", [$request->start_at, $request->end_at]);
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
        //
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
            'content_icon'        => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
            'quick_link_icon'     => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',


        ]);
        if ($validator->fails()) {
            return redirect(route('exhibitor-users.create'))->withInput()
                ->withErrors($validator);
        }

       DB::beginTransaction();
       try {
  
    $user = User::create([
        'name'       => $request->company_name,
        'email'      => $request->company_email,
        'password'   => Hash::make('password'),
    ]);
    $user->assignRole('Exhibitor');

    
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
    ]);

   
    $user->company_id = $company->id;
    $user->save();
       if ($request->file("content_icon")) {
        $this->imageUpload(
        $request->file("content_icon"),
        'content_icon',
        $company->id,
        'companies',
        'content_icon'
        );
    } 

    if ($request->file("quick_link_icon")) {
    $this->imageUpload(
        $request->file("quick_link_icon"),
        'quick_link_icon',
        $company->id,
        'companies',
        'quick_link_icon'
    );
    }


    DB::commit();

    return redirect(route('exhibitor-users.index'))
        ->withSuccess('Exhibitor Created');
    }   catch (\Exception $e) {
    DB::rollBack();
    return redirect(route('exhibitor-users.create'))
        ->withInput()
        ->withErrors(['error' => 'Something went wrong: ' . $e->getMessage()]);
     }

    }


    public function show(User $exhibitor_user, Request $request)
        {   
            
            $company = Company::with(['certificationFile', 'logoFile', 'mediaGallery', 'videos','booths'])
                ->where('user_id', $exhibitor_user->id)
                ->first();

            return view('users.exhibitor_users.view', [
                'user'      => $exhibitor_user,
                'company' => $company,
            ]);
        }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $exhibitor_user)
    {
        //
         return view('users.exhibitor_users.edit', ['user' => $exhibitor_user]);
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, User $exhibitor_user)
    {
        $validator = Validator::make($request->all(), [
            'username'           => 'required|string|unique:users,username,' . $exhibitor_user->id,
            'first_name'         => 'required|string|max:255',
            'last_name'          => 'required|string|max:255',
            'email'              => 'required|string|max:255|email|unique:users,email,' . $exhibitor_user->id,
            'mobile'             => 'required|string|digits:10|unique:users,mobile,' . $exhibitor_user->id,
            'company_name'       => 'required|string|max:255',
            'company_email'      => 'required|email|max:255',
            'company_phone'      => 'required|string|max:20',
            'company_description'=> 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }


            $exhibitor_user->username = $request->username;
            $exhibitor_user->name = $request->first_name;
            $exhibitor_user->lastname = $request->last_name;
            $exhibitor_user->email = $request->email;
            $exhibitor_user->mobile = $request->mobile;


            $exhibitor_user->save();
            $exhibitor_user->company()->updateOrCreate(
                ['user_id' => $exhibitor_user->id],
                [
                    'name'        => $request->company_name,
                    'email'       => $request->company_email,
                    'phone'       => $request->company_phone,
                    'description' => $request->company_description,
                ]
            );

        return redirect(route('exhibitor-users.index'))
            ->withSuccess('User and company data have been updated successfully.');
    }

    public function approve($id)
    {
        $user = User::findOrFail($id);
        $user->is_approve = true;
        $user->save();

        return redirect()->route('exhibitor-users.show', $id)->with('success', 'User has been approved successfully.');
    }


    public function assignBoothForm($id)
    {
        $user = User::with('company')->findOrFail($id);
        $booths = Booth::all(); 

        return view('users.exhibitor_users.show', compact('user', 'booths'));
    }

    public function assignBooth(Request $request, $id)
    {
        $user = User::with('company')->findOrFail($id);
        $boothId = $request->input('booth_id');

        $request->validate([
            'booth_id' => 'required|exists:booths,id',
        ]);

        $booth = Booth::findOrFail($boothId);

        // Assign booth to user's company
        if (!$user->company) {
          return redirect()->back()->with('error', 'User does not have an associated company.');
        }

        $booth->company_id = $user->company->id;
        $booth->save();

        return redirect()->route('exhibitor-users.show', $user->id)
            ->with('success', 'Booth assigned successfully.');
    }

    public function toggleBlock(User $user)
    {
        $currentUser = auth()->user();

        if ($currentUser->hasRole(['Admin'])) {
                $user->is_block = true;
                $user->save();
                return back()->withSuccess('User has been blocked successfully.');

        }

        return back()->withErrors('You do not have permission to perform this action.');
    }

}
