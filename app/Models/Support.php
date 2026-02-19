<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;
use App\Traits\AutoHtmlDecode;

class Support extends Model
{
     use HasFactory;
     use  Auditable;
     use AutoHtmlDecode;

    protected $fillable = [
        'name',
        'email' ,
        'phone',
        'location',
        'subject',
        'description',
        'added_by',
        'status',
        
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
