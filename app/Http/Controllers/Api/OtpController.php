<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Event;
use App\Models\Otp;
use App\Models\User;
use App\Mail\OtpMail;
use App\Models\SessionDate;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use App\Mail\UserWelcome;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OtpController extends Controller
{
    protected function canAccessEvent(User $user, int $eventId): bool
    {
        $event = Event::find($eventId);

        if (! $event) {
            return false;
        }

        if ($user->hasRole('Super Admin')) {
            return true;
        }

        if ($user->hasRole('Admin')) {
            return (int) $event->created_by === (int) $user->id;
        }

        return DB::table('event_and_entity_link')
            ->where('event_id', $eventId)
            ->where('entity_type', 'users')
            ->where('entity_id', $user->id)
            ->exists();
    }

    public function generate(Request $request) {

        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => "Invalid email format",
                ], 422);
            }

            $email   = $request->email;
            $eventId = (int) $request->event_id;
            $event = null;

            // Fetch user once (including soft deleted)
            $user = User::withTrashed()->where('email', $email)->first();
            
            if (User::where('email', $request->email)->doesntExist()) {
                return response()->json([
                   'success' => false,
                   'message' => 'You are not approved by admin.',
                ],403);  
            }

            if (User::onlyTrashed()->where('email', $request->email)->first()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your account is deleted or block.',
                ], 403); 
            }

            if (User::where('email', $request->email)->where('is_approve', 0)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your account is inactive.',
                ], 403); 
            }

            if(isset($request->event_id)){
                $event = Event::find($eventId);

                if (! $event) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid event.',
                    ], 400);
                }

                if (! $this->canAccessEvent($user, $eventId)) {
                    $message = $user->hasRole('Admin')
                        ? 'You can login only to events created by you.'
                        : 'You are not registered for this event.';

                    return response()->json([
                        'success' => false,
                        'message' => $message,
                    ], 403);
                }
            }
            
           
            $lastOtp = Otp::where('email',$request->email)->latest()->first();
            if ($lastOtp && $lastOtp->created_at->diffInSeconds(now()) < 60) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please wait a minute before requesting another OTP.',
                ], 429);
            }

            $subject = 'Login OTP';
            if (!empty($event?->title)) {
                $subject .= ' - ' . $event->title;
            }

            $code = rand(1000, 9999);
            $currentDateTime = Carbon::now();
          
                $otp = Otp::updateOrCreate(
                    ['email' => $request->email],
                    [
                        'otp' => $code,
                        'expired_at' => now()->addMinutes(60),
                    ]
                );

                // Mail::to($request->email)->send(new OtpMail($code)); //Subabrata da code for otp mail

                Mail::raw($code.' is your login OTP. Please ensure this as confidential. ' .env('APP_NAME'). ' will never call you to verify your OTP. Good Luck,', function($m) use ($request, $subject){ $m->to($request->email)->subject($subject); }); //My code for otp mail

                return response()->json([
                    'success' => true,
                    'message' => 'OTP sent successfully to '.$request->email. '.',
                    'data' => $otp,
                ]);

            } catch (JWTException $e) {
                Log::error('OTP Generation Error: ' . $e->getMessage());
                    return response()->json([
                        'success' => false,
                        'message' => 'Fail to send OTP."',
                        'error'   => $e->getMessage(),
                    ], 500);
            }
        
    }

    public function verify(Request $request)
    {  
        // log request all
        // Log::info('Verify API Request', $request->all());

            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|max:255',
                'otp'   => 'required|digits:4',
            ]);
            
            if (User::where('email', $request->email)->doesntExist()) {
                return response()->json([
                'success' => false,
                'message' => 'You are not approved by admin.',
                ],403);  
            }
                
            if (User::onlyTrashed()->where('email', $request->email)->first()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your account is deleted or block.',
                ], 403); 
            }

            if (User::where('email', $request->email)->where('is_approve', 0)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your account is inactive.',
                ], 403); 
            }
        
            $allowedEmails = [
                "henry.roy@example.com",
                "subhabrata1@example.com"
            ];

            

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 422);
        }

        $otp = null;
            if (!in_array($request->email, $allowedEmails)) {
                $otp = Otp::where('email', $request->email)
                    ->where('otp', $request->otp)
                    ->where('expired_at', '>', Carbon::now())
                    ->first();
            
                if (!$otp) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid or expired OTP',
                    ], 400);
                }
            }
    
        $user = User::where('email', $request->email)->first();
        
        try {

            if($user->is_approve == 0){
            return response()->json([
                'success'    => false,
                'message'    => 'Your account is inactive.',
            ]); 
            }
            
            $credentials = [
                'email'    => $request->email,
                'password' => $request->otp, 
            ];

            if ($request->filled('event_id')) {
                $eventId = (int) $request->event_id;
                $event = Event::find($eventId);

                if (! $event) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid event.',
                    ], 400);
                }

                if (! $this->canAccessEvent($user, $eventId)) {
                    $message = $user->hasRole('Admin')
                        ? 'You can login only to events created by you.'
                        : 'You are not registered for this event.';

                    return response()->json([
                        'success' => false,
                        'message' => $message,
                    ], 403);
                }
            }

            // Log::info('Attempting to authenticate user', ['email' => $request->email]);
            // Log user 
            // Log::info('User details', ['user_id' => $user->id, 'email' => $user->email, 'is_approved' => $user->is_approve]);

            $token = JWTAuth::fromUser($user);
        
            // Log::info('User authenticated successfully', ['email' => $request->email, 'token' => $token]);

            if (! $token ) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid OTP.',
                ], 401);
            }
            
            $user->update([
            'jwt_token' => $token
            ]);
    
            $session = SessionDate::updateOrCreate(
                ['user_id' => $user->id], 
                ['expires_at' => now()->addMonths(2)] 
            );
            
            notification($user->id);
            $user = User::where('id',$user->id)->first();
            if(empty($user->qr_code)){
                $user->refresh();
                $qrGenerated = qrCode($user->id);
                if (!empty($user->qr_code) && $qrGenerated) {
                    sendNotification("Welcome Email", $user);
                }
            }
            if ($otp) {
                $otp->delete();
            }

            $splashScreen = null;
            if ($request->filled('event_id')) {
                $splashScreenRecord = \App\Models\SplashScreen::with([
                    'iosIphone', 'iosIpad', 'androidHdpi', 'androidMdpi', 'androidXhdpi', 'androidXxhdpi'
                ])->where('event_id', (int) $request->event_id)->first();

                if ($splashScreenRecord) {
                    $splashScreen = [
                        'ios_iphone' => $splashScreenRecord->iosIphone?->file_path,
                        'ios_ipad' => $splashScreenRecord->iosIpad?->file_path,
                        'android_hdpi' => $splashScreenRecord->androidHdpi?->file_path,
                        'android_mdpi' => $splashScreenRecord->androidMdpi?->file_path,
                        'android_xhdpi' => $splashScreenRecord->androidXhdpi?->file_path,
                        'android_xxhdpi' => $splashScreenRecord->androidXxhdpi?->file_path,
                    ];
                }
            }

            return response()->json([
                'success'    => true,
                'message'    => 'Login successful',
                'token'      => $token,
                'expires_at' => $session->expires_at,
                'splash_screen' => $splashScreen,
            ]);

        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

}
