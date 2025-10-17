<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Storage;
class Drive extends Model
{
    protected $fillable = [
        'table_id',
        'table_type',
        'file_name',
        'file_type',
        'is_local_file'
    ];

    public function getFilePathAttribute()
    {  
        if( $this->is_local_file == 1){
            if (\File::isFile('storage/' . $this->table_type . '/' . $this->file_name)) {
                return asset('storage/' . $this->table_type . '/' . $this->file_name . '?v=' . time());
            }

            if (\File::isFile('storage/' . $this->file_type . '/' . $this->file_name)) {
                return asset('storage/' . $this->file_type . '/' . $this->file_name . '?v=' . time());
            }
        }else{
           if (Storage::disk('s3')->exists($this->file_type . '/' . $this->file_name)) {
              return Storage::disk('s3')->url($this->file_type . '/' . $this->file_name);
           }

           if (Storage::disk('s3')->exists($this->table_type . '/' . $this->file_name)) {
              return Storage::disk('s3')->url($this->table_type . '/' . $this->file_name);
           }
        }

        return asset('images/default.png');
    }

    protected $appends = ['file_path'];
}
