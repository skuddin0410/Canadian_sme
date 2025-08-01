<?php

namespace App\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;

class CompanyContact extends Model
{
    //
    protected $fillable = [
        'company_id', 'name', 'email', 'phone', 'designation', 'purpose'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
