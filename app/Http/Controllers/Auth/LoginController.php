<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Models\User;

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

  public function showLoginForm()
  {
    $isAdmin = request()->is('admin/login') || request()->is('admin');
    return view('auth.login', compact('isAdmin'));
  }

  protected function attemptLogin(Request $request)
  {
    $credentials = $this->credentials($request);
    $user = User::where('email', $credentials['email'])
      ->orWhere('username', $credentials['email'])
      ->first();

    if ($user) {
      $isAdminRoute = $request->is('admin/login') || $request->is('admin');
      
      // Define who counts as an Admin/Staff member
      $hasAdminRole = $user->hasAnyRole(['Admin', 'Super Admin', 'Exhibitor', 'Representative', 'Speaker', 'Support Staff Or Helpdesk', 'Registration Desk']);
      $isAttendee = $user->hasRole('Attendee');

      // 1. Admin Portal: Must have an Admin-level role.
      if ($isAdminRoute && !$hasAdminRole) {
        return false;
      }

      // 2. Attendee Portal: Block if they have an Admin-level role (even if they also have Attendee)
      // If they only have Attendee role, allow.
      if (!$isAdminRoute && $hasAdminRole) {
        return false;
      }

      // 3. Attendee Portal: If NOT an admin, must at least have Attendee role.
      if (!$isAdminRoute && !$isAttendee) {
        return false;
      }
    }

    return $this->guard()->attempt(
      $credentials, $request->filled('remember')
    );
  }

  protected function redirectTo()
  {
    $user = auth()->user();

    // Prioritize Admin dashboard for anyone with Admin-level roles
    if ($user->hasAnyRole(['Admin', 'Super Admin', 'Exhibitor', 'Representative', 'Speaker', 'Support Staff Or Helpdesk', 'Registration Desk'])) {
      return '/admin/home';
    }

    if ($user->hasRole('Attendee')) {
      $eventId = session('event_id');
      if ($eventId) {
        $event = \App\Models\Event::find($eventId);
        if ($event) {
          return route('user.front.events', $event->slug);
        }
      }
      return '/user/home';
    }

    return '/admin/home';
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
