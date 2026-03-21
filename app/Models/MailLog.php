<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\EmailEngagement;
use App\Models\User;

class MailLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email',
        'subject',
        'message',
        'status',
        'send_by',
        'opened',
        'opened_at',
        'click_count'
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at'=> 'datetime',
        'opened' => 'boolean',
        'opened_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function engagements()
    {
        return $this->hasMany(EmailEngagement::class);
    }
}
