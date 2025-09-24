<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Drive;
use Illuminate\Support\Facades\File;
use Storage;
use Carbon;
use DB;
use Intervention\Image\Laravel\Facades\Image;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public static function imageUpload(object $file , string $uploadPath=null,$table_id,$table_type,$file_type,$idForUpdate=null) : ?string
    {
    
        if($file  && $uploadPath) {
           try {
                $filename = now()->format('Y-m-d').'-'.abs(crc32(uniqid())).'-'.Carbon\Carbon::now()->timestamp . '.' . $file->getClientOriginalExtension();
                $file_path = $uploadPath;
                $image = '';

                $size = getimagesize($file);
                $width = $size[0];
                $height = $size[1];

                if($table_type=='companies' && ($file_type =='content_icon' || $file_type =='logo')){
                   if($width > 408 ||  $height > 480){
                     $image = Image::read($file)->resize(408, 480);
                   }
                }

                if($table_type=='companies' && ($file_type =='banner' || $file_type =='quick_link_icon')){
                   if($width > 1920 ||  $height > 1081){  
                    $image = Image::read($file)->resize(1920, 1081);
                   }
                }

                if($table_type=='speakers' && $file_type =='photo'){
                  if($width > 490 ||  $height > 559){  
                    $image = Image::read($file)->resize(490, 559);
                  }
                }

                
                if($table_type=='users' && ($file_type =='photo')){
                  if($width > 408 ||  $height > 480){   
                   $image = Image::read($file)->resize(408, 480);
                  }
                }

                if($table_type=='users' && ($file_type =='cover_photo')){
                  if($width > 1920 ||  $height > 1081){   
                    $image = Image::read($file)->resize(1920, 1081);
                  }
                }
                
                if(!empty($image)){
                 Storage::disk('public')->put($file_path.'/'.$filename,$image->encodeByExtension($file->getClientOriginalExtension(), quality: 70));
                }else{
                    $url = $file->storeAs($file_path,$filename,'public');
                }

                static::saveImageDataIntoDrive($filename,$file_type,$table_id,$table_type,$idForUpdate);
                return $filename;
            } catch (\Exception $e) {
                return "null";
            }
        }

    }

    public static function saveImageDataIntoDrive($file_path,$file_type,$table_id,$table_type,$idForUpdate){
            $drive = new Drive();   

            if($idForUpdate){
               $drive = Drive::where('table_id',$idForUpdate)->where('table_type',$table_type)->where('file_type',$file_type)->first();
            }
            if($drive == null){
               $drive = new Drive();  
            }

           $drive->table_id = $table_id;
           $drive->table_type = $table_type;
           $drive->file_name = $file_path;
           $drive->file_type = $file_type;
           $drive->save();
    }

    public static function generateMobileImage($file,$file_path,$filename){
        $mobile = Image::read($file)->fit(300, 300);
        $mask = Image::canvas(300, 300);
        $mask->circle(300, 150, 150, function ($draw) {
            $draw->background('#fff'); // white circle area
        });
        $mobile->mask($mask, true);
        $mobile->encode('png');
        Storage::disk('public')->put($file_path.'/mobile/'.$filename,$mobile->encodeByExtension($file->getClientOriginalExtension(), quality: 70));
     }   

    public static function deleteFile($table_id,$table_type,$file_type='photo'){
        $drive = Drive::where('table_type',$table_type)
               ->where('file_type',$file_type)
               ->where('table_id',$table_id)
               ->first();

        if($drive){
            $file_path = Storage::path($drive->table_type.'/'.$drive->file_name,'public');
            if (\File::isFile('storage/' . $drive->table_type . '/' . $drive->file_name)) {
                unlink(Storage::disk('public')->path($drive->table_type . '/' . $drive->file_name));
            }
            $drive->delete(); 
        }

    }
    
    public static function orderSet($table_id,$table,$orderVal){
        $datas = DB::table($table)->whereNotIn('id',[$table_id])->where('order',">=",$orderVal)->whereNotNull('order')->orderBy('order','ASC')->get();
        if(!empty($datas)){
            
                foreach($datas as $data){
                    DB::table($table)->where('id',$data->id)->update(['order'=>$data->order +1]);   
                }
                sleep(1);
                //Automatic rearrange if order has not null value
                $rearranges = DB::table($table)->whereNotNull('order')->orderBy('order','ASC')->get();
                if(!empty($rearranges)){
                    foreach($rearranges as $key => $rearrange){
                       DB::table($table)->where('id',$rearrange->id)->update(['order'=>$key +1]);
                    }
                }
                //Automatic rearrange if order has null value
                $maxId = DB::table($table)->orderBy('order', 'desc')->value('order');
                $maxID = $maxId ?? 0;
                $rearrangesNullData = DB::table($table)->whereNull('order')->orderBy('created_at','DESC')->get();
                if(!empty($rearrangesNullData)){
                    foreach($rearrangesNullData as $key => $rearrange){
                       DB::table($table)->where('id',$rearrange->id)->update(['order'=> $key +1 +$maxID ]);
                    }
                }
        }
    }

    public static function imageBase64Upload($file , string $uploadPath=null,$table_id,$table_type,$file_type,$idForUpdate=null) : ?string
    {
    
        if($file  && $uploadPath) {
           try {
                $directoryPath = storage_path('app/public/' . $uploadPath);

            // Check if the directory exists, and create it if it does not
            if (!File::exists($directoryPath)) {
                File::makeDirectory($directoryPath, 0755, true); // Recursive directory creation
            }

            // Extract the base64 image data by removing the data URI scheme (e.g., "data:image/jpeg;base64,")
            list($type, $imageData) = explode(';', $file); 
            $imageData = explode(',', $imageData)[1]; // Get the actual base64 data
            $mimeType = explode(':', $type)[1];  // Extract the mime type (e.g., image/jpeg)
            $extension = explode('/', $mimeType)[1];  // Extract the extension (e.g., jpeg, png)
            
            // Decode the base64 string into image binary
            $image = base64_decode($imageData);

            // Generate a unique filename using current timestamp and CRC32 of the unique id
            $filename = now()->format('Y-m-d') . '-' . abs(crc32(uniqid())) . '-' . Carbon\Carbon::now()->timestamp . '.' . $extension;

            // Define the full path where the file will be saved
            $file_path = $directoryPath . '/' . $filename;

            // Save the file (base64 decoded image data) to the specified file path
            file_put_contents($file_path, $image);

            // Call a function to save image data (e.g., store the image filename in a database)
            static::saveImageDataIntoDrive($filename, $file_type, $table_id, $table_type, $idForUpdate);

            return $filename;
            } catch (\Exception $e) {
                return "null";
            }
        }

    }

}
