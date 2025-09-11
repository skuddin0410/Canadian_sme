<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Drive;
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
            'name' => $user->full_name ?? '',
            'company' => $user->company ?? '',
            'email'     => $user->email ?? '',
            'phone'    => $user->mobile ?? '',
            'imageUrl' => !empty($user->photo) ? $user->photo->file_path : asset('images/default.png'),
            'designation'=> $user->designation,
            'bio'       => $user->bio,
            'tag'      => !empty($user->tags) ? explode(',',$user->tags) : [],
            'my_qr_code' => asset($user->qr_code),
            'company_name'   => !empty($user->usercompany) ? $user->usercompany->name : '', 
            'company_email'   => !empty($user->usercompany) ? $user->usercompany->email : '', 
            'company_phone'   => !empty($user->usercompany) ? $user->usercompany->phone : '', 
            'company_website'=>  !empty($user->usercompany) ? $user->usercompany->website : '', 
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
        $user->tags =  !empty($request->tags) ? $request->tags : '';
        $user->mobile = $request->phone ?? '';
        $user->bio = $request->bio ?? '';
        $user->website_url = $request->company_website ?? '';
        $user->save();
        qrCode($user->id);
        

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

     
        $exhibitor = User::with([
            'photo',
            'usercompany',
            'usercompany.files',
        ])->find($exhibitorId);

        if (! $exhibitor) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load exhibitor details!',
                'data'    => collect(),
            ], 404);
        }

        $company = $exhibitor->usercompany;          // may be null
        $booth   = $company?->booth;      // first booth (Collection-safe)

        $response = [
            'name'     => $exhibitor->full_name ?? '',
            'word_no'  => $booth ?? '-',
            'avatar'   => $exhibitor->photo?->file_path ?? asset('images/default.png'),
            'location' => $booth ?? '-',
            'email'    => $exhibitor->email ?? '',
            'phone'    => $exhibitor->mobile ?? '',
            'website'  => $exhibitor->website_url ?? '',
            'social_links' => [
                ['name' => 'linkedin',  'url' => $exhibitor->linkedin_url  ?? ''],
                ['name' => 'facebook',  'url' => $exhibitor->facebook_url  ?? ''],
                ['name' => 'instagram', 'url' => $exhibitor->instagram_url ?? ''],
                ['name' => 'twitter',   'url' => $exhibitor->twitter_url   ?? ''],
                ['name' => 'github',    'url' => $exhibitor->github_url    ?? ''],
            ],
            'bio'         => $exhibitor->bio ?? '',
            'my_qr_code'  => $exhibitor->qr_code ? asset($exhibitor->qr_code) : '',
            'uploaded_files' => ($company?->files ?? collect())
                ->map(fn ($file) => [
                    'name' => $file->file_name,
                    'url'  => $file->file_path,
                ])
                ->values()
                ->all(),
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


public function getSpeaker(Request $request)
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
                'message' => 'No speakers found!'
            ], 404);
        }

        
       $response = $speakers->map(function ($speaker) {
            return [
                'id'     => $speaker->id,
                'name'     => $speaker->full_name,
                'company_name'  => $speaker->company ?? '',
                'role'  => $speaker->designation ?? '',
                'image_url'   => !empty($speaker->photo) ? $speaker->photo->file_path  : asset('images/default.png'),
                'roles' => groups($speaker)
            ];
        });

        return response()->json($response);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Fail to load data! ',
        ], 500);
    }
}

public function getSpeakerById(Request $request){
   try {
       
        if (! $requester = JWTAuth::parseToken()->authenticate()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
                'data'    => collect(),
            ], 401);
        }

        $speaker = User::with('roles','photo')->whereHas('roles', function ($q) {
         $q->where('name', 'Speaker');
        })->where('id',$request->id)->first();
    
        if (empty($speaker)) {
            return response()->json([
                'success' => false,
                'message' => 'No speakers found!'
            ], 404);
        }

        $response =  [
            'id'     => $speaker->id,
            'name'     => $speaker->full_name,
            'company_name'  => $speaker->company ?? '',
            "company_details"=>"Microsoft Corporation is an American multinational technology corporation which produces computer software",
            "bio"=> $speaker->bio ?? '',
            'role'  => $speaker->designation ?? '',
            'image_url'   => !empty($speaker->photo) ? $speaker->photo->file_path  : asset('images/default.png'),
            'roles' => groups($speaker),
            "contact_details"=>[
               "email"=> $speaker->email,
               "phone"=> $speaker->mobile ?? '',
                "social_media_links"=>[
                  "linkedin"=> $speaker->linkedin_url,
                  "facebook"=> $speaker->facebook_url,
                  "instagram"=> $speaker->instagram_url,
                  "twitter"=> $speaker->twitter_url

                ]

            ]
        ];
    
        return response()->json([$response]);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Fail to load data! ',
        ], 500);
    }
}

