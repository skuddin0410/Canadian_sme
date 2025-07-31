<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use DataTables;
use DB;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Mail\UserWelcome;
use Illuminate\Support\Facades\Mail;
class AffiliateController extends Controller
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
                    $q->whereIn("name", ["Affiliate Manager"]);
                })->orderBy('created_at', 'DESC');

            if ($request->search) {
                $users = $users->where(function ($query) use ($request) {
                    $query->where('name', 'LIKE', '%' . $request->search . '%');
                    $query->orWhere('username', 'LIKE', '%' . $request->search . '%');
                    $query->orWhere('mobile', 'LIKE', '%' . $request->search . '%');
                    $query->orWhere('email', 'LIKE', '%' . $request->search . '%');
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
            $users->setPath(route('affiliates.index'));
            $data['html'] = view('users.affiliate.table', compact('users', 'perPage'))
                ->with('i', $pageNo * $perPage)
                ->render();

            return response($data);
        }

        return view('users.affiliate.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.affiliate.create');
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
            'username' => 'required|string|alpha_num|unique:users,username',
            'referral_coupon' => 'required|string|alpha_num|unique:users,referral_coupon',
            'referral_percentage'=>'required|numeric|min:1|max:100',
            'password' => 'required|string|min:8',
            'confirm_password' => 'min:8|same:password',

        ]);
        if ($validator->fails()) {
            return redirect(route('affiliates.create'))->withInput()
                ->withErrors($validator);
        }

        $user = new User();
        $user->username = $request->username;
        $user->name = $request->first_name;
        $user->lastname = $request->last_name;
        $user->email = $request->email;
        $user->mobile = $request->mobile;
        $user->referral_coupon = $request->referral_coupon;
        $user->referral_percentage = $request->referral_percentage;
        $user->password = Hash::make($request->password);
        $user->save();
        $user->assignRole('Affiliate Manager');
        $user->user_password = $request->password;
        Mail::to($user->email)->send(new UserWelcome($user));
        return redirect(route('affiliates.index'))
            ->withSuccess('Affiliate Manager has been saved successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {  
       $user = User::where('id',$id)->first(); 
       return view('users.affiliate.view',compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
       $user = User::where('id',$id)->first(); 
       return view('users.affiliate.create',compact('user'));
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
            'username' => 'required|string|alpha_num|unique:users,username,'. $id,
            'referral_coupon' => 'required|string|alpha_num|unique:users,referral_coupon,'.$id,
            'referral_percentage'=>'required|numeric|min:1|max:100'

        ]);

        if ($validator->fails()) {
            return redirect(route('affiliates.edit', ["affiliate" => $id]))->withInput()
                ->withErrors($validator);
        }

        $user = User::where('id',$id)->first();
        $user->username = $request->username;
        $user->name = $request->first_name;
        $user->lastname = $request->last_name;
        $user->email = $request->email;
        $user->mobile = $request->mobile;
        $user->referral_coupon = $request->referral_coupon;
        $user->referral_percentage = $request->referral_percentage;
        $user->password = Hash::make($request->password);
        $user->save();
        $user->assignRole('Affiliate Manager');

        return redirect(route('affiliates.index'))
            ->withSuccess('Affiliate Manager data has been saved successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
