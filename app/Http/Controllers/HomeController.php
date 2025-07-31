<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Giveaway;
use App\Models\Quiz;
use App\Models\Wallet;
use App\Models\Order;
use App\Models\Transaction;

use Validator;
use Illuminate\Support\Facades\Hash;
use Auth;
use App\Models\Payment;

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
            || Auth::user()->hasRole('Exhibitor Admin') 
            || Auth::user()->hasRole('Exhibitor Representative')
            || Auth::user()->hasRole('Attendee')
            || Auth::user()->hasRole('Speaker')
            || Auth::user()->hasRole('Support Staff Or Helpdesk')
            || Auth::user()->hasRole('Registration Desk')) {
            
            $usersCount = null;
            $giveawayCount = null;
            $quizCount = null;
            $order =null; 
            $summary = null; 
            $giveawayOrderCount = null; 
            $quizOrderCount = null; 
            $spinnerOrderCount = null; 
            
            $orderAmountGiveaway = null; 
            $orderAmountQuiz = null; 
            
            $usersStateWiseCounts =null;

            return view('home',compact('usersCount','giveawayCount','quizCount','order','summary','giveawayOrderCount','quizOrderCount','spinnerOrderCount','usersStateWiseCounts','orderAmountGiveaway','orderAmountQuiz'));
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
