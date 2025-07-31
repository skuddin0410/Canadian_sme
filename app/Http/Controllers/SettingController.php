<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {  
       $settings = Setting::whereNotIn('key',['home_page_link_4','home_page_link_3','home_page_link_2','home_page_link_1','quiz_on_top','giveaway_on_top','home_page_spinners','home_page_quizzes','home_page_giveaways'])->orderBy('id','DESC')->paginate(25);
       return view("setting.index",["settings"=>$settings]);
    }

    public function indexHome()
    {  
       return view("setting.view-home");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {   
        if($request->setting =='home'){
            //dd($request->all());
            $giveawayOnTop = Setting::where('key','giveaway_on_top')->first();
            $giveawayOnTop->value= $request->giveaway_quiz_on  == 'giveaway_on_top' ? 1:0;
            $giveawayOnTop->save();

            $quizOnTop = Setting::where('key','quiz_on_top')->first();
            $quizOnTop->value= $request->giveaway_quiz_on  == 'quiz_on_top' ? 1:0;
            $quizOnTop->save();

            $homePageLink1 = Setting::where('key','home_page_link_1')->first();
            $homePageLink1->value= $request->home_page_link_1 ?? '';
            $homePageLink1->save();
            
            if(!empty($request->file("home_page_image_1"))){
            $this->deleteFile($homePageLink1->id,'settings');    
            $this->imageUpload($request->file("home_page_image_1"),"settings",$homePageLink1->id,'settings','photo');
            }
            
            $homePageLink2 = Setting::where('key','home_page_link_2')->first();
            $homePageLink2->value= $request->home_page_link_2 ?? '';
            $homePageLink2->save();

            if(!empty($request->file("home_page_image_2"))){
            $this->deleteFile($homePageLink2->id,'settings');    
            $this->imageUpload($request->file("home_page_image_2"),"settings",$homePageLink2->id,'settings','photo');
            }
            

            $homePageLink3 = Setting::where('key','home_page_link_3')->first();
            $homePageLink3->value= $request->home_page_link_3 ?? '';
            $homePageLink3->save();

            if(!empty($request->file("home_page_image_3"))){
            $this->deleteFile($homePageLink3->id,'settings');    
            $this->imageUpload($request->file("home_page_image_3"),"settings",$homePageLink3->id,'settings','photo');
            }
            

            $homePageLink4 = Setting::where('key','home_page_link_4')->first();
            $homePageLink4->value= $request->home_page_link_4 ?? '';
            $homePageLink4->save();

            if(!empty($request->file("home_page_image_4"))){
            $this->deleteFile($homePageLink4->id,'settings');    
            $this->imageUpload($request->file("home_page_image_4"),"settings",$homePageLink4->id,'settings','photo');
            }
            
            $home_page_giveaways = Setting::where('key','home_page_giveaways')->first();
            $home_page_giveaways->value= $request->home_page_giveaways ?? '';
            $home_page_giveaways->save();

            if(!empty($request->file("home_page_giveaways_image"))){
                $this->deleteFile($home_page_giveaways->id,'settings');    
                $this->imageUpload($request->file("home_page_giveaways_image"),"settings",$home_page_giveaways->id,'settings','photo');
            }

            $home_page_quizzes = Setting::where('key','home_page_quizzes')->first();
            $home_page_quizzes->value= $request->home_page_quizzes ?? '';
            $home_page_quizzes->save();

            if(!empty($request->file("home_page_quizzes_image"))){
                $this->deleteFile($home_page_quizzes->id,'settings');    
                $this->imageUpload($request->file("home_page_quizzes_image"),"settings",$home_page_quizzes->id,'settings','photo');
            }

            $home_page_spinners = Setting::where('key','home_page_spinners')->first();
            $home_page_spinners->value= $request->home_page_spinners ?? '';
            $home_page_spinners->save();

            if(!empty($request->file("home_page_spinners_image"))){
                $this->deleteFile($home_page_spinners->id,'settings');    
                $this->imageUpload($request->file("home_page_spinners_image"),"settings",$home_page_spinners->id,'settings','photo');
           }


            return redirect(route('indexHome'))->withSuccess("Settings has been saved successfully");

        }else{
            // $validator = Validator::make($request->all(), [
            //     //'key_value'=>'required|string|max:255',
            //     'key' => 'required|string|max:255|unique:settings,key',
            //     'value'=>'required|string|max:255'
            // ]);

            // if($validator->fails()){
            //     return redirect(route('settings.index'))->withInput()->withError($validator->errors()->first());
            // }
    
            // $setting = Setting::where('key',$request->key)->first();
            // $setting->key = $request->key;
            // $setting->value = $request->value;
            // $setting->save();
            // return redirect(route('settings.index'))->withSuccess("Settings has been saved successfully");
            
        }
        
    }

    /**
     * Display the specified resource.
     */
    public function show(Setting $setting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Setting $setting)
    {
    $settings = Setting::whereNotIn('key',['home_page_link_4','home_page_link_3','home_page_link_2','home_page_link_1','quiz_on_top','giveaway_on_top','home_page_spinners','home_page_quizzes','home_page_giveaways'])->orderBy('id','DESC')->paginate(25);
       return view("setting.index",["settings"=>$settings,"setting"=>$setting]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Setting $setting)
    {
        $validator = Validator::make($request->all(), [
            //'key_value'=>'required|string|max:255',
            'key' => 'required|string|max:255|unique:settings,key,'.$setting->id,
            'value'=>'required|string|max:255'
        ]);
        // if($request->key=='referrer' && !is_numeric($request->value)){
        //     return redirect(route('settings.index'))->withError("Referrer value should be numeric");
        // }
        if($validator->fails()){
            return redirect(route('settings.edit',['setting'=>$setting]))->withInput()->withError($validator->errors()->first());
        }
        $setting = Setting::where('key',$request->key)->first();
        $setting->key = $request->key;
        $setting->value = $request->value;
        $setting->save();
        return redirect(route('settings.index'))->withSuccess("Settings has been saved successfully");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Setting $setting)
    {
        $setting->delete();
        return redirect(route('settings.index'))->withSuccess("Settings deleted successfully");
    }
}
