<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\Drive;
use Storage;
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

use App\Models\Email;
use App\Mail\TrackedEmail;

class UserController extends Controller
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
            $users = User::with("roles")
                ->whereHas("roles", function ($q) {
                    $q->whereIn("name", ["Admin",'Admin','Representative','Attendee','Speaker','Support Staff Or Helpdesk','Registration Desk']);
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
            $data['html'] = view('users.table', compact('users', 'perPage'))
                ->with('i', $pageNo * $perPage)
                ->render();

            return response($data);
        }

        return view('users.index', ["kyc" => ""]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
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
            // 'password' => 'required|string|min:8',
            

        ]);
        if ($validator->fails()) {
            return redirect(route('users.create'))->withInput()
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
        // $user->password = Hash::make($request->password);
        $user->save();
        $user->assignRole($request->user_type);

     
        return redirect(route('users.index'))
            ->withSuccess('User data has been saved successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user, Request $request)
    {

        if ($request->input('page', '') == 'kyc') {
            return view('users.kyc.view', ['user' => $user]);
        }
        if ($request->input('page', '') == 'pending') {
            return view('users.pending.view', ['user' => $user]);
        };
        $user->load('loginLogs');
                              
        return view('users.view', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('users.create', ['user' => $user]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'nullable|file|mimetypes:' . config('app.image_mime_types') . '|max:' . config('app.adhaar_image_size'),
            'frontimage' => 'nullable|file|mimetypes:' . config('app.image_mime_types') . '|max:' . config('app.adhaar_image_size'),
            'username' => 'required|string|unique:users,username,' . $user->id,
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|max:255|email|unique:users,email,' . $user->id,
            'mobile' => 'required|string|digits:10|unique:users,mobile,' . $user->id,
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
            return redirect(route('users.edit', ["user" => $user->id]))->withInput()
                ->withErrors($validator);
        }

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
        $user->syncRoles([]);
        $user->assignRole($request->user_type);

        if ($request->file("frontimage")) {
            $this->imageUpload($request->file("frontimage"), 'users', $user->id, 'users', 'photo', $idForUpdate = $user->id);
        }

        if ($request->file("image")) {
            $this->imageUpload($request->file("image"), 'users', $user->id, 'users', 'background', $idForUpdate = $user->id);
        }
        return redirect(route('users.index'))
            ->withSuccess('User data has been saved successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect(route('users.index'))
            ->withSuccess('User deleted successfully');
    }

    public function export() 
    {
        return Excel::download(new UsersExport, 'users_'.Carbon\Carbon::now()->timestamp.'.xlsx');
    }

    public function importUser(Request $request){

      if ($request->hasFile('file')) {
        $file = $request->file('file');

        if (!$file->isValid()) {
         return back()->withErrors(['file' => 'Uploaded file is not valid']);
        }
        
        // Get original extension
        $extension = strtolower($file->getClientOriginalExtension());

        // Allowed extensions
        $allowedExtensions = ['csv', 'xls', 'xlsx'];

        if (!in_array($extension, $allowedExtensions)) {
            return back()->withErrors(['file' => 'Only CSV, XLS, or XLSX files are allowed.']);
        }

        Excel::import(new UsersImport, $request->file('file'));
        return back();

      } else {
       return back()->withErrors(['file' => 'No file uploaded.']);
      }
    }
    public function representativeIndex()
{
    $users = User::role('Representative')
        ->where('created_by', auth()->id())
        ->get();

    return view('users.representative_users.index', compact('users'));
}

public function attendeeIndex()
{
    $users = User::role('attendee')
        ->where('created_by', auth()->id())
        ->get();

    return view('users.attendee_users.index', compact('users'));
}


    public function sendTrackedEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect(route('users.index'))->withInput()
                ->withErrors($validator);
        }

        $user = User::where('id',$request->user_id)->first();

        $email = Email::create([
            'user_id' => $user->id,
            'subject' => $request->subject,
            'body' => $request->body,
            'email' => $user->email
        ]);
        $emailId = $user->email;
        //$emailId = 'subhabrata06.dapl@gmail.com';
        Mail::to($emailId)->send(new TrackedEmail($email));
        
        return redirect(route('users.index'))
            ->withSuccess('Email sent successfully');
    }
    // Admin blocks a user


// public function blockUser($id)
// {
//     $user = User::findOrFail($id);

//     // Prevent blocking yourself
//     if ($user->id === Auth::id()) {
//         return redirect()->back()->with('error', 'You cannot block your own account.');
//     }

//     $user->is_block = true;
//     $user->save();

//     return redirect()->back()->with('success', 'User has been blocked successfully.');
// }

// // Helpdesk (or Admin) unblocks a user
// public function unblockUser($id)
// {
//     $user = User::findOrFail($id);

//     // If Helpdesk role, restrict to specific roles
//     if (Auth::user()->hasRole('Support Staff Or Helpdesk')) {
//         if (! $user->hasAnyRole(['Admin', 'Representative', 'Attendee', 'Speaker'])) {
//             return redirect()->back()->with('error', 'You are not allowed to unblock this role.');
//         }
//     }

//     $user->is_block = false;
//     $user->save();

//     return redirect()->back()->with('success', 'User has been unblocked successfully.');
// }
}
