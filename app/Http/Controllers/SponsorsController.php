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
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|string|max:255|email|unique:users,email',
        'designation' => 'nullable|string|max:255',
        'tags' => 'nullable|string|max:255',
        'website_url' => 'nullable|url|max:255',
        'linkedin_url' => 'nullable|url|max:255',
        'instagram_url' => 'nullable|url|max:255',
        'facebook_url' => 'nullable|url|max:255',
        'twitter_url' => 'nullable|url|max:255',
        'mobile' => 'required|string|digits:10|unique:users,mobile',
        'user_type' => 'required|string',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        if ($validator->fails()) {
            return redirect(route('sponsors.create'))->withInput()
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
        $user->save();
        $user->assignRole($request->user_type);
           
        if ($request->hasFile('image')) {
        $file = $request->file('image');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('public/users', $filename);

      
        $user->photo()->updateOrCreate(
            ['table_type' => 'users', 'file_type' => 'photo'], 
            [
                'table_id' => $user->id,
                'file_name' => $filename,
                'file_type' => 'photo',
            ]
        );
    }
        return redirect(route('sponsors.index'))
            ->withSuccess('Sponsors data has been saved successfully');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id); // ensures fresh data
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
            'website_url' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'facebook_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'mobile' => 'required|string|digits:10|unique:users,mobile,' . $user->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'user_type' => 'required|string'
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
        $user->save();
        $user->syncRoles([]);
        $user->assignRole($request->user_type);
         if ($request->hasFile('image')) {
        $file = $request->file('image');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('public/users', $filename);

        // Save or update in Drive table
        $user->photo()->updateOrCreate(
            ['table_type' => 'users', 'file_type' => 'photo'], // match existing
            [
                'table_id' => $user->id,
                'file_name' => $filename,
                'file_type' => 'photo',
            ]
        );
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
