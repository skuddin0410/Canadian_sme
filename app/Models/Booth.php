<?php

namespace App\Models;

use App\Models\User;
use App\Models\Company;
use App\Traits\Auditable;
use App\Traits\AutoHtmlDecode;
use Illuminate\Database\Eloquent\Model;


class Booth extends Model
{
    //
     use  Auditable;
     use AutoHtmlDecode;

     protected $fillable = [
        'company_id',
        'title',
        'booth_number',
        'size',
        'location_preferences',
    ];

   
//     public function company()
// {
//     return $this->belongsTo(Company::class, 'user_id', 'user_id');
// }

//     public function user()
// {
//     return $this->belongsTo(User::class);
// }
// public function companies()
// {
//     return $this->hasMany(Company::class);
// }
 public function companies()
    {
        return $this->hasManyThrough(
            Company::class,
            BoothUser::class,
            'booth_id',   // Foreign key on BoothUser table
            'id',         // Foreign key on Company table
            'id',         // Local key on Booth table
            'company_id'  // Local key on BoothUser table
        );
    }

public function boothUsers()
{
    return $this->hasMany(BoothUser::class);
}


    public function sessions()
    {
        return $this->hasMany(Session::class, 'booth_id');
    }
}
