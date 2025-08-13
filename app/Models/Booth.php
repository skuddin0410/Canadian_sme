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

    // public function company()
    // {
    //     return $this->belongsTo(Company::class);
    // }
    public function company()
{
    return $this->belongsTo(Company::class, 'user_id', 'user_id');
}

    public function user()
{
    return $this->belongsTo(User::class);
}



    public function sessions()
    {
        return $this->hasMany(Session::class, 'booth_id');
    }
}
