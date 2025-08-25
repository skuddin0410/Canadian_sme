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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|max:255|email|unique:users,email',
            'mobile' => 'required|string|unique:users,mobile',
            'bio'    => 'required|string|max:500',

        ]);

    if ($validator->fails()) {
        return redirect(route('speaker.create'))->withInput()
            ->withErrors($validator);
    }

    $user = new User();
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
    $user->bio = $request->bio;
    $user->save();
    $user->assignRole('Speaker');


     if ($request->file("image")) {
         $this->imageUpload($request->file("image"), 'users', $user->id, 'users', 'photo');
     }
    

    return redirect(route('speaker.index'))
        ->withSuccess('Speaker data has been saved successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = User::findOrFail($id); // ensures fresh data
        return view('users.speaker.view', compact('user'));

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        
     $user = User::findOrFail($id);
    return view('users.speaker.edit', compact('user'));
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
        'mobile' => 'required|string|digits:10|unique:users,mobile,' . $user->id,
        'bio'    => 'required|string|max:500',

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
    $user->bio = $request->bio;
    $user->save();

    if ($request->file("image")) {
         $this->imageUpload($request->file("image"), 'users', $user->id, 'users', 'photo',$user->id);
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
      public function toggleBlock(User $user)
{
    $currentUser = auth()->user();

    // Admin or Admin can block
    if ($currentUser->hasRole(['Admin', 'Admin'])) {
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
