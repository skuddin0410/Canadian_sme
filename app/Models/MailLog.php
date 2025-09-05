<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email',
        'subject',
        'message',
        'status',
        'send_by'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