public function getAttendee(Request $request)
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
         $q->where('name', 'Attendee');
        })->get();

        if ($speakers->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No speakers found!'
            ], 404);
        }

        
       $response = $speakers->map(function ($speaker) {
            return [
                'id'     => $speaker->id,
                'name'     => $speaker->full_name,
                'company_name'  => $speaker->company ?? '',
                'role'  => $speaker->designation ?? '',
                'image_url'   => !empty($speaker->photo) ? $speaker->photo->file_path  : asset('images/default.png'),
                'roles' => groups($speaker)
            ];
        });

        return response()->json($response);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Fail to load data! ',
        ], 500);
    }
}

public function getAttendeeById(Request $request){
   try {
       
        if (! $requester = JWTAuth::parseToken()->authenticate()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
                'data'    => collect(),
            ], 401);
        }

        $speaker = User::with('roles','photo')->whereHas('roles', function ($q) {
         $q->where('name', 'Attendee');
        })->where('id',$request->id)->first();
    
        if (empty($speaker)) {
            return response()->json([
                'success' => false,
                'message' => 'No speakers found!'
            ], 404);
        }

        $response =  [
            'id'     => $speaker->id,
            'name'     => $speaker->full_name,
            'company_name'  => $speaker->company ?? '',
            "company_details"=>"Microsoft Corporation is an American multinational technology corporation which produces computer software",
            "bio"=> $speaker->bio ?? '',
            'role'  => $speaker->designation ?? '',
            'image_url'   => !empty($speaker->photo) ? $speaker->photo->file_path  : asset('images/default.png'),
            'roles' => groups($speaker),
            "contact_details"=>[
               "email"=> $speaker->email,
               "phone"=> $speaker->mobile ?? '',
                "social_media_links"=>[
                  "linkedin"=> $speaker->linkedin_url,
                  "facebook"=> $speaker->facebook_url,
                  "instagram"=> $speaker->instagram_url,
                  "twitter"=> $speaker->twitter_url

                ]

            ]
        ];
    
        return response()->json([$response]);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Fail to load data! ',
        ], 500);
    }
}

public function getTags()
{
    try {
        $tags = Category::whereIn('type',['tags','connections'])->pluck('name') 
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

public function getAllExhibitor(Request $request){
    
    try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

          $exhibitors = Company::with(['contentIconFile'])->where('is_sponsor', 0)->orderBy('id', 'DESC')->get();

            if ($exhibitors->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No exhibitors found!'
                ], 404);
            }

            $response = $exhibitors->map(function ($exhibitor) {
                return [
                    'id'          => $exhibitor->id,
                    'name'        => $exhibitor->name,
                    'image_url'   => $exhibitor->contentIconFile ? $exhibitor->contentIconFile->file_path : asset('images/default.png'),
                    'location'    => $exhibitor->booth ?? '',
                ];
            });

            return response()->json($response);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Fail to get data! ',
            ], 500);
        }

    }


    public function getAllSponsor(Request $request){
    try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }
          $sponsors = Company::with(['contentIconFile'])->where('is_sponsor', 1)->orderBy('id', 'DESC')->get();

            if ($sponsors->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No sponsors found!'
                ], 404);
            }
            $response = $sponsors->map(function ($sponsor) {
                return [
                    'id'          => $sponsor->id,
                    'name'        => $sponsor->name,
                    'image_url'   => $sponsor->contentIconFile ? $sponsor->contentIconFile->file_path : asset('images/default.png'),
                    'level'    => ucfirst($sponsor->type) ?? '',
                ];
            });
            return response()->json($response);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Fail to get data! ',
            ], 500);
        }

    }


    public function getSponsor(Request $request){
    try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

                $sponsor = Company::with(['contentIconFile', 'Docs'])->where('id', $request->id)->where('is_sponsor', 1)->first();

                if (!$sponsor) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No sponsors found!'
                    ], 404);
                }

                $data = [
                    "id"=> $sponsor->id ?? '',
                    "name" => $sponsor->name ?? '',
                    "avatar" => $sponsor->contentIconFile ? $sponsor->contentIconFile->file_path : asset('images/default.png'),
                    "word_no" => $sponsor->booth ?? '',
                    "location" => $sponsor->booth ?? '',
                    "email" => $sponsor->email ?? '',
                    "phone" => $sponsor->phone ?? '',
                    "website" => $sponsor->website ?? '',
                    "social_links" => [
                        [
                            "name" => "facebook",
                            "url" => $sponsor->facebook ?? ''
                        ],
                        [
                            "name" => "instagram",
                            "url" => $sponsor->instagram ?? ''
                        ],
                        [
                            "name" => "twitter",
                            "url" => $sponsor->twitter ?? ''
                        ],
                        [
                            "name" => "linkedin",
                            "url" => $sponsor->linkedin ?? ''
                        ],
                    ],
                    "bio" => $sponsor->description,
                    "uploaded_files" => $sponsor->Docs->map(fn ($sp) => [
                           "name"=> $sp->file_name,
                           "url"=> $sp->file_path
                    ])->values(),
                ];


                return response()->json($data);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Fail to get data! ',
            ], 500);
        }

    }


}
