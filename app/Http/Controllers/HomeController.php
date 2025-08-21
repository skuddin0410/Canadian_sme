<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Event;

use Validator;
use Illuminate\Support\Facades\Hash;
use Auth;
use App\Models\Payment;
use App\Models\AuditLog;
use App\Models\UserLogin;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {  
        if ( Auth::user()->hasRole('Admin') 
            || Auth::user()->hasRole('Event Admin') 
            || Auth::user()->hasRole('Admin') 
            || Auth::user()->hasRole('Representative')
            || Auth::user()->hasRole('Attendee')
            || Auth::user()->hasRole('Speaker')
            || Auth::user()->hasRole('Support Staff Or Helpdesk')
            || Auth::user()->hasRole('Registration Desk')) {
            
            $evntCount = Event::count();
            $userCount = User::with("roles")
                ->whereHas("roles", function ($q) {
                    $q->whereNotIn("name", ["Admin"]);
                })->count();
            
            if(Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Event Admin')){
                $logs = AuditLog::with('user')->orderBy('created_at', 'desc')->limit(5)->get(); 
                $loginlogs = UserLogin::with('user')->orderBy('created_at', 'desc')->limit(5)->get();   
            }else{
                $logs = AuditLog::with('user')->where('audit_logs.user_id',auth()->id())->orderBy('created_at', 'desc')->limit(5)->get(); 
                $loginlogs = UserLogin::with('user')->where('user_logins.user_id',auth()->id())->orderBy('created_at', 'desc')->limit(5)->get(); 
            } 


            return view('home',compact('evntCount','userCount','logs','loginlogs'));
        }

        if (Auth::user()->hasRole('Affiliate Manager')) {
            return redirect(route('affiliate.index'));
        }
    }

    public function accountInfo(Request $request)
    {
        return view('account_settings.account_information');
    }

    public function accountInformation(Request $request) 
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'contact_number' => 'required',
            ]);

             if($validator->fails()) {
              return redirect()->route('change.account.information')->withError($validator->errors()->first());
            }


            $user = auth()->user();
            User::where('id',$user->id)->update(['name' =>$request->name,'mobile' =>$request->contact_number]);
            return redirect()->route('change.account.information')
            ->withSuccess('Your account information changed successfully.');
        }catch(\Exception $e) {
            return redirect()->route('change.account.information')
            ->withError($e->getMessage());
        }
    }

     public function changeAccountPassword() 
    {
        return view('account_settings.change_password');
    }

     public function changePassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'old_password' => 'required',
                'new_password' => 'min:8|same:confirm_password',
            ]);
            
            if($validator->fails()) {
              return redirect()->route('admin.change.password')->withError($validator->errors()->first());
            }

            $user = auth()->user();

            if (Hash::check($request->old_password, $user->password)) {
                $user->password = Hash::make($request->new_password);
                $user->save();
                return redirect()->route('admin.change.password')->withSuccess('Your password changed successfully.');
            }else{
                return redirect()->route('admin.change.password')->withError('Old Password does not match with our database');
            }


            
        }catch(\Exception $e) {
            dd($e);
            return redirect()->route('admin.change.password')->withError('Sorry some problem occoured, please try again.');
        }
    }
}
