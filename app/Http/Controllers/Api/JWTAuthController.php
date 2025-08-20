<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Company;
use App\Models\Setting;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

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

    // public function getUser() {
    //     try {
    //         if (! $user = JWTAuth::parseToken()->authenticate()) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'User not found.',
    //                 'data' => collect(),
    //             ], 404);
    //         }

    //         $photo = $user->load('photo');
    //         $token = request()->bearerToken() ?? JWTAuth::refresh();
    //         $roles = $user->getRoleNames();

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'successful',
    //             'data' => compact('user', 'photo', 'roles', 'token'),
    //         ]);
    //     } catch (JWTException $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Unauthorized',
    //             'data' => null,
    //         ], 401);
    //     }
    // }
    public function getUser() {
    try {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json([
                'success' => false,
                'message' => 'Fail to load profile',
                'data' => collect(),
            ], 404);
        }

        // Load related photo once
        $user->load('photo'); 

        // Get Bearer token or refresh
        $token = request()->bearerToken() ?? JWTAuth::refresh();

        // Roles
        $roles = $user->getRoleNames();

        // Build address object
        $address = [
            'street'   => $user->address ?? null,
            'city'     => $user->city ?? null,
            'state'    => $user->state ?? null,
            'country'  => $user->country ?? null,
            'zipcode'  => $user->zipcode ?? null,
        ];
        $user->load(['photo', 'company']);

return response()->json([
    'success' => true,
    'message' => 'successful',
    'data' => [
        'user' => [
            'id'        => $user->id,
            'name'      => $user->name,
            'lastname'  => $user->lastname,
            'full_name' => $user->full_name,
            'email'     => $user->email,
            'mobile'    => $user->mobile,
            'designation'=> $user->designation,
            'bio'       => $user->about,
            'tags'      => $user->tags,
            'qr_code' => $user-> qr_code,
            'address'   => [
                'street'  => $user->street,
                'city'    => $user->city,
                'state'   => $user->state,
                'country' => $user->country,
                'zipcode' => $user->zipcode,
            ],
            'company'   => $user->company, // full company object
        ],
        'image_url' => $user->photo,
        'roles'     => $user->getRoleNames(),
    ],
]);


        // return response()->json([
        //     'success' => true,
        //     'message' => 'successful',
        //     'data' => [
        //         // full user info like before
        //         'user' => [
        //             'id'        => $user->id,
        //             'name'      => $user->name,
        //             'lastname'  => $user->lastname,
        //             'full_name' => $user->full_name,
        //             'email'     => $user->email,
        //             'mobile'    => $user->mobile,
        //             // 'QR'    => $user->qr_code,
        //             // 'gender'    => $user->gender,
        //             // 'dob'       => $user->dob,
        //             'company_name'   => $user->company_name,
        //             'company_email'   => $user->company_email,
        //             'company_phone'   => $user->company_phone,
        //             'designation'=> $user->designation,
        //             'bio'     => $user->about,
        //             'tags'      => $user->tags,
        //             // 'status'    => $user->status,
        //             'created_at'=> $user->created_at,
        //             'updated_at'=> $user->updated_at,
        //             // merged address
        //             'address'   => $address,
        //         ],
        //         'image_url' => $user->photo, // photo relation
        //         'roles' => $roles,       // roles separately
        //         // 'token' => $token,
        //     ],
        // ]);

    } catch (JWTException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized',
            'data' => null,
        ], 401);
    }
}



// public function updateUser(Request $request)
// {
//     try {
//         if (! $user = JWTAuth::parseToken()->authenticate()) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'User not found.',
//                 'data' => collect(),
//             ], 404);
//         }

//         // Validate request
//         $validated = $request->validate([
//             'name'        => 'nullable|string|max:255',
//             'lastname'    => 'nullable|string|max:255',
//             'designation' => 'nullable|string|max:255',
//             'email'       => 'nullable|email|unique:users,email,' . $user->id,
//             'mobile'      => 'nullable|string|max:20',
//             'gender'      => 'nullable|in:male,female,other',
//             'dob'         => 'nullable|date',
//             // company fields
//             'company_name'     => 'nullable|string|max:255',
//             'company_email'    => 'nullable|email|max:255',
//             'company_phone'    => 'nullable|string|max:20',
//             'company_website'  => 'nullable|string|max:200',
//             'about'            => 'nullable|string',
//             'tags'             => 'nullable|string',
//             // address
//             'street'      => 'nullable|string|max:255',
//             'city'        => 'nullable|string|max:255',
//             'state'       => 'nullable|string|max:255',
//             'country'     => 'nullable|string|max:255',
//             'zipcode'     => 'nullable|string|max:20',
//         ]);

//         // --- Update user basic info ---
//         $user->update($validated);
        

//         // --- Update or create company record ---
//         if ($request->hasAny(['company_name', 'company_email', 'company_phone', 'company_website'])) {
//             $companyData = [
//                 'name'        => $request->company_name,
//                 'email'       => $request->company_email,
//                 'phone'       => $request->company_phone,
//                 'website'     => $request->company_website,
//             ];

//             // Attach user_id so FK doesn’t fail
//             $company = \App\Models\Company::updateOrCreate(
//                 ['user_id' => $user->id], // condition
//                 $companyData              // values
//             );
//         }

//         // Reload relations
//         $user->load(['photo', 'company']);

//         return response()->json([
//             'success' => true,
//             'message' => 'Profile updated successfully.',
//             // 'data' => [
//             //     'user' => $user,
//             //     'company' => $user->company ?? null,
//             // ]
//         ]);

