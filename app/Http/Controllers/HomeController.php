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
use App\Models\Order;
use App\Models\Page;

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
        if ( Auth::user()->hasRole('Admin') 
            || Auth::user()->hasRole('Exhibitor')
            || Auth::user()->hasRole('Representative')
            || Auth::user()->hasRole('Attendee')
            || Auth::user()->hasRole('Speaker')
            || Auth::user()->hasRole('Support Staff Or Helpdesk')
            || Auth::user()->hasRole('Registration Desk')) {
            
            $evntCount = Session::count();
            $attendeeCount = User::with("roles")
                ->whereHas("roles", function ($q) {
                    $q->whereNotIn("name", ["Attendee"]);
                })->count();

            $speakerCount = User::with("roles")
                ->whereHas("roles", function ($q) {
                    $q->whereNotIn("name", ["Speaker"]);
                })->count();  

            $sponsorCount = User::with("roles")
                ->whereHas("roles", function ($q) {
                    $q->whereNotIn("name", ["Sponsors"]);
                })->count();         
            
            $exhibitorCount = User::with("roles")
                ->whereHas("roles", function ($q) {
                    $q->whereNotIn("name", ["Exhibitor"]);
                })->count();
            $revenue = Order::sum('amount') ?? 0;
            if(Auth::user()->hasRole('Admin') ){
                $logs = AuditLog::with('user')->orderBy('created_at', 'desc')->limit(5)->get(); 
                $loginlogs = UserLogin::with('user')->orderBy('created_at', 'desc')->limit(5)->get();   
            }else{
                $logs = AuditLog::with('user')->where('audit_logs.user_id',auth()->id())->orderBy('created_at', 'desc')->limit(5)->get(); 
                $loginlogs = UserLogin::with('user')->where('user_logins.user_id',auth()->id())->orderBy('created_at', 'desc')->limit(5)->get(); 
            } 

            return view('home',compact('evntCount','attendeeCount','speakerCount','sponsorCount','exhibitorCount','revenue','logs','loginlogs'));
        }

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

        return view('brand');
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
        return view('splash'); 
    }

    public function registrationSettings(Request $request){

        if($request->mode == 'save'){
               $data = $request->validate([
                'company_name'     => ['required','string','max:255'],
                'company_address'  => ['required','string'],
                'support_email'    => ['required','email'],
                'tax_name'         => ['required','string','max:100'],
                'tax_percentage'   => ['required','numeric','between:0,100'],
                'company_number'   => ['required','string','max:100'],
                'privacy_policy'   => ['nullable','string'],
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
            } 
            session()->flash('success', 'Saved successfully.');

            return redirect()->route('registration-settings'); 
        }
       

        return view('registration-settings');
    }
    
}
