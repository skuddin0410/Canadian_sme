<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Drive;
use App\Models\Wallet;
use App\Models\Company;
use App\Models\Setting;
use App\Models\Category;
use App\Models\SessionDate;
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

   
    public function getUser() {
    try {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json([
                'success' => false,
                'message' => 'Fail to load profile',
                'data' => collect(),
            ], 404);
        }

    
        $token = request()->bearerToken() ?? JWTAuth::refresh();
        $roles = $user->getRoleNames();
        $address = [
            'street'   => $user->address ?? null,
            'city'     => $user->city ?? null,
            'state'    => $user->state ?? null,
            'country'  => $user->country ?? null,
            'zipcode'  => $user->zipcode ?? null,
        ];
        $user->load(['photo', 'usercompany']);
        return response()->json([
            'success' => true,
            'message' => 'successful',
            'id'        => $user->id,
            'first_name'      => $user->name ?? '',
            'lastname'  => $user->lastname ?? '',
            'name' => $user->full_name,
            'email'     => $user->email ?? '',
            'phone'    => $user->mobile ?? '',
            'imageUrl' => !empty($user->photo) ? $user->photo->file_path : asset('images/default.png'),
            'designation'=> $user->designation,
            'bio'       => $user->about,
            'tags'      => !empty($user->tags) ? explode(',',$user->tags) : '',
            'my_qr_code' => asset($user->qr_code),
            'company_name'   => !empty($user->usercompany) ? $user->usercompany->name : '', 
            'company_email'   => !empty($user->usercompany) ? $user->usercompany->email : '', 
            'company_phone'   => !empty($user->usercompany) ? $user->usercompany->phone : '', 
            'image_url' => !empty($user->photo) ? $user->photo->file_path : asset('images/default.png') ,
            'roles'     => $user->getRoleNames(),
            'company_about_page'  => config('app.url').'app/page/about',
            'company_location_page'    => config('app.url').'app/page/location',
            'company_privacy_policy_page' => config('app.url').'app/page/privacy',
            'company_terms_of_service_page' => config('app.url').'app/page/terms',
        ]);


    

    } catch (JWTException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized',
            'data' => null,
        ], 401);
    }
}




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
            'first_name'        => 'required|string|max:255',
            'last_name'    => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email,' . $user->id,
            'phone'      => 'required|string|max:20',
            'bio'    => 'nullable|max:300',
            'tags'   => 'nullable|string',
            // company fields
            'company_name'     => 'required|string|max:255',
            'company_website'     => 'nullable|string|max:255'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile.',
                'errors'  => $validator->errors()->first(),
            ], 422);
        }
        

        $user->name = $request->first_name ?? '';
        $user->lastname = $request->last_name ?? '';
        $user->email = $request->email ?? '';
        $user->company = $request->company_name?? '';
        $user->designation = $request->designation ?? '';
        $user->tags =  !empty($request->tags) ? implode(',',$request->tags) : '';
        $user->mobile = $request->phone ?? '';
        $user->bio = $request->bio ?? '';
        $user->save();
        qrCode($user->id);
        // --- Update or create company record ---
        if ($request->hasAny(['company_name', 'email'])) {
            $companyData = [
                'name'    => $request->company_name ?? '',
                'email'   => $request->email ?? '',
                'phone'   => $request->phone ?? '',
                'website' => $request->company_website ?? '',
            ];

            \App\Models\Company::updateOrCreate(
                ['user_id' => $user->id], // condition
                $companyData              // values
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
        ]);

    } catch (JWTException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Fail to update profile!',
            'data'    => null,
        ], 401);
    }
}


public function updateUserImage(Request $request)
{
    try {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

       
        $validator = Validator::make($request->all(), [
         'image' => 'required|image|mimes:jpeg,png,jpg|max:2048', 
        ]);
        

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }
        

       if ($request->hasFile('image')) {
          $this->imageUpload($request->file("image"),"users",$user->id,'users','photo',$user->id);
        }

        return response()->json([
            'success'   => true,
            'message'   => 'Profile image updated successfully.',
        ]);

    } catch (JWTException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Fail to upload profile image!',
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
            ], 401);
        }

     
        $exhibitor = User::with('photo','usercompany','usercompany.booths','usercompany.files')->find($exhibitorId);

        if (! $exhibitor) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load exhibitor details!',
                'data'    => collect(),
            ], 404);
        }
        $response = [
            'name'     => $exhibitor->full_name ?? '',
            'word_no'  => $exhibitor?->usercompany?->booths[0]?->booth_number ?? '',
            'avatar'=> !empty($exhibitor->photo) ? $exhibitor->photo->file_path : '',
            'location' => $exhibitor?->usercompany?->booths[0]?->location_preferences ?? '',
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
            'my_qr_code' => asset($exhibitor->qr_code) ?? '',
            'uploaded_files' => $exhibitor->usercompany->files->map(function ($file) {
                    return [
                        'name' => $file->file_name, 
                        'url'  => $file->file_path,
                    ];
             })->toArray() ?? [],

        ];
        
        
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


