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
        /**
     * Generate a circular 300x300 "mobile" image and store it under public/{file_path}/mobile/{filename}
     *
     * @param \Illuminate\Http\UploadedFile|string $file      Uploaded file object or path
     * @param string                              $file_path Destination folder (relative to storage disk)
     * @param string                              $filename  Filename to save (e.g. 'img.png')
     * @return string Path (relative) of stored file
     * @throws \Exception
     */

    public static function imageUpload(object $file , string $uploadPath=null,$table_id,$table_type,$file_type,$idForUpdate=null) : ?string
    {
    
        if($file  && $uploadPath) {
           try {
                $filename = now()->format('Y-m-d').'-'.abs(crc32(uniqid())).'-'.Carbon\Carbon::now()->timestamp . '.' . $file->getClientOriginalExtension();
                $file_path = $uploadPath;

                //if not image then upload pdf,doc  etc
                if(!in_array(strtolower($file->getClientOriginalExtension()), ['png', 'jpg', 'jpeg'])) {
                    //$file->storeAs($file_path,$filename,'public');
                    $file->storeAs($file_path, $filename, 's3');
                    static::saveImageDataIntoDrive($filename,$file_type,$table_id,$table_type,$idForUpdate);
                    return $filename;
                }  
                 
               
                $image = '';

                $size = getimagesize($file);
                $width = $size[0];
                $height = $size[1];

                if($table_type=='companies' && ($file_type =='content_icon' || $file_type =='logo')){
                    $image = Image::read($file)->resize(408, 480, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                }

                if($table_type=='companies' && ($file_type =='banner' || $file_type =='quick_link_icon')){

                   $image = Image::read($file)->resize(1920, 1081, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                }

                if($table_type=='speakers' && $file_type =='photo'){
                    $image = Image::read($file)->resize(490, 559, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                }

                
                if($table_type=='users' && ($file_type =='photo')){
                   $image = Image::read($file)->resize(408, 480, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                }

                if($table_type=='users' && ($file_type =='cover_photo')){
                   $image = Image::read($file)->resize(1920, 1081, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                }
                
                if(!empty($image)){
                Storage::disk('s3')->put($file_path . '/' . $filename, $image->encodeByExtension($file->getClientOriginalExtension(), quality: 70)); 
     
                    if($file_type =='content_icon' || $file_type =='logo' || $file_type =='photo' ){ 
                       $url = $file->storeAs($file_path, $filename, 's3');
                    } 

                }else{
                    //$url = $file->storeAs($file_path,$filename,'public');
                      $url = $file->storeAs($file_path, $filename, 's3');
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
           $drive->is_local_file = 0;
           $drive->save();
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

public static function imageBase64Upload($file, string $uploadPath = null, $table_id, $table_type, $file_type, $idForUpdate = null): ?string
{
    if ($file && $uploadPath) {
        try {
            // Set the directory path on S3
            $directoryPath = $uploadPath; // S3 will handle folders via path, no need for local directories

            // Extract the base64 image data by removing the data URI scheme (e.g., "data:image/jpeg;base64,")
            list($type, $imageData) = explode(';', $file); 
            $imageData = explode(',', $imageData)[1]; // Get the actual base64 data
            $mimeType = explode(':', $type)[1];  // Extract the mime type (e.g., image/jpeg)
            $extension = explode('/', $mimeType)[1];  // Extract the extension (e.g., jpeg, png)
            
            // Decode the base64 string into image binary
            $image = base64_decode($imageData);

            // Generate a unique filename using current timestamp and CRC32 of the unique id
            $filename = now()->format('Y-m-d') . '-' . abs(crc32(uniqid())) . '-' . Carbon\Carbon::now()->timestamp . '.' . $extension;

            // Define the full path where the file will be saved on S3
            $file_path = $directoryPath . '/' . $filename;

            // Store the file on AWS S3
            Storage::disk('s3')->put($file_path, $image);

            // Call a function to save image data (e.g., store the image filename in a database)
            static::saveImageDataIntoDrive($filename, $file_type, $table_id, $table_type, $idForUpdate);

            return $filename;
        } catch (\Exception $e) {
            // Log the error or return null if there's an issue
            return "null";
        }
    }

}

}