<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Session;
use App\Models\Setting;

use Validator;
use Illuminate\Support\Facades\Hash;
use Auth;
use App\Models\Payment;
use App\Models\AuditLog;
use App\Models\UserLogin;
use App\Models\Page;
use App\Models\Speaker;
use App\Models\Company;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
        if ($user->hasRole('Admin')) {
            $isSuperAdmin = isSuperAdmin();
            $eventIds = [];

            if (!$isSuperAdmin) {
                $eventIds = \App\Models\EventAndEntityLink::where('entity_type', 'users')
                    ->where('entity_id', $user->id)
                    ->pluck('event_id')
                    ->toArray();
            }

            // Sessions / Events Count
            $querySessions = Session::query();
            if (!$isSuperAdmin) {
                $querySessions->whereIn('event_id', $eventIds);
            }
            $evntCount = $querySessions->count();

            // Attendees Count
            $queryAttendees = User::whereHas("roles", function ($q) {
                $q->where("name", "Attendee");
            });
            if (!$isSuperAdmin) {
                $queryAttendees->whereHas('eventAndEntityLinks', function ($q) use ($eventIds) {
                    $q->whereIn('event_id', $eventIds);
                });
            }
            $attendeeCount = $queryAttendees->count();

            // Speakers Count
            $querySpeakers = Speaker::query();
            if (!$isSuperAdmin) {
                $querySpeakers->whereHas('eventAndEntityLinks', function ($q) use ($eventIds) {
                    $q->whereIn('event_id', $eventIds);
                });
            }
            $speakerCount = $querySpeakers->count();

            // Sponsors Count
            $querySponsors = Company::where('is_sponsor', 1);
            if (!$isSuperAdmin) {
                $querySponsors->whereHas('eventAndEntityLinks', function ($q) use ($eventIds) {
                    $q->whereIn('event_id', $eventIds);
                });
            }
            $sponsorCount = $querySponsors->count();

            // Exhibitors Count
            $queryExhibitors = Company::where('is_sponsor', 0);
            if (!$isSuperAdmin) {
                $queryExhibitors->whereHas('eventAndEntityLinks', function ($q) use ($eventIds) {
                    $q->whereIn('event_id', $eventIds);
                });
            }
            $exhibitorCount = $queryExhibitors->count();

            // Logs
            if ($isSuperAdmin) {
                $logs = AuditLog::with('user')->orderBy('created_at', 'desc')->limit(5)->get();
                $loginlogs = UserLogin::with('user')->orderBy('created_at', 'desc')->limit(5)->get();
            } else {
                $logs = AuditLog::with('user')->where('user_id', $user->id)->orderBy('created_at', 'desc')->limit(5)->get();
                $loginlogs = UserLogin::with('user')->where('user_id', $user->id)->orderBy('created_at', 'desc')->limit(5)->get();
            }

            // Events Count (based on Event model)
            $queryEvents = \App\Models\Event::query();
            if (!$isSuperAdmin) {
                $queryEvents->whereIn('id', $eventIds);
            }
            $totalEventCount = $queryEvents->count();

            // Subscription
            $subscription = \App\Models\Subscription::with('pricing')->where('user_id', $user->id)->first();

            return view('home', compact('evntCount', 'attendeeCount', 'speakerCount', 'sponsorCount', 'exhibitorCount', 'logs', 'loginlogs', 'subscription', 'totalEventCount'));
        }

        return redirect()->route('user.home');
    }

    public function accountInfo(Request $request)
    {
        return view('account_settings.account_information');
    }

    public function accountInformation(Request $request) 
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'contact_number' => 'required',
            ]);

             if($validator->fails()) {
              return redirect()->route('change.account.information')->withError($validator->errors()->first());
            }


            $user = auth()->user();
            User::where('id',$user->id)->update([
                'name' =>$request->name,
                'lastname' =>$request->lastname,
                'mobile' =>$request->contact_number,
                'designation' =>$request->designation,
                'website_url' =>$request->website_url,
                'linkedin_url' =>$request->linkedin_url,
                'facebook_url' =>$request->facebook_url,
                'instagram_url' =>$request->instagram_url,
                'twitter_url' =>$request->twitter_url

            ]);

           if($request->file("image")){
              $this->imageUpload($request->file("image"),'users',$user->id,'users','photo',$idForUpdate=$user->id);   
            }

            return redirect()->route('change.account.information')
            ->withSuccess('Your account information changed successfully.');
        }catch(\Exception $e) {
            return redirect()->route('change.account.information')
            ->withError($e->getMessage());
        }
    }

     public function changeAccountPassword() 
    {
        return view('account_settings.change_password');
    }

     public function changePassword(Request $request)
     {
        try {
            $validator = Validator::make($request->all(), [
                'old_password' => 'required',
                'new_password' => 'min:8|same:confirm_password',
            ]);
            
            if($validator->fails()) {
              return redirect()->route('admin.change.password')->withError($validator->errors()->first());
            }

            $user = auth()->user();

            if (Hash::check($request->old_password, $user->password)) {
                $user->password = Hash::make($request->new_password);
                $user->save();
                return redirect()->route('admin.change.password')->withSuccess('Your password changed successfully.');
            }else{
                return redirect()->route('admin.change.password')->withError('Old Password does not match with our database');
            }


            
        }catch(\Exception $e) {
            return redirect()->route('admin.change.password')->withError('Sorry some problem occoured, please try again.');
        }
    }


    public function changeUserPassword(Request $request)
     {
        try {
            
            $validator = Validator::make($request->all(), [
                'password' => 'required|min:8',
                'confirm_password' => 'min:8|same:confirm_password',
            ]);
            
            if($validator->fails()) {
               return redirect()->back()->withInput()->withErrors($validator);
            }
         
            $user = User::where('id',$request->user_id)->firstOrFail();
            $user->password = Hash::make($request->password);
            $user->save();
            return redirect()->back()->withSuccess('Your password changed successfully.');
   
        }catch(\Exception $e) {
            return redirect()->route('admin.change.password')->withError('Sorry some problem occoured, please try again.');
        }
    }


    public function brand(Request $request){
        if($request->mode =='save'){
            $eventLogo = Setting::where('key','logo')->first();
            $eventLogo->save();

            if(!empty($request->file("event_logo"))){
                    $this->deleteFile($eventLogo->id,'settings');    
                    $this->imageUpload($request->file("event_logo"),"settings",$eventLogo->id,'settings','photo',$eventLogo->id);
            }

            $brand_cover = Setting::where('key','cover')->first();
            $brand_cover->save();

            if(!empty($request->file("brand_cover"))){
                    $this->deleteFile($brand_cover->id,'settings');    
                    $this->imageUpload($request->file("brand_cover"),"settings",$brand_cover->id,'settings','photo',$brand_cover->id);
            }

            $theme_color = Setting::where('key','color')->first();
            $theme_color->value = $request->theme_color;
            $theme_color->save();
        } 

        return view('brand')->with('message','App Branding media updated successfully');
    }

    public function splash(Request $request){

        if($request->mode =='save'){
            $ios_iphone_image = Setting::where('key','ios_iphone_image')->first();
            $ios_iphone_image->save();

            if(!empty($request->file("ios_iphone_image"))){
                    $this->deleteFile($ios_iphone_image->id,'settings');    
                    $this->imageUpload($request->file("ios_iphone_image"),"settings",$ios_iphone_image->id,'settings','photo',$ios_iphone_image->id);
            }

            $ios_ipad_image = Setting::where('key','ios_ipad_image')->first();
            $ios_ipad_image->save();

            if(!empty($request->file("ios_ipad_image"))){
                    $this->deleteFile($ios_ipad_image->id,'settings');    
                    $this->imageUpload($request->file("ios_ipad_image"),"settings",$ios_ipad_image->id,'settings','photo',$ios_ipad_image->id);
            }

            $android_hdpi_image = Setting::where('key','android_hdpi_image')->first();
            $android_hdpi_image->save();

            if(!empty($request->file("android_hdpi_image"))){
                    $this->deleteFile($android_hdpi_image->id,'settings');    
                    $this->imageUpload($request->file("android_hdpi_image"),"settings",$android_hdpi_image->id,'settings','photo',$android_hdpi_image->id);
            }

            $android_mdpi_image = Setting::where('key','android_mdpi_image')->first();
            $android_mdpi_image->save();

            if(!empty($request->file("android_mdpi_image"))){
                    $this->deleteFile($android_mdpi_image->id,'settings');    
                    $this->imageUpload($request->file("android_mdpi_image"),"settings",$android_mdpi_image->id,'settings','photo',$android_mdpi_image->id);
            }


            $android_xhdpi_image = Setting::where('key','android_xhdpi_image')->first();
            $android_xhdpi_image->save();

            if(!empty($request->file("android_xhdpi_image"))){
                    $this->deleteFile($android_xhdpi_image->id,'settings');    
                    $this->imageUpload($request->file("android_xhdpi_image"),"settings",$android_xhdpi_image->id,'settings','photo',$android_xhdpi_image->id);
            }

            $android_xxhdpi_image = Setting::where('key','android_xxhdpi_image')->first();
            $android_xxhdpi_image->save();

            if(!empty($request->file("android_xxhdpi_image"))){
                    $this->deleteFile($android_xxhdpi_image->id,'settings');    
                    $this->imageUpload($request->file("android_xxhdpi_image"),"settings",$android_xxhdpi_image->id,'settings','photo',$android_xxhdpi_image->id);
            }

        }
        session()->flash('success', 'Saved successfully.');
        //return redirect()->back()->with('success','App Branding media updated successfully');
        return view('splash'); 
    }

    public function registrationSettings(Request $request){
        if($request->mode == 'save'){
               $data = $request->validate([
                'company_name'     => ['required','string','max:255'],
                'company_address'  => ['required','string'],
                'support_email'    => ['required','email'],
                'tax_name'         => ['nullable','string','max:100'],
                'tax_percentage'   => ['nullable','numeric','between:0,100'],
                'company_number'   => ['nullable','string','max:100'],
                'privacy_policy'   => ['nullable','string'],
                'support'=>['nullable','string'],
                'about'   => ['nullable','string'],
                'location'   => ['nullable','string'],
                'terms_conditions' => ['nullable','string'],
                'thank_you_page'   => ['nullable','string'],
               ]);

            foreach ($data as $key => $value) {
                \App\Models\Setting::updateOrCreate(['key' => $key], ['value' => $value]);

                if($key == 'about'){
                   Page::where('slug','about')->update(['description' => $value]);
                }

                if($key == 'location'){
                   Page::where('slug','location')->update(['description' => $value]);
                }

                if($key == 'privacy_policy'){
                   Page::where('slug','privacy')->update(['description' => $value]);
                }

                if($key == 'terms_conditions'){
                   Page::where('slug','terms')->update(['description' => $value]);
                }


                if($key == 'support'){
                   Page::updateOrCreate(['slug' => "support"], ['description' => $value]);
                }
            }  
            session()->flash('success', 'Saved successfully.');

            return redirect()->route('registration-settings'); 
        }
       

        return view('registration-settings');
    }
    
   public function emailTemplateSettings(Request $request){
        if($request->mode == 'save'){
               $data = $request->validate([
                'subject'     => ['required','string','max:255'],
                'content'  => ['required','string',"max:500"],
               ]);
            
            if(!empty($request->subject)) {
                $subject = Setting::where('key','email_subject')->first();
                $subject->value = $request->subject;
                $subject->save();   
            }  

             if(!empty($request->content)) {
                $content = Setting::where('key','email_content')->first();
                $content->value = $request->content;
                $content->save();   
            }
            
            session()->flash('success', 'Saved successfully.');     
            return redirect()->route('email-template-settings'); 
        }
            
       return view('EmailTemplate');
   }
 public function deleteMedia(Request $request)
{
    $key = trim($request->input('key'));

    // Map frontend keys to Setting keys
    $map = [
        'event_logo'  => 'logo',
        'brand_cover' => 'cover',
    ];

    if (!isset($map[$key])) {
        return response()->json(['error' => 'Invalid media key'], 400);
    }

    $setting = Setting::where('key', $map[$key])->first();
    if (!$setting) {
        return response()->json(['error' => 'Setting not found'], 404);
    }

    // Delete associated photo record and file
    if ($setting->photo) {
        $photo = $setting->photo;

        // Delete physical file
        if (file_exists(public_path($photo->file_path))) {
            @unlink(public_path($photo->file_path));
        }

        // Delete photo record
        $photo->delete();
    }

    return response()->json(['message' => ucfirst(str_replace('_', ' ', $key)) . ' deleted successfully']);
}

    public function markAllNotificationsAsRead()
    {
        \App\Models\GeneralNotification::where('user_id', auth()->id())
            ->where('is_read', 0)
            ->update(['is_read' => 1, 'read_at' => now()]);

        return response()->json(['success' => true]);
    }

    public function markNotificationAsRead($id)
    {
        \App\Models\GeneralNotification::where('user_id', auth()->id())
            ->where('id', $id)
            ->update(['is_read' => 1, 'read_at' => now()]);

        return response()->json(['success' => true]);
    }
}
