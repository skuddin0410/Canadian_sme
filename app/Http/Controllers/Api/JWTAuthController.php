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
use App\Models\Speaker;

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
        $user->load(['photo', 'usercompany']);
        return response()->json([
            'success' => true,
            'message' => 'successful',
            'id'        => $user->id,
            'name' => $user->full_name ?? '',
            'first_name' => $user->name ?? '',
            'last_name' => $user->lastname ?? '',
            'email'     => $user->email ?? '',
            'phone'    => $user->mobile ?? '',
            'imageUrl' => !empty($user->photo) ? $user->photo->file_path : asset('images/default.png'),
            'company_about_page'  => config('app.url').'app/page/about',
            'company_location_page'    => config('app.url').'app/page/location',
            'company_privacy_policy_page' => config('app.url').'app/page/privacy',
            'company_terms_of_service_page' => config('app.url').'app/page/terms',
            'designation'=> $user->designation,
            'bio'       => $user->bio,
            'tag'      => !empty($user->tags) ? explode(',',$user->tags) : [],
            'my_qr_code' => asset($user->qr_code),

            'company_name'   => !empty($user->usercompany) ? $user->usercompany->name : $user->company, 
            'company_email'   => !empty($user->usercompany) ? $user->usercompany->email : $user->email, 
            'company_phone'   => !empty($user->usercompany) ? $user->usercompany->phone : $user->mobile, 
            'company_website'=>  !empty($user->usercompany) ? $user->usercompany->website : $user->website_url, 
            'roles'     => groups($user),
            'is_speaker_id'   => (int) ($user->access_speaker_ids ?? 0),
            'is_exhibitor_id' => (int) ($user->access_exhibitor_ids ?? 0),
            'is_sponsor_id'   => (int) ($user->access_sponsor_ids ?? 0),
            
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
            'bio'    => 'required|max:300',
            'tags'   => 'required|string',
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
        
        if(!empty($user->access_exhibitor_ids)){
          Company::where('id', $user->access_exhibitor_ids)->update(["name"=>$request->company_name, "website"=>$request->company_website]);
        }

        if(!empty($user->access_sponsor_ids)){
          Company::where('id', $user->access_sponsor_ids)->update(["name"=>$request->company_name, "website"=>$request->company_website]);
        }

        if(!empty($user->access_speaker_ids)){
          Speaker::where('id', $user->access_speaker_ids)->update(
            [
                 "name"=>$request->first_name, 
                 "lastname"=>$request->last_name,
                 "email"=>$request->email,
                 "company"=>$request->company_name ?? '',
                 "designation"=>$request->designation ?? '',
                 "mobile"=>$request->phone ?? '',
                 "bio"=>$request->bio ?? '',
                 "website_url"=>$request->company_website ?? ''
            ]);
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
         'image' => 'required|image|mimes:jpeg,png,jpg|max:5048', 
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
            
            if(!empty($user->access_speaker_ids)){
              $this->imageUpload($request->file("image"),"speakers",$user->access_speaker_ids,'speakers','photo',$user->access_speaker_ids);
            }
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

     
        $exhibitor = Company::with([
            'contentIconFile',
            'quickLinkIconFile',
            'Docs',
        ])->find($exhibitorId);

        if (! $exhibitor) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load exhibitor details!',
                'data'    => collect(),
            ], 404);
        }

        $response = [
            'name'     => $exhibitor->name ?? '',
            'word_no'  => $exhibitor->booth ?? '-',
            'avatar'   => $exhibitor->contentIconFile?->file_path ?? asset('images/default.png'),
            'banner'   => $exhibitor->quickLinkIconFile?->file_path ?? asset('images/eventify-banner.jpg'),
            'location' => $exhibitor->booth ?? '-',
            'email'    => $exhibitor->email ?? '',
            'phone'    => $exhibitor->phone ?? '',
            'website'  => $exhibitor->website ?? '',
            'social_links' => [
                ['name' => 'linkedin',  'url' => $exhibitor->linkedin  ?? ''],
                ['name' => 'facebook',  'url' => $exhibitor->facebook  ?? ''],
                ['name' => 'instagram', 'url' => $exhibitor->instagram ?? ''],
                ['name' => 'twitter',   'url' => $exhibitor->twitter   ?? '']
            ],
            'bio'         => $exhibitor->description ?? '',
            "company_details"=>$sponsor->description ?? '',
            "uploaded_files" => $exhibitor->Docs->map(fn ($sp) => [
                           "fileID"=>$sp->id,
                           "name"=> $sp->file_name,
                           "url"=> $sp->file_path
                ])->values()
        ];
        
        
        return response()->json($response, 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Something went wrong: ' . $e->getMessage(),
            'data'    => collect(),
        ], 500);
    }
}


