<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Support extends Model
{
     use HasFactory;

    protected $fillable = [
      
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
