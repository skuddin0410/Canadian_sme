<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketOrder extends Model
{
    protected $fillable = [
        'event_id',
        'ticket_type_id',
        'promo_code_id',
        'coordinator_user_id',
        'coordinator_name',
        'coordinator_email',
        'attendee_count',
        'total_amount',
        'promo_discount_amount',
        'currency',
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

    public function promoCode()
    {
        return $this->belongsTo(PromoCode::class);
    }

    public function coordinatorUser()
    {
        return $this->belongsTo(User::class, 'coordinator_user_id');
    }

    public function attendeePurchases()
    {
        return $this->hasMany(TicketPurchase::class, 'ticket_order_id');
    }

    public function invoice()
    {
        return $this->hasOne(TicketInvoice::class, 'ticket_order_id');
    }

    public function promoRedemptions()
    {
        return $this->hasMany(PromoCodeRedemption::class, 'ticket_order_id');
    }
}
