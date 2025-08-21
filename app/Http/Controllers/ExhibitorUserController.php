<?php

namespace App\Http\Controllers;

use DB;
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
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class ExhibitorUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    $perPage = (int) $request->input('perPage', 20);
    $pageNo = (int) $request->input('page', 1);
    $offset = $perPage * ($pageNo - 1);

    // Start the query
    $users = User::with("roles")
        ->whereHas("roles", function ($q) {
            $q->whereIn("name", ['Exhibitor Admin']);
        })
        ->orderBy('created_at', 'DESC');

    // Apply search filter if present
    if ($request->filled('search')) {
        $search = '%' . $request->search . '%';
        $users = $users->where(function ($query) use ($search) {
            $query->where('users.name', 'LIKE', $search)
                  ->orWhere('users.username', 'LIKE', $search)
                  ->orWhere('users.mobile', 'LIKE', $search)
                  ->orWhere('users.email', 'LIKE', $search);
        });
    }

    // Filter by created_at date range if given
    if ($request->filled('start_at') && $request->filled('end_at')) {
        $users = $users->whereBetween('created_at', [$request->start_at, $request->end_at]);
    }

    // Clone for count
    $usersCount = clone $users;
    $totalRecords = $usersCount->count();

    // Pagination
    $users = $users->offset($offset)->limit($perPage)->get();

    // Convert to LengthAwarePaginator for proper pagination links
    $users = new \Illuminate\Pagination\LengthAwarePaginator($users, $totalRecords, $perPage, $pageNo, [
        'path'  => $request->url(),
        'query' => $request->query(),
    ]);

    $data['offset'] = $offset;
    $data['pageNo'] = $pageNo;
    $users->setPath(route('exhibitor-users.index'));
    
    // Return AJAX response with HTML partial
    if ($request->ajax() && $request->ajax_request == true) {
        $data['html'] = view('users.exhibitor_users.table', compact('users', 'perPage'))
            ->with('i', $offset)
            ->render();

        return response()->json($data);
    }

    // Normal page load
    return view('users.exhibitor_users.index', ["kyc" => ""]);
}

    // public function index(Request $request)
    // {
    //     //
    //     $perPage = (int) $request->input('perPage', 20);
    //     $pageNo = (int) $request->input('page', 1);
    //     $offset = $perPage * ($pageNo - 1);
    //     $search = $request->input('search', '');
    //     $kyc = $request->input('kyc', '');
        
    //     if ($request->ajax() && $request->ajax_request == true) {
    //         $users = User::with("roles")
    //             ->whereHas("roles", function ($q) {
    //                 $q->whereIn("name", ['Exhibitor Admin']);
    //             })->orderBy('created_at', 'DESC');

    //         if ($request->search) {
    //             $users = $users->where(function ($query) use ($request) {
    //                 $query->where('name', 'LIKE', '%' . $request->search . '%');
    //                 $query->orWhere('username', 'LIKE', '%' . $request->search . '%');
    //                 $query->orWhere('mobile', 'LIKE', '%' . $request->search . '%');
    //                 $query->orWhere('email', 'LIKE', '%' . $request->search . '%');
    //             });
    //         }

    //         if ($request->start_at && $request->end_at) {
    //             $users = $users->where(function ($query) use ($request) {
    //                 $query->whereDate('created_at', '>=', $request->start_at);
    //                 $query->whereDate('created_at', '<=', $request->end_at);
                    
    //             });
    //         }

           

    //         $usersCount = clone $users;
    //         $totalRecords = $usersCount->count(DB::raw('DISTINCT(users.id)'));
    //         $users = $users->offset($offset)->limit($perPage)->get();
    //         $users = new LengthAwarePaginator($users, $totalRecords, $perPage, $pageNo, [
    //             'path'  => $request->url(),
    //             'query' => $request->query(),
    //         ]);
    //         $data['offset'] = $offset;
    //         $data['pageNo'] = $pageNo;
    //         $users->setPath(route('users.index'));
    //         $data['html'] = view('users.exhibitor_users.table', compact('users', 'perPage'))
    //             ->with('i', $pageNo * $perPage)
    //             ->render();

    //         return response($data);
    //     }
    //      $users = User::role('Exhibitor Representative')
    //         ->where('created_by', auth()->id())
    //         ->get();

    //     return view('users.exhibitor_users.index', ["kyc" => ""]);
    // }

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
        //
         $validator = Validator::make($request->all(), [
          
            'username' => 'required|string|unique:users,username',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|max:255|email|unique:users,email',
            'mobile' => 'required|string|digits:10|unique:users,mobile',
            'dob' => 'required|date|max:255',
            'gender' => 'nullable|string|max:255',
            'street' => 'nullable|string|max:255',
            'zipcode' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:8',
            'company_name'          => 'required|string|max:255',
            'company_email'         => 'required|email|max:255',
            'company_phone'         => 'required|string|max:20',
            'company_description'   => 'nullable|string'


        ]);
        if ($validator->fails()) {
            return redirect(route('exhibitor-users.create'))->withInput()
                ->withErrors($validator);
        }

          DB::transaction(function () use ($request) {
        $user = new User();
        $user->username = $request->username;
        $user->name = $request->first_name;
        $user->lastname = $request->last_name;
        $user->email = $request->email;
        $user->mobile = $request->mobile;
        $user->dob = $request->dob;
        $user->gender = $request->gender;
        $user->street = $request->street;
        $user->zipcode = $request->zipcode;
        $user->city = $request->city;
        $user->state = $request->state;
        $user->country = $request->country;
        $user->place = $request->place;
        $user->password = Hash::make($request->password);
        $user->save();
        $user->assignRole($request->user_type);

         Company::create([
            'user_id'      => $user->id,
            'name'         => $request->company_name,
            'email'        => $request->company_email,
            'phone'        => $request->company_phone,
            'description'  => $request->company_description,
        ]);
        });



      
        return redirect(route('exhibitor-users.index'))
            ->withSuccess('Exhibitor User data has been saved successfully');

    }

    /**
     * Display the specified resource.
     */
    // public function show(User $exhibitor_user, Request $request)
    // {
       
    //     $exhibitor_user->load('booths');
    //     $booths = Booth::all(); 
    //     $companies = Company::with('certificationFile','logoFile', 'mediaGallery', 'videos')
    //     ->where('user_id',$exhibitor_user->id)
    //     ->get();

                              
    //     return view('users.exhibitor_users.view', ['user' => $exhibitor_user , 'booths' => $exhibitor_user->booths,'companies' => $companies]);
    // }
