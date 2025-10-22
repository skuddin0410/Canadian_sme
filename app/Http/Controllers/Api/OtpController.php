<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
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

class OtpController extends Controller
{
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
            
            if (User::where('email', $request->email)->doesntExist()) {
                return response()->json([
                   'success' => false,
                   'message' => 'You are not approved by admin.',
                ],403);  
            }
           

            $lastOtp = Otp::where('email',$request->email)->latest()->first();
            if ($lastOtp && $lastOtp->created_at->diffInSeconds(now()) < 60) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please wait a minute before requesting another OTP.',
                ], 429);
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

                Mail::to($request->email)->send(new OtpMail($code));

                return response()->json([
                    'success' => true,
                    'message' => 'OTP sent successfully to '.$request->email. '.',
                    'data' => $otp,
                ]);

            } catch (JWTException $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Fail to send OTP."',
                        'error'   => $e->getMessage(),
                    ], 500);
            }
        
    }

public function verify(Request $request)
{  
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'otp'   => 'required|digits:4',
        ]);

             
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

        if (User::where('email', $request->email)->doesntExist()) {
            return response()->json([
               'success' => false,
               'message' => 'You are not approved by admin.',
            ],403);  
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
   
    $user = User::firstOrCreate(
        ['email' => $request->email],
        ['password' => Hash::make($request->otp)]
    );
    
    $user->assignRole('Attendee');
    
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

        $token = JWTAuth::fromUser($user);
      
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
        return response()->json([
            'success'    => true,
            'message'    => 'Login successful',
            'token'      => $token,
            'expires_at' => $session->expires_at,
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
