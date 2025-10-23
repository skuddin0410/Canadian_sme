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
                     
         
            $usersCount = clone $users;
            $totalRecords = $usersCount->count(DB::raw('DISTINCT(users.id)'));
            $users = $users->offset($offset)->limit($perPage)->get();

            $users = new LengthAwarePaginator($users, $totalRecords, $perPage, $pageNo, [
                'path'  => $request->url(),
                'query' => $request->query(),
            ]);
            $startRange = $offset + 1;
            $endRange = min($offset + $perPage, $totalRecords);

            // Prepare response data
            $data['totalUsers'] = $totalRecords; // Total number of users
            $data['offset'] = $offset;
            $data['pageNo'] = $pageNo;
            $range=$data['range'] = "$startRange-$endRange"; 

            $users->setPath(route('attendee-users.index'));
            $data['html'] = view('users.attendee_users.table', compact('users', 'perPage','range','totalRecords'))
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
    $userIds = json_decode($request->user_ids, true);
    $type = $request->query('type'); // email or notification
    $users = User::whereIn('id', $userIds)->get();

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
            
            $title = 'Hi, ' . ($user->full_name ?? '') . ',' ;

            notification($user->id,$type='push_notification',null, $title ,$message);

            if(!empty($user->onesignal_userid)){
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
                        'Authorization: Basic os_v2_app_kpowxj4tqjdj3cw2ojlo3xcztb4tfmbonf7ewyffzeqt5vujo22nbbneafdpruklh6rfzrfs6hqwfmc465icn75e3mx3k53i2zfn7yq'
                    ]);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                    curl_setopt($ch, CURLOPT_HEADER, FALSE);
                    curl_setopt($ch, CURLOPT_POST, TRUE);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                 
                    $response = curl_exec($ch);
                    curl_close($ch);

            }
        }
    }

    return redirect()->back()->with('success', ucfirst($type) . " sent successfully to selected users.");
}

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


}
