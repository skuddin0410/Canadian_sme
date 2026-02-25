<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Event;

class EventSupport extends Model
{
     protected $table = 'event_support';

    protected $fillable = [
        'event_id',
        'name',
        'email',
        'phone',
        'message',
        'status'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
