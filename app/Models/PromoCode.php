<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    protected $fillable = [
        'event_id',
        'ticket_type_id',
        'code',
        'discount_type',
        'discount_value',
        'is_active',
        'starts_at',
        'ends_at',
        'usage_limit_total',
        'usage_limit_per_user',
        'min_attendee_count',
        'max_attendee_count',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function ticketType()
    {
        return $this->belongsTo(TicketType::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function redemptions()
    {
        return $this->hasMany(PromoCodeRedemption::class);
    }

    public function completedRedemptions()
    {
        return $this->hasMany(PromoCodeRedemption::class)->where('status', 'completed');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function isWithinWindow(): bool
    {
        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }

        if ($this->ends_at && $this->ends_at->isPast()) {
            return false;
        }

        return true;
    }
}
