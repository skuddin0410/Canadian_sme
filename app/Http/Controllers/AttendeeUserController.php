<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use App\Models\Wallet;
use App\Models\Booth;
use Illuminate\Support\Facades\Hash;
use App\Models\Drive;
use Storage;
use App\Mail\KycMail;
use Illuminate\Support\Facades\Mail;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use DataTables;
use DB;
use App\Models\Order;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon;


class AttendeeUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $perPage = (int) $request->input('perPage', 20);
        $pageNo = (int) $request->input('page', 1);
        $offset = $perPage * ($pageNo - 1);
        $search = $request->input('search', '');
        $kyc = $request->input('kyc', '');
        if ($request->ajax() && $request->ajax_request == true) {
    $users = User::with("roles")
        ->whereHas("roles", function ($q) {
            $q->whereIn("name", ['Attendee']);
        })
        ->orderBy('created_at', 'DESC');

    // Search (triggered by search button)
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
            $data['html'] = view('users.attendee_users.table', compact('users', 'perPage'))
                ->with('i', $pageNo * $perPage)
                ->render();

            return response($data);
        }

        return view('users.attendee_users.index', ["kyc" => ""]);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('users.attendee_users.create');

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
        'designation' => 'nullable|string|max:255',
        'tags' => 'nullable|string|max:255',
        'website_url' => 'nullable|url|max:255',
        'linkedin_url' => 'nullable|url|max:255',
        'mobile' => 'required|string|digits:10|unique:users,mobile',
        'dob' => 'required|date|max:255',
        'gender' => 'nullable|string|max:255',
        'street' => 'nullable|string|max:255',
        'zipcode' => 'nullable|string|max:255',
        'city' => 'nullable|string|max:255',
        'state' => 'nullable|string|max:255',
        'country' => 'nullable|string|max:255',
        'place' => 'nullable|string|max:255',
        'password' => 'nullable|string|min:8',
       
    ]);

    if ($validator->fails()) {
        return redirect(route('attendee-users.create'))->withInput()
            ->withErrors($validator);
    }

    $user = new User();
    $user->username = $request->username;
    $user->name = $request->first_name;
    $user->lastname = $request->last_name;
    $user->email = $request->email;
    $user->designation = $request->designation;
    $user->tags = $request->tags;
    $user->website_url = $request->website_url;
    $user->linkedin_url = $request->linkedin_url;
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

   

    return redirect(route('attendee-users.index'))
        ->withSuccess('Attendee data has been saved successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $user = User::findOrFail($id); // ensures fresh data
    return view('users.attendee_users.view', compact('user'));


    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
          $user = User::findOrFail($id);
    return view('users.attendee_users.edit', compact('user'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
         $user = User::findOrFail($id);

    $validator = Validator::make($request->all(), [
        
        'username' => 'required|string|unique:users,username,' . $user->id,
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|string|max:255|email|unique:users,email,' . $user->id,
        'designation' => 'nullable|string|max:255' ,
        'tags' => 'nullable|string|max:255'  ,
        'website_url' => 'nullable|url|max:255',
        'linkedin_url' => 'nullable|url|max:255',
        'mobile' => 'required|string|digits:10|unique:users,mobile,' . $user->id,
        'dob' => 'required|date',
        'gender' => 'nullable|string|max:255',
        'street' => 'nullable|string|max:255',
        'zipcode' => 'nullable|string|max:255',
        'city' => 'nullable|string|max:255',
        'state' => 'nullable|string|max:255',
        'country' => 'nullable|string|max:255',
        'place' => 'nullable|string|max:255',
        'password' => 'nullable|string|min:8',
        'user_type' => 'required|string'
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withInput()->withErrors($validator);
    }

    $user->username = $request->username;
    $user->name = $request->first_name;
    $user->lastname = $request->last_name;
    $user->email = $request->email;
    $user->designation = $request->designation;
    $user->tags = $request->tags;
    $user->website_url = $request->website_url;
    $user->linkedin_url = $request->linkedin_url;
    $user->mobile = $request->mobile;
    $user->dob = $request->dob;
    $user->gender = $request->gender;
    $user->street = $request->street;
    $user->zipcode = $request->zipcode;
    $user->city = $request->city;
    $user->state = $request->state;
    $user->country = $request->country;
    $user->place = $request->place;

    if ($request->filled('password')) {
        $user->password = Hash::make($request->password);
    }

    $user->save();

    $user->syncRoles([]);
    $user->assignRole($request->user_type);

   

    return redirect(route('attendee-users.index'))->withSuccess('Attendee data has been updated successfully.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $user = User::findOrFail($id);

    // Detach roles to clean up pivot table (optional but good practice)
    $user->roles()->detach();

    // Delete user permanently
    $user->delete();

    return redirect()
        ->route('attendee-users.index')
        ->withSuccess('Attendee user deleted successfully.');

    }
       public function toggleBlock(User $user)
{
    $currentUser = auth()->user();

    // Admin or Event Admin can block
    if ($currentUser->hasRole(['Admin', 'Event Admin'])) {
        // $user->is_block = true;
        // $user->save();
        // return back()->withSuccess('User has been blocked successfully.');
        $allowedRoles = ['Admin', 'Representative', 'Attendee', 'Speaker'];

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
