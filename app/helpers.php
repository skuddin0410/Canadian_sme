<?php
use App\Models\Question;
use App\Models\Category;
use App\Models\Giveaway;
use App\Models\Quiz;
use App\Models\Setting;
use App\Models\User;
use BaconQrCode\Renderer\GDLibRenderer;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Storage;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use App\Models\GeneralNotification;
use App\Models\Session;
use App\Models\Company;



if (!function_exists('getCategory')) {
    function getCategory($type=null)
    {
       $category = Category::orderBy("created_at","DESC");
       if($type){
            $category = $category->where('type',$type);
       } 
       $category = $category->get();
       return $category;
    }
}

if (!function_exists('getExpert')) {
    function getExpert($string)
    { 
      if(strlen($string) > 150){
         return substr($string, 0,150)."...";
      }
      return $string;
    }
}

if (!function_exists('dateFormat')) {
    function dateFormat($date)
    { 
      return Carbon\Carbon::parse($date)->toFormattedDateString();
    }
}

if (!function_exists('dateTimeFormat')) {
    function dateTimeFormat($date)
    { 
      return Carbon\Carbon::parse($date)->toDayDateTimeString();
    }
}

if (!function_exists('userDateFormat')) {
    function userDateFormat($date)
    { 
      return Carbon\Carbon::parse($date)->toDateString();
    }
}

if (!function_exists('createSlug')) {
  function createSlug($str, $delimiter = '-')
  {
    
      if ($str != '') {
        $unwanted_array = [
          'ś' => 's',
          'ą' => 'a',
          'ć' => 'c',
          'ç' => 'c',
          'ę' => 'e',
          'ł' => 'l',
          'ń' => 'n',
          'ó' => 'o',
          'ź' => 'z',
          'ż' => 'z',
          'Ś' => 's',
          'Ą' => 'a',
          'Ć' => 'c',
          'Ç' => 'c',
          'Ę' => 'e',
          'Ł' => 'l',
          'Ń' => 'n',
          'Ó' => 'o',
          'Ź' => 'z',
          'Ż' => 'z'
        ]; // Polish letters for example
        $str = strtr($str, $unwanted_array);
        $slug = strtolower(trim(preg_replace('/[\s-]+/', $delimiter, preg_replace('/[^A-Za-z0-9-]+/', $delimiter, preg_replace('/[&]/', 'and', preg_replace('/[\']/', '', iconv('utf-8', 'us-ascii//IGNORE', $str))))), $delimiter));
        return $slug;
      }
  }
}



if (!function_exists('getKeyValue')) {
    function getKeyValue($key)
    {
       $setting = Setting::with('photo')->where('key',$key)->first();
       return $setting;
    }
}



if (!function_exists('downloadQrCode')) {
    function downloadQrCode($userId)
    {
        $user = User::findOrFail($userId);
        // Regenerate if missing
        if (!$user->qr_code || !file_exists(public_path($user->qr_code))) {
            qrCode($userId, "user");
        }

        $filePath = public_path($user->qr_code);
       
        if (!file_exists($filePath)) {
            abort(404, 'QR code not found.');
        }
        return response()->download($filePath, 'qrcodes' . $user->id . '.png', [
            'Content-Type' => 'image/png',
        ])->deleteFileAfterSend(false); // keep file in public/qrcodes
    }
}



if (!function_exists('qrCode')) {
  function qrCode($id,$folder="user")
  {     
        if (!file_exists(public_path('qrcodes'))) {
            mkdir(public_path('qrcodes'), 0755, true);
        }

        if($folder == 'user'){
            $user = User::findOrFail($id);
            $data = json_encode([
                'id' => $user->id,
                'name' => $user->full_name ?? '',
                'email' => $user->email ?? '',
            ]);
            $fileName = 'qrcodes/'.$folder.'_'. $user->id . '.png';
            $timestamp= Carbon\Carbon::now()->timestamp;
        }

        if($folder == 'company'){
           $company = Company::where('id',$id)->findOrFail();
           $data = json_encode([
                'id' => $company->id,
                'name' => $company->name ?? '',
                'email' => $company->email ?? '',
            ]);
            $fileName = 'qrcodes/'.$folder.'_'. $user->id . '.png';
            $timestamp= Carbon\Carbon::now()->timestamp;
        }

        
        
        $filePath = public_path($fileName);

        // if (file_exists($filePath)) {
        //     unlink($filePath);
        // }

        // Use GDLibRenderer
        $renderer = new GDLibRenderer(300); // 300 is the size of the QR code
        $writer = new Writer($renderer);
        $writer->writeFile($data, $filePath);
        $user->qr_code = 'qrcodes/' . $folder . '_' . $timestamp. '.png';


        $user->save();
  }
}

if (!function_exists('notification')) {
  function notification($user_id,$type='welcome', $session_id=null)
  {  
    $user = User::find($user_id);
    $session=null;
    if($session_id){
       $session = Session::find($session_id); 
    }
    
    if($type == 'welcome'){
      $title = 'Hi, welcome to '.config('app.name', 'SME').'!'; //welcome,Attendee,Exhibitors,Speaker 
      $body = 'Hi '.$user->full_name.', welcome to '.config('app.name', 'SME').'! We’re glad to have you here.';  
    }

    if($type == 'Attendee'){
        $title = 'Hi, '.$user->full_name; //welcome,Attendee,Exhibitors,Speaker 
        $body = 'Don’t miss it, '.$user->full_name.'! '.$session->title.' starts soon.';
    }
    
    if($type == 'Attendee_Reminder'){
        $title = 'Hi, '.$user->full_name; //welcome,Attendee,Exhibitors,Speaker 
        $body = '📅 Don’t miss it, '.$user->full_name.'! '.$session->title.' starts soon. Check the agenda';
    }


    if($type == 'Speaker_Reminder'){
        $title = 'Hi, '.$user->full_name; //welcome,Attendee,Exhibitors,Speaker 
        $body = '⏰ Reminder, '.$user->full_name.': Your talk ‘'.$session->title.'’ at '.$session->event->title.' starts in '.$session->starts_in.' minutes.';
    }

    if($type == 'Speaker_Thankyou'){
        $title = 'Hi, '.$user->full_name; //welcome,Attendee,Exhibitors,Speaker 
        $body = 'Thank you, '.$user->full_name.'! Your session at ‘'.$session->title.'’ inspired many attendees.';
    }

    if($type == 'Exhibitor_Reminder'){
        $title = 'Hi, '.$user->full_name; //welcome,Attendee,Exhibitors,Speaker 
        $body = '📅 Don’t miss it, '.$user->full_name.'! '.$session->title.' starts soon. Check the agenda';
    }

    $arr =[
        'user'=> $user,
        'session' => $session
    ];

     GeneralNotification::create([
        'user_id'=>$user->id,
        'title'=>$title,
        'related_type'=> $type,
        'body'=>$body,
        'meta'=> json_encode($arr)
    ]);
  }

}

if (!function_exists('groups')) {
function groups($user)
    {  
        $arr =[];
        if(!empty($user->secondary_group)){
         $arr = explode(',', $user->secondary_group);
        }
        array_push($arr, $user->primary_group);

        return array_unique($arr);
    }
}

if (! function_exists('shortenName')) {
    function shortenName($firstName, $lastName='')
    {
        $firstInitial = strtoupper($firstName[0]);
        if($lastName){
            $lastName = strtoupper($lastName[0]);
        }
        return $firstInitial . $lastName;
    }
}
