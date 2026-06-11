<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendingRegistration extends Model
{
    protected $fillable = [
        'event_id',
        'ticket_type_id',
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

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function ticketType()
    {
        return $this->belongsTo(TicketType::class);
    }

    public function promoCodeRedemptions()
    {
        return $this->hasMany(PromoCodeRedemption::class);
    }
}
