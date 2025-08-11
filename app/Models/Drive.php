<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Drive extends Model
{
    protected $fillable = [
        'table_id',
        'table_type',
        'file_name',
        'file_type',
    ];

    public function getFilePathAttribute()
    {  
        if (\File::isFile('storage/' . $this->table_type . '/' . $this->file_name)) {
            return asset('storage/' . $this->table_type . '/' . $this->file_name . '?v=' . time());
        }

        if (\File::isFile('storage/' . $this->file_type . '/' . $this->file_name)) {
            return asset('storage/' . $this->file_type . '/' . $this->file_name . '?v=' . time());
        }

        return asset('images/no-image.jpg');
    }

    protected $appends = ['file_path'];
}