public function uploadExhibitorFiles(Request $request, $exhibitorId)
{
    try {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        $exhibitor = User::with('usercompany','usercompany.files')->find($exhibitorId);
      
        if (! $exhibitor) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load exhibitor details!',
                'data'    => collect(),
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'file' => 'required|image|mimes:jpg,jpeg,png',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors()->first(),
            ], 422);
        }

        if ($request->file("file")) {
            $fileRecord = $this->imageUpload(
                $request->file("file"),
                'companies',
                $exhibitor->usercompany->id,
                'companies',
                'files'
            );
         
        }
    
        $exhibitor = User::where('id', $exhibitor->id)->with(['files' => function ($query) {
            $query->latest()->take(1); 
         }])->first();
 
        return response()->json([
          
            'message'   => 'File uploaded successfully.',
            'file_id'   =>  !empty($exhibitor->files) ? $exhibitor->files[0]->id : null,
            'image_url' => !empty($exhibitor->files) ? $exhibitor->files[0]->file_path : asset('images/default.png'),
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 500);
    }
}

public function deleteExhibitorFiles($exhibitorId, $fileId)
{
    try {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        $exhibitor = User::find($exhibitorId);

        if (! $exhibitor) {
            return response()->json([
                'success' => false,
                'message' => 'Exhibitor not found!',
            ], 404);
        }

        // Fetch file record from Drive table
        $file = Drive::where('id', $fileId)
            ->where('table_id', $exhibitor->id)
            ->where('table_type', 'users')
            ->where('file_type', 'photo')
            ->first();

        if (! $file) {
            return response()->json([
                'success' => false,
                'message' => 'File not found!',
            ], 404);
        }

        // Delete from storage
        if (\Storage::disk('public')->exists($file->file_path)) {
            \Storage::disk('public')->delete($file->file_path);
        }

        // Delete record from DB
        $file->delete();

        return response()->json([
            // 'success' => true,
            'message' => 'Exhibitor file deleted successfully.',
            'file_id' => $fileId,
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
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

     
        $speakers = User::with('roles','photo')->whereHas('roles', function ($q) {
         $q->where('name', 'Speaker');
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
                'name'     => $speaker->full_name,
                'company'  => $speaker->company ?? '',
                'designation'  => $speaker->company ?? '',
                'location' => '',
                'email'    => $speaker->email ?? '',
                'phone'    => $speaker->mobile ?? '',
                'website'  => $speaker->website_url ?? '',
                'avatar'   => !empty($speaker->photo) ? $speaker->photo->file_path  : '',
                'tags' => !empty($speaker->tags) ? explode(',',$speaker->tags) : '',
                'groups' => groups($speaker),

                'social_links' => [
                    ['name' => 'facebook', 'url' => $speaker->facebook_url ?? ''],
                    ['name' => 'instagram', 'url' => $speaker->instagram_url ?? ''],
                    ['name' => 'linkedin', 'url' => $speaker->linkedin_url ?? ''],
                    ['name' => 'twitter', 'url' => $speaker->twitter_url ?? ''],
                    ['name' => 'github', 'url' => $speaker->github_url ?? ''],
                ],

                'bio'   => $speaker->bio ?? '',
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
        $tags = Category::pluck('name') 
            ->filter() 
            ->flatMap(function ($tagString) {
                if (is_array($tagString)) {
                    return $tagString;
                }

                // try decode JSON first
                $jsonDecoded = json_decode($tagString, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($jsonDecoded)) {
                    return $jsonDecoded;
                }

                // fallback: split by comma
                return explode(',', $tagString);
            })
            ->map(fn($tag) => trim($tag)) // clean spaces
            ->filter() // remove empty after trim
            ->unique()
            ->values();

        return response()->json($tags,200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Something went wrong: ' . $e->getMessage(),
            'data'    => [],
        ], 500);
    }
}
public function checkSession(Request $request)
{
    $user = $request->user(); // or Auth::user()

    if (!$user = JWTAuth::parseToken()->authenticate()) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized'
        ], 401);
    }

    $session = SessionDate::where('user_id', $user->id)->first();

    if (!$session) {
        return response()->json([
            'success' => false,
            'message' => 'Session not found'
        ], 401);
    }

  
    if (Carbon::now()->greaterThan($session->expires_at)) {
        return response()->json([
            'success' => false,
            'message' => 'Session expired'
        ], 401);
    }

    return response()->json([
        // 'success' => true,
        'message' => 'Session validated',
        // 'expires_at' => $session->expires_at->toDateTimeString()
    ]);
}




}
