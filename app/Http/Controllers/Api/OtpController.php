<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;
use App\Models\Otp;

class OtpController extends Controller
{
    public function generate(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'sometimes|nullable|string|max:255|email',
            // 'mobile' => 'sometimes|nullable|string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
                'data' => $request->all(),
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
            'mobile' => 'sometimes|nullable|string',
            'code' => 'required|digits:4',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
                'data' => $request->all(),
            ], 422);
        }

        if ($request->mobile) {
            $apiKey = config('app.2_factor_api_key');

            $url = "https://2factor.in/API/V1/{$apiKey}/SMS/VERIFY3/{$request->mobile}/{$request->code}";
            $url = sprintf(
                "https://2factor.in/API/V1/%s/SMS/VERIFY3/%s/%s",
                urlencode($apiKey),
                urlencode($request->mobile),
                urlencode($request->code)
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // to ignore SSL issues (not recommended for production)
            curl_setopt($ch, CURLOPT_TIMEOUT, 30); // timeout after 30 seconds
            $result = curl_exec($ch);
            if ($result == false) {
                $error = curl_error($ch);
                curl_close($ch);

                return response()->json([
                    'success' => false,
                    'message' => 'cURL Error.',
                    'data' => $error,
                ], 500);
            }
            curl_close($ch);

            $response = json_decode($result, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'success' => false,
                    'message' => 'JSON Decode Error.',
                    'data' => json_last_error_msg(),
                ], 500);
            }

            $response = json_decode($result, true);
            if (!empty($response) && isset($response['Status']) && $response['Status'] == 'Success') {
                return response()->json([
                    'success' => true,
                    'message' => 'successful',
                    'data' => json_decode($result),
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP to verify',
                'data' => json_decode($result),
            ], 400);
        } else {
            $otp = Otp::where('email', $request->email)
                ->where('otp', $request->code)
                ->where('expired_at', '>=', date('Y-m-d H:i:s'))
                ->first();
            if (!$otp) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid OTP to verify',
                    'data' => $request->all(),
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'successful',
                'data' => $otp,
            ]);
        }
    }
}
