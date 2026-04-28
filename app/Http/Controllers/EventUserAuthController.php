<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Form;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Models\Otp;


class EventUserAuthController extends Controller
{
    public function showLogin(Event $event)
    {
        Session::put('event_id', $event->id);
        // dd($event);

        return view('auth.login', compact('event'));
    }

    public function login(Request $request, Event $event)
    {
        // This is the fallback for non-AJAX if needed, 
        // but we'll primarily use sendOtp and verifyOtp via AJAX.
        return $this->verifyOtp($request);
    }

    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Invalid email format'], 422);
        }

        $email = $request->email;
        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Email not found in our records.'], 404);
        }

        if ($user->is_approve == 0) {
            return response()->json(['success' => false, 'message' => 'Your account is inactive.'], 403);
        }

        // Role check
        $isAdminRoute = $request->isAdmin == 'true'; // Passed from frontend
        $hasAdminRole = $user->hasAnyRole(['Admin', 'Super Admin', 'Exhibitor', 'Representative', 'Speaker', 'Support Staff Or Helpdesk', 'Registration Desk']);
        $isAttendee = $user->hasRole('Attendee');

        if ($isAdminRoute && !$hasAdminRole) {
            return response()->json(['success' => false, 'message' => 'These credentials do not match our records.'], 403);
        }

        // Priority: If user is an Admin, they MUST login via Admin route and are blocked from Attendee route
        if (!$isAdminRoute && $hasAdminRole) {
             return response()->json(['success' => false, 'message' => 'These credentials do not match our records.'], 403);
        }

        if (!$isAdminRoute && !$isAttendee) {
            return response()->json(['success' => false, 'message' => 'These credentials do not match our records.'], 403);
        }

        // Check mapping if event_id is provided
        if ($request->has('event_id') && !empty($request->event_id)) {
            $isMapped = DB::table('event_and_entity_link')
                ->where('event_id', $request->event_id)
                ->where('entity_type', 'users')
                ->where('entity_id', $user->id)
                ->exists();

            if (!$isMapped) {
                return response()->json(['success' => false, 'message' => 'You are not registered for this event.'], 403);
            }
        }

        $code = rand(1000, 9999);
        Otp::updateOrCreate(
            ['email' => $email],
            ['otp' => $code, 'expired_at' => now()->addMinutes(10)]
        );

        Mail::raw($code . ' is your login OTP. Please ensure this as confidential. ' . config('app.name') . ' will never call you to verify your OTP.', function ($m) use ($email) {
            $m->to($email)->subject('Login OTP');
        });

        return response()->json(['success' => true, 'message' => 'OTP sent successfully to ' . $email]);
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'otp' => 'required|digits:4',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed'], 422);
        }

        $otp = Otp::where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('expired_at', '>', now())
            ->first();

        if (!$otp) {
            return response()->json(['success' => false, 'message' => 'Invalid or expired OTP'], 400);
        }

        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
             return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        Auth::login($user, $request->filled('remember'));
        
        $redirectUrl = '/user/home';
        if ($user->hasAnyRole(['Admin', 'Super Admin', 'Exhibitor', 'Representative', 'Speaker', 'Support Staff Or Helpdesk', 'Registration Desk'])) {
            $redirectUrl = '/admin/home';
        } else if ($user->hasRole('Attendee')) {
            $eventId = Session::get('event_id');
            if ($eventId) {
                $event = Event::find($eventId);
                if ($event) {
                    $redirectUrl = route('user.front.events', $event->slug);
                    // Session::forget('event_id'); // Keep it in session if needed for other parts, or forget it
                }
            }
        }

        $otp->delete();

        return response()->json([
            'success' => true, 
            'message' => 'Login successful',
            'redirect' => $redirectUrl
        ]);
    }

    public function showRegister(Event $event)
    {
        Session::put('event_id', $event->id);

        $form = Form::where('is_active', true)->first();
        $tickets = TicketType::where('event_id', $event->id)->get();

        return view('formbuilder.showform', compact('event', 'form', 'tickets'));
    }
}
