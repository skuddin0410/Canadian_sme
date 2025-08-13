<?php

namespace App\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
use App\Traits\AutoHtmlDecode;


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

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function sessions()
    {
        return $this->hasMany(Session::class, 'booth_id');
    }
}
