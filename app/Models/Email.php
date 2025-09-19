<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;
use App\Traits\AutoHtmlDecode;

class Email extends Model
{
    use HasFactory;
    use  Auditable;
    use AutoHtmlDecode;

    protected $fillable = [
        'user_id',
        'subject',
        'body',
        'email',
        'opened_at',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
    ];

    // Optional: If you want to define the relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
