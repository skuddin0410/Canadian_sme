<?php

namespace App\Http\Controllers;

use DB;
use Carbon;
use Storage;
use DataTables;
use App\Models\User;
use App\Mail\KycMail;
use App\Models\Drive;
use App\Models\Order;
use App\Models\Wallet;


use App\Models\Company;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
use Illuminate\Http\Request;
use App\Exports\SpeakersExport;
use App\Mail\CustomSpeakerMail;
use Illuminate\Support\Collection;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\MailLog;

class SpeakerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $perPage = (int) $request->input('perPage', 20);
        $pageNo = (int) $request->input('page', 1);
        $offset = $perPage * ($pageNo - 1);
        $search = $request->input('search', '');
        $kyc = $request->input('kyc', '');
        if ($request->ajax() && $request->ajax_request == true) {
            $users = User::with("roles")
                ->whereHas("roles", function ($q) {
                    $q->whereIn("name", ['Speaker']);
                })->where('primary_group','Speaker')->orderBy('created_at', 'DESC');

            if ($request->search) {
                $users = $users->where(function ($query) use ($request) {
                    $query->where('name', 'LIKE', '%' . $request->search . '%');
                    $query->orWhere('username', 'LIKE', '%' . $request->search . '%');
                    $query->orWhere('mobile', 'LIKE', '%' . $request->search . '%');
                    $query->orWhere('email', 'LIKE', '%' . $request->search . '%');
                });
            }

            if ($request->start_at && $request->end_at) {
                $users = $users->where(function ($query) use ($request) {
                    $query->whereDate('created_at', '>=', $request->start_at);
                    $query->whereDate('created_at', '<=', $request->end_at);
                    
                });
            }

           

            $usersCount = clone $users;
            $totalRecords = $usersCount->count(DB::raw('DISTINCT(users.id)'));
            $users = $users->offset($offset)->limit($perPage)->get();
            $users = new LengthAwarePaginator($users, $totalRecords, $perPage, $pageNo, [
                'path'  => $request->url(),
                'query' => $request->query(),
            ]);
            $data['offset'] = $offset;
            $data['pageNo'] = $pageNo;
            $users->setPath(route('speaker.index'));
            $data['html'] = view('users.speaker.table', compact('users', 'perPage'))
                ->with('i', $pageNo * $perPage)
                ->render();

            return response($data);
              }

        return view('users.speaker.index', ["kyc" => ""]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $speakers = User::select('id','name','lastname')->with("roles")->whereHas("roles", function ($q) {
                $q->whereIn("name", ['Speaker']);
            })->orderBy('created_at', 'DESC')->get();

        $exhibitors = Company::select('id','name')->orderBy('created_at', 'DESC')->get();

        $sponsors = Company::select('id','name')->orderBy('created_at', 'DESC')->get();

        $groups = config('roles.groups');
        return view('users.speaker.create',compact('groups','exhibitors','sponsors','speakers'));
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
            'website_url' => 'nullable|string|max:255',
            'linkedin_url' => 'nullable|string|max:255',
            'mobile' => 'nullable|string|unique:users,mobile',
            'bio' => 'required|string',
            'secondary_group'   => ['nullable','array'],
            'secondary_group.*' => ['string'], 
            'tags'   => ['nullable','array'],
            'tags.*' => ['string'], 

            'secondary_group'   => ['nullable','array'],
            'secondary_group.*' => ['string'],  
            'primary_group' => 'required|string', 

          'access_speaker_ids'    => ['nullable','array'],
          'access_speaker_ids.*'  => ['integer','exists:users,id'],
          'access_exhibitor_ids'  => ['nullable','array'],
          'access_exhibitor_ids.*'=> ['integer','exists:companies,id'],
          'access_sponsor_ids'    => ['nullable','array'],
          'access_sponsor_ids.*'  => ['integer','exists:companies,id'],                
        ]);

        if ($validator->fails()) {
            return redirect(route('speaker.create'))->withInput()
                ->withErrors($validator);
        }

        $user = new User();
        $user->name = $request->first_name;
        $user->lastname = $request->last_name;
        $user->email = $request->email;
        $user->company = $request->company;
        $user->primary_group = $request->primary_group;
        $user->secondary_group = !empty($request->secondary_group) ? implode(',',$request->secondary_group) : '';
        $user->status = $request->status;
        $user->gdpr_consent = $request->gdpr_consent;
        $user->designation = $request->designation;
        $user->tags =  !empty($request->tags) ? implode(',',$request->tags) : '';
        $user->website_url = $request->website_url;
        $user->linkedin_url = $request->linkedin_url;
        $user->instagram_url = $request->linkedin_url;
        $user->facebook_url = $request->facebook_url;
        $user->twitter_url = $request->twitter_url;
        $user->mobile = $request->mobile;
        $user->bio = $request->bio;
        $user->access_speaker_ids = !empty($request->access_speaker_ids) ? implode(',',$request->access_speaker_ids) : '';
        $user->access_exhibitor_ids =!empty($request->access_exhibitor_ids) ?  implode(',',$request->access_exhibitor_ids) : '';
        $user->access_sponsor_ids = !empty($request->access_sponsor_ids) ? implode(',',$request->access_sponsor_ids) : '';
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
        qrCode($user->id);
    

      return redirect(route('speaker.index'))
        ->withSuccess('Speaker data has been saved successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = User::findOrFail($id); // ensures fresh data
        return view('users.speaker.view', compact('user'));

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        
    $user = User::findOrFail($id);

    $speakers = User::select('id','name','lastname')->with("roles")->whereHas("roles", function ($q) {
                $q->whereIn("name", ['Speaker']);
            })->orderBy('created_at', 'DESC')->get();

    $exhibitors = Company::select('id','name')->orderBy('created_at', 'DESC')->get();

    $sponsors = Company::select('id','name')->orderBy('created_at', 'DESC')->get();

    $groups = config('roles.groups');

    return view('users.speaker.edit', compact('user','groups','exhibitors','sponsors','speakers'));
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
            'mobile' => 'nullable|string|unique:users,mobile,' . $user->id,
            'bio' => 'required|string',
            'secondary_group'   => ['nullable','array'],
            'secondary_group.*' => ['string'], 
            'tags'   => ['nullable','array'],
            'tags.*' => ['string'], 

            'secondary_group'   => ['nullable','array'],
            'secondary_group.*' => ['string'],  
            'primary_group' => 'required|string', 

         'access_speaker_ids'    => ['nullable','array'],
          'access_speaker_ids.*'  => ['integer','exists:users,id'],
          'access_exhibitor_ids'  => ['nullable','array'],
          'access_exhibitor_ids.*'=> ['integer','exists:companies,id'],
          'access_sponsor_ids'    => ['nullable','array'],
          'access_sponsor_ids.*'  => ['integer','exists:companies,id'],        
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $user->name = $request->first_name;
        $user->lastname = $request->last_name;
        $user->email = $request->email;
        $user->company = $request->company;
        $user->primary_group = $request->primary_group;
        $user->secondary_group = !empty($request->secondary_group) ? implode(',',$request->secondary_group) : '';
        $user->status = $request->status;
        $user->gdpr_consent = $request->gdpr_consent;
        $user->designation = $request->designation;
        $user->tags = !empty($request->tags) ? implode(',',$request->tags) : '';
        $user->website_url = $request->website_url;
        $user->linkedin_url = $request->linkedin_url;
        $user->instagram_url = $request->linkedin_url;
        $user->facebook_url = $request->facebook_url;
        $user->twitter_url = $request->twitter_url;
        $user->mobile = $request->mobile;
        $user->bio = $request->bio;
        $user->access_speaker_ids = !empty($request->access_speaker_ids) ? implode(',',$request->access_speaker_ids) : '';
        $user->access_exhibitor_ids =!empty($request->access_exhibitor_ids) ?  implode(',',$request->access_exhibitor_ids) : '';
        $user->access_sponsor_ids = !empty($request->access_sponsor_ids) ? implode(',',$request->access_sponsor_ids) : '';
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
        qrCode($user->id);

     return redirect(route('speaker.index'))
        ->withSuccess('Speaker data has been saved successfully');
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
            ->route('speaker.index')
            ->withSuccess('Speaker user deleted successfully.');
    }
    public function toggleBlock(User $user)
    {
    $currentUser = auth()->user();

    // Admin or Admin can block
    if ($currentUser->hasRole(['Admin', 'Admin'])) {
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
public function downloadQr($userid){
    return downloadQrCode($userid);
}
public function exportSpeakers()
{
    return Excel::download(new SpeakersExport, 'speakers.xlsx');
}

public function allowAccess(string $id)
{
    $user = User::with('roles')
        ->whereHas('roles', function ($q) {
            $q->where('name', 'Speaker');
        })
        ->findOrFail($id);
    
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
            $q->where('name', 'Speaker');
        })
        ->findOrFail($id);

    Mail::to($user->email)->send(new CustomSpeakerMail($user, $request->subject, $request->message));

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


}
