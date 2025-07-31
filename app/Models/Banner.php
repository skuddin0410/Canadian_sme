<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'name',
        'description',
        'link',
        'order',
    ];

    public function photo()
    {
        return $this->hasOne(Drive::class, 'table_id', 'id')
            ->where('table_type', 'banners')
            ->where('file_type', 'photo')
            ->whereNotNull('file_name');
    }
}
