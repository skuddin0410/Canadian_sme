<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventTicket extends Model
{
    protected $fillable = [
        'session_id', 'name', 'price', 'quantity', 'is_group', 'group_size'
    ];

    // A ticket belongs to a session
    public function session()
    {
        return $this->belongsTo(Session::class, 'session_id');
    }

    // A ticket has many bookings
    public function bookings()
    {
        return $this->hasMany(EventTicketBooking::class, 'ticket_id');
    }

    public function isGroupTicket()
    {
        return $this->is_group;
    }
}
