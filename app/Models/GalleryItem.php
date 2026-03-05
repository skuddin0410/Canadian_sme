<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GalleryItem extends Model
{
    protected $fillable = [
        'file_path',
        'file_name',
        'file_type',
        'added_by',
        'is_approved',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
