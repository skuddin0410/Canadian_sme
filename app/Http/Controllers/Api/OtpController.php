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

            $code = random_int(1000, 9999);
            $expiresMinutes = 60;

            $otp = Otp::updateOrCreate(
                ['email' => $request->email],
                [
                    'otp' => $code,
                    'expired_at' => now()->addMinutes($expiresMinutes),
                ]
            );

            Mail::to($request->email)->send(new OtpMail($code, $event, $expiresMinutes));

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

    // public function verify(Request $request)
    // {  
    //     // log request all
    //     Log::info('Verify API Request', $request->all());

    //         $validator = Validator::make($request->all(), [
    //             'email' => 'required|string|email|max:255',
    //             'otp'   => 'required|digits:4',
    //         ]);
            
    //         if (User::where('email', $request->email)->doesntExist()) {
    //             return response()->json([
    //             'success' => false,
    //             'message' => 'You are not approved by admin.',
    //             ],403);  
    //         }
                
    //         if (User::onlyTrashed()->where('email', $request->email)->first()) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Your account is deleted or block.',
    //             ], 403); 
    //         }

    //         if (User::where('email', $request->email)->where('is_approve', 0)->exists()) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Your account is inactive.',
    //             ], 403); 
    //         }
        
    //         $allowedEmails = [
    //             "henry.roy@example.com",
    //             "subhabrata1@example.com"
    //         ];

            

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => $validator->errors(),
    //         ], 422);
    //     }

    //     $otp = null;
    //         if (!in_array($request->email, $allowedEmails)) {
    //             $otp = Otp::where('email', $request->email)
    //                 ->where('otp', $request->otp)
    //                 ->where('expired_at', '>', Carbon::now())
    //                 ->first();
            
    //             if (!$otp) {
    //                 return response()->json([
    //                     'success' => false,
    //                     'message' => 'Invalid or expired OTP',
    //                 ], 400);
    //             }
    //         }
    
    //     $user = User::where('email', $request->email)->first();
        
    //     try {

    //         if($user->is_approve == 0){
    //         return response()->json([
    //             'success'    => false,
    //             'message'    => 'Your account is inactive.',
    //         ]); 
    //         }
            
    //         $credentials = [
    //             'email'    => $request->email,
    //             'password' => $request->otp, 
    //         ];

    //         if ($request->filled('event_id')) {
    //             $eventId = (int) $request->event_id;
    //             $event = Event::find($eventId);

    //             if (! $event) {
    //                 return response()->json([
    //                     'success' => false,
    //                     'message' => 'Invalid event.',
    //                 ], 400);
    //             }

    //             if (! $this->canAccessEvent($user, $eventId)) {
    //                 $message = $user->hasRole('Admin')
    //                     ? 'You can login only to events created by you.'
    //                     : 'You are not registered for this event.';

    //                 return response()->json([
    //                     'success' => false,
    //                     'message' => $message,
    //                 ], 403);
    //             }
    //         }

    //         // Log::info('Attempting to authenticate user', ['email' => $request->email]);
    //         // Log user 
    //         // Log::info('User details', ['user_id' => $user->id, 'email' => $user->email, 'is_approved' => $user->is_approve]);

    //         $token = JWTAuth::fromUser($user);
        
    //         Log::info('User authenticated successfully', ['email' => $request->email, 'token' => $token]);

    //         if (! $token ) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Invalid OTP 1.',
    //             ], 401);
    //         }
            
    //         $user->update([
    //         'jwt_token' => $token
    //         ]);
    
    //         $session = SessionDate::updateOrCreate(
    //             ['user_id' => $user->id], 
    //             ['expires_at' => now()->addMonths(2)] 
    //         );
            
    //         notification($user->id);
    //         $user = User::where('id',$user->id)->first();
    //         if(empty($user->qr_code)){
    //             $user->refresh();
    //             $qrGenerated = qrCode($user->id);
    //             if (!empty($user->qr_code) && $qrGenerated) {
    //                 sendNotification("Welcome Email", $user);
    //             }
    //         }
    //         if ($otp) {
    //             $otp->delete();
    //         }

    //         $splashScreen = null;
    //         if ($request->filled('event_id')) {
    //             $splashScreenRecord = \App\Models\SplashScreen::with([
    //                 'iosIphone', 'iosIpad', 'androidHdpi', 'androidMdpi', 'androidXhdpi', 'androidXxhdpi'
    //             ])->where('event_id', (int) $request->event_id)->first();

    //             if ($splashScreenRecord) {
    //                 $splashScreen = [
    //                     'ios_iphone' => $splashScreenRecord->iosIphone?->file_path,
    //                     'ios_ipad' => $splashScreenRecord->iosIpad?->file_path,
    //                     'android_hdpi' => $splashScreenRecord->androidHdpi?->file_path,
    //                     'android_mdpi' => $splashScreenRecord->androidMdpi?->file_path,
    //                     'android_xhdpi' => $splashScreenRecord->androidXhdpi?->file_path,
    //                     'android_xxhdpi' => $splashScreenRecord->androidXxhdpi?->file_path,
    //                 ];
    //             }
    //         }

    //         return response()->json([
    //             'success'    => true,
    //             'message'    => 'Login successful',
    //             'token'      => $token,
    //             'expires_at' => $session->expires_at,
    //             'splash_screen' => $splashScreen,
    //         ]);

    //     } catch (JWTException $e) {
    //         dd($e->getMessage());
    //         Log::error('JWT Exception during OTP verification: ' . $e->getMessage());
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Invalid OTP 2.',
    //             'error'   => $e->getMessage(),
    //         ], 500);
    //     }
    // }


    public function verify(Request $request)
    {
        Log::info('========== VERIFY API START ==========', [
            'email' => $request->email,
            'otp_present' => $request->filled('otp'),
            'otp_length' => $request->otp ? strlen((string) $request->otp) : 0,
            'event_id' => $request->event_id ?? null,
        ]);

        // ---------------------------------------------------------
        // STEP 1: Validate request
        // ---------------------------------------------------------
        Log::info('VERIFY STEP 1: Starting validation');

        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'otp'   => 'required|digits:4',
        ]);

        if ($validator->fails()) {

            Log::warning('VERIFY STEP 1 FAILED: Validation failed', [
                'errors' => $validator->errors()->toArray(),
                'email' => $request->email,
            ]);

            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 422);
        }

        Log::info('VERIFY STEP 1 SUCCESS: Validation passed');


        // ---------------------------------------------------------
        // STEP 2: Check user exists
        // ---------------------------------------------------------
        Log::info('VERIFY STEP 2: Checking user existence', [
            'email' => $request->email,
        ]);

        $userExists = User::where('email', $request->email)->exists();

        Log::info('VERIFY STEP 2 RESULT', [
            'user_exists' => $userExists,
        ]);

        if (!$userExists) {

            Log::warning('VERIFY STEP 2 FAILED: User does not exist', [
                'email' => $request->email,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'You are not approved by admin.',
            ], 403);
        }


        // ---------------------------------------------------------
        // STEP 3: Check deleted user
        // ---------------------------------------------------------
        Log::info('VERIFY STEP 3: Checking deleted account');

        $deletedUser = User::onlyTrashed()
            ->where('email', $request->email)
            ->first();

        Log::info('VERIFY STEP 3 RESULT', [
            'deleted_user' => !empty($deletedUser),
        ]);

        if ($deletedUser) {

            Log::warning('VERIFY STEP 3 FAILED: User account is deleted', [
                'email' => $request->email,
                'user_id' => $deletedUser->id ?? null,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Your account is deleted or block.',
            ], 403);
        }


        // ---------------------------------------------------------
        // STEP 4: Check approval
        // ---------------------------------------------------------
        Log::info('VERIFY STEP 4: Checking user approval');

        $inactiveUser = User::where('email', $request->email)
            ->where('is_approve', 0)
            ->exists();

        Log::info('VERIFY STEP 4 RESULT', [
            'inactive_user' => $inactiveUser,
        ]);

        if ($inactiveUser) {

            Log::warning('VERIFY STEP 4 FAILED: User inactive', [
                'email' => $request->email,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Your account is inactive.',
            ], 403);
        }


        // ---------------------------------------------------------
        // STEP 5: Get user
        // ---------------------------------------------------------
        Log::info('VERIFY STEP 5: Fetching user');

        $user = User::where('email', $request->email)->first();

        Log::info('VERIFY STEP 5 RESULT', [
            'user_id' => $user?->id,
            'email' => $user?->email,
            'is_approve' => $user?->is_approve,
        ]);


        // ---------------------------------------------------------
        // STEP 6: Allowed email / OTP verification
        // ---------------------------------------------------------
        $allowedEmails = [
            "henry.roy@example.com",
            "subhabrata1@example.com"
        ];

        Log::info('VERIFY STEP 6: Checking OTP', [
            'email' => $request->email,
            'is_allowed_email' => in_array($request->email, $allowedEmails),
        ]);

        $otp = null;

        if (!in_array($request->email, $allowedEmails)) {

            Log::info('VERIFY STEP 6.1: Searching OTP record');

            $otpQuery = Otp::where('email', $request->email)
                ->where('otp', $request->otp)
                ->where('expired_at', '>', Carbon::now());

            Log::info('VERIFY STEP 6.2: OTP query prepared', [
                'email' => $request->email,
                'current_time' => Carbon::now()->toDateTimeString(),
            ]);

            $otp = $otpQuery->first();

            if (!$otp) {

                // Check whether an OTP exists but expired
                $latestOtp = Otp::where('email', $request->email)
                    ->latest('created_at')
                    ->first();

                Log::warning('VERIFY STEP 6 FAILED: Invalid or expired OTP', [
                    'email' => $request->email,
                    'otp_record_found' => false,
                    'latest_otp_id' => $latestOtp?->id,
                    'latest_otp_expired_at' => $latestOtp?->expired_at,
                    'current_time' => Carbon::now()->toDateTimeString(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired OTP',
                ], 400);
            }

            Log::info('VERIFY STEP 6 SUCCESS: OTP verified', [
                'otp_id' => $otp->id,
                'email' => $otp->email,
                'expired_at' => $otp->expired_at,
            ]);

        } else {

            Log::info('VERIFY STEP 6: OTP validation skipped for allowed email', [
                'email' => $request->email,
            ]);
        }


        // ---------------------------------------------------------
        // STEP 7: Try block
        // ---------------------------------------------------------
        try {

            Log::info('VERIFY STEP 7: Entering authentication process', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);


            // -----------------------------------------------------
            // STEP 8: Check approval again
            // -----------------------------------------------------
            Log::info('VERIFY STEP 8: Checking approval before JWT');

            if ($user->is_approve == 0) {

                Log::warning('VERIFY STEP 8 FAILED: User inactive', [
                    'user_id' => $user->id,
                    'is_approve' => $user->is_approve,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Your account is inactive.',
                ]);
            }


            // -----------------------------------------------------
            // STEP 9: Event validation
            // -----------------------------------------------------
            if ($request->filled('event_id')) {

                Log::info('VERIFY STEP 9: Event login requested', [
                    'user_id' => $user->id,
                    'event_id' => $request->event_id,
                ]);

                $eventId = (int) $request->event_id;

                $event = Event::find($eventId);

                Log::info('VERIFY STEP 9.1: Event lookup result', [
                    'event_id' => $eventId,
                    'event_exists' => !empty($event),
                ]);

                if (!$event) {

                    Log::warning('VERIFY STEP 9 FAILED: Invalid event', [
                        'event_id' => $eventId,
                    ]);

                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid event.',
                    ], 400);
                }

                $canAccess = $this->canAccessEvent($user, $eventId);

                Log::info('VERIFY STEP 9.2: Event access result', [
                    'user_id' => $user->id,
                    'event_id' => $eventId,
                    'can_access' => $canAccess,
                    'is_admin' => $user->hasRole('Admin'),
                ]);

                if (!$canAccess) {

                    $message = $user->hasRole('Admin')
                        ? 'You can login only to events created by you.'
                        : 'You are not registered for this event.';

                    Log::warning('VERIFY STEP 9 FAILED: User cannot access event', [
                        'user_id' => $user->id,
                        'event_id' => $eventId,
                        'message' => $message,
                    ]);

                    return response()->json([
                        'success' => false,
                        'message' => $message,
                    ], 403);
                }

                Log::info('VERIFY STEP 9 SUCCESS: Event access allowed');

            } else {

                Log::info('VERIFY STEP 9: No event_id provided');
            }


            // -----------------------------------------------------
            // STEP 10: Generate JWT
            // -----------------------------------------------------
            Log::info('VERIFY STEP 10: Generating JWT', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            $token = JWTAuth::fromUser($user);

            Log::info('VERIFY STEP 10 RESULT: JWT generated', [
                'user_id' => $user->id,
                'token_generated' => !empty($token),
                'token_length' => $token ? strlen($token) : 0,
            ]);

            if (!$token) {

                Log::error('VERIFY STEP 10 FAILED: JWT token is empty', [
                    'user_id' => $user->id,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Invalid OTP 1.',
                ], 401);
            }


            // -----------------------------------------------------
            // STEP 11: Save JWT
            // -----------------------------------------------------
            Log::info('VERIFY STEP 11: Saving JWT token to user');

            $user->update([
                'jwt_token' => $token
            ]);

            Log::info('VERIFY STEP 11 SUCCESS: JWT token saved', [
                'user_id' => $user->id,
            ]);


            // -----------------------------------------------------
            // STEP 12: Create/update session
            // -----------------------------------------------------
            Log::info('VERIFY STEP 12: Creating/updating session');

            $session = SessionDate::updateOrCreate(
                ['user_id' => $user->id],
                ['expires_at' => now()->addMonths(2)]
            );

            Log::info('VERIFY STEP 12 SUCCESS: Session created/updated', [
                'user_id' => $user->id,
                'session_id' => $session->id ?? null,
                'expires_at' => $session->expires_at,
            ]);


            // -----------------------------------------------------
            // STEP 13: Notification
            // -----------------------------------------------------
            Log::info('VERIFY STEP 13: Calling notification()', [
                'user_id' => $user->id,
            ]);

            notification($user->id);

            Log::info('VERIFY STEP 13 SUCCESS');


            // -----------------------------------------------------
            // STEP 14: QR code
            // -----------------------------------------------------
            Log::info('VERIFY STEP 14: Checking QR code', [
                'user_id' => $user->id,
                'has_qr_code' => !empty($user->qr_code),
            ]);

            $user = User::where('id', $user->id)->first();

            if (empty($user->qr_code)) {

                Log::info('VERIFY STEP 14.1: QR code missing, generating');

                $user->refresh();

                $qrGenerated = qrCode($user->id);

                Log::info('VERIFY STEP 14.2: QR generation result', [
                    'user_id' => $user->id,
                    'qr_generated' => $qrGenerated,
                    'has_qr_code_after_generation' => !empty($user->qr_code),
                ]);

                if (!empty($user->qr_code) && $qrGenerated) {

                    Log::info('VERIFY STEP 14.3: Sending welcome email', [
                        'user_id' => $user->id,
                    ]);

                    sendNotification("Welcome Email", $user);

                    Log::info('VERIFY STEP 14.4: Welcome email function completed');
                }
            }


            // -----------------------------------------------------
            // STEP 15: Delete OTP
            // -----------------------------------------------------
            if ($otp) {

                Log::info('VERIFY STEP 15: Deleting used OTP', [
                    'otp_id' => $otp->id,
                ]);

                $otp->delete();

                Log::info('VERIFY STEP 15 SUCCESS: OTP deleted');
            }


            // -----------------------------------------------------
            // STEP 16: Splash screen
            // -----------------------------------------------------
            $splashScreen = null;

            if ($request->filled('event_id')) {

                Log::info('VERIFY STEP 16: Fetching splash screen', [
                    'event_id' => $request->event_id,
                ]);

                $splashScreenRecord = \App\Models\SplashScreen::with([
                    'iosIphone',
                    'iosIpad',
                    'androidHdpi',
                    'androidMdpi',
                    'androidXhdpi',
                    'androidXxhdpi'
                ])
                ->where('event_id', (int) $request->event_id)
                ->first();

                Log::info('VERIFY STEP 16 RESULT', [
                    'found' => !empty($splashScreenRecord),
                ]);

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


            // -----------------------------------------------------
            // STEP 17: Final response
            // -----------------------------------------------------
            Log::info('VERIFY STEP 17 SUCCESS: Login successful', [
                'user_id' => $user->id,
                'email' => $user->email,
                'session_expires_at' => $session->expires_at,
            ]);

            Log::info('========== VERIFY API END: SUCCESS ==========');

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'token' => $token,
                'expires_at' => $session->expires_at,
                'splash_screen' => $splashScreen,
            ]);

        } catch (JWTException $e) {

            Log::error('VERIFY JWT EXCEPTION', [
                'message' => $e->getMessage(),
                'exception' => get_class($e),
                'user_id' => $user?->id,
                'email' => $request->email,
                'trace' => $e->getTraceAsString(),
            ]);

            Log::error('========== VERIFY API END: JWT FAILED ==========');

            return response()->json([
                'success' => false,
                'message' => 'JWT authentication failed.',
                'error' => $e->getMessage(),
            ], 500);

        } catch (\Throwable $e) {

            Log::error('VERIFY GENERAL EXCEPTION', [
                'message' => $e->getMessage(),
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'user_id' => $user?->id ?? null,
                'email' => $request->email,
                'trace' => $e->getTraceAsString(),
            ]);

            Log::error('========== VERIFY API END: GENERAL FAILED ==========');

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
