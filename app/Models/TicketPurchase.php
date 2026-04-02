<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\TicketType;
use App\Models\Event;

class TicketPurchase extends Model
{
    protected $fillable = [
        'user_id',
        'ticket_type_id',
        'event_id',
        'amount',
        'request',
        'response',
        'status',
        'payment_reference',
    ];

    protected $casts = [
        'request' => 'array',
        'response' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ticketType()
    {
        return $this->belongsTo(TicketType::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
