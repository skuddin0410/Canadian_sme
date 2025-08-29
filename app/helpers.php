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


if (!function_exists('getCategory')) {
    function getCategory()
    {
       $category = Category::orderBy("created_at","DESC")->get();
       return $category;
    }
}

if (!function_exists('getExperts')) {
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


if (!function_exists('qrCode')) {
    function qrCode($userId, $folder = "user")
    {
        $user = User::findOrFail($userId);

        $data = json_encode([
            'id' => $user->id,
            'name' => $user->full_name ?? '',
            'email' => $user->email ?? '',
        ]);

        // Ensure the directory exists
        $dirPath = public_path('qrcodes');
        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0755, true);
        }

        $fileName = $folder . '_' . $user->id . '.png';
        $filePath = $dirPath . '/' . $fileName;

        // Remove old QR if exists
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Setup the QR renderer
        $renderer = new ImageRenderer(
            new RendererStyle(300),
            new ImagickImageBackEnd() // more reliable than GDLibRenderer
        );

        $writer = new Writer($renderer);
        $writer->writeFile($data, $filePath);

        // Save relative path in DB
        $user->qr_code = 'qrcodes/' . $fileName;
        $user->save();
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



// if (!function_exists('qrCode')) {
//   function qrCode($userId,$folder="user")
//   { 
//        $user = User::findOrFail($userId);

//         $data = json_encode([
//             'id' => $user->id,
//             'name' => $user->full_name ?? '',
//             'email' => $user->email ?? '',
//         ]);

//         if (!file_exists(public_path('qrcodes'))) {
//             mkdir(public_path('qrcodes'), 0755, true);
//         }

//         $fileName = 'qrcodes/'.$folder.'_'. $user->id . '.png';
//         $filePath = public_path($fileName);

//         if (file_exists($filePath)) {
//             unlink($filePath);
//         }

//         // Use GDLibRenderer
//         $renderer = new GDLibRenderer(300); // 300 is the size of the QR code
//         $writer = new Writer($renderer);
//         $writer->writeFile($data, $filePath);

//         // $user->qr_code = asset($fileName);
//         // $user->qr_code = $fileName; // e.g. "qrcodes/user_1.png"
//         $user->qr_code = 'qrcodes/' . $folder . '_' . $user->id . '.png';


//         $user->save();
//   }

//   if (!function_exists('downloadQrCode')) {
//     function downloadQrCode($userId)
// {
//     $user = User::findOrFail($userId);

//     if (!$user->qr_code || !file_exists(public_path($user->qr_code))) {
//         qrCode($userId, "user"); // regenerate
//     }

//     $filePath = public_path($user->qr_code);

//     if (file_exists($filePath)) {
//         return response()->download($filePath, 'qr_code_' . $user->id . '.png', [
//             'Content-Type' => 'image/png',
//         ]);
//     }

//     abort(404, 'QR code not found.');
// }

// }

// }