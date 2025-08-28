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
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->input('perPage', 20);
        $pageNo = (int) $request->input('page', 1);
        $offset = $perPage * ($pageNo - 1);
        $search = $request->input('search', '');
        $kyc = $request->input('kyc', '');
        if ($request->ajax() && $request->ajax_request == true) {
        $users = User::with("roles")->whereHas("roles", function ($q) {
            $q->whereIn("name", ['Sponsors']);
            })->orderBy('created_at', 'DESC');

 
            if ($request->filled('search')) {
                $users = $users->where(function ($query) use ($request) {
                    $query->where('name', 'LIKE', '%' . $request->search . '%')
                          ->orWhere('username', 'LIKE', '%' . $request->search . '%')
                        //   ->orWhere('mobile', 'LIKE', '%' . $request->search . '%')
                          ->orWhere('email', 'LIKE', '%' . $request->search . '%');
                });
            }

            // Filters (triggered by filter button, add your filter logic here)
            if ($request->filled('start_at') && $request->filled('end_at')) {
                $users = $users->whereBetween('created_at', [$request->start_at, $request->end_at]);
            }
            
            if ($request->has('exhibitor_id')) {
                $users = $users->where('created_by_exhibitor_id', $request->exhibitor_id);
            }
                     
         
            $usersCount = clone $users;
            $totalRecords = $usersCount->count(DB::raw('DISTINCT(users.id)'));
            $users = $users->offset($offset)->limit($perPage)->get();
            $users = new LengthAwarePaginator($users, $totalRecords, $perPage, $pageNo, [
                'path'  => $request->url(),
                'query' => $request->query(),
            ]);
            $data['offset'] = $offset;
            $data['pageNo'] = $pageNo;
            $users->setPath(route('users.index'));
            $data['html'] = view('users.sponsors.table', compact('users', 'perPage'))
                ->with('i', $pageNo * $perPage)
                ->render();

            return response($data);
        }

        return view('users.sponsors.index', ["kyc" => ""]);
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
    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //     'first_name' => 'required|string|max:255',
    //     'last_name' => 'required|string|max:255',
    //     'email' => 'required|string|max:255|email|unique:users,email',
    //     'designation' => 'nullable|string|max:255',
    //     'tags' => 'nullable|string|max:255',
    //     'website_url' => 'nullable|string|max:255',
    //     'linkedin_url' => 'nullable|string|max:255',
    //     'instagram_url' => 'nullable|string|max:255',
    //     'facebook_url' => 'nullable|string|max:255',
    //     'twitter_url' => 'nullable|string|max:255',
    //     'mobile' => 'required|string|digits:10|unique:users,mobile',
    //     'user_type' => 'required|string',
    //     'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
    //     'bio' => 'string|string|max:500',
    //     ]);

    //     if ($validator->fails()) {
    //         return redirect(route('sponsors.create'))->withInput()
    //             ->withErrors($validator);
    //     }

    //     $user = new User();
    //     $user->name = $request->first_name;
    //     $user->lastname = $request->last_name;
    //     $user->email = $request->email;
    //     $user->designation = $request->designation;
    //     $user->tags = $request->tags;
    //     $user->website_url = $request->website_url;
    //     $user->linkedin_url = $request->linkedin_url;
    //     $user->instagram_url = $request->linkedin_url;
    //     $user->facebook_url = $request->facebook_url;
    //     $user->twitter_url = $request->twitter_url;
    //     $user->mobile = $request->mobile;
    //     $user->bio=$request->bio;
    //     $user->save();
    //     $user->assignRole($request->user_type);
           
    //     if ($request->hasFile('image')) {
    //       $this->imageUpload($request->file("image"),"users",$user->id,'users','photo');
    //     }
    //     return redirect(route('sponsors.index'))
    //         ->withSuccess('Sponsors data has been saved successfully');

    // }
    public function store(Request $request){
         $validator = Validator::make($request->all(), [
        'name'     => 'required|string|max:255',
        'industry' => 'required|string|max:255',
        'size'     => 'required|string|max:255',
        'location' => 'required|string|max:255',
        'email'    => 'required|email|unique:users,email',
        'phone'    => 'required|string|max:20',
        'website'  => 'required|url',
        'logo'     => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    // Step 1: Create Company
    $company = new Company();
    $company->fill($request->only(['name', 'industry', 'size', 'location', 'email', 'phone', 'website']));
    $company->save();

    // Upload logo
    if ($request->hasFile('logo')) {
        $this->imageUpload($request->file('logo'), 'logo', $company->id, 'companies', 'logo');
    }

    // Step 2: Create User linked to this company
    $user = new User();
    $user->name       = $company->name;
    $user->email      = $company->email;
    $user->company_id = $company->id;
    $user->password   = bcrypt(Str::random(10)); // random password
    $user->save();

    $user->assignRole('Sponsor');

    return redirect()->route('sponsors.index')->with('success', 'Sponsor and Company created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id); // ensures fresh data
        $user->load('photo');
        return view('users.sponsors.view', compact('user'));
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