//     } catch (JWTException $e) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Unauthorized',
//             'data' => null,
//         ], 401);
//     }
// }
public function updateUser(Request $request)
{
    try {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
                'data' => collect(),
            ], 404);
        }

        // Custom validation with error handling
        $validator = Validator::make($request->all(), [
            'name'        => 'nullable|string|max:255',
            'lastname'    => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'email'       => 'nullable|email|unique:users,email,' . $user->id,
            'mobile'      => 'nullable|string|max:20',
            'gender'      => 'nullable|in:male,female,other',
            'dob'         => 'nullable|date',
            // company fields
            'company_name'     => 'nullable|string|max:255',
            'company_email'    => 'nullable|email|max:255',
            'company_phone'    => 'nullable|string|max:20',
            'company_website'  => 'nullable|string|max:200',
            'about'            => 'nullable|string',
            'tags'             => 'nullable|string',
            // address
            'street'      => 'nullable|string|max:255',
            'city'        => 'nullable|string|max:255',
            'state'       => 'nullable|string|max:255',
            'country'     => 'nullable|string|max:255',
            'zipcode'     => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        // --- Update user basic info ---
        $user->update($validated);

        // --- Update or create company record ---
        if ($request->hasAny(['company_name', 'company_email', 'company_phone', 'company_website'])) {
            $companyData = [
                'name'    => $request->company_name,
                'email'   => $request->company_email,
                'phone'   => $request->company_phone,
                'website' => $request->company_website,
            ];

            \App\Models\Company::updateOrCreate(
                ['user_id' => $user->id], // condition
                $companyData              // values
            );
        }

        // Reload relations
        $user->load(['photo', 'company']);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'data'    => $user,
        ]);

    } catch (JWTException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized',
            'data'    => null,
        ], 401);
    }
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
public function getExhibitor($exhibitorId)
{
    try {
        
        if (! $requester = JWTAuth::parseToken()->authenticate()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
                // 'data'    => collect(),
            ], 401);
        }

     
        $exhibitor = User::find($exhibitorId);

        if (! $exhibitor) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load exhibitor details!',
                'data'    => collect(),
            ], 404);
        }

     
        $response = [
            'name'     => trim(($exhibitor->name ?? '') . ' ' . ($exhibitor->lastname ?? '')),
            'word_no'  => $exhibitor->designation ?? '',
            'location' => trim(($exhibitor->city ?? '') . ', ' . ($exhibitor->country ?? '')),
            'email'    => $exhibitor->email ?? '',
            'phone'    => $exhibitor->mobile ?? '',
            'website'  => $exhibitor->website_url ?? '',

            'social_links' => [
                ['name' => 'linkedin', 'url' => $exhibitor->linkedin_url ?? ''],
                ['name' => 'facebook', 'url' => $speaker->facebook_url ?? ''],
                ['name' => 'instagram', 'url' => $speaker->instagram_url ?? ''],
                ['name' => 'twitter', 'url' => $speaker->twitter_url ?? ''],
                ['name' => 'github', 'url' => $speaker->github_url ?? ''],

            ],

            'bio' => $exhibitor->bio ?? '',
        ];

        return response()->json([
            'success' => true,
            // 'message' => 'Exhibitor details fetched successfully',
            'data'    => $response,
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Something went wrong: ' . $e->getMessage(),
            'data'    => collect(),
        ], 500);
    }
}
public function getSpeaker()
{
    try {
       
        if (! $requester = JWTAuth::parseToken()->authenticate()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
                'data'    => collect(),
            ], 401);
        }

     
        $speakers = User::whereHas("roles", function ($q) {
            $q->where("name", "Speaker");
        })->get();

        if ($speakers->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No speakers found!',
                'data'    => collect(),
            ], 404);
        }

        
        $response = $speakers->map(function ($speaker) {
            return [
                'name'     => trim(($speaker->name ?? '') . ' ' . ($speaker->lastname ?? '')),
                'word_no'  => $speaker->designation ?? '',
                'location' => trim(($speaker->place ?? '') . ', ' . ($speaker->street ?? '') . ', ' . ($speaker->city ?? '') . '- ' . ($speaker->zipcode ?? '') . ', ' . ($speaker->state ?? '') . ', ' . ($speaker->country ?? '')),
                'email'    => $speaker->email ?? '',
                'phone'    => $speaker->mobile ?? '',
                'website'  => $speaker->website_url ?? '',
                // 'avatar'   => $speaker->avatar ?? '',

                'social_links' => [
                    ['name' => 'facebook', 'url' => $speaker->facebook_url ?? ''],
                    ['name' => 'instagram', 'url' => $speaker->instagram_url ?? ''],
                    ['name' => 'linkedin', 'url' => $speaker->linkedin_url ?? ''],
                    ['name' => 'twitter', 'url' => $speaker->twitter_url ?? ''],
                    ['name' => 'github', 'url' => $speaker->github_url ?? ''],
                ],

                'bio'            => $speaker->bio ?? '',
                // 'uploaded_files' => method_exists($speaker, 'files')
                //     ? $speaker->files->map(function ($file) {
                //         return [
                //             'name' => $file->name,
                //             'url'  => $file->url,
                //         ];
                //     })
                //     : [], 
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $response,
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Something went wrong: ' . $e->getMessage(),
            'data'    => collect(),
        ], 500);
    }
}
public function getTags()
{
    try {
     
        $tags = \App\Models\User::pluck('tags')
            ->filter() 
            ->flatMap(function ($tagString) {
                // handle JSON or comma-separated tags
                if (is_array($tagString)) {
                    return $tagString;
                }
                return explode(',', $tagString);
            })
            ->map(fn($tag) => trim($tag)) // clean spaces
            ->filter() // remove empty after trim
            ->unique()
            ->values();

        return response()->json([
            'success' => true,
            'data'    => $tags,
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Something went wrong: ' . $e->getMessage(),
            'data'    => [],
        ], 500);
    }
}




}
