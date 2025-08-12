<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventTicketBooking extends Model
{
    protected $fillable = [
        'user_id', 'ticket_id', 'booking_code', 'quantity', 'total_amount', 'status', 'booked_at'
    ];

    // A booking belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // A booking belongs to a ticket
    public function ticket()
    {
        return $this->belongsTo(EventTicket::class, 'ticket_id');
    }
}
