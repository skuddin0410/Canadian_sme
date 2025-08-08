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

class ExhibitorUserController extends Controller
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
                    $q->whereIn("name", ['Exhibitor Admin']);
                })->orderBy('created_at', 'DESC');

            if ($request->search) {
                $users = $users->where(function ($query) use ($request) {
                    $query->where('name', 'LIKE', '%' . $request->search . '%');
                    $query->orWhere('username', 'LIKE', '%' . $request->search . '%');
                    $query->orWhere('mobile', 'LIKE', '%' . $request->search . '%');
                    $query->orWhere('email', 'LIKE', '%' . $request->search . '%');
                });
            }

            if ($request->start_at && $request->end_at) {
                $users = $users->where(function ($query) use ($request) {
                    $query->whereDate('created_at', '>=', $request->start_at);
                    $query->whereDate('created_at', '<=', $request->end_at);
                    
                });
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
            $data['html'] = view('users.exhibitor_users.table', compact('users', 'perPage'))
                ->with('i', $pageNo * $perPage)
                ->render();

            return response($data);
        }

        return view('users.exhibitor_users.index', ["kyc" => ""]);
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
        //
         $validator = Validator::make($request->all(), [
            'image' => 'nullable|file|mimetypes:' . config('app.image_mime_types') . '|max:' . config('app.adhaar_image_size'),
            'frontimage' => 'nullable|file|mimetypes:' . config('app.image_mime_types') . '|max:' . config('app.adhaar_image_size'),
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
            'password' => 'nullable|string|min:8'

        ]);
        if ($validator->fails()) {
            return redirect(route('exhibitor-users.create'))->withInput()
                ->withErrors($validator);
        }

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

        if ($request->file("frontimage")) {
            $this->imageUpload($request->file("frontimage"), 'users', $user->id, 'users', 'photo');
        }
        if ($request->file("image")) {
            $this->imageUpload($request->file("image"), 'users', $user->id, 'users', 'background');
        }
        return redirect(route('exhibitor-users.index'))
            ->withSuccess('Exhibitor User data has been saved successfully');

    }

    /**
     * Display the specified resource.
     */
    public function show(User $exhibitor_user, Request $request)
    {
        //  $exhibitor_user = User::findOrFail($exhibitor_user->id);
        // dd($exhibitor_user->is_approve); // should return true or false
        $exhibitor_user->load('booths');
          $booths = Booth::all(); 

                              
        return view('users.exhibitor_users.view', ['user' => $exhibitor_user , 'booths' => $booths]);
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
        'image' => 'nullable|file|mimetypes:' . config('app.image_mime_types') . '|max:' . config('app.adhaar_image_size'),
        'frontimage' => 'nullable|file|mimetypes:' . config('app.image_mime_types') . '|max:' . config('app.adhaar_image_size'),
        'username' => 'required|string|unique:users,username,' . $exhibitor_user->id,
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|string|max:255|email|unique:users,email,' . $exhibitor_user->id,
        'mobile' => 'required|string|digits:10|unique:users,mobile,' . $exhibitor_user->id,
        'dob' => 'required|date',
        'gender' => 'nullable|string|max:255',
        'street' => 'nullable|string|max:255',
        'zipcode' => 'nullable|string|max:255',
        'city' => 'nullable|string|max:255',
        'state' => 'nullable|string|max:255',
        'country' => 'nullable|string|max:255',
        'password' => 'nullable|string|min:8'
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withInput()->withErrors($validator);
    }

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

    if ($request->password) {
        $exhibitor_user->password = Hash::make($request->password);
    }

    $exhibitor_user->save();

    $exhibitor_user->syncRoles([]);
    $exhibitor_user->assignRole($request->user_type);

    if ($request->file("frontimage")) {
        $this->imageUpload($request->file("frontimage"), 'users', $exhibitor_user->id, 'users', 'photo', $exhibitor_user->id);
    }

    if ($request->file("image")) {
        $this->imageUpload($request->file("image"), 'users', $exhibitor_user->id, 'users', 'background', $exhibitor_user->id);
    }

    return redirect(route('exhibitor-users.index'))
        ->withSuccess('User data has been updated successfully.');
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
}
