<?php

namespace App\Http\Controllers\SupportStaff;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class PasswordResetController extends Controller
{
    //
     protected $allowedRoles = [
        'Exhibitor Admin',
        'Exhibitor Representative',
        'Attendee',
        'Speaker'
    ];

    public function showLinkRequestForm()
    {
        return view('users.supportstaff.passwords.email');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user || !$user->hasAnyRole($this->allowedRoles)) {
            return back()->withErrors([
                'email' => 'You are not authorized to reset the password for this account.'
            ]);
        }

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetForm($token)
    {
        return view('users.supportstaff.passwords.reset', ['token' => $token]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !$user->hasAnyRole($this->allowedRoles)) {
            return back()->withErrors([
                'email' => 'You are not authorized to reset the password for this account.'
            ]);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                Auth::login($user);
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('supportstaff.login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }
}
