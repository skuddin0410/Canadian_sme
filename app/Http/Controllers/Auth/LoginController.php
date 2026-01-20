<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
  /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

  use AuthenticatesUsers {
    logout as performLogout;
  }

  /**
   * Where to redirect users after login.
   *
   * @var string
   */
  // protected $redirectTo = '/admin/home';

  protected function redirectTo()
  {
    // dd(1);
      $user = auth()->user();
      // dd($user->roles->pluck('name'));

      if ($user->hasRole('Admin')) {
        // dd(1);
        // dd(auth()->user());
          return '/admin/home';
      }
        dd(2);

      return '/user/home';
  }


  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->middleware('guest')->except('logout');
    $this->middleware('auth')->only('logout');
  }

  protected function loggedOut(Request $request)
  {
    return redirect(route('login'));
  }
}
