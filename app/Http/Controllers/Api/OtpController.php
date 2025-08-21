<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Otp;
use App\Models\User;
use App\Mail\OtpMail;
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
            'email' => 'sometimes|nullable|string|max:255|email',
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

    
    public function verify(Request $request) {
    $validator = Validator::make($request->all(), [
        'email' => 'sometimes|nullable|string|max:255|email',
        // 'mobile' => 'sometimes|nullable|string',
        'otp'   => 'required|digits:4',
    ]);
    
    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => $validator->errors(),
            'data'    => $request->all(),
        ], 422);
    }

    
    $otp = Otp::where('email', $request->email)
        ->where('otp', $request->otp)
        ->where('expired_at', '>=', now())
        ->first();

    if (!$otp) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid OTP',
            // 'data'    => $request->all(),
        ], 400);
    }

    
    $user = User::where('email', $request->email)->first();

    if (!$user) {
        // Create new user if not exists
        $user = new User();
        $user->email = $request->email;
        $user->password = Hash::make($request->otp); // store hashed OTP
        $user->save();
    } else {
        // Update password with new OTP hash (optional, so OTP works for login)
        $user->password = Hash::make($request->otp);
        $user->save();
    }

    try {
        // Prepare credentials for JWT attempt
        $credentials = [
            'email'    => $request->email,
            'password' => $request->otp, // raw OTP entered by user
        ];

        if (! $token = JWTAuth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
                'data'    => $request->all(),
            ], 401);
        }

        $user = JWTAuth::user();

        return response()->json([
            // 'success' => true,
            'message' => 'Login successful',
            'data'    => compact( 'token'),
        ]);

    } catch (JWTException $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
            'data'    => [],
        ], 500);
    }
}

}
