<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoCodeRedemption extends Model
{
    protected $fillable = [
        'promo_code_id',
        'event_id',
        'ticket_type_id',
        'ticket_order_id',
        'pending_registration_id',
        'user_id',
        'email',
        'code',
        'attendee_count',
        'discount_amount',
        'final_total',
        'status',
        'used_at',
        'refunded_at',
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
        'final_total' => 'decimal:2',
        'used_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    public function promoCode()
    {
        return $this->belongsTo(PromoCode::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function ticketType()
    {
        return $this->belongsTo(TicketType::class);
    }

    public function ticketOrder()
    {
        return $this->belongsTo(TicketOrder::class);
    }

    public function pendingRegistration()
    {
        return $this->belongsTo(PendingRegistration::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
