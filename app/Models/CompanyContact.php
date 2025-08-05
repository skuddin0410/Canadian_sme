<?php

namespace App\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
use App\Traits\AutoHtmlDecode;

class CompanyContact extends Model
{
    //
    use  Auditable;

    use AutoHtmlDecode;

 
    protected $fillable = [
        'company_id', 'name', 'email', 'phone', 'designation', 'purpose'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
