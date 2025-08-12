<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventSession extends Model
{  
    protected $table = "event_sessions";

    protected $fillable = [
        'event_id', 'booth_id', 'title', 'start_time', 'end_time'
    ];

    // A session belongs to an event
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    // A session belongs to a venue
    public function booth()
    {
        return $this->belongsTo(Booth::class, 'booth_id');
    }

    // A session has many tickets
    public function tickets()
    {
        return $this->hasMany(EventTicket::class, 'session_id');
    }
}
