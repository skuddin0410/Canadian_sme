<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
use App\Traits\AutoHtmlDecode;

class Otp extends Model
{   
    use  Auditable;
    use AutoHtmlDecode;
    protected $fillable = [
        'email',
        'otp',
        'expired_at',
    ];
}
