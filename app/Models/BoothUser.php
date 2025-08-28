<?php

namespace App\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BoothUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'booth_id',
        'company_id'
    ];

    
    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function booth()
    {
        return $this->belongsTo(Booth::class);
    }
      public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
