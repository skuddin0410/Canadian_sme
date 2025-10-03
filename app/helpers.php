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
use App\Models\UserAgenda;
use App\Models\FavoriteSession;
use App\Models\UserConnection;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserWelcome;
use App\Models\Badge;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;


if (!function_exists('getCategory')) {
    function getCategory($type=null)
    {  
       $category = Category::orderBy("created_at","DESC");
       if($type){
            $category = $category->whereIn('type',explode(',', $type));
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


if (!function_exists('createSlugBase')) {
    /**
     * Create the base slug string from a title (with custom transliteration).
     */
    function createSlugBase(string $str, string $delimiter = '-'): string
    {
        if ($str === '') return '';

        // Custom mapping (your original list; extend as needed)
        $unwanted = [
            'ś' => 's','ą' => 'a','ć' => 'c','ç' => 'c','ę' => 'e','ł' => 'l','ń' => 'n','ó' => 'o','ź' => 'z','ż' => 'z',
            'Ś' => 's','Ą' => 'a','Ć' => 'c','Ç' => 'c','Ę' => 'e','Ł' => 'l','Ń' => 'n','Ó' => 'o','Ź' => 'z','Ż' => 'z',
        ];

        $str = strtr($str, $unwanted);

        // Best-effort ASCII fallback (iconv can return false; guard it)
        $ascii = @iconv('UTF-8', 'US-ASCII//IGNORE', $str);
        if ($ascii === false) $ascii = $str;

        // Normalize: replace & with 'and', drop apostrophes, keep alnum and dashes
        $ascii = preg_replace('/&/u', 'and', $ascii);
        $ascii = preg_replace('/[\']/u', '', $ascii);
        $slug  = preg_replace('/[^A-Za-z0-9-]+/u', $delimiter, $ascii);
        $slug  = preg_replace('/[\s-]+/u', $delimiter, $slug);
        $slug  = strtolower(trim($slug, $delimiter));

        return $slug;
    }
}


if (!function_exists('createUniqueSlug')) {
    /**
     * Create a unique slug for a given table (and column), optionally ignoring a row ID.
     *
     * @param  string      $table       Table name (e.g. 'posts')
     * @param  string      $title       Source text to slugify
     * @param  string      $column      Column name that stores the slug (default 'slug')
     * @param  int|null    $ignoreId    Existing row ID to exclude (useful on update)
     * @param  string      $delimiter   Slug delimiter (default '-')
     * @return string
     * createUniqueSlug('posts', $request->title); Create (table: posts, column: slug)
     * createUniqueSlug('posts', $request->title, 'slug', 15);         // e.g., keeps "hello-world" unless another row has it
     */
    function createUniqueSlug(
        string $table,
        string $title,
        string $column = 'slug',
        ?int $ignoreId = null,
        string $delimiter = '-'
    ): string {
        $base = createSlugBase($title, $delimiter);

        // If the base becomes empty (e.g., only symbols), fall back to a short token
        if ($base === '') {
            $base = 'item';
        }

        // If no collision, return early
        $existsQuery = DB::table($table)->where($column, $base);
        if ($ignoreId !== null) {
            $existsQuery->where('id', '!=', $ignoreId);
        }
        if (!$existsQuery->exists()) {
            return $base;
        }

        // Pull all conflicting slugs that start with the base (e.g., 'post-title', 'post-title-2', 'post-title-10')
        $like = $base . '%';
        $candidates = DB::table($table)
            ->select($column)
            ->when($ignoreId !== null, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where($column, 'like', $like)
            ->pluck($column)
            ->all();

        // Find the highest numeric suffix currently used
        $max = 1; // start at "-2" (since base exists)
        $pattern = '/^' . preg_quote($base, '/') . $delimiter . '(\d+)$/';

        foreach ($candidates as $slug) {
            if ($slug === $base) {
                // base taken; ensure we'll return at least "-2"
                $max = max($max, 1);
                continue;
            }
            if (preg_match($pattern, $slug, $m)) {
                $num = (int) $m[1];
                $max = max($max, $num);
            }
        }

        return $base . $delimiter . ($max + 1);
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
        
        $timestamp= Carbon\Carbon::now()->timestamp;
        if($folder == 'user'){
            $user = User::findOrFail($id);
            if(!empty($user->qr_code)){
               $oldFilePath = public_path($user->qr_code);
                if (file_exists($oldFilePath)) {
                   unlink($oldFilePath);
                }
            }

            $data = json_encode([
                'id' => $user->id,
                'name' => $user->full_name ?? '',
                'email' => $user->email ?? '',
                'app' => 'com.canadianSME.app'
            ]);
            $fileName = 'qrcodes/'.$folder.'_'. $timestamp . '.png';
        }

        $filePath = public_path($fileName);
        // Use GDLibRenderer
        //  $renderer = new ImageRenderer(
        //     new RendererStyle(300, 14), // 300px QR, 4 module quiet zone
        //     new ImagickImageBackEnd()  // or GD if you prefer
        // );
        $renderer = new GDLibRenderer(300);
        $writer = new Writer($renderer);
        $writer->writeFile($data, $filePath);
        $user->qr_code = 'qrcodes/' . $folder . '_' . $timestamp. '.png';
        $user->save();

        return true;
  }
}

if (!function_exists('notification')) {
  function notification($user_id,$type='welcome', $session_id=null, $title='',$body='')
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
        $body = '⏰ Reminder, '.$user->full_name.': Your talk "'.$session->title.'" is scheduled for '.$session->event->title.'. Please be ready to join.';
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
        if(!empty($arr)){
           return $user->roles?->pluck('name')->toArray() ?? [];
        }
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


if (! function_exists('addAgenda')) {
    function addAgenda($sessionId,$agenda_type=null,$userId=null,$message='')
    {

        $session = Session::find($sessionId); 
        $title= $session->title. " added to agenda";
        $body = $session->title. " has been added to your agenda list";

        UserAgenda::updateOrCreate(
            [
                'user_id' => !empty($userId) ? $userId : auth()->id(), 
                'session_id' => $sessionId, 
            ],
            [
                'agenda_type' => $agenda_type,
                'message' => $message ?? $body,
            ]
        );

        notification(!empty($userId) ? $userId : auth()->id(),$type='general_notifications',$sessionId, $title,$body);
    }
}

if (! function_exists('addFavorite')) {
    function addFavorite($sessionId,$userId=null)
    {
        FavoriteSession::firstOrCreate([
            'user_id' => !empty($userId) ? $userId : auth()->id(),
            'session_id' => $sessionId
        ]);

        $session = Session::find($sessionId); 
        $title="Favorite list updated";
        $body = $session->title. " has been added to your favorite list";
        notification(!empty($userId) ? $userId : auth()->id(),$type='general_notifications',$sessionId, $title,$body); 
    }
}

if (! function_exists('isAgenda')) {
    function isAgenda($sessionId)
    {
        $exists = UserAgenda::where('user_id', auth()->id())
        ->where('session_id', $sessionId)
        ->exists();

        if ($exists) {
            return true; // already exists
        }

        return false;
    }
}

if (! function_exists('isFavorite')) {
    function isFavorite($sessionId)
    {
        $exists = FavoriteSession::where('user_id', auth()->id())
        ->where('session_id', $sessionId)
        ->exists();

        if ($exists) {
            return true; // already exists
        }
        return false; // newly created
    }
}

if (! function_exists('userConnection')) {
    function userConnection($senderId,$receiverId)
    {   

        UserConnection::updateOrCreate(
            [
                'user_id'       => $senderId,
                'connection_id' => $receiverId,
            ],
            [
                'status'        => 'accepted',
            ]
        );
    }
}


if (! function_exists('removeFavorite')) {
    function removeFavorite($sessionId, $userId = null): bool
    {
        $uid = $userId ?? auth()->id();
        if (! $uid) {
            return false; 
        }

        return (bool) FavoriteSession::where('user_id', $uid)
            ->where('session_id', $sessionId)
            ->delete();
    }
}


if (!function_exists('fetchEmailTemplates')) {
    function fetchEmailTemplates()
    {  
       $emailTemplate = EmailTemplate::where('type','email')->orderBy("created_at","DESC")->get();
       return $emailTemplate;
    }
}


if (!function_exists('fetchNotificationTemplates')) {
    function fetchNotificationTemplates()
    {  
       $emailTemplate = EmailTemplate::where('type','notification')->orderBy("created_at","DESC")->get();
       return $emailTemplate;
    }
}


if (!function_exists('sendNotification')) {
    function sendNotification($template_name, $user)
    {  
      
        $emailTemplate = EmailTemplate::where('template_name', $template_name)->first();
        if (!empty($emailTemplate) && $emailTemplate->type == 'email') {
            $subject = $emailTemplate->subject ?? '';
            $subject = str_replace('{{site_name}}', config('app.name'), $subject);
            $subject = str_replace('{{site_name}}', config('app.name'), $subject);
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
 
        } elseif (!empty($emailTemplate) && $emailTemplate->type == 'notification') {
            foreach ($users as $user) {
                //$user->notify(new AppNotification("Bulk Notification", "This is a bulk notification."));
            }
        }

    }
}


if (! function_exists('agendaNote')) {
    function agendaNote($sessionId)
    {
        $agenda = UserAgenda::where('user_id', auth()->id())
        ->where('session_id', $sessionId)
        ->first();

        if ($agenda) {
            return $agenda->message ?? '';
        }
        return '';
    }
}


if (!function_exists('fetchBadgeTemplates')) {
    function fetchBadgeTemplates()
    {  
       $emailBadgeTemplate = Badge::orderBy("created_at","DESC")->get();
       return $emailBadgeTemplate;
    }
}

if (!function_exists('truncateString')) {
    function truncateString($string, $length) {
        if (strlen($string) > $length) {
            return substr($string, 0, $length) . '...';
        }
        return $string;
    }
}