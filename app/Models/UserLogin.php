<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
use App\Traits\AutoHtmlDecode;

class UserLogin extends Model
{   
    use  Auditable;
    use AutoHtmlDecode;
    protected $fillable = [
         'user_id',
         'ip_address',
         'user_agent',
         'logged_in_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
