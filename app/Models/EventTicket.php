<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventTicket extends Model
{
    protected $fillable = [
        'session_id', 'name', 'price', 'quantity'
    ];

    // A ticket belongs to a session
    public function session()
    {
        return $this->belongsTo(EventSession::class, 'session_id');
    }

    // A ticket has many bookings
    public function bookings()
    {
        return $this->hasMany(EventTicketBooking::class, 'ticket_id');
    }
}
