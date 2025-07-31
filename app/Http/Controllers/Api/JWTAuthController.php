<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use App\Models\User;
use App\Models\Setting;
use App\Models\Wallet;

class JWTAuthController extends Controller
{
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'lastname' => 'sometimes|nullable|string|max:255',
            'email' => 'required|string|max:255|email|unique:users,email',
            'mobile' => 'required|string|unique:users,mobile',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:8',
            'dob' => 'required|date|date_format:Y-m-d|before_or_equal:' . date('Y-m-d', strtotime('-18 years')),
            'gender' => 'required|string|max:255',
            'place' => 'sometimes|nullable|string|max:255',
            'street' => 'sometimes|nullable|string|max:255',
            'zipcode' => 'sometimes|nullable|string|max:255',
            'city' => 'sometimes|nullable|string|max:255',
            'state' => 'sometimes|nullable|string|max:255',
            'country' => 'sometimes|nullable|string|max:255',
            'referral_coupon' => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
                'data' => $request->all(),
            ], 422);
        }

        $user = new User();
        $user->name = $request->name;
        if ($request->lastname) {
            $user->lastname = $request->lastname;
        }
        $user->email = $request->email;
        $user->mobile = $request->mobile;
        $user->username = $request->username;
        $user->password = Hash::make($request->password);
        if ($request->dob) {
            $user->dob = $request->dob;
        }
        if ($request->gender) {
            $user->gender = $request->gender;
        }
        if ($request->place) {
            $user->place = $request->place;
        }
        if ($request->street) {
            $user->street = $request->street;
        }
        if ($request->zipcode) {
            $user->zipcode = $request->zipcode;
        }
        if ($request->city) {
            $user->city = $request->city;
        }
        if ($request->state) {
            $user->state = $request->state;
        }
        if ($request->country) {
            $user->country = $request->country;
        }
        $user->referral_coupon = strtoupper(substr(str_shuffle(uniqid()), 5, 13));
        $user->referral_percentage = Setting::firstWhere('key', 'referrer')->value ?? 0.00;
        $user->save();
        $user->assignRole('User');

        $token = JWTAuth::fromUser($user); // JWTAuth::login($user);

        if ($request->referral_coupon) {
            $referrerUser = User::firstWhere('referral_coupon', $request->referral_coupon);
            if ($referrerUser) {
                $wallet = new Wallet();
                $wallet->user_id = $user->id;
                $wallet->table_id = $referrerUser->id;
                $wallet->table_type = 'users';
                $wallet->amount = $referrerUser->referral_percentage ?? 0.00;
                $wallet->status = 'init';
                $wallet->save();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'successful',
            'data' => compact('user', 'token'),
        ]);
    }

    public function login(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required|string|max:255',
                'password' => 'required|string|min:8',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors(),
                    'data' => $request->all(),
                ], 422);
            }

            if (filter_var($request->username, FILTER_VALIDATE_EMAIL)) {
                // It's an email
                $credentials = ['email' => $request->username, 'password' => $request->password];
            } elseif (preg_match('/^[0-9]+$/', $request->username)) {
                // It's a mobile number (assuming mobile consists of numbers only)
                $credentials = ['mobile' => $request->username, 'password' => $request->password];
            } else {
                // It's a username
                $credentials = ['username' => $request->username, 'password' => $request->password];
            }

            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials',
                    'data' => $request->all(),
                ], 401);
            }

            $user = JWTAuth::user(); // alias: auth()->guard('api')->user();

            return response()->json([
                'success' => true,
                'message' => 'successful',
                'data' => compact('user', 'token'),
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => $e,
            ], 500);
        }
    }

    public function loginByUser(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'sometimes|nullable|string|max:255|email',
            'mobile' => 'sometimes|nullable|string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
                'data' => $request->all(),
            ], 422);
        }

        $user = User::query()
            ->when($request->email, function ($query) use ($request) {
                $query->where('email', $request->email);
            })
            ->when($request->mobile, function ($query) use ($request) {
                $query->where('mobile', $request->mobile);
            })
            ->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
                'data' => $request->all(),
            ], 404);
        }

        $token = JWTAuth::fromUser($user); // JWTAuth::login($user);

        return response()->json([
            'success' => true,
            'message' => 'successful',
            'data' => compact('user', 'token'),
        ]);
    }

    public function social(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'lastname' => 'nullable|string|max:255',
            'email' => 'required|string|max:255|email|unique:users,email',
            'mobile' => 'sometimes|nullable|string|unique:users,mobile',
            'dob' => 'sometimes|nullable|date|date_format:Y-m-d|before_or_equal:' . date('Y-m-d', strtotime('-18 years')),
            'gender' => 'nullable|string|max:255',
            'place' => 'nullable|string|max:255',
            'street' => 'nullable|string|max:255',
            'zipcode' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'google_id' => 'sometimes|nullable|string|max:255',
            'meta_id' => 'sometimes|nullable|string|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
                'data' => $request->all(),
            ], 422);
        }

        $user = User::firstWhere('email', $request->email);
        if (!$user) {
            $user = new User();
            $user->name = $request->name;
            if ($request->lastname) {
                $user->lastname = $request->lastname;
            }
            $user->email = $request->email;
            $user->mobile = $request->mobile;
            if ($request->dob) {
                $user->dob = $request->dob;
            }
            if ($request->gender) {
                $user->gender = $request->gender;
            }
            if ($request->place) {
                $user->place = $request->place;
            }
            if ($request->street) {
                $user->street = $request->street;
            }
            if ($request->zipcode) {
                $user->zipcode = $request->zipcode;
            }
            if ($request->city) {
                $user->city = $request->city;
            }
            if ($request->state) {
                $user->state = $request->state;
            }
            if ($request->country) {
                $user->country = $request->country;
            }
            $user->referral_coupon = strtoupper(substr(str_shuffle(uniqid()), 5, 13));
            $user->save();
            $user->assignRole('User');
        }
        if ($request->google_id) {
            $user->google_id = $request->google_id;
        }
        if ($request->meta_id) {
            $user->meta_id = $request->meta_id;
        }
        $user->save();

        $token = JWTAuth::fromUser($user); // JWTAuth::login($user);

        return response()->json([
            'success' => true,
            'message' => 'successful',
            'data' => compact('user', 'token'),
        ]);
    }

    public function getUser() {
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found.',
                    'data' => collect(),
                ], 404);
            }

            $photo = $user->load('photo');
            $background = $user->load('background');
            $bank = $user->load('bank');

            $token = request()->bearerToken() ?? JWTAuth::refresh();

            return response()->json([
                'success' => true,
                'message' => 'successful',
                'data' => compact('user', 'photo', 'background', 'bank', 'token'),
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
                'data' => null,
            ], 401);
        }
    }

    public function updateUser(Request $request) {
        $user = JWTAuth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
                'data' => collect(),
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|nullable|string|max:255',
            'lastname' => 'sometimes|nullable|string|max:255',
            'email' => 'sometimes|nullable|string|max:255|email|unique:users,email,' . $user->id,
            'mobile' => 'sometimes|nullable|string|unique:users,mobile,' . $user->id,
            'username' => 'sometimes|nullable|string|unique:users,username,' . $user->id,
            'dob' => 'sometimes|nullable|date|date_format:Y-m-d|before_or_equal:' . date('Y-m-d', strtotime('-18 years')),
            'gender' => 'sometimes|nullable|string|max:255',
            'place' => 'sometimes|nullable|string|max:255',
            'street' => 'sometimes|nullable|string|max:255',
            'zipcode' => 'sometimes|nullable|string|max:255',
            'city' => 'sometimes|nullable|string|max:255',
            'state' => 'sometimes|nullable|string|max:255',
            'country' => 'sometimes|nullable|string|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
                'data' => $request->all(),
            ], 422);
        }

        if ($request->name) {
            $user->name = $request->name;
        }
        if ($request->lastname) {
            $user->lastname = $request->lastname;
        }
        if ($request->email) {
            $user->email = $request->email;
        }
        if ($request->mobile) {
            $user->mobile = $request->mobile;
        }
        if ($request->username) {
            $user->username = $request->username;
        }
        if ($request->dob) {
            $user->dob = $request->dob;
        }
        if ($request->gender) {
            $user->gender = $request->gender;
        }
        if ($request->place) {
            $user->place = $request->place;
        }
        if ($request->street) {
            $user->street = $request->street;
        }
        if ($request->zipcode) {
            $user->zipcode = $request->zipcode;
        }
        if ($request->city) {
            $user->city = $request->city;
        }
        if ($request->state) {
            $user->state = $request->state;
        }
        if ($request->country) {
            $user->country = $request->country;
        }
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'successful',
            'data' => compact('user'),
        ]);
    }

    public function changePassword(Request $request) {
        $user = JWTAuth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
                'data' => collect(),
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed|different:current_password',
            'password_confirmation' => 'required|min:8',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
                'data' => $request->all(),
            ], 422);
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => [
                    'current_password' => [
                        'Current password is incorrect.'
                    ],
                ],
                'data' => $request->all(),
            ], 422);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'successful',
            'data' => compact('user'),
        ]);
    }

    public function resetPassword(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'sometimes|nullable|string|max:255|email',
            'mobile' => 'sometimes|nullable|string',
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required|min:8',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
                'data' => $request->all(),
            ], 422);
        }

        $user = User::query()
            ->when($request->email, function ($query) use ($request) {
                $query->where('email', $request->email);
            })
            ->when($request->mobile, function ($query) use ($request) {
                $query->where('mobile', $request->mobile);
            })
            ->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
                'data' => $request->all(),
            ], 404);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'successful',
            'data' => compact('user'),
        ]);
    }

    public function referrals(Request $request) {
        $user = JWTAuth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
                'data' => collect(),
            ], 404);
        }

        $referrals = Wallet::with(['user', 'user.photo'])
            ->where('table_id', $user->id)
            ->where('table_type', 'users')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'successful',
            'data' => compact('referrals'),
        ]);
    }

    public function refreshToken() {
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found.',
                    'data' => collect(),
                ], 404);
            }

            $token = JWTAuth::refresh();

            return response()->json([
                'success' => true,
                'message' => 'successful',
                'data' => compact('user', 'token'),
            ]);
        } catch (TokenExpiredException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token has expired.',
                'data' => null,
            ], 401);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token is invalid.',
                'data' => null,
            ], 401);
        }
    }

    public function logout(Request $request) {
        try {
            $token = JWTAuth::getToken() ?? $request->bearerToken();
            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token not found.',
                    'data' => null,
                ], 404);
            }

            // Invalidate the token
            JWTAuth::invalidate($token);

            return response()->json([
                'success' => true,
                'message' => 'successful',
                'data' => null,
            ]);
        } catch (TokenExpiredException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token has expired.',
                'data' => null,
            ], 401);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token is invalid.',
                'data' => null,
            ], 401);
        }
    }
}
