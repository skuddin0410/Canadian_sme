<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DemoRequests extends Model
{
    protected $table = "demo_requests";
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'timezone',
        'booking_date',
        'time_slot',
        'status',
        'note'
    ];
}
