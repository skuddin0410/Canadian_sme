<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
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
use Maatwebsite\Excel\Facades\Excel;
use Carbon;

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
                    $q->whereIn("name", ["User"]);
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

            if ($kyc == 'verified') {
                $users = $users->where('kyc_verified', 1);
            }
            if ($kyc == 'pending') {
                $users = $users->where('kyc_verified', 0);
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
            'password' => 'required|string|min:8'

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
        $user->password = Hash::make($request->password);
        $user->save();
        $user->assignRole($request->user_type);

        if ($request->file("frontimage")) {
            $this->imageUpload($request->file("frontimage"), 'users', $user->id, 'users', 'photo');
        }
        if ($request->file("image")) {
            $this->imageUpload($request->file("image"), 'users', $user->id, 'users', 'background');
        }
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
        }

        $orders = Order::query()
            ->when(Order::where('table_type', 'giveaways')->exists(), function ($query) {
                $query->with(['giveaway', 'giveaway.photo']);
            })
            ->when(Order::where('table_type', 'quizzes')->exists(), function ($query) {
                $query->with(['quiz', 'quiz.photo']);
            })
            ->when(Order::where('table_type', 'spinners')->exists(), function ($query) {
                $query->with(['spinner']);
            })
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $giveawayCount = Order::where('table_type', 'giveaways')
                    ->where('user_id', $user->id)
                    ->count(); 
        $quizCount = Order::where('table_type', 'quizzes')
                    ->where('user_id', $user->id)
                    ->count(); 
        $spinnerCount = Order::where('table_type', 'spinners')
                    ->where('user_id', $user->id)
                    ->count();                              
        $totalCount = $giveawayCount + $quizCount + $spinnerCount;
        return view('users.view', compact('user','orders','giveawayCount','quizCount','spinnerCount','totalCount'));
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

    public function referralUsers(Request $request)
    {
        $referrals = Wallet::with(['user', 'user.photo'])
            ->where('table_id', $request->id)
            ->where('table_type', 'users')
            ->paginate(25);
        return view('refferal.index', ['users' => $referrals]);
    }

    public function kycUsers(Request $request)
    {
        $perPage = (int) $request->input('perPage', 20);
        $pageNo = (int) $request->input('page', 1);
        $offset = $perPage * ($pageNo - 1);
        $search = $request->input('search', '');
        $user_type = $request->input('user_type', '');
        if ($request->ajax() && $request->ajax_request == true) {
            $users = User::with("roles")->where('kyc_verified', 1)
                ->whereHas("roles", function ($q) use ($user_type) {
                    if (!$user_type) {
                        $q->whereIn("name", ["User", "Affiliate"]);
                    }

                    if ($user_type == 'User') {
                        $q->where("name", "User");
                    }

                    if ($user_type == 'Affiliate') {
                        $q->where("name", "Affiliate");
                    }
                })->whereHas('photo', function ($q) {
                    $q->where('table_type', 'users')
                        ->where('file_type', 'photo')
                        ->whereNotNull('file_name');
                })->whereHas('background', function ($q) {
                    $q->where('table_type', 'users')
                        ->where('file_type', 'background')
                        ->whereNotNull('file_name');
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
            $users->setPath(route('kyc-users'));
            $data['html'] = view('users.kyc.table', compact('users', 'perPage'))
                ->with('i', $pageNo * $perPage)
                ->render();

            return response($data);
        }

        return view('users.kyc.index', ["kyc" => "done"]);
    }

    public function kycRequiredUsers(Request $request)
    {
        $perPage = (int) $request->input('perPage', 20);
        $pageNo = (int) $request->input('page', 1);
        $offset = $perPage * ($pageNo - 1);
        $search = $request->input('search', '');
        $user_type = $request->input('user_type', '');
        if ($request->ajax() && $request->ajax_request == true) {
            $users = User::with("roles")->where('kyc_verified', 0)
                ->whereHas("roles", function ($q) use ($user_type) {
                    if (!$user_type) {
                        $q->whereIn("name", ["User", "Affiliate"]);
                    }

                    if ($user_type == 'User') {
                        $q->where("name", "User");
                    }

                    if ($user_type == 'Affiliate') {
                        $q->where("name", "Affiliate");
                    }
                })->whereHas('photo', function ($q) {
                    $q->where('table_type', 'users')
                        ->where('file_type', 'photo')
                        ->whereNotNull('file_name');
                })->whereHas('background', function ($q) {
                    $q->where('table_type', 'users')
                        ->where('file_type', 'background')
                        ->whereNotNull('file_name');
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
            $users->setPath(route('kyc-users'));
            $data['html'] = view('users.pending.table', compact('users', 'perPage'))
                ->with('i', $pageNo * $perPage)
                ->render();

            return response($data);
        }
        return view('users.pending.index', ["kyc" => "required"]);
    }

    public function approveRejectKyc(Request $request)
    {
        $user = User::with('background')->where("id", $request->id)->first();
        $user->kyc_verified = $request->status;
        $user->save();
        $user->reasons = $request->reasons ?? '';
        if ($request->status == 0) {
            $this->deleteFile($request->id, 'users');
            $this->deleteFile($request->id, 'users', 'background');
        }
        Mail::to($user->email)->send(new KycMail($user));
        if ($request->status == 0) {
            return redirect(route('users.index'))
                ->withSuccess('KYC has been rejected successfully');
        }
        return redirect(route('kyc-users', ["user" => $request->id]))
            ->withSuccess('KYC has been approved successfully');
    }

    public function export() 
    {
        return Excel::download(new UsersExport, 'users_'.Carbon\Carbon::now()->timestamp.'.xlsx');
    }
}
