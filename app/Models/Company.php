<?php

namespace App\Models;

use App\Models\User;
use App\Models\CompanyContact;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    //
    protected $fillable = [
        'user_id','name', 'industry', 'size', 'location', 'email', 'phone',
        'description', 'website', 'linkedin', 'twitter', 'facebook', 'instagram', 'certifications'
    ];
   public function contacts()
    {
        return $this->hasMany(CompanyContact::class);
    }
    public function user()
{
    return $this->belongsTo(User::class);
}

}
