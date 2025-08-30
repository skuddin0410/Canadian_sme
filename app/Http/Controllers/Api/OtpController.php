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

class OtpController extends Controller
{
    public function generate(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|nullable|string|max:255|email',
            // 'mobile' => 'sometimes|nullable|string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                // 'success' => false,
                'error' => "Invalid email format",
                // 'data' => $request->all(),
            ], 422);
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
                'message' => 'successful',
                'data' => $otp,
            ]);
        
    }

public function verify(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|string|email|max:255',
        'otp'   => 'required|digits:4',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => $validator->errors(),
        ], 422);
    }

    // Find OTP
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

   
    $user = User::firstOrCreate(
        ['email' => $request->email],
        ['password' => Hash::make($request->otp)]
    );

 
    $user->update([
        'password' => Hash::make($request->otp)
    ]);

    try {
       
        $credentials = [
            'email'    => $request->email,
            'password' => $request->otp, 
        ];

        if (! $token = JWTAuth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

    
        $session = SessionDate::create([
            'user_id'    => $user->id,
            'expires_at' => Carbon::now()->addMonths(2),
        ]);

        return response()->json([
            'success'    => true,
            'message'    => 'Login successful',
            'token'      => $token,
            'expires_at' => $session->expires_at,
        ]);

    } catch (JWTException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Could not create token',
            'error'   => $e->getMessage(),
        ], 500);
    }
}


}