public function uploadExhibitorFiles(Request $request, $detailsID)
{
    try {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        //$exhibitor = Company::find($exhibitorId);

        $validator = Validator::make($request->all(), [
            'file' => 'required|image|mimes:jpg,jpeg,png',
            'type' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors()->first(),
            ], 422);
        }
        
        if($request->type == "exhibitor" || $request->type == "sponsor") {  

            if ($request->file("file")) {
                $fileRecord = $this->imageUpload(
                    $request->file("file"),
                    'companies',
                    $detailsID,
                    'companies',
                    'private_docs'
                );
             
            }
        
            $exhibitor = Company::where('id', $detailsID)->with(['Docs' => function ($query) {
                $query->latest()->take(1); 
             }])->first();
     
            return response()->json([
                'message'   => 'File uploaded successfully.',
                'file_id'   =>  !empty($exhibitor->Docs) ? $exhibitor->Docs[0]->id : null,
                'image_url' => !empty($exhibitor->Docs) ? $exhibitor->Docs[0]->file_path : asset('images/default.png'),
                "type"=>$request->type
            ]);

        }else{
            
            if ($request->file("file")) {
                $fileRecord = $this->imageUpload(
                    $request->file("file"),
                    'users',
                    $detailsID,
                    'users',
                    'private_docs'
                );
            }

            $user = User::where('id', $detailsID)->with(['privateDocs' => function ($query) {
                $query->latest()->take(1); 
             }])->first();

            return response()->json([
                'message'   => 'File uploaded successfully.',
                'file_id'   =>  !empty($user->privateDocs) ? $user->privateDocs[0]->id : null,
                'image_url' => !empty($user->privateDocs) ? $user->privateDocs[0]->file_path : asset('images/default.png'),
                "type"=>$request->type
            ]);

        }
   

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 500);
    }
}

