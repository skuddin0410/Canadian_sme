<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use App\Models\Wallet;
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

class SpeakerController extends Controller
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
                    $q->whereIn("name", ['Speaker']);
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
            $users->setPath(route('speaker.index'));
            $data['html'] = view('users.speaker.table', compact('users', 'perPage'))
                ->with('i', $pageNo * $perPage)
                ->render();

            return response($data);
              }

        return view('users.speaker.index', ["kyc" => ""]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
           return view('users.speaker.create');
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
        return redirect(route('speaker.create'))->withInput()
            ->withErrors($validator);
    }

    $user = new User();
    $user->username = $request->username;
    $user->name = $request->first_name;
    $user->lastname = $request->last_name;
    $user->email = $request->email;
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

    if ($request->file("frontimage")) {
        $this->imageUpload($request->file("frontimage"), 'users', $user->id, 'users', 'photo');
    }
    if ($request->file("image")) {
        $this->imageUpload($request->file("image"), 'users', $user->id, 'users', 'background');
    }

    return redirect(route('speaker.index'))
        ->withSuccess('Speaker data has been saved successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        //  return view('users.speaker.view', ['user' => $user]);
        $user = User::findOrFail($id); // ensures fresh data
    return view('users.speaker.view', compact('user'));

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
     $user = User::findOrFail($id);
    return view('users.speaker.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $user = User::findOrFail($id);

    $validator = Validator::make($request->all(), [
        'image' => 'nullable|file|mimetypes:' . config('app.image_mime_types') . '|max:' . config('app.adhaar_image_size'),
        'frontimage' => 'nullable|file|mimetypes:' . config('app.image_mime_types') . '|max:' . config('app.adhaar_image_size'),
        'username' => 'required|string|unique:users,username,' . $user->id,
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|string|max:255|email|unique:users,email,' . $user->id,
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

    if ($request->file("frontimage")) {
        $this->imageUpload($request->file("frontimage"), 'users', $user->id, 'users', 'photo', $user->id);
    }

    if ($request->file("image")) {
        $this->imageUpload($request->file("image"), 'users', $user->id, 'users', 'background', $user->id);
    }

    return redirect(route('speaker.index'))->withSuccess('Speaker data has been updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
