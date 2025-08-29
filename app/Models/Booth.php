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
