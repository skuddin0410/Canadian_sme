<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventTicket extends Model
{
    protected $fillable = [
         'session_id', 'ticket_type_id', 'category_id', 'name', 'sku',
        'description', 'price', 'quantity', 'features', 'is_group',
        'group_size', 'status', 'sale_start_date', 'sale_end_date',
        'sort_order', 'metadata'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_group' => 'boolean',
        'features' => 'array',
        'sale_start_date' => 'datetime',
        'sale_end_date' => 'datetime',
        'metadata' => 'array'
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

     public function ticketType()
    {
        return $this->belongsTo(TicketType::class);
    }

    public function category()
    {
        return $this->belongsTo(TicketCategory::class, 'category_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'active')
                    ->where('quantity', '>', 0);
    }

    // Methods
    public function getAvailableQuantity()
    {
        $bookedQuantity = $this->bookings()
                              ->whereIn('status', ['confirmed', 'pending'])
                              ->sum('quantity');
        return $this->quantity - $bookedQuantity;
    }
}
