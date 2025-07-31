<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Wallet;
use Auth;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {   

        $userCount = Wallet::where('table_id', Auth::user()->id)
            ->where('table_type', 'users')
            ->distinct('user_id')
            ->count();

        $latestEarnings = Wallet::with(['user'])
            ->where('table_id', Auth::user()->id)
            ->where('table_type', 'users')
            ->where('status', 'success')
            ->orderBy('created_at','DESC')
            ->limit(10)
            ->get();    
    
        $referrals = Wallet::where('table_id', Auth::user()->id)
            ->where('table_type', 'users')
            ->where('status', 'success')
            ->sum('amount');
        return view('affiliate.index',compact('referrals','userCount','latestEarnings'));
    }

    public function usersListWithEarning(Request $request)
    {   

        $latestEarnings = Wallet::with(['user'])
           ->whereHas("user", function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->search . '%');
                $q->orWhere('username', 'LIKE', '%' . $request->search . '%');
                $q->orWhere('mobile', 'LIKE', '%' . $request->search . '%');
                $q->orWhere('email', 'LIKE', '%' . $request->search . '%');
            })->where('table_id', Auth::user()->id)
            ->where('table_type', 'users')
            ->where('status', 'success')
            ->orderBy('created_at','DESC')
            ->get();    
    
        return view('affiliate.users',compact('latestEarnings'));
    }
}