public function deleteExhibitorFiles(Request $request, $exhibitorId)
{
    try {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        if($request->type == 'exhibitor' || $request->type == 'sponsor'){

            $exhibitor = Company::find($exhibitorId);
           
            if (! $exhibitor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Exhibitor not found!',
                ], 404);
            }

            // Fetch file record from Drive table
            $file = Drive::where('id', $request->fileId)
                ->where('table_id', $exhibitor->id)
                ->where('table_type', 'companies')
                ->where('file_type', 'private_docs')
                ->first();

            if (! $file) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found!',
                ], 404);
            }

            // Delete record from DB
             $file->delete();
        }

        if($request->type == 'connection'){
            $user = User::find($exhibitorId);
            $file = Drive::where('id', $request->fileId)
            ->where('table_id', $user->id)
            ->where('table_type', 'users')
            ->where('file_type', 'private_docs')
            ->first();

            if (! $file) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found!',
                ], 404);
            }
            $file->delete();
        }

        return response()->json([
            'message' => 'File deleted successfully.',
            'file_id' => $request->fileId,
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

     
        /*$speakers = User::with('roles','photo')->whereHas('roles', function ($q) {
         $q->where('name', 'Speaker');
        })->whereNotNull('access_speaker_ids')->orderBy('id', 'DESC')->get();*/

        $speakers = Speaker::with('photo')->orderBy('id', 'DESC')->get();

        if ($speakers->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No speakers found!'
            ], 404);
        }

        
       $response = $speakers->map(function ($speaker) {
            return [
                'id'     => $speaker->id,
                'access_speaker_ids'=>$speaker->access_speaker_ids,
                'name'     => $speaker->full_name,
                'company_name'  => $speaker->company ?? '',
                'role'  => $speaker->designation ?? '',
                'image_url'   => !empty($speaker->photo) ? $speaker->photo->file_path  : asset('images/default.png'),
                'roles' => speakerGroups($speaker)
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

        /*$speaker = User::with('roles','photo')->whereHas('roles', function ($q) {
            $q->where('name', 'Speaker');
        })->where('id', $request->id)->first();*/
        $speaker = Speaker::with('photo')->where('id', $request->id)->first();

        if (empty($speaker)) {
            return response()->json([
                'success' => false,
                'message' => 'No speakers found!'
            ], 404);
        }

        // Prepare contact details
        $contactDetails = [
            "email" => $speaker->email ?? '',
            "phone" => $speaker->mobile ?? ''
        ];

        // Only add social_media_links if any link exists
        $socialLinks = array_filter([
            "linkedin"  => $speaker->linkedin_url,
            "facebook"  => $speaker->facebook_url,
            "instagram" => $speaker->instagram_url,
            "twitter"   => $speaker->twitter_url
        ]);

        if (!empty($socialLinks)) {
            $contactDetails['social_media_links'] = $socialLinks;
        }

        $response = [
            'id'             => $speaker->id,
            'name'           => $speaker->full_name,
            'company_name'   => $speaker->company ?? '',
            'company_details'=> $speaker->bio ?? '',
            'bio'            => $speaker->bio ?? '',
            'role'           => $speaker->designation ?? '',
            'image_url'      => !empty($speaker->photo) ? $speaker->photo->file_path : asset('images/default.png'),
            'roles'          => speakerGroups($speaker),
            'company_website'=> $speaker->website_url ?? '',
            'contact_details'=> $contactDetails
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
        })->whereNotNull('users.name')->orderBy('id', 'DESC')->get();

        if ($speakers->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No Attendee found!'
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
        })->where('id', $request->id)->first();

        if (empty($speaker)) {
            return response()->json([
                'success' => false,
                'message' => 'No attendees found!'
            ], 404);
        }

        // Prepare contact details
        $contactDetails = [
            "email" => $speaker->email,
            "phone" => $speaker->mobile ?? ''
        ];

        // Only add social_media_links if any link exists
        $socialLinks = array_filter([
            "linkedin"  => $speaker->linkedin_url,
            "facebook"  => $speaker->facebook_url,
            "instagram" => $speaker->instagram_url,
            "twitter"   => $speaker->twitter_url
        ]);

        if (!empty($socialLinks)) {
            $contactDetails['social_media_links'] = $socialLinks;
        }

        $response = [
            'id'             => $speaker->id,
            'name'           => $speaker->full_name,
            'company_name'   => $speaker->company ?? '',
            'company_details'=> $speaker->bio ?? '',
            'bio'            => $speaker->bio ?? '',
            'role'           => $speaker->designation ?? '',
            'image_url'      => !empty($speaker->photo) ? $speaker->photo->file_path : asset('images/default.png'),
            'roles'          => groups($speaker),
            'contact_details'=> $contactDetails
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
    
    if($user->is_approve == 0){
       return response()->json([
        'success'    => false,
        'message'    => 'Your account is inactive. Please contact support for assistance.',
       ],401); 
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
            'message' => 'Session expired!'
        ], 401);
    }

    return response()->json([
        'message' => 'Session validated',
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
          $sponsors = Company::with(['logo'])->where('is_sponsor', 1)->orderBy('id', 'DESC')->get();

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
                    'image_url'   => $sponsor->logo ? $sponsor->logo->file_path : asset('images/default.png'),
                    'level'    =>  $sponsor->type ? ucfirst(str_replace('-', ' ', $sponsor->type)) : '',
                    'color_code'    => $sponsor->type ?  typeColor($sponsor->type) : '',
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

                $sponsor = Company::with(['logo', 'Docs','banner'])->where('id', $request->id)->where('is_sponsor', 1)->first();

                if (!$sponsor) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No sponsors found!'
                    ], 404);
                }

                $data = [
                    "id"=> $sponsor->id ?? '',
                    "name" => $sponsor->name ?? '',
                    "avatar" => $sponsor->logo ? $sponsor->logo->file_path : asset('images/default.png'),
                    'banner'   => $sponsor->banner?->file_path ?? asset('images/eventify-banner.jpg'),
                    "word_no" => $sponsor->booth ?? '',
                    "location" => $sponsor->booth ?? '',
                    "email" => $sponsor->email ?? '',
                    "phone" => $sponsor->phone ?? '',
                    "website" => $sponsor->website ?? '',
                    'level'    =>  $sponsor->type ? ucfirst(str_replace('-', ' ', $sponsor->type)) : '',
                    'color_code'    => $sponsor->type ?  typeColor($sponsor->type) : '',
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
                    "company_details"=>$sponsor->description ?? '',
                    "uploaded_files" => $sponsor->Docs->map(fn ($sp) => [
                           "fileID"=> $sp->id,
                           "name"=> $sp->file_name,
                           "url"=> $sp->file_path,
                           "fileID"=>$sp->id
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

    public function deleteAccount(){
      try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }
            User::where('id',$user->id)->update(['is_approve'=>0]);
            return response()->json([
                'success' => true,
                'message' => 'Account deleted successfully ',
            ], 200);

         } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Fail to get data! ',
            ], 500);
        }    
    }
}
