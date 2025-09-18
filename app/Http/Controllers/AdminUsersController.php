<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use DataTables;
use DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;


class AdminUsersController extends Controller
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
        if ($request->ajax() && $request->ajax_request == true) {
            $users = User::with("roles")
                ->whereHas("roles", function ($q) {
                    $q->whereIn("name", ["Admin"]);
                })->orderBy('created_at', 'DESC');

            if ($request->search) {
                $users = $users->where(function ($query) use ($request) {
                    $query->where('name', 'LIKE', '%' . $request->search . '%');
                    $query->orWhere('username', 'LIKE', '%' . $request->search . '%');
                    $query->orWhere('mobile', 'LIKE', '%' . $request->search . '%');
                    $query->orWhere('email', 'LIKE', '%' . $request->search . '%');
                    $query->orWhere('referral_coupon', 'LIKE', '%' . $request->search . '%');
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
            $users->setPath(route('admin-users.index'));
            $data['html'] = view('users.admin_users.table', compact('users', 'perPage'))
                ->with('i', $pageNo * $perPage)
                ->render();

            return response($data);
        }

        return view('users.admin_users.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.admin_users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {   
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|max:255|email|unique:users,email',
            'mobile' => 'required|string|unique:users,mobile',
            'bio'    => 'required|string|max:500',

        ]);
        if ($validator->fails()) {
            return redirect(route('admin-users.create'))->withInput()
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
        $user->assignRole('Admin');
        if ($request->hasFile('image')) {
          $this->imageUpload($request->file("image"),"users",$user->id,'users','photo');
        }

        return redirect(route('admin-users.index'))
            ->withSuccess('Admin users has been saved successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
       $user = User::where('id',$id)->first(); 
       $user->load('photo');
       return view('users.admin_users.view',compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {  
       $user = User::where('id',$id)->first(); 
       $user->load('photo');
       return view('users.admin_users.create',compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|max:255|email|unique:users,email,'. $id,
            'mobile' => 'required|string|unique:users,mobile,'. $id,
            'bio'    => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect(route('admin-users.edit', ["admin_user" => $id]))->withInput()
                ->withErrors($validator);
        }

        $user = User::where('id',$id)->first();
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
        $user->assignRole('Admin');
        if ($request->hasFile('image')) {
          $this->imageUpload($request->file("image"),"users",$user->id,'users','photo',$user->id);
        }

        return redirect(route('admin-users.index'))
            ->withSuccess('Admin user data has been saved successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
