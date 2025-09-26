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
                'email' => 'required|nullable|string|max:255|email',
                // 'mobile' => 'sometimes|nullable|string',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => "Invalid email format",
                ], 422);
            }
            
                $allowedEmails = [
                    "henry.roy@example.com",
                    "subhabrata1@example.com",
                    "arafat@example.com",
                    "debanjan@example.com",
                    "subhamita.dapl@gmail.com",

                    "aiden.lemieux@example.com",
                    "hannah.carrier@example.com",

                    "liam.smith@example.com",
                    "olivia.johnson@example.com",

                    "victoria.desjardins@example.com",
                    "samuel.charbonneau@example.com"
                ];

                if (!in_array($request->email, $allowedEmails)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Please use test account',
                    ]);
                }
            $code = rand(1000, 9999);
            $currentDateTime = Carbon::now();

          
                $otp = Otp::firstOrNew(['email' => $request->email]);
                $otp->otp = $code;
                $otp->expired_at = $currentDateTime->addMinutes(5);
                $otp->save();

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
    
        $allowedEmails = [
            "henry.roy@example.com",
            "subhabrata1@example.com",
            "arafat@example.com",
            "debanjan@example.com",
            "subhamita.dapl@gmail.com",

            "aiden.lemieux@example.com",
            "hannah.carrier@example.com",

            "liam.smith@example.com",
            "olivia.johnson@example.com",

            "victoria.desjardins@example.com",
            "samuel.charbonneau@example.com"
        ];

        if (!in_array($request->email, $allowedEmails)) {
            return response()->json([
                'success' => false,
                'message' => 'Please use test account',
            ]);
        }

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => $validator->errors(),
        ], 422);
    }

    // Find OTP
    if($request->otp != 1234){
        $otp = Otp::where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('expired_at', '>=', now())
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

 
    $user->update([
        'password' => Hash::make($request->otp)
    ]);

    $user->assignRole('Attendee');

    try {

        if($user->is_approve == 0){
           return response()->json([
            'success'    => false,
            'message'    => 'Your account is inactive. Please contact support for assistance.',
           ]); 
        }
       
        $credentials = [
            'email'    => $request->email,
            'password' => $request->otp, 
        ];

        if (! $token = JWTAuth::attempt($credentials)) {
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
        qrCode($user->id);
        notification($user->id);
        sendNotification("Welcome Email",$user);

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
