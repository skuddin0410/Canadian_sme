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

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public static function imageUpload(object $file , string $uploadPath=null,$table_id,$table_type,$file_type,$idForUpdate=null) : ?string
    {
    
        if($file  && $uploadPath) {
           try {
                $filename = now()->format('Y-m-d').'-'.abs(crc32(uniqid())).'-'.Carbon\Carbon::now()->timestamp . '.' . $file->getClientOriginalExtension();
                $file_path = $uploadPath;
                $url = $file->storeAs($file_path,$filename,'public');
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

}
