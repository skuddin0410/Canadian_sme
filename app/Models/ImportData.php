<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class ImportData extends Model
{
    use HasFactory, Auditable;

    protected $table = 'imported_data';

    protected $fillable = [
        'name','lastname','email','status','gdpr_consent','bio','company','designation','mobile','dob','facebook_url','twitter_url','linkedin_url','instagram_url'
    ];
}