//     public function show(User $exhibitor_user, Request $request)
// {
//     // Don’t try to load booths via relation
//     $companies = Company::with('certificationFile','logoFile','mediaGallery','videos')
//         ->where('user_id', $exhibitor_user->id)
//         ->get();

//     // Get booths by user directly
//     $booths = Booth::where('user_id', $exhibitor_user->id)->get();

//     return view('users.exhibitor_users.view', [
//         'user' => $exhibitor_user,
//         'booths' => $booths,
//         'companies' => $companies
//     ]);
// }
public function show(User $exhibitor_user, Request $request)
{
    // Load companies with related files
    $companies = Company::with(['certificationFile', 'logoFile', 'mediaGallery', 'videos'])
        ->where('user_id', $exhibitor_user->id)
        ->get();

    // Eager load booths via the hasManyThrough relationship
    $exhibitor_user->load('booths');

    return view('users.exhibitor_users.view', [
        'user'      => $exhibitor_user,
        'booths'    => $exhibitor_user->booths,  // now works via hasManyThrough
        'companies' => $companies,
    ]);
}



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $exhibitor_user)
    {
        //
         return view('users.exhibitor_users.edit', ['exhibitor_user' => $exhibitor_user]);
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
        'dob'                => 'required|date',
        'gender'             => 'nullable|string|max:255',
        'street'             => 'nullable|string|max:255',
        'zipcode'            => 'nullable|string|max:255',
        'city'               => 'nullable|string|max:255',
        'state'              => 'nullable|string|max:255',
        'country'            => 'nullable|string|max:255',
        'password'           => 'nullable|string|min:8',

        'company_name'       => 'required|string|max:255',
        'company_email'      => 'required|email|max:255',
        'company_phone'      => 'required|string|max:20',
        'company_description'=> 'nullable|string'
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withInput()->withErrors($validator);
    }

    DB::transaction(function () use ($request, $exhibitor_user) {

        // Update Exhibitor User details
        $exhibitor_user->username = $request->username;
        $exhibitor_user->name = $request->first_name;
        $exhibitor_user->lastname = $request->last_name;
        $exhibitor_user->email = $request->email;
        $exhibitor_user->mobile = $request->mobile;
        $exhibitor_user->dob = $request->dob;
        $exhibitor_user->gender = $request->gender;
        $exhibitor_user->street = $request->street;
        $exhibitor_user->zipcode = $request->zipcode;
        $exhibitor_user->city = $request->city;
        $exhibitor_user->state = $request->state;
        $exhibitor_user->country = $request->country;
        $exhibitor_user->place = $request->place;

        if ($request->filled('password')) {
            $exhibitor_user->password = Hash::make($request->password);
        }

        $exhibitor_user->save();

        // Update or Create Company
        $exhibitor_user->company()->updateOrCreate(
            ['user_id' => $exhibitor_user->id],
            [
                'name'        => $request->company_name,
                'email'       => $request->company_email,
                'phone'       => $request->company_phone,
                'description' => $request->company_description,
            ]
        );

        // Sync Roles
        $exhibitor_user->syncRoles([]);
        $exhibitor_user->assignRole($request->user_type);
    });

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



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function assignBoothForm($id)
{
    $user = User::with('company')->findOrFail($id);
    $booths = Booth::all(); // Or filter by availability

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

    // Admin or Event Admin can block
    if ($currentUser->hasRole(['Admin', 'Event Admin'])) {
        // $user->is_block = true;
        // $user->save();
        // return back()->withSuccess('User has been blocked successfully.');
        $allowedRoles = ['Exhibitor Admin', 'Exhibitor Representative', 'Attendee', 'Speaker'];

        if ($user->hasAnyRole($allowedRoles)) {
            $user->is_block = true;
            $user->save();
            return back()->withSuccess('User has been blocked successfully.');
        } else {
            return back()->withErrors('You are not allowed to block this type of user.');
        }
    

    }

    

    return back()->withErrors('You do not have permission to perform this action.');
}

}
