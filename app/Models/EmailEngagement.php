<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailEngagement extends Model
{
    protected $fillable = [
        'mail_log_id',
        'user_id',
        'event_type',
        'clicked_url',
        'ip_address',
        'user_agent'
    ];
}
