<?php

namespace App\Http\Controllers;

use DB;
use Carbon;
use Storage;
use DataTables;
use App\Models\User;
use App\Mail\KycMail;
use App\Models\Booth;
use App\Models\Drive;

use App\Models\Company;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
use Illuminate\Http\Request;
use App\Mail\CustomSpeakerMail;
use App\Exports\AttendeesExport;
use Illuminate\Support\Collection;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\MailLog;
use App\Mail\UserWelcome;
use App\Models\EmailTemplate;
use OneSignal;
use App\Models\Speaker;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AttendeeUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $perPage = (int) $request->input('perPage', 150);
        $pageNo = (int) $request->input('page', 1);
        $offset = $perPage * ($pageNo - 1);
        $search = $request->input('search', '');
        $kyc = $request->input('kyc', '');
        if ($request->ajax() && $request->ajax_request == true) {
            // $users = User::with("roles")->whereHas("roles", function ($q) {
            //   $q->whereIn("name", ['Attendee']);
            // })->orderBy('id', 'DESC');
            
           $users = User::with('roles')->whereNotIn('id',[1,2])->orderBy('id', 'DESC');
 
            if ($request->filled('search')) {
                $users = $users->where(function ($query) use ($request) {
                    $query->where('name', 'LIKE', '%' . $request->search . '%')
                          ->orWhere('email', 'LIKE', '%' . $request->search . '%')
                          ->orWhere('lastname', 'LIKE', '%' . $request->search . '%')
                          ->orWhere(DB::raw("CONCAT(name, ' ', lastname)"), 'LIKE', "%{$request->search}%")
                          ->orWhere('designation', 'LIKE', '%' . $request->search . '%')
                          ->orWhere('mobile', 'LIKE', '%' . $request->search . '%')
                          ->orWhere('company', 'LIKE', '%' . $request->search . '%');

                          
                });
            }

            // Filters (triggered by filter button, add your filter logic here)
            if ($request->filled('start_at') && $request->filled('end_at')) {
                $users = $users->whereBetween('created_at', [$request->start_at, $request->end_at]);
            }
            
            if ($request->has('exhibitor_id')) {
                $users = $users->where('created_by_exhibitor_id', $request->exhibitor_id);
            }

            if ($request->has('onsignal') && $request->onsignal == 1) {
                $users = $users->whereNotNull('onesignal_userid');
            }
                     
         
            $usersCount = clone $users;
            $totalRecords = $usersCount->count(DB::raw('DISTINCT(users.id)'));
            $totalAppUsers = (clone $usersCount)
                ->whereNotNull('onesignal_userid')
                ->count(DB::raw('DISTINCT(users.id)'));
            $users = $users->offset($offset)->limit($perPage)->get();

            $users = new LengthAwarePaginator($users, $totalRecords, $perPage, $pageNo, [
                'path'  => $request->url(),
                'query' => $request->query(),
            ]);
            $startRange = $offset + 1;
            $endRange = min($offset + $perPage, $totalRecords);

            // Prepare response data
            $data['totalUsers'] = $totalRecords; // Total number of users
            $data['totalAppUsers'] = $totalAppUsers;
            $data['offset'] = $offset;
            $data['pageNo'] = $pageNo;
            $range=$data['range'] = "$startRange-$endRange"; 

            $users->setPath(route('attendee-users.index'));
            $data['html'] = view('users.attendee_users.table', compact('users', 'perPage','range','totalRecords','totalAppUsers'))
                ->with('i', $pageNo * $perPage)
                ->render();

            return response($data);
        }

        return view('users.attendee_users.index', ["kyc" => ""]);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {   
        $speakers = Speaker::select('id','name','lastname')->orderBy('created_at', 'DESC')->get();

        $exhibitors = Company::select('id','name')->where('is_sponsor',0)->orderBy('created_at', 'DESC')->get();

        $sponsors = Company::select('id','name')->where('is_sponsor',1)->orderBy('created_at', 'DESC')->get();

        $groups = config('roles.groups');
        return view('users.attendee_users.create',compact('groups','exhibitors','sponsors','speakers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
   
        $validator = Validator::make($request->all(), [
         
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|max:255|email|unique:users,email',
            'designation' => 'nullable|string|max:255' ,
            'tags' => 'nullable|string|max:255'  ,
            'website_url' => 'nullable|url',
            'linkedin_url' => 'nullable|url',
            'facebook_url' => 'nullable|url',
            'instagram_url' => 'nullable|url',
            'twitter_url' => 'nullable|url',
            'mobile' =>'nullable|string',
            'bio' => 'nullable|string',
            'secondary_group'   => ['nullable','array'],
            'secondary_group.*' => ['string'], 
            'tags'   => ['nullable','array'],
            'tags.*' => ['string'], 

            'secondary_group'   => ['nullable','array'],
            'secondary_group.*' => ['string'],  
            'primary_group' => 'required|string',               
        ]);

        if ($validator->fails()) {
            return redirect(route('attendee-users.create'))->withInput()
                ->withErrors($validator);
        }

        $user = new User();
        $user->name = $request->first_name;
        $user->lastname = $request->last_name;
        $user->slug = createUniqueSlug('users', $request->first_name.'_'.$request->last_name);
        $user->email = $request->email;
        $user->company = $request->company;
        $user->primary_group = $request->primary_group;
        $user->secondary_group = !empty($request->secondary_group) ? implode(',',$request->secondary_group) : '';
        $user->status = $request->status;
        $user->gdpr_consent = $request->gdpr_consent;
        $user->designation = $request->designation;
        $user->tags =  !empty($request->tags)? implode(',',$request->tags) : '';
        $user->website_url = $request->website_url;
        $user->linkedin_url = $request->linkedin_url;
        $user->instagram_url = $request->instagram_url;
        $user->facebook_url = $request->facebook_url;
        $user->twitter_url = $request->twitter_url;
        $user->mobile = $request->mobile;
        $user->bio = $request->bio ?? null;
        $user->is_approve = true;
        $user->access_speaker_ids = $request->access_speaker_ids ?? '';
        $user->access_exhibitor_ids =$request->access_exhibitor_ids??  '';
        $user->access_sponsor_ids = $request->access_sponsor_ids ?? '';
        $user->company_id = $request->access_exhibitor_ids ?? '';
        $user->save();

        $cometChatID = $this->createCometChatUser($user->id, $user->name, $user->email, $user->mobile);
        $user->cometchat_id = $cometChatID['uid'];
        $user->save();
       
        if ($request->has('edit_permission') && $request->has('access_exhibitor_ids') && $request->edit_permission == 'Edit Company' && !empty($request->access_exhibitor_ids)) {
            $user->givePermissionTo('Edit Company');
        }
        
        $primaryGroupArray= [];
        $secondaryGroupArray=[];
         
        if(!empty($request->primary_group)) {
          $primaryGroupArray = explode(',', $request->primary_group);   
        }
        if(!empty($request->secondary_group)) {
          $secondaryGroupArray = $request->secondary_group ?? [];
        }
          $combinedGroups = array_merge($primaryGroupArray, $secondaryGroupArray);
          $combinedGroups = array_unique($combinedGroups); 
        
        if(!empty($combinedGroups)){
          $user->syncRoles($combinedGroups);  
        }

        if ($request->hasFile('image')) {
          $this->imageUpload($request->file("image"),"users",$user->id,'users','photo',$user->id);
        }

        if ($request->hasFile('cover_image')) {
          $this->imageUpload($request->file("cover_image"),"users",$user->id,'users','cover_photo',$user->id);
        }

        if (!empty($request->private_docs)) {

          foreach($request->private_docs as $img){
             $this->imageUpload($img,"users",$user->id,'users','private_docs'); 
          }  
          
        }
        
        $user = User::where('id',$user->id)->first(); 
        
        if($user){  
          sendNotification("Welcome Email",$user);
          qrCode($user->id);
        }

        return redirect()->to(route('attendee-users.index', $user->id))->withSuccess('Saved successfully.');
    }
    


    private function createCometChatUser($userId, $name, $email, $mobile)
    {
        $appID = env('COMETCHAT_APP_ID');
        $apiKey = env('COMETCHAT_API_KEY');
        $region = env('COMETCHAT_REGION');
 
        $user = User::find($userId);
        $avatarUrl = $user->photo ? $user->photo->mobile_path : asset('images/default.png');
 
        $data = [
            'uid' => "SME_CometChat_{$userId}",
            'name' => $name,
            'avatar' => $avatarUrl,
            // 'link' => "https://commons.wikimedia.org/wiki/File:No_Image_Available.jpg",
            'role' => 'default',
            'statusMessage' => 'default',
            'metadata' => [
                '@private' => [
                    'email' => $email,
                    'contactNumber' => $mobile,
                ]
            ],
            'tags' => [],
            'withAuthToken' => true
        ];
 
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'apikey' => $apiKey,
        ])->post(
            "https://{$appID}.api-{$region}.cometchat.io/v3/users",
            $data
        );
 
        if ($response->successful()) {
            $responseData = $response->json();
            // Return relevant data
            return [
                'uid' => $responseData['data']['uid'],
                'status' => $responseData['data']['status'],
                'authToken' => $responseData['data']['authToken']
            ];
        }
 
        return null;
    }
 
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
     
        $user = User::findOrFail($id); // ensures fresh data
        return view('users.attendee_users.view', compact('user'));


    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        
    $user = User::findOrFail($id);
    $speakers = Speaker::select('id','name','lastname')->orderBy('created_at', 'DESC')->get();

    $exhibitors = Company::select('id','name')->where('is_sponsor',0)->orderBy('created_at', 'DESC')->get();

    $sponsors = Company::select('id','name')->where('is_sponsor',1)->orderBy('created_at', 'DESC')->get();

    $groups = config('roles.groups');

    return view('users.attendee_users.edit', compact('user','groups','exhibitors','sponsors','speakers'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {    
        $user = User::findOrFail($id);
        $validator = Validator::make($request->all(), [
            
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|max:255|email|unique:users,email,' . $user->id,
            'designation' => 'nullable|string|max:255' ,
            'tags' => 'nullable|string|max:255'  ,
            'website_url' => 'nullable|string|max:255',
            'linkedin_url' => 'nullable|string|max:255',
            'mobile' => 'nullable|string',
            'bio' => 'nullable|string',
            'secondary_group'   => ['nullable','array'],
            'secondary_group.*' => ['string'], 
            'tags'   => ['nullable','array'],
            'tags.*' => ['string'], 

            'secondary_group'   => ['nullable','array'],
            'secondary_group.*' => ['string'],  
            'primary_group' => 'required|string',         
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }


        $user->name = $request->first_name;
        $user->lastname = $request->last_name;
        $user->slug = createUniqueSlug('users', $request->first_name.'_'.$request->last_name,'slug',$user->id);
        $user->email = $request->email;
        $user->company = $request->company;
        $user->primary_group = $request->primary_group;
        $user->secondary_group = !empty($request->secondary_group) ? implode(',',$request->secondary_group) : '';
        $user->status = $request->status;
        $user->gdpr_consent = $request->gdpr_consent;
        $user->designation = $request->designation;
        $user->tags =  !empty($request->tags)? implode(',',$request->tags) : '';
        $user->website_url = $request->website_url;
        $user->linkedin_url = $request->linkedin_url;
        $user->instagram_url = $request->linkedin_url;
        $user->facebook_url = $request->facebook_url;
        $user->twitter_url = $request->twitter_url;
        $user->mobile = $request->mobile;
        $user->bio = $request->bio ?? '';
        $user->is_approve = true;

        $user->access_speaker_ids = $request->access_speaker_ids ?? '';
        $user->access_exhibitor_ids =$request->access_exhibitor_ids??  '';
        $user->access_sponsor_ids = $request->access_sponsor_ids ?? '';
        $user->company_id = $request->access_exhibitor_ids ?? '';
        $user->save();

        if ( $request->has('edit_permission') && $request->has('access_exhibitor_ids') && $request->edit_permission == 'Edit Company' && !empty($request->access_exhibitor_ids)) {
            $user->givePermissionTo('Edit Company');
        }else{
            $user->revokePermissionTo('Edit Company'); 
        }

    
        $primaryGroupArray= [];
        $secondaryGroupArray=[];
         
        if(!empty($request->primary_group)) {
          $primaryGroupArray = explode(',', $request->primary_group);   
        }
        if(!empty($request->secondary_group)) {
          $secondaryGroupArray = $request->secondary_group ?? [];
        }
          $combinedGroups = array_merge($primaryGroupArray, $secondaryGroupArray);
          $combinedGroups = array_unique($combinedGroups); 
        
        if(!empty($combinedGroups)){
          $user->syncRoles($combinedGroups);  
        }

        if ($request->hasFile('image')) {
          $this->imageUpload($request->file("image"),"users",$user->id,'users','photo',$user->id);
        }

         if ($request->hasFile('cover_image')) {
          $this->imageUpload($request->file("cover_image"),"users",$user->id,'users','cover_photo',$user->id);
        }

        if (!empty($request->private_docs)) {

          foreach($request->private_docs as $img){
             $this->imageUpload($img,"users",$user->id,'users','private_docs'); 
          }  
          
        }
        
        return redirect()->to(route('attendee-users.index', $user->id))->withSuccess('Saved successfully.');   
    

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->roles()->detach();
        $user->delete();

        return redirect()
            ->route('attendee-users.index')
            ->withSuccess('Attendee user deleted successfully.');

    }
    public function toggleBlock(User $user){
        $currentUser = auth()->user();
        if ($currentUser->hasRole(['Admin'])) {
            $allowedRoles = ['Admin', 'Representative', 'Attendee', 'Speaker'];
            if ($user->hasAnyRole($allowedRoles)) {
                $user->is_block = true;
                $user->save();
                return back()->withSuccess('User has been blocked successfully.');
            } else {
                return back()->withErrors('You are not allowed to block this type of user.');
            }
        }
        return back()->withErrors('You do not have permission to perform this action.');
    }
    public function exportAttendees()
    {
        return Excel::download(new AttendeesExport, 'attendees.xlsx');
    }
    public function allowAccess(string $id)
    {
        $user = User::with('roles')->findOrFail($id);


        if($user->is_approve == 1){
           $user->is_approve = 0;
           $message = "App access allowed successfully";
        }else{
            $user->is_approve = 1;
            $message = "App access removed successfully";
        } 
        $user->save();
        return back()->withSuccess($message);

  }

public function sendMail(Request $request, $id)
{
    $request->validate([
        'subject' => 'required|string|max:255',
        'message' => 'required|string',
    ]);

    $user = User::with('roles')
        ->whereHas('roles', function ($q) {
            $q->where('name', 'Attendee');
        })
        ->findOrFail($id);

    Mail::to($user->email)->send(new CustomSpeakerMail($user,$request->subject, $request->message));
    MailLog::create([
        'user_id' => $user->id,
        'email'   => $user->email,
        'subject' => $request->subject,
        'message' => $request->message,
        'status'  => 'sent',
        'send_by'  => auth()->id(),
    ]);
    return back()->withSuccess('Welcome Mail sent successfully to ' . $user->name);
}

public function bulkAction(Request $request)
{
    // dd($request->all());

    $userIds = json_decode($request->user_ids, true);


    $type = $request->query('type'); // email or notification
    if (in_array('all', $userIds, true)) {
       $users = User::get();
    }else{
      $users = User::whereIn('id', $userIds)->get();  
    }
    
    $emailTemplate = EmailTemplate::where('template_name', $request->template_name)->first();
    $subject = $emailTemplate->subject ?? '';
    $subject = str_replace('{{site_name}}', config('app.name'), $subject);
    $subject = str_replace('{{site_name}}', config('app.name'), $subject);

    if (!empty($emailTemplate) && $emailTemplate->type === 'email') {
        foreach ($users as $user) {
            $qr_code_url = asset($user->qr_code);
            $message = $emailTemplate->message ?? '';
            $message = str_replace('{{name}}', $user->full_name, $message);
            $message = str_replace('{{site_name}}', config('app.name'), $message);
            if (strpos($message, '{{qr_code}}') !== false) {
              $message = str_replace('{{qr_code}}', '<br><img src="' . $qr_code_url . '" alt="QR Code" />', $message);
            }

            if (strpos($message, '{{profile_update_link}}') !== false) {
              $updateUrl = route('update-user',  Crypt::encryptString($user->id));  
              $message = str_replace('{{profile_update_link}}', '<br><a href="' . $updateUrl . '">Update Profile</a>', $message);
            }
          
            Mail::to($user->email)->send(new UserWelcome($user, $subject, $message));
        }
    } else if (!empty($emailTemplate) && $emailTemplate->type == 'notifications') {
        // dd('notification block');
         foreach ($users as $user) {
            $message = $emailTemplate->message ?? '';
            $message = str_replace('{{name}}', $user->full_name, $message);
            $message = str_replace('{{site_name}}', config('app.name'), $message);

            $message = $message; // dynamic message from database
            $notificationMessage = trim(
                preg_replace('/\s+/', ' ',
                    strip_tags(
                        html_entity_decode($message, ENT_QUOTES | ENT_HTML5, 'UTF-8')
                    )
                )
            );
            Log::info(''. $notificationMessage .'');
            
            $title = 'Hi, ' . ($user->full_name ?? '') . ',' ;

            notification($user->id,$type='push_notification',null, $title ,$message);

            // if(!empty($user->onesignal_userid)){
                
            //         $content = [
            //             // "app_id" => "53dd6ba7-9382-469d-8ada-7256eddc5998",
            //             "app_id" => "53dd6ba7-9382-469d-8ada-7256eddc5998",
            //             // "include_player_ids" => [$user->onesignal_userid],
            //             "include_subscription_ids" => [$user->onesignal_userid], //new addition
            //             'headings' => ['en' => $title],
            //             "contents" => ["en" => $message],
            //             "target_channel" => "push", //new addition
            //         ];
                 
            //         // $fields = json_encode($content);
                 
            //         // $ch = curl_init();
            //         // // curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
            //         // curl_setopt($ch, CURLOPT_URL, "https://api.onesignal.com/notifications?c=push");
            //         // curl_setopt($ch, CURLOPT_HTTPHEADER, [
            //         //     'Content-Type: application/json; charset=utf-8',
            //         //     // 'Authorization: Basic os_v2_app_kpowxj4tqjdj3cw2ojlo3xcztb4tfmbonf7ewyffzeqt5vujo22nbbneafdpruklh6rfzrfs6hqwfmc465icn75e3mx3k53i2zfn7yq'
            //         //     'Authorization: Key os_v2_app_kpowxj4tqjdj3cw2ojlo3xczta2fjjcewqyuyz4kuwhcc7isatc64afnmopzvmnkd7tw6i5qmu3vdzcl3qh5ittn3xgwpad43g5rd4y',
            //         //     // 'target_channel: push'
            //         // ]);
            //         // curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            //         // curl_setopt($ch, CURLOPT_HEADER, FALSE);
            //         // curl_setopt($ch, CURLOPT_POST, TRUE);
            //         // curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            //         // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                 
            //         // $response = curl_exec($ch);
            //         // curl_close($ch);

            //         $curl = curl_init();
 
            //         curl_setopt_array($curl, [
            //         CURLOPT_URL => "https://api.onesignal.com/notifications?c=push",
            //         CURLOPT_RETURNTRANSFER => true,
            //         CURLOPT_ENCODING => "",
            //         CURLOPT_MAXREDIRS => 10,
            //         CURLOPT_TIMEOUT => 30,
            //         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            //         CURLOPT_CUSTOMREQUEST => "POST",
            //         CURLOPT_POSTFIELDS => json_encode([
            //             'app_id' => '53dd6ba7-9382-469d-8ada-7256eddc5998',
            //             'contents' => [
            //                 'en' => $notificationMessage
            //             ],
            //             'include_aliases' => [
            //                 'external_id' => [
            //                         '<string>'
            //                 ]
            //             ],
            //             'target_channel' => 'push',
            //             'include_subscription_ids' => [
            //                 $user->onesignal_userid
            //             ],
            //             'included_segments' => [
            //                 '<string>'
            //             ],
            //             'excluded_segments' => [
            //                 '<string>'
            //             ],
            //             'filters' => [
            //                 [
            //                         'field' => 'tag',
            //                         'relation' => '=',
            //                         'key' => '<string>',
            //                         'value' => '<string>'
            //                 ]
            //             ],
            //             'headings' => [
            //                 'en' => '<string>'
            //             ],
            //             'subtitle' => [
            //                 'en' => '<string>'
            //             ],
            //             'name' => '<string>',
            //             'template_id' => '<string>',
            //             'custom_data' => [
            //             ],
            //             'ios_attachments' => [
            //                 'id' => '<string>'
            //             ],
            //             'big_picture' => '<string>',
            //             'huawei_big_picture' => '<string>',
            //             'adm_big_picture' => '<string>',
            //             'chrome_web_image' => '<string>',
            //             'small_icon' => '<string>',
            //             'huawei_small_icon' => '<string>',
            //             'adm_small_icon' => '<string>',
            //             'large_icon' => '<string>',
            //             'huawei_large_icon' => '<string>',
            //             'adm_large_icon' => '<string>',
            //             'chrome_web_icon' => '<string>',
            //             'firefox_icon' => '<string>',
            //             'chrome_web_badge' => '<string>',
            //             'android_channel_id' => '<string>',
            //             'existing_android_channel_id' => '<string>',
            //             'huawei_channel_id' => '<string>',
            //             'huawei_existing_channel_id' => '<string>',
            //             'huawei_category' => 'MARKETING',
            //             'huawei_msg_type' => 'message',
            //             'huawei_bi_tag' => '<string>',
            //             'priority' => 10,
            //             'ios_interruption_level' => 'active',
            //             'ios_sound' => '<string>',
            //             'ios_badgeType' => 'None',
            //             'ios_badgeCount' => 123,
            //             'android_accent_color' => '<string>',
            //             'huawei_accent_color' => '<string>',
            //             'url' => '<string>',
            //             'app_url' => '<string>',
            //             'web_url' => '<string>',
            //             'target_content_identifier' => '<string>',
            //             'buttons' => [
            //                 [
            //                         'id' => '<string>',
            //                         'text' => '<string>',
            //                         'icon' => '<string>'
            //                 ]
            //             ],
            //             'web_buttons' => [
            //                 [
            //                         'id' => '<string>',
            //                         'text' => '<string>',
            //                         'url' => '<string>'
            //                 ]
            //             ],
            //             'thread_id' => '<string>',
            //             'ios_relevance_score' => 123,
            //             'android_group' => '<string>',
            //             'adm_group' => '<string>',
            //             'ttl' => 259200,
            //             'collapse_id' => '<string>',
            //             'web_push_topic' => '<string>',
            //             'data' => [
            //             ],
            //             'content_available' => true,
            //             'ios_category' => '<string>',
            //             'apns_push_type_override' => '<string>',
            //             'isIos' => true,
            //             'isAndroid' => true,
            //             'isHuawei' => true,
            //             'isAnyWeb' => true,
            //             'isChromeWeb' => true,
            //             'isFirefox' => true,
            //             'isSafari' => true,
            //             'isWP_WNS' => true,
            //             'isAdm' => true,
            //             'send_after' => '<string>',
            //             'delayed_option' => '<string>',
            //             'delivery_time_of_day' => '<string>',
            //             'throttle_rate_per_minute' => 123,
            //             'enable_frequency_cap' => true,
            //             'idempotency_key' => '<string>'
            //         ]),
            //         CURLOPT_HTTPHEADER => [
            //             "Authorization: Key os_v2_app_kpowxj4tqjdj3cw2ojlo3xcztcxchozkwy3ev3mhwmznaa7gq66vcthhiacbc7j3tsa5zdrffpripuvqpm5glloxdiumcg4yhkmzfla",
            //             "Content-Type: application/json"
            //         ],
            //         ]);
                    
            //         $response = curl_exec($curl);
            //         $err = curl_error($curl);
                    
            //         curl_close($curl);
                    
            //         if($err)
            //         Log::info("". $err);

            //         // if ($err) {
            //         // echo "cURL Error #:" . $err;
            //         // } else {
            //         // echo $response;
            //         // }

            // }


            if(!empty($user->onesignal_userid)){
                $payload = [
                    'app_id' => '53dd6ba7-9382-469d-8ada-7256eddc5998',
                    'contents' => [
                        'en' => $notificationMessage ?? 'Default message.',
                    ],
                    'headings' => [
                        'en' => $title ?? 'Notification',
                    ],
                    'target_channel' => 'push',
                    'include_subscription_ids' => [
                        $user->onesignal_userid,
                    ],
                ];

                $response = Http::withHeaders([
                        'Authorization' => 'Key os_v2_app_kpowxj4tqjdj3cw2ojlo3xcztcxchozkwy3ev3mhwmznaa7gq66vcthhiacbc7j3tsa5zdrffpripuvqpm5glloxdiumcg4yhkmzfla',
                        'Content-Type'  => 'application/json',
                    ])
                    ->post('https://api.onesignal.com/notifications?c=push', $payload);

                if ($response->failed()) {
                    Log::error('OneSignal push failed', [
                        'status' => $response->status(),
                        'body'   => $response->body(),
                    ]);
                }
            }


            // $this->sendOneSignalPushByExternalId(
            //     (string) $user->id,
            //     $title,
            //     $notificationMessage
            // );
        }
    }

    return redirect()->back()->with('success', ucfirst($type) . " sent successfully to selected users.");
}

    // private function sendOneSignalPushByExternalId(string $externalId, string $title, string $content): void
    // {
    //     // $appId = env('ONESIGNAL_APP_ID');         // e.g. 53dd6ba7-9382-469d-8ada-7256eddc5998
    //     $appId = '53dd6ba7-9382-469d-8ada-7256eddc5998';

    //     // $apiKey = env('ONESIGNAL_REST_API_KEY');  // your OneSignal App API Key
    //     $apiKey = 'os_v2_app_kpowxj4tqjdj3cw2ojlo3xcztb4tfmbonf7ewyffzeqt5vujo22nbbneafdpruklh6rfzrfs6hqwfmc465icn75e3mx3k53i2zfn7yq';

    //     if (empty($appId) || empty($apiKey)) {
    //         \Log::warning('OneSignal env not set: ONESIGNAL_APP_ID / ONESIGNAL_REST_API_KEY');
    //         return;
    //     }

    //     $payload = [
    //         'app_id' => $appId,
    //         'contents' => ['en' => $content],         // required
    //         'headings' => ['en' => $title],
    //         'target_channel' => 'push',
    //         'include_aliases' => [
    //             'external_id' => [$externalId],
    //         ],
    //     ];

    //     $res = Http::withHeaders([
    //             'Authorization' => 'Key ' . $apiKey,  // required format
    //             'Content-Type'  => 'application/json',
    //         ])
    //         ->post('https://api.onesignal.com/notifications?c=push', $payload);

    //     if (!$res->successful()) {
    //         \Log::error('OneSignal push failed (external_id)', [
    //             'external_id' => $externalId,
    //             'status' => $res->status(),
    //             'body' => $res->body(),
    //         ]);
    //     }
    // }

public function generateQrCodeManually(){
        $users = User::whereNull('qr_code')->orWhere('qr_code', '')->get();
        if(!empty($users)){
           foreach ($users as $user) {
            if(empty($user->qr_code)){
               qrCode($user->id, 'user'); 
            } 

            if (!$user->hasRole('Attendee')) {
              $user->assignRole('Attendee');
            } 
           } 

           
        }

        return redirect()->back()->with('success',"Qr Code generated sent successfully.");   
    }


public function sendBoth(Request $request)
{   
    $request->validate([
        'email_template' => 'nullable|string',
        'notification_template' => 'nullable|string',
    ]);
 
    $emailTemplateName = $request->email_template;
    $notificationTemplateName = $request->notification_template;

    // Get all users, excluding ids 1 and 2, in chunks of 200
    $usersQuery = User::whereNotIn('id', [1, 2]);

    if ($usersQuery->count() === 0) {
        return redirect()->back()->withErrors('No users found to send.');
    }

    // Process in chunks of 200
    $usersQuery->chunk(200, function ($users) use ($emailTemplateName, $notificationTemplateName) {
        foreach ($users as $user) {
            // Handle email sending
            if ($emailTemplateName) {
                $emailTemplate = EmailTemplate::where('template_name', $emailTemplateName)->first();
                if ($emailTemplate && $emailTemplate->type === 'email') {
                    $subject = str_replace('{{ site_name }}', config('app.name'), $emailTemplate->subject ?? '');
                    $message = $emailTemplate->message ?? '';

                    $qr_code_url = $user->qr_code ? asset($user->qr_code) : '';
                    $updateUrl = route('update-user', Crypt::encryptString($user->id));

                    $message = str_replace(
                        ['{{ name }}', '{{ qr_code }}', '{{ profile_update_link }}'],
                        [
                            $user->name ?? $user->email,
                            $qr_code_url ? '<br><img src="' . $qr_code_url . '" alt="QR Code" />' : '',
                            '<br><a href="' . $updateUrl . '">Update Profile</a>'
                        ],
                        $message
                    );

                    Mail::to($user->email)->send(new UserWelcome($user, $subject, $message));
                }
            }

            // Handle notification sending
            if ($notificationTemplateName) {
                $notificationTemplate = EmailTemplate::where('template_name', $notificationTemplateName)->first();
                if ($notificationTemplate && $notificationTemplate->type === 'notifications') {
                    $title = 'Hi, ' . ($user->name ?? $user->email) . ',';
                    $message = str_replace(
                        ['{{ name }}', '{{ qr_code }}', '{{ profile_update_link }}'],
                        [
                            $user->name ?? $user->email,
                            $user->qr_code ?? '',
                            route('update-user', Crypt::encryptString($user->id))
                        ],
                        $notificationTemplate->message ?? ''
                    );

                    // Create notification for user
                    $user->notifications()->create([
                        'title' => $notificationTemplate->title ?? $title,
                        'body' => $message,
                        'read_at' => null
                    ]);

                    // Send OneSignal push notification if user has OneSignal ID
                    if (!empty($user->onesignal_userid)) {
                        $content = [
                            "app_id" => "53dd6ba7-9382-469d-8ada-7256eddc5998",
                            "include_player_ids" => [$user->onesignal_userid],
                            'headings' => ['en' => $title],
                            "contents" => ["en" => $message]
                        ];

                        $fields = json_encode($content);
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
                        curl_setopt($ch, CURLOPT_HTTPHEADER, [
                            'Content-Type: application/json; charset=utf-8',
                            'Authorization: Basic YOUR_ONESIGNAL_KEY'
                        ]);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                        curl_setopt($ch, CURLOPT_HEADER, FALSE);
                        curl_setopt($ch, CURLOPT_POST, TRUE);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                        curl_exec($ch);
                        curl_close($ch);
                    }
                }
            }
        }
    });
 
    return redirect()->back()->with('success', 'Emails & Notifications sent successfully to all selected users.');
}

    
public function generateBadge(Request $request)
{
    $request->validate([
        'badge_id' => 'required|exists:badges,id',
        'user_ids' => 'required',
    ]);
 
    $badge = Badge::findOrFail($request->badge_id);
    $userIds = json_decode($request->user_ids, true);
 
    if (empty($userIds)) {
        return back()->with('error', 'No users selected.');
    }
 
    $users = User::whereIn('id', $userIds)->get();
 
    // Generate PDF (or redirect to view)
    $pdf = PDF::loadView('badges.pdf', compact('badge', 'users'));
    return $pdf->download('attendee_badges.pdf');
}



}
